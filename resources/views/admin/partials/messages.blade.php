@if (session('success'))
    <div class="alert alert-success bg-success-50 text-success-700 border-success-200 alert-dismissible fade show radius-8 mb-20 d-flex align-items-center gap-2" role="alert">
        <iconify-icon icon="lucide:check-circle" class="fs-5 flex-shrink-0 text-success"></iconify-icon>
        <div class="flex-grow-1">{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger bg-danger-50 text-danger-700 border-danger-200 alert-dismissible fade show radius-8 mb-20 d-flex align-items-center gap-2" role="alert">
        <iconify-icon icon="lucide:alert-circle" class="fs-5 flex-shrink-0 text-danger"></iconify-icon>
        <div class="flex-grow-1">{{ session('error') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('warning'))
    <div class="alert alert-warning bg-warning-50 text-warning-700 border-warning-200 alert-dismissible fade show radius-8 mb-20 d-flex align-items-center gap-2" role="alert">
        <iconify-icon icon="lucide:alert-triangle" class="fs-5 flex-shrink-0 text-warning"></iconify-icon>
        <div class="flex-grow-1">{{ session('warning') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger bg-danger-50 text-danger-700 border-danger-200 alert-dismissible fade show radius-8 mb-20" role="alert">
        <div class="d-flex align-items-center gap-2 mb-2">
            <iconify-icon icon="lucide:alert-circle" class="fs-5 text-danger flex-shrink-0"></iconify-icon>
            <strong>Please resolve the following errors:</strong>
        </div>
        <ul class="mb-0 ps-4">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Admin Floating Toast Notification System -->
<div id="admin-toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 999999; pointer-events: none; max-width: 420px; width: 100%;"></div>

<style>
    .admin-nb-toast {
        pointer-events: auto;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12), 0 2px 8px rgba(0, 0, 0, 0.06);
        border: 1px solid #e2e8f0;
        margin-bottom: 12px;
        overflow: hidden;
        animation: adminToastSlideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        transition: all 0.3s ease;
    }
    .admin-nb-toast.toast-success { border-left: 5px solid #16a34a; }
    .admin-nb-toast.toast-error { border-left: 5px solid #dc2626; }
    .admin-nb-toast.toast-warning { border-left: 5px solid #f59e0b; }
    .admin-nb-toast.toast-info { border-left: 5px solid #0284c7; }
    .admin-nb-toast.hiding {
        opacity: 0;
        transform: translateX(50px);
    }
    @keyframes adminToastSlideIn {
        from { opacity: 0; transform: translateX(50px) scale(0.95); }
        to { opacity: 1; transform: translateX(0) scale(1); }
    }
</style>

<script>
    (function () {
        window.nbAdminToast = function (message, type = 'success', title = '') {
            const container = document.getElementById('admin-toast-container');
            if (!container) return;

            const typeConfig = {
                success: { title: 'Success', icon: '✓', color: '#16a34a', bg: '#f0fdf4' },
                error: { title: 'Error', icon: '✕', color: '#dc2626', bg: '#fef2f2' },
                warning: { title: 'Warning', icon: '!', color: '#f59e0b', bg: '#fffbeb' },
                info: { title: 'Notice', icon: 'i', color: '#0284c7', bg: '#f0f9ff' }
            };

            const config = typeConfig[type] || typeConfig.info;
            const toastEl = document.createElement('div');
            toastEl.className = `admin-nb-toast toast-${type} p-3 d-flex align-items-start gap-3`;
            toastEl.innerHTML = `
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 fw-bold" 
                     style="width: 28px; height: 28px; background: ${config.bg}; color: ${config.color}; border: 1px solid ${config.color}; font-size: 14px;">
                    ${config.icon}
                </div>
                <div class="flex-grow-1 min-w-0">
                    <div class="fw-bold text-dark font-sm mb-1">${title || config.title}</div>
                    <div class="text-secondary font-xs" style="line-height: 1.4;">${message}</div>
                </div>
                <button type="button" class="btn-close flex-shrink-0" style="font-size: 10px; margin-top: 2px;" aria-label="Close"></button>
            `;

            toastEl.querySelector('.btn-close').addEventListener('click', () => {
                toastEl.classList.add('hiding');
                setTimeout(() => toastEl.remove(), 300);
            });

            container.appendChild(toastEl);

            setTimeout(() => {
                if (toastEl.parentNode) {
                    toastEl.classList.add('hiding');
                    setTimeout(() => toastEl.remove(), 300);
                }
            }, 5000);
        };

        @if (session('success'))
            window.nbAdminToast(@json(session('success')), 'success');
        @endif
        @if (session('error'))
            window.nbAdminToast(@json(session('error')), 'error');
        @endif
        @if (session('warning'))
            window.nbAdminToast(@json(session('warning')), 'warning');
        @endif
        @if ($errors->any())
            window.nbAdminToast(@json($errors->first()), 'error', 'Validation Error');
        @endif
    })();
</script>
