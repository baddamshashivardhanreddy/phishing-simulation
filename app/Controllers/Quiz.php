<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\QuizResultModel;
use App\Models\TargetModel;
use App\Models\CampaignTargetModel;
use App\Models\TrackingLogModel;

class Quiz extends Controller
{
    public function index()
    {
        return view('quiz/index');
    }

    public function submit()
    {
        $request = service('request');
        $score = 0;
        $total = 3;

        $q1 = $request->getPost('q1');
        $q2 = $request->getPost('q2');
        $q3 = $request->getPost('q3');

        // Q1: What should you do with a suspicious email?
        // A: Delete/Report (correct)
        if ($q1 === 'report') {
            $score++;
        }

        // Q2: How can you verify a link destination?
        // A: Hover over it (correct)
        if ($q2 === 'hover') {
            $score++;
        }

        // Q3: Who should you contact if unsure?
        // A: IT Security (correct)
        if ($q3 === 'it') {
            $score++;
        }

        $passed = $score >= 3;
        $victimEmail = session()->get('victim_email') ?: ($request->getPost('email') ?: null);
        $victimCampaignId = session()->get('victim_campaign_id') ?: null;

        try {
            $quizModel = new QuizResultModel();
            $quizModel->insert([
                'email' => $victimEmail,
                'score' => $score,
                'total' => $total,
                'passed' => $passed ? 1 : 0,
                'q1_answer' => (string) $q1,
                'q2_answer' => (string) $q2,
                'q3_answer' => (string) $q3,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            if ($victimEmail) {
                // Update Master Target Directory
                $targetModel = new TargetModel();
                $target = $targetModel->where('email', $victimEmail)->first();
                if ($target) {
                    $targetModel->update($target['id'], [
                        'opened' => 1,
                        'clicked' => 1,
                        'submitted' => 1,
                        'last_activity' => date('Y-m-d H:i:s')
                    ]);
                }

                // Update Campaign Target
                $campTargetModel = new CampaignTargetModel();
                $query = $campTargetModel->where('email', $victimEmail);
                if ($victimCampaignId) {
                    $query->where('campaign_id', $victimCampaignId);
                } else {
                    $query->orderBy('id', 'DESC');
                }
                $campTarget = $query->first();

                if ($campTarget) {
                    $campTargetModel->update($campTarget['id'], [
                        'opened' => 1,
                        'clicked' => 1,
                        'submitted' => 1,
                        'last_activity' => date('Y-m-d H:i:s')
                    ]);
                }

                $logModel = new TrackingLogModel();
                $logModel->insert([
                    'campaign_id' => $victimCampaignId ?: ($campTarget['campaign_id'] ?? null),
                    'email' => $victimEmail,
                    'action' => 'submitted_data',
                    'ip_address' => $this->request->getIPAddress(),
                    'user_agent' => (string) $this->request->getUserAgent(),
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Quiz DB error: ' . $e->getMessage());
        }

        return view('quiz/result', [
            'score' => $score,
            'total' => $total,
            'passed' => $passed
        ]);
    }
}
