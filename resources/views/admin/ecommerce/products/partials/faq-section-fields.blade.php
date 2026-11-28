@php
    $formProduct = $product ?? null;
    $faqTitle = old('faq_title', $formProduct?->faq_title ?? 'Parents <span class="acc">Ask</span> Us 🙋');
    $savedFaqs = $formProduct?->faqs;
    $initialFaqs = old('faqs', is_array($savedFaqs) && count($savedFaqs) > 0 ? $savedFaqs : []);

    $standardDefaultFaqs = [
        [
            'question' => 'Are NutriBuddy gummies safe for children?',
            'answer' => 'ABSOLUTELY. NutriBuddy gummies are made specifically for kids aged 2–14 years with age-appropriate dosages, third-party lab tested every batch, and reviewed by certified pediatricians. No harmful preservatives, no refined sugar, no artificial colours.',
        ],
        [
            'question' => 'When will I start seeing results?',
            'answer' => 'Most parents notice small changes within 3–4 weeks, improved energy, or focus. For stronger immunity and long-term benefits, consistency for 2–3 months works best along with customized diet given by us.',
        ],
        [
            'question' => 'Can my child take multiple products together?',
            'answer' => "Yes! Our products are designed to complement each other beautifully. Take our personalized quiz to build the right supplement stack for your child's specific age, goals, and health needs.",
        ],
        [
            'question' => 'My child is a picky eater. Will they like it?',
            'answer' => 'This is something parents worry about a lot. The good part our gummies are naturally flavoured and taste like a treat, so kids usually enjoy them without any struggle.',
        ],
        [
            'question' => 'How do I get a personalized diet plan for my child?',
            'answer' => "Just answer a few simple questions about your child's age, height, weight and daily eating habits. Based on the information provided, we'll generate a personalized diet chart designed to support healthy growth and everyday nutrition.",
        ],
        [
            'question' => 'Is there a subscription option?',
            'answer' => 'Yes! Subscribe & Save gives you 20% off every order, free delivery, priority restocking during shortages, and exclusive member discounts. Cancel or pause anytime — no penalties whatsoever.',
        ],
        [
            'question' => "What if my child doesn't like the taste?",
            'answer' => "We offer a 30-day taste guarantee. If your child genuinely doesn't enjoy the flavor, we'll refund you completely — no questions asked. We're that confident. (But honestly, 98% of kids love it!)",
        ],
    ];
@endphp

<style>
    .faq-admin-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
    }
    .faq-admin-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }
    .faq-admin-card .faq-ans-textarea {
        font-size: 14px;
        line-height: 1.6;
        min-height: 85px;
        resize: vertical;
    }
</style>

