<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - PhishGuard Admin</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('icon.svg') ?>">
    <link rel="alternate icon" href="<?= base_url('favicon.ico') ?>">
    <!-- Link to the custom CSS -->
    <link href="<?= base_url('assets/admin.css') ?>" rel="stylesheet">
    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>
<body>

    <!-- Sidebar -->
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
            <li><a href="<?= base_url('admin/profile') ?>"><ion-icon name="shield-outline" style="margin-right:10px"></ion-icon> <span>Admin Users</span></a></li>
            <li><a href="<?= base_url('admin/settings') ?>" class="active"><ion-icon name="settings-outline" style="margin-right:10px"></ion-icon> <span>Settings</span></a></li>
            <li><a href="<?= base_url('quiz') ?>" target="_blank"><ion-icon name="help-circle-outline" style="margin-right:10px"></ion-icon> <span>View Quiz</span></a></li>
            <li><a href="<?= base_url('admin/export') ?>"><ion-icon name="download-outline" style="margin-right:10px"></ion-icon> <span>Export Data</span></a></li>
        </ul>
        <div style="margin-top: auto; padding: 20px;">
           <a href="<?= base_url('logout') ?>" style="color: #ef4444; text-decoration: none; display: flex; align-items: center;"><ion-icon name="log-out-outline" style="margin-right:10px"></ion-icon> Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <div>
                <h2>System Configuration</h2>
                <p>Manage Sending Profiles, SMTP, API Keys, and Simulation Preferences</p>
            </div>
        </div>

        <div class="table-container" style="max-width: 800px;">
            <div class="table-header">
                <h3>Sending Profile & SMTP Settings</h3>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('admin/save_settings') ?>" method="post">
                
                <h4 style="margin-bottom: 8px; border-bottom: 1px solid #eee; padding-bottom: 8px; color: var(--primary-color); display: flex; align-items: center; gap: 8px;">
                    <ion-icon name="globe-outline"></ion-icon> Domain & Server Architecture (Gophish Style Separation)
                </h4>
                <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 16px; line-height: 1.45;">
                    Keep the <strong>Admin Console Domain</strong> completely separate from the <strong>Phishing Domain</strong> sent to recipients. Users and network scanners visiting the Phishing Domain will never see or discover the Admin Login portal.
                </p>

                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; margin-bottom: 1.75rem;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 1rem;">
                        <div>
                            <label style="display:block; margin-bottom: 5px; color: #334155; font-weight: 700; font-size: 0.86rem;">
                                🛡️ Admin Server URL / Domain (Private)
                            </label>
                            <input type="text" name="admin_server_url" value="<?= esc($settings['admin_server_url'] ?? rtrim(base_url(), '/')) ?>" placeholder="e.g. http://10.10.0.190/phishing-simulation" required
                                   style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px; font-family: monospace; font-size: 0.88rem;">
                            <small style="color: #64748b; display: block; margin-top: 4px;">Private URL used exclusively by IT Security Admins for management, login, and analytics.</small>
                        </div>
                        <div>
                            <label style="display:block; margin-bottom: 5px; color: #334155; font-weight: 700; font-size: 0.86rem;">
                                🎣 Phishing Server URL / Domain (Public Target Link)
                            </label>
                            <input type="text" name="phish_server_url" value="<?= esc($settings['phish_server_url'] ?? '') ?>" placeholder="e.g. http://employee-awards.com or http://10.10.0.191/phishing-simulation"
                                   style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px; font-family: monospace; font-size: 0.88rem;">
                            <small style="color: #64748b; display: block; margin-top: 4px;">Public/decoy domain embedded in phishing email links ({{.URL}}), tracking pixels, and forms. Targets will only see this domain.</small>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 20px; align-items: center; border-top: 1px solid #eef2f6; padding-top: 14px; margin-top: 10px;">
                        <div>
                            <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer;">
                                <input type="checkbox" name="cloak_phish_domain" value="1" <?= (!empty($settings['cloak_phish_domain'])) ? 'checked' : '' ?> style="margin-top: 3px; width: 16px; height: 16px; accent-color: var(--primary-color);">
                                <div>
                                    <strong style="color: #1e293b; font-size: 0.88rem; display: block;">Enable Strict Phishing Domain Cloaking (Decoy 404)</strong>
                                    <small style="color: #64748b; font-size: 0.8rem; display: block; margin-top: 2px;">
                                        If targets browse to <code>/</code>, <code>/login</code>, or <code>/admin</code> on the Phishing Domain, the server returns an authentic 404 Not Found error instead of the admin panel.
                                    </small>
                                </div>
                            </label>
                        </div>
                        <div>
                            <label style="display:block; margin-bottom: 4px; color: #334155; font-weight: 600; font-size: 0.82rem;">
                                Emergency Admin Access Key
                            </label>
                            <input type="text" name="admin_bypass_key" value="<?= esc($settings['admin_bypass_key'] ?? 'phishguard-bypass') ?>" placeholder="phishguard-bypass"
                                   style="width: 100%; padding: 8px 10px; border: 1px solid var(--border-color); border-radius: 6px; font-family: monospace; font-size: 0.82rem;">
                            <small style="color: #94a3b8; display: block; margin-top: 2px;">Append <code>?admin_key=...</code> if emergency access needed on phishing domain.</small>
                        </div>
                    </div>
                </div>

                <h4 style="margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 5px; color: var(--primary-color);">Sender Identity (Gophish Sending Profile)</h4>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display:block; margin-bottom: 5px; color: var(--text-muted); font-weight: 600;">Sender Display Name</label>
                        <input type="text" name="from_name" value="<?= esc($settings['from_name'] ?? 'Ravindranath Yarlagadda') ?>" placeholder="e.g. Ravindranath Yarlagadda" required
                               style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px;">
                        <small style="color: #777;">Full name shown in the target's inbox.</small>
                    </div>
                    <div>
                        <label style="display:block; margin-bottom: 5px; color: var(--text-muted); font-weight: 600;">Sender Email Address</label>
                        <input type="email" name="from_email" value="<?= esc($settings['from_email'] ?? '') ?>" placeholder="e.g. security@company.com" required
                               style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px;">
                        <small style="color: #777;">From email address displayed to recipients.</small>
                    </div>
                </div>

                <h4 style="margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 5px; color: var(--primary-color);">SMTP Server Configuration</h4>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div style="margin-bottom: 1rem;">
                        <label style="display:block; margin-bottom: 5px; color: var(--text-muted);">SMTP Host</label>
                        <input type="text" name="smtp_host" value="<?= esc($settings['smtp_host']) ?>" required 
                               style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px;">
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <label style="display:block; margin-bottom: 5px; color: var(--text-muted);">SMTP Port</label>
                        <input type="number" name="smtp_port" value="<?= esc($settings['smtp_port']) ?>" required 
                               style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div style="margin-bottom: 1rem;">
                        <label style="display:block; margin-bottom: 5px; color: var(--text-muted);">SMTP User (Username)</label>
                        <input type="text" name="smtp_user" value="<?= esc($settings['smtp_user']) ?>" 
                               style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px;">
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <label style="display:block; margin-bottom: 5px; color: var(--text-muted);">SMTP Password</label>
                        <input type="password" name="smtp_pass" value="<?= esc($settings['smtp_pass']) ?>" 
                               style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px;">
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display:block; margin-bottom: 5px; color: var(--text-muted);">Crypto / TLS</label>
                    <select name="smtp_crypto" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px;">
                        <option value="tls" <?= ($settings['smtp_crypto'] == 'tls') ? 'selected' : '' ?>>TLS (Recommended for Port 587)</option>
                        <option value="ssl" <?= ($settings['smtp_crypto'] == 'ssl') ? 'selected' : '' ?>>SSL (Port 465)</option>
                        <option value="" <?= (empty($settings['smtp_crypto'])) ? 'selected' : '' ?>>None</option>
                    </select>
                </div>

                <h4 style="margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px; color: var(--primary-color);">Brevo API Configuration (Optional)</h4>
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display:block; margin-bottom: 5px; color: var(--text-muted);">Brevo API Key (v3)</label>
                    <input type="password" name="brevo_api_key" value="<?= esc($settings['brevo_api_key'] ?? '') ?>" placeholder="xkeysib-..." 
                           style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px;">
                    <small style="color: #666;">If provided, emails will be dispatched via Brevo REST API instead of SMTP.</small>
                </div>

                <button type="submit" class="btn-primary" style="padding: 10px 25px;">Save Settings</button>
            </form>
        </div>
    </div>

</body>
</html>
