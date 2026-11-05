<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Templates - PhishGuard Admin</title>
    <!-- Link to CSS -->
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
            <li><a href="<?= base_url('admin/templates') ?>" class="active"><ion-icon name="mail-outline" style="margin-right:10px"></ion-icon> <span>Email Templates</span></a></li>
            <li><a href="<?= base_url('admin/profile') ?>"><ion-icon name="shield-outline" style="margin-right:10px"></ion-icon> <span>Admin Users</span></a></li>
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
                <h2>Email Templates (Gophish Style)</h2>
                <p>Manage and customize simulated phishing email templates for employee awareness</p>
            </div>
            <button type="button" class="btn-primary" onclick="openNewModal()">
                <ion-icon name="add-outline" style="margin-right:5px; vertical-align:middle;"></ion-icon> New Template
            </button>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <div class="table-header">
                <h3>Available Email Templates</h3>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Template Name</th>
                        <th>Email Subject</th>
                        <th>Sender Name</th>
                        <th>Created Date</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($templates)): ?>
                        <tr><td colspan="5" style="text-align: center; color: #94a3b8; padding: 2rem;">No templates found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($templates as $t): ?>
                            <tr>
                                <td><strong><?= esc($t['name']) ?></strong></td>
                                <td><?= esc($t['subject']) ?></td>
                                <td><?= esc($t['sender_name']) ?></td>
                                <td style="font-size: 0.85rem; color: var(--text-muted);"><?= date('M d, Y', strtotime($t['created_at'])) ?></td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <button type="button" class="btn-secondary" style="padding: 4px 10px; font-size: 0.8rem;" 
                                            onclick="editTemplate(<?= $t['id'] ?>)" title="Edit Template">
                                        <ion-icon name="create-outline"></ion-icon> Edit
                                    </button>
                                    <button type="button" class="btn-secondary" style="padding: 4px 10px; font-size: 0.8rem;" 
                                            onclick="previewTemplate(<?= $t['id'] ?>)" title="Preview Template">
                                        <ion-icon name="eye-outline"></ion-icon> Preview
                                    </button>
                                    <a href="<?= base_url('admin/delete_template/' . $t['id']) ?>" class="btn-danger-outline" 
                                       onclick="return confirm('Delete this template?');" title="Delete Template">
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

    <!-- Template Modal (Create / Preview) -->
    <div id="templateModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <h3 id="modalTitle">New Email Template</h3>
                <button type="button" class="modal-close" onclick="closeModal('templateModal')">&times;</button>
            </div>
            <form action="<?= base_url('admin/save_template') ?>" method="post">
                <input type="hidden" name="id" id="templateId">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Template Name</label>
                        <input type="text" name="name" id="templateName" required placeholder="e.g. IT Security Password Expiry" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Email Subject</label>
                        <input type="text" name="subject" id="templateSubject" required placeholder="e.g. Action Required: Password Expiry" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Sender Display Name</label>
                        <input type="text" name="sender_name" id="templateSender" value="Ravindranath Yarlagadda" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Email HTML Body</label>
                        <textarea name="body_html" id="templateBody" rows="10" required class="form-control" style="font-family: monospace; font-size: 0.85rem;"></textarea>
                        <small style="color: var(--text-muted); margin-top: 4px; display: block;">
                            Supported Tags: <code>{{.FirstName}}</code>, <code>{{.LastName}}</code>, <code>{{.URL}}</code>, <code>{{.TrackingURL}}</code>
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('templateModal')">Cancel</button>
                    <button type="submit" class="btn-primary">Save Template</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Preview Modal -->
    <div id="previewModal" class="modal-overlay">
        <div class="modal-card" style="max-width: 750px;">
            <div class="modal-header">
                <h3 id="previewModalTitle">Template Preview</h3>
                <button type="button" class="modal-close" onclick="closeModal('previewModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div style="background: #f8fafc; border: 1px solid var(--border-color); padding: 10px 15px; border-radius: 8px; margin-bottom: 15px;">
                    <div><strong>Subject:</strong> <span id="previewSubject"></span></div>
                    <div><strong>Sender:</strong> <span id="previewSender"></span></div>
                </div>
                <div id="previewContent" style="border: 1px solid var(--border-color); padding: 20px; border-radius: 8px; background: #fff;"></div>
            </div>
            <div class="modal-footer" style="display: flex; justify-content: space-between;">
                <button type="button" class="btn-secondary" onclick="editFromPreview()" style="color: var(--primary-color);">
                    <ion-icon name="create-outline"></ion-icon> Edit This Template
                </button>
                <button type="button" class="btn-secondary" onclick="closeModal('previewModal')">Close</button>
            </div>
        </div>
    </div>

    <!-- Embedded Templates JSON Cache for safe client-side loading -->
    <script id="templatesData" type="application/json">
        <?= json_encode($templates, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>
    </script>

    <script>
        const templatesMap = {};
        try {
            const rawTemplates = JSON.parse(document.getElementById('templatesData').textContent || '[]');
            rawTemplates.forEach(t => { templatesMap[t.id] = t; });
        } catch (e) {
            console.error('Failed to parse templates JSON:', e);
        }

        let currentPreviewId = null;

        function openModal(id) { document.getElementById(id).classList.add('show'); }
        function closeModal(id) { document.getElementById(id).classList.remove('show'); }

        function openNewModal() {
            document.getElementById('templateId').value = '';
            document.getElementById('templateName').value = '';
            document.getElementById('templateSubject').value = '';
            document.getElementById('templateSender').value = 'Ravindranath Yarlagadda';
            document.getElementById('templateBody').value = '';
            document.getElementById('modalTitle').innerText = 'New Email Template';
            openModal('templateModal');
        }

        function editTemplate(id) {
            const tmpl = templatesMap[id];
            if (!tmpl) return;

            document.getElementById('templateId').value = tmpl.id;
            document.getElementById('templateName').value = tmpl.name;
            document.getElementById('templateSubject').value = tmpl.subject;
            document.getElementById('templateSender').value = tmpl.sender_name || 'Ravindranath Yarlagadda';
            document.getElementById('templateBody').value = tmpl.body_html;
            document.getElementById('modalTitle').innerText = 'Edit Email Template: ' + tmpl.name;
            openModal('templateModal');
        }

        function previewTemplate(id) {
            const tmpl = templatesMap[id];
            if (!tmpl) return;

            currentPreviewId = id;
            document.getElementById('previewModalTitle').innerText = 'Preview: ' + tmpl.name;
            document.getElementById('previewSubject').innerText = tmpl.subject;
            document.getElementById('previewSender').innerText = tmpl.sender_name || 'Ravindranath Yarlagadda';

            // Render placeholders with safe preview data (avoids triggering server tracking)
            let html = tmpl.body_html
                .replaceAll('{{.FirstName}}', 'John')
                .replaceAll('{{.LastName}}', 'Doe')
                .replaceAll('{{.URL}}', '#')
                .replaceAll('{{.TrackingURL}}', 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7')
                .replaceAll('{{.ReportURL}}', '#');

            document.getElementById('previewContent').innerHTML = html;
            openModal('previewModal');
        }

        function editFromPreview() {
            if (currentPreviewId) {
                closeModal('previewModal');
                editTemplate(currentPreviewId);
            }
        }

        // Close on background click
        window.onclick = function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                e.target.classList.remove('show');
            }
        };
    </script>
</body>
</html>

