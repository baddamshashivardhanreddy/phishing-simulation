<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🚨 Security Alert - Phishing Test</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica Neue', sans-serif;
        }

        /* ================= DARK TO LIGHT BLINKING BACKGROUND ================= */
        @keyframes darkToLightBlink {
            0% {
                background-color: #0b0102;
                box-shadow: inset 0 0 120px rgba(0, 0, 0, 0.95);
            }
            50% {
                background-color: #f41922;
                box-shadow: inset 0 0 220px rgba(255, 255, 255, 0.7), 0 0 140px rgba(255, 30, 30, 0.95);
            }
            100% {
                background-color: #0b0102;
                box-shadow: inset 0 0 120px rgba(0, 0, 0, 0.95);
            }
        }

        body {
            animation: darkToLightBlink 1.3s ease-in-out infinite;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 30px 20px;
            color: #ffffff;
            transition: background 0.3s;
        }

        body.paused {
            animation: none !important;
            background-color: #120304 !important;
            box-shadow: none !important;
        }

        /* ================= PHASE 1: YOU HAVE BEEN HACKED! (5 TO 6 SECONDS SCARE) ================= */
        @keyframes skullPulse {
            0%, 100% { transform: scale(1) rotate(-3deg); filter: drop-shadow(0 0 15px rgba(255,0,0,0.8)); }
            50% { transform: scale(1.22) rotate(3deg); filter: drop-shadow(0 0 45px rgba(255,255,255,1)) drop-shadow(0 0 80px rgba(255,0,0,1)); }
        }

        @keyframes glitchBlink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.15; }
        }

        #hacked-scare-section {
            display: flex;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1000;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #ffffff;
            padding: 20px;
        }

        .skull-symbol {
            font-size: 90px;
            display: inline-block;
            margin-bottom: 15px;
            animation: skullPulse 0.9s ease-in-out infinite alternate;
        }

        .hacked-title {
            font-size: 44px;
            font-weight: 900;
            letter-spacing: 2px;
            color: #ffffff;
            text-shadow: 0 0 25px #ff0000, 0 0 50px #ff0000;
            margin-bottom: 12px;
            animation: glitchBlink 0.8s infinite;
        }

        .hacked-subtitle {
            font-size: 20px;
            color: #ffd2d2;
            font-weight: 600;
            margin-bottom: 25px;
        }

        .terminal-box {
            background: rgba(0, 0, 0, 0.85);
            border: 2px solid #ff3333;
            border-radius: 8px;
            padding: 16px 24px;
            max-width: 650px;
            width: 100%;
            text-align: left;
            font-family: 'Consolas', 'Courier New', monospace;
            font-size: 13.5px;
            color: #00ff66;
            line-height: 1.6;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.9);
        }

        .terminal-box .err {
            color: #ff3333;
            font-weight: bold;
        }

        .scare-timer-bar {
            margin-top: 24px;
            font-size: 14px;
            color: #fca5a5;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .scare-progress-track {
            width: 320px;
            height: 8px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            margin: 10px auto 0 auto;
            overflow: hidden;
        }

        .scare-progress-fill {
            height: 100%;
            width: 100%;
            background: #ef4444;
            animation: progressShrink 5.5s linear forwards;
        }

        @keyframes progressShrink {
            from { width: 100%; }
            to { width: 0%; }
        }

        /* ================= PHASE 2: PHISHING TEST DEBRIEF ================= */
        #phishing-test-section {
            display: none;
            width: 100%;
            min-height: 100vh;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            position: relative;
            z-index: 900;
        }

        .test-card {
            max-width: 820px;
            width: 100%;
            background: rgba(14, 2, 4, 0.94);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 2px solid #22c55e;
            border-radius: 16px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.9), 0 0 35px rgba(34, 197, 94, 0.35);
            color: #ffffff;
            padding: 40px;
            text-align: center;
        }

        .test-badge {
            display: inline-block;
            background: #16a34a;
            color: white;
            font-weight: 800;
            font-size: 13px;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 6px 18px;
            border-radius: 20px;
            margin-bottom: 14px;
            box-shadow: 0 0 15px rgba(34, 197, 94, 0.6);
        }

        .test-card h1 {
            font-size: 30px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 10px;
        }

        .test-card .sub {
            font-size: 16px;
            color: #d1fae5;
            margin-bottom: 24px;
            line-height: 1.5;
        }

        .captured-data-card {
            background: rgba(30, 10, 15, 0.85);
            border: 1px solid #ef4444;
            border-radius: 10px;
            padding: 20px 24px;
            margin: 20px 0;
            text-align: left;
        }

        .captured-data-card h3 {
            font-size: 15px;
            font-weight: 700;
            color: #fca5a5;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
        }

        .data-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px 20px;
            font-size: 14px;
        }

        .data-grid div {
            color: #cbd5e1;
            word-break: break-all;
        }

        .data-grid strong {
            display: block;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            margin-bottom: 2px;
        }

        .data-grid .highlight-val {
            color: #f87171;
            font-weight: 700;
            font-size: 14.5px;
        }

        .flags-box {
            text-align: left;
            margin: 24px 0;
        }

        .flags-box h3 {
            font-size: 14px;
            font-weight: 700;
            color: #fef08a;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .flag-item {
            background: rgba(255, 255, 255, 0.05);
            border-left: 4px solid #facc15;
            padding: 12px 16px;
            border-radius: 0 8px 8px 0;
            margin-bottom: 10px;
            font-size: 13.5px;
            line-height: 1.5;
            color: #f1f5f9;
        }

        .flag-item strong {
            color: #fde047;
        }

        .btn-ack {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            font-size: 15px;
            font-weight: 700;
            padding: 12px 36px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
            transition: all 0.2s ease;
        }

        .btn-ack:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.6);
        }

        @media (max-width: 600px) {
            .test-card { padding: 24px 18px; }
            .data-grid { grid-template-columns: 1fr; }
            .hacked-title { font-size: 30px; }
        }
    </style>
