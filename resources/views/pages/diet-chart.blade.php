@extends('layouts.main')
@section('title', 'NutriBuddy — Personalized Diet Chart')

@php
    $basic = $plans['basic'] ?? null;
    $plus = $plans['plus'] ?? null;
    $pro = $plans['pro'] ?? null;
    $premium = $plans['premium'] ?? null;
@endphp

@section('content')
    <main>
        <section class="diet-page-hero">
            <div class="diet-page-hero-inner">
                <div class="diet-hero-copy">
                    <div class="diet-page-breadcrumb">
                        <a href="{{ route('home') }}">Home</a>
                        <span>/</span>
                        <span>Personalized Diet Chart</span>
                    </div>
                    <span class="diet-page-hero-badge">Free for Every Parent</span>
                    <h1 class="diet-page-hero-title">Get Your Child's Personalized Diet Chart</h1>
                    <p class="diet-page-hero-sub">Answer a few simple questions and get a nutrition plan shaped around your
                        child's age, goals, diet preference, and daily routine.</p>
                    <div class="diet-hero-actions">
                        <a class="diet-hero-btn" href="#diet-chart">Start Free Plan</a>
                        <a class="diet-hero-link" href="{{ route('product') }}">Shop Wellness Gummies</a>
                    </div>
                </div>

                <div class="diet-hero-visual" aria-hidden="true">
                    <div class="diet-visual-top">
                        <div class="diet-visual-icon">🥣</div>
                        <div>
                            <div class="diet-visual-title">Smart Meal Planner</div>
                            <div class="diet-visual-sub">2-day preview now, full 7-day plan after unlock</div>
                        </div>
                    </div>
                    <div class="diet-visual-list">
                        <div class="diet-visual-item">
                            <div class="diet-visual-dot">1</div>
                            <div class="diet-visual-copy">
                                <strong>Child profile</strong>
                                <span>Age, gender, height and weight for better personalization.</span>
                            </div>
                        </div>
                        <div class="diet-visual-item">
                            <div class="diet-visual-dot">2</div>
                            <div class="diet-visual-copy">
                                <strong>Health goals</strong>
                                <span>Focus on immunity, growth, digestion, sleep or picky eating.</span>
                            </div>
                        </div>
                        <div class="diet-visual-item">
                            <div class="diet-visual-dot">3</div>
                            <div class="diet-visual-copy">
                                <strong>Meal plan</strong>
                                <span>Daily meals, nutrition targets, tips and supplement suggestions.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- STATS -->
        <div class="stats-bar">
            <div class="stat">
                <div class="stat-num">50<span>K+</span></div>
                <div class="stat-label">Happy Kids</div>
            </div>
            <div class="stat">
                <div class="stat-num">4</div>
                <div class="stat-label">India Zones Covered</div>
            </div>
            <div class="stat">
                <div class="stat-num">14</div>
                <div class="stat-label">Age-Specific Plans</div>
            </div>
            <div class="stat">
                <div class="stat-num">98<span>%</span></div>
                <div class="stat-label">Parent Satisfaction</div>
            </div>
            <div class="stat">
                <div class="stat-num">12<span>+</span></div>
                <div class="stat-label">Pediatricians on Team</div>
            </div>
        </div>
        <!-- HOW IT WORKS -->
        <section class="how" id="how">
            <div class="text-center">
                <div class="section-label">Simple Process</div>
                <div class="section-title">How KidzNourish Works</div>
                <div class="section-sub">From onboarding to daily meal tips — everything is automated and science-backed.
                </div>
            </div>
            <div class="steps">
                <div class="step">
                    <div class="step-num" style="background:#e8f5e9;color:var(--green)">1</div>
                    <div class="step-icon">📝</div>
                    <h3>Build Your Child's Profile</h3>
                    <p>Enter age, weight, height, food preferences, allergies, and your zone of India. Takes under 3
                        minutes.</p>
                </div>
                <div class="step-arrow">→</div>
                <div class="step">
                    <div class="step-num" style="background:#fff3e0;color:var(--orange)">2</div>
                    <div class="step-icon">🧬</div>
                    <h3>AI + Science Creates the Plan</h3>
                    <p>Our algorithm combines paediatric nutrition science, regional food data, and growth milestones to
                        craft a personalised plan.</p>
                </div>
                <div class="step-arrow">→</div>
                <div class="step">
                    <div class="step-num" style="background:#f3e5f5;color:var(--purple)">3</div>
                    <div class="step-icon">📲</div>
                    <h3>Receive Daily Tips & Plans</h3>
                    <p>Get daily WhatsApp/app nudges, weekly meal plans, and monthly growth-nutrition reports — all
                        personalized.</p>
                </div>
                <div class="step-arrow">→</div>
                <div class="step">
                    <div class="step-num" style="background:#e3f2fd;color:var(--blue)">4</div>
                    <div class="step-icon">📈</div>
                    <h3>Track Growth & Adjust</h3>
                    <p>Log your child's growth monthly. Our system auto-adjusts the plan as they grow — from toddler to
                        teen.</p>
                </div>
            </div>
        </section>
        <!-- UNIQUE FEATURES -->
        <section id="features">
            <div class="text-center">
                <div class="section-label">What Sets Us Apart</div>
                <div class="section-title">Features You Won't Find Anywhere Else</div>
                <div class="section-sub">We built KidzNourish specifically for Indian children and Indian kitchens.</div>
            </div>
            <div class="features-grid">
                <div class="feature-card fc-1">
                    <div class="feature-icon">🗺️</div>
                    <h3>Zone-Smart™ Algorithm</h3>
                    <p>Recommendations use foods actually available and culturally accepted in your region — dal in the
                        North, rice in the South, fish in the East, bajra in the West.</p>
                    <span class="tag">🏆 Unique to KidzNourish</span>
                </div>
                <div class="feature-card fc-2">
                    <div class="feature-icon">📊</div>
                    <h3>Growth Percentile Tracker</h3>
                    <p>Plot your child's height & weight on WHO/IAP growth charts. The plan automatically recalibrates when
                        your child hits a new growth milestone.</p>
                    <span class="tag">Science-Based</span>
                </div>
                <div class="feature-card fc-3">
                    <div class="feature-icon">🌦️</div>
                    <h3>Seasonal Sync</h3>
                    <p>Summer plans are cooling (watermelon, cucumber, lassi). Winter plans are warming (methi, gur, til).
                        Every season, your child's plan changes automatically.</p>
                    <span class="tag">Ayurveda Inspired</span>
                </div>
                <div class="feature-card fc-4">
                    <div class="feature-icon">🎒</div>
                    <h3>Lunchbox Planner</h3>
                    <p>Every school-age child gets a weekly lunchbox schedule — nutritionally balanced, zero repetition, and
                        actually kid-friendly.</p>
                    <span class="tag">School-Ready</span>
                </div>
                <div class="feature-card fc-5">
                    <div class="feature-icon">⚠️</div>
                    <h3>Deficiency Alert System</h3>
                    <p>Parents log symptoms (pale gums, poor focus, frequent colds). Our system flags likely deficiencies
                        and adjusts the diet immediately — no lab test needed.</p>
                    <span class="tag">Early Warning</span>
                </div>
                <div class="feature-card fc-6">
                    <div class="feature-icon">👩‍⚕️</div>
                    <h3>Pediatrician-Reviewed Plans</h3>
                    <p>Every plan template is reviewed by AIIMS-trained pediatric nutritionists. You're not getting generic
                        internet advice — you're getting clinical-grade guidance.</p>
                    <span class="tag">Medical Grade</span>
                </div>
                <div class="feature-card fc-7">
                    <div class="feature-icon">📲</div>
                    <h3>Daily Parent Nudges</h3>
                    <p>Every morning, a WhatsApp message tells you exactly what to give your child today, why it matters,
                        and a quick recipe — in under 30 seconds to read.</p>
                    <span class="tag">Daily Habit</span>
                </div>
                <div class="feature-card fc-8">
                    <div class="feature-icon">🥄</div>
                    <h3>Taste Profile Engine</h3>
                    <p>Kids hate bitter gourd? Love curd? We know. The engine suggests nutritionally equivalent swaps so
                        your child gets the nutrients without the dinnertime battles.</p>
                    <span class="tag">Picky-Eater Friendly</span>
                </div>
            </div>
        </section>
        <!-- INDIA ZONES -->
        <section class="zones-section" id="zones">
            <div class="text-center">
                <div class="section-label">Regional Intelligence</div>
                <div class="section-title">Built for All 4 Zones of India</div>
                <div class="section-sub">Every zone has different staple foods, agricultural cycles, and culinary
                    traditions. We respect and leverage all of them.</div>
            </div>
            <div class="zones-grid">
                <div class="zone-card zc-1">
                    <div class="zone-flag">🌾</div>
                    <div class="zone-name">North India</div>
                    <div class="zone-states">Punjab · UP · Delhi · Haryana · Rajasthan · Himachal</div>
                    <div class="zone-foods">
                        <span class="food-chip">Roti/Paratha</span>
                        <span class="food-chip">Paneer</span>
                        <span class="food-chip">Dal</span>
                        <span class="food-chip">Ghee</span>
                        <span class="food-chip">Sarson Saag</span>
                        <span class="food-chip">Lassi</span>
                    </div>
                    <div class="zone-insight">🧠 North Indian kids often need extra iron (heavy wheat diets) and Vitamin D
                        (limited sunlight in winters). Plans emphasise iron-rich greens and fortified dairy.</div>
                </div>
                <div class="zone-card zc-2">
                    <div class="zone-flag">🥥</div>
                    <div class="zone-name">South India</div>
                    <div class="zone-states">Tamil Nadu · Kerala · Karnataka · Andhra · Telangana</div>
                    <div class="zone-foods">
                        <span class="food-chip">Idli/Dosa</span>
                        <span class="food-chip">Sambar</span>
                        <span class="food-chip">Rice</span>
                        <span class="food-chip">Coconut</span>
                        <span class="food-chip">Curry Leaves</span>
                        <span class="food-chip">Tamarind</span>
                    </div>
                    <div class="zone-insight">🧠 Fermented foods (idli/dosa) are naturally probiotic — great for gut health.
                        Plans build on this strength and ensure adequate calcium despite lower dairy use.</div>
                </div>
                <div class="zone-card zc-3">
                    <div class="zone-flag">🐟</div>
                    <div class="zone-name">East India</div>
                    <div class="zone-states">West Bengal · Odisha · Bihar · Jharkhand · Assam · NE States</div>
                    <div class="zone-foods">
                        <span class="food-chip">Rice</span>
                        <span class="food-chip">Fish</span>
                        <span class="food-chip">Mustard Oil</span>
                        <span class="food-chip">Posto</span>
                        <span class="food-chip">Hilsa</span>
                        <span class="food-chip">Jaggery</span>
                    </div>
                    <div class="zone-insight">🧠 Fish-eating zones provide excellent DHA for brain development. Plans
                        maximise omega-3 from regional fish varieties and address iodine gaps in inland areas.</div>
                </div>
                <div class="zone-card zc-4">
                    <div class="zone-flag">🌻</div>
                    <div class="zone-name">West India</div>
                    <div class="zone-states">Maharashtra · Gujarat · Goa · Rajasthan · MP</div>
                    <div class="zone-foods">
                        <span class="food-chip">Bajra/Jowar</span>
                        <span class="food-chip">Groundnuts</span>
                        <span class="food-chip">Jaggery</span>
                        <span class="food-chip">Kokum</span>
                        <span class="food-chip">Shrikhand</span>
                        <span class="food-chip">Dhokla</span>
                    </div>
                    <div class="zone-insight">🧠 Millets like bajra and jowar are nutrient-dense and gluten-free. Plans
                        leverage these ancient grains for sustained energy and micronutrient density.</div>
                </div>
            </div>
        </section>

        <!-- PRICING SECTION -->
        <section class="pricing-section" id="pricing">
            <div class="text-center">
                <div class="section-label">Simple Pricing</div>
                <div class="section-title">Choose What Works for Your Family</div>
                <div class="section-sub">All plans include zone-smart personalisation, age-specific science, and daily
                    parent nudges.</div>
            </div>



            <!-- Pricing toggle hidden for demo -->
            <div style="text-align: center; margin-bottom: 40px; margin-top: 20px; display: none;">
                <div class="billing-toggle"
                    style="display: inline-flex; background: #f0f4f8; padding: 4px; border-radius: 30px;">
                    <button class="billing-btn active" data-cycle="monthly"
                        style="border:none; padding: 8px 24px; border-radius: 26px; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-weight: 600; color: var(--primary); cursor: pointer; transition: all 0.3s;">Monthly</button>
                    <button class="billing-btn" data-cycle="quarterly"
                        style="border:none; padding: 8px 24px; border-radius: 26px; background: transparent; font-weight: 600; color: #666; cursor: pointer; transition: all 0.3s;">Quarterly</button>
                    <button class="billing-btn" data-cycle="annually"
                        style="border:none; padding: 8px 24px; border-radius: 26px; background: transparent; font-weight: 600; color: #666; cursor: pointer; transition: all 0.3s;">Annually</button>
                </div>
            </div>

            <div class="plans">
                <!-- Plan 1 -->
                @if($basic)
                    <div class="plan-card pc-1">
                        <div class="plan-icon">🥉</div>
                        <div class="plan-name">{{ $basic->name }}</div>
                        <div class="plan-price" style="display: none;" data-monthly="{{ $basic->monthly_price }}"
                            data-quarterly="{{ $basic->quarterly_price }}" data-annually="{{ $basic->annual_price }}">
                            ₹{{ number_format((float) $basic->monthly_price) }}</div>
                        <div class="plan-period" style="display: none;">per month</div>
                        <ul class="plan-features">
                            <li><strong>12 Questions</strong> analyzed</li>
                            <li>Basic personalized diet chart</li>
                        </ul>
                        <button class="plan-btn pb-1" onclick="startQuestionnaire('{{ $basic->slug }}')">Start
                            {{ $basic->name }}</button>
                    </div>
                @endif

                <!-- Plan 2 -->
                @if($plus)
                    <div class="plan-card pc-2 popular">
                        <div class="popular-badge">⭐ POPULAR</div>
                        <div class="plan-icon">🥈</div>
                        <div class="plan-name">{{ $plus->name }}</div>
                        <div class="plan-price" style="display: none;" data-monthly="{{ $plus->monthly_price }}"
                            data-quarterly="{{ $plus->quarterly_price }}" data-annually="{{ $plus->annual_price }}">
                            ₹{{ number_format((float) $plus->monthly_price) }}</div>
                        <div class="plan-period" style="display: none;">per month</div>
                        <ul class="plan-features">
                            <li><strong>21 Questions</strong> (cumulative)</li>
                            <li>Everything in Basic, plus:</li>
                            <li>Lifestyle tracking</li>
                            <li>Actual eating habits</li>
                        </ul>
                        <button class="plan-btn pb-2" onclick="startQuestionnaire('{{ $plus->slug }}')">Start
                            {{ $plus->name }}</button>
                    </div>
                @endif

                <!-- Plan 3 -->
                @if($pro)
                    <div class="plan-card pc-3">
                        <div class="plan-icon">🥇</div>
                        <div class="plan-name">{{ $pro->name }}</div>
                        <div class="plan-price" style="display: none;" data-monthly="{{ $pro->monthly_price }}"
                            data-quarterly="{{ $pro->quarterly_price }}" data-annually="{{ $pro->annual_price }}">
                            ₹{{ number_format((float) $pro->monthly_price) }}</div>
                        <div class="plan-period" style="display: none;">per month</div>
                        <ul class="plan-features">
                            <li><strong>29 Questions</strong> (cumulative)</li>
                            <li>Everything in Plus, plus:</li>
                            <li>Health goals integration</li>
                            <li>Clinical screening</li>
                            <li>Gut & digestion check</li>
                            <li>Deficiency screening</li>
                        </ul>
                        <button class="plan-btn pb-3" onclick="startQuestionnaire('{{ $pro->slug }}')">Start
                            {{ $pro->name }}</button>
                    </div>
                @endif

                <!-- Plan 4 -->
                @if($premium)
                    <div class="plan-card pc-4">
                        <div class="plan-icon">🏆</div>
                        <div class="plan-name">{{ $premium->name }}</div>
                        <div class="plan-price" style="display: none;" data-monthly="{{ $premium->monthly_price }}"
                            data-quarterly="{{ $premium->quarterly_price }}" data-annually="{{ $premium->annual_price }}">
                            ₹{{ number_format((float) $premium->monthly_price) }}</div>
                        <div class="plan-period" style="display: none;">per month</div>
                        <ul class="plan-features">
                            <li><strong>37 Questions</strong> (cumulative)</li>
                            <li>Everything in Pro, plus:</li>
                            <li>Medical history & labs</li>
                            <li>Puberty tracking</li>
                            <li>Budget considerations</li>
                            <li>Monthly progress tracking</li>
                        </ul>
                        <button class="plan-btn pb-4" onclick="startQuestionnaire('{{ $premium->slug }}')">Start
                            {{ $premium->name }}</button>
                    </div>
                @endif
            </div>

            <p class="pricing-note">🔒 7-day free trial · No credit card required · Cancel anytime</p>
        </section>
        @if(!$hasActivePlan)
            <section class="diet-section" id="diet-chart">
                <div class="diet-section-inner">
                    <div class="diet-header">
                        <div>
                            <span class="sec-eye">Free for Every Parent</span>
                            <h2 class="sec-title">Build a <span class="acc">personalized</span> diet chart</h2>
                            <p class="sec-sub">A guided planner for parents who want practical meals, clearer nutrition targets,
                                and simple next steps without guesswork.</p>
                        </div>
                        <div class="diet-mini-stats">
                            <div class="diet-mini-stat">
                                <strong>4</strong>
                                <span>guided steps</span>
                            </div>
                            <div class="diet-mini-stat">
                                <strong>2</strong>
                                <span>free preview days</span>
                            </div>
                            <div class="diet-mini-stat">
                                <strong>7</strong>
                                <span>day full plan</span>
                            </div>
                        </div>
                    </div>

                    <div class="diet-planner-shell">
                        <aside class="diet-side-panel">
                            <h3>What you get</h3>
                            <p>Designed for busy parents, with clean steps and output that is easy to follow at home.</p>
                            <div class="diet-check-list">
                                <div class="diet-check-item"><span>✓</span>Meal ideas based on diet preference and goals</div>
                                <div class="diet-check-item"><span>✓</span>Daily nutrition targets for calories, protein and
                                    micronutrients</div>
                                <div class="diet-check-item"><span>✓</span>Parent tips and a matching NutriBuddy product
                                    suggestion</div>
                            </div>
                            <div class="diet-side-note">Tip: add height and weight if you know them. The plan still works
                                without them, but the summary becomes more accurate.</div>
                        </aside>

                        <div class="stepper-wrap">

                            <!-- Stepper Progress -->
                            <div class="stepper-progress">
                                <div class="sp-step active" id="sp1">
                                    <div class="sp-ball">1</div>
                                    <div class="sp-label">Child Info</div>
                                </div>
                                <div class="sp-line">
                                    <div class="sp-line-fill" id="line1"></div>
                                </div>
                                <div class="sp-step" id="sp2">
                                    <div class="sp-ball">2</div>
                                    <div class="sp-label">Health Goals</div>
                                </div>
                                <div class="sp-line">
                                    <div class="sp-line-fill" id="line2"></div>
                                </div>
                                <div class="sp-step" id="sp3">
                                    <div class="sp-ball">3</div>
                                    <div class="sp-label">Diet Type</div>
                                </div>
                                <div class="sp-line">
                                    <div class="sp-line-fill" id="line3"></div>
                                </div>
                                <div class="sp-step" id="sp4">
                                    <div class="sp-ball">4</div>
                                    <div class="sp-label">Your Plan</div>
                                </div>
                            </div>

                            <!-- Diet Card -->
                            <div class="diet-card">

                                <!-- STEP 1 -->
                                <div class="step-panel active" id="panel1">
                                    <div class="step-head">
                                        <span class="step-emoji">👶</span>
                                        <h3>Tell us about your child</h3>
                                        <p>Enter your child's name, select age, gender, and optional body measurements.</p>
                                    </div>

                                    <!-- Child Name -->
                                    <div class="name-input-wrap">
                                        <label class="name-input-label">👦 Child's Full Name</label>
                                        <input type="text" class="name-input-field" id="childNameInput"
                                            placeholder="e.g. Aarav Sharma" autocomplete="off">
                                        <div class="selection-error" id="nameError">⚠️ Please enter your child's name to
                                            continue.</div>
                                    </div>

                                    <!-- Age -->
                                    <div style="margin-bottom:24px">
                                        <div>
                                            Age Group
                                        </div>
                                        <div class="age-grid" id="ageGrid">
                                            <div class="age-card" data-age="2-3" onclick="dcSelectAge(this)">
                                                <span class="age-emoji">👶</span>
                                                <span class="age-range">2–3 yrs</span>
                                                <span class="age-label">Toddler</span>
                                            </div>
                                            <div class="age-card" data-age="4-6" onclick="dcSelectAge(this)">
                                                <span class="age-emoji">🧒</span>
                                                <span class="age-range">4–6 yrs</span>
                                                <span class="age-label">Pre-School</span>
                                            </div>
                                            <div class="age-card" data-age="7-9" onclick="dcSelectAge(this)">
                                                <span class="age-emoji">🧑</span>
                                                <span class="age-range">7–9 yrs</span>
                                                <span class="age-label">Primary</span>
                                            </div>
                                            <div class="age-card" data-age="10-12" onclick="dcSelectAge(this)">
                                                <span class="age-emoji">🧑</span>
                                                <span class="age-range">10–12 yrs</span>
                                                <span class="age-label">Middle</span>
                                            </div>
                                            <div class="age-card" data-age="13-14" onclick="dcSelectAge(this)">
                                                <span class="age-emoji">🧑</span>
                                                <span class="age-range">13–14 yrs</span>
                                                <span class="age-label">Teen</span>
                                            </div>
                                        </div>
                                        <div class="selection-error" id="ageError">⚠️ Please select an age group to continue.
                                        </div>
                                    </div>

                                    <!-- Gender -->
                                    <div style="margin-bottom:24px">
                                        <div>
                                            Gender
                                        </div>
                                        <div class="gender-row" id="genderRow">
                                            <div class="gender-card" data-gender="boy" onclick="dcSelectGender(this)">
                                                <span class="gender-emoji">👦</span>
                                                <div class="gender-name">Boy</div>
                                            </div>
                                            <div class="gender-card" data-gender="girl" onclick="dcSelectGender(this)">
                                                <span class="gender-emoji">👧</span>
                                                <div class="gender-name">Girl</div>
                                            </div>
                                            <div class="gender-card" data-gender="other" onclick="dcSelectGender(this)">
                                                <span class="gender-emoji">🧒</span>
                                                <div class="gender-name">Prefer not to say</div>
                                            </div>
                                        </div>
                                        <div class="selection-error" id="genderError">⚠️ Please select a gender to continue.
                                        </div>
                                    </div>

                                    <!-- Height & Weight -->
                                    <div>
                                        <div>
                                            Body Measurements
                                            <span
                                                style="font-size:.72rem;font-family:'Nunito',sans-serif;font-weight:700;color:#aaa;margin-left:6px">(Optional
                                                — improves accuracy)</span>
                                        </div>
                                        <div class="hw-row">
                                            <div class="hw-group">
                                                <label>📏 Height (cm)</label>
                                                <input type="number" id="heightInput" placeholder="e.g. 115" min="50" max="200">
                                            </div>
                                            <div class="hw-group">
                                                <label>⚖️ Weight (kg)</label>
                                                <input type="number" id="weightInput" placeholder="e.g. 22" min="5" max="100">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="step-nav">
                                        <div></div>
                                        <button class="btn-next" onclick="dcGoStep(2)">Continue → Health Goals</button>
                                    </div>
                                </div>

                                <!-- STEP 2 -->
                                <div class="step-panel" id="panel2">
                                    <div class="step-head">
                                        <span class="step-emoji">🎯</span>
                                        <h3>What are your goals for your child?</h3>
                                        <p>Select all that apply — we'll personalize the diet chart accordingly.</p>
                                    </div>
                                    <div class="problem-grid" id="problemGrid">
                                        <div class="prob-tag" data-prob="immunity" onclick="dcToggleProb(this)"><span
                                                class="prob-icon">🛡️</span>
                                            Boost Immunity</div>
                                        <div class="prob-tag" data-prob="growth" onclick="dcToggleProb(this)"><span
                                                class="prob-icon">📏</span>
                                            Height & Growth</div>
                                        <div class="prob-tag" data-prob="brain" onclick="dcToggleProb(this)"><span
                                                class="prob-icon">🧠</span>
                                            Brain & Focus</div>
                                        <div class="prob-tag" data-prob="weight" onclick="dcToggleProb(this)"><span
                                                class="prob-icon">⚖️</span>
                                            Healthy Weight</div>
                                        <div class="prob-tag" data-prob="energy" onclick="dcToggleProb(this)"><span
                                                class="prob-icon">⚡</span>
                                            More Energy</div>
                                        <div class="prob-tag" data-prob="sleep" onclick="dcToggleProb(this)"><span
                                                class="prob-icon">😴</span>
                                            Better Sleep</div>
                                        <div class="prob-tag" data-prob="digestion" onclick="dcToggleProb(this)"><span
                                                class="prob-icon"></span>
                                            Gut & Digestion</div>
                                        <div class="prob-tag" data-prob="bones" onclick="dcToggleProb(this)"><span
                                                class="prob-icon">💪</span>
                                            Strong Bones</div>
                                        <div class="prob-tag" data-prob="mood" onclick="dcToggleProb(this)"><span
                                                class="prob-icon">😊</span> Mood
                                            & Calm</div>
                                        <div class="prob-tag" data-prob="skin" onclick="dcToggleProb(this)"><span
                                                class="prob-icon">✨</span> Skin
                                            & Hair</div>
                                        <div class="prob-tag" data-prob="appetite" onclick="dcToggleProb(this)"><span
                                                class="prob-icon">🍽️</span>
                                            Picky Eater Fix</div>
                                        <div class="prob-tag" data-prob="exam" onclick="dcToggleProb(this)"><span
                                                class="prob-icon">📚</span> Exam
                                            Performance</div>
                                    </div>
                                    <div class="selection-error" id="probError">⚠️ Please select at least one health goal.</div>
                                    <div class="step-nav">
                                        <button class="btn-back" onclick="dcGoStep(1)">← Back</button>
                                        <button class="btn-next" onclick="dcGoStep(3)">Next → Diet Preferences</button>
                                    </div>
                                </div>

                                <!-- STEP 3 -->
                                <div class="step-panel" id="panel3">
                                    <div class="step-head">
                                        <span class="step-emoji"></span>
                                        <h3>Any food preferences or allergies?</h3>
                                        <p>This helps us build a plan your family will actually love and follow.</p>
                                    </div>
                                    <div style="margin-bottom:24px">
                                        <div>
                                            Diet Type
                                        </div>
                                        <div class="diet-pref-row" id="dietPrefRow">
                                            <div class="dpref" data-pref="vegetarian" onclick="dcSelectPref(this)"> Vegetarian
                                            </div>
                                            <div class="dpref" data-pref="vegan" onclick="dcSelectPref(this)">🌱 Vegan</div>
                                            <div class="dpref" data-pref="eggetarian" onclick="dcSelectPref(this)">🥚 Eggetarian
                                            </div>
                                            <div class="dpref" data-pref="non-veg" onclick="dcSelectPref(this)">🍗 Non-Veg</div>
                                        </div>
                                        <div class="selection-error" id="prefError">⚠️ Please select a diet preference.</div>
                                    </div>
                                    <div>
                                        <div>
                                            Known Allergies <span
                                                style="font-weight:400;font-size:.8rem;font-family:'DM Sans',sans-serif;color:#aaa">(Optional)</span>
                                        </div>
                                        <div class="allergy-row" id="allergyRow">
                                            <div class="atag" data-allergy="dairy" onclick="dcToggleAllergy(this)">🥛 Dairy
                                            </div>
                                            <div class="atag" data-allergy="gluten" onclick="dcToggleAllergy(this)">🌾 Gluten
                                            </div>
                                            <div class="atag" data-allergy="nuts" onclick="dcToggleAllergy(this)">🥜 Nuts</div>
                                            <div class="atag" data-allergy="soy" onclick="dcToggleAllergy(this)">🫘 Soy</div>
                                            <div class="atag" data-allergy="eggs" onclick="dcToggleAllergy(this)">🥚 Eggs</div>
                                            <div class="atag" data-allergy="none" onclick="dcToggleAllergy(this)">✅ No Allergies
                                            </div>
                                        </div>
                                    </div>
                                    <div class="step-nav">
                                        <button class="btn-back" onclick="dcGoStep(2)">← Back</button>
                                        <button class="btn-generate" onclick="dcGenerateChart()">✨ Generate My Diet
                                            Chart</button>
                                    </div>
                                </div>

                                <!-- Loading -->
                                <div class="loading-state" id="loadingState">
                                    <div class="loader-ring"></div>
                                    <h3>Crafting Your Child's Plan </h3>
                                    <p>Our Ayurvedic nutritionists are personalizing this just for you…</p>
                                    <div class="loading-facts">
                                        <span class="lfact">✅ FSSAI Certified Recipes</span>
                                        <span class="lfact">👩‍⚕️ Pediatrician Approved</span>
                                        <span class="lfact"> Ayurveda-Backed</span>
                                    </div>
                                </div>

                                <!-- Result -->
                                <div class="result-state" id="resultState">

                                    <div class="result-hero">
                                        <div class="result-badge">Personalized Diet Chart</div>
                                        <h3 id="resultTitle">Your Child's Nutrition Plan</h3>
                                        <p id="resultDesc"></p>
                                        <div id="bmiRow"></div>
                                        <div class="result-tags" id="resultTags"></div>
                                        <div style="margin-top:14px">
                                            <span class="free-preview-badge">🎁 Free Preview: Day 1 & Day 2</span>
                                        </div>
                                    </div>

                                    <div id="day1Section">
                                        <div
                                            style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:8px">
                                            <div>
                                                Daily Meal
                                                Plan</div>
                                        </div>
                                        <div class="day-pill">📅 Day 1 — Monday</div>
                                        <div class="meal-plan-grid" id="mealGrid1"></div>
                                    </div>

                                    <div id="day2Section">
                                        <div class="day-pill"
                                            style="background:linear-gradient(135deg,var(--sk),#0088bb);box-shadow:0 4px 14px rgba(0,191,255,.28)">
                                            📅 Day 2 — Tuesday</div>
                                        <div class="meal-plan-grid" id="mealGrid2"></div>
                                    </div>

                                    <div class="nutrients-section">
                                        <div class="nutrients-title">Daily Nutrition Targets</div>
                                        <div id="nutrientBars"></div>
                                    </div>

                                    <div class="product-rec" id="productRec"></div>

                                    <div>
                                        💡 Parent
                                        Tips</div>
                                    <div class="tips-grid" id="tipsGrid"></div>

                                    <!-- Lock Banner -->
                                    <div class="lock-banner" id="lockBanner">
                                        <span class="lock-icon-lg">🔒</span>
                                        <div class="lock-title">Unlock the Full 7-Day Plan</div>
                                        <div class="lock-sub">Get Day 3–7 personalized meal plans, weekly grocery list,
                                            supplement
                                            stack, and a
                                            downloadable PDF chart.</div>
                                        <div class="plan-cards-grid">
                                            @if($plus)
                                                <div class="plan-card" id="planBasic"
                                                    onclick="dcSelectPlan('basic', '{{ $plus->id }}')">
                                                    <div class="plan-name">{{ $plus->name }}</div>
                                                    <div class="plan-price">₹{{ number_format($plus->price) }} <span>/
                                                            one-time</span></div>
                                                    <div class="plan-features">
                                                        <div class="pf">Full 7-day meal plan</div>
                                                        <div class="pf">PDF download</div>
                                                        <div class="pf">Email delivery</div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if($premium)
                                                <div class="plan-card active" id="planPro"
                                                    onclick="dcSelectPlan('pro', '{{ $premium->id }}')">
                                                    <span class="plan-pop-badge">⭐ Most Popular</span>
                                                    <div class="plan-name">{{ $premium->name }}</div>
                                                    <div class="plan-price">₹{{ number_format($premium->price) }} <span>/
                                                            month</span></div>
                                                    <div class="plan-features">
                                                        <div class="pf">New plan every month</div>
                                                        <div class="pf">PDF downloads</div>
                                                        <div class="pf">Progress tracker</div>
                                                        <div class="pf">Priority support</div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <button class="btn-subscribe" onclick="dcOpenModal()">Subscribe & Unlock Full Plan
                                            →</button>
                                    </div>

                                    <!-- Success Banner -->
                                    <div class="success-banner" id="successBanner">
                                        <div style="font-size:3rem;margin-bottom:12px">🎉</div>
                                        <h3>Payment Successful! Your PDF is Ready.</h3>
                                        <p>The complete 7-day plan has been sent to your email. You can also download it
                                            directly below.
                                        </p>
                                        <button class="btn-generate" onclick="dcDownload()">⬇ Download PDF Chart</button>
                                    </div>

                                    <div class="result-actions">
                                        <button class="btn-restart" onclick="dcRestart()">↩ Make Another Plan</button>
                                    </div>

                                </div><!-- /result-state -->
                            </div><!-- /diet-card -->
                        </div><!-- /stepper-wrap -->
                    </div><!-- /diet-planner-shell -->
                </div><!-- /diet-section-inner -->
            </section>
        @else
            {{-- ── Enrolled Banner (shown when user already has an active plan) ── --}}
            <section id="diet-chart" style="padding: 60px 20px; text-align: center;">
                <div
                    style="max-width:560px; margin: 0 auto; background: linear-gradient(135deg,#f0faf4,#e8f9ee); border: 2px solid #a8e6c4; border-radius: 24px; padding: 48px 36px; box-shadow: 0 8px 32px rgba(39,169,110,.12);">
                    <div style="font-size: 3rem; margin-bottom: 16px;">🎉</div>
                    <h2
                        style="font-family:'Outfit',sans-serif; font-weight:800; font-size:1.7rem; color:#1a3a2a; margin:0 0 12px;">
                        You're Already Enrolled!</h2>
                    <p
                        style="font-family:'Nunito',sans-serif; color:#4a6a55; font-size:1rem; line-height:1.6; margin:0 0 28px;">
                        You have an active diet plan. Head to your child profile to view your personalized meal plan, track
                        progress, and access all features.</p>
                    <a href="{{ route('child-profile') }}"
                        style="display:inline-flex; align-items:center; gap:8px; background:linear-gradient(135deg,#27a96e,#1d8c58); color:#fff; font-family:'Outfit',sans-serif; font-weight:700; font-size:1rem; padding:14px 32px; border-radius:14px; text-decoration:none; box-shadow:0 4px 16px rgba(39,169,110,.35); transition: opacity .2s;">📊
                        Go to My Profile →</a>
                </div>
            </section>
        @endif


        <!-- ══ MODAL ══ -->
        <div class="modal-overlay" id="dcModalOverlay" onclick="dcCloseModalOutside(event)">
            <div class="modal-box">
                <button class="modal-close-btn" onclick="dcCloseModal()">✕</button>
                <h2 class="modal-h" id="dcModalTitle">Unlock Your 7-Day Plan </h2>
                <p class="modal-sub" id="dcModalSub">Enter your details to receive the complete personalized diet chart
                    via
                    email.</p>
                <div class="mfield">
                    <label>Parent's name</label>
                    <input type="text" id="dcName" placeholder="e.g. Priya Sharma">
                </div>
                <div class="mfield">
                    <label>Email address</label>
                    <input type="email" id="dcEmail" placeholder="hello@gmail.com">
                </div>
                <div class="mfield">
                    <label>Phone number</label>
                    <input type="tel" id="dcPhone" placeholder="+91 98765 43210">
                </div>
                <div class="order-summary-box">
                    <div
                        style="font-family:'Nunito',sans-serif;font-weight:800;font-size:.75rem;color:#888;text-transform:uppercase;letter-spacing:1px;margin-bottom:10px">
                        Order Summary</div>
                    <div class="os-row">
                        <span class="os-lbl" id="dcOrderItem">7-Day Diet Chart (Monthly)</span>
                        <span class="os-val" id="dcOrderPrice">₹199</span>
                    </div>
                </div>
                <button class="btn-generate" style="width:100%;justify-content:center;font-size:.95rem"
                    onclick="dcProcessPayment()">
                    Pay & Download Chart →
                </button>
                <div class="secure-note">🔒 Secure payment &nbsp;·&nbsp; Instant PDF delivery &nbsp;·&nbsp; Cancel anytime
                </div>
            </div>
        </div>
    </main>


    <!-- ══════════════════════════════════════════
                                             PARENT REVIEWS
                                        ══════════════════════════════════════════ -->
    @include('partials.parent-reviews')

    <!-- ══════════════════════════════════════════
                                             FAQ
                                        ══════════════════════════════════════════ -->
    @include('partials.faq-section')

    <!-- Razorpay & Cashfree SDK -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>
    <script>
        window.nutribuddyConfig = {
            paymentCreateRoute: '{{ route("diet.payment.create") }}',
            paymentVerifyRoute: '{{ route("diet.payment.verify") }}',
            csrfToken: '{{ csrf_token() }}',
            cashfreeEnv: '{{ config("services.cashfree.env") === "production" ? "production" : "sandbox" }}',
            razorpayKey: '{{ config("services.razorpay.key_id") }}'
        };
    </script>
    <script src="{{ asset('assets/js/pages/diet-chart-payment.js') }}"></script>
@endsection