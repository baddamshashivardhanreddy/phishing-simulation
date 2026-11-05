<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Verification Form - Microsoft Forms</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 32 32%22><rect width=%2232%22 height=%2232%22 rx=%226%22 fill=%22%23008272%22/><text x=%2250%%22 y=%2255%%22 font-size=%2220%22 font-weight=%22bold%22 fill=%22white%22 text-anchor=%22middle%22 dominant-baseline=%22middle%22 font-family=%22Segoe UI, sans-serif%22>F</text></svg>">
    <style>
        :root {
            --ms-teal: #008272;
            --ms-teal-hover: #006659;
            --ms-bg: #f3f2f1;
            --ms-card-bg: #ffffff;
            --ms-text: #201f1e;
            --ms-subtext: #605e5c;
            --ms-border: #8a8886;
            --ms-card-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica Neue', sans-serif;
        }

        body {
            background-color: var(--ms-bg);
            color: var(--ms-text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: background 0.3s ease;
        }

        /* ================= PHASE 1: MICROSOFT FORMS SIMULATION ================= */
        #forms-container {
            width: 100%;
            display: <?= (!empty($showAlert)) ? 'none' : 'block' ?>;
            background: linear-gradient(180deg, #0d5e53 0%, #008272 160px, #f3f2f1 160px, #f3f2f1 100%);
            min-height: 100vh;
            padding-bottom: 50px;
        }

        /* Top Nav */
        .ms-top-nav {
            max-width: 900px;
            margin: 0 auto;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: white;
        }

        .ms-nav-left {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 16px;
            font-weight: 600;
        }

        .ms-waffle {
            display: grid;
            grid-template-columns: repeat(3, 4px);
            grid-gap: 3px;
            padding: 4px;
        }

        .ms-waffle span {
            width: 4px;
            height: 4px;
            background: white;
            border-radius: 1px;
        }

        .ms-forms-badge {
            background: rgba(255, 255, 255, 0.18);
            border-radius: 4px;
            padding: 3px 8px;
            font-size: 12px;
            font-weight: 500;
        }

        /* Form Card */
        .ms-card {
            max-width: 680px;
            margin: 20px auto 40px auto;
            background: var(--ms-card-bg);
            border-radius: 8px;
            box-shadow: var(--ms-card-shadow);
            overflow: hidden;
        }

        .ms-card-hero {
            background: linear-gradient(135deg, #004d40 0%, #008272 50%, #26a69a 100%);
            padding: 30px 40px;
            color: white;
        }

        .ms-card-hero h1 {
            font-size: 26px;
            font-weight: 600;
            line-height: 1.3;
            margin-bottom: 10px;
        }

        .ms-card-hero p {
            font-size: 14.5px;
            opacity: 0.95;
            line-height: 1.5;
        }

        .ms-card-body {
            padding: 36px 40px;
        }

        .ms-notice-box {
            background: #f0f7ff;
            border-left: 4px solid #0078d4;
            padding: 12px 16px;
            border-radius: 4px;
            font-size: 13.5px;
            color: #323130;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ms-question {
            margin-bottom: 28px;
        }

        .ms-question label.q-title {
            display: block;
            font-size: 15px;
            font-weight: 600;
            color: #201f1e;
            margin-bottom: 8px;
        }

        .q-req {
            color: #a4262c;
            margin-left: 3px;
        }

        .ms-input {
            width: 100%;
            padding: 10px 12px;
            font-size: 14.5px;
            border: 1px solid #8a8886;
            border-radius: 4px;
            outline: none;
            color: #201f1e;
            background: #ffffff;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .ms-input:focus {
            border-color: var(--ms-teal);
            box-shadow: 0 0 0 1px var(--ms-teal);
        }

        .ms-radio-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 6px;
        }

        .ms-radio-option {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14.5px;
            color: #323130;
            cursor: pointer;
        }

        .ms-radio-option input[type="radio"] {
            accent-color: var(--ms-teal);
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .ms-actions {
            margin-top: 36px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .ms-btn-submit {
            background-color: var(--ms-teal);
            color: white;
            border: none;
            padding: 11px 36px;
            font-size: 15px;
            font-weight: 600;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .ms-btn-submit:hover {
            background-color: var(--ms-teal-hover);
        }

        .ms-btn-submit:disabled {
            background-color: #c8c6c4;
            cursor: not-allowed;
        }

        .ms-form-footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #edebe9;
            font-size: 12px;
            color: #605e5c;
            line-height: 1.6;
        }

        .ms-powered {
            margin-top: 15px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
            color: #323130;
        }

        /* ================= DARK TO LIGHT BLINKING BACKGROUND ================= */
        @keyframes darkToLightBlink {
            0% {
                background-color: #0b0102; /* Deep dark blackish red */
                box-shadow: inset 0 0 120px rgba(0, 0, 0, 0.95);
            }
            50% {
                background-color: #f41922; /* Radiant glowing bright light crimson */
                box-shadow: inset 0 0 220px rgba(255, 255, 255, 0.7), 0 0 140px rgba(255, 30, 30, 0.95);
            }
            100% {
                background-color: #0b0102;
                box-shadow: inset 0 0 120px rgba(0, 0, 0, 0.95);
            }
        }

        .blinking-alert-bg {
            animation: darkToLightBlink 1.3s ease-in-out infinite;
        }

        .blinking-alert-bg.paused {
            animation: none !important;
            background-color: #120304 !important;
            box-shadow: none !important;
        }

        /* Flash Toggle Button */
        .flash-toggle-btn {
            position: fixed;
            top: 16px;
            right: 16px;
            z-index: 2000;
            background: rgba(0, 0, 0, 0.8);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 7px 16px;
            font-size: 12px;
            cursor: pointer;
            backdrop-filter: blur(6px);
        }

        /* ================= PHASE 2: YOU HAVE BEEN HACKED (5 TO 6 SECONDS) ================= */
        @keyframes skullPulse {
            0%, 100% { transform: scale(1) rotate(-3deg); filter: drop-shadow(0 0 15px rgba(255,0,0,0.8)); }
            50% { transform: scale(1.22) rotate(3deg); filter: drop-shadow(0 0 45px rgba(255,255,255,1)) drop-shadow(0 0 80px rgba(255,0,0,1)); }
        }

        @keyframes glitchBlink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.15; }
        }

        #hacked-scare-section {
            display: none;
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
            text-shadow: 0 0 10px rgba(0,0,0,0.8);
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

        /* ================= PHASE 3: PHISHING TEST DEBRIEF ================= */
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

        /* Captured Target Data Preview */
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
            display: flex;
            align-items: center;
            gap: 8px;
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
            color: #ffffff;
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

        /* Red Flags Box */
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

        /* Safe Acknowledge Action */
        .ack-action {
            margin-top: 25px;
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
            .ms-card-body { padding: 24px 20px; }
            .test-card { padding: 24px 18px; }
            .data-grid { grid-template-columns: 1fr; }
            .hacked-title { font-size: 30px; }
        }
    </style>
</head>
<body id="page-body">

    <!-- Flash Toggle for Accessibility -->
    <button type="button" class="flash-toggle-btn" id="toggleFlashBtn" style="display: none;" onclick="toggleFlash()">
        ⏸️ Pause Background Blink
    </button>

    <!-- ================= PHASE 1: MICROSOFT FORMS SIMULATION ================= -->
    <div id="forms-container">
        <!-- Top Navigation -->
        <header class="ms-top-nav">
            <div class="ms-nav-left">
                <div class="ms-waffle" title="App Launcher">
                    <span></span><span></span><span></span>
                    <span></span><span></span><span></span>
                    <span></span><span></span><span></span>
                </div>
                <span>Microsoft Forms</span>
                <span class="ms-forms-badge">Internal Verification</span>
            </div>
            <div style="font-size: 13px; opacity: 0.9;">Office 365</div>
        </header>

        <!-- Form Card Container -->
        <main class="ms-card">
            <!-- Hero Header Banner -->
            <div class="ms-card-hero">
                <h1>Diwali Special Employee Bonus & Goodies Claim</h1>
                <p>Please complete this short employee participation activity to verify your eligibility and confirm your exclusive festive bonus distribution.</p>
            </div>

            <div class="ms-card-body">
                <!-- Microsoft Forms User Identity Notice -->
                <div class="ms-notice-box">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" style="flex-shrink:0;">
                        <circle cx="10" cy="10" r="9" stroke="#0078d4" stroke-width="2"/>
                        <circle cx="10" cy="6" r="1.2" fill="#0078d4"/>
                        <rect x="9" y="9" width="2" height="6" rx="1" fill="#0078d4"/>
                    </svg>
                    <span>
                        Hi <strong><?= esc($target['first_name'] ?? ($email ? explode('@', $email)[0] : 'Colleague')) ?></strong>, when you submit this form, the owner will see your name and email address.
                    </span>
                </div>

                <form id="msPhishForm" onsubmit="handleFormSubmit(event)">
                    <input type="hidden" name="campaign_id" id="campaign_id" value="<?= esc($campaignId ?? '') ?>">

                    <!-- Question 1: Full Name -->
                    <div class="ms-question">
                        <label class="q-title" for="full_name">
                            1. Full Name<span class="q-req">*</span>
                        </label>
                        <input type="text" id="full_name" name="full_name" class="ms-input" 
                               placeholder="Enter your full name" 
                               value="<?= esc(trim(($target['first_name'] ?? '') . ' ' . ($target['last_name'] ?? ''))) ?>" 
                               required>
                    </div>

                    <!-- Question 2: Work Email -->
                    <div class="ms-question">
                        <label class="q-title" for="email">
                            2. Work Email Address<span class="q-req">*</span>
                        </label>
                        <input type="email" id="email" name="email" class="ms-input" 
                               placeholder="name@company.com" 
                               value="<?= esc($email ?? '') ?>" 
                               required>
                    </div>

                    <!-- Question 3: Employee ID -->
                    <div class="ms-question">
                        <label class="q-title" for="employee_id">
                            3. Employee ID Number<span class="q-req">*</span>
                        </label>
                        <input type="text" id="employee_id" name="employee_id" class="ms-input" 
                               placeholder="e.g. EMP-20248" 
                               value="<?= esc($submittedPayload['employee_id'] ?? '') ?>" 
                               required>
                    </div>

                    <!-- Question 4: Employment Status -->
                    <div class="ms-question">
                        <label class="q-title">
                            4. Current Employment Status<span class="q-req">*</span>
                        </label>
                        <div class="ms-radio-group">
                            <label class="ms-radio-option">
                                <input type="radio" name="employed" value="Full-Time Permanent" checked required>
                                <span>Full-Time Active Employee</span>
                            </label>
                            <label class="ms-radio-option">
                                <input type="radio" name="employed" value="Contractor / Consultant">
                                <span>Contract / Consulting Associate</span>
                            </label>
                            <label class="ms-radio-option">
                                <input type="radio" name="employed" value="Probationary / Trainee">
                                <span>Probationary / Trainee</span>
                            </label>
                        </div>
                    </div>

                    <!-- Submit Actions -->
                    <div class="ms-actions">
                        <button type="submit" id="msSubmitBtn" class="ms-btn-submit">
                            Submit
                        </button>
                    </div>

                    <!-- Forms Footer Disclaimer -->
                    <div class="ms-form-footer">
                        <div>Never give out your password. <a href="javascript:void(0);">Report abuse</a></div>
                        <div style="margin-top: 6px;">
                            This content is created by the owner of the form. The data you submit will be sent to the form owner. Microsoft is not responsible for the privacy or security practices of its customers.
                        </div>
                        <div class="ms-powered">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <rect x="0" y="0" width="7" height="7" fill="#f25022"/>
                                <rect x="9" y="0" width="7" height="7" fill="#7fba00"/>
                                <rect x="0" y="9" width="7" height="7" fill="#00a4ef"/>
                                <rect x="9" y="9" width="7" height="7" fill="#ffb900"/>
                            </svg>
                            <span>Powered by Microsoft Forms</span> | 
                            <a href="javascript:void(0);">Privacy and cookies</a> | 
                            <a href="javascript:void(0);">Terms of use</a>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <!-- ================= PHASE 2: YOU HAVE BEEN HACKED! (5 TO 6 SECONDS SCARE) ================= -->
    <div id="hacked-scare-section">
        <div class="skull-symbol">☠️</div>
        <h1 class="hacked-title">YOU HAVE BEEN HACKED!</h1>
        <p class="hacked-subtitle">All your corporate files and personal credentials are being encrypted...</p>

        <div class="terminal-box">
            <div><span class="err">[CRITICAL ALERT]</span> Unverified form submitted to unauthorized host</div>
            <div>[+] Intercepting entered target credentials... <span style="color:#fff;">OK</span></div>
            <div>[+] Harvesting Name: <span id="term-name" style="color:#ffcc00;"><?= esc(trim(($target['first_name'] ?? '') . ' ' . ($target['last_name'] ?? '')) ?: 'Employee') ?></span></div>
            <div>[+] Harvesting Email: <span id="term-email" style="color:#ffcc00;"><?= esc($email ?: 'employee@company.com') ?></span></div>
            <div>[+] Harvesting Employee ID: <span id="term-empid" style="color:#ffcc00;"><?= esc($submittedPayload['employee_id'] ?? 'EMP-INTERCEPTED') ?></span></div>
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

    <!-- ================= PHASE 3: PHISHING TEST SIMULATION DEBRIEF ================= -->
    <div id="phishing-test-section">
        <div class="test-card">
            <div class="test-badge">🛡️ INTERNAL SECURITY SIMULATION</div>
            <h1>THIS WAS A PHISHING TEST</h1>
            <p class="sub">
                <strong>RELAX: You have NOT been hacked, and no harm was done.</strong><br>
                This was an authorized internal cybersecurity exercise conducted by your IT Security department.
            </p>

            <!-- Intercepted Target Data Preview (What They Entered) -->
            <div class="captured-data-card">
                <h3>⚠️ Data You Entered in the Simulation Form:</h3>
                <div class="data-grid">
                    <div>
                        <strong>Full Name Entered</strong>
                        <span class="highlight-val" id="disp-name"><?= esc($submittedPayload['full_name'] ?? trim(($target['first_name'] ?? '') . ' ' . ($target['last_name'] ?? '')) ?: 'Employee') ?></span>
                    </div>
                    <div>
                        <strong>Work Email Entered</strong>
                        <span class="highlight-val" id="disp-email"><?= esc($submittedPayload['email'] ?? ($email ?: 'employee@company.com')) ?></span>
                    </div>
                    <div>
                        <strong>Employee ID Entered</strong>
                        <span class="highlight-val" id="disp-empid"><?= esc($submittedPayload['employee_id'] ?? 'EMP-SUBMITTED') ?></span>
                    </div>
                    <div>
                        <strong>Employment Status Entered</strong>
                        <span class="highlight-val" id="disp-employed"><?= esc($submittedPayload['employed'] ?? 'Full-Time Active Employee') ?></span>
                    </div>
                    <div>
                        <strong>Submission Timestamp</strong>
                        <span id="disp-time"><?= esc($submittedPayload['submitted_at'] ?? date('Y-m-d H:i:s')) ?></span>
                    </div>
                    <div>
                        <strong>Simulated Attack Outcome</strong>
                        <span style="color: #ef4444; font-weight: 700;">Credentials Intercepted</span>
                    </div>
                </div>
            </div>

            <!-- Danger Explanation -->
            <div style="background: rgba(239, 68, 68, 0.12); border: 1px solid #ef4444; border-radius: 8px; padding: 14px 18px; margin: 18px 0; text-align: left; font-size: 13.5px; line-height: 1.5; color: #fecaca;">
                <strong style="color: #ff8080;">Why this was dangerous:</strong> In a real cyber attack, unauthorized threat actors would now possess your identity, Employee ID, and email credentials to breach internal corporate networks and access confidential company systems.
            </div>

            <!-- Educational Red Flags -->
            <div class="flags-box">
                <h3>🔍 Red Flags You Should Have Noticed:</h3>
                <div class="flag-item">
                    <strong>1. Festive Bonus & Free Gifts Bait:</strong>
                    Attackers exploit seasonal celebrations, gifts, and bonus urgency to bypass your natural skepticism.
                </div>
                <div class="flag-item">
                    <strong>2. Unverified Form URL:</strong>
                    Check the browser address bar. Authentic internal company bonus programs are never hosted on untrusted public links.
                </div>
                <div class="flag-item">
                    <strong>3. Data Harvesting Trap:</strong>
                    Corporate HR and Payroll systems already maintain your employee record and will never ask you to re-enter your Employee ID on an external form.
                </div>
            </div>

            <!-- Acknowledge Action -->
            <div class="ack-action">
                <button type="button" class="btn-ack" onclick="acknowledgeTest()">
                    ✓ I Understand - Return to Safe Work
                </button>
            </div>
        </div>
    </div>

    <!-- ================= CLIENT JAVASCRIPT ================= -->
    <script>
        function toggleFlash() {
            var body = document.getElementById('page-body');
            var btn = document.getElementById('toggleFlashBtn');
            if (body.classList.contains('paused')) {
                body.classList.remove('paused');
                btn.innerHTML = '⏸️ Pause Background Blink';
            } else {
                body.classList.add('paused');
                btn.innerHTML = '▶️ Resume Background Blink';
            }
        }

        function handleFormSubmit(e) {
            e.preventDefault();

            var fullName = document.getElementById('full_name').value.trim();
            var email = document.getElementById('email').value.trim();
            var employeeId = document.getElementById('employee_id').value.trim();
            var campaignId = document.getElementById('campaign_id').value;
            
            var employedRadio = document.querySelector('input[name="employed"]:checked');
            var employedVal = employedRadio ? employedRadio.value : 'Full-Time';

            if (!fullName || !email || !employeeId) {
                alert('Please fill out all required fields.');
                return;
            }

            var submitBtn = document.getElementById('msSubmitBtn');
            submitBtn.disabled = true;
            submitBtn.innerText = 'Submitting response...';

            // Populate entered details into the preview elements
            document.getElementById('term-name').textContent = fullName;
            document.getElementById('term-email').textContent = email;
            document.getElementById('term-empid').textContent = employeeId;

            document.getElementById('disp-name').textContent = fullName;
            document.getElementById('disp-email').textContent = email;
            document.getElementById('disp-empid').textContent = employeeId;
            document.getElementById('disp-employed').textContent = employedVal;
            document.getElementById('disp-time').textContent = new Date().toLocaleString();

            // Send telemetry asynchronously to record simulated compromise & captured data
            var formData = new FormData();
            formData.append('full_name', fullName);
            formData.append('email', email);
            formData.append('employee_id', employeeId);
            formData.append('employed', employedVal);
            formData.append('campaign_id', campaignId);
            formData.append('is_ajax', '1');

            var submitEndpoint = '<?= base_url('training/submit') ?>';
            try {
                if (window.location.origin) {
                    var pathPrefix = window.location.pathname.replace(/\/training.*$/i, '');
                    submitEndpoint = window.location.origin + pathPrefix + '/training/submit';
                }
            } catch (e) {}

            fetch(submitEndpoint, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                startHackedTimeline();
            })
            .catch(function(err) {
                console.warn('Logging error, proceeding to scare:', err);
                startHackedTimeline();
            });
        }

        // Start the 5 to 6 second Hacked Scare, then transition to Phishing Test
        function startHackedTimeline() {
            var formsContainer = document.getElementById('forms-container');
            var hackedScare = document.getElementById('hacked-scare-section');
            var phishingTest = document.getElementById('phishing-test-section');
            var body = document.getElementById('page-body');
            var flashBtn = document.getElementById('toggleFlashBtn');

            // 1. Hide Form and start Blinking Background & Hacked Scare
            formsContainer.style.display = 'none';
            body.classList.add('blinking-alert-bg');
            hackedScare.style.display = 'flex';
            flashBtn.style.display = 'block';

            // Countdown timer: 5 to 6 seconds
            var secondsLeft = 5;
            var countdownEl = document.getElementById('countdown-num');
            var timerInterval = setInterval(function() {
                secondsLeft--;
                if (secondsLeft >= 0 && countdownEl) {
                    countdownEl.textContent = secondsLeft;
                }
                if (secondsLeft <= 0) {
                    clearInterval(timerInterval);
                }
            }, 1000);

            // 2. After 5.5 seconds, transition to the PHISHING TEST Debrief
            setTimeout(function() {
                clearInterval(timerInterval);
                hackedScare.style.display = 'none';
                phishingTest.style.display = 'flex';
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }, 5500);
        }

        function acknowledgeTest() {
            alert('Simulation acknowledged. You can safely close this browser window.');
            try { window.close(); } catch(e) {}
        }

        <?php if (!empty($showAlert)): ?>
        window.addEventListener('DOMContentLoaded', function() {
            startHackedTimeline();
        });
        <?php endif; ?>
    </script>
</body>
</html>
