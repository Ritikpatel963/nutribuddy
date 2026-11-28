<style>
    .form-sections-toolbar {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }
    .accordion-card-header {
        cursor: pointer !important;
        user-select: none !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 16px !important;
        width: 100% !important;
        transition: background-color 0.15s ease, border-color 0.15s ease;
    }
    .accordion-card-header:hover {
        background-color: #f8fafc !important;
    }
    .accordion-header-left {
        flex: 1 1 auto;
        min-width: 0;
    }
    .card-collapse-toggle {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #f1f5f9;
        transition: background 0.2s ease, transform 0.2s ease;
        flex-shrink: 0 !important;
        margin-left: auto !important;
    }
    .accordion-card-header:hover .card-collapse-toggle {
        background: #e2e8f0;
    }
    .collapse-chevron {
        transition: transform 0.25s ease;
        font-size: 18px;
        color: #475569;
    }
    .card-is-collapsed {
        box-shadow: none !important;
        margin-bottom: 14px !important;
    }
    .card-is-collapsed .card-header {
        border-bottom: 0 !important;
        border-radius: 12px !important;
    }
    .card-is-collapsed .card-body {
        display: none !important;
    }
    /* When expanded, chevron rotates 180 degrees */
    .card-is-expanded .collapse-chevron {
        transform: rotate(180deg);
    }
</style>

<!-- SECTION ACCORDION CONTROLS BAR -->
<div class="form-sections-toolbar">
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-primary-50 text-primary-600 p-2 rounded-2 d-flex align-items-center justify-content-center">
            <iconify-icon icon="lucide:layers" class="fs-5"></iconify-icon>
        </span>
        <div>
            <span class="fw-bold text-dark d-block" style="font-size:14px;">Compact Section Mode</span>
            <small class="text-secondary">All sections are collapsed by default. Click any header to expand or edit.</small>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-outline-secondary btn-sm px-3 d-inline-flex align-items-center gap-1 radius-8" id="collapseAllCardsBtn">
            <iconify-icon icon="lucide:chevrons-up-down" style="font-size:15px;"></iconify-icon>
            <span>Collapse All</span>
        </button>
        <button type="button" class="btn btn-outline-primary btn-sm px-3 d-inline-flex align-items-center gap-1 radius-8" id="expandAllCardsBtn">
            <iconify-icon icon="lucide:maximize-2" style="font-size:14px;"></iconify-icon>
            <span>Expand All</span>
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('productForm') || document.querySelector('form');
    if (!form) return;

    // Find all top-level card containers in the form, excluding the sticky save footer card
    const cards = Array.from(form.querySelectorAll('.card')).filter(card => {
        return !card.classList.contains('sticky-bottom') && !card.closest('.sticky-bottom');
    });

    cards.forEach((card) => {
        const header = card.querySelector('.card-header');
        const body = card.querySelector('.card-body');
        if (!header || !body) return;

        // Enhance header for accordion layout
        header.classList.add('accordion-card-header');
        header.setAttribute('role', 'button');
        header.setAttribute('tabindex', '0');

        // Wrap existing header contents into left container to keep right toggle pinned
        if (!header.querySelector('.accordion-header-left')) {
            const leftWrapper = document.createElement('div');
            leftWrapper.className = 'accordion-header-left d-flex flex-wrap align-items-center justify-content-between gap-2 flex-grow-1';
            while (header.firstChild) {
                leftWrapper.appendChild(header.firstChild);
            }
            header.appendChild(leftWrapper);
        }

        // Append toggle chevron pinned to the far right
        if (!header.querySelector('.card-collapse-toggle')) {
            const toggleSpan = document.createElement('span');
            toggleSpan.className = 'card-collapse-toggle';
            toggleSpan.innerHTML = '<iconify-icon icon="lucide:chevron-down" class="collapse-chevron"></iconify-icon>';
            header.appendChild(toggleSpan);
        }

        // By default, CLOSE ALL cards unless it contains validation errors
        const hasError = card.querySelector('.text-danger, .is-invalid, [aria-invalid="true"]');
        if (hasError) {
            expandCard(card);
        } else {
            collapseCard(card);
        }

        // Click handler to toggle card
        header.addEventListener('click', function (e) {
            // Do not toggle if click originated from interactive elements inside the header
            if (e.target.closest('button, a, input, select, textarea, .btn, .upload-area')) {
                return;
            }
            toggleCard(card);
        });

        // Keyboard accessibility (Enter / Space)
        header.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                if (!e.target.closest('button, a, input, select, textarea, .btn, .upload-area')) {
                    e.preventDefault();
                    toggleCard(card);
                }
            }
        });
    });

    function toggleCard(card) {
        if (card.classList.contains('card-is-collapsed')) {
            expandCard(card);
        } else {
            collapseCard(card);
        }
    }

    function collapseCard(card) {
        card.classList.add('card-is-collapsed');
        card.classList.remove('card-is-expanded');
        const header = card.querySelector('.card-header');
        if (header) header.setAttribute('aria-expanded', 'false');
    }

    function expandCard(card) {
        card.classList.remove('card-is-collapsed');
        card.classList.add('card-is-expanded');
        const header = card.querySelector('.card-header');
        if (header) header.setAttribute('aria-expanded', 'true');
    }

    const collapseAllBtn = document.getElementById('collapseAllCardsBtn');
    const expandAllBtn = document.getElementById('expandAllCardsBtn');

    if (collapseAllBtn) {
        collapseAllBtn.addEventListener('click', function () {
            cards.forEach(card => {
                if (!card.querySelector('.text-danger, .is-invalid')) {
                    collapseCard(card);
                }
            });
        });
    }

    if (expandAllBtn) {
        expandAllBtn.addEventListener('click', function () {
            cards.forEach(card => expandCard(card));
        });
    }
});
</script>