<div class="card border-0 radius-12 mb-24 shadow-sm">
    <div class="card-header bg-base border-bottom d-flex justify-content-between align-items-center py-16 px-24 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-warning-50 text-warning-600 p-2 rounded-2 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                <iconify-icon icon="lucide:help-circle" class="fs-4"></iconify-icon>
            </span>
            <div>
                <h5 class="card-title mb-0">Frequently Asked Questions (FAQ Section)</h5>
                <small class="text-secondary">Custom questions &amp; answers displayed on this product's detail page accordion. If left empty, standard parent FAQs will display.</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm px-3 d-inline-flex align-items-center gap-1" id="loadDefaultFaqsBtn">
                <iconify-icon icon="lucide:sparkles" style="font-size: 15px;"></iconify-icon> Load Default 7 FAQs
            </button>
            <button type="button" class="btn btn-primary-600 btn-sm px-3 d-inline-flex align-items-center gap-1" id="addFaqBtn">
                <iconify-icon icon="lucide:plus" style="font-size: 16px;"></iconify-icon> Add Question
            </button>
        </div>
    </div>

    <div class="card-body p-24">
        <!-- FAQ HEADING -->
        <div class="mb-24">
            <label class="form-label fw-bold">FAQ Section Heading (HTML allowed for styling)</label>
            <input type="text" name="faq_title" value="{{ $faqTitle }}" class="form-control" placeholder='Parents &lt;span class="acc"&gt;Ask&lt;/span&gt; Us 🙋'>
            <small class="text-muted">Use <code>&lt;span class="acc"&gt;Word&lt;/span&gt;</code> to highlight in pink accent.</small>
        </div>

        <div id="faqItemsContainer" class="d-flex flex-column gap-3">
            <!-- Dynamic FAQ cards injected here -->
        </div>

        <div id="noFaqItemsMsg" class="text-center py-5 text-muted bg-light-50 rounded border border-dashed" style="display: none;">
            <iconify-icon icon="lucide:help-circle" class="fs-1 opacity-25"></iconify-icon>
            <p class="mt-2 mb-2 small">No custom questions added yet. The storefront will show the 7 standard NutriBuddy FAQs.</p>
            <button type="button" class="btn btn-primary-600 btn-sm" onclick="document.getElementById('addFaqBtn').click()">
                <iconify-icon icon="lucide:plus" style="font-size:14px;"></iconify-icon> Add Custom Question
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const faqContainer = document.getElementById('faqItemsContainer');
    const noFaqMsg = document.getElementById('noFaqItemsMsg');
    const addFaqBtn = document.getElementById('addFaqBtn');
    const loadDefaultBtn = document.getElementById('loadDefaultFaqsBtn');

    let faqIndex = 0;

    const defaultFaqsList = @json($standardDefaultFaqs);
    const initialFaqsList = @json($initialFaqs);

    function syncFaqMsg() {
        if (faqContainer && noFaqMsg) {
            noFaqMsg.style.display = faqContainer.children.length > 0 ? 'none' : 'block';
        }
        reindexFaqCards();
    }

    function reindexFaqCards() {
        if (!faqContainer) return;
        const cards = faqContainer.querySelectorAll('.faq-admin-card');
        cards.forEach((card, idx) => {
            const badge = card.querySelector('.faq-number-badge');
            if (badge) {
                badge.textContent = `Q ${idx + 1}`;
            }
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function addFaqItem(question = '', answer = '') {
        const index = faqIndex++;
        const card = document.createElement('div');
        card.className = 'faq-admin-card p-20 bg-white shadow-xs';
        card.dataset.cardIndex = index;

        card.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-12">
                <div class="d-flex align-items-center gap-2 flex-grow-1 me-3">
                    <span class="badge bg-primary-50 text-primary-600 border px-2 py-1 fs-7 faq-number-badge fw-bold">Q</span>
                    <input type="text" name="faqs[${index}][question]" value="${escapeHtml(question)}"
                        class="form-control form-control-sm fw-bold border-1 text-dark"
                        placeholder="e.g. Are NutriBuddy gummies safe for children?" required>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <button type="button" class="btn btn-outline-secondary btn-sm p-1 px-2 move-faq-up-btn" title="Move Up">
                        <iconify-icon icon="lucide:arrow-up" style="font-size: 14px;"></iconify-icon>
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm p-1 px-2 move-faq-down-btn" title="Move Down">
                        <iconify-icon icon="lucide:arrow-down" style="font-size: 14px;"></iconify-icon>
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm p-1 px-2 delete-faq-btn" title="Delete Question">
                        <iconify-icon icon="lucide:trash-2" style="font-size: 14px;"></iconify-icon>
                    </button>
                </div>
            </div>
            <div>
                <label class="form-label text-muted small fw-semibold mb-1">Answer</label>
                <textarea name="faqs[${index}][answer]" class="form-control faq-ans-textarea" rows="2" placeholder="Write the answer here...">${escapeHtml(answer)}</textarea>
            </div>
        `;

        card.querySelector('.delete-faq-btn').addEventListener('click', function () {
            card.remove();
            syncFaqMsg();
        });

        card.querySelector('.move-faq-up-btn').addEventListener('click', function () {
            const prev = card.previousElementSibling;
            if (prev) {
                faqContainer.insertBefore(card, prev);
                reindexFaqCards();
            }
        });

        card.querySelector('.move-faq-down-btn').addEventListener('click', function () {
            const next = card.nextElementSibling;
            if (next) {
                faqContainer.insertBefore(next, card);
                reindexFaqCards();
            }
        });

        faqContainer.appendChild(card);
        syncFaqMsg();
    }

    if (addFaqBtn) {
        addFaqBtn.addEventListener('click', function () {
            addFaqItem('', '');
            const inputs = faqContainer.querySelectorAll('input[type="text"]');
            if (inputs.length > 0) {
                inputs[inputs.length - 1].focus();
            }
        });
    }

    if (loadDefaultBtn) {
        loadDefaultBtn.addEventListener('click', function () {
            if (faqContainer.children.length > 0 && !confirm('Load the 7 default questions? Existing customized questions will not be removed.')) {
                return;
            }
            defaultFaqsList.forEach(item => {
                // Strip paragraph tags if present for textarea editing
                const cleanAnswer = item.answer.replace(/<\/?p>/gi, '').trim();
                addFaqItem(item.question, cleanAnswer);
            });
        });
    }

    // Populate initial items
    if (Array.isArray(initialFaqsList) && initialFaqsList.length > 0) {
        initialFaqsList.forEach(item => {
            const q = typeof item === 'object' && item !== null ? (item.question || '') : '';
            const a = typeof item === 'object' && item !== null ? (item.answer || '') : '';
            const cleanA = a.replace(/<\/?p>/gi, '').trim();
            addFaqItem(q, cleanA);
        });
    } else {
        syncFaqMsg();
    }
});
</script>