</head>
<body id="page-body">

    <!-- PHASE 1: YOU HAVE BEEN HACKED! (5 TO 6 SECONDS SCARE) -->
    <div id="hacked-scare-section">
        <div class="skull-symbol">☠️</div>
        <h1 class="hacked-title">YOU HAVE BEEN HACKED!</h1>
        <p class="hacked-subtitle">All your corporate files and personal credentials are being encrypted...</p>

        <div class="terminal-box">
            <div><span class="err">[CRITICAL ALERT]</span> Unverified form submitted to unauthorized host</div>
            <div>[+] Intercepting entered target credentials... <span style="color:#fff;">OK</span></div>
            <div>[+] Target: <span style="color:#ffcc00;"><?= esc(trim(($target['first_name'] ?? '') . ' ' . ($target['last_name'] ?? '')) ?: 'Corporate Target') ?></span></div>
            <div>[+] Email: <span style="color:#ffcc00;"><?= esc($email ?: 'employee@company.com') ?></span></div>
            <div>[+] Logging Remote Host & IP Address... <span style="color:#fff;">10.10.0.190</span></div>
            <div><span class="err">[!] Initializing ransomware encryption payload...</span></div>
        </div>

        <div class="scare-timer-bar">
            <span>SECURITY OVERRIDE IN PROGRESS: <span id="countdown-num">5</span>s</span>
            <div class="scare-progress-track">
                <div class="scare-progress-fill"></div>
            </div>
        </div>
    </div>

    <!-- PHASE 2: PHISHING TEST SIMULATION DEBRIEF -->
    <?php if (!isset($showDebrief) || !empty($showDebrief)): ?>
    <div id="phishing-test-section">
        <div class="test-card">
            <div class="test-badge">🛡️ INTERNAL SECURITY SIMULATION</div>
            <h1>THIS WAS A PHISHING TEST</h1>
            <p class="sub">
                <strong>RELAX: You have NOT been hacked, and no harm was done.</strong><br>
                This was an authorized internal cybersecurity exercise conducted by your IT Security department.
            </p>

            <!-- Intercepted Target Data Preview -->
            <div class="captured-data-card">
                <h3>⚠️ Data Intercepted in the Simulation:</h3>
                <div class="data-grid">
                    <div>
                        <strong>Target Name</strong>
                        <span class="highlight-val"><?= esc($submittedPayload['full_name'] ?? trim(($target['first_name'] ?? '') . ' ' . ($target['last_name'] ?? '')) ?: 'Employee') ?></span>
                    </div>
                    <div>
                        <strong>Work Email</strong>
                        <span class="highlight-val"><?= esc($submittedPayload['email'] ?? ($email ?: 'employee@company.com')) ?></span>
                    </div>
                    <div>
                        <strong>Employee ID</strong>
                        <span class="highlight-val"><?= esc($submittedPayload['employee_id'] ?? ($target['employee_id'] ?? 'EMP-SUBMITTED')) ?></span>
                    </div>
                    <div>
                        <strong>Employment Status</strong>
                        <span class="highlight-val"><?= esc($submittedPayload['employed'] ?? ($target['position'] ?? 'Full-Time Active Employee')) ?></span>
                    </div>
                    <div>
                        <strong>Submission Timestamp</strong>
                        <span><?= esc($submittedPayload['submitted_at'] ?? date('Y-m-d H:i:s')) ?></span>
                    </div>
                    <div>
                        <strong>Simulated Attack Outcome</strong>
                        <span style="color: #ef4444; font-weight: 700;">Credentials Intercepted</span>
                    </div>
                </div>
            </div>

            <!-- Danger Explanation -->
            <div style="background: rgba(239, 68, 68, 0.12); border: 1px solid #ef4444; border-radius: 8px; padding: 14px 18px; margin: 18px 0; text-align: left; font-size: 13.5px; line-height: 1.5; color: #fecaca;">
                <strong style="color: #ff8080;">Why this was dangerous:</strong> In a real cyber attack, unauthorized threat actors would now possess your identity, Employee ID, and email credentials to breach internal corporate networks.
            </div>

            <!-- Educational Red Flags -->
            <div class="flags-box">
                <h3>🔍 Red Flags You Should Have Noticed:</h3>
                <div class="flag-item">
                    <strong>1. Festive Bonus & Free Gifts Bait:</strong>
                    Attackers exploit seasonal celebrations, gifts, and bonus urgency to bypass caution.
                </div>
                <div class="flag-item">
                    <strong>2. Unverified Form URL:</strong>
                    Check the browser address bar. Authentic internal company bonus programs are never hosted on untrusted public links.
                </div>
                <div class="flag-item">
                    <strong>3. Data Harvesting Trap:</strong>
                    Official HR systems never ask you to re-enter your Employee ID or credentials on an external form.
                </div>
            </div>

            <div style="margin-top: 25px;">
                <button type="button" class="btn-ack" onclick="handleSimulationAck()">
                    ✓ I Understand - Return to Safe Work
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="<?= base_url('assets/dialogs.js') ?>"></script>
    <script>
        function handleSimulationAck() {
            customAlert({
                title: 'Simulation Acknowledged',
                message: 'Thank you for reviewing the security drill flags. You may safely close this window.',
                type: 'success',
                btnText: 'Close Window'
            }).then(function() {
                try { window.close(); } catch(e) {}
            });
        }

        // Configurable Red Screen Duration (Default 5s) & Debrief Toggle
        var duration = <?= (int)($redScreenDuration ?? 5) ?>;
        if (duration <= 0) duration = 5;
        var showDebrief = <?= (!isset($showDebrief) || !empty($showDebrief)) ? 'true' : 'false' ?>;

        var secondsLeft = duration;
        var countdownEl = document.getElementById('countdown-num');
        if (countdownEl) countdownEl.textContent = secondsLeft;

        var timerInterval = setInterval(function() {
            secondsLeft--;
            if (secondsLeft >= 0 && countdownEl) {
                countdownEl.textContent = secondsLeft;
            }
            if (secondsLeft <= 0) {
                clearInterval(timerInterval);
            }
        }, 1000);

        setTimeout(function() {
            clearInterval(timerInterval);
            var phishingTest = document.getElementById('phishing-test-section');
            var hackedScare = document.getElementById('hacked-scare-section');
            if (showDebrief && phishingTest) {
                if (hackedScare) hackedScare.style.display = 'none';
                phishingTest.style.display = 'flex';
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                // Locked terminal state: remain on red screen without showing debrief
                var timerBar = document.querySelector('.scare-timer-bar');
                if (timerBar) {
                    timerBar.innerHTML = '<span style="color:#ef4444; font-weight:800; font-size:1.05rem;">🔒 WORKSTATION LOCKED - CONTACT INCIDENT RESPONSE</span>';
                }
                var terminal = document.querySelector('.terminal-box');
                if (terminal) {
                    terminal.innerHTML += '<div style="color:#ff3333; margin-top:8px; font-weight:bold;">[!] System lock active. Remote connection established. Workstation isolated.</div>';
                }
            }
        }, duration * 1000);
    </script>
</body>
</html>
