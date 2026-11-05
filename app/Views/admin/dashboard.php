<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PhishGuard - Campaign Dashboard</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('icon.svg') ?>">
    <link rel="alternate icon" href="<?= base_url('favicon.ico') ?>">
    <!-- Custom CSS -->
    <link href="<?= base_url('assets/admin.css') ?>" rel="stylesheet">
    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <!-- Chart.js for Gophish-style campaign visual charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <!-- Custom Modern Dialogs -->
    <script src="<?= base_url('assets/dialogs.js') ?>"></script>
</head>
<body>

    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="brand">
            <ion-icon name="shield-checkmark-outline"></ion-icon>
            <span>PhishGuard</span>
        </div>
        <ul class="nav-links">
            <li><a href="<?= base_url('admin') ?>" class="active"><ion-icon name="grid-outline" style="margin-right:10px"></ion-icon> <span>Campaign Dashboard</span></a></li>
            <li><a href="<?= base_url('admin/campaigns') ?>"><ion-icon name="albums-outline" style="margin-right:10px"></ion-icon> <span>Campaigns</span></a></li>
            <li><a href="<?= base_url('admin/users') ?>"><ion-icon name="people-outline" style="margin-right:10px"></ion-icon> <span>Simulation Users</span></a></li>
            <li><a href="<?= base_url('admin/templates') ?>"><ion-icon name="mail-outline" style="margin-right:10px"></ion-icon> <span>Email Templates</span></a></li>
            <li><a href="<?= base_url('admin/form') ?>"><ion-icon name="newspaper-outline" style="margin-right:10px"></ion-icon> <span>Landing Form</span></a></li>
            <li><a href="<?= base_url('admin/profile') ?>"><ion-icon name="shield-outline" style="margin-right:10px"></ion-icon> <span>Admin Users</span></a></li>
            <li><a href="<?= base_url('admin/settings') ?>"><ion-icon name="settings-outline" style="margin-right:10px"></ion-icon> <span>Settings</span></a></li>
            <li><a href="<?= base_url('admin/export?campaign_id=' . ($campaign['id'] ?? '')) ?>"><ion-icon name="download-outline" style="margin-right:10px"></ion-icon> <span>Export Data</span></a></li>
        </ul>
        <div style="margin-top: auto; padding: 20px;">
           <a href="<?= base_url('logout') ?>" style="color: #ef4444; text-decoration: none; display: flex; align-items: center;"><ion-icon name="log-out-outline" style="margin-right:10px"></ion-icon> Logout</a>
        </div>
    </div>

    <!-- Main Content Area -->
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
                <span><strong>Notice:</strong> <?= session()->getFlashdata('error') ?></span>
                <span style="cursor:pointer;" onclick="this.parentElement.style.display='none';">&times;</span>
            </div>
        <?php endif; ?>

        <!-- Campaign Switcher Bar -->
        <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 8px; padding: 10px 18px; margin-bottom: 1.25rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <span style="font-size: 0.85rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 6px;">
                    <ion-icon name="albums-outline" style="color: var(--primary-color); font-size: 1.1rem;"></ion-icon> Active Campaign:
                </span>
                <select id="campaignSwitcher" class="form-control" style="width: auto; min-width: 280px; font-weight: 600; font-size: 0.9rem; padding: 6px 12px;" onchange="window.location.href='<?= base_url('admin?campaign_id=') ?>' + this.value">
                    <?php if (!empty($campaigns)): ?>
                        <?php foreach ($campaigns as $c): ?>
                            <option value="<?= esc($c['id']) ?>" <?= ($c['id'] == $campaign['id']) ? 'selected' : '' ?>>
                                <?= esc($c['name']) ?> (<?= esc($c['status']) ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="<?= esc($campaign['id']) ?>"><?= esc($campaign['name']) ?></option>
                    <?php endif; ?>
                </select>
                <a href="<?= base_url('admin/campaigns') ?>" class="btn-secondary" style="font-size: 0.8rem; padding: 6px 12px; text-decoration: none;">
                    <ion-icon name="list-outline"></ion-icon> All Campaigns (<?= count($campaigns) ?>)
                </a>
            </div>
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <!-- Manual Refresh Button -->
                <button type="button" class="btn-refresh" id="btnManualRefresh" onclick="triggerManualRefresh()" title="Refresh Dashboard Now">
                    <ion-icon name="sync-outline" id="refreshSpinIcon"></ion-icon>
                    <span id="refreshBtnLabel">Refresh</span>
                </button>

                <!-- Auto-Refresh Pill with Live Indicator & Interval Selector -->
                <div class="auto-refresh-pill active" id="autoRefreshPill" title="Live Auto-Refresh Dashboard">
                    <label class="auto-refresh-label">
                        <input type="checkbox" id="autoRefreshCheckbox" onchange="toggleAutoRefresh(this.checked)" checked style="cursor: pointer;">
                        <span class="live-dot" id="liveDot"></span>
                        <span>Auto-Refresh:</span>
                    </label>
                    <select id="autoRefreshIntervalSelect" onchange="changeAutoRefreshInterval(this.value)" class="auto-refresh-select" title="Refresh Interval">
                        <option value="5">5s</option>
                        <option value="10" selected>10s</option>
                        <option value="15">15s</option>
                        <option value="30">30s</option>
                        <option value="60">60s</option>
                    </select>
                    <span id="autoRefreshTimerPill" class="timer-pill" title="Next update countdown">10s</span>
                </div>

                <!-- Last Refreshed Time -->
                <span id="lastRefreshTimestamp" style="color: #94a3b8; font-size: 0.78rem; white-space: nowrap;">
                    Updated: <strong id="lastRefreshTimeVal" style="color: #475569;"><?= date('h:i:s A') ?></strong>
                </span>

                <a href="<?= base_url('admin/users') ?>" style="font-size: 0.82rem; color: var(--primary-color); text-decoration: none; font-weight: 500; border-left: 1px solid #e2e8f0; padding-left: 10px;">
                    <ion-icon name="people-outline" style="vertical-align: middle;"></ion-icon> Directory: <strong><?= (int)$masterTargetCount ?></strong>
                </a>
            </div>
        </div>

        <!-- Campaign Hero (Gophish Style) -->
        <div class="campaign-hero">
            <div class="campaign-title-group">
                <h2>
                    <?= esc($campaign['name'] ?? 'Employee Phishing Awareness Assessment') ?>
                    <?php if (($campaign['status'] ?? '') === 'Completed'): ?>
                        <span class="status-pill" style="background: rgba(255,255,255,0.25); color: #fff;">Completed</span>
                    <?php else: ?>
                        <span class="status-pill active"><?= esc($campaign['status'] ?? 'In progress') ?></span>
                    <?php endif; ?>
                </h2>
                <div class="campaign-meta" id="campaignMetaContainer">
                    <span><ion-icon name="calendar-outline" style="vertical-align:middle;"></ion-icon> Started: <?= !empty($campaign['created_at']) ? date('M d, Y', strtotime($campaign['created_at'])) : date('M d, Y') ?></span>
                    <span><ion-icon name="mail-outline" style="vertical-align:middle;"></ion-icon> Template: <strong><?= esc($campaign['template_name'] ?? 'Standard Security Notice') ?></strong></span>
                    <span><ion-icon name="people-outline" style="vertical-align:middle;"></ion-icon> Targets: <strong><?= count($targets) ?> enrolled</strong></span>
                </div>
            </div>
            <div class="campaign-actions">
                <button type="button" class="btn-refresh" onclick="triggerManualRefresh()" style="border: 1px solid #cbd5e1;" title="Refresh Campaign Data">
                    <ion-icon name="sync-outline"></ion-icon> Refresh
                </button>
                <button type="button" class="btn-success" onclick="openModal('launchModal')">
                    <ion-icon name="paper-plane-outline"></ion-icon> Launch Simulation
                </button>
                <?php if (($campaign['status'] ?? '') !== 'Completed'): ?>
                    <a href="<?= base_url('admin/campaigns/complete/' . $campaign['id']) ?>" class="btn-secondary" onclick="return confirmAction(event, 'Mark this campaign as completed?', { type: 'info', title: 'Complete Campaign?', confirmBtn: 'Mark Completed' });">
                        <ion-icon name="checkmark-done-outline"></ion-icon> Complete
                    </a>
                <?php endif; ?>
                <button type="button" class="btn-secondary" onclick="openModal('importModal')">
                    <ion-icon name="person-add-outline"></ion-icon> Import Users (CSV)
                </button>
                <a href="<?= base_url('admin/export?campaign_id=' . $campaign['id']) ?>" class="btn-secondary">
                    <ion-icon name="cloud-download-outline"></ion-icon> Export Report
                </a>
                <a href="<?= base_url('admin/clear_logs?campaign_id=' . $campaign['id']) ?>" class="btn-secondary" style="color: #ef4444;" onclick="return confirmAction(event, 'Reset results and tracking logs for this campaign? Enrolled targets will remain.', { type: 'warning', title: 'Reset Campaign Results?', confirmBtn: 'Yes, Reset Results' });">
                    <ion-icon name="refresh-outline"></ion-icon> Reset Results
                </a>
            </div>
        </div>

        <!-- Gophish Funnel Progression Bar (5 Signature Stages including Reported) -->
        <div class="funnel-grid-5" id="funnelGridContainer"
             data-sent="<?= (int)$stats['sent_count'] ?>"
             data-opened="<?= (int)$stats['opened_count'] ?>"
             data-clicked="<?= (int)$stats['clicked_count'] ?>"
             data-submitted="<?= (int)$stats['submitted_count'] ?>"
             data-reported="<?= (int)$stats['reported_count'] ?>">
            <!-- Stage 1: Email Sent -->
            <div class="funnel-card stage-sent">
                <div class="funnel-header">
                    <span class="funnel-title">Email Sent</span>
                    <ion-icon name="mail-outline" class="funnel-icon"></ion-icon>
                </div>
                <div class="funnel-numbers">
                    <span class="funnel-count"><?= esc($stats['sent_count']) ?></span>
                    <span class="funnel-pct"><?= esc($stats['sent_pct']) ?>%</span>
                </div>
                <div class="funnel-progress">
                    <div class="funnel-bar" style="width: <?= esc($stats['sent_pct']) ?>%;"></div>
                </div>
            </div>

            <!-- Stage 2: Email Opened -->
            <div class="funnel-card stage-opened">
                <div class="funnel-header">
                    <span class="funnel-title">Email Opened</span>
                    <ion-icon name="mail-open-outline" class="funnel-icon"></ion-icon>
                </div>
                <div class="funnel-numbers">
                    <span class="funnel-count"><?= esc($stats['opened_count']) ?></span>
                    <span class="funnel-pct"><?= esc($stats['opened_pct']) ?>%</span>
                </div>
                <div class="funnel-progress">
                    <div class="funnel-bar" style="width: <?= esc($stats['opened_pct']) ?>%;"></div>
                </div>
            </div>

            <!-- Stage 3: Clicked Link -->
            <div class="funnel-card stage-clicked">
                <div class="funnel-header">
                    <span class="funnel-title">Clicked Link</span>
                    <ion-icon name="link-outline" class="funnel-icon"></ion-icon>
                </div>
                <div class="funnel-numbers">
                    <span class="funnel-count"><?= esc($stats['clicked_count']) ?></span>
                    <span class="funnel-pct"><?= esc($stats['clicked_pct']) ?>%</span>
                </div>
                <div class="funnel-progress">
                    <div class="funnel-bar" style="width: <?= esc($stats['clicked_pct']) ?>%;"></div>
                </div>
            </div>

            <!-- Stage 4: Submitted Credentials / Data -->
            <div class="funnel-card stage-submitted">
                <div class="funnel-header">
                    <span class="funnel-title">Submitted Data</span>
                    <ion-icon name="warning-outline" class="funnel-icon"></ion-icon>
                </div>
                <div class="funnel-numbers">
                    <span class="funnel-count"><?= esc($stats['submitted_count']) ?></span>
                    <span class="funnel-pct"><?= esc($stats['submitted_pct']) ?>%</span>
                </div>
                <div class="funnel-progress">
                    <div class="funnel-bar" style="width: <?= esc($stats['submitted_pct']) ?>%;"></div>
                </div>
            </div>

            <!-- Stage 5: Reported Phish -->
            <div class="funnel-card stage-reported">
                <div class="funnel-header">
                    <span class="funnel-title">Reported Phish</span>
                    <ion-icon name="shield-checkmark-outline" class="funnel-icon"></ion-icon>
                </div>
                <div class="funnel-numbers">
                    <span class="funnel-count"><?= esc($stats['reported_count']) ?></span>
                    <span class="funnel-pct"><?= esc($stats['reported_pct']) ?>%</span>
                </div>
                <div class="funnel-progress">
                    <div class="funnel-bar" style="width: <?= esc($stats['reported_pct']) ?>%;"></div>
                </div>
            </div>
        </div>

        <!-- Campaign Visual Chart & Overview -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
            <!-- Gophish Breakdown Chart -->
            <div class="table-container">
                <div class="table-header" style="display:flex; justify-content:space-between; align-items:center;">
                    <h3>Campaign Engagement Breakdown</h3>
                    <small style="color: var(--text-muted);">Real-time engagement breakdown</small>
                </div>
                <div style="height: 220px; position: relative;">
                    <canvas id="campaignChart"></canvas>
                </div>
            </div>

            <!-- Campaign Quick Details & Test Link -->
            <div class="table-container">
                <div class="table-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <h3>Simulation Drill Configuration & Quick Tester</h3>
                    <button type="button" class="btn-secondary" onclick="openModal('editDrillSettingsModal')" style="font-size: 0.82rem; padding: 6px 14px; font-weight: 600;">
                        <ion-icon name="settings-outline"></ion-icon> Configure Drill Behavior
                    </button>
                </div>
                <div style="font-size: 0.9rem; line-height: 1.8; color: #475569;">
                    <!-- Active Settings Status Pills -->
                    <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 14px;">
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 6px 12px; font-size: 0.82rem; display: flex; align-items: center; gap: 6px;">
                            <strong>Debrief Screen:</strong>
                            <?php if (!isset($campaign['show_debrief_screen']) || !empty($campaign['show_debrief_screen'])): ?>
                                <span style="background: #dcfce7; color: #15803d; padding: 2px 8px; border-radius: 6px; font-weight: 700;">🟢 Enabled (Shows Test Tips)</span>
                            <?php else: ?>
                                <span style="background: #fee2e2; color: #dc2626; padding: 2px 8px; border-radius: 6px; font-weight: 700;">🔴 Disabled (Workstation Remains Locked)</span>
                            <?php endif; ?>
                        </div>
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 6px 12px; font-size: 0.82rem; display: flex; align-items: center; gap: 6px;">
                            <strong>Entry Mode:</strong>
                            <?php if (!empty($campaign['has_credential_form'])): ?>
                                <span style="background: #e0f2fe; color: #0284c7; padding: 2px 8px; border-radius: 6px; font-weight: 700;">📝 Microsoft Forms First</span>
                            <?php else: ?>
                                <span style="background: #fef3c7; color: #b45309; padding: 2px 8px; border-radius: 6px; font-weight: 700;">⚡ Instant Red Alert Screen</span>
                            <?php endif; ?>
                        </div>
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 6px 12px; font-size: 0.82rem; display: flex; align-items: center; gap: 6px;">
                            <strong>Red Screen Countdown:</strong>
                            <span style="font-weight: 700; color: #0f172a;"><?= (int)($campaign['red_screen_duration'] ?? 5) ?>s</span>
                        </div>
                    </div>

                    <p><strong>Educational Objective:</strong> Train employees to recognize urgent spoofed emails, inspect suspicious links, and report phishing attacks to IT Security.</p>
                    <p><strong>Reporting vs Clicking:</strong> Employees who report the simulation are recorded as <em>Security Champions</em>. Victims who click are routed to interactive awareness training.</p>
                    
                    <div style="margin-top: 15px; padding: 12px; background: #f8fafc; border: 1px solid var(--border-color); border-radius: 8px;">
                        <span style="font-weight: 600; font-size: 0.85rem; color: #334155;">Quick Link & Pixel Tester:</span>
                        <div style="display: flex; gap: 8px; margin-top: 8px;">
                            <input type="email" id="quickTestEmail" placeholder="test.user@company.com" class="form-control" style="font-size: 0.85rem;" oninput="generateQuickLink()">
                            <button type="button" class="btn-secondary" onclick="copyQuickLink()" title="Copy simulation link">
                                <ion-icon name="copy-outline"></ion-icon> Link
                            </button>
                            <button type="button" class="btn-secondary" onclick="testTrackingPixel()" title="Test tracking pixel in new tab" style="color: #0284c7;">
                                <ion-icon name="image-outline"></ion-icon> Pixel
                            </button>
                        </div>
                        <code id="quickLinkOutput" style="display: block; font-size: 0.8rem; margin-top: 6px; color: #64748b; word-break: break-all;">
                            Enter an email above to generate simulation URL
                        </code>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gophish Campaign Results Table -->
        <div class="table-container" id="targetsSectionContainer">
            <div class="table-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <div>
                    <h3>Campaign Results & Engagement Progression</h3>
                    <small style="color: var(--text-muted);"><?= count($targets) ?> Targets enrolled in this simulation</small>
                </div>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <button type="button" class="btn-secondary" onclick="openModal('importModal')">
                        <ion-icon name="add-outline"></ion-icon> Add / Import Targets
                    </button>
                    <?php if (!empty($targets)): ?>
                        <a href="<?= base_url('admin/clear_targets?campaign_id=' . $campaign['id']) ?>" class="btn-secondary" style="color: #ef4444;" onclick="return confirmAction(event, 'Clear ALL enrolled targets from this campaign? All progression data for these targets will be deleted.', { type: 'danger', title: 'Clear Enrolled Targets?', confirmBtn: 'Clear All Targets' });">
                            <ion-icon name="trash-outline"></ion-icon> Clear All
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Toolbar & Filter Tabs -->
            <div class="table-toolbar">
                <div class="filter-tabs">
                    <button class="filter-tab active" data-filter="all" onclick="filterTable('all', this)">All (<?= count($targets) ?>)</button>
                    <button class="filter-tab" data-filter="sent" onclick="filterTable('sent', this)">Sent (<?= $stats['sent_count'] ?>)</button>
                    <button class="filter-tab" data-filter="opened" onclick="filterTable('opened', this)">Opened (<?= $stats['opened_count'] ?>)</button>
                    <button class="filter-tab" data-filter="clicked" onclick="filterTable('clicked', this)">Clicked (<?= $stats['clicked_count'] ?>)</button>
                    <button class="filter-tab" data-filter="submitted" onclick="filterTable('submitted', this)">Submitted (<?= $stats['submitted_count'] ?>)</button>
                    <button class="filter-tab" data-filter="reported" onclick="filterTable('reported', this)" style="color: #059669; font-weight: 700;">🛡️ Reported (<?= $stats['reported_count'] ?>)</button>
                </div>

                <div style="display: flex; gap: 10px; align-items: center;">
                    <button type="button" class="btn-danger-outline" id="bulkDeleteTargetsBtn" style="display: none; padding: 6px 12px; font-size: 0.8rem;" onclick="confirmBulkDeleteTargets()">
                        <ion-icon name="trash-outline"></ion-icon> Remove Selected (<span id="selectedTargetsCount">0</span>)
                    </button>
                    <div class="search-box">
                        <ion-icon name="search-outline" style="color: var(--text-muted);"></ion-icon>
                        <input type="text" id="targetSearch" placeholder="Search targets..." onkeyup="searchTargets()">
                    </div>
                </div>
            </div>

            <!-- Table of Targets -->
            <div style="overflow-x: auto;">
                <table id="targetsTable">
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">
                                <input type="checkbox" id="selectAllTargets" onchange="toggleSelectAllTargets(this)" title="Select All Targets" style="cursor: pointer; width: 16px; height: 16px;">
                            </th>
                            <th>Target Employee</th>
                            <th>Position / Dept</th>
                            <th>Funnel Progression</th>
                            <th>Current Status</th>
                            <th>Last Activity</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($targets)): ?>
                            <tr id="noTargetsRow">
                                <td colspan="7" style="text-align: center; padding: 3rem; color: #94a3b8;">
                                    <ion-icon name="people-outline" style="font-size: 2.5rem; margin-bottom: 10px; display: block;"></ion-icon>
                                    No targets enrolled in this campaign yet.<br>
                                    <button type="button" class="btn-primary" style="margin-top: 15px;" onclick="openModal('importModal')">
                                        <ion-icon name="person-add-outline" style="margin-right: 5px;"></ion-icon> Bulk Import Targets (CSV)
                                    </button>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($targets as $t): ?>
                                <tr class="target-row" 
                                    data-sent="<?= (!empty($t['sent']) || !empty($t['opened']) || !empty($t['clicked']) || !empty($t['submitted']) || !empty($t['reported'])) ? '1' : '0' ?>"
                                    data-opened="<?= (!empty($t['opened']) || !empty($t['clicked']) || !empty($t['submitted'])) ? '1' : '0' ?>"
                                    data-clicked="<?= (!empty($t['clicked']) || !empty($t['submitted'])) ? '1' : '0' ?>"
                                    data-submitted="<?= !empty($t['submitted']) ? '1' : '0' ?>"
                                    data-reported="<?= !empty($t['reported']) ? '1' : '0' ?>">
                                    <td style="text-align: center;">
                                        <input type="checkbox" value="<?= $t['id'] ?>" class="target-select-check" onchange="updateSelectedTargetsCount()" style="cursor: pointer; width: 16px; height: 16px;">
                                    </td>
                                    <td>
                                        <?php
                                            $firstName = $t['first_name'] ?? '';
                                            $lastName  = $t['last_name'] ?? '';
                                            $fullName  = trim($firstName . ' ' . $lastName);
                                            if (empty($fullName)) {
                                                $fullName = !empty($t['name']) ? $t['name'] : 'Target';
                                            }
                                            $position = !empty($t['position']) ? $t['position'] : (!empty($t['department']) ? $t['department'] : 'Staff');
                                        ?>
                                        <strong><?= esc($fullName) ?></strong>
                                        <div style="font-size: 0.82rem;">
                                            <a href="mailto:<?= esc($t['email'] ?? '') ?>" style="color: var(--primary-color); text-decoration: none;">
                                                <?= esc($t['email'] ?? '') ?>
                                            </a>
                                        </div>
                                    </td>
                                    <td><?= esc($position) ?></td>
                                    <td>
                                        <!-- Visual Funnel Progression: Shows Sent -> Opened -> Clicked -> Submitted -> Reported -->
                                        <div style="display: flex; gap: 4px; align-items: center; flex-wrap: wrap;">
                                            <span class="status-pill-mini <?= (!empty($t['sent']) || !empty($t['opened']) || !empty($t['clicked']) || !empty($t['submitted']) || !empty($t['reported'])) ? 'pill-active-sent' : 'pill-inactive' ?>" title="Email Sent">
                                                <ion-icon name="paper-plane-outline"></ion-icon> Sent
                                            </span>
                                            <span style="color: #cbd5e1; font-size: 0.75rem;">&rsaquo;</span>
                                            <span class="status-pill-mini <?= (!empty($t['opened']) || !empty($t['clicked']) || !empty($t['submitted'])) ? 'pill-active-opened' : 'pill-inactive' ?>" title="Email Opened">
                                                <ion-icon name="mail-open-outline"></ion-icon> Opened
                                            </span>
                                            <span style="color: #cbd5e1; font-size: 0.75rem;">&rsaquo;</span>
                                            <span class="status-pill-mini <?= (!empty($t['clicked']) || !empty($t['submitted'])) ? 'pill-active-clicked' : 'pill-inactive' ?>" title="Clicked Link">
                                                <ion-icon name="link-outline"></ion-icon> Clicked
                                            </span>
                                            <span style="color: #cbd5e1; font-size: 0.75rem;">&rsaquo;</span>
                                            <span class="status-pill-mini <?= !empty($t['submitted']) ? 'pill-active-submitted' : 'pill-inactive' ?>" title="Submitted Data">
                                                <ion-icon name="warning-outline"></ion-icon> Submitted
                                            </span>
                                            <?php if (!empty($t['submitted'])): ?>
                                                <?php
                                                    $subPayload = !empty($t['submitted_data_payload']) ? json_decode($t['submitted_data_payload'], true) : [];
                                                    if (empty($subPayload)) {
                                                        $subPayload = [
                                                            'full_name' => !empty($t['first_name']) ? ($t['first_name'] . ' ' . ($t['last_name'] ?? '')) : (!empty($t['name']) ? $t['name'] : 'Target'),
                                                            'email' => $t['email'],
                                                            'employee_id' => 'EMP-SUBMITTED',
                                                            'employed' => $t['position'] ?? 'Staff',
                                                            'submitted_at' => $t['last_activity'] ?? ''
                                                        ];
                                                    }
                                                ?>
                                                <button type="button" class="btn-data-pill" 
                                                        onclick="viewCapturedData('<?= esc($t['email']) ?>', <?= htmlspecialchars(json_encode($subPayload), ENT_QUOTES) ?>)"
                                                        title="Click to view data entered by target"
                                                        style="background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; border-radius: 12px; padding: 2px 8px; font-size: 0.72rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                                    <ion-icon name="eye-outline"></ion-icon> View Data
                                                </button>
                                            <?php endif; ?>
                                            <?php if (!empty($t['reported'])): ?>
                                                <span style="color: #cbd5e1; font-size: 0.75rem;">&rsaquo;</span>
                                                <span class="status-pill-mini pill-active-reported" title="Reported: <?= esc($t['reported_at'] ?? 'Yes') ?>">
                                                    <ion-icon name="shield-checkmark"></ion-icon> Reported
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge <?= esc($t['badge_class']) ?>">
                                            <?php if (!empty($t['reported'])): ?>
                                                <ion-icon name="shield-checkmark-outline"></ion-icon>
                                            <?php elseif (!empty($t['submitted'])): ?>
                                                <ion-icon name="warning-outline"></ion-icon>
                                            <?php elseif (!empty($t['clicked'])): ?>
                                                <ion-icon name="link-outline"></ion-icon>
                                            <?php elseif (!empty($t['opened'])): ?>
                                                <ion-icon name="mail-open-outline"></ion-icon>
                                            <?php elseif (!empty($t['sent'])): ?>
                                                <ion-icon name="paper-plane-outline"></ion-icon>
                                            <?php endif; ?>
                                            <?= esc($t['computed_status']) ?>
                                        </span>
                                    </td>
                                    <td style="font-size: 0.85rem; color: var(--text-muted);">
                                        <?= !empty($t['last_activity']) ? esc($t['last_activity']) : '—' ?>
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        <button type="button" class="btn-secondary" style="padding: 4px 8px; font-size: 0.8rem;" 
                                                onclick="sendSingleModal('<?= esc($t['email']) ?>')" title="Send simulation email to this target">
                                            <ion-icon name="send-outline"></ion-icon>
                                        </button>
                                        <?php if (!empty($t['submitted'])): ?>
                                            <button type="button" class="btn-secondary" style="padding: 4px 8px; font-size: 0.8rem; color: #dc2626;" 
                                                    onclick="viewCapturedData('<?= esc($t['email']) ?>', <?= htmlspecialchars(json_encode($subPayload ?? []), ENT_QUOTES) ?>)" 
                                                    title="View Entered Target Form Data">
                                                <ion-icon name="document-text-outline"></ion-icon>
                                            </button>
                                        <?php endif; ?>
                                        <?php if (empty($t['opened']) && empty($t['clicked'])): ?>
                                            <a href="<?= base_url('admin/mark_opened/' . $t['id'] . '?campaign_id=' . $campaign['id']) ?>" 
                                               class="btn-secondary" style="padding: 4px 8px; font-size: 0.8rem; color: #0284c7;" 
                                               title="Simulate / Mark Email Opened">
                                                <ion-icon name="mail-open-outline"></ion-icon>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (empty($t['reported'])): ?>
                                            <a href="<?= base_url('admin/mark_reported/' . $t['id'] . '?campaign_id=' . $campaign['id']) ?>" 
                                               class="btn-secondary" style="padding: 4px 8px; font-size: 0.8rem; color: #059669;" 
                                               onclick="return confirmAction(event, 'Mark <?= esc($t['email'], 'js') ?> as Reported Phish (Security Champion)?', { type: 'success', title: 'Mark as Reported?', confirmBtn: 'Mark Reported' });" title="Mark as Reported Phish">
                                                <ion-icon name="shield-checkmark-outline"></ion-icon>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= base_url('admin/delete_target/' . $t['id'] . '?campaign_id=' . $campaign['id']) ?>" class="btn-danger-outline" 
                                           onclick="return confirmAction(event, 'Remove <?= esc($t['email'], 'js') ?> from this campaign?', { type: 'danger', title: 'Remove Target?', confirmBtn: 'Remove Target' });" title="Delete target">
                                            <ion-icon name="trash-outline"></ion-icon>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Live Campaign Timeline Feed (Gophish Style) -->
        <div class="table-container" id="recentActivityContainer" style="margin-top: 2rem;">
            <div class="table-header">
                <h3>Campaign Activity Stream</h3>
                <small style="color: var(--text-muted)">Chronological event log of recipient interactions</small>
            </div>

            <?php if (empty($logs)): ?>
                <p style="color: #94a3b8; text-align: center; padding: 2rem;">No simulation events recorded yet.</p>
            <?php else: ?>
                <div style="max-height: 350px; overflow-y: auto;">
                    <?php foreach ($logs as $log): ?>
                        <div class="timeline-item">
                            <?php
                                $dotColor = '#3b82f6';
                                $actionName = 'Email Sent';
                                if ($log['action'] === 'image_viewed') { $dotColor = '#06b6d4'; $actionName = 'Email Opened (Tracking Pixel)'; }
                                elseif ($log['action'] === 'clicked_download') { $dotColor = '#f97316'; $actionName = 'Clicked Phishing Link'; }
                                elseif ($log['action'] === 'submitted_data') { $dotColor = '#ef4444'; $actionName = 'Submitted Phishing Form (Compromised)'; }
                                elseif ($log['action'] === 'email_reported') { $dotColor = '#10b981'; $actionName = 'Reported Phish (Security Champion! 🛡️)'; }
                            ?>
                            <div class="timeline-dot" style="background: <?= $dotColor ?>;"></div>
                            <div style="flex: 1;">
                                <div style="display: flex; justify-content: space-between; font-size: 0.85rem;">
                                    <span>
                                        <strong><?= esc($log['email']) ?></strong> — 
                                        <span style="color: <?= $dotColor ?>; font-weight: 600;"><?= esc($actionName) ?></span>
                                    </span>
                                    <span style="color: var(--text-muted); font-size: 0.8rem;"><?= esc($log['created_at']) ?></span>
                                </div>
                                <?php if (!empty($log['details'])): ?>
                                    <div style="font-size: 0.78rem; background: #fff1f2; border: 1px solid #fecdd3; border-radius: 4px; padding: 4px 8px; margin-top: 4px; color: #9f1239;">
                                        <strong>Entered Data:</strong> <?= esc($log['details']) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($log['ip_address'])): ?>
                                    <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 2px;">
                                        IP: <?= esc($log['ip_address']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <!-- ==========================================================
         MODAL 1: Launch Campaign / Send Phishing Simulation (Redesigned Modern UI)
         ========================================================== -->
    <div id="launchModal" class="modal-overlay">
        <div class="modal-card modal-card-lg">
            <div class="modal-header-modern">
                <div class="modal-title-group">
                    <div class="modal-title-icon">
                        <ion-icon name="paper-plane"></ion-icon>
                    </div>
                    <div class="modal-title-text">
                        <h3>Launch Phishing Simulation</h3>
                        <p>Configure campaign audience, template, and delivery preferences</p>
                    </div>
                </div>
                <button type="button" class="modal-close-round" onclick="closeModal('launchModal')" title="Close">&times;</button>
            </div>

            <form action="<?= base_url('admin/send') ?>" method="post" enctype="multipart/form-data" onsubmit="onSimulationSubmit(event, this)">
                <input type="hidden" name="campaign_id" value="<?= esc($campaign['id']) ?>">
                <div class="modal-body" style="padding: 1.5rem 1.75rem;">
                    
                    <!-- SECTION 1: CAMPAIGN CONFIGURATION -->
                    <div class="modal-section">
                        <div class="modal-section-title">
                            <ion-icon name="options-outline"></ion-icon> 1. Campaign Mode & Identity
                        </div>

                        <!-- Segmented Mode Cards -->
                        <div class="segmented-cards">
                            <label class="segmented-card active" id="segCardNew" onclick="selectCampaignTypeMode('new')">
                                <input type="radio" name="campaign_type" value="new" id="campTypeNew" checked onchange="toggleCampaignType()">
                                <div>
                                    <strong>Create as New Campaign</strong>
                                    <small>Independent analytics funnel & report</small>
                                </div>
                            </label>
                            <label class="segmented-card" id="segCardExisting" onclick="selectCampaignTypeMode('existing')">
                                <input type="radio" name="campaign_type" value="existing" id="campTypeExisting" onchange="toggleCampaignType()">
                                <div>
                                    <strong>Add to Active Campaign</strong>
                                    <small><?= esc($campaign['name']) ?></small>
                                </div>
                            </label>
                        </div>

                        <!-- Campaign Name Input -->
                        <div class="form-group" id="campaignNameGroup" style="margin-bottom: 1rem;">
                            <label style="font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                                Campaign Assessment Name <span style="color:#ef4444;">*</span>
                            </label>
                            <input type="text" name="campaign_name" id="campaignNameInput" required 
                                   value="Diwali Special Employee Bonus Assessment" 
                                   placeholder="e.g. Q4 Executive Phishing Simulation" class="form-control" style="font-size: 0.9rem;">
                        </div>

                        <!-- Email Template Selector -->
                        <div class="form-group" style="margin-bottom: 0;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                <label style="font-size: 0.82rem; font-weight: 700; color: #334155; margin: 0;">
                                    Phishing Email Template
                                </label>
                                <button type="button" class="btn-secondary" style="padding: 2px 8px; font-size: 0.75rem; border-radius: 4px;" onclick="togglePreviewAccordion()">
                                    <ion-icon name="eye-outline"></ion-icon> Toggle Live Preview
                                </button>
                            </div>
                            <select name="template_id" id="templateSelect" class="form-control" onchange="onTemplateChange()" style="font-size: 0.9rem;">
                                <?php foreach ($templates as $tmpl): ?>
                                    <option value="<?= esc($tmpl['id']) ?>" 
                                            data-name="<?= esc($tmpl['name']) ?>"
                                            data-subject="<?= esc($tmpl['subject']) ?>"
                                            data-sender="<?= esc($tmpl['sender_name']) ?>"
                                            <?= (str_contains(strtolower($tmpl['name']), 'diwali')) ? 'selected' : '' ?>>
                                        <?= esc($tmpl['name']) ?> — "<?= esc($tmpl['subject']) ?>"
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <!-- Live Template Preview Box (Collapsible) -->
                            <div id="previewAccordion" class="template-preview-box" style="display: none; border-radius: 8px; background: #f8fafc; border: 1px solid #cbd5e1; margin-top: 10px;">
                                <div id="templatePreviewSubject" style="font-weight: 700; margin-bottom: 8px; color: #1e293b; font-size: 0.9rem;"></div>
                                <div id="templatePreviewBody" style="background: white; padding: 12px; border-radius: 6px; border: 1px solid #e2e8f0;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: TARGET AUDIENCE -->
                    <div class="modal-section">
                        <div class="modal-section-title">
                            <ion-icon name="people-outline"></ion-icon> 2. Target Audience Selection
                        </div>

                        <!-- Segmented Audience Chips -->
                        <div class="audience-chips">
                            <label class="audience-chip active" id="chipBulk" onclick="selectAudienceMode('bulk')">
                                <input type="radio" name="send_mode" value="bulk" id="modeBulk" checked onchange="toggleSendMode()">
                                <ion-icon name="people"></ion-icon> All Users (<?= (int)$masterTargetCount ?>)
                            </label>
                            <label class="audience-chip" id="chipSelected" onclick="selectAudienceMode('selected')">
                                <input type="radio" name="send_mode" value="selected" id="modeSelected" onchange="toggleSendMode()">
                                <ion-icon name="checkmark-done-circle-outline"></ion-icon> Select Specific Users
                            </label>
                            <label class="audience-chip" id="chipSingle" onclick="selectAudienceMode('single')">
                                <input type="radio" name="send_mode" value="single" id="modeSingle" onchange="toggleSendMode()">
                                <ion-icon name="mail-outline"></ion-icon> Single Target Email
                            </label>
                        </div>

                        <!-- Select Specific Users Checklist (Hidden unless selected) -->
                        <div id="selectUsersGroup" style="display: none; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-top: 10px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <span style="font-weight: 700; font-size: 0.85rem; color: #1e293b;">
                                    Select Users (<span id="modalSelectedUsersCount">0</span> selected):
                                </span>
                                <div style="display: flex; gap: 6px;">
                                    <button type="button" class="btn-secondary" style="padding: 2px 8px; font-size: 0.75rem;" onclick="toggleAllModalUsers(true)">Select All</button>
                                    <button type="button" class="btn-secondary" style="padding: 2px 8px; font-size: 0.75rem;" onclick="toggleAllModalUsers(false)">Clear</button>
                                </div>
                            </div>
                            <input type="text" id="modalUserSearchInput" placeholder="Filter users by name or email..." class="form-control" style="font-size: 0.82rem; margin-bottom: 8px;" onkeyup="filterModalUsersList()">
                            <div id="modalUsersListContainer" style="max-height: 180px; overflow-y: auto; border: 1px solid #cbd5e1; border-radius: 6px; background: white; padding: 4px;">
                                <?php if (empty($allDirectoryUsers)): ?>
                                    <div style="text-align: center; color: #94a3b8; padding: 15px; font-size: 0.85rem;">No directory users found. Please add users in the Simulation Users tab.</div>
                                <?php else: ?>
                                    <?php foreach ($allDirectoryUsers as $du): ?>
                                        <label class="modal-user-row" style="display: flex; align-items: center; gap: 8px; padding: 6px 8px; border-bottom: 1px solid #f1f5f9; cursor: pointer; font-size: 0.85rem;">
                                            <input type="checkbox" name="selected_user_ids[]" value="<?= $du['id'] ?>" class="modal-target-check" onchange="updateModalSelectedCount()">
                                            <span>
                                                <strong><?= esc(!empty($du['first_name']) ? ($du['first_name'] . ' ' . ($du['last_name'] ?? '')) : ($du['name'] ?? 'Target')) ?></strong>
                                                <code style="color: #64748b; font-size: 0.8rem; margin-left: 4px;">&lt;<?= esc($du['email']) ?>&gt;</code>
                                                <small style="color: #94a3b8; margin-left: 4px;">(<?= esc($du['position'] ?: ($du['department'] ?: 'Staff')) ?>)</small>
                                            </span>
                                        </label>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Single Target Email Input (Hidden unless single selected) -->
                        <div id="singleEmailGroup" style="display: none; margin-top: 10px;">
                            <label style="font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px; display: block;">Recipient Email Address</label>
                            <input type="email" name="email" id="targetEmailInput" placeholder="employee@company.com" class="form-control" style="font-size: 0.9rem;">
                        </div>
                    </div>

                    <!-- SECTION 3: DELIVERY & SENDING PREFERENCES -->
                    <div class="modal-section" style="margin-bottom: 0;">
                        <div class="modal-section-title">
                            <ion-icon name="shield-outline"></ion-icon> 3. Delivery & Sending Profile
                        </div>

                        <!-- Sender Profile Pill Card -->
                        <div class="profile-pill-card">
                            <div class="profile-pill-left">
                                <div class="profile-avatar-sm">
                                    RY
                                </div>
                                <div class="profile-info-text">
                                    <strong id="previewSenderName"><?= esc($settings['from_name'] ?? 'Ravindranath Yarlagadda') ?></strong>
                                    <small><?= esc($settings['from_email'] ?? 'nobitanobi0272@gmail.com') ?></small>
                                </div>
                            </div>
                            <a href="<?= base_url('admin/settings') ?>" class="btn-profile-edit">
                                <ion-icon name="settings-outline" style="vertical-align: middle;"></ion-icon> Edit Profile
                            </a>
                        </div>

                        <!-- Custom Image Attachment Dropzone -->
                        <div style="margin-top: 12px;">
                            <label style="font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px; display: block;">
                                Custom Attachment / Inline Evidence (Optional)
                            </label>
                            <label class="custom-file-upload">
                                <input type="file" name="custom_image" id="customImageInput" accept="image/png, image/jpeg, image/gif" onchange="onCustomFileSelected(this)">
                                <span class="custom-file-label" id="customFileLabel">📎 Choose file (PNG, JPG, GIF) or leave empty for default</span>
                                <span class="custom-file-btn">Browse</span>
                            </label>
                        </div>

                        <!-- Modern Toggle Switch Card for Report Phishing Link -->
                        <label class="toggle-switch-card">
                            <div class="toggle-switch-text">
                                <strong>Include "Report Phishing" Button in Email Footer</strong>
                                <small>Appends: <em>"Suspicious email? Report this email to IT Security 🛡️"</em>. Disabled by default so emails realistically simulate actual phishing attacks.</small>
                            </div>
                            <div class="switch-control">
                                <input type="checkbox" name="include_report_link" value="1" id="includeReportLink">
                                <span class="switch-slider"></span>
                            </div>
                        </label>
                    </div>

                    <!-- SECTION 4: LANDING PAGE & RED ALERT EXPERIENCE -->
                    <div class="modal-section" style="margin-bottom: 0; margin-top: 1.15rem;">
                        <div class="modal-section-title">
                            <ion-icon name="color-wand-outline"></ion-icon> 4. Landing Page & Red Alert Experience
                        </div>

                        <!-- Toggle 1: Microsoft Forms Credential Input (Optional, default OFF) -->
                        <label class="toggle-switch-card" style="margin-top: 0; margin-bottom: 12px;">
                            <div class="toggle-switch-text">
                                <strong>Require Microsoft Forms Data Submission</strong>
                                <small><strong>Default OFF:</strong> Targets immediately experience the blinking Red Alert screen upon clicking the email link.<br>
                                <em>Turn ON if you want targets to submit personal/employee details into a Microsoft Forms page first.</em></small>
                            </div>
                            <div class="switch-control">
                                <input type="hidden" name="has_credential_form" value="0">
                                <input type="checkbox" name="has_credential_form" value="1" id="hasCredentialFormCheck" <?= !empty($campaign['has_credential_form']) ? 'checked' : '' ?>>
                                <span class="switch-slider"></span>
                            </div>
                        </label>

                        <!-- Red Screen Duration Input & Preset Chips -->
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px; margin-bottom: 12px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <label style="font-size: 0.84rem; font-weight: 700; color: #1e293b; margin: 0;">
                                    ⏱️ Red Screen Alert Duration
                                </label>
                                <div style="display: flex; gap: 6px;">
                                    <button type="button" class="btn-secondary" style="padding: 2px 8px; font-size: 0.75rem;" onclick="setRedScreenDuration(3)">3s</button>
                                    <button type="button" class="btn-secondary" style="padding: 2px 8px; font-size: 0.75rem;" onclick="setRedScreenDuration(5)">5s</button>
                                    <button type="button" class="btn-secondary" style="padding: 2px 8px; font-size: 0.75rem;" onclick="setRedScreenDuration(8)">8s</button>
                                    <button type="button" class="btn-secondary" style="padding: 2px 8px; font-size: 0.75rem;" onclick="setRedScreenDuration(10)">10s</button>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <input type="number" name="red_screen_duration" id="redScreenDurationInput" value="<?= esc($campaign['red_screen_duration'] ?? 5) ?>" min="1" max="60" class="form-control" style="width: 90px; font-size: 0.95rem; text-align: center; font-weight: 700;">
                                <span style="font-size: 0.82rem; color: #64748b;">seconds of blinking "YOU HAVE BEEN HACKED!" alert</span>
                            </div>
                        </div>

                        <!-- Toggle 2: Show Debrief Screen After Red Alert (Default ON) -->
                        <label class="toggle-switch-card" style="margin-top: 0; margin-bottom: 0;">
                            <div class="toggle-switch-text">
                                <strong>Display "THIS WAS A PHISHING TEST" Debrief Screen</strong>
                                <small><strong>Default ON:</strong> Smoothly transitions to the educational debrief with security tips after the countdown.<br>
                                <em>Turn OFF if you want the screen to remain locked without revealing that it was a test drill.</em></small>
                            </div>
                            <div class="switch-control">
                                <input type="hidden" name="show_debrief_screen" value="0">
                                <input type="checkbox" name="show_debrief_screen" value="1" id="showDebriefCheck" <?= (!isset($campaign['show_debrief_screen']) || !empty($campaign['show_debrief_screen'])) ? 'checked' : '' ?>>
                                <span class="switch-slider"></span>
                            </div>
                        </label>
                    </div>

                </div>

                <div class="modal-footer" style="padding: 1.1rem 1.75rem;">
                    <button type="button" class="btn-secondary" onclick="closeModal('launchModal')" style="padding: 10px 20px;">Cancel</button>
                    <button type="submit" class="btn-launch-glow">
                        <ion-icon name="paper-plane-outline"></ion-icon> Launch Simulation
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==========================================================
         MODAL 1B: Quick Campaign Drill Settings Configuration
         ========================================================== -->
    <div id="editDrillSettingsModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <h3>⚙️ Simulation Drill Behavior Settings</h3>
                <button type="button" class="modal-close" onclick="closeModal('editDrillSettingsModal')">&times;</button>
            </div>
            <form action="<?= base_url('admin/campaigns/update_settings/' . $campaign['id']) ?>" method="post">
                <div class="modal-body">
                    <p style="font-size: 0.88rem; color: #475569; margin-bottom: 1.25rem;">
                        Customize what targets experience when clicking phishing simulation links for <strong><?= esc($campaign['name']) ?></strong>.
                    </p>

                    <!-- Toggle 1: Require MS Forms -->
                    <label class="toggle-switch-card" style="margin-top: 0; margin-bottom: 14px;">
                        <div class="toggle-switch-text">
                            <strong>Require Microsoft Forms Data Submission</strong>
                            <small>Turn ON to show Microsoft Forms page first. Turn OFF to show the red alert screen immediately.</small>
                        </div>
                        <div class="switch-control">
                            <input type="hidden" name="has_credential_form" value="0">
                            <input type="checkbox" name="has_credential_form" value="1" <?= !empty($campaign['has_credential_form']) ? 'checked' : '' ?>>
                            <span class="switch-slider"></span>
                        </div>
                    </label>

                    <!-- Red Screen Duration -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px; margin-bottom: 14px;">
                        <label style="font-size: 0.84rem; font-weight: 700; color: #1e293b; margin-bottom: 8px; display: block;">
                            ⏱️ Red Screen Alert Duration
                        </label>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <input type="number" name="red_screen_duration" value="<?= esc($campaign['red_screen_duration'] ?? 5) ?>" min="1" max="60" class="form-control" style="width: 90px; text-align: center; font-weight: 700;">
                            <span style="font-size: 0.82rem; color: #64748b;">seconds before transition/lock</span>
                        </div>
                    </div>

                    <!-- Toggle 2: Show Debrief Screen -->
                    <label class="toggle-switch-card" style="margin-top: 0; margin-bottom: 0;">
                        <div class="toggle-switch-text">
                            <strong>Display "THIS WAS A PHISHING TEST" Debrief Screen</strong>
                            <small>
                                <strong>ON:</strong> Transitions to educational tips after the countdown.<br>
                                <strong>OFF:</strong> Workstation remains locked on the red screen without revealing it was a drill.
                            </small>
                        </div>
                        <div class="switch-control">
                            <input type="hidden" name="show_debrief_screen" value="0">
                            <input type="checkbox" name="show_debrief_screen" value="1" <?= (!isset($campaign['show_debrief_screen']) || !empty($campaign['show_debrief_screen'])) ? 'checked' : '' ?>>
                            <span class="switch-slider"></span>
                        </div>
                    </label>
                </div>
                <div class="modal-footer" style="padding: 1.1rem 1.75rem;">
                    <button type="button" class="btn-secondary" onclick="closeModal('editDrillSettingsModal')" style="padding: 10px 20px;">Cancel</button>
                    <button type="submit" class="btn-primary" style="padding: 10px 22px; font-weight: 700;">
                        Save Drill Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==========================================================
         MODAL 2: Bulk User / Target Import (Gophish Style)
         ========================================================== -->
    <div id="importModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <h3>📥 Bulk Target Import (CSV)</h3>
                <button type="button" class="modal-close" onclick="closeModal('importModal')">&times;</button>
            </div>
            <form action="<?= base_url('admin/import_targets') ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="campaign_id" value="<?= esc($campaign['id']) ?>">
                <div class="modal-body">
                    <p style="font-size: 0.9rem; color: #475569; margin-bottom: 1rem;">
                        Import multiple employees into this awareness campaign using standard Gophish CSV format or by choosing from existing directory users.
                    </p>

                    <!-- Option C: Enroll from Directory -->
                    <div class="form-group" style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px; margin-bottom: 15px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <label style="font-weight: 700; color: #166534; margin: 0;">
                                <ion-icon name="people-outline"></ion-icon> Select & Enroll from Users Directory:
                            </label>
                            <div style="display: flex; gap: 6px;">
                                <button type="button" class="btn-secondary" style="padding: 2px 8px; font-size: 0.75rem;" onclick="toggleAllImportDirUsers(true)">Select All</button>
                                <button type="button" class="btn-secondary" style="padding: 2px 8px; font-size: 0.75rem;" onclick="toggleAllImportDirUsers(false)">Clear</button>
                            </div>
                        </div>
                        <input type="text" id="importDirUserSearch" placeholder="Filter directory users..." class="form-control" style="font-size: 0.82rem; margin-bottom: 8px;" onkeyup="filterImportDirUserList()">
                        <div id="importDirUserList" style="max-height: 150px; overflow-y: auto; border: 1px solid #cbd5e1; border-radius: 6px; background: white; padding: 4px;">
                            <?php if (empty($allDirectoryUsers)): ?>
                                <div style="text-align: center; color: #94a3b8; padding: 15px; font-size: 0.85rem;">No directory users found.</div>
                            <?php else: ?>
                                <?php foreach ($allDirectoryUsers as $du): ?>
                                    <label class="import-dir-item" style="display: flex; align-items: center; gap: 8px; padding: 4px 6px; border-bottom: 1px solid #f8fafc; font-size: 0.82rem; cursor: pointer;">
                                        <input type="checkbox" name="directory_user_ids[]" value="<?= $du['id'] ?>" class="import-dir-user-check">
                                        <span>
                                            <strong><?= esc(!empty($du['first_name']) ? ($du['first_name'] . ' ' . ($du['last_name'] ?? '')) : ($du['name'] ?? 'User')) ?></strong>
                                            <code style="color: #64748b; font-size: 0.78rem;">&lt;<?= esc($du['email']) ?>&gt;</code>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div style="text-align: center; margin: 15px 0; color: #94a3b8; font-weight: 600; font-size: 0.85rem;">— OR IMPORT VIA CSV FILE / TEXT —</div>

                    <div style="margin-bottom: 1.25rem;">
                        <a href="<?= base_url('admin/sample_csv') ?>" class="btn-secondary" style="font-size: 0.85rem;">
                            <ion-icon name="download-outline"></ion-icon> Download Sample CSV Template
                        </a>
                    </div>

                    <!-- Option A: File Upload -->
                    <div class="form-group">
                        <label>Upload CSV File (.csv)</label>
                        <input type="file" name="csv_file" accept=".csv, text/csv, text/plain" class="form-control" style="padding: 6px;">
                    </div>

                    <!-- Option B: Paste CSV Text -->
                    <div class="form-group">
                        <label>Paste CSV Data</label>
                        <textarea name="csv_text" rows="4" class="form-control" placeholder="Alice,Johnson,alice@company.com,Finance Analyst&#10;Bob,Miller,bob@company.com,HR Specialist"></textarea>
                        <small style="color: var(--text-muted);">One user per line. Header row optional.</small>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('importModal')">Cancel</button>
                    <button type="submit" class="btn-success">
                        <ion-icon name="cloud-upload-outline"></ion-icon> Import Targets
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript logic for Dashboard, Charts & Modals -->
    <script>
        // Modal Handlers
        function openModal(id) {
            document.getElementById(id).classList.add('show');
            if (id === 'launchModal') {
                onTemplateChange();
            }
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('show');
        }

        function setRedScreenDuration(sec) {
            const input = document.getElementById('redScreenDurationInput');
            if (input) input.value = sec;
        }

        // Segmented Campaign Type Switcher
        function selectCampaignTypeMode(type) {
            if (type === 'new') {
                document.getElementById('campTypeNew').checked = true;
                if (document.getElementById('segCardNew')) document.getElementById('segCardNew').classList.add('active');
                if (document.getElementById('segCardExisting')) document.getElementById('segCardExisting').classList.remove('active');
            } else {
                document.getElementById('campTypeExisting').checked = true;
                if (document.getElementById('segCardExisting')) document.getElementById('segCardExisting').classList.add('active');
                if (document.getElementById('segCardNew')) document.getElementById('segCardNew').classList.remove('active');
            }
            toggleCampaignType();
        }

        // Toggle Campaign Type (New vs Existing)
        function toggleCampaignType() {
            const isNew = document.getElementById('campTypeNew').checked;
            document.getElementById('campaignNameGroup').style.display = isNew ? 'block' : 'none';
            if (document.getElementById('segCardNew')) {
                document.getElementById('segCardNew').classList.toggle('active', isNew);
                document.getElementById('segCardExisting').classList.toggle('active', !isNew);
            }
            if (isNew) {
                document.getElementById('campaignNameInput').setAttribute('required', 'required');
            } else {
                document.getElementById('campaignNameInput').removeAttribute('required');
            }
        }

        // Segmented Target Audience Chips
        function selectAudienceMode(mode) {
            if (document.getElementById('chipBulk')) document.getElementById('chipBulk').classList.toggle('active', mode === 'bulk');
            if (document.getElementById('chipSelected')) document.getElementById('chipSelected').classList.toggle('active', mode === 'selected');
            if (document.getElementById('chipSingle')) document.getElementById('chipSingle').classList.toggle('active', mode === 'single');

            if (mode === 'bulk') document.getElementById('modeBulk').checked = true;
            else if (mode === 'selected') document.getElementById('modeSelected').checked = true;
            else if (mode === 'single') document.getElementById('modeSingle').checked = true;

            toggleSendMode();
        }

        // Custom File Upload Label Helper
        function onCustomFileSelected(input) {
            const label = document.getElementById('customFileLabel');
            if (input.files && input.files[0]) {
                const file = input.files[0];
                label.innerHTML = '📁 <strong>' + file.name + '</strong> (' + Math.round(file.size / 1024) + ' KB)';
                label.style.color = '#15803d';
            } else {
                label.innerHTML = '📎 Choose file (PNG, JPG, GIF) or leave empty for default';
                label.style.color = '#64748b';
            }
        }

        function onTemplateChange() {
            const select = document.getElementById('templateSelect');
            const selectedOpt = select.options[select.selectedIndex];
            if (selectedOpt) {
                const tmplName = selectedOpt.getAttribute('data-name');
                const sender = selectedOpt.getAttribute('data-sender');
                if (sender) {
                    document.getElementById('previewSenderName').innerText = sender;
                }
                const nameInput = document.getElementById('campaignNameInput');
                if (tmplName && (!nameInput.value || nameInput.value.includes('Assessment') || nameInput.value.includes('Simulation'))) {
                    nameInput.value = tmplName + ' Assessment';
                }
            }
            loadTemplatePreview();
        }

        // Toggle Single vs Selected vs Bulk Mode
        function toggleSendMode() {
            const isSingle = document.getElementById('modeSingle').checked;
            const isSelected = document.getElementById('modeSelected') && document.getElementById('modeSelected').checked;

            document.getElementById('singleEmailGroup').style.display = isSingle ? 'block' : 'none';
            const selectGroup = document.getElementById('selectUsersGroup');
            if (selectGroup) {
                selectGroup.style.display = isSelected ? 'block' : 'none';
            }

            if (isSingle) {
                document.getElementById('targetEmailInput').setAttribute('required', 'required');
            } else {
                document.getElementById('targetEmailInput').removeAttribute('required');
            }
        }

        // Modal Directory Users Filters & Selection
        function toggleAllModalUsers(select) {
            document.querySelectorAll('.modal-target-check').forEach(cb => {
                const label = cb.closest('label');
                if (label && label.style.display !== 'none') {
                    cb.checked = select;
                }
            });
            updateModalSelectedCount();
        }

        function updateModalSelectedCount() {
            const checked = document.querySelectorAll('.modal-target-check:checked');
            const counter = document.getElementById('modalSelectedUsersCount');
            if (counter) counter.innerText = checked.length;
        }

        function filterModalUsersList() {
            const query = document.getElementById('modalUserSearchInput').value.toLowerCase();
            document.querySelectorAll('.modal-user-row').forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(query) ? 'flex' : 'none';
            });
        }

        function toggleAllImportDirUsers(select) {
            document.querySelectorAll('.import-dir-user-check').forEach(cb => {
                const label = cb.closest('label');
                if (label && label.style.display !== 'none') {
                    cb.checked = select;
                }
            });
        }

        function filterImportDirUserList() {
            const query = document.getElementById('importDirUserSearch').value.toLowerCase();
            document.querySelectorAll('.import-dir-item').forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(query) ? 'flex' : 'none';
            });
        }

        // Campaign Targets Table Bulk Selection & Removal
        function toggleSelectAllTargets(master) {
            document.querySelectorAll('.target-select-check').forEach(cb => {
                const row = cb.closest('tr');
                if (row && row.style.display !== 'none') {
                    cb.checked = master.checked;
                }
            });
            updateSelectedTargetsCount();
        }

        function updateSelectedTargetsCount() {
            const checked = document.querySelectorAll('.target-select-check:checked');
            const count = checked.length;
            const btn = document.getElementById('bulkDeleteTargetsBtn');
            const counter = document.getElementById('selectedTargetsCount');
            if (counter) counter.innerText = count;
            if (btn) btn.style.display = count > 0 ? 'inline-flex' : 'none';
        }

        function confirmBulkDeleteTargets() {
            const checked = document.querySelectorAll('.target-select-check:checked');
            if (checked.length === 0) {
                customAlert({
                    title: 'No Targets Selected',
                    message: 'Please select at least one target checkbox from the list to remove.',
                    type: 'warning'
                });
                return;
            }
            customConfirm({
                title: 'Remove Selected Targets?',
                message: 'Remove <strong>' + checked.length + '</strong> selected target(s) from this campaign? This action cannot be undone.',
                type: 'danger',
                confirmBtn: 'Yes, Remove Selected'
            }).then(confirmed => {
                if (!confirmed) return;
                const form = document.getElementById('bulkDeleteTargetsForm');
                form.querySelectorAll('input[name="target_ids[]"]').forEach(el => el.remove());
                checked.forEach(cb => {
                    const inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = 'target_ids[]';
                    inp.value = cb.value;
                    form.appendChild(inp);
                });
                form.submit();
            });
        }

        // Pre-fill Single Send Modal
        function sendSingleModal(email) {
            selectCampaignTypeMode('existing');
            selectAudienceMode('single');
            document.getElementById('targetEmailInput').value = email;
            openModal('launchModal');
        }

        // Filter Table by Status
        function filterTable(filter, tabBtn) {
            document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
            tabBtn.classList.add('active');

            const rows = document.querySelectorAll('.target-row');
            rows.forEach(row => {
                if (filter === 'all') {
                    row.style.display = '';
                } else if (filter === 'sent') {
                    row.style.display = row.dataset.sent === '1' ? '' : 'none';
                } else if (filter === 'opened') {
                    row.style.display = row.dataset.opened === '1' ? '' : 'none';
                } else if (filter === 'clicked') {
                    row.style.display = row.dataset.clicked === '1' ? '' : 'none';
                } else if (filter === 'submitted') {
                    row.style.display = row.dataset.submitted === '1' ? '' : 'none';
                } else if (filter === 'reported') {
                    row.style.display = row.dataset.reported === '1' ? '' : 'none';
                }
            });
        }

        // Search in Targets Table
        function searchTargets() {
            const input = document.getElementById('targetSearch').value.toLowerCase();
            const rows = document.querySelectorAll('.target-row');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(input) ? '' : 'none';
            });
        }

        // Quick Link Generator
        function generateQuickLink() {
            const email = document.getElementById('quickTestEmail').value.trim();
            const out = document.getElementById('quickLinkOutput');
            if (email && email.includes('@')) {
                const baseUrl = '<?= base_url("download/") ?>';
                out.innerText = baseUrl + encodeURIComponent(email) + '/<?= $campaign['id'] ?>';
            } else {
                out.innerText = 'Enter an email above to generate simulation URL';
            }
        }

        function copyQuickLink() {
            const out = document.getElementById('quickLinkOutput').innerText;
            if (out && out.startsWith('http')) {
                navigator.clipboard.writeText(out);
                customAlert({
                    title: 'Simulation Link Copied',
                    message: 'Simulation link copied to clipboard:<br><code style="font-size:0.8rem; color:#0284c7; word-break:break-all;">' + out + '</code>',
                    type: 'success'
                });
            }
        }

        function testTrackingPixel() {
            const email = document.getElementById('quickTestEmail').value.trim();
            if (email && email.includes('@')) {
                const url = '<?= base_url("image/") ?>' + encodeURIComponent(email) + '/<?= $campaign['id'] ?>/pixel.gif';
                window.open(url, '_blank');
            } else {
                customAlert({
                    title: 'Email Address Required',
                    message: 'Please enter a target email address in the input above to test tracking pixel.',
                    type: 'info'
                });
            }
        }

        // Template Preview Fetcher
        function loadTemplatePreview() {
            const select = document.getElementById('templateSelect');
            const templateId = select.value;
            if (!templateId) return;

            fetch('<?= base_url("admin/template_preview/") ?>' + templateId)
                .then(res => res.json())
                .then(data => {
                    if (data && data.html) {
                        document.getElementById('templatePreviewSubject').innerText = 'Subject: ' + data.subject;
                        document.getElementById('templatePreviewBody').innerHTML = data.html;
                    }
                })
                .catch(err => console.error('Preview error:', err));
        }

        function togglePreviewAccordion() {
            const box = document.getElementById('previewAccordion');
            box.style.display = box.style.display === 'none' ? 'block' : 'none';
        }

        // ==========================================
        // AUTO-REFRESH & LIVE DASHBOARD POLLING
        // ==========================================
        let autoRefreshTimer = null;
        let autoRefreshInterval = 10;
        let autoRefreshSecondsLeft = 10;
        let isRefreshing = false;
        window.campaignChartInstance = null;

        function initAutoRefresh() {
            const savedEnabled = localStorage.getItem('phishguard_auto_refresh_enabled');
            const savedInterval = localStorage.getItem('phishguard_auto_refresh_interval');

            if (savedInterval) {
                autoRefreshInterval = parseInt(savedInterval, 10) || 10;
                const select = document.getElementById('autoRefreshIntervalSelect');
                if (select) select.value = autoRefreshInterval;
            }

            const isEnabled = (savedEnabled === null) ? true : (savedEnabled === 'true');
            const checkbox = document.getElementById('autoRefreshCheckbox');
            if (checkbox) checkbox.checked = isEnabled;

            toggleAutoRefresh(isEnabled, false);
        }

        function toggleAutoRefresh(enable, save = true) {
            const pill = document.getElementById('autoRefreshPill');
            if (save) localStorage.setItem('phishguard_auto_refresh_enabled', enable ? 'true' : 'false');

            if (enable) {
                if (pill) pill.classList.add('active');
                startAutoRefreshTimer();
            } else {
                if (pill) pill.classList.remove('active');
                stopAutoRefreshTimer();
                const badge = document.getElementById('autoRefreshTimerPill');
                if (badge) badge.textContent = 'OFF';
            }
        }

        function changeAutoRefreshInterval(sec) {
            autoRefreshInterval = parseInt(sec, 10) || 10;
            autoRefreshSecondsLeft = autoRefreshInterval;
            localStorage.setItem('phishguard_auto_refresh_interval', autoRefreshInterval);
            const badge = document.getElementById('autoRefreshTimerPill');
            if (badge) badge.textContent = autoRefreshSecondsLeft + 's';
        }

        function startAutoRefreshTimer() {
            stopAutoRefreshTimer();
            autoRefreshSecondsLeft = autoRefreshInterval;
            const badge = document.getElementById('autoRefreshTimerPill');
            if (badge) badge.textContent = autoRefreshSecondsLeft + 's';

            autoRefreshTimer = setInterval(() => {
                // If any modal is currently open, pause countdown so user is not interrupted
                if (document.querySelector('.modal-overlay.show')) {
                    return;
                }

                autoRefreshSecondsLeft--;
                if (badge) badge.textContent = autoRefreshSecondsLeft + 's';

                if (autoRefreshSecondsLeft <= 0) {
                    autoRefreshSecondsLeft = autoRefreshInterval;
                    refreshDashboard(false);
                }
            }, 1000);
        }

        function stopAutoRefreshTimer() {
            if (autoRefreshTimer) {
                clearInterval(autoRefreshTimer);
                autoRefreshTimer = null;
            }
        }

        function triggerManualRefresh() {
            refreshDashboard(true);
        }

        function refreshDashboard(isManual = false) {
            if (isRefreshing) return;
            isRefreshing = true;

            const btn = document.getElementById('btnManualRefresh');
            const label = document.getElementById('refreshBtnLabel');
            if (btn) btn.classList.add('refreshing');
            if (label && isManual) label.textContent = 'Updating...';

            const currentSearch = document.getElementById('targetSearch') ? document.getElementById('targetSearch').value : '';
            const activeTab = document.querySelector('.filter-tab.active');
            const activeFilterType = activeTab ? activeTab.getAttribute('data-filter') : 'all';

            fetch(window.location.href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                cache: 'no-store'
            })
            .then(res => {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.text();
            })
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // 1. Update Funnel Grid
                const newFunnel = doc.getElementById('funnelGridContainer');
                const curFunnel = document.getElementById('funnelGridContainer');
                if (newFunnel && curFunnel) {
                    curFunnel.innerHTML = newFunnel.innerHTML;
                    curFunnel.setAttribute('data-sent', newFunnel.getAttribute('data-sent') || 0);
                    curFunnel.setAttribute('data-opened', newFunnel.getAttribute('data-opened') || 0);
                    curFunnel.setAttribute('data-clicked', newFunnel.getAttribute('data-clicked') || 0);
                    curFunnel.setAttribute('data-submitted', newFunnel.getAttribute('data-submitted') || 0);
                    curFunnel.setAttribute('data-reported', newFunnel.getAttribute('data-reported') || 0);

                    // Update Chart
                    updateCampaignChart(
                        parseInt(newFunnel.getAttribute('data-sent') || 0, 10),
                        parseInt(newFunnel.getAttribute('data-opened') || 0, 10),
                        parseInt(newFunnel.getAttribute('data-clicked') || 0, 10),
                        parseInt(newFunnel.getAttribute('data-submitted') || 0, 10),
                        parseInt(newFunnel.getAttribute('data-reported') || 0, 10)
                    );
                }

                // 2. Update Campaign Meta
                const newMeta = doc.getElementById('campaignMetaContainer');
                const curMeta = document.getElementById('campaignMetaContainer');
                if (newMeta && curMeta) {
                    curMeta.innerHTML = newMeta.innerHTML;
                }

                // 3. Update Targets Table
                const newTargets = doc.getElementById('targetsSectionContainer');
                const curTargets = document.getElementById('targetsSectionContainer');
                if (newTargets && curTargets) {
                    curTargets.innerHTML = newTargets.innerHTML;

                    // Re-apply filter tab
                    if (activeFilterType && activeFilterType !== 'all') {
                        const targetTab = curTargets.querySelector(`.filter-tab[data-filter="${activeFilterType}"]`);
                        if (targetTab) {
                            curTargets.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
                            targetTab.classList.add('active');
                            filterTable(activeFilterType, targetTab);
                        }
                    }
                    // Re-apply search filter
                    if (currentSearch) {
                        const searchInput = document.getElementById('targetSearch');
                        if (searchInput) {
                            searchInput.value = currentSearch;
                            searchTargets();
                        }
                    }
                }

                // 4. Update Activity Container
                const newActivity = doc.getElementById('recentActivityContainer');
                const curActivity = document.getElementById('recentActivityContainer');
                if (newActivity && curActivity) {
                    curActivity.innerHTML = newActivity.innerHTML;
                }

                // 5. Update timestamp
                const timeEl = document.getElementById('lastRefreshTimeVal');
                if (timeEl) {
                    const now = new Date();
                    timeEl.textContent = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                }

                // Reset timer badge
                autoRefreshSecondsLeft = autoRefreshInterval;
                const badge = document.getElementById('autoRefreshTimerPill');
                if (badge && document.getElementById('autoRefreshCheckbox').checked) {
                    badge.textContent = autoRefreshSecondsLeft + 's';
                }
            })
            .catch(err => {
                console.warn('Dashboard live refresh error:', err);
                if (isManual) {
                    window.location.reload();
                }
            })
            .finally(() => {
                isRefreshing = false;
                if (btn) btn.classList.remove('refreshing');
                if (label) label.textContent = 'Refresh';
            });
        }

        function updateCampaignChart(sent, opened, clicked, submitted, reported) {
            if (!window.campaignChartInstance) return;
            const hasData = (sent > 0 || opened > 0 || clicked > 0 || submitted > 0 || reported > 0);
            window.campaignChartInstance.data.labels = hasData 
                ? ['Sent (Unopened)', 'Opened', 'Clicked Link', 'Submitted Data', 'Reported Phish 🛡️']
                : ['No Events Yet'];
            window.campaignChartInstance.data.datasets[0].data = hasData ? [
                Math.max(0, sent - opened),
                Math.max(0, opened - clicked),
                Math.max(0, clicked - submitted),
                submitted,
                reported
            ] : [1];
            window.campaignChartInstance.data.datasets[0].backgroundColor = hasData ? [
                '#3b82f6', '#06b6d4', '#f97316', '#ef4444', '#10b981'
            ] : ['#e2e8f0'];
            window.campaignChartInstance.update();
        }

        function onSimulationSubmit(event, form) {
            const btn = form.querySelector('.btn-launch-glow');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<ion-icon name="sync-outline" style="animation: spinRefresh 0.8s linear infinite; vertical-align: middle;"></ion-icon> Dispatching Simulation...';
                btn.style.opacity = '0.85';
            }
        }

        // Initialize Chart.js for Gophish-style Campaign Overview
        document.addEventListener('DOMContentLoaded', function() {
            initAutoRefresh();

            const ctx = document.getElementById('campaignChart').getContext('2d');
            const sent = <?= (int)$stats['sent_count'] ?>;
            const opened = <?= (int)$stats['opened_count'] ?>;
            const clicked = <?= (int)$stats['clicked_count'] ?>;
            const submitted = <?= (int)$stats['submitted_count'] ?>;
            const reported = <?= (int)$stats['reported_count'] ?>;

            const hasData = (sent > 0 || opened > 0 || clicked > 0 || submitted > 0 || reported > 0);

            window.campaignChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: hasData 
                        ? ['Sent (Unopened)', 'Opened', 'Clicked Link', 'Submitted Data', 'Reported Phish 🛡️']
                        : ['No Events Yet'],
                    datasets: [{
                        data: hasData ? [
                            Math.max(0, sent - opened),
                            Math.max(0, opened - clicked),
                            Math.max(0, clicked - submitted),
                            submitted,
                            reported
                        ] : [1],
                        backgroundColor: hasData ? [
                            '#3b82f6',
                            '#06b6d4',
                            '#f97316',
                            '#ef4444',
                            '#10b981'
                        ] : ['#e2e8f0'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 14,
                                font: { size: 12, family: 'Inter' }
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        });

        // View Captured Form Data in Modal
        function viewCapturedData(email, data) {
            document.getElementById('capModalEmail').textContent = email;
            document.getElementById('capModalName').textContent = data.full_name || '—';
            document.getElementById('capModalEmpId').textContent = data.employee_id || '—';
            document.getElementById('capModalEmployed').textContent = data.employed || '—';
            document.getElementById('capModalTime').textContent = data.submitted_at || '—';
            document.getElementById('capModalIp').textContent = data.ip_address || '—';
            openModal('capturedDataModal');
        }

        // Close modal on click outside
        window.onclick = function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                e.target.classList.remove('show');
            }
        };
    </script>

    <!-- Modal: View Target Captured Form Data -->
    <div id="capturedDataModal" class="modal-overlay">
        <div class="modal-card" style="max-width: 540px;">
            <div class="modal-header" style="background: #fff1f2; border-bottom: 1px solid #fecdd3;">
                <h3 style="color: #9f1239; display: flex; align-items: center; gap: 8px; margin: 0; font-size: 1.1rem;">
                    <ion-icon name="warning" style="font-size: 1.4rem; color: #e11d48;"></ion-icon>
                    Captured Simulation Form Data
                </h3>
                <button type="button" class="modal-close" onclick="closeModal('capturedDataModal')">&times;</button>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 15px;">
                    Below are the exact values submitted by the employee into the Microsoft Forms simulation:
                </p>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px;">
                    <div style="margin-bottom: 12px;">
                        <strong style="font-size: 0.75rem; text-transform: uppercase; color: #64748b; display: block;">Recipient Email:</strong>
                        <span id="capModalEmail" style="font-weight: 700; color: #1e293b; font-size: 0.95rem;"></span>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div>
                            <strong style="font-size: 0.75rem; text-transform: uppercase; color: #64748b; display: block;">Full Name Entered:</strong>
                            <span id="capModalName" style="color: #e11d48; font-weight: 700; font-size: 0.95rem;"></span>
                        </div>
                        <div>
                            <strong style="font-size: 0.75rem; text-transform: uppercase; color: #64748b; display: block;">Employee ID:</strong>
                            <span id="capModalEmpId" style="color: #e11d48; font-weight: 700; font-size: 0.95rem;"></span>
                        </div>
                        <div>
                            <strong style="font-size: 0.75rem; text-transform: uppercase; color: #64748b; display: block;">Employment Status:</strong>
                            <span id="capModalEmployed" style="color: #1e293b; font-weight: 600;"></span>
                        </div>
                        <div>
                            <strong style="font-size: 0.75rem; text-transform: uppercase; color: #64748b; display: block;">Submitted Timestamp:</strong>
                            <span id="capModalTime" style="color: #64748b; font-size: 0.85rem;"></span>
                        </div>
                    </div>
                    <div style="margin-top: 14px; padding-top: 10px; border-top: 1px dashed #cbd5e1; font-size: 0.8rem; color: #64748b;">
                        Client IP Address: <code id="capModalIp" style="color: #0284c7; font-weight: bold;"></code>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('capturedDataModal')">Close</button>
            </div>
        </div>
    </div>

    <!-- Hidden Bulk Delete Targets Form for Campaign -->
    <form id="bulkDeleteTargetsForm" action="<?= base_url('admin/bulk_delete_campaign_targets') ?>" method="post" style="display: none;">
        <input type="hidden" name="campaign_id" value="<?= esc($campaign['id'] ?? '') ?>">
    </form>
</body>
</html>
