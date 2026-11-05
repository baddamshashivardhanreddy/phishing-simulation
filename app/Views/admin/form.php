<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulation Landing Form Editor - PhishGuard</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('icon.svg') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/admin.css') ?>">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            align-items: start;
        }
        @media (max-width: 1100px) {
            .form-grid-2 {
                grid-template-columns: 1fr;
            }
        }
        .editor-section {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            padding: 20px 22px;
            margin-bottom: 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        }
        .editor-section-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 10px;
        }
        .color-chips {
            display: flex;
            gap: 8px;
            align-items: center;
            margin-top: 8px;
            flex-wrap: wrap;
        }
        .color-chip {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            cursor: pointer;
            border: 2px solid #ffffff;
            box-shadow: 0 0 0 1px #cbd5e1;
            transition: transform 0.15s ease;
        }
        .color-chip:hover {
            transform: scale(1.15);
        }

        /* Live Preview Card styles simulating MS Forms */
        .preview-sticky-wrap {
            position: sticky;
            top: 20px;
        }
        .preview-container {
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
            background: #f3f2f1;
            font-family: 'Segoe UI', -apple-system, sans-serif;
        }
        .preview-header-bar {
            padding: 10px 14px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .preview-ms-nav {
            padding: 12px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: white;
            font-weight: 600;
            font-size: 0.95rem;
        }
        .preview-ms-badge {
            background: rgba(255, 255, 255, 0.22);
            border-radius: 4px;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 500;
        }
        .preview-ms-card {
            max-width: 92%;
            margin: 15px auto 25px auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }
        .preview-ms-hero {
            padding: 22px 24px;
            color: white;
            transition: background 0.2s ease;
        }
        .preview-ms-hero h2 {
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 8px;
            line-height: 1.3;
        }
        .preview-ms-hero p {
            font-size: 0.85rem;
            opacity: 0.95;
            line-height: 1.5;
            margin: 0;
        }
        .preview-ms-body {
            padding: 20px 22px;
        }
        .preview-ms-notice {
            background: #f8fafc;
            border-left: 4px solid #0078d4;
            padding: 9px 12px;
            border-radius: 4px;
            font-size: 0.78rem;
            color: #334155;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .preview-ms-q {
            margin-bottom: 16px;
        }
        .preview-ms-q-title {
            font-size: 0.84rem;
            font-weight: 700;
            color: #201f1e;
            margin-bottom: 5px;
            display: block;
        }
        .preview-ms-input {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #8a8886;
            border-radius: 3px;
            font-size: 0.82rem;
            background: #ffffff;
            color: #64748b;
            box-sizing: border-box;
        }
        .preview-ms-btn {
            color: white;
            padding: 8px 24px;
            font-size: 0.85rem;
            font-weight: 600;
            border: none;
            border-radius: 3px;
            cursor: default;
        }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="brand">
            <ion-icon name="shield-checkmark-outline"></ion-icon>
            <span>PhishGuard</span>
        </div>
        <ul class="nav-links">
            <li><a href="<?= base_url('admin') ?>"><ion-icon name="grid-outline" style="margin-right:10px"></ion-icon> <span>Campaign Dashboard</span></a></li>
            <li><a href="<?= base_url('admin/campaigns') ?>"><ion-icon name="albums-outline" style="margin-right:10px"></ion-icon> <span>Campaigns</span></a></li>
            <li><a href="<?= base_url('admin/users') ?>"><ion-icon name="people-outline" style="margin-right:10px"></ion-icon> <span>Simulation Users</span></a></li>
            <li><a href="<?= base_url('admin/templates') ?>"><ion-icon name="mail-outline" style="margin-right:10px"></ion-icon> <span>Email Templates</span></a></li>
            <li><a href="<?= base_url('admin/form') ?>" class="active"><ion-icon name="newspaper-outline" style="margin-right:10px"></ion-icon> <span>Landing Form</span></a></li>
            <li><a href="<?= base_url('admin/profile') ?>"><ion-icon name="shield-outline" style="margin-right:10px"></ion-icon> <span>Admin Users</span></a></li>
            <li><a href="<?= base_url('admin/settings') ?>"><ion-icon name="settings-outline" style="margin-right:10px"></ion-icon> <span>Settings</span></a></li>
            <li><a href="<?= base_url('admin/export') ?>"><ion-icon name="download-outline" style="margin-right:10px"></ion-icon> <span>Export Data</span></a></li>
        </ul>
        <div style="margin-top: auto; padding: 20px;">
           <a href="<?= base_url('logout') ?>" style="color: #ef4444; text-decoration: none; display: flex; align-items: center;"><ion-icon name="log-out-outline" style="margin-right:10px"></ion-icon> Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">

        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div style="background: #d4edda; color: #155724; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #28a745; display: flex; align-items: center; justify-content: space-between;">
                <span><strong>Success:</strong> <?= session()->getFlashdata('success') ?></span>
                <span style="cursor:pointer;" onclick="this.parentElement.style.display='none';">&times;</span>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #dc3545; display: flex; align-items: center; justify-content: space-between;">
                <span><strong>Error:</strong> <?= session()->getFlashdata('error') ?></span>
                <span style="cursor:pointer;" onclick="this.parentElement.style.display='none';">&times;</span>
            </div>
        <?php endif; ?>

        <!-- Page Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.75rem; flex-wrap: wrap; gap: 15px;">
            <div>
                <h1 style="font-size: 1.75rem; font-weight: 700; color: #0f172a; margin: 0;">Simulation Landing Form Editor</h1>
                <p style="color: var(--text-muted); font-size: 0.92rem; margin-top: 4px;">
                    Customize the Microsoft Forms credential harvesting lure page presented to targets when credential collection is turned ON.
                </p>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="<?= base_url('training?has_form=1') ?>" target="_blank" class="btn-secondary" style="display: inline-flex; align-items: center; gap: 6px;">
                    <ion-icon name="open-outline"></ion-icon> Fullscreen Preview
                </a>
                <form action="<?= base_url('admin/form/reset') ?>" method="post" style="display: inline;">
                    <button type="submit" class="btn-secondary" style="color: #dc2626;" onclick="return confirmAction(event, 'Reset the simulation landing form back to default festive bonus settings?', { type: 'warning', title: 'Reset Form Settings?', confirmBtn: 'Yes, Reset Form' });">
                        <ion-icon name="refresh-outline"></ion-icon> Reset Defaults
                    </button>
                </form>
            </div>
        </div>

        <!-- Editor Dual Grid -->
        <div class="form-grid-2">

            <!-- LEFT COLUMN: Editor Controls -->
            <div>
                <form action="<?= base_url('admin/form/save') ?>" method="post" id="landingFormEditor">
                    <input type="hidden" name="id" value="<?= esc($form['id'] ?? 1) ?>">

                    <!-- SECTION 1: Brand & Theme -->
                    <div class="editor-section">
                        <div class="editor-section-title">
                            <ion-icon name="color-palette-outline" style="color: var(--primary-color); font-size: 1.25rem;"></ion-icon>
                            1. Header Branding & Theme Colors
                        </div>
                        <div class="form-group" style="margin-bottom: 14px;">
                            <label style="font-size: 0.84rem; font-weight: 600; color: #334155;">Internal Form Configuration Name</label>
                            <input type="text" name="name" class="form-control" value="<?= esc($form['name'] ?? 'Default Employee Verification Form') ?>" placeholder="e.g. Employee Bonus Claim Form" required>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label style="font-size: 0.84rem; font-weight: 600; color: #334155;">Brand Name</label>
                                <input type="text" name="brand_name" id="inputBrandName" class="form-control" value="<?= esc($form['brand_name'] ?? 'Microsoft Forms') ?>" oninput="updateLivePreview()">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label style="font-size: 0.84rem; font-weight: 600; color: #334155;">Top Badge Label</label>
                                <input type="text" name="badge_text" id="inputBadgeText" class="form-control" value="<?= esc($form['badge_text'] ?? 'Internal Verification') ?>" oninput="updateLivePreview()">
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="font-size: 0.84rem; font-weight: 600; color: #334155;">Brand Theme Color</label>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <input type="color" name="theme_color" id="inputThemeColor" value="<?= esc($form['theme_color'] ?? '#008272') ?>" style="width: 44px; height: 38px; border: 1px solid #cbd5e1; border-radius: 6px; padding: 2px; cursor: pointer;" oninput="updateThemeColor(this.value)">
                                <input type="text" id="themeColorHex" class="form-control" style="width: 120px; font-weight: 600;" value="<?= esc($form['theme_color'] ?? '#008272') ?>" oninput="updateThemeColor(this.value)">
                                <div class="color-chips">
                                    <div class="color-chip" style="background: #008272;" onclick="setThemePreset('#008272')" title="MS Teal"></div>
                                    <div class="color-chip" style="background: #0078d4;" onclick="setThemePreset('#0078d4')" title="Office Blue"></div>
                                    <div class="color-chip" style="background: #107c41;" onclick="setThemePreset('#107c41')" title="Excel Green"></div>
                                    <div class="color-chip" style="background: #5b21b6;" onclick="setThemePreset('#5b21b6')" title="Festive Purple"></div>
                                    <div class="color-chip" style="background: #0f172a;" onclick="setThemePreset('#0f172a')" title="Dark Slate"></div>
                                    <div class="color-chip" style="background: #dc2626;" onclick="setThemePreset('#dc2626')" title="Urgent Red"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: Hero Header & Description -->
                    <div class="editor-section">
                        <div class="editor-section-title">
                            <ion-icon name="text-outline" style="color: var(--primary-color); font-size: 1.25rem;"></ion-icon>
                            2. Hero Banner Content & Instructions
                        </div>
                        <div class="form-group" style="margin-bottom: 14px;">
                            <label style="font-size: 0.84rem; font-weight: 600; color: #334155;">Hero Title (Main Form Heading)</label>
                            <input type="text" name="hero_title" id="inputHeroTitle" class="form-control" value="<?= esc($form['hero_title'] ?? 'Diwali Special Employee Bonus & Goodies Claim') ?>" placeholder="e.g. Corporate Benefits & Verification Portal" required oninput="updateLivePreview()">
                        </div>
                        <div class="form-group" style="margin-bottom: 14px;">
                            <label style="font-size: 0.84rem; font-weight: 600; color: #334155;">Hero Description / Instructions</label>
                            <textarea name="hero_description" id="inputHeroDesc" class="form-control" rows="3" placeholder="Please complete this short employee participation activity..." oninput="updateLivePreview()"><?= esc($form['hero_description'] ?? '') ?></textarea>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="font-size: 0.84rem; font-weight: 600; color: #334155;">Greeting / Identity Notice <small style="color: #64748b;">(Use <code>{name}</code> for recipient's first name)</small></label>
                            <input type="text" name="notice_text" id="inputNoticeText" class="form-control" value="<?= esc($form['notice_text'] ?? 'Hi {name}, when you submit this form, the owner will see your name and email address.') ?>" oninput="updateLivePreview()">
                        </div>
                    </div>

                    <!-- SECTION 3: Questions & Input Fields -->
                    <div class="editor-section">
                        <div class="editor-section-title">
                            <ion-icon name="list-outline" style="color: var(--primary-color); font-size: 1.25rem;"></ion-icon>
                            3. Form Questions & Input Fields
                        </div>

                        <!-- Question 1: Name -->
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-bottom: 12px;">
                            <span style="font-weight: 700; font-size: 0.84rem; color: #0f172a; display: block; margin-bottom: 8px;">Question 1 (Full Name Harvest)</span>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                <input type="text" name="q1_label" id="inputQ1Label" class="form-control" value="<?= esc($form['q1_label'] ?? '1. Full Name') ?>" placeholder="Label" oninput="updateLivePreview()">
                                <input type="text" name="q1_placeholder" id="inputQ1Placeholder" class="form-control" value="<?= esc($form['q1_placeholder'] ?? 'Enter your full name') ?>" placeholder="Placeholder" oninput="updateLivePreview()">
                            </div>
                        </div>

                        <!-- Question 2: Work Email -->
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-bottom: 12px;">
                            <span style="font-weight: 700; font-size: 0.84rem; color: #0f172a; display: block; margin-bottom: 8px;">Question 2 (Corporate Email Harvest)</span>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                <input type="text" name="q2_label" id="inputQ2Label" class="form-control" value="<?= esc($form['q2_label'] ?? '2. Work Email Address') ?>" placeholder="Label" oninput="updateLivePreview()">
                                <input type="text" name="q2_placeholder" id="inputQ2Placeholder" class="form-control" value="<?= esc($form['q2_placeholder'] ?? 'name@company.com') ?>" placeholder="Placeholder" oninput="updateLivePreview()">
                            </div>
                        </div>

                        <!-- Question 3: Employee ID -->
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-bottom: 12px;">
                            <span style="font-weight: 700; font-size: 0.84rem; color: #0f172a; display: block; margin-bottom: 8px;">Question 3 (Employee ID Harvest)</span>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                <input type="text" name="q3_label" id="inputQ3Label" class="form-control" value="<?= esc($form['q3_label'] ?? '3. Employee ID Number') ?>" placeholder="Label" oninput="updateLivePreview()">
                                <input type="text" name="q3_placeholder" id="inputQ3Placeholder" class="form-control" value="<?= esc($form['q3_placeholder'] ?? 'e.g. EMP-20248') ?>" placeholder="Placeholder" oninput="updateLivePreview()">
                            </div>
                        </div>

                        <!-- Question 4: Radio Status Options -->
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-bottom: 0;">
                            <span style="font-weight: 700; font-size: 0.84rem; color: #0f172a; display: block; margin-bottom: 8px;">Question 4 (Multiple Choice Status Options)</span>
                            <input type="text" name="q4_label" id="inputQ4Label" class="form-control" value="<?= esc($form['q4_label'] ?? '4. Current Employment Status') ?>" placeholder="Label" style="margin-bottom: 8px;" oninput="updateLivePreview()">
                            <label style="font-size: 0.78rem; color: #64748b; margin-bottom: 4px; display: block;">Radio Choices (1 per line):</label>
                            <textarea name="q4_options" id="inputQ4Options" class="form-control" rows="3" placeholder="Full-Time Active Employee&#10;Contract / Consulting Associate&#10;Probationary / Trainee" oninput="updateLivePreview()"><?= esc($form['q4_options'] ?? "Full-Time Active Employee\nContract / Consulting Associate\nProbationary / Trainee") ?></textarea>
                        </div>
                    </div>

                    <!-- SECTION 4: Submit Button & Disclaimer -->
                    <div class="editor-section">
                        <div class="editor-section-title">
                            <ion-icon name="checkmark-done-circle-outline" style="color: var(--primary-color); font-size: 1.25rem;"></ion-icon>
                            4. Submit Button & Disclaimer
                        </div>
                        <div class="form-group" style="margin-bottom: 14px;">
                            <label style="font-size: 0.84rem; font-weight: 600; color: #334155;">Submit Button Text</label>
                            <input type="text" name="submit_btn_text" id="inputSubmitBtn" class="form-control" value="<?= esc($form['submit_btn_text'] ?? 'Submit') ?>" placeholder="Submit" oninput="updateLivePreview()">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="font-size: 0.84rem; font-weight: 600; color: #334155;">Footer Legal / Privacy Disclaimer</label>
                            <textarea name="footer_text" id="inputFooterText" class="form-control" rows="2" oninput="updateLivePreview()"><?= esc($form['footer_text'] ?? 'Never give out your password. This content is created by the owner of the form. The data you submit will be sent to the form owner.') ?></textarea>
                        </div>
                    </div>

                    <!-- Save Action -->
                    <div style="margin-bottom: 40px;">
                        <button type="submit" class="btn-primary" style="padding: 13px 28px; font-size: 0.95rem; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(67, 97, 238, 0.4);">
                            <ion-icon name="save-outline"></ion-icon> Save Form Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- RIGHT COLUMN: Interactive Live Victim Preview -->
            <div class="preview-sticky-wrap">
                <div class="preview-header-bar">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-weight: 700; font-size: 0.86rem; color: #1e293b;">Live Employee Screen Mockup</span>
                        <span style="background: #e0f2fe; color: #0284c7; padding: 2px 7px; border-radius: 4px; font-size: 11px; font-weight: 700;">Realtime</span>
                    </div>
                    <small style="color: #64748b;">Updates as you type</small>
                </div>

                <div class="preview-container">
                    <!-- Fake MS Forms Top Bar -->
                    <div class="preview-ms-nav" id="prevMsNav" style="background: <?= esc($form['theme_color'] ?? '#008272') ?>;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="display: grid; grid-template-columns: repeat(3, 3px); grid-gap: 2px;">
                                <span style="width:3px;height:3px;background:white;"></span><span style="width:3px;height:3px;background:white;"></span><span style="width:3px;height:3px;background:white;"></span>
                                <span style="width:3px;height:3px;background:white;"></span><span style="width:3px;height:3px;background:white;"></span><span style="width:3px;height:3px;background:white;"></span>
                                <span style="width:3px;height:3px;background:white;"></span><span style="width:3px;height:3px;background:white;"></span><span style="width:3px;height:3px;background:white;"></span>
                            </span>
                            <span id="prevBrandName"><?= esc($form['brand_name'] ?? 'Microsoft Forms') ?></span>
                            <span class="preview-ms-badge" id="prevBadgeText"><?= esc($form['badge_text'] ?? 'Internal Verification') ?></span>
                        </div>
                        <span style="font-size: 11px; opacity: 0.85;">Office 365</span>
                    </div>

                    <!-- Hero Card -->
                    <div class="preview-ms-card">
                        <div class="preview-ms-hero" id="prevMsHero" style="background: <?= esc($form['theme_color'] ?? '#008272') ?>;">
                            <h2 id="prevHeroTitle"><?= esc($form['hero_title'] ?? 'Diwali Special Employee Bonus & Goodies Claim') ?></h2>
                            <p id="prevHeroDesc"><?= esc($form['hero_description'] ?? 'Please complete this short employee participation activity to verify your eligibility and confirm your exclusive festive bonus distribution.') ?></p>
                        </div>
                        <div class="preview-ms-body">
                            <!-- Notice -->
                            <div class="preview-ms-notice">
                                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" style="flex-shrink:0;">
                                    <circle cx="10" cy="10" r="9" stroke="#0078d4" stroke-width="2"/>
                                    <circle cx="10" cy="6" r="1.2" fill="#0078d4"/>
                                    <rect x="9" y="9" width="2" height="6" rx="1" fill="#0078d4"/>
                                </svg>
                                <span id="prevNoticeText">Hi <strong>Colleague</strong>, when you submit this form, the owner will see your name and email address.</span>
                            </div>

                            <!-- Q1 -->
                            <div class="preview-ms-q">
                                <label class="preview-ms-q-title" id="prevQ1Label"><?= esc($form['q1_label'] ?? '1. Full Name') ?> *</label>
                                <input type="text" class="preview-ms-input" id="prevQ1Input" placeholder="<?= esc($form['q1_placeholder'] ?? 'Enter your full name') ?>" disabled>
                            </div>

                            <!-- Q2 -->
                            <div class="preview-ms-q">
                                <label class="preview-ms-q-title" id="prevQ2Label"><?= esc($form['q2_label'] ?? '2. Work Email Address') ?> *</label>
                                <input type="text" class="preview-ms-input" id="prevQ2Input" placeholder="<?= esc($form['q2_placeholder'] ?? 'name@company.com') ?>" disabled>
                            </div>

                            <!-- Q3 -->
                            <div class="preview-ms-q">
                                <label class="preview-ms-q-title" id="prevQ3Label"><?= esc($form['q3_label'] ?? '3. Employee ID Number') ?> *</label>
                                <input type="text" class="preview-ms-input" id="prevQ3Input" placeholder="<?= esc($form['q3_placeholder'] ?? 'e.g. EMP-20248') ?>" disabled>
                            </div>

                            <!-- Q4 -->
                            <div class="preview-ms-q">
                                <label class="preview-ms-q-title" id="prevQ4Label"><?= esc($form['q4_label'] ?? '4. Current Employment Status') ?> *</label>
                                <div id="prevQ4OptionsContainer" style="display: flex; flex-direction: column; gap: 6px; font-size: 0.82rem; color: #334155; margin-top: 5px;">
                                    <?php
                                    $prevOpts = array_filter(array_map('trim', explode("\n", $form['q4_options'] ?? "Full-Time Active Employee\nContract / Consulting Associate\nProbationary / Trainee")));
                                    foreach ($prevOpts as $idx => $opt):
                                    ?>
                                        <label style="display: flex; align-items: center; gap: 6px;">
                                            <input type="radio" disabled <?= ($idx === 0) ? 'checked' : '' ?>>
                                            <span><?= esc($opt) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Submit Btn Mock -->
                            <div style="margin-top: 18px;">
                                <button type="button" class="preview-ms-btn" id="prevSubmitBtn" style="background: <?= esc($form['theme_color'] ?? '#008272') ?>;">
                                    <?= esc($form['submit_btn_text'] ?? 'Submit') ?>
                                </button>
                            </div>

                            <!-- Footer Disclaimer Mock -->
                            <div style="margin-top: 20px; font-size: 10px; color: #64748b; line-height: 1.4; border-top: 1px solid #f1f5f9; padding-top: 10px;">
                                <span id="prevFooterText"><?= esc($form['footer_text'] ?? 'Never give out your password. This content is created by the owner of the form.') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Modern Dialogs Script for sleek confirms -->
    <script src="<?= base_url('assets/dialogs.js') ?>"></script>

    <script>
        function updateThemeColor(color) {
            document.getElementById('inputThemeColor').value = color;
            document.getElementById('themeColorHex').value = color;
            document.getElementById('prevMsNav').style.background = color;
            document.getElementById('prevMsHero').style.background = color;
            document.getElementById('prevSubmitBtn').style.background = color;
        }

        function setThemePreset(color) {
            updateThemeColor(color);
        }

        function updateLivePreview() {
            // Brand & Badge
            const brand = document.getElementById('inputBrandName').value.trim() || 'Microsoft Forms';
            const badge = document.getElementById('inputBadgeText').value.trim() || 'Internal Verification';
            document.getElementById('prevBrandName').textContent = brand;
            document.getElementById('prevBadgeText').textContent = badge;

            // Hero
            const title = document.getElementById('inputHeroTitle').value.trim() || 'Diwali Special Employee Bonus & Goodies Claim';
            const desc = document.getElementById('inputHeroDesc').value.trim() || '';
            document.getElementById('prevHeroTitle').textContent = title;
            document.getElementById('prevHeroDesc').textContent = desc;

            // Notice
            const notice = document.getElementById('inputNoticeText').value.trim() || 'Hi {name}, when you submit this form, the owner will see your name and email address.';
            document.getElementById('prevNoticeText').innerHTML = notice.replace('{name}', '<strong>Colleague</strong>');

            // Q1, Q2, Q3
            document.getElementById('prevQ1Label').textContent = (document.getElementById('inputQ1Label').value.trim() || '1. Full Name') + ' *';
            document.getElementById('prevQ1Input').placeholder = document.getElementById('inputQ1Placeholder').value.trim() || 'Enter your full name';

            document.getElementById('prevQ2Label').textContent = (document.getElementById('inputQ2Label').value.trim() || '2. Work Email Address') + ' *';
            document.getElementById('prevQ2Input').placeholder = document.getElementById('inputQ2Placeholder').value.trim() || 'name@company.com';

            document.getElementById('prevQ3Label').textContent = (document.getElementById('inputQ3Label').value.trim() || '3. Employee ID Number') + ' *';
            document.getElementById('prevQ3Input').placeholder = document.getElementById('inputQ3Placeholder').value.trim() || 'e.g. EMP-20248';

            // Q4
            document.getElementById('prevQ4Label').textContent = (document.getElementById('inputQ4Label').value.trim() || '4. Current Employment Status') + ' *';
            const rawOpts = document.getElementById('inputQ4Options').value.split('\n');
            const container = document.getElementById('prevQ4OptionsContainer');
            container.innerHTML = '';
            rawOpts.forEach((opt, idx) => {
                const trimmed = opt.trim();
                if (trimmed) {
                    const row = document.createElement('label');
                    row.style.display = 'flex';
                    row.style.alignItems = 'center';
                    row.style.gap = '6px';
                    row.innerHTML = `<input type="radio" disabled ${idx === 0 ? 'checked' : ''}> <span>${trimmed}</span>`;
                    container.appendChild(row);
                }
            });

            // Submit btn & footer
            document.getElementById('prevSubmitBtn').textContent = document.getElementById('inputSubmitBtn').value.trim() || 'Submit';
            document.getElementById('prevFooterText').textContent = document.getElementById('inputFooterText').value.trim() || '';
        }
    </script>
</body>
</html>

