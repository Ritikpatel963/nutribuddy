# -*- coding: utf-8 -*-
import re

html_path = 'e:/website-project/Nutribuddy/kidznourish_website.html'
blade_path = 'e:/website-project/Nutribuddy/resources/views/pages/diet-chart.blade.php'
css_path = 'e:/website-project/Nutribuddy/public/assets/css/frontendstyle.css'

pricing_html = '''
<!-- PRICING SECTION -->
<section class="pricing-section" id="pricing">
  <div class="text-center">
    <div class="section-label">Simple Pricing</div>
    <div class="section-title">Choose What Works for Your Family</div>
    <div class="section-sub">All plans include zone-smart personalisation, age-specific science, and daily parent nudges.</div>
  </div>
  
  <div class="billing-toggle">
    <span class="toggle-label">₹ INR</span>
    <div class="toggle-switch on" id="currToggle" onclick="toggleCurr()">
      <div class="toggle-knob"></div>
    </div>
    <span class="toggle-label">Save 20% Annually 🎉</span>
  </div>

  <div class="plans">
    <!-- Plan 1 -->
    <div class="plan-card pc-1">
      <div class="plan-icon">🗓️</div>
      <div class="plan-name">Weekly Plan</div>
      <div class="plan-price" id="price-week">₹299</div>
      <div class="plan-period">per week · billed weekly</div>
      <ul class="plan-features">
        <li>7-day personalised meal plan</li>
        <li>Daily WhatsApp tips & recipes</li>
        <li>Zone-smart food selection</li>
        <li>Age-specific nutrient focus</li>
        <li>Lunchbox ideas for school kids</li>
        <li>Allergen-safe recommendations</li>
      </ul>
      <button class="plan-btn pb-1">Start Weekly Plan</button>
    </div>

    <!-- Plan 2 -->
    <div class="plan-card pc-2 popular">
      <div class="popular-badge">⭐ MOST POPULAR</div>
      <div class="plan-icon">📅</div>
      <div class="plan-name">Monthly Plan</div>
      <div class="plan-price" id="price-month">₹799</div>
      <div class="plan-period">per month · billed monthly</div>
      <ul class="plan-features">
        <li>Everything in Weekly, plus:</li>
        <li>Monthly growth-nutrition report</li>
        <li>Pediatrician-reviewed adjustments</li>
        <li>Deficiency alert monitoring</li>
        <li>Seasonal sync updates</li>
        <li>Recipe cards with step-by-step</li>
        <li>Parent WhatsApp community</li>
      </ul>
      <button class="plan-btn pb-2">Start Monthly Plan</button>
    </div>

    <!-- Plan 3 -->
    <div class="plan-card pc-3">
      <div class="plan-icon">📆</div>
      <div class="plan-name">Tri-Monthly Plan</div>
      <div class="plan-price" id="price-quarter">₹1,999</div>
      <div class="plan-period">per quarter · billed every 3 months</div>
      <ul class="plan-features">
        <li>Everything in Monthly, plus:</li>
        <li>Quarterly growth assessment</li>
        <li>Video consult with nutritionist</li>
        <li>Detailed 90-day nutrition roadmap</li>
        <li>Blood report interpretation</li>
        <li>Exclusive webinars for parents</li>
        <li>Priority customer support</li>
      </ul>
      <button class="plan-btn pb-3">Start Quarterly Plan</button>
    </div>

    <!-- Plan 4 (Added as requested) -->
    <div class="plan-card pc-4">
      <div class="plan-icon">🏆</div>
      <div class="plan-name">Annual Plan</div>
      <div class="plan-price" id="price-annual">₹5,999</div>
      <div class="plan-period">per year · billed annually (Save 20%)</div>
      <ul class="plan-features">
        <li>Everything in Tri-Monthly, plus:</li>
        <li>Full year growth tracking</li>
        <li>2 Free Doctor Consultations</li>
        <li>Advanced Immunity Protocol</li>
        <li>VIP dedicated support line</li>
        <li>Free exclusive merchandise</li>
        <li>Cancel anytime guarantee</li>
      </ul>
      <button class="plan-btn pb-4">Start Annual Plan</button>
    </div>
  </div>
  
  <p class="pricing-note">🔒 7-day free trial · No credit card required · Cancel anytime</p>
</section>
'''

with open(blade_path, 'r', encoding='utf-8') as f:
    blade_content = f.read()

# Insert before diet-chart section
parts = blade_content.split('<section class="diet-section" id="diet-chart">')
new_blade = parts[0] + pricing_html + '<section class="diet-section" id="diet-chart">' + parts[1]

