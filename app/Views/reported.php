<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phishing Simulation Reported - Security Awareness</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Inter',sans-serif; }
        body {
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 50%, #e0f2fe 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #1e293b;
        }
        .card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            max-width: 580px;
            width: 100%;
            overflow: hidden;
            border: 1px solid #d1fae5;
            animation: fadeIn 0.4s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .header {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            padding: 32px 24px;
            text-align: center;
        }
        .icon-circle {
            width: 72px;
            height: 72px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 40px;
        }
        .content {
            padding: 32px 28px;
            line-height: 1.6;
        }
        .badge-hero {
            display: inline-block;
            background: #d1fae5;
            color: #065f46;
            font-weight: 700;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .tip-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #10b981;
            border-radius: 8px;
            padding: 16px;
            margin: 20px 0;
            font-size: 0.9rem;
        }
        .btn-close {
            display: inline-block;
            width: 100%;
            background: #059669;
            color: white;
            text-align: center;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
            transition: background 0.2s;
        }
        .btn-close:hover {
            background: #047857;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <div class="icon-circle">
                <ion-icon name="shield-checkmark"></ion-icon>
            </div>
            <h1 style="font-size: 1.6rem; font-weight: 800; margin-bottom: 6px;">Phishing Attempt Reported!</h1>
            <p style="font-size: 0.95rem; opacity: 0.9;">Information Security Awareness Program</p>
        </div>
        <div class="content">
            <div style="text-align: center;">
                <span class="badge-hero">🛡️ Assessment Result: PASSED</span>
                <h3 style="font-size: 1.2rem; color: #0f172a; margin-bottom: 8px;">Outstanding vigilance, <?= esc($email) ?>!</h3>
                <p style="color: #475569; font-size: 0.95rem;">
                    You recognized this email as a suspicious phishing test and reported it to IT Security.
                </p>
            </div>

            <div class="tip-box">
                <strong style="color: #065f46; display: block; margin-bottom: 6px;">Why Your Action Matters:</strong>
                <ul style="padding-left: 20px; color: #475569; margin: 0;">
                    <li>Reporting suspicious emails helps protect the entire organization from cyber threats.</li>
                    <li>You avoided clicking dangerous links or submitting sensitive corporate credentials.</li>
                    <li>Your positive response has been recorded on the Information Security training dashboard.</li>
                </ul>
            </div>

            <button type="button" class="btn-close" onclick="window.close();">
                Close Window / Return to Work
            </button>
        </div>
    </div>
</body>
</html>

