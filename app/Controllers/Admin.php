<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\SettingsModel;
use App\Models\TargetModel;
use App\Models\CampaignTargetModel;
use App\Models\TrackingLogModel;
use App\Models\EmailTemplateModel;
use App\Models\CampaignModel;

class Admin extends Controller
{
    private $logFile = WRITEPATH . 'logs/tracking.log';
    private $usersFile = WRITEPATH . 'admin_users.json';
    private $settingsFile = WRITEPATH . 'settings.json';

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Strict domain cloaking: Block admin console if accessed via Phishing Domain
        $settings = $this->getSettingsData();
        if (!empty($settings['phish_server_url']) && (!isset($settings['cloak_phish_domain']) || !empty($settings['cloak_phish_domain']))) {
            $incomingHost = strtolower(explode(':', $_SERVER['HTTP_HOST'] ?? '')[0]);
            $phishHost = strtolower(parse_url($settings['phish_server_url'], PHP_URL_HOST) ?? '');
            $bypassKey = trim($settings['admin_bypass_key'] ?? 'phishguard-bypass');
            $hasBypass = (!empty($bypassKey) && isset($_GET['admin_key']) && $_GET['admin_key'] === $bypassKey) || session()->get('admin_bypass');

            if ($phishHost && $incomingHost === $phishHost && !$hasBypass) {
                header("HTTP/1.1 404 Not Found");
                header("Content-Type: text/html; charset=iso-8859-1");
                $reqUri = htmlspecialchars($_SERVER['REQUEST_URI'] ?? '/admin');
                $serverSoft = $_SERVER['SERVER_SOFTWARE'] ?? 'Apache/2.4.58 (Win64)';
                $serverName = $_SERVER['SERVER_NAME'] ?? $incomingHost;
                $serverPort = $_SERVER['SERVER_PORT'] ?? 80;
                echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\"><html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL {$reqUri} was not found on this server.</p><hr><address>{$serverSoft} Server at {$serverName} Port {$serverPort}</address></body></html>";
                exit;
            }
        }
        
