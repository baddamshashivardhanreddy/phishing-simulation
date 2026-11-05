<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PhishGuard - Campaigns Management</title>
    <!-- Custom CSS -->
    <link href="<?= base_url('assets/admin.css') ?>" rel="stylesheet">
    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
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
            <li><a href="<?= base_url('admin/campaigns') ?>" class="active"><ion-icon name="albums-outline" style="margin-right:10px"></ion-icon> <span>Campaigns</span></a></li>
            <li><a href="<?= base_url('admin/users') ?>"><ion-icon name="people-outline" style="margin-right:10px"></ion-icon> <span>Simulation Users</span></a></li>
            <li><a href="<?= base_url('admin/templates') ?>"><ion-icon name="mail-outline" style="margin-right:10px"></ion-icon> <span>Email Templates</span></a></li>
            <li><a href="<?= base_url('admin/profile') ?>"><ion-icon name="shield-outline" style="margin-right:10px"></ion-icon> <span>Admin Users</span></a></li>
            <li><a href="<?= base_url('admin/settings') ?>"><ion-icon name="settings-outline" style="margin-right:10px"></ion-icon> <span>Settings</span></a></li>
            <li><a href="<?= base_url('quiz') ?>" target="_blank"><ion-icon name="help-circle-outline" style="margin-right:10px"></ion-icon> <span>View Quiz</span></a></li>
            <li><a href="<?= base_url('admin/export') ?>"><ion-icon name="download-outline" style="margin-right:10px"></ion-icon> <span>Export Data</span></a></li>
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

        <!-- Campaigns Management Header -->
        <div class="table-container" style="margin-bottom: 2rem;">
            <div class="table-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <div>
                    <h2 style="margin: 0; font-size: 1.5rem; color: #1e293b; display: flex; align-items: center; gap: 10px;">
                        <ion-icon name="albums-outline" style="color: var(--primary-color);"></ion-icon>
                        Phishing Awareness Campaigns
                    </h2>
                    <p style="margin: 5px 0 0; color: var(--text-muted); font-size: 0.9rem;">
                        Manage separate awareness campaigns independently like Gophish. Each campaign maintains isolated tracking statistics.
                    </p>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="button" class="btn-primary" onclick="openModal('launchModal')" style="padding: 10px 20px;">
                        <ion-icon name="add-circle-outline"></ion-icon> New Campaign
                    </button>
                    <a href="<?= base_url('admin/users') ?>" class="btn-secondary" style="padding: 10px 16px;">
                        <ion-icon name="people-outline"></ion-icon> Directory (<?= $masterTargetCount ?> Users)
                    </a>
                </div>
            </div>
        </div>

        <!-- Campaigns List Table -->
        <div class="table-container">
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Campaign Name</th>
                            <th>Template</th>
                            <th>Created Date</th>
                            <th>Status</th>
                            <th>Targets</th>
                            <th>Engagement Funnel</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($campaigns)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 3rem; color: #94a3b8;">
                                    <ion-icon name="albums-outline" style="font-size: 2.5rem; margin-bottom: 10px; display: block;"></ion-icon>
                                    No campaigns created yet.<br>
                                    <button type="button" class="btn-primary" style="margin-top: 15px;" onclick="openModal('launchModal')">
                                        <ion-icon name="paper-plane-outline" style="margin-right: 5px;"></ion-icon> Launch Your First Campaign
                                    </button>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($campaigns as $c): ?>
                                <tr>
                                    <td>
                                        <a href="<?= base_url('admin?campaign_id=' . $c['id']) ?>" style="font-weight: 700; color: #1e293b; text-decoration: none; font-size: 0.95rem;">
                                            <?= esc($c['name']) ?>
                                        </a>
                                        <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 2px;">
                                            ID #<?= esc($c['id']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span style="font-size: 0.9rem; font-weight: 500; color: #334155;">
                                            <?= esc($c['template_name'] ?: 'Standard Security Notice') ?>
                                        </span>
                                    </td>
                                    <td style="font-size: 0.85rem; color: var(--text-muted);">
                                        <?= !empty($c['created_at']) ? date('M d, Y H:i', strtotime($c['created_at'])) : '—' ?>
                                    </td>
                                    <td>
                                        <?php if ($c['status'] === 'Completed'): ?>
                                            <span class="badge" style="background: #e2e8f0; color: #475569;">Completed</span>
                                        <?php else: ?>
                                            <span class="badge badge-status-sent" style="background: #dcfce7; color: #166534;">In progress</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= (int)$c['total_targets'] ?></strong> recipients
                                    </td>
                                    <td style="min-width: 260px;">
                                        <div style="font-size: 0.8rem; display: flex; justify-content: space-between; margin-bottom: 4px; flex-wrap: wrap; gap: 4px;">
                                            <span>Sent: <strong><?= $c['sent_count'] ?></strong></span>
                                            <span>Opened: <strong><?= $c['opened_pct'] ?>%</strong></span>
                                            <span>Clicked: <strong style="color: #ea580c;"><?= $c['clicked_pct'] ?>%</strong></span>
                                            <span>Submitted: <strong style="color: #dc2626;"><?= $c['submitted_pct'] ?>%</strong></span>
                                            <span>Reported: <strong style="color: #059669;"><?= $c['reported_pct'] ?>%</strong></span>
                                        </div>
                                        <div style="background: #e2e8f0; height: 8px; border-radius: 4px; overflow: hidden; display: flex;">
                                            <div style="background: #3b82f6; width: <?= (int)$c['sent_count'] > 0 ? '100%' : '0%' ?>;" title="Sent: <?= $c['sent_count'] ?>"></div>
                                            <div style="background: #06b6d4; width: <?= (int)$c['opened_pct'] ?>%;" title="Opened: <?= $c['opened_pct'] ?>%"></div>
                                            <div style="background: #f97316; width: <?= (int)$c['clicked_pct'] ?>%;" title="Clicked: <?= $c['clicked_pct'] ?>%"></div>
                                            <div style="background: #ef4444; width: <?= (int)$c['submitted_pct'] ?>%;" title="Submitted: <?= $c['submitted_pct'] ?>%"></div>
                                            <div style="background: #10b981; width: <?= (int)$c['reported_pct'] ?>%;" title="Reported: <?= $c['reported_pct'] ?>%"></div>
                                        </div>
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        <a href="<?= base_url('admin?campaign_id=' . $c['id']) ?>" class="btn-primary" style="padding: 5px 12px; font-size: 0.8rem; text-decoration: none;" title="Open Campaign Dashboard">
                                            <ion-icon name="stats-chart-outline"></ion-icon> Dashboard
                                        </a>
                                        <?php if ($c['status'] !== 'Completed'): ?>
                                            <a href="<?= base_url('admin/campaigns/complete/' . $c['id']) ?>" class="btn-secondary" style="padding: 5px 10px; font-size: 0.8rem; text-decoration: none;" onclick="return confirm('Mark campaign <?= esc($c['name']) ?> as completed?');" title="Mark as Completed">
                                                <ion-icon name="checkmark-done-outline"></ion-icon>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= base_url('admin/export?campaign_id=' . $c['id']) ?>" class="btn-secondary" style="padding: 5px 10px; font-size: 0.8rem; text-decoration: none;" title="Export CSV Report">
                                            <ion-icon name="download-outline"></ion-icon>
                                        </a>
                                        <a href="<?= base_url('admin/campaigns/delete/' . $c['id']) ?>" class="btn-danger-outline" style="padding: 5px 10px; font-size: 0.8rem; text-decoration: none;" onclick="return confirm('Permanently delete campaign \'<?= esc($c['name']) ?>\' and its results?');" title="Delete Campaign">
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

    </div>

    <!-- ==========================================================
         MODAL: Launch New Phishing Awareness Campaign (Redesigned Modern UI)
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
                        <p>Configure assessment parameters, audience targeting, and email delivery</p>
                    </div>
                </div>
                <button type="button" class="modal-close-round" onclick="closeModal('launchModal')" title="Close">&times;</button>
            </div>

            <form action="<?= base_url('admin/send') ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="campaign_type" value="new">
                <div class="modal-body" style="padding: 1.5rem 1.75rem;">
                    
                    <!-- SECTION 1: CAMPAIGN CONFIGURATION -->
                    <div class="modal-section">
                        <div class="modal-section-title">
                            <ion-icon name="options-outline"></ion-icon> 1. Campaign Assessment Setup
                        </div>

                        <!-- Campaign Name -->
                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label style="font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                                Campaign Assessment Name <span style="color:#ef4444;">*</span>
                            </label>
                            <input type="text" name="campaign_name" id="campaignNameInput" required 
                                   value="Diwali Special Employee Bonus Assessment" 
                                   placeholder="e.g. Q4 Phishing Awareness Simulation" class="form-control" style="font-size: 0.9rem;">
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

                            <!-- Live Template Preview Box -->
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

                        <!-- Select Specific Users Checklist -->
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
                                    <div style="text-align: center; color: #94a3b8; padding: 15px; font-size: 0.85rem;">No directory users found. Please import users in the Simulation Users tab.</div>
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

                        <!-- Single Target Email Input -->
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
                                <div class="profile-avatar-sm">RY</div>
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
                                <input type="checkbox" name="include_report_link" value="1" id="includeReportLinkCampaigns">
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

    <script>
        function openModal(id) {
            document.getElementById(id).classList.add('show');
            if (id === 'launchModal') {
                onTemplateChange();
            }
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('show');
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

        window.onclick = function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                e.target.classList.remove('show');
            }
        };
    </script>
</body>
</html>

