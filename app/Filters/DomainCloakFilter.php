<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * DomainCloakFilter
 *
 * Implements strict Gophish-style domain separation:
 * - Admin Domain: Restricted to administrators (login, dashboard, campaigns, settings).
 * - Phishing Domain: Sent in simulation emails to target employees (download, training, pixel, report).
 * - Domain Cloaking: Anyone visiting root '/', '/login', or '/admin' on the Phishing Domain receives
 *   an authentic Apache 404 Not Found decoy page, completely hiding the admin portal and simulation server.
 */
class DomainCloakFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $settings = $this->getSettings();
        $phishUrl = trim($_SERVER['PHISH_SERVER_URL'] ?? env('PHISH_SERVER_URL') ?? $settings['phish_server_url'] ?? '');
        $adminUrl = trim($_SERVER['ADMIN_SERVER_URL'] ?? env('ADMIN_SERVER_URL') ?? $settings['admin_server_url'] ?? '');
        $cloakEnabled = !isset($settings['cloak_phish_domain']) || !empty($settings['cloak_phish_domain']);
        $bypassKey = trim($settings['admin_bypass_key'] ?? 'phishcall-bypass');

        // Check if explicitly flagged as phishing server by Nginx fastcgi_param IS_PHISH_SERVER 1
        $explicitPhishFlag = !empty($_SERVER['IS_PHISH_SERVER']);

        // If no separate phishing domain is configured, or cloaking is disabled, skip
        if (empty($phishUrl) || !$cloakEnabled) {
            return null;
        }

        $incomingHostHeader = strtolower($_SERVER['HTTP_HOST'] ?? '');
        $incomingHost = explode(':', $incomingHostHeader)[0];
        $incomingPort = isset(explode(':', $incomingHostHeader)[1]) ? (int)explode(':', $incomingHostHeader)[1] : null;

        $phishHost = strtolower(parse_url($phishUrl, PHP_URL_HOST) ?? '');
        $phishPort = parse_url($phishUrl, PHP_URL_PORT);

        $adminHost = strtolower(parse_url($adminUrl, PHP_URL_HOST) ?? '');
        $adminPort = parse_url($adminUrl, PHP_URL_PORT);

        // If admin domain and phishing domain are identical, we are in single-domain mode
        if (!empty($adminHost) && $adminHost === $phishHost && (int)$adminPort === (int)$phishPort) {
            return null;
        }

        // Determine if current request arrived on the Phishing Domain
        $isPhishDomain = false;
        if ($explicitPhishFlag) {
            $isPhishDomain = true;
        } elseif (!empty($phishHost) && $incomingHost === $phishHost) {
            if ($phishPort) {
                $isPhishDomain = ($incomingPort === (int)$phishPort);
            } else {
                $isPhishDomain = true;
            }
        }

        // If explicit admin domain matches, request is definitively NOT phishing domain
        if (!empty($adminHost) && $incomingHost === $adminHost) {
            if ($adminPort) {
                if ($incomingPort === (int)$adminPort) {
                    $isPhishDomain = false;
                }
            } else {
                $isPhishDomain = false;
            }
        }

        if (!$isPhishDomain) {
            return null;
        }

        // Emergency Admin Bypass Key (allows admin access even from phishing domain via ?admin_key=...)
        if (!empty($bypassKey) && isset($_GET['admin_key']) && $_GET['admin_key'] === $bypassKey) {
            session()->set('admin_bypass', true);
            return null;
        }
        if (session()->get('admin_bypass')) {
            return null;
        }

        // Extract and normalize relative path
        $path = trim($request->getUri()->getPath(), '/');
        
        // Strip subdirectory prefix if installed in subfolder
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $subDir = trim(dirname($scriptName), '/');
        if ($subDir !== '' && str_starts_with($path, $subDir)) {
            $path = trim(substr($path, strlen($subDir)), '/');
        }

        // Strip index.php prefix if present
        if (str_starts_with($path, 'index.php')) {
            $path = trim(substr($path, strlen('index.php')), '/');
        }

        // Detect cloaked routes on the Phishing Domain:
        // Root '/', 'login', 'admin', 'admin/*', 'logout', 'quiz'
        $cloaked = false;
        if ($path === '') {
            $cloaked = true;
        } elseif (preg_match('/^(login|admin|logout|quiz)(\/.*)?$/i', $path)) {
            $cloaked = true;
        }

        if ($cloaked) {
            return $this->renderDecoy404();
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }

    /**
     * Render an authentic Apache/Nginx 404 Not Found error page with standard HTTP 404 status.
     * Zero application branding or clues that an admin or simulation server exists.
     */
    private function renderDecoy404(): ResponseInterface
    {
        $response = \Config\Services::response();
        $serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? 'Apache/2.4.58 (Win64) OpenSSL/3.1.3 PHP/8.2.12';
        $serverName = $_SERVER['SERVER_NAME'] ?? $_SERVER['HTTP_HOST'] ?? 'localhost';
        $serverPort = $_SERVER['SERVER_PORT'] ?? 80;
        $requestUri = htmlspecialchars($_SERVER['REQUEST_URI'] ?? '/');

        $html = <<<HTML
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL {$requestUri} was not found on this server.</p>
<hr>
<address>{$serverSoftware} Server at {$serverName} Port {$serverPort}</address>
</body></html>
HTML;

        return $response->setStatusCode(404, 'Not Found')
            ->setHeader('Content-Type', 'text/html; charset=iso-8859-1')
            ->setBody($html);
    }

    private function getSettings(): array
    {
        $settingsFile = WRITEPATH . 'settings.json';
        if (file_exists($settingsFile)) {
            $data = json_decode(file_get_contents($settingsFile), true);
            if (is_array($data)) {
                return $data;
            }
        }
        try {
            $db = \Config\Database::connect();
            $row = $db->table('settings')->get()->getRowArray();
            if ($row) {
                return $row;
            }
        } catch (\Throwable $e) {}

        return [];
    }
}

