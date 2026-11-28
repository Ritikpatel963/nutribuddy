@php
    $currentProduct = $product ?? null;
    $accordionTabs = old('accordion_tabs');

    if ($accordionTabs === null) {
        if ($currentProduct && !empty($currentProduct->accordion_tabs)) {
            $accordionTabs = $currentProduct->accordion_tabs;
        } else {
            // Default 5 standard tabs matching the storefront PDP
            $descDefault = $currentProduct && !empty(trim((string)$currentProduct->description))
                ? $currentProduct->description
                : '<p>NutriBuddy brings delicious and scientifically formulated daily nutrition for growing kids.</p>';
            
            $howToUse = '<p>Follow the serving instructions printed on the product pack. For children, use under adult supervision.</p>';
            if ($currentProduct && $currentProduct->dosage) {
                $howToUse = '<p>' . e($currentProduct->dosage) . '</p>' . ($currentProduct->routine ? '<p>' . e($currentProduct->routine) . '</p>' : '');
            }

            $accordionTabs = [
                [
                    'title' => 'Product description',
                    'content' => $descDefault,
                ],
                [
                    'title' => 'What’s inside?',
                    'content' => '<p>Made with carefully selected premium ingredients and essential vitamins for your daily wellness routine.</p>',
                ],
                [
                    'title' => 'How do I use it?',
                    'content' => $howToUse,
                ],
                [
                    'title' => 'How does Subscribe & Save work?',
                    'content' => '<p>Choose a 2, 3, or 4 month plan for monthly delivery. Your first box is charged at the one-time price; the lower plan price applies from month 2. You can cancel anytime.</p>',
                ],
                [
                    'title' => 'What are the shipping and return policies?',
                    'content' => "<p>Free shipping is available on orders of ₹1,000 or more. Cash on delivery is available where serviceable. Delivery dates depend on your location.</p><p>For return or damage assistance, contact NutriBuddy support with your order details and product photos.</p>",
                ],
            ];
        }
    }
@endphp

<!-- CKEditor 5 Library for Accordion Descriptions -->
<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>

<style>
    .accordion-tab-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
    }
    .accordion-tab-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }
    .accordion-tab-card .ck-editor__editable_inline {
        min-height: 130px;
        font-size: 14px;
        line-height: 1.6;
        border-bottom-left-radius: 8px !important;
        border-bottom-right-radius: 8px !important;
    }
    .accordion-tab-card .ck.ck-toolbar {
        border-top-left-radius: 8px !important;
        border-top-right-radius: 8px !important;
        background: #f8fafc;
        border-color: #e2e8f0;
    }
    .accordion-tab-card .ck.ck-editor__main>.ck-editor__editable {
        background: #ffffff;
        border-color: #e2e8f0;
    }
</style>

