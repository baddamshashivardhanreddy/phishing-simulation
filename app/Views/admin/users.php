<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulation Users - PhishGuard Admin</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('icon.svg') ?>">
    <link rel="alternate icon" href="<?= base_url('favicon.ico') ?>">
    <!-- Stylesheet -->
    <link href="<?= base_url('assets/admin.css') ?>" rel="stylesheet">
    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
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
            <li><a href="<?= base_url('admin') ?>"><ion-icon name="grid-outline" style="margin-right:10px"></ion-icon> <span>Campaign Dashboard</span></a></li>
            <li><a href="<?= base_url('admin/campaigns') ?>"><ion-icon name="albums-outline" style="margin-right:10px"></ion-icon> <span>Campaigns</span></a></li>
            <li><a href="<?= base_url('admin/users') ?>" class="active"><ion-icon name="people-outline" style="margin-right:10px"></ion-icon> <span>Simulation Users</span></a></li>
            <li><a href="<?= base_url('admin/templates') ?>"><ion-icon name="mail-outline" style="margin-right:10px"></ion-icon> <span>Email Templates</span></a></li>
            <li><a href="<?= base_url('admin/form') ?>"><ion-icon name="newspaper-outline" style="margin-right:10px"></ion-icon> <span>Landing Form</span></a></li>
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
        
        <!-- Header -->
        <div class="header">
            <div>
                <h2>Simulation Users (Targets)</h2>
                <p>Manage employee target lists enrolled in phishing awareness campaigns</p>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="button" class="btn-primary" onclick="openNewUserModal()">
                    <ion-icon name="person-add-outline" style="margin-right: 5px; vertical-align: middle;"></ion-icon> Add New User
                </button>
                <button type="button" class="btn-secondary" onclick="openModal('importModal')">
                    <ion-icon name="cloud-upload-outline" style="margin-right: 5px; vertical-align: middle;"></ion-icon> Bulk Import (CSV)
                </button>
                <a href="<?= base_url('admin/sample_csv') ?>" class="btn-secondary" title="Download CSV template">
                    <ion-icon name="download-outline"></ion-icon> Sample CSV
                </a>
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

        <!-- Table Container -->
        <div class="table-container">
            <div class="table-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <div>
                    <h3>Enrolled Users (<?= count($users) ?>)</h3>
                    <small style="color: var(--text-muted)">All employees eligible for phishing awareness testing</small>
                </div>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <button type="button" class="btn-danger-outline" id="bulkDeleteBtn" style="display: none; padding: 6px 14px; font-size: 0.85rem;" onclick="confirmBulkDelete()">
                        <ion-icon name="trash-outline"></ion-icon> Delete Selected (<span id="selectedCount">0</span>)
                    </button>
                    <div class="search-box">
                        <ion-icon name="search-outline" style="color: var(--text-muted);"></ion-icon>
                        <input type="text" id="userSearch" placeholder="Search by name, email, role..." onkeyup="filterUsers()">
                    </div>
                </div>
            </div>

            <!-- Table of Simulation Users -->
            <div style="overflow-x: auto;">
                <table id="usersTable">
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">
                                <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAllUsers(this)" title="Select All Users" style="cursor: pointer; width: 16px; height: 16px;">
                            </th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email Address</th>
                            <th>Position / Department</th>
                            <th>Campaign Status</th>
                            <th>Last Activity</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr id="noUsersRow">
                                <td colspan="8" style="text-align: center; padding: 3rem; color: #94a3b8;">
                                    <ion-icon name="people-outline" style="font-size: 2.5rem; margin-bottom: 10px; display: block;"></ion-icon>
                                    No simulation users found.<br>
                                    <button type="button" class="btn-primary" style="margin-top: 15px;" onclick="openNewUserModal()">
                                        <ion-icon name="person-add-outline" style="margin-right: 5px;"></ion-icon> Add Your First User
                                    </button>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $u): ?>
                                <tr class="user-row">
                                    <td style="text-align: center;">
                                        <input type="checkbox" value="<?= $u['id'] ?>" class="user-select-check" onchange="updateSelectedUsersCount()" style="cursor: pointer; width: 16px; height: 16px;">
                                    </td>
                                    <td><strong><?= esc(!empty($u['first_name']) ? $u['first_name'] : '—') ?></strong></td>
                                    <td><?= esc(!empty($u['last_name']) ? $u['last_name'] : '—') ?></td>
                                    <td>
                                        <a href="mailto:<?= esc($u['email'] ?? '') ?>" style="color: var(--primary-color); text-decoration: none; font-weight: 500;">
                                            <?= esc($u['email'] ?? '') ?>
                                        </a>
                                    </td>
                                    <td><?= esc(!empty($u['position']) ? $u['position'] : (!empty($u['department']) ? $u['department'] : 'Staff')) ?></td>
                                    <td>
                                        <span class="badge <?= esc($u['badge_class']) ?>">
                                            <?= esc($u['computed_status']) ?>
                                        </span>
                                    </td>
                                    <td style="font-size: 0.85rem; color: var(--text-muted);">
                                        <?= !empty($u['last_activity']) ? esc($u['last_activity']) : '—' ?>
                                    </td>
                                    <td style="text-align: right;">
                                        <button type="button" class="btn-secondary" style="padding: 5px 12px; font-size: 0.82rem;" 
                                                onclick='openEditUserModal(<?= json_encode($u) ?>)' title="Edit user">
                                            <ion-icon name="create-outline"></ion-icon> Edit
                                        </button>
                                        <a href="<?= base_url('admin/users/delete/' . $u['id']) ?>" class="btn-danger-outline" 
                                           onclick="return confirmAction(event, 'Permanently delete user \'<?= esc($u['email'], 'js') ?>\' from the directory? This action cannot be undone.', { type: 'danger', title: 'Delete Directory User?', confirmBtn: 'Yes, Delete User' });" title="Delete user">
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
         MODAL 1: Add / Edit Simulation User
         ========================================================== -->
    <div id="userModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <h3 id="userModalTitle">Add Simulation User</h3>
                <button type="button" class="modal-close" onclick="closeModal('userModal')">&times;</button>
            </div>
            <form action="<?= base_url('admin/users/save') ?>" method="post">
                <input type="hidden" name="id" id="userId">
                <div class="modal-body">
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 1.25rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>First Name</label>
                            <input type="text" name="first_name" id="userFirstName" placeholder="e.g. John" class="form-control">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Last Name</label>
                            <input type="text" name="last_name" id="userLastName" placeholder="e.g. Doe" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Email Address <span style="color: #ef4444;">*</span></label>
                        <input type="email" name="email" id="userEmail" required placeholder="john.doe@company.com" class="form-control">
                        <small style="color: var(--text-muted);">This is the recipient address for simulation awareness emails.</small>
                    </div>

                    <div class="form-group">
                        <label>Position / Department</label>
                        <input type="text" name="position" id="userPosition" placeholder="e.g. Financial Analyst / Accounts" class="form-control">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('userModal')">Cancel</button>
                    <button type="submit" class="btn-primary" id="userSubmitBtn">Save User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==========================================================
         MODAL 2: Bulk CSV Import
         ========================================================== -->
    <div id="importModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <h3>📥 Bulk Import Simulation Users (CSV)</h3>
                <button type="button" class="modal-close" onclick="closeModal('importModal')">&times;</button>
            </div>
            <form action="<?= base_url('admin/import_targets') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <p style="font-size: 0.9rem; color: #475569; margin-bottom: 1rem;">
                        Upload a CSV file or paste employee lines formatted as:
                        <code>First Name, Last Name, Email, Position</code>
                    </p>

                    <div style="margin-bottom: 1.25rem;">
                        <a href="<?= base_url('admin/sample_csv') ?>" class="btn-secondary" style="font-size: 0.85rem;">
                            <ion-icon name="download-outline"></ion-icon> Download Sample CSV Template
                        </a>
                    </div>

                    <div class="form-group">
                        <label>Upload CSV File (.csv)</label>
                        <input type="file" name="csv_file" accept=".csv, text/csv, text/plain" class="form-control" style="padding: 6px;">
                    </div>

                    <div style="text-align: center; margin: 15px 0; color: #94a3b8; font-weight: 600; font-size: 0.85rem;">— OR PASTE USERS —</div>

                    <div class="form-group">
                        <label>Paste CSV Lines</label>
                        <textarea name="csv_text" rows="5" class="form-control" placeholder="Alice,Johnson,alice@company.com,Finance Analyst&#10;Bob,Miller,bob@company.com,HR Specialist"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('importModal')">Cancel</button>
                    <button type="submit" class="btn-success">
                        <ion-icon name="cloud-upload-outline"></ion-icon> Import Users
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) { document.getElementById(id).classList.add('show'); }
        function closeModal(id) { document.getElementById(id).classList.remove('show'); }

        function openNewUserModal() {
            document.getElementById('userId').value = '';
            document.getElementById('userFirstName').value = '';
            document.getElementById('userLastName').value = '';
            document.getElementById('userEmail').value = '';
            document.getElementById('userPosition').value = '';
            document.getElementById('userModalTitle').innerText = 'Add Simulation User';
            document.getElementById('userSubmitBtn').innerText = 'Save User';
            openModal('userModal');
        }

        function openEditUserModal(user) {
            document.getElementById('userId').value = user.id;
            document.getElementById('userFirstName').value = user.first_name || '';
            document.getElementById('userLastName').value = user.last_name || '';
            document.getElementById('userEmail').value = user.email || '';
            document.getElementById('userPosition').value = user.position || (user.department || '');
            document.getElementById('userModalTitle').innerText = 'Edit Simulation User';
            document.getElementById('userSubmitBtn').innerText = 'Update User';
            openModal('userModal');
        }

        function filterUsers() {
            const input = document.getElementById('userSearch').value.toLowerCase();
            const rows = document.querySelectorAll('.user-row');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(input) ? '' : 'none';
            });
        }

        // Bulk Selection & Deletion for Users
        function toggleSelectAllUsers(master) {
            document.querySelectorAll('.user-select-check').forEach(cb => {
                const row = cb.closest('tr');
                if (row && row.style.display !== 'none') {
                    cb.checked = master.checked;
                }
            });
            updateSelectedUsersCount();
        }

        function updateSelectedUsersCount() {
            const checked = document.querySelectorAll('.user-select-check:checked');
            const count = checked.length;
            const btn = document.getElementById('bulkDeleteBtn');
            const counter = document.getElementById('selectedCount');
            if (counter) counter.innerText = count;
            if (btn) btn.style.display = count > 0 ? 'inline-flex' : 'none';
        }

        function confirmBulkDelete() {
            const checked = document.querySelectorAll('.user-select-check:checked');
            if (checked.length === 0) {
                customAlert({
                    title: 'No Users Selected',
                    message: 'Please select at least one user from the directory to delete.',
                    type: 'warning'
                });
                return;
            }
            customConfirm({
                title: 'Delete Directory Users?',
                message: 'Are you sure you want to permanently delete <strong>' + checked.length + '</strong> selected user(s) from the directory? This action cannot be undone.',
                type: 'danger',
                confirmBtn: 'Yes, Delete Users'
            }).then(confirmed => {
                if (!confirmed) return;
                const form = document.getElementById('bulkDeleteForm');
                form.innerHTML = '';
                checked.forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'user_ids[]';
                    input.value = cb.value;
                    form.appendChild(input);
                });
                form.submit();
            });
        }

        window.onclick = function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                e.target.classList.remove('show');
            }
        };
    </script>

    <!-- Hidden Bulk Delete Form -->
    <form id="bulkDeleteForm" action="<?= base_url('admin/users/bulk_delete') ?>" method="post" style="display: none;"></form>
</body>
</html>

