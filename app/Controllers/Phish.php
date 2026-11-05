<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\TrackingLogModel;
use App\Models\TargetModel;
use App\Models\CampaignTargetModel;
use App\Models\CampaignModel;

class Phish extends Controller
{
    private $logFile = WRITEPATH . 'logs/tracking.log';

    private function writeLog($email, $event, $campaignId = null, $details = null, $payload = null)
    {
        $email = trim(strtolower($email));
        // Reject empty or dummy preview emails from polluting tracking and databases
        if (empty($email) || str_contains($email, 'example.com') || str_contains($email, 'example.org') || str_contains($email, 'company.local') || $email === 'john.doe') {
            return;
        }

        $line = date('Y-m-d H:i:s') . " - $email - $event" . ($campaignId ? " - Camp#$campaignId" : "") . ($details ? " - $details" : "") . PHP_EOL;
        file_put_contents($this->logFile, $line, FILE_APPEND);

        try {
            // 1. Determine Campaign ID if not explicitly provided
            if (!$campaignId) {
                $campaignModel = new CampaignModel();
                $latestActive = $campaignModel->orderBy('id', 'DESC')->first();
                $campaignId = $latestActive ? (int)$latestActive['id'] : 1;
            }

            // 2. Insert into tracking_logs
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

            // 3. Update Campaign-specific Target Record (campaign_targets)
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
                if ($payload) {
                    $campData['submitted_data_payload'] = is_array($payload) ? json_encode($payload) : $payload;
                }
            } elseif ($event === 'email_reported') {
                $campData['opened'] = 1;
                $campData['reported'] = 1;
                $campData['reported_at'] = date('Y-m-d H:i:s');
            }

            if ($campTarget) {
                if (!empty($campTarget['sent'])) $campData['sent'] = 1;
                if (!empty($campTarget['opened']) || in_array($event, ['image_viewed', 'clicked_download', 'submitted_data', 'email_reported'])) {
                    $campData['opened'] = 1;
                }
                if (!empty($campTarget['clicked']) || in_array($event, ['clicked_download', 'submitted_data'])) {
                    $campData['clicked'] = 1;
                }
                if (!empty($campTarget['submitted']) || $event === 'submitted_data') {
                    $campData['submitted'] = 1;
                }
                if (!empty($campTarget['reported']) || $event === 'email_reported') {
                    $campData['reported'] = 1;
                    $campData['reported_at'] = $campData['reported_at'] ?? $campTarget['reported_at'] ?? date('Y-m-d H:i:s');
                }
                $campTargetModel->update($campTarget['id'], $campData);
            }

            // 4. Also update Master User Directory (targets)
            $targetModel = new TargetModel();
            $target = $targetModel->where('email', $email)->first();
            $dirData = [
                'email' => $email,
                'last_activity' => date('Y-m-d H:i:s')
            ];
            if ($payload) {
                $dirData['submitted_data_payload'] = is_array($payload) ? json_encode($payload) : $payload;
            }
            if ($event === 'email_sent') {
                $dirData['sent'] = 1;
            } elseif ($event === 'image_viewed') {
                $dirData['opened'] = 1;
            } elseif ($event === 'clicked_download') {
                $dirData['opened'] = 1;
                $dirData['clicked'] = 1;
            } elseif ($event === 'submitted_data') {
                $dirData['opened'] = 1;
                $dirData['clicked'] = 1;
                $dirData['submitted'] = 1;
                if ($payload) {
                    $dirData['submitted_data_payload'] = is_array($payload) ? json_encode($payload) : $payload;
                }
            } elseif ($event === 'email_reported') {
                $dirData['opened'] = 1;
                $dirData['reported'] = 1;
                $dirData['reported_at'] = date('Y-m-d H:i:s');
            }

            if ($target) {
                if (!empty($target['sent'])) $dirData['sent'] = 1;
                if (!empty($target['opened']) || in_array($event, ['image_viewed', 'clicked_download', 'submitted_data', 'email_reported'])) {
                    $dirData['opened'] = 1;
                }
                if (!empty($target['clicked']) || in_array($event, ['clicked_download', 'submitted_data'])) {
                    $dirData['clicked'] = 1;
                }
                if (!empty($target['submitted']) || $event === 'submitted_data') {
                    $dirData['submitted'] = 1;
                }
                if (!empty($target['reported']) || $event === 'email_reported') {
                    $dirData['reported'] = 1;
                    $dirData['reported_at'] = $dirData['reported_at'] ?? $target['reported_at'] ?? date('Y-m-d H:i:s');
                }
                $targetModel->update($target['id'], $dirData);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Phish writeLog DB error: ' . $e->getMessage());
        }
    }