<!-- ACCORDION DESCRIPTIONS -->
<div class="card border-0 radius-12 mb-24 shadow-sm">
    <div class="card-header bg-base border-bottom d-flex justify-content-between align-items-center py-16 px-24">
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-primary-50 text-primary-600 p-2 rounded-2 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                <iconify-icon icon="lucide:list-collapse" class="fs-4"></iconify-icon>
            </span>
            <div>
                <h5 class="card-title mb-0">Accordion Descriptions & Details</h5>
                <small class="text-secondary">Collapsible accordion sections with rich text editors. Add, format, rename, or remove tabs according to your need.</small>
            </div>
        </div>
        <button type="button" class="btn btn-primary-600 btn-sm px-3 d-inline-flex align-items-center gap-1" id="addAccordionTabBtn">
            <iconify-icon icon="lucide:plus" style="font-size: 16px;"></iconify-icon> Add Accordion Tab
        </button>
    </div>

    <div class="card-body p-24">
        <div id="accordionTabsContainer" class="d-flex flex-column gap-4">
            <!-- Dynamic accordion tab cards injected here -->
        </div>

        <div id="noAccordionTabsMsg" class="text-center py-5 text-muted bg-light-50 rounded border border-dashed" style="display: none;">
            <iconify-icon icon="lucide:list-collapse" class="fs-1 opacity-25"></iconify-icon>
            <p class="mt-2 mb-0 small">No accordion sections configured. Click <strong>Add Accordion Tab</strong> to add one.</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabsContainer = document.getElementById('accordionTabsContainer');
    const noTabsMsg = document.getElementById('noAccordionTabsMsg');
    const addTabBtn = document.getElementById('addAccordionTabBtn');

    let accordionTabIndex = 0;
    const tabEditors = new Map();

    function syncNoTabsMsg() {
        if (tabsContainer && noTabsMsg) {
            noTabsMsg.style.display = tabsContainer.children.length > 0 ? 'none' : 'block';
        }
    }

    function initCkEditor(textarea, initialContent = '') {
        if (!window.ClassicEditor) return Promise.resolve(null);

        return ClassicEditor.create(textarea, {
            toolbar: [
                'heading', '|',
                'bold', 'italic', 'link', '|',
                'bulletedList', 'numberedList', '|',
                'blockQuote', 'insertTable', '|',
                'undo', 'redo'
            ]
        }).then(editor => {
            if (initialContent) {
                editor.setData(initialContent);
            }
            editor.model.document.on('change:data', () => {
                textarea.value = editor.getData();
            });
            return editor;
        }).catch(err => {
            console.error('CKEditor initialization error:', err);
            return null;
        });
    }

    function addAccordionTab(title = '', content = '') {
        const index = accordionTabIndex++;
        const card = document.createElement('div');
        card.className = 'accordion-tab-card p-20 bg-white shadow-xs';
        card.dataset.cardIndex = index;

        card.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-16">
                <div class="d-flex align-items-center gap-2 flex-grow-1 me-3">
                    <span class="badge bg-light text-secondary border px-2 py-1 fs-7 tab-number-badge">Tab</span>
                    <input type="text" name="accordion_tabs[${index}][title]" value="${escapeHtml(title)}"
                        class="form-control form-control-sm fw-bold border-1 text-dark tab-title-input"
                        placeholder="Accordion Title (e.g. Product description, What's inside?)" required>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <button type="button" class="btn btn-outline-secondary btn-sm p-1 px-2 move-tab-up-btn" title="Move Up">
                        <iconify-icon icon="lucide:arrow-up" style="font-size: 14px;"></iconify-icon>
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm p-1 px-2 move-tab-down-btn" title="Move Down">
                        <iconify-icon icon="lucide:arrow-down" style="font-size: 14px;"></iconify-icon>
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm p-1 px-2 delete-tab-btn" title="Delete Tab">
                        <iconify-icon icon="lucide:trash-2" style="font-size: 14px;"></iconify-icon>
                    </button>
                </div>
            </div>
            <div>
                <label class="form-label text-muted small fw-semibold mb-1">Section Content (Rich Text)</label>
                <textarea name="accordion_tabs[${index}][content]" class="tab-content-textarea d-none">${escapeHtml(content)}</textarea>
                <div class="ckeditor-mount"></div>
            </div>
        `;

        const textarea = card.querySelector('.tab-content-textarea');
        const mount = card.querySelector('.ckeditor-mount');

        // Create mount textarea for CKEditor
        const editorTextarea = document.createElement('textarea');
        mount.appendChild(editorTextarea);

        initCkEditor(editorTextarea, content).then(editor => {
            if (editor) {
                tabEditors.set(card, { editor, textarea, editorTextarea });
                editor.model.document.on('change:data', () => {
                    textarea.value = editor.getData();
                });
            }
        });

        card.querySelector('.delete-tab-btn').addEventListener('click', function () {
            const entry = tabEditors.get(card);
            if (entry && entry.editor) {
                entry.editor.destroy().catch(() => {});
            }
            tabEditors.delete(card);
            card.remove();
            updateTabNumbers();
            syncNoTabsMsg();
        });

        // Swap cards smoothly without destroying CKEditor DOM instances
        card.querySelector('.move-tab-up-btn').addEventListener('click', function () {
            const prev = card.previousElementSibling;
            if (prev) {
                swapTabCards(card, prev);
            }
        });

        card.querySelector('.move-tab-down-btn').addEventListener('click', function () {
            const next = card.nextElementSibling;
            if (next) {
                swapTabCards(card, next);
            }
        });

        tabsContainer.appendChild(card);
        updateTabNumbers();
        syncNoTabsMsg();
    }

    function swapTabCards(cardA, cardB) {
        const titleA = cardA.querySelector('.tab-title-input');
        const titleB = cardB.querySelector('.tab-title-input');
        const entryA = tabEditors.get(cardA);
        const entryB = tabEditors.get(cardB);

        // Swap titles
        const tempTitle = titleA.value;
        titleA.value = titleB.value;
        titleB.value = tempTitle;

        // Swap contents
        if (entryA && entryB && entryA.editor && entryB.editor) {
            const contentA = entryA.editor.getData();
            const contentB = entryB.editor.getData();

            entryA.editor.setData(contentB);
            entryB.editor.setData(contentA);

            entryA.textarea.value = contentB;
            entryB.textarea.value = contentA;
        }
    }

    function updateTabNumbers() {
        if (!tabsContainer) return;
        const cards = tabsContainer.querySelectorAll('.accordion-tab-card');
        cards.forEach((c, idx) => {
            const badge = c.querySelector('.tab-number-badge');
            if (badge) {
                badge.textContent = '#' + (idx + 1);
            }
        });
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function (m) { return map[m]; });
    }

    if (addTabBtn) {
        addTabBtn.addEventListener('click', function () {
            addAccordionTab('', '');
        });
    }

    // Sync all editors to textareas before form submission
    const form = tabsContainer.closest('form');
    if (form) {
        form.addEventListener('submit', function () {
            tabEditors.forEach(({ editor, textarea }) => {
                if (editor && textarea) {
                    textarea.value = editor.getData();
                }
            });
        });
    }

    // Populate existing / default tabs
    const initialTabs = @json($accordionTabs);
    if (initialTabs && initialTabs.length > 0) {
        initialTabs.forEach(function (tab) {
            addAccordionTab(tab.title || '', tab.content || '');
        });
    } else {
        syncNoTabsMsg();
    }
});
</script>
