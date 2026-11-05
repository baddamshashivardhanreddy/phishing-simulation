/**
 * Modern Custom Dialogs (Alert & Confirm) for PhishCall
 * Replaces ugly browser native alert() and confirm() popups with sleek, accessible UI modals.
 */

(function () {
    // Self-contained SVGs so icons render with zero CDN lag
    const DIALOG_ICONS = {
        danger: `<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>`,
        warning: `<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>`,
        success: `<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>`,
        info: `<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>`
    };

    // Inject Dialog DOM & Styles
    function ensureDialogDOM() {
        if (!document.getElementById('customDialogStyles')) {
            const style = document.createElement('style');
            style.id = 'customDialogStyles';
            style.textContent = `
                .custom-dialog-backdrop {
                    position: fixed;
                    top: 0; left: 0; right: 0; bottom: 0;
                    background: rgba(15, 23, 42, 0.68);
                    backdrop-filter: blur(6px);
                    -webkit-backdrop-filter: blur(6px);
                    display: none;
                    align-items: center;
                    justify-content: center;
                    z-index: 999999;
                    opacity: 0;
                    transition: opacity 0.22s ease-out;
                    padding: 1.5rem;
                    box-sizing: border-box;
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                }
                .custom-dialog-backdrop.show { opacity: 1; }
                .custom-dialog-card {
                    background: #ffffff;
                    border-radius: 16px;
                    box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.4), 0 0 0 1px rgba(15, 23, 42, 0.08);
                    width: 100%;
                    max-width: 440px;
                    padding: 26px 24px 22px 24px;
                    text-align: center;
                    transform: scale(0.92) translateY(12px);
                    transition: all 0.24s cubic-bezier(0.16, 1, 0.3, 1);
                    box-sizing: border-box;
                }
                .custom-dialog-backdrop.show .custom-dialog-card { transform: scale(1) translateY(0); }
                .custom-dialog-icon {
                    width: 60px;
                    height: 60px;
                    margin: 0 auto 16px auto;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    animation: dialogIconPop 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                }
                @keyframes dialogIconPop {
                    0% { transform: scale(0.6); opacity: 0; }
                    100% { transform: scale(1); opacity: 1; }
                }
                .custom-dialog-icon-danger { background: #fee2e2; color: #dc2626; border: 3px solid #fecaca; }
                .custom-dialog-icon-warning { background: #fef3c7; color: #d97706; border: 3px solid #fde68a; }
                .custom-dialog-icon-info { background: #e0f2fe; color: #0284c7; border: 3px solid #bae6fd; }
                .custom-dialog-icon-success { background: #dcfce7; color: #15803d; border: 3px solid #bbf7d0; }
                .custom-dialog-title {
                    font-size: 1.2rem;
                    font-weight: 700;
                    color: #0f172a;
                    margin: 0 0 8px 0;
                    line-height: 1.35;
                }
                .custom-dialog-text {
                    font-size: 0.92rem;
                    color: #475569;
                    line-height: 1.55;
                    margin: 0 0 22px 0;
                    word-break: break-word;
                }
                .custom-dialog-actions {
                    display: flex;
                    gap: 10px;
                    justify-content: flex-end;
                }
                .custom-dialog-actions.single-btn { justify-content: center; }
                .custom-dialog-btn {
                    flex: 1;
                    padding: 11px 18px;
                    border-radius: 9px;
                    font-size: 0.88rem;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.18s ease;
                    border: none;
                    outline: none;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    gap: 6px;
                }
                .custom-dialog-btn.btn-cancel {
                    background: #ffffff;
                    color: #475569;
                    border: 1px solid #cbd5e1;
                }
                .custom-dialog-btn.btn-cancel:hover {
                    background: #f1f5f9;
                    color: #0f172a;
                    border-color: #94a3b8;
                }
                .custom-dialog-btn.btn-confirm-danger {
                    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                    color: #ffffff;
                    box-shadow: 0 4px 14px rgba(220, 38, 38, 0.32);
                }
                .custom-dialog-btn.btn-confirm-danger:hover {
                    transform: translateY(-1px);
                    box-shadow: 0 6px 18px rgba(220, 38, 38, 0.45);
                    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
                }
                .custom-dialog-btn.btn-confirm-warning {
                    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                    color: #ffffff;
                    box-shadow: 0 4px 14px rgba(217, 119, 6, 0.32);
                }
                .custom-dialog-btn.btn-confirm-warning:hover {
                    transform: translateY(-1px);
                    box-shadow: 0 6px 18px rgba(217, 119, 6, 0.45);
                }
                .custom-dialog-btn.btn-confirm-info, .custom-dialog-btn.btn-confirm-primary {
                    background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
                    color: #ffffff;
                    box-shadow: 0 4px 14px rgba(79, 70, 229, 0.32);
                }
                .custom-dialog-btn.btn-confirm-info:hover, .custom-dialog-btn.btn-confirm-primary:hover {
                    transform: translateY(-1px);
                    box-shadow: 0 6px 18px rgba(79, 70, 229, 0.45);
                }
                .custom-dialog-btn.btn-confirm-success {
                    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                    color: #ffffff;
                    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.32);
                }
                .custom-dialog-btn.btn-confirm-success:hover {
                    transform: translateY(-1px);
                    box-shadow: 0 6px 18px rgba(16, 185, 129, 0.45);
                }
            `;
            document.head.appendChild(style);
        }

        if (document.getElementById('customDialogBackdrop')) {
            return;
        }

        const backdrop = document.createElement('div');
        backdrop.id = 'customDialogBackdrop';
        backdrop.className = 'custom-dialog-backdrop';
        backdrop.setAttribute('role', 'dialog');
        backdrop.setAttribute('aria-modal', 'true');

        backdrop.innerHTML = `
            <div class="custom-dialog-card" id="customDialogCard">
                <div class="custom-dialog-icon" id="customDialogIcon"></div>
                <div class="custom-dialog-content">
                    <h3 class="custom-dialog-title" id="customDialogTitle">Confirmation</h3>
                    <div class="custom-dialog-text" id="customDialogText">Are you sure?</div>
                </div>
                <div class="custom-dialog-actions" id="customDialogActions">
                    <button type="button" class="custom-dialog-btn btn-cancel" id="customDialogCancelBtn">Cancel</button>
                    <button type="button" class="custom-dialog-btn" id="customDialogConfirmBtn">Confirm</button>
                </div>
            </div>
        `;

        document.body.appendChild(backdrop);
    }

    /**
     * Show Modern Custom Confirmation Dialog
     * Returns Promise<boolean>
     */
    window.customConfirm = function (options = {}) {
        ensureDialogDOM();

        return new Promise((resolve) => {
            const backdrop = document.getElementById('customDialogBackdrop');
            const iconContainer = document.getElementById('customDialogIcon');
            const titleEl = document.getElementById('customDialogTitle');
            const textEl = document.getElementById('customDialogText');
            const cancelBtn = document.getElementById('customDialogCancelBtn');
            const confirmBtn = document.getElementById('customDialogConfirmBtn');
            const actions = document.getElementById('customDialogActions');

            const type = options.type || 'danger';
            const title = options.title || 'Are you sure?';
            const message = options.message || '';
            const confirmText = options.confirmBtn || (type === 'danger' ? 'Yes, Delete' : 'Confirm');
            const cancelText = options.cancelBtn || 'Cancel';

            titleEl.textContent = title;
            textEl.innerHTML = message;

            iconContainer.className = 'custom-dialog-icon custom-dialog-icon-' + type;
            iconContainer.innerHTML = DIALOG_ICONS[type] || DIALOG_ICONS.warning;

            cancelBtn.style.display = 'inline-flex';
            cancelBtn.textContent = cancelText;

            confirmBtn.className = 'custom-dialog-btn btn-confirm-' + type;
            confirmBtn.textContent = confirmText;
            actions.className = 'custom-dialog-actions';

            backdrop.style.display = 'flex';
            requestAnimationFrame(() => {
                backdrop.classList.add('show');
                confirmBtn.focus();
            });

            function cleanup() {
                backdrop.classList.remove('show');
                setTimeout(() => {
                    backdrop.style.display = 'none';
                }, 220);
                window.removeEventListener('keydown', handleKey);
                backdrop.removeEventListener('click', handleBackdropClick);
                cancelBtn.onclick = null;
                confirmBtn.onclick = null;
            }

            function handleKey(e) {
                if (e.key === 'Escape') {
                    cleanup();
                    resolve(false);
                } else if (e.key === 'Enter') {
                    cleanup();
                    resolve(true);
                }
            }

            function handleBackdropClick(e) {
                if (e.target === backdrop) {
                    cleanup();
                    resolve(false);
                }
            }

            cancelBtn.onclick = function () {
                cleanup();
                resolve(false);
            };

            confirmBtn.onclick = function () {
                cleanup();
                resolve(true);
            };

            window.addEventListener('keydown', handleKey);
            backdrop.addEventListener('click', handleBackdropClick);
        });
    };

    /**
     * Show Modern Custom Alert Dialog
     * Returns Promise<void>
     */
    window.customAlert = function (options = {}) {
        ensureDialogDOM();

        return new Promise((resolve) => {
            const backdrop = document.getElementById('customDialogBackdrop');
            const iconContainer = document.getElementById('customDialogIcon');
            const titleEl = document.getElementById('customDialogTitle');
            const textEl = document.getElementById('customDialogText');
            const cancelBtn = document.getElementById('customDialogCancelBtn');
            const confirmBtn = document.getElementById('customDialogConfirmBtn');
            const actions = document.getElementById('customDialogActions');

            const type = options.type || 'info';
            const title = options.title || 'Notice';
            const message = options.message || '';
            const btnText = options.btnText || 'Got it';

            titleEl.textContent = title;
            textEl.innerHTML = message;

            iconContainer.className = 'custom-dialog-icon custom-dialog-icon-' + type;
            iconContainer.innerHTML = DIALOG_ICONS[type] || DIALOG_ICONS.info;

            cancelBtn.style.display = 'none';
            confirmBtn.className = 'custom-dialog-btn btn-confirm-' + (type === 'danger' ? 'danger' : (type === 'success' ? 'success' : 'primary'));
            confirmBtn.textContent = btnText;
            actions.className = 'custom-dialog-actions single-btn';

            backdrop.style.display = 'flex';
            requestAnimationFrame(() => {
                backdrop.classList.add('show');
                confirmBtn.focus();
            });

            function cleanup() {
                backdrop.classList.remove('show');
                setTimeout(() => {
                    backdrop.style.display = 'none';
                }, 220);
                window.removeEventListener('keydown', handleKey);
                backdrop.removeEventListener('click', handleBackdropClick);
                confirmBtn.onclick = null;
            }

            function handleKey(e) {
                if (e.key === 'Escape' || e.key === 'Enter') {
                    cleanup();
                    resolve();
                }
            }

            function handleBackdropClick(e) {
                if (e.target === backdrop) {
                    cleanup();
                    resolve();
                }
            }

            confirmBtn.onclick = function () {
                cleanup();
                resolve();
            };

            window.addEventListener('keydown', handleKey);
            backdrop.addEventListener('click', handleBackdropClick);
        });
    };

    /**
     * Universal Click Helper for Inline Links and Buttons
     * Usage:
     *   onclick="return confirmAction(event, 'Permanently delete campaign...', { type: 'danger', title: '...' })"
     */
    window.confirmAction = function (event, message, options = {}) {
        if (event) {
            if (event.preventDefault) event.preventDefault();
            if (event.stopPropagation) event.stopPropagation();
        }

        const targetEl = event ? (event.currentTarget || event.target.closest('a') || event.target.closest('button')) : null;
        const targetUrl = targetEl ? (targetEl.href || targetEl.getAttribute('data-href')) : null;
        const form = targetEl ? targetEl.closest('form') : null;

        // Auto-detect tone if not explicitly provided
        let type = options.type;
        let title = options.title;
        let confirmBtn = options.confirmBtn;
        const lower = (message || '').toLowerCase();

        if (!type) {
            if (lower.includes('delete') || lower.includes('remove') || lower.includes('clear')) {
                type = 'danger';
            } else if (lower.includes('reset') || lower.includes('warning')) {
                type = 'warning';
            } else if (lower.includes('complete') || lower.includes('reported')) {
                type = 'info';
            } else {
                type = 'danger';
            }
        }

        if (!title) {
            if (lower.includes('delete campaign')) title = 'Delete Campaign?';
            else if (lower.includes('delete')) title = 'Confirm Deletion';
            else if (lower.includes('remove')) title = 'Remove Item?';
            else if (lower.includes('reset')) title = 'Reset Results?';
            else if (lower.includes('clear')) title = 'Clear All Data?';
            else if (lower.includes('complete')) title = 'Complete Campaign?';
            else if (lower.includes('reported')) title = 'Mark as Reported?';
            else title = 'Are you sure?';
        }

        if (!confirmBtn) {
            if (type === 'danger') confirmBtn = 'Yes, Delete';
            else if (type === 'warning') confirmBtn = 'Yes, Reset';
            else if (type === 'info') confirmBtn = 'Yes, Proceed';
            else confirmBtn = 'Confirm';
        }

        customConfirm({
            title: title,
            message: message,
            type: type,
            confirmBtn: confirmBtn,
            cancelBtn: options.cancelBtn || 'Cancel'
        }).then(confirmed => {
            if (confirmed) {
                if (options.onConfirm && typeof options.onConfirm === 'function') {
                    options.onConfirm();
                } else if (targetUrl && targetUrl !== '#' && !targetUrl.startsWith('javascript:')) {
                    window.location.href = targetUrl;
                } else if (form) {
                    form.submit();
                }
            }
        });

        return false;
    };

    // Override native alert() so legacy calls display the modern dialog
    window.alert = function (msg) {
        return customAlert({
            title: 'Notice',
            message: String(msg),
            type: 'info'
        });
    };

    // Auto-initialize on load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', ensureDialogDOM);
    } else {
        ensureDialogDOM();
    }
})();