    // Track image view (Invisible Tracking Pixel with query & path param support)
    public function image($email = '', $campaignId = null)
    {
        $email = rawurldecode(trim($email));
        if (empty($email)) {
            $email = rawurldecode(trim($this->request->getGet('email') ?? ''));
        }
        if (empty($campaignId)) {
            $campaignId = $this->request->getGet('campaign_id') ?: null;
        }

        // Clean up email if .gif/.png appended in path
        $email = preg_replace('/\.(gif|png|jpg|jpeg)$/i', '', $email);

        if (!empty($email)) {
            $this->writeLog($email, 'image_viewed', $campaignId);
        }

        // Highly compatible 43-byte transparent 1x1 GIF
        $gif1x1 = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');

        return $this->response
            ->setStatusCode(200)
            ->setHeader('Content-Type', 'image/gif')
            ->setHeader('Content-Length', (string) strlen($gif1x1))
            ->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0, proxy-revalidate')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT')
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Timing-Allow-Origin', '*')
            ->setBody($gif1x1);
    }

    // Track download click (Phishing link click)
    public function download($email = '', $campaignId = null)
    {
        $email = rawurldecode(trim($email));
        if (empty($email)) {
            $email = rawurldecode(trim($this->request->getGet('email') ?? ''));
        }
        if (empty($campaignId)) {
            $campaignId = $this->request->getGet('campaign_id') ?: null;
        }

        if (!empty($email)) {
            // Both opened and clicked are attributed to the campaign
            $this->writeLog($email, 'image_viewed', $campaignId);
            $this->writeLog($email, 'clicked_download', $campaignId);
            session()->set('victim_email', $email);
            session()->set('victim_campaign_id', $campaignId);
        }

        $params = [];
        if (!empty($email)) {
            $params['email'] = $email;
        }
        if (!empty($campaignId)) {
            $params['campaign_id'] = $campaignId;
        }
        $query = !empty($params) ? ('?' . http_build_query($params)) : '';

        return redirect()->to('/training' . $query);
    }

    // Track reporting of phishing email by vigilant user (Gophish-style)
    public function report($email = '', $campaignId = null)
    {
        $email = rawurldecode(trim($email));
        if (empty($email)) {
            $email = rawurldecode(trim($this->request->getGet('email') ?? ''));
        }
        if (empty($campaignId)) {
            $campaignId = $this->request->getGet('campaign_id') ?: null;
        }

        if (!empty($email)) {
            $this->writeLog($email, 'email_reported', $campaignId);
        }

        return view('reported', [
            'email' => $email ?: 'employee@company.com',
            'campaignId' => $campaignId
        ]);
    }

    // Training page (Microsoft Forms simulation & Red Alert transition)
    public function training($email = null, $campaignId = null)
    {
        $email = rawurldecode(trim((string)($email ?: ($this->request->getGet('email') ?: session()->get('victim_email') ?: ''))));
        $campaignId = $campaignId ?: ($this->request->getGet('campaign_id') ?: session()->get('victim_campaign_id') ?: null);
        $email = trim(strtolower($email));

        $target = null;
        if (!empty($email)) {
            if ($campaignId) {
                $campTargetModel = new CampaignTargetModel();
                $target = $campTargetModel->where('campaign_id', $campaignId)->where('email', $email)->first();
            }
            if (!$target) {
                $targetModel = new TargetModel();
                $target = $targetModel->where('email', $email)->first();
            }
        }

        $submittedPayload = session()->get('submitted_payload');
        if (!$submittedPayload && !empty($target['submitted_data_payload'])) {
            $submittedPayload = json_decode($target['submitted_data_payload'], true);
        }

        $campaignModel = new CampaignModel();
        $campaign = null;
        if ($campaignId) {
            $campaign = $campaignModel->find($campaignId);
        }
        if (!$campaign) {
            $campaign = $campaignModel->orderBy('id', 'DESC')->first();
        }

        // Form is optional, by default keep the red screen (hasForm = false)
        if ($this->request->getGet('has_form') !== null) {
            $hasForm = !empty($this->request->getGet('has_form'));
        } else {
            $hasForm = !empty($campaign['has_credential_form']);
        }

        if ($this->request->getGet('duration') !== null) {
            $redScreenDuration = (int)$this->request->getGet('duration');
        } else {
            $redScreenDuration = (int)($campaign['red_screen_duration'] ?? 5);
        }
        if ($redScreenDuration <= 0) $redScreenDuration = 5;

        if ($this->request->getGet('show_debrief') !== null) {
            $showDebrief = !empty($this->request->getGet('show_debrief'));
        } else {
            $showDebrief = !isset($campaign['show_debrief_screen']) || !empty($campaign['show_debrief_screen']);
        }

        // Load custom simulation form configuration
        $formModel = new \App\Models\SimulationFormModel();
        $formData = null;
        try {
            $formData = $formModel->where('is_active', 1)->first();
            if (!$formData) {
                $formData = $formModel->first();
            }
        } catch (\Throwable $e) {}

        if (!$formData) {
            $formData = [
                'brand_name' => 'Microsoft Forms',
                'badge_text' => 'Internal Verification',
                'theme_color' => '#008272',
                'hero_title' => 'Diwali Special Employee Bonus & Goodies Claim',
                'hero_description' => 'Please complete this short employee participation activity to verify your eligibility and confirm your exclusive festive bonus distribution.',
                'notice_text' => 'Hi {name}, when you submit this form, the owner will see your name and email address.',
                'q1_label' => '1. Full Name',
                'q1_placeholder' => 'Enter your full name',
                'q2_label' => '2. Work Email Address',
                'q2_placeholder' => 'name@company.com',
                'q3_label' => '3. Employee ID Number',
                'q3_placeholder' => 'e.g. EMP-20248',
                'q4_label' => '4. Current Employment Status',
                'q4_options' => "Full-Time Active Employee\nContract / Consulting Associate\nProbationary / Trainee",
                'submit_btn_text' => 'Submit',
                'footer_text' => 'Never give out your password. This content is created by the owner of the form. The data you submit will be sent to the form owner. Microsoft is not responsible for the privacy or security practices of its customers.'
            ];
        }

        return view('training', [
            'email' => $email,
            'campaignId' => $campaignId ?: ($campaign['id'] ?? null),
            'target' => $target,
            'submittedPayload' => $submittedPayload,
            'hasForm' => $hasForm,
            'redScreenDuration' => $redScreenDuration,
            'showDebrief' => $showDebrief,
            'formData' => $formData,
            'showAlert' => (bool)$this->request->getGet('alert')
        ]);
    }

    // Submit form endpoint (logs submitted_data, stores captured fields, and triggers Hacked/Phish Test)
    public function submit_form()
    {
        $email = rawurldecode(trim(strtolower($this->request->getPost('email') ?: ($this->request->getGet('email') ?: session()->get('victim_email') ?: ''))));
        $campaignId = $this->request->getPost('campaign_id') ?: ($this->request->getGet('campaign_id') ?: session()->get('victim_campaign_id') ?: null);
        $fullName = trim($this->request->getPost('full_name') ?: '');
        $employeeId = trim($this->request->getPost('employee_id') ?: '');
        $employed = trim($this->request->getPost('employed') ?: '');

        $payload = [
            'full_name' => $fullName,
            'email' => $email,
            'employee_id' => $employeeId,
            'employed' => $employed,
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => (string)$this->request->getUserAgent(),
            'submitted_at' => date('Y-m-d H:i:s')
        ];
        $details = "Name: {$fullName} | Emp ID: {$employeeId} | Status: {$employed}";

        if (!empty($email)) {
            $this->writeLog($email, 'submitted_data', $campaignId, $details, $payload);
            session()->set('victim_email', $email);
            session()->set('submitted_payload', $payload);
            if ($campaignId) {
                session()->set('victim_campaign_id', $campaignId);
            }
        }

        if ($this->request->isAJAX() || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest' || $this->request->getPost('is_ajax')) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Simulated data submission logged',
                'email' => $email,
                'campaign_id' => $campaignId,
                'payload' => $payload
            ]);
        }

        $params = ['alert' => 1];
        if (!empty($email)) $params['email'] = $email;
        if (!empty($campaignId)) $params['campaign_id'] = $campaignId;

        return redirect()->to('/training?' . http_build_query($params));
    }

    // Fake fullscreen warning page with red alert
    public function warning($email = null, $campaignId = null)
    {
        $email = rawurldecode(trim((string)($email ?: ($this->request->getGet('email') ?: session()->get('victim_email') ?: ''))));
        $campaignId = $campaignId ?: ($this->request->getGet('campaign_id') ?: session()->get('victim_campaign_id') ?: null);
        $email = trim(strtolower($email));

        $target = null;
        if (!empty($email)) {
            if ($campaignId) {
                $campTargetModel = new CampaignTargetModel();
                $target = $campTargetModel->where('campaign_id', $campaignId)->where('email', $email)->first();
            }
            if (!$target) {
                $targetModel = new TargetModel();
                $target = $targetModel->where('email', $email)->first();
            }
        }

        $submittedPayload = session()->get('submitted_payload');
        if (!$submittedPayload && !empty($target['submitted_data_payload'])) {
            $submittedPayload = json_decode($target['submitted_data_payload'], true);
        }

        $campaignModel = new CampaignModel();
        $campaign = null;
        if ($campaignId) {
            $campaign = $campaignModel->find($campaignId);
        }
        if (!$campaign) {
            $campaign = $campaignModel->orderBy('id', 'DESC')->first();
        }

        // Form is optional, by default keep the red screen (hasForm = false)
        if ($this->request->getGet('has_form') !== null) {
            $hasForm = !empty($this->request->getGet('has_form'));
        } else {
            $hasForm = !empty($campaign['has_credential_form']);
        }

        if ($this->request->getGet('duration') !== null) {
            $redScreenDuration = (int)$this->request->getGet('duration');
        } else {
            $redScreenDuration = (int)($campaign['red_screen_duration'] ?? 5);
        }
        if ($redScreenDuration <= 0) $redScreenDuration = 5;

        if ($this->request->getGet('show_debrief') !== null) {
            $showDebrief = !empty($this->request->getGet('show_debrief'));
        } else {
            $showDebrief = !isset($campaign['show_debrief_screen']) || !empty($campaign['show_debrief_screen']);
        }

        return view('fullscreen_warning', [
            'email' => $email,
            'campaignId' => $campaignId ?: ($campaign['id'] ?? null),
            'target' => $target,
            'submittedPayload' => $submittedPayload,
            'hasForm' => $hasForm,
            'redScreenDuration' => $redScreenDuration,
            'showDebrief' => $showDebrief
        ]);
    }

    // Fake lock screen (harmless)
    public function lockscreen()
    {
        return view('lockscreen');
    }

    // Serve icon.svg / favicon
    public function favicon()
    {
        $svgPath = FCPATH . 'icon.svg';
        if (file_exists($svgPath)) {
            return $this->response
                ->setStatusCode(200)
                ->setHeader('Content-Type', 'image/svg+xml')
                ->setHeader('Cache-Control', 'public, max-age=604800')
                ->setBody(file_get_contents($svgPath));
        }

        $icoPath = FCPATH . 'favicon.ico';
        if (file_exists($icoPath)) {
            return $this->response
                ->setStatusCode(200)
                ->setHeader('Content-Type', 'image/x-icon')
                ->setHeader('Cache-Control', 'public, max-age=604800')
                ->setBody(file_get_contents($icoPath));
        }

        return $this->response->setStatusCode(404);
    }
}
