@php
    $defaultFaqs = [
        [
            'question' => 'Are NutriBuddy gummies safe for children?',
            'answer' => '<p>ABSOLUTELY. NutriBuddy gummies are made specifically for kids aged 2–14 years with age-appropriate dosages, third-party lab tested every batch, and reviewed by certified pediatricians. No harmful preservatives, no refined sugar, no artificial colours.</p>',
        ],
        [
            'question' => 'When will I start seeing results?',
            'answer' => '<p>Most parents notice small changes within 3–4 weeks, improved energy, or focus. For stronger immunity and long-term benefits, consistency for 2–3 months works best along with customized diet given by us.</p>',
        ],
        [
            'question' => 'Can my child take multiple products together?',
            'answer' => "<p>Yes! Our products are designed to complement each other beautifully. Take our personalized quiz to build the right supplement stack for your child's specific age, goals, and health needs.</p>",
        ],
        [
            'question' => 'My child is a picky eater. Will they like it?',
            'answer' => '<p>This is something parents worry about a lot. The good part our gummies are naturally flavoured and taste like a treat, so kids usually enjoy them without any struggle.</p>',
        ],
        [
            'question' => 'How do I get a personalized diet plan for my child?',
            'answer' => "<p>Just answer a few simple questions about your child's age, height, weight and daily eating habits. Based on the information provided, we'll generate a personalized diet chart designed to support healthy growth and everyday nutrition.</p>",
        ],
        [
            'question' => 'Is there a subscription option?',
            'answer' => '<p>Yes! Subscribe & Save gives you 20% off every order, free delivery, priority restocking during shortages, and exclusive member discounts. Cancel or pause anytime — no penalties whatsoever.</p>',
        ],
        [
            'question' => "What if my child doesn't like the taste?",
            'answer' => "<p>We offer a 30-day taste guarantee. If your child genuinely doesn't enjoy the flavor, we'll refund you completely — no questions asked. We're that confident. (But honestly, 98% of kids love it!)</p>",
        ],
    ];

    $items = isset($faqs) && is_array($faqs) && count($faqs) > 0 ? $faqs : $defaultFaqs;
    $title = isset($faqTitle) && trim((string)$faqTitle) !== '' ? $faqTitle : 'Parents <span class="acc">Ask</span> Us 🙋';
@endphp

<section class="faq-section reveal">
    <h2 class="sec-title">{!! $title !!}</h2>
    <div class="faq-list">
        @foreach($items as $faq)
            @if(!empty(trim($faq['question'] ?? '')) && !empty(trim($faq['answer'] ?? '')))
                <div class="faq-item {{ $loop->first ? 'open' : '' }}">
                    <button class="faq-q">{{ $faq['question'] }}<span class="faq-tog">+</span></button>
                    <div class="faq-ans">
                        @if(\Illuminate\Support\Str::startsWith(trim($faq['answer']), '<p>'))
                            {!! $faq['answer'] !!}
                        @else
                            <p>{!! nl2br(e($faq['answer'])) !!}</p>
                        @endif
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</section>
