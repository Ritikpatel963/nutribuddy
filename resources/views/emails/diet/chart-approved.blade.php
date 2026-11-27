@extends('emails.layout')

@section('title', 'Your Child’s Diet Chart is Ready!')
@section('preheader', 'The certified pediatric nutritionist has reviewed and approved the personalized diet chart.')

@section('hero')
    <p style="margin: 0 0 10px; color: #c0306f; font-size: 11px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase;">
        CHART APPROVED &amp; READY 📑
    </p>
    <h1 class="hero-h1" style="margin: 0 auto 12px; color: #0d0020; font-family: 'Nunito', Arial, sans-serif; font-size: 28px; font-weight: 900; line-height: 1.25;">
        {{ $child?->name ?? 'Your Child' }}’s Diet Chart is Ready!
    </h1>
    <p style="margin: 0 auto; color: #555166; font-size: 15px; line-height: 1.6;">
        Approved and signed off by certified pediatric nutritionists.
    </p>
@endsection

@section('content')
    <p style="margin: 0 0 16px; font-size: 16px;">
        Hello <strong>{{ $customer?->name ?: 'Parent' }}</strong>,
    </p>
    <p style="margin: 0 0 20px; color: #555166; line-height: 1.75;">
        Exciting news! Our clinical nutrition team has completed the review of <strong>{{ $child?->name ?? 'your child' }}</strong>’s nutritional assessment. Your 7-day personalized meal plan, nutrient targets, and pediatrician tips are now live in your account.
    </p>

    <!-- NUTRITIONIST NOTES IF PRESENT -->
    @if($dietPlan->review_notes)
        <div style="background-color: #faf5ff; border-left: 4px solid #7c3aed; padding: 16px 20px; border-radius: 0 12px 12px 0; margin-bottom: 24px;">
            <div style="font-size: 12px; font-weight: 800; color: #5b21b6; text-transform: uppercase; margin-bottom: 6px;">
                👩‍⚕️ Notes from the Pediatric Dietitian:
            </div>
            <p style="margin: 0; color: #0d0020; font-size: 14px; line-height: 1.6; font-style: italic;">
                “{{ $dietPlan->review_notes }}”
            </p>
        </div>
    @endif

    <!-- HIGHLIGHTS CARD -->
    <div style="background-color: #fffbf5; border: 1px solid #f4e7ed; border-radius: 14px; padding: 20px; margin-bottom: 24px;">
        <div style="font-size: 13px; font-weight: 800; color: #c0306f; text-transform: uppercase; margin-bottom: 12px;">
            What’s Inside the Plan:
        </div>
        <ul style="margin: 0; padding-left: 20px; color: #555166; font-size: 14px; line-height: 1.8;">
            <li><strong>7-Day Meal Schedule:</strong> Breakfast, morning snack, lunch, evening snack, and dinner mapped to your family's culinary preferences.</li>
            <li><strong>Allergen-Safe &amp; Tailored:</strong> 100% filtered against any disclosed food dislikes and sensitivities.</li>
            <li><strong>ICMR Micronutrient Target Tracking:</strong> Calibrated for Protein, Calcium, Iron, Zinc, and Essential Vitamins.</li>
            <li><strong>Instant PDF Download:</strong> High-resolution exportable PDF for the kitchen or fridge.</li>
        </ul>
    </div>

    <!-- CTA -->
    <div style="text-align: center; margin: 30px 0 18px;">
        @if($child)
            <a href="{{ route('child.diet-chart.show', $child) }}" class="cta-btn" target="_blank">
                View &amp; Download Diet Chart &rarr;
            </a>
        @else
            <a href="{{ route('userdashboard') }}" class="cta-btn" target="_blank">
                View in Account Dashboard &rarr;
            </a>
        @endif
    </div>
@endsection