        if (!session()->get('is_admin')) {
            header('Location: ' . base_url('login'));
            exit;
        }
    }

    private function ensureSchemaUpgrades()
    {
        try {
            $db = \Config\Database::connect();
            // 1. Add reported columns to campaign_targets if missing
            if (!$db->fieldExists('reported', 'campaign_targets')) {
                $db->query("ALTER TABLE `campaign_targets` ADD COLUMN `reported` TINYINT(1) DEFAULT 0 AFTER `submitted`");
            }
            if (!$db->fieldExists('reported_at', 'campaign_targets')) {
                $db->query("ALTER TABLE `campaign_targets` ADD COLUMN `reported_at` DATETIME DEFAULT NULL AFTER `last_activity`");
            }
            // 2. Add reported columns to targets if missing
            if (!$db->fieldExists('reported', 'targets')) {
                $db->query("ALTER TABLE `targets` ADD COLUMN `reported` TINYINT(1) DEFAULT 0 AFTER `submitted`");
            }
            if (!$db->fieldExists('reported_at', 'targets')) {
                $db->query("ALTER TABLE `targets` ADD COLUMN `reported_at` DATETIME DEFAULT NULL AFTER `last_activity`");
            }

            // 3. Ensure Sender Name is Ravindranath Yarlagadda across settings and templates
            $db->query("UPDATE `settings` SET `from_name` = 'Ravindranath Yarlagadda' WHERE `from_name` = 'Ravi' OR `from_name` = '' OR `from_name` IS NULL");
            $db->query("UPDATE `email_templates` SET `sender_name` = 'Ravindranath Yarlagadda' WHERE `sender_name` = 'Ravi' OR `sender_name` = '' OR `sender_name` IS NULL");

            // 4. Update Diwali template to plain text format exactly matching company email style
            $diwaliHtml = '<div style="font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif; font-size: 15px; line-height: 1.65; color: #1e293b; max-width: 650px;">
  <p style="margin: 0 0 16px 0;">Dear {{.FirstName}},</p>
  <p style="margin: 0 0 16px 0;">The festive season is here! 🪔✨</p>
  <p style="margin: 0 0 16px 0;">As part of our annual Diwali celebrations, we are excited to announce that <strong>Diwali goodies will be distributed to all employees, as usual!</strong></p>
  <p style="margin: 0 0 16px 0;">But this year, we have something EXTRA for you! 🎉</p>
  <p style="margin: 0 0 16px 0;">🎁 <strong>Introducing the Diwali Special Employee Bonus!</strong></p>
  <p style="margin: 0 0 16px 0;">Employees who complete our special Diwali participation activity will be eligible for an exclusive festive bonus.</p>
  <p style="margin: 0 0 8px 0;"><strong>Here\'s how you can participate:</strong></p>
  <p style="margin: 0 0 4px 0;">1. Click the link below.</p>
  <p style="margin: 0 0 4px 0;">2. Complete the short employee participation activity.</p>
  <p style="margin: 0 0 16px 0;">3. Submit your details to confirm your eligibility.</p>
  <p style="margin: 0 0 18px 0;">👉 <a href="{{.URL}}" style="color: #4f46e5; text-decoration: underline; font-weight: 600;">CLICK HERE TO CLAIM YOUR DIWALI BONUS</a></p>
  <p style="margin: 0 0 16px 0;">Wishing you and your family a very Happy Diwali!</p>
  <p style="margin: 0 0 20px 0;">Regards,<br>Ravi</p>
  <img src="{{.TrackingURL}}" alt="" width="1" height="1" border="0" style="display:block;width:1px;height:1px;opacity:0.01;border:none;margin:0;padding:0;" />
</div>';

            $templateModel = new EmailTemplateModel();
            $diwaliTmpl = $templateModel->like('name', 'Diwali')->first();
            if ($diwaliTmpl && str_contains($diwaliTmpl['body_html'], 'linear-gradient')) {
                $templateModel->update($diwaliTmpl['id'], [
                    'name' => 'Diwali Special Employee Bonus',
                    'subject' => 'Diwali Celebrations: Claim Your Special Employee Bonus! 🪔✨',
                    'sender_name' => 'Ravindranath Yarlagadda',
                    'body_html' => $diwaliHtml
                ]);
            }

            // 5. Clean up any phantom dummy preview users
            $db->query("DELETE FROM `targets` WHERE `email` LIKE '%example.com%' OR `email` = 'john.doe@example.com'");
            $db->query("DELETE FROM `campaign_targets` WHERE `email` LIKE '%example.com%' OR `email` = 'john.doe@example.com'");
            $db->query("DELETE FROM `tracking_logs` WHERE `email` LIKE '%example.com%' OR `email` = 'john.doe@example.com'");

            // 6. Ensure campaign_id and details columns exist in tracking_logs
            if (!$db->fieldExists('campaign_id', 'tracking_logs')) {
                $db->query("ALTER TABLE `tracking_logs` ADD COLUMN `campaign_id` INT DEFAULT NULL AFTER `id`, ADD INDEX `idx_camp_id` (`campaign_id`)");
            }
            if (!$db->fieldExists('details', 'tracking_logs')) {
                $db->query("ALTER TABLE `tracking_logs` ADD COLUMN `details` TEXT DEFAULT NULL AFTER `action`");
            }

            // 7. Ensure include_report_link column exists in campaigns
            if (!$db->fieldExists('include_report_link', 'campaigns')) {
                $db->query("ALTER TABLE `campaigns` ADD COLUMN `include_report_link` TINYINT(1) DEFAULT 0 AFTER `template_name`");
            }

            // 8. Ensure submitted_data_payload exists in campaign_targets and targets
            if (!$db->fieldExists('submitted_data_payload', 'campaign_targets')) {
                $db->query("ALTER TABLE `campaign_targets` ADD COLUMN `submitted_data_payload` TEXT DEFAULT NULL AFTER `submitted`");
            }
            if (!$db->fieldExists('submitted_data_payload', 'targets')) {
                $db->query("ALTER TABLE `targets` ADD COLUMN `submitted_data_payload` TEXT DEFAULT NULL AFTER `submitted`");
            }

            // 9. Ensure Domain Separation columns exist in settings
            if (!$db->fieldExists('admin_server_url', 'settings')) {
                $db->query("ALTER TABLE `settings` ADD COLUMN `admin_server_url` VARCHAR(255) DEFAULT 'http://10.10.0.190/phishing-simulation' AFTER `from_name`");
            }
            if (!$db->fieldExists('phish_server_url', 'settings')) {
                $db->query("ALTER TABLE `settings` ADD COLUMN `phish_server_url` VARCHAR(255) DEFAULT NULL AFTER `admin_server_url`");
            }
            if (!$db->fieldExists('cloak_phish_domain', 'settings')) {
                $db->query("ALTER TABLE `settings` ADD COLUMN `cloak_phish_domain` TINYINT(1) DEFAULT 1 AFTER `phish_server_url`");
            }
            if (!$db->fieldExists('admin_bypass_key', 'settings')) {
                $db->query("ALTER TABLE `settings` ADD COLUMN `admin_bypass_key` VARCHAR(100) DEFAULT 'phishguard-bypass' AFTER `cloak_phish_domain`");
            }

            // 10. Ensure phish_server_url exists in campaigns table
            if (!$db->fieldExists('phish_server_url', 'campaigns')) {
                $db->query("ALTER TABLE `campaigns` ADD COLUMN `phish_server_url` VARCHAR(255) DEFAULT NULL AFTER `include_report_link`");
            }

            // 11. Ensure landing page and red screen options exist in campaigns table
            if (!$db->fieldExists('has_credential_form', 'campaigns')) {
                $db->query("ALTER TABLE `campaigns` ADD COLUMN `has_credential_form` TINYINT(1) DEFAULT 0 AFTER `phish_server_url`");
            }
            if (!$db->fieldExists('red_screen_duration', 'campaigns')) {
                $db->query("ALTER TABLE `campaigns` ADD COLUMN `red_screen_duration` INT DEFAULT 5 AFTER `has_credential_form`");
            }
            if (!$db->fieldExists('show_debrief_screen', 'campaigns')) {
                $db->query("ALTER TABLE `campaigns` ADD COLUMN `show_debrief_screen` TINYINT(1) DEFAULT 1 AFTER `red_screen_duration`");
            }

            // 8. Backfill missing chronological event logs into tracking_logs from campaign_targets snapshot
            $activeTargets = $db->query("SELECT * FROM `campaign_targets` WHERE `sent` = 1 OR `opened` = 1 OR `clicked` = 1 OR `submitted` = 1 OR `reported` = 1")->getResultArray();
            foreach ($activeTargets as $at) {
                $cId = (int)($at['campaign_id'] ?: 1);
                $cEmail = strtolower(trim($at['email'] ?? ''));
                if (empty($cEmail) || str_contains($cEmail, 'example.com') || $cEmail === 'john.doe') continue;
                
                $cCreated = !empty($at['created_at']) ? $at['created_at'] : date('Y-m-d H:i:s');
                $cAct = !empty($at['last_activity']) ? $at['last_activity'] : $cCreated;

                if (!empty($at['sent'])) {
                    $has = $db->query("SELECT id FROM `tracking_logs` WHERE `campaign_id` = ? AND `email` = ? AND `action` = 'email_sent' LIMIT 1", [$cId, $cEmail])->getRow();
                    if (!$has) {
                        $db->query("INSERT INTO `tracking_logs` (`campaign_id`, `email`, `action`, `created_at`) VALUES (?, ?, 'email_sent', ?)", [$cId, $cEmail, $cCreated]);
                    }
                }
                if (!empty($at['opened'])) {
                    $has = $db->query("SELECT id FROM `tracking_logs` WHERE `campaign_id` = ? AND `email` = ? AND `action` = 'image_viewed' LIMIT 1", [$cId, $cEmail])->getRow();
                    if (!$has) {
                        $db->query("INSERT INTO `tracking_logs` (`campaign_id`, `email`, `action`, `created_at`) VALUES (?, ?, 'image_viewed', ?)", [$cId, $cEmail, $cAct]);
                    }
                }
                if (!empty($at['clicked'])) {
                    $has = $db->query("SELECT id FROM `tracking_logs` WHERE `campaign_id` = ? AND `email` = ? AND `action` = 'clicked_download' LIMIT 1", [$cId, $cEmail])->getRow();
                    if (!$has) {
                        $db->query("INSERT INTO `tracking_logs` (`campaign_id`, `email`, `action`, `created_at`) VALUES (?, ?, 'clicked_download', ?)", [$cId, $cEmail, $cAct]);
                    }
                }
                if (!empty($at['submitted'])) {
                    $has = $db->query("SELECT id FROM `tracking_logs` WHERE `campaign_id` = ? AND `email` = ? AND `action` = 'submitted_data' LIMIT 1", [$cId, $cEmail])->getRow();
                    if (!$has) {
                        $db->query("INSERT INTO `tracking_logs` (`campaign_id`, `email`, `action`, `created_at`) VALUES (?, ?, 'submitted_data', ?)", [$cId, $cEmail, $cAct]);
                    }
                }
                if (!empty($at['reported'])) {
                    $has = $db->query("SELECT id FROM `tracking_logs` WHERE `campaign_id` = ? AND `email` = ? AND `action` = 'email_reported' LIMIT 1", [$cId, $cEmail])->getRow();
                    if (!$has) {
                        $rTime = !empty($at['reported_at']) ? $at['reported_at'] : $cAct;
                        $db->query("INSERT INTO `tracking_logs` (`campaign_id`, `email`, `action`, `created_at`) VALUES (?, ?, 'email_reported', ?)", [$cId, $cEmail, $rTime]);
                    }
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Schema upgrade error: ' . $e->getMessage());
        }
    }

    public function index()
    {
        $this->ensureSchemaUpgrades();

        $campaignModel = new CampaignModel();
        $campaignTargetModel = new CampaignTargetModel();
        $templateModel = new EmailTemplateModel();
        $targetModel = new TargetModel();
        $logModel = new TrackingLogModel();

        // 1. Campaign Resolution & Full List (for Gophish switcher)
        $campaigns = [];
        $campaign = null;
        try {
            $campaigns = $campaignModel->orderBy('id', 'DESC')->findAll();
            $requestedCampId = $this->request->getGet('campaign_id');

            if ($requestedCampId) {
                $campaign = $campaignModel->find($requestedCampId);
            }

            if (!$campaign && !empty($campaigns)) {
                $campaign = $campaigns[0];
            }

            if (!$campaign) {
                $campaignId = $campaignModel->insert([
                    'name' => 'Q4 Employee Phishing Awareness Assessment',
                    'template_name' => 'IT Security Password Expiry',
                    'status' => 'In progress',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                $campaign = $campaignModel->find($campaignId);
                $campaigns = [$campaign];
            }
        } catch (\Throwable $e) {
            log_message('error', 'Campaign load error: ' . $e->getMessage());
        }

        // 2. Email Templates
        $templates = [];
        try {
            $templates = $templateModel->findAll();
        } catch (\Throwable $e) {
            log_message('error', 'Template load error: ' . $e->getMessage());
        }

        // 3. Targets and Gophish Funnel Metrics for THIS Active Campaign
        $targets = [];
        $stats = [
            'total_targets' => 0,
            'sent_count' => 0,
            'opened_count' => 0,
            'clicked_count' => 0,
            'submitted_count' => 0,
            'reported_count' => 0,
            'sent_pct' => 0,
            'opened_pct' => 0,
            'clicked_pct' => 0,
            'submitted_pct' => 0,
            'reported_pct' => 0
        ];

        try {
            $campId = $campaign ? (int)$campaign['id'] : 1;
            $dbTargets = $campaignTargetModel->where('campaign_id', $campId)->orderBy('id', 'DESC')->findAll();

            // If campaign #1 targets table is empty in campaign_targets, backfill existing targets
            if (empty($dbTargets) && $campId === 1) {
                $masterTargets = $targetModel->findAll();
                foreach ($masterTargets as $mt) {
                    $campaignTargetModel->insert([
                        'campaign_id' => 1,
                        'target_id' => $mt['id'],
                        'email' => $mt['email'],
                        'first_name' => $mt['first_name'] ?? '',
                        'last_name' => $mt['last_name'] ?? '',
                        'position' => $mt['position'] ?? '',
                        'department' => $mt['department'] ?? '',
                        'sent' => $mt['sent'] ?? 0,
                        'opened' => $mt['opened'] ?? 0,
                        'clicked' => $mt['clicked'] ?? 0,
                        'submitted' => $mt['submitted'] ?? 0,
                        'reported' => $mt['reported'] ?? 0,
                        'reported_at' => $mt['reported_at'] ?? null,
                        'last_activity' => $mt['last_activity'] ?? null,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
                $dbTargets = $campaignTargetModel->where('campaign_id', $campId)->orderBy('id', 'DESC')->findAll();
            }

            foreach ($dbTargets as $t) {
                $status = 'Not Sent';
                $badgeClass = 'badge-status-pending';

                if (!empty($t['reported'])) {
                    $status = 'Reported Phish';
                    $badgeClass = 'badge-status-reported';
                } elseif (!empty($t['submitted'])) {
                    $status = 'Submitted Data';
                    $badgeClass = 'badge-status-submitted';
                } elseif (!empty($t['clicked'])) {
                    $status = 'Clicked Link';
                    $badgeClass = 'badge-status-clicked';
                } elseif (!empty($t['opened'])) {
                    $status = 'Email Opened';
                    $badgeClass = 'badge-status-opened';
                } elseif (!empty($t['sent'])) {
                    $status = 'Email Sent';
                    $badgeClass = 'badge-status-sent';
                }

                $t['first_name'] = $t['first_name'] ?? '';
                $t['last_name']  = $t['last_name'] ?? '';
                $t['name']       = !empty($t['name']) ? $t['name'] : trim($t['first_name'] . ' ' . $t['last_name']);
                if (empty($t['name'])) {
                    $t['name'] = !empty($t['first_name']) ? $t['first_name'] : 'Target';
                }
                $t['position']   = !empty($t['position']) ? $t['position'] : (!empty($t['department']) ? $t['department'] : 'Staff');
                $t['department'] = $t['department'] ?? '';
                $t['computed_status'] = $status;
                $t['badge_class'] = $badgeClass;
                $targets[] = $t;

                $isSent = !empty($t['sent']) || !empty($t['opened']) || !empty($t['clicked']) || !empty($t['submitted']) || !empty($t['reported']);
                $isOpened = !empty($t['opened']) || !empty($t['clicked']) || !empty($t['submitted']);
                $isClicked = !empty($t['clicked']) || !empty($t['submitted']);
                $isSubmitted = !empty($t['submitted']);
                $isReported = !empty($t['reported']);

                if ($isSent) $stats['sent_count']++;
                if ($isOpened) $stats['opened_count']++;
                if ($isClicked) $stats['clicked_count']++;
                if ($isSubmitted) $stats['submitted_count']++;
                if ($isReported) $stats['reported_count']++;
            }

            $stats['total_targets'] = count($targets);

            if ($stats['total_targets'] > 0) {
                $stats['sent_pct'] = round(($stats['sent_count'] / $stats['total_targets']) * 100);
            }
            if ($stats['sent_count'] > 0) {
                $stats['opened_pct'] = round(($stats['opened_count'] / $stats['sent_count']) * 100);
                $stats['clicked_pct'] = round(($stats['clicked_count'] / $stats['sent_count']) * 100);
                $stats['submitted_pct'] = round(($stats['submitted_count'] / $stats['sent_count']) * 100);
                $stats['reported_pct'] = round(($stats['reported_count'] / $stats['sent_count']) * 100);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Target load error: ' . $e->getMessage());
        }

        // 4. Live Activity / Tracking Logs for THIS Campaign
        $logs = [];
        try {
            $campId = $campaign ? (int)$campaign['id'] : 1;
            $logs = $logModel->where('campaign_id', $campId)->orderBy('created_at', 'DESC')->findAll(150);
            if (empty($logs)) {
                // If campaign_id was missing on legacy records, search by target emails
                $campTargetEmails = array_map(function($t) { return strtolower($t['email']); }, $targets);
                if (!empty($campTargetEmails)) {
                    $logs = $logModel->whereIn('email', $campTargetEmails)->orderBy('created_at', 'DESC')->findAll(150);
                }
            }
            if (empty($logs) && $campId === 1) {
                $logs = $logModel->orderBy('created_at', 'DESC')->findAll(150);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Logs load error: ' . $e->getMessage());
        }

        // 5. Settings & Master Directory Target Count
        $settings = $this->getSettingsData();
        $masterTargetCount = 0;
        $allDirectoryUsers = [];
        try {
            $masterTargetCount = $targetModel->countAllResults();
            $allDirectoryUsers = $targetModel->orderBy('first_name', 'ASC')->findAll();
        } catch (\Throwable $e) {}

        return view('admin/dashboard', [
            'campaign' => $campaign,
            'campaigns' => $campaigns,
            'templates' => $templates,
            'targets' => $targets,
            'stats' => $stats,
            'logs' => $logs,
            'settings' => $settings,
            'masterTargetCount' => $masterTargetCount,
            'allDirectoryUsers' => $allDirectoryUsers
        ]);
    }

    /**
     * ==========================================================
     * SIMULATION USERS (TARGETS) MANAGEMENT: View, Add, Edit, Delete
     * ==========================================================
     */
    public function users()
    {
        $targetModel = new TargetModel();
        $users = [];
        try {
            $dbUsers = $targetModel->orderBy('id', 'DESC')->findAll();
            foreach ($dbUsers as $u) {
                $status = 'Not Sent';
                $badgeClass = 'badge-status-pending';

                if (!empty($u['submitted'])) {
                    $status = 'Submitted Data';
                    $badgeClass = 'badge-status-submitted';
                } elseif (!empty($u['clicked'])) {
                    $status = 'Clicked Link';
                    $badgeClass = 'badge-status-clicked';
                } elseif (!empty($u['opened'])) {
                    $status = 'Email Opened';
                    $badgeClass = 'badge-status-opened';
                } elseif (!empty($u['sent'])) {
                    $status = 'Email Sent';
                    $badgeClass = 'badge-status-sent';
                }

                $u['computed_status'] = $status;
                $u['badge_class'] = $badgeClass;
                $users[] = $u;
            }
        } catch (\Throwable $e) {
            log_message('error', 'Users list error: ' . $e->getMessage());
        }

        return view('admin/users', [
            'users' => $users,
            'total_users' => count($users)
        ]);
    }

    public function save_user()
    {
        $id = $this->request->getPost('id');
        $email = trim($this->request->getPost('email'));
        $firstName = trim($this->request->getPost('first_name'));
        $lastName = trim($this->request->getPost('last_name'));
        $position = trim($this->request->getPost('position'));

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('error', 'Please provide a valid email address.');
        }

        $targetModel = new TargetModel();

        // Check duplicate email
        $existing = $targetModel->where('email', $email)->first();
        if ($existing && (empty($id) || $existing['id'] != $id)) {
            return redirect()->back()->with('error', "A simulation user with email '{$email}' already exists.");
        }

        $fullName = trim("$firstName $lastName");
        $data = [
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'name' => $fullName,
            'position' => $position,
            'department' => $position
        ];

        if (!empty($id)) {
            $targetModel->update($id, $data);
            return redirect()->to('/admin/users')->with('success', "Simulation user '{$email}' updated successfully.");
        } else {
            $data['sent'] = 0;
            $data['opened'] = 0;
            $data['clicked'] = 0;
            $data['submitted'] = 0;
            $data['created_at'] = date('Y-m-d H:i:s');
            $targetModel->insert($data);
            return redirect()->to('/admin/users')->with('success', "Simulation user '{$email}' added successfully.");
        }
    }

    public function delete_user($id)
    {
        try {
            $targetModel = new TargetModel();
            $user = $targetModel->find($id);
            if ($user) {
                $targetModel->delete($id);
                return redirect()->to('/admin/users')->with('success', "Simulation user '{$user['email']}' deleted successfully.");
            }
            return redirect()->to('/admin/users')->with('error', 'User not found.');
        } catch (\Throwable $e) {
            return redirect()->to('/admin/users')->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }

    public function bulk_delete_users()
    {
        $userIds = $this->request->getPost('user_ids');
        if (empty($userIds) || !is_array($userIds)) {
            return redirect()->to('/admin/users')->with('error', 'No users selected for deletion.');
        }

        try {
            $targetModel = new TargetModel();
            $targetModel->whereIn('id', $userIds)->delete();
            return redirect()->to('/admin/users')->with('success', count($userIds) . ' simulation user(s) permanently deleted.');
        } catch (\Throwable $e) {
            return redirect()->to('/admin/users')->with('error', 'Failed to bulk delete users: ' . $e->getMessage());
        }
    }

    public function bulk_delete_campaign_targets()
    {
        $campaignId = (int)$this->request->getPost('campaign_id');
        $targetIds = $this->request->getPost('target_ids');
        if (empty($targetIds) || !is_array($targetIds)) {
            return redirect()->to('/admin?campaign_id=' . $campaignId)->with('error', 'No targets selected.');
        }

        try {
            $campTargetModel = new CampaignTargetModel();
            $campTargetModel->where('campaign_id', $campaignId)->whereIn('id', $targetIds)->delete();
            return redirect()->to('/admin?campaign_id=' . $campaignId)->with('success', count($targetIds) . ' target(s) removed from campaign.');
        } catch (\Throwable $e) {
            return redirect()->to('/admin?campaign_id=' . $campaignId)->with('error', 'Error removing targets: ' . $e->getMessage());
        }
    }

    /**
     * Bulk Target Import (Gophish-style CSV Upload / Paste / Directory Enrollment)
     */
    public function import_targets()
    {
        $campaignId = (int)$this->request->getPost('campaign_id');
        $dirUserIds = $this->request->getPost('directory_user_ids');

        // Fast-path: Enroll selected existing users from directory into this campaign
        if (!empty($dirUserIds) && is_array($dirUserIds)) {
            $targetModel = new TargetModel();
            $campTargetModel = new CampaignTargetModel();
            $selectedUsers = $targetModel->whereIn('id', $dirUserIds)->findAll();
            $enrolled = 0;
            foreach ($selectedUsers as $su) {
                if ($campaignId) {
                    $existing = $campTargetModel->where('campaign_id', $campaignId)->where('email', $su['email'])->first();
                    if (!$existing) {
                        $campTargetModel->insert([
                            'campaign_id' => $campaignId,
                            'target_id' => $su['id'],
                            'email' => $su['email'],
                            'first_name' => $su['first_name'] ?? '',
                            'last_name' => $su['last_name'] ?? '',
                            'position' => $su['position'] ?? '',
                            'department' => $su['department'] ?? '',
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                        $enrolled++;
                    }
                }
            }
            return redirect()->to('/admin?campaign_id=' . $campaignId)->with('success', "Enrolled {$enrolled} user(s) from directory into this campaign!");
        }

        $csvFile = $this->request->getFile('csv_file');
        $csvText = $this->request->getPost('csv_text');
        $lines = [];

        if ($csvFile && $csvFile->isValid() && !$csvFile->hasMoved()) {
            $content = file_get_contents($csvFile->getTempName());
            $lines = preg_split('/\r\n|\r|\n/', trim($content));
        } elseif (!empty($csvText)) {
            $lines = preg_split('/\r\n|\r|\n/', trim($csvText));
        }

        if (empty($lines)) {
            return redirect()->back()->with('error', 'Please provide a CSV file or paste valid target data.');
        }

        $targetModel = new TargetModel();
        $imported = 0;
        $updated = 0;
        $headerSkipped = false;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $row = str_getcsv($line);
            if (empty($row)) continue;

            if (!$headerSkipped) {
                $lowerRow = array_map('strtolower', array_map('trim', $row));
                if (in_array('email', $lowerRow) || in_array('first name', $lowerRow) || in_array('firstname', $lowerRow)) {
                    $headerSkipped = true;
                    continue;
                }
                $headerSkipped = true;
            }

            $firstName = '';
            $lastName = '';
            $email = '';
            $position = '';

            $emailIdx = -1;
            foreach ($row as $idx => $val) {
                $val = trim($val);
                if (filter_var($val, FILTER_VALIDATE_EMAIL)) {
                    $emailIdx = $idx;
                    $email = $val;
                    break;
                }
            }

            if ($emailIdx === -1) {
                continue;
            }

            if (count($row) >= 4) {
                if ($emailIdx === 2) {
                    $firstName = trim($row[0] ?? '');
                    $lastName = trim($row[1] ?? '');
                    $position = trim($row[3] ?? '');
                } else {
                    $firstName = trim($row[0] ?? '');
                    $lastName = trim($row[1] ?? '');
                    $position = trim($row[count($row) - 1] ?? '');
                }
            } elseif (count($row) === 3) {
                if ($emailIdx === 2) {
                    $firstName = trim($row[0] ?? '');
                    $lastName = trim($row[1] ?? '');
                } else {
                    $firstName = trim($row[0] ?? '');
                    $position = trim($row[2] ?? '');
                }
            } elseif (count($row) === 2) {
                $otherVal = trim($row[$emailIdx === 0 ? 1 : 0]);
                $firstName = $otherVal;
            }

            $fullName = trim("$firstName $lastName");

            $existing = $targetModel->where('email', $email)->first();
            if ($existing) {
                $targetModel->update($existing['id'], [
                    'first_name' => $firstName ?: $existing['first_name'],
                    'last_name' => $lastName ?: $existing['last_name'],
                    'name' => $fullName ?: $existing['name'],
                    'position' => $position ?: $existing['position'],
                    'department' => $position ?: $existing['department']
                ]);
                $updated++;
            } else {
                $targetModel->insert([
                    'email' => $email,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'name' => $fullName,
                    'position' => $position,
                    'department' => $position,
                    'sent' => 0,
                    'opened' => 0,
                    'clicked' => 0,
                    'submitted' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                $imported++;
            }
        }

        $msg = "Import complete! Added {$imported} new target(s)";
        if ($updated > 0) {
            $msg .= ", updated {$updated} existing record(s).";
        } else {
            $msg .= ".";
        }

        return redirect()->back()->with('success', $msg);
    }

    public function sample_csv()
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="gophish_targets_template.csv"');
        $fp = fopen('php://output', 'w');
        fputcsv($fp, ['First Name', 'Last Name', 'Email', 'Position']);
        fputcsv($fp, ['Alice', 'Johnson', 'alice.johnson@company.com', 'Financial Analyst']);
        fputcsv($fp, ['Bob', 'Miller', 'bob.miller@company.com', 'HR Specialist']);
        fputcsv($fp, ['Charlie', 'Smith', 'charlie.smith@company.com', 'IT Security Lead']);
        fputcsv($fp, ['David', 'Williams', 'david.williams@company.com', 'Operations Associate']);
        fclose($fp);
        exit;
    }

    public function delete_target($id)
    {
        $campaignId = $this->request->getGet('campaign_id');
        try {
            $campTargetModel = new CampaignTargetModel();
            $ct = $campTargetModel->find($id);
            if ($ct) {
                $cId = $ct['campaign_id'];
                $campTargetModel->delete($id);
                return redirect()->to('/admin?campaign_id=' . $cId)->with('success', 'Target removed from campaign.');
            }
            // Fallback to TargetModel if not in campaign_targets
            (new TargetModel())->delete($id);
            return redirect()->to('/admin' . ($campaignId ? '?campaign_id=' . $campaignId : ''))->with('success', 'Target removed.');
        } catch (\Throwable $e) {
            return redirect()->to('/admin')->with('error', 'Failed to delete target: ' . $e->getMessage());
        }
    }

    public function clear_targets()
    {
        $campaignId = $this->request->getGet('campaign_id');
        try {
            $campTargetModel = new CampaignTargetModel();
            if ($campaignId) {
                $campTargetModel->where('campaign_id', $campaignId)->delete();
                return redirect()->to('/admin?campaign_id=' . $campaignId)->with('success', 'All targets cleared from this campaign.');
            } else {
                $campTargetModel->truncate();
                return redirect()->to('/admin')->with('success', 'All campaign targets have been cleared.');
            }
        } catch (\Throwable $e) {
            return redirect()->to('/admin')->with('error', 'Error clearing targets: ' . $e->getMessage());
        }
    }

    /**
     * Email Templates Management
     */
    public function templates()
    {
        $templates = (new EmailTemplateModel())->findAll();
        return view('admin/templates', ['templates' => $templates]);
    }

    public function save_template()
    {
        $id = $this->request->getPost('id');
        $data = [
            'name' => $this->request->getPost('name'),
            'subject' => $this->request->getPost('subject'),
            'sender_name' => $this->request->getPost('sender_name') ?: 'Ravindranath Yarlagadda',
            'body_html' => $this->request->getPost('body_html')
        ];

        $templateModel = new EmailTemplateModel();
        if (!empty($id)) {
            $templateModel->update($id, $data);
            return redirect()->back()->with('success', 'Template updated successfully.');
        } else {
            $templateModel->insert($data);
            return redirect()->back()->with('success', 'New template created successfully.');
        }
    }

    public function delete_template($id)
    {
        (new EmailTemplateModel())->delete($id);
        return redirect()->back()->with('success', 'Template deleted successfully.');
    }

    public function template_preview($id)
    {
        $template = (new EmailTemplateModel())->find($id);
        if (!$template) {
            return $this->response->setJSON(['error' => 'Template not found']);
        }

        $dummyTarget = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'preview.user@company.local',
            'position' => 'Staff Analyst'
        ];

        // Safe preview: use data URI for tracking pixel and # for links so previewing never hits tracking endpoints
        $safePixel = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
        $rendered = $this->renderTemplate($template['body_html'], $dummyTarget, '#', $safePixel, '#');

        return $this->response->setJSON([
            'id' => $template['id'],
            'name' => $template['name'],
            'subject' => $template['subject'],
            'sender_name' => $template['sender_name'],
            'html' => $rendered
        ]);
    }

    /**
     * Send Phishing Awareness Simulation (Single or Bulk)
     * Supports creating a New Campaign or targeting an Existing Campaign
     */
    public function send_simulation()
    {
        $campaignType = $this->request->getPost('campaign_type') ?: 'new';
        $campaignNameInput = trim($this->request->getPost('campaign_name') ?? '');
        $campaignId = (int)$this->request->getPost('campaign_id');
        $sendMode = $this->request->getPost('send_mode') ?: 'bulk';
        $templateId = (int)$this->request->getPost('template_id');
        $customImage = $this->request->getFile('custom_image');
        $hasCustomImage = ($customImage && $customImage->isValid() && !$customImage->hasMoved());
        $cidName = 'evidence.png';
        $attachments = [];

        $settings = $this->getSettingsData();
        $defaultSenderName = !empty($settings['from_name']) ? $settings['from_name'] : 'Ravindranath Yarlagadda';
        $senderEmail = !empty($settings['from_email']) ? $settings['from_email'] : 'nobitanobi0272@gmail.com';
        $apiKey = $settings['brevo_api_key'] ?? '';

        $templateModel = new EmailTemplateModel();
        $campaignType = $this->request->getPost('campaign_type') ?: 'new';
        $campaignNameInput = trim($this->request->getPost('campaign_name') ?? '');
        $campaignId = $this->request->getPost('campaign_id');
        $includeReportLink = (bool)$this->request->getPost('include_report_link');
        $hasCredentialForm = $this->request->getPost('has_credential_form') ? 1 : 0;
        $redScreenDuration = (int)($this->request->getPost('red_screen_duration') ?: 5);
        if ($redScreenDuration <= 0) $redScreenDuration = 5;
        $showDebriefScreen = ($this->request->getPost('show_debrief_screen') !== null) ? (int)$this->request->getPost('show_debrief_screen') : 1;

        $phishServerUrl = trim($this->request->getPost('phish_server_url') ?? '');
        if (empty($phishServerUrl)) {
            $phishServerUrl = trim($_SERVER['PHISH_SERVER_URL'] ?? env('PHISH_SERVER_URL') ?? $settings['phish_server_url'] ?? '');
        }

        $template = null;
        if (!empty($templateId)) {
            $template = $templateModel->find($templateId);
        }
        if (!$template) {
            $template = $templateModel->first();
        }

        // Sender name is always full name Ravindranath Yarlagadda
        $senderName = (!empty($template['sender_name']) && $template['sender_name'] !== 'Ravi') ? $template['sender_name'] : $defaultSenderName;
        if (empty($senderName) || $senderName === 'Ravi') {
            $senderName = 'Ravindranath Yarlagadda';
        }
        $subject = $template ? $template['subject'] : 'Phishing Awareness Simulation';
        $baseHtml = $template ? $template['body_html'] : '';

        // Campaign Resolution: Create new campaign or attach to selected
        $campaignModel = new CampaignModel();
        if ($campaignType === 'new' || empty($campaignId)) {
            $campName = $campaignNameInput ?: ($template ? $template['name'] . ' Campaign' : 'Simulation ' . date('Y-m-d H:i'));
            $campaignId = $campaignModel->insert([
                'name' => $campName,
                'template_name' => $template ? $template['name'] : 'Custom Simulation',
                'include_report_link' => $includeReportLink ? 1 : 0,
                'phish_server_url' => $phishServerUrl ?: null,
                'has_credential_form' => $hasCredentialForm,
                'red_screen_duration' => $redScreenDuration,
                'show_debrief_screen' => $showDebriefScreen,
                'status' => 'In progress',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $existingCamp = $campaignModel->find($campaignId);
            if (!$existingCamp) {
                $campaignId = $campaignModel->insert([
                    'name' => $campaignNameInput ?: 'Simulation Campaign',
                    'template_name' => $template ? $template['name'] : 'Custom Simulation',
                    'include_report_link' => $includeReportLink ? 1 : 0,
                    'phish_server_url' => $phishServerUrl ?: null,
                    'has_credential_form' => $hasCredentialForm,
                    'red_screen_duration' => $redScreenDuration,
                    'show_debrief_screen' => $showDebriefScreen,
                    'status' => 'In progress',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                $campaignModel->update($campaignId, [
                    'include_report_link' => $includeReportLink ? 1 : 0,
                    'phish_server_url' => $phishServerUrl ?: ($existingCamp['phish_server_url'] ?? null),
                    'has_credential_form' => $hasCredentialForm,
                    'red_screen_duration' => $redScreenDuration,
                    'show_debrief_screen' => $showDebriefScreen
                ]);
                if (empty($phishServerUrl) && !empty($existingCamp['phish_server_url'])) {
                    $phishServerUrl = $existingCamp['phish_server_url'];
                }
            }
        }

        if ($hasCustomImage) {
            $cidName = $customImage->getName();
            $fileContent = file_get_contents($customImage->getTempName());
            $attachments[] = [
                'content' => base64_encode($fileContent),
                'name' => $cidName
            ];
        }

        $targetModel = new TargetModel();
        $campTargetModel = new CampaignTargetModel();
        $targetsToSend = [];

        if ($sendMode === 'bulk') {
            $targetsToSend = $targetModel->findAll();
            if (empty($targetsToSend)) {
                return redirect()->back()->with('error', 'No simulation users found in directory. Please add or import users in the Simulation Users tab first.');
            }
        } elseif ($sendMode === 'selected') {
            $selectedUserIds = $this->request->getPost('selected_user_ids');
            if (empty($selectedUserIds) || !is_array($selectedUserIds)) {
                return redirect()->back()->with('error', 'Please select at least one user from the directory to launch this simulation.');
            }
            $targetsToSend = $targetModel->whereIn('id', $selectedUserIds)->findAll();
            if (empty($targetsToSend)) {
                return redirect()->back()->with('error', 'Selected users were not found in directory.');
            }
        } else {
            $singleEmail = trim($this->request->getPost('email'));
            if (empty($singleEmail) || !filter_var($singleEmail, FILTER_VALIDATE_EMAIL)) {
                return redirect()->back()->with('error', 'Please provide a valid target email address.');
            }

            $target = $targetModel->where('email', $singleEmail)->first();
            if (!$target) {
                $nameParts = explode('@', $singleEmail)[0];
                $id = $targetModel->insert([
                    'email' => $singleEmail,
                    'first_name' => ucfirst($nameParts),
                    'name' => ucfirst($nameParts),
                    'position' => 'Staff',
                    'sent' => 0,
                    'opened' => 0,
                    'clicked' => 0,
                    'submitted' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                $target = $targetModel->find($id);
            }
            $targetsToSend[] = $target;
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($targetsToSend as $tgt) {
            $targetEmail = $tgt['email'];

            // Ensure recipient is added to campaign_targets snapshot
            $existingCampTarget = $campTargetModel->where('campaign_id', $campaignId)->where('email', $targetEmail)->first();
            if (!$existingCampTarget) {
                $campTargetId = $campTargetModel->insert([
                    'campaign_id' => $campaignId,
                    'target_id' => $tgt['id'] ?? null,
                    'email' => $targetEmail,
                    'first_name' => $tgt['first_name'] ?? '',
                    'last_name' => $tgt['last_name'] ?? '',
                    'position' => $tgt['position'] ?? '',
                    'department' => $tgt['department'] ?? '',
                    'sent' => 0,
                    'opened' => 0,
                    'clicked' => 0,
                    'submitted' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                $campTarget = $campTargetModel->find($campTargetId);
            } else {
                $campTarget = $existingCampTarget;
            }

            // Tracking & Reporting Links using dedicated Phishing Domain (never leaks Admin Domain to users)
            $phishBase = !empty($phishServerUrl) ? rtrim($phishServerUrl, '/') : rtrim(base_url(), '/');
            $trackingPixel = $phishBase . "/image/" . rawurlencode($targetEmail) . "/{$campaignId}/pixel.gif";
            $phishingLink = $phishBase . "/download/" . rawurlencode($targetEmail) . "/{$campaignId}";
            $reportLink = $phishBase . "/report/" . rawurlencode($targetEmail) . "/{$campaignId}";

            $message = $this->renderTemplate($baseHtml, $tgt, $phishingLink, $trackingPixel, $reportLink, $includeReportLink);
            $sentSuccessfully = false;

            if (!empty($apiKey)) {
                $result = $this->sendViaBrevo($apiKey, $targetEmail, $subject, $message, $senderEmail, $senderName, $attachments);
                if ($result['success']) {
                    $sentSuccessfully = true;
                } else {
                    log_message('error', 'Brevo API Error for ' . $targetEmail . ': ' . $result['error']);
                }
            }

            if (!$sentSuccessfully) {
                $emailFunc = \Config\Services::email();
                $emailFunc->setTo($targetEmail);
                $emailFunc->setFrom($senderEmail, $senderName);
                $emailFunc->setSubject($subject);
                $emailFunc->setMailType('html');
                $emailFunc->setMessage($message);

                if ($hasCustomImage) {
                    $emailFunc->attach($customImage->getTempName(), 'inline', $cidName);
                }

                if ($emailFunc->send()) {
                    $sentSuccessfully = true;
                } else {
                    log_message('error', 'SMTP Send Error for ' . $targetEmail . ': ' . $emailFunc->printDebugger(['headers']));
                }
            }

            if ($sentSuccessfully) {
                $this->writeLog($targetEmail, 'email_sent', $campaignId);
                $campTargetModel->update($campTarget['id'], [
                    'sent' => 1,
                    'last_activity' => date('Y-m-d H:i:s')
                ]);
                $successCount++;
            } else {
                $failCount++;
            }
        }

        if ($successCount > 0) {
            $msg = "Phishing awareness simulation launched successfully to {$successCount} target(s)!";
            if ($failCount > 0) {
                $msg .= " ({$failCount} failed to deliver).";
            }
            return redirect()->to('/admin?campaign_id=' . $campaignId)->with('success', $msg);
        } else {
            return redirect()->to('/admin?campaign_id=' . $campaignId)->with('error', "Failed to deliver simulation emails. Please check your SMTP settings in Settings.");
        }
    }

    private function renderTemplate($templateHtml, $target, $phishingLink, $trackingPixel, $reportLink = '', $includeReportFooter = false)
    {
        $firstName = !empty($target['first_name']) ? $target['first_name'] : (!empty($target['name']) ? explode(' ', $target['name'])[0] : 'Colleague');
        $lastName = !empty($target['last_name']) ? $target['last_name'] : '';
        $email = $target['email'];
        $position = !empty($target['position']) ? $target['position'] : (!empty($target['department']) ? $target['department'] : 'Employee');

        $search = ['{{.FirstName}}', '{{.LastName}}', '{{.Email}}', '{{.Position}}', '{{.URL}}', '{{.TrackingURL}}', '{{.ReportURL}}'];
        $replace = [$firstName, $lastName, $email, $position, $phishingLink, $trackingPixel, $includeReportFooter ? $reportLink : '#'];

        $rendered = str_replace($search, $replace, $templateHtml);
        $rendered = preg_replace('/display\s*:\s*none\s*;?/i', 'display:block;width:1px;height:1px;opacity:0.01;', $rendered);
        
        // Ensure tracking pixel is embedded with standard compatibility
        if (!str_contains($rendered, 'pixel.gif') && !str_contains($rendered, '/image')) {
            $rendered .= '<img src="' . $trackingPixel . '" alt="" width="1" height="1" border="0" style="display:block;width:1px;height:1px;opacity:0.01;border:none;margin:0;padding:0;" />';
        }

        // Only append reporting action if EXPLICITLY requested by the admin when launching the campaign (DEFAULT: OFF)
        if ($includeReportFooter && !empty($reportLink) && !str_contains($rendered, 'report/')) {
            $rendered .= '<div style="text-align: center; margin-top: 24px; padding-top: 14px; border-top: 1px solid #e2e8f0; font-size: 11px; color: #94a3b8;">Suspicious email? <a href="' . $reportLink . '" style="color: #64748b; text-decoration: underline; font-weight: 600;">Report this email to IT Security 🛡️</a></div>';
        }

        return $rendered;
    }

    private function writeLog($email, $event, $campaignId = null, $details = null, $payload = null)
    {
        $email = trim(strtolower($email));
        if (empty($email) || str_contains($email, 'example.com') || str_contains($email, 'example.org') || str_contains($email, 'company.local') || $email === 'john.doe') {
            return;
        }

        $line = date('Y-m-d H:i:s') . " - $email - $event" . ($campaignId ? " - Camp#$campaignId" : "") . ($details ? " - $details" : "") . PHP_EOL;
        file_put_contents($this->logFile, $line, FILE_APPEND);

        try {
            if (!$campaignId) {
                $campaignModel = new CampaignModel();
                $latest = $campaignModel->orderBy('id', 'DESC')->first();
                $campaignId = $latest ? (int)$latest['id'] : 1;
            }

            $logModel = new TrackingLogModel();
            $logModel->insert([
                'campaign_id' => $campaignId,
                'email' => $email,
                'action' => $event,
                'details' => $details ?: (is_array($payload) ? json_encode($payload) : $payload),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => (string) $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s')
            ]);

            // Update Campaign Target snapshot
            $campTargetModel = new CampaignTargetModel();
            $campTarget = $campTargetModel->where('campaign_id', $campaignId)->where('email', $email)->first();
            $campData = [
                'campaign_id' => $campaignId,
                'email' => $email,
                'last_activity' => date('Y-m-d H:i:s')
            ];
            if ($payload) {
                $campData['submitted_data_payload'] = is_array($payload) ? json_encode($payload) : $payload;
            }
            if ($event === 'email_sent') {
                $campData['sent'] = 1;
            } elseif ($event === 'image_viewed') {
                $campData['opened'] = 1;
            } elseif ($event === 'clicked_download') {
                $campData['opened'] = 1;
                $campData['clicked'] = 1;
            } elseif ($event === 'submitted_data') {
                $campData['opened'] = 1;
                $campData['clicked'] = 1;
                $campData['submitted'] = 1;
            } elseif ($event === 'email_reported') {
                $campData['opened'] = 1;
                $campData['reported'] = 1;
                $campData['reported_at'] = date('Y-m-d H:i:s');
            }

            if ($campTarget) {
                if (!empty($campTarget['sent'])) $campData['sent'] = 1;
                if (!empty($campTarget['opened']) || in_array($event, ['image_viewed', 'clicked_download', 'submitted_data', 'email_reported'])) $campData['opened'] = 1;
                if (!empty($campTarget['clicked']) || in_array($event, ['clicked_download', 'submitted_data'])) $campData['clicked'] = 1;
                if (!empty($campTarget['submitted']) || $event === 'submitted_data') $campData['submitted'] = 1;
                if (!empty($campTarget['reported']) || $event === 'email_reported') {
                    $campData['reported'] = 1;
                    $campData['reported_at'] = $campData['reported_at'] ?? $campTarget['reported_at'] ?? date('Y-m-d H:i:s');
                }
                $campTargetModel->update($campTarget['id'], $campData);
            } else {
                $campTargetModel->insert($campData);
            }

            // Update Master Target Directory
            $targetModel = new TargetModel();
            $target = $targetModel->where('email', $email)->first();
            $data = [
                'email' => $email,
                'last_activity' => date('Y-m-d H:i:s')
            ];
            if ($event === 'email_sent') {
                $data['sent'] = 1;
            } elseif ($event === 'image_viewed') {
                $data['opened'] = 1;
            } elseif ($event === 'clicked_download') {
                $data['opened'] = 1;
                $data['clicked'] = 1;
            } elseif ($event === 'submitted_data') {
                $data['opened'] = 1;
                $data['clicked'] = 1;
                $data['submitted'] = 1;
                if ($payload) {
                    $data['submitted_data_payload'] = is_array($payload) ? json_encode($payload) : $payload;
                }
            } elseif ($event === 'email_reported') {
                $data['opened'] = 1;
                $data['reported'] = 1;
                $data['reported_at'] = date('Y-m-d H:i:s');
            }

            if ($target) {
                if (!empty($target['sent'])) $data['sent'] = 1;
                if (!empty($target['opened']) || in_array($event, ['image_viewed', 'clicked_download', 'submitted_data', 'email_reported'])) $data['opened'] = 1;
                if (!empty($target['clicked']) || in_array($event, ['clicked_download', 'submitted_data'])) $data['clicked'] = 1;
                if (!empty($target['submitted']) || $event === 'submitted_data') $data['submitted'] = 1;
                if (!empty($target['reported']) || $event === 'email_reported') {
                    $data['reported'] = 1;
                    $data['reported_at'] = $data['reported_at'] ?? $target['reported_at'] ?? date('Y-m-d H:i:s');
                }
                $targetModel->update($target['id'], $data);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Admin writeLog DB error: ' . $e->getMessage());
        }
    }

    private function sendViaBrevo($apiKey, $to, $subject, $htmlContent, $senderEmail, $senderName = 'Ravindranath Yarlagadda', $attachments = [])
    {
        $url = 'https://api.brevo.com/v3/smtp/email';
        $senderEmail = !empty($senderEmail) ? $senderEmail : 'nobitanobi0272@gmail.com';

        $data = [
            'sender' => ['name' => $senderName, 'email' => $senderEmail],
            'to' => [['email' => $to]],
            'subject' => $subject,
            'htmlContent' => $htmlContent
        ];

        if (!empty($attachments)) {
            $data['attachment'] = $attachments;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'accept: application/json',
            'api-key: ' . $apiKey,
            'content-type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => $response];
        }
    }

    /**
     * ==========================================================
     * CAMPAIGNS MANAGEMENT (Gophish-style Multi-Campaigns)
     * ==========================================================
     */
    public function campaigns()
    {
        $campaignModel = new CampaignModel();
        $campTargetModel = new CampaignTargetModel();
        $campaigns = [];

        try {
            $allCamps = $campaignModel->orderBy('id', 'DESC')->findAll();
            foreach ($allCamps as $c) {
                $cId = $c['id'];
                $cTargets = $campTargetModel->where('campaign_id', $cId)->findAll();
                $total = count($cTargets);
                $sent = 0; $opened = 0; $clicked = 0; $submitted = 0; $reported = 0;
                foreach ($cTargets as $ct) {
                    if (!empty($ct['sent']) || !empty($ct['opened']) || !empty($ct['clicked']) || !empty($ct['submitted']) || !empty($ct['reported'])) $sent++;
                    if (!empty($ct['opened']) || !empty($ct['clicked']) || !empty($ct['submitted'])) $opened++;
                    if (!empty($ct['clicked']) || !empty($ct['submitted'])) $clicked++;
                    if (!empty($ct['submitted'])) $submitted++;
                    if (!empty($ct['reported'])) $reported++;
                }
                $c['total_targets'] = $total;
                $c['sent_count'] = $sent;
                $c['opened_count'] = $opened;
                $c['clicked_count'] = $clicked;
                $c['submitted_count'] = $submitted;
                $c['reported_count'] = $reported;
                $c['opened_pct'] = $sent > 0 ? round(($opened / $sent) * 100) : 0;
                $c['clicked_pct'] = $sent > 0 ? round(($clicked / $sent) * 100) : 0;
                $c['submitted_pct'] = $sent > 0 ? round(($submitted / $sent) * 100) : 0;
                $c['reported_pct'] = $sent > 0 ? round(($reported / $sent) * 100) : 0;
                $campaigns[] = $c;
            }
        } catch (\Throwable $e) {
            log_message('error', 'Campaigns list error: ' . $e->getMessage());
        }

        $templates = (new EmailTemplateModel())->findAll();
        $targetModel = new TargetModel();
        $masterTargetCount = $targetModel->countAllResults();
        $allDirectoryUsers = $targetModel->orderBy('first_name', 'ASC')->findAll();
        $settings = $this->getSettingsData();

        return view('admin/campaigns', [
            'campaigns' => $campaigns,
            'templates' => $templates,
            'masterTargetCount' => $masterTargetCount,
            'settings' => $settings,
            'allDirectoryUsers' => $allDirectoryUsers
        ]);
    }

    public function view_campaign($id)
    {
        return redirect()->to('/admin?campaign_id=' . (int)$id);
    }

    public function complete_campaign($id)
    {
        try {
            $campaignModel = new CampaignModel();
            $campaignModel->update($id, [
                'status' => 'Completed',
                'completed_at' => date('Y-m-d H:i:s')
            ]);
            return redirect()->to('/admin?campaign_id=' . (int)$id)->with('success', 'Campaign marked as completed.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Error completing campaign: ' . $e->getMessage());
        }
    }

    public function delete_campaign($id)
    {
        try {
            $campaignModel = new CampaignModel();
            $camp = $campaignModel->find($id);
            if ($camp) {
                $campaignModel->delete($id);
                (new CampaignTargetModel())->where('campaign_id', $id)->delete();
                (new TrackingLogModel())->where('campaign_id', $id)->delete();
                return redirect()->to('/admin/campaigns')->with('success', "Campaign '{$camp['name']}' and its results deleted successfully.");
            }
            return redirect()->to('/admin/campaigns')->with('error', 'Campaign not found.');
        } catch (\Throwable $e) {
            return redirect()->to('/admin/campaigns')->with('error', 'Error deleting campaign: ' . $e->getMessage());
        }
    }

    public function clear_logs()
    {
        $campaignId = $this->request->getGet('campaign_id');
        try {
            $logModel = new TrackingLogModel();
            $campTargetModel = new CampaignTargetModel();

            if ($campaignId) {
                $logModel->where('campaign_id', $campaignId)->delete();
                $campTargetModel->where('campaign_id', $campaignId)->set([
                    'sent' => 0,
                    'opened' => 0,
                    'clicked' => 0,
                    'submitted' => 0,
                    'last_activity' => null
                ])->update();
                return redirect()->to('/admin?campaign_id=' . $campaignId)->with('success', 'Campaign results and activity stream reset.');
            } else {
                $db = \Config\Database::connect();
                $db->table('tracking_logs')->truncate();
                $campTargetModel->set([
                    'sent' => 0,
                    'opened' => 0,
                    'clicked' => 0,
                    'submitted' => 0,
                    'last_activity' => null
                ])->update();
            }
        } catch (\Throwable $e) {
            log_message('error', 'DB clear_logs error: ' . $e->getMessage());
        }

        if (file_exists($this->logFile)) {
            file_put_contents($this->logFile, '');
        }
        return redirect()->to('/admin' . ($campaignId ? '?campaign_id=' . $campaignId : ''))->with('success', 'Campaign event logs reset.');
    }

    public function export()
    {
        $campaignId = $this->request->getGet('campaign_id');
        $campaignModel = new CampaignModel();
        $camp = $campaignId ? $campaignModel->find($campaignId) : null;
        $campaignName = $camp ? preg_replace('/[^a-zA-Z0-9_-]/', '_', $camp['name']) : 'all_campaigns';

        $filename = 'phishguard_report_' . $campaignName . '_' . date('Y-m-d') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $fp = fopen('php://output', 'w');
        fputcsv($fp, ['Campaign Name', 'Email', 'First Name', 'Last Name', 'Position / Dept', 'Status', 'Sent', 'Opened', 'Clicked', 'Submitted', 'Captured Form Data', 'Reported', 'Reported At', 'Last Activity']);

        try {
            $campTargetModel = new CampaignTargetModel();
            $query = $campTargetModel;
            if ($campaignId) {
                $query = $query->where('campaign_id', $campaignId);
            }
            $targets = $query->findAll();

            // If empty and campaign 1, try master targets
            if (empty($targets)) {
                $targets = (new TargetModel())->findAll();
            }

            foreach ($targets as $t) {
                $status = 'Not Sent';
                if (!empty($t['reported'])) $status = 'Reported Phish';
                elseif (!empty($t['submitted'])) $status = 'Submitted Data';
                elseif (!empty($t['clicked'])) $status = 'Clicked Link';
                elseif (!empty($t['opened'])) $status = 'Email Opened';
                elseif (!empty($t['sent'])) $status = 'Email Sent';

                $capturedStr = '';
                if (!empty($t['submitted_data_payload'])) {
                    $p = json_decode($t['submitted_data_payload'], true);
                    if ($p) {
                        $capturedStr = "Name: " . ($p['full_name'] ?? '') . " | EmpID: " . ($p['employee_id'] ?? '') . " | Status: " . ($p['employed'] ?? '');
                    }
                }

                fputcsv($fp, [
                    $camp['name'] ?? 'Simulation Campaign',
                    $t['email'],
                    $t['first_name'] ?? '',
                    $t['last_name'] ?? '',
                    $t['position'] ?? ($t['department'] ?? ''),
                    $status,
                    !empty($t['sent']) ? 'Yes' : 'No',
                    !empty($t['opened']) ? 'Yes' : 'No',
                    !empty($t['clicked']) ? 'Yes' : 'No',
                    !empty($t['submitted']) ? 'Yes' : 'No',
                    $capturedStr,
                    $t['reported_at'] ?? '',
                    $t['last_activity'] ?? ''
                ]);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Export error: ' . $e->getMessage());
        }

        fclose($fp);
        exit;
    }

    public function mark_reported($id)
    {
        $campaignId = (int)$this->request->getGet('campaign_id');
        try {
            $campTargetModel = new CampaignTargetModel();
            $target = $campTargetModel->find($id);
            if ($target) {
                $cId = $target['campaign_id'];
                $this->writeLog($target['email'], 'email_reported', $cId);
                return redirect()->to('/admin?campaign_id=' . $cId)->with('success', "Target '{$target['email']}' marked as Reported Phish 🛡️.");
            }
            $targetModel = new TargetModel();
            $t = $targetModel->find($id);
            if ($t) {
                $this->writeLog($t['email'], 'email_reported', $campaignId ?: 1);
                return redirect()->to('/admin' . ($campaignId ? '?campaign_id=' . $campaignId : ''))->with('success', "Target '{$t['email']}' marked as Reported Phish 🛡️.");
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Error marking reported: ' . $e->getMessage());
        }
        return redirect()->back()->with('error', 'Target record not found.');
    }

    public function mark_opened($id)
    {
        $campaignId = (int)$this->request->getGet('campaign_id');
        try {
            $campTargetModel = new CampaignTargetModel();
            $target = $campTargetModel->find($id);
            if ($target) {
                $cId = $target['campaign_id'];
                $this->writeLog($target['email'], 'image_viewed', $cId);
                return redirect()->to('/admin?campaign_id=' . $cId)->with('success', "Target '{$target['email']}' marked as Email Opened 👁️.");
            }
            $targetModel = new TargetModel();
            $t = $targetModel->find($id);
            if ($t) {
                $this->writeLog($t['email'], 'image_viewed', $campaignId ?: 1);
                return redirect()->to('/admin' . ($campaignId ? '?campaign_id=' . $campaignId : ''))->with('success', "Target '{$t['email']}' marked as Email Opened 👁️.");
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Error marking opened: ' . $e->getMessage());
        }
        return redirect()->back()->with('error', 'Target record not found.');
    }

    public function settings()
    {
        $data = $this->getSettingsData();
        return view('admin/settings', ['settings' => $data]);
    }

    public function save_settings()
    {
        $data = [
            'smtp_host' => $this->request->getPost('smtp_host'),
            'smtp_user' => $this->request->getPost('smtp_user'),
            'smtp_pass' => $this->request->getPost('smtp_pass'),
            'smtp_port' => (int) $this->request->getPost('smtp_port'),
            'smtp_crypto' => $this->request->getPost('smtp_crypto'),
            'brevo_api_key' => $this->request->getPost('brevo_api_key'),
            'from_email' => $this->request->getPost('from_email'),
            'from_name' => $this->request->getPost('from_name') ?: 'Ravindranath Yarlagadda',
            'admin_server_url' => trim($this->request->getPost('admin_server_url') ?? ''),
            'phish_server_url' => trim($this->request->getPost('phish_server_url') ?? ''),
            'cloak_phish_domain' => $this->request->getPost('cloak_phish_domain') ? 1 : 0,
            'admin_bypass_key' => trim($this->request->getPost('admin_bypass_key') ?? 'phishguard-bypass')
        ];

        try {
            $settingsModel = new SettingsModel();
            $existing = $settingsModel->first();
            if ($existing) {
                $settingsModel->update($existing['id'], $data);
            } else {
                $settingsModel->insert($data);
            }
        } catch (\Throwable $e) {
            log_message('error', 'DB save_settings error: ' . $e->getMessage());
        }

        file_put_contents($this->settingsFile, json_encode($data, JSON_PRETTY_PRINT));

        return redirect()->to('/admin/settings')->with('success', 'Settings saved successfully.');
    }

    /**
     * ==========================================================
     * ADMIN USERS & PROFILE MANAGEMENT
     * ==========================================================
     */
    public function profile()
    {
        $userModel = new UserModel();
        $currentSessionUser = session()->get('admin_user') ?? 'admin';
        $currentAdmin = null;

        try {
            $currentAdmin = $userModel->where('username', $currentSessionUser)->first();
            if (!$currentAdmin) {
                $currentAdmin = $userModel->first();
            }
            $allAdmins = $userModel->orderBy('id', 'ASC')->findAll();
        } catch (\Throwable $e) {
            $allAdmins = [];
            log_message('error', 'Profile load error: ' . $e->getMessage());
        }

        return view('admin/profile', [
            'currentAdmin' => $currentAdmin,
            'allAdmins' => $allAdmins,
            'username' => $currentAdmin['username'] ?? 'admin',
            'email' => $currentAdmin['email'] ?? ''
        ]);
    }

    public function update_profile()
    {
        $id = $this->request->getPost('id');
        $newUsername = trim($this->request->getPost('username'));
        $newEmail = trim($this->request->getPost('email'));
        $newPassword = $this->request->getPost('password');

        if (empty($newUsername)) {
            return redirect()->back()->with('error', 'Username is required.');
        }

        $userModel = new UserModel();
        $admin = !empty($id) ? $userModel->find($id) : ($userModel->where('username', session()->get('admin_user'))->first() ?: $userModel->first());

        if (!$admin) {
            return redirect()->back()->with('error', 'Admin user account not found.');
        }

        // Check unique username
        $duplicate = $userModel->where('username', $newUsername)->where('id !=', $admin['id'])->first();
        if ($duplicate) {
            return redirect()->back()->with('error', "Username '{$newUsername}' is already taken by another admin.");
        }

        $updateData = [
            'username' => $newUsername,
            'email' => $newEmail
        ];

        if (!empty($newPassword)) {
            $updateData['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        try {
            $userModel->update($admin['id'], $updateData);
        } catch (\Throwable $e) {
            log_message('error', 'DB update_profile error: ' . $e->getMessage());
        }

        $data = [
            'username' => $newUsername,
            'password_hash' => $updateData['password_hash'] ?? $admin['password_hash'],
            'email' => $newEmail
        ];
        file_put_contents($this->usersFile, json_encode($data, JSON_PRETTY_PRINT));
        session()->set('admin_user', $newUsername);

        return redirect()->to('/admin/profile')->with('success', 'Admin profile updated successfully.');
    }

    public function create_admin()
    {
        $username = trim($this->request->getPost('username'));
        $email = trim($this->request->getPost('email'));
        $password = $this->request->getPost('password');

        if (empty($username) || empty($password)) {
            return redirect()->back()->with('error', 'Username and password are required.');
        }

        $userModel = new UserModel();
        $existing = $userModel->where('username', $username)->first();
        if ($existing) {
            return redirect()->back()->with('error', "An admin with username '{$username}' already exists.");
        }

        try {
            $userModel->insert([
                'username' => $username,
                'email' => $email,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s')
            ]);
            return redirect()->to('/admin/profile')->with('success', "New admin user '{$username}' created successfully.");
        } catch (\Throwable $e) {
            return redirect()->to('/admin/profile')->with('error', 'Failed to create admin: ' . $e->getMessage());
        }
    }

    public function delete_admin($id)
    {
        $userModel = new UserModel();
        $admin = $userModel->find($id);

        if (!$admin) {
            return redirect()->back()->with('error', 'Admin user not found.');
        }

        if ($admin['username'] === session()->get('admin_user')) {
            return redirect()->back()->with('error', 'You cannot delete your own active admin session.');
        }

        if ($userModel->countAllResults() <= 1) {
            return redirect()->back()->with('error', 'Cannot delete the only existing admin account.');
        }

        try {
            $userModel->delete($id);
            return redirect()->to('/admin/profile')->with('success', "Admin user '{$admin['username']}' deleted successfully.");
        } catch (\Throwable $e) {
            return redirect()->to('/admin/profile')->with('error', 'Error deleting admin: ' . $e->getMessage());
        }
    }

    private function getSettingsData()
    {
        $data = [
            'smtp_host' => 'smtp.gmail.com',
            'smtp_user' => 'nobitanobi0272@gmail.com',
            'smtp_pass' => '',
            'smtp_port' => '587',
            'smtp_crypto' => 'tls',
            'brevo_api_key' => '',
            'from_email' => 'nobitanobi0272@gmail.com',
            'from_name' => 'Ravindranath Yarlagadda',
            'admin_server_url' => rtrim(base_url(), '/'),
            'phish_server_url' => '',
            'cloak_phish_domain' => 1,
            'admin_bypass_key' => 'phishguard-bypass'
        ];

        try {
            $settingsModel = new SettingsModel();
            $settings = $settingsModel->first();
            if ($settings) {
                return array_merge($data, array_filter($settings));
            }
        } catch (\Throwable $e) {
            log_message('error', 'Settings fetch error: ' . $e->getMessage());
        }

        if (file_exists($this->settingsFile)) {
            $fileData = json_decode(file_get_contents($this->settingsFile), true);
            if (is_array($fileData)) {
                return array_merge($data, array_filter($fileData));
            }
        }

        return $data;
    }
}