with open(blade_path, 'w', encoding='utf-8') as f:
    f.write(new_blade)

css_content = '''
/* --- Pricing Section --- */
.pricing-section { padding: 80px 20px; background: var(--cr); max-width: 1200px; margin: 0 auto; }
.pricing-section .text-center { text-align: center; margin-bottom: 40px; }
.pricing-section .section-label { color: var(--mn); font-weight: 700; text-transform: uppercase; letter-spacing: 2px; font-size: 0.85rem; margin-bottom: 10px; }
.pricing-section .section-title { font-family: 'Fredoka One', cursive; font-size: 2.8rem; color: var(--dk); margin-bottom: 15px; }
.pricing-section .section-sub { color: #666; font-size: 1.1rem; max-width: 600px; margin: 0 auto; }

.billing-toggle { display: flex; align-items: center; justify-content: center; gap: 15px; margin-bottom: 40px; }
.toggle-label { font-size: 0.95rem; font-weight: 700; color: var(--dk); }
.toggle-switch { width: 56px; height: 30px; background: #ddd; border-radius: 30px; position: relative; cursor: pointer; transition: 0.3s; }
.toggle-switch.on { background: var(--mn); }
.toggle-knob { width: 24px; height: 24px; background: #fff; border-radius: 50%; position: absolute; top: 3px; left: 3px; transition: 0.3s; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
.toggle-switch.on .toggle-knob { left: 29px; }

.plans { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; align-items: start; }
.pricing-section .plan-card { background: #fff; border-radius: 20px; padding: 40px 24px; text-align: center; position: relative; border: 2px solid var(--pkl); transition: transform 0.3s, box-shadow 0.3s; }
.pricing-section .plan-card:hover { transform: translateY(-8px); box-shadow: 0 15px 40px rgba(0,0,0,0.06); }
.pricing-section .plan-card.popular { border-color: var(--mn); border-width: 3px; transform: scale(1.03); background: var(--mn); color: #fff; }
.pricing-section .plan-card.popular:hover { transform: scale(1.03) translateY(-5px); }

.popular-badge { position: absolute; top: -15px; left: 50%; transform: translateX(-50%); background: var(--ye); color: var(--dk); font-weight: 800; font-size: 0.75rem; padding: 6px 16px; border-radius: 20px; white-space: nowrap; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
.pricing-section .plan-icon { font-size: 2.5rem; margin-bottom: 15px; }
.pricing-section .plan-name { font-family: 'Fredoka One', cursive; font-size: 1.4rem; color: var(--dk); margin-bottom: 10px; }
.pricing-section .plan-card.popular .plan-name { color: #fff; }

.pricing-section .plan-price { font-family: 'Fredoka One', cursive; font-size: 2.5rem; color: var(--dk); margin-bottom: 5px; }
.pricing-section .plan-card.popular .plan-price { color: #fff; }

.pricing-section .plan-period { font-size: 0.85rem; color: #888; margin-bottom: 25px; font-weight: 600; }
.pricing-section .plan-card.popular .plan-period { color: rgba(255,255,255,0.8); }

.pricing-section .plan-features { list-style: none; text-align: left; margin-bottom: 30px; }
.pricing-section .plan-features li { padding-left: 24px; position: relative; font-size: 0.9rem; margin-bottom: 12px; color: #555; line-height: 1.4; }
.pricing-section .plan-card.popular .plan-features li { color: #fff; }
.pricing-section .plan-features li::before { content: '✓'; position: absolute; left: 0; color: var(--mn); font-weight: bold; }
.pricing-section .plan-card.popular .plan-features li::before { color: var(--ye); }

.pricing-section .plan-btn { width: 100%; padding: 14px; border-radius: 50px; font-weight: 800; font-size: 0.95rem; border: none; cursor: pointer; transition: 0.3s; font-family: 'Nunito', sans-serif;}
.pb-1 { background: var(--cr); color: var(--dk); border: 2px solid var(--pkl); }
.pb-1:hover { background: var(--pkl); color: var(--pk); }

.pb-2 { background: var(--ye); color: var(--dk); }
.pb-2:hover { background: #e6c200; transform: translateY(-2px); }

.pb-3, .pb-4 { background: var(--cr); color: var(--dk); border: 2px solid var(--pkl); }
.pb-3:hover, .pb-4:hover { background: var(--pkl); color: var(--pk); }

.pricing-note { text-align: center; margin-top: 30px; color: #888; font-size: 0.88rem; font-weight: 600; }
'''

with open(css_path, 'a', encoding='utf-8') as f:
    f.write(css_content)

print("Pricing added")
