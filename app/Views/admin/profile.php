<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Users & Profile - PhishGuard Admin</title>
    <!-- Stylesheet -->
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
            <li><a href="<?= base_url('admin/campaigns') ?>"><ion-icon name="albums-outline" style="margin-right:10px"></ion-icon> <span>Campaigns</span></a></li>
            <li><a href="<?= base_url('admin/users') ?>"><ion-icon name="people-outline" style="margin-right:10px"></ion-icon> <span>Simulation Users</span></a></li>
            <li><a href="<?= base_url('admin/templates') ?>"><ion-icon name="mail-outline" style="margin-right:10px"></ion-icon> <span>Email Templates</span></a></li>
            <li><a href="<?= base_url('admin/profile') ?>" class="active"><ion-icon name="shield-outline" style="margin-right:10px"></ion-icon> <span>Admin Users</span></a></li>
            <li><a href="<?= base_url('admin/settings') ?>"><ion-icon name="settings-outline" style="margin-right:10px"></ion-icon> <span>Settings</span></a></li>
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
                <h2>Admin Users & Security Profile</h2>
                <p>Manage system administrator credentials and access accounts</p>
            </div>
            <div>
                <button type="button" class="btn-primary" onclick="openModal('newAdminModal')">
                    <ion-icon name="person-add-outline" style="margin-right:5px; vertical-align:middle;"></ion-icon> Add New Admin
                </button>
            </div>
        </div>

        <!-- Flash Notifications -->
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

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            
            <!-- Card 1: Edit Current Admin Profile -->
            <div class="table-container">
                <div class="table-header">
                    <h3>My Profile Details</h3>
                    <small style="color: var(--text-muted)">Logged in as: <strong><?= esc($username) ?></strong></small>
                </div>

                <form action="<?= base_url('admin/update_profile') ?>" method="post">
                    <input type="hidden" name="id" value="<?= esc($currentAdmin['id'] ?? '') ?>">

                    <div class="form-group">
                        <label>Admin Username <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="username" value="<?= esc($username) ?>" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Admin Email Address</label>
                        <input type="email" name="email" value="<?= esc($currentAdmin['email'] ?? '') ?>" placeholder="admin@company.com" class="form-control">
                        <small style="color: var(--text-muted);">Used for administrator notifications and alerts.</small>
                    </div>

                    <div class="form-group">
                        <label>Change Password</label>
                        <input type="password" name="password" placeholder="Leave blank to keep current password" class="form-control">
                        <small style="color: var(--text-muted);">Only fill this in if you want to update your login password.</small>
                    </div>

                    <button type="submit" class="btn-primary" style="padding: 10px 24px;">Save Profile Changes</button>
                </form>
            </div>

            <!-- Card 2: Security & Account Info -->
            <div class="table-container">
                <div class="table-header">
                    <h3>Account Security Overview</h3>
                </div>
                <div style="font-size: 0.92rem; line-height: 1.8; color: #475569;">
                    <p><strong>Authentication Mode:</strong> Database-backed password hashing with bcrypt (Cost: 10).</p>
                    <p><strong>Account Role:</strong> System Administrator (Full Access).</p>
                    <p><strong>Session Created:</strong> <?= date('Y-m-d H:i:s') ?></p>
                    <p><strong>Last Updated:</strong> <?= !empty($currentAdmin['updated_at']) ? esc($currentAdmin['updated_at']) : 'Recently' ?></p>
                    
                    <div style="margin-top: 20px; padding: 15px; background: #f8fafc; border: 1px solid var(--border-color); border-radius: 8px;">
                        <span style="font-weight: 600; color: #1e293b; display: block; margin-bottom: 5px;">Security Recommendations:</span>
                        <ul style="padding-left: 20px; color: #64748b; font-size: 0.85rem; line-height: 1.6;">
                            <li>Use a strong password with at least 10 characters.</li>
                            <li>Do not share administrator login details with untrusted parties.</li>
                            <li>Remember to log out after completing phishing campaigns.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>

        <!-- All Admin Accounts Table -->
        <div class="table-container" style="margin-top: 2rem;">
            <div class="table-header" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3>Administrator Accounts (<?= count($allAdmins) ?>)</h3>
                    <small style="color: var(--text-muted)">Accounts with administrative access to PhishGuard</small>
                </div>
                <button type="button" class="btn-secondary" onclick="openModal('newAdminModal')">
                    <ion-icon name="add-outline"></ion-icon> New Admin
                </button>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email Address</th>
                        <th>Role</th>
                        <th>Created Date</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allAdmins as $adm): ?>
                        <tr>
                            <td>#<?= esc($adm['id']) ?></td>
                            <td>
                                <strong><?= esc($adm['username']) ?></strong>
                                <?php if ($adm['username'] === session()->get('admin_user')): ?>
                                    <span class="badge badge-success" style="margin-left: 6px;">Current</span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($adm['email'] ?: '—') ?></td>
                            <td><span class="badge badge-info">Administrator</span></td>
                            <td style="font-size: 0.85rem; color: var(--text-muted);"><?= esc($adm['created_at'] ?? '—') ?></td>
                            <td style="text-align: right;">
                                <?php if ($adm['username'] !== session()->get('admin_user')): ?>
                                    <a href="<?= base_url('admin/delete_admin/' . $adm['id']) ?>" class="btn-danger-outline" 
                                       onclick="return confirm('Delete admin user <?= esc($adm['username']) ?>?');">
                                        <ion-icon name="trash-outline"></ion-icon> Delete
                                    </a>
                                <?php else: ?>
                                    <span style="font-size: 0.8rem; color: var(--text-muted);">Active Session</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

    <!-- Modal: Add New Admin Account -->
    <div id="newAdminModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <h3>Add New Administrator</h3>
                <button type="button" class="modal-close" onclick="closeModal('newAdminModal')">&times;</button>
            </div>
            <form action="<?= base_url('admin/create_admin') ?>" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Username <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="username" required placeholder="e.g. security_admin" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" placeholder="admin@company.com" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Password <span style="color: #ef4444;">*</span></label>
                        <input type="password" name="password" required placeholder="Enter strong password" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('newAdminModal')">Cancel</button>
                    <button type="submit" class="btn-primary">Create Admin Account</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) { document.getElementById(id).classList.add('show'); }
        function closeModal(id) { document.getElementById(id).classList.remove('show'); }

        window.onclick = function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                e.target.classList.remove('show');
            }
        };
    </script>
</body>
</html>
