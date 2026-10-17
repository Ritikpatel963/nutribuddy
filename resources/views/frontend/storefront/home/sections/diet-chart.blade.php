<section class="diet-section" id="diet-chart">

        <div class="diet-header">
            <span class="sec-eye">Free for Every Parent</span>
            <h2 class="sec-title">Get Your Child's <span class="acc">Personalized</span><br>Diet Chart </h2>
            <p class="sec-sub">Answer 4 quick questions — get a free 2-day expert diet plan. Subscribe to unlock the full
                7-day plan + PDF download.</p>
        </div>

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
                        <span class="step-emoji"></span>
                        <h3>Tell us about your child</h3>
                        <p>Select age, gender, and optional body measurements for a more accurate plan.</p>
                    </div>

                    <div style="margin-bottom:24px">
                        <div style="font-family:'Fredoka One',cursive;font-size:1rem;color:var(--dk);margin-bottom:14px">
                            Age Group
                        </div>
                        <div class="age-grid" id="ageGrid">
                            <div class="age-card" data-age="2-3" onclick="dcSelectAge(this)">
                                <span class="age-emoji"><img src="{{ asset('img/girl.png') }}" alt=""></span>
                                <span class="age-range">2–3 yrs</span>
                                <span class="age-label">Toddler</span>
                            </div>
                            <div class="age-card" data-age="4-6" onclick="dcSelectAge(this)">
                                <span class="age-emoji"><img src="{{ asset('img/b1.png') }}" alt=""></span>
                                <span class="age-range">4–6 yrs</span>
                                <span class="age-label">Pre-School</span>
                            </div>
                            <div class="age-card" data-age="7-9" onclick="dcSelectAge(this)">
                                <span class="age-emoji"><img src="{{ asset('img/b2.png') }}" alt=""></span>
                                <span class="age-range">7–9 yrs</span>
                                <span class="age-label">Primary School</span>
                            </div>
                            <div class="age-card" data-age="10-12" onclick="dcSelectAge(this)">
                                <span class="age-emoji"><img src="{{ asset('img/g1.png') }}" alt=""></span>
                                <span class="age-range">10–12 yrs</span>
                                <span class="age-label">Middle School</span>
                            </div>
                            <div class="age-card" data-age="13-14" onclick="dcSelectAge(this)">
                                <span class="age-emoji"><img src="{{ asset('img/b4.png') }}" alt=""></span>
                                <span class="age-range">13–14 yrs</span>
                                <span class="age-label">Teen</span>
                            </div>
                        </div>
                        <div class="selection-error" id="ageError">⚠️ Please select an age group to continue.</div>
                    </div>

                    <div style="margin-bottom:24px">
                        <div style="font-family:'Fredoka One',cursive;font-size:1rem;color:var(--dk);margin-bottom:14px">
                            Gender
                        </div>
                        <div class="gender-row" id="genderRow">
                            <div class="gender-card" data-gender="boy" onclick="dcSelectGender(this)">
                                <span class="gender-emoji"><img src="{{ asset('img/boy.png') }}" alt=""></span>
                                <div class="gender-name">Boy</div>
                            </div>
                            <div class="gender-card" data-gender="girl" onclick="dcSelectGender(this)">
                                <span class="gender-emoji"><img src="{{ asset('img/girl.png') }}" alt=""></span>
                                <div class="gender-name">Girl</div>
                            </div>
                            <!-- <div class="gender-card" data-gender="other" onclick="dcSelectGender(this)">
                                                                                                                                                                                    <span class="gender-emoji"><img src="{{ asset('img/boyn.png') }}" alt=""></span>
                                                                                                                                                                                    <div class="gender-name">Prefer not to say</div>
                                                                                                                                                                                </div> -->
                        </div>
                        <div class="selection-error" id="genderError">⚠️ Please select a gender to continue.</div>
                    </div>

                    <!-- NEW: Height & Weight -->
                    <div>
                        <div style="font-family:'Fredoka One',cursive;font-size:1rem;color:var(--dk);margin-bottom:6px">
                            Body Measurements
                            <span
                                style="font-size:.72rem;font-family:'Nunito',sans-serif;font-weight:700;color:#aaa;margin-left:6px">(Optional
                                — improves accuracy)</span>
                        </div>
                        <div class="hw-row">
                            <div class="hw-group">
                                <label>Height (cm)</label>
                                <input type="number" id="heightInput" placeholder="e.g. 115" min="50" max="200">
                            </div>
                            <div class="hw-group">
                                <label>Weight (kg)</label>
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
                        <p>Select all that apply — we'll personalize your diet chart accordingly.</p>
                    </div>
                    <div class="problem-grid" id="problemGrid">
                        <div class="prob-tag" data-prob="immunity" onclick="dcToggleProb(this)"><span
                                class="prob-icon">🛡️</span>
                            Boost Immunity</div>
                        <div class="prob-tag" data-prob="growth" onclick="dcToggleProb(this)"><span
                                class="prob-icon">📏</span>
                            Height & Growth</div>
                        <div class="prob-tag" data-prob="brain" onclick="dcToggleProb(this)"><span
                                class="prob-icon">🧠</span> Brain
                            & Focus</div>
                        <div class="prob-tag" data-prob="weight" onclick="dcToggleProb(this)"><span
                                class="prob-icon">⚖️</span>
                            Healthy Weight</div>
                        <div class="prob-tag" data-prob="energy" onclick="dcToggleProb(this)"><span
                                class="prob-icon">⚡</span> More
                            Energy</div>
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
                                class="prob-icon">😊</span> Mood &
                            Calm</div>
                        <div class="prob-tag" data-prob="skin" onclick="dcToggleProb(this)"><span class="prob-icon">✨</span>
                            Skin &
                            Hair</div>
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
                        <div style="font-family:'Fredoka One',cursive;font-size:1rem;color:var(--dk);margin-bottom:14px">
                            Diet Type
                        </div>
                        <div class="diet-pref-row" id="dietPrefRow">
                            <div class="dpref" data-pref="vegetarian" onclick="dcSelectPref(this)"> Vegetarian</div>
                            <div class="dpref" data-pref="vegan" onclick="dcSelectPref(this)">🌱 Vegan</div>
                            <div class="dpref" data-pref="eggetarian" onclick="dcSelectPref(this)">🥚 Eggetarian</div>
                            <div class="dpref" data-pref="non-veg" onclick="dcSelectPref(this)">🍗 Non-Veg</div>
                        </div>
                        <div class="selection-error" id="prefError">⚠️ Please select a diet preference.</div>
                    </div>
                    <div>
                        <div style="font-family:'Fredoka One',cursive;font-size:1rem;color:var(--dk);margin-bottom:8px">
                            Known Allergies <span
                                style="font-weight:400;font-size:.8rem;font-family:'DM Sans',sans-serif;color:#aaa">(Optional)</span>
                        </div>
                        <div class="allergy-row" id="allergyRow">
                            <div class="atag" data-allergy="dairy" onclick="dcToggleAllergy(this)">🥛 Dairy</div>
                            <div class="atag" data-allergy="gluten" onclick="dcToggleAllergy(this)">🌾 Gluten</div>
                            <div class="atag" data-allergy="nuts" onclick="dcToggleAllergy(this)">🥜 Nuts</div>
                            <div class="atag" data-allergy="soy" onclick="dcToggleAllergy(this)">🫘 Soy</div>
                            <div class="atag" data-allergy="eggs" onclick="dcToggleAllergy(this)">🥚 Eggs</div>
                            <div class="atag" data-allergy="none" onclick="dcToggleAllergy(this)">✅ No Allergies</div>
                        </div>
                    </div>
                    <div class="step-nav">
                        <button class="btn-back" onclick="dcGoStep(2)">← Back</button>
                        <button class="btn-generate" onclick="dcGenerateChart()">✨ Generate My Diet Chart</button>
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

                    <!-- Hero result card -->
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

                    <!-- Day 1 -->
                    <div id="day1Section">
                        <div
                            style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:8px">
                            <div style="font-family:'Fredoka One',cursive;font-size:1.2rem;color:var(--dk)">Daily Meal Plan
                            </div>
                        </div>
                        <div class="day-pill">📅 Day 1 — Monday</div>
                        <div class="meal-plan-grid" id="mealGrid1"></div>
                    </div>

                    <!-- Day 2 -->
                    <div id="day2Section">
                        <div class="day-pill"
                            style="background:linear-gradient(135deg,var(--sk),#0088bb);box-shadow:0 4px 14px rgba(0,191,255,.28)">
                            📅
                            Day 2 — Tuesday</div>
                        <div class="meal-plan-grid" id="mealGrid2"></div>
                    </div>

                    <!-- Nutrients -->
                    <div class="nutrients-section">
                        <div class="nutrients-title">Daily Nutrition Targets</div>
                        <div id="nutrientBars"></div>
                    </div>

                    <!-- Product recommendation -->
                    <div class="product-rec" id="productRec"></div>

                    <!-- Parent tips -->
                    <div style="font-family:'Fredoka One',cursive;font-size:1.2rem;color:var(--dk);margin-bottom:16px">💡
                        Parent
                        Tips</div>
                    <div class="tips-grid" id="tipsGrid"></div>

                    <!-- ═══ LOCK BANNER — 7-day plan ═══ -->
                    <div class="lock-banner" id="lockBanner">
                        <span class="lock-icon-lg">🔒</span>
                        <div class="lock-title">Unlock the Full 7-Day Plan</div>
                        <div class="lock-sub">Get Day 3–7 personalized meal plans, weekly grocery list, supplement stack,
                            and a
                            downloadable PDF chart.</div>

                        <div class="plan-cards-grid">
                            <div class="plan-card" id="planBasic" onclick="dcSelectPlan('basic')">
                                <div class="plan-name">7-Day Plan</div>
                                <div class="plan-price">₹99 <span>/ one-time</span></div>
                                <div class="plan-features">
                                    <div class="pf">Full 7-day meal plan</div>
                                    <div class="pf">PDF download</div>
                                    <div class="pf">Email delivery</div>
                                </div>
                            </div>
                            <div class="plan-card active" id="planPro" onclick="dcSelectPlan('pro')">
                                <span class="plan-pop-badge">⭐ Most Popular</span>
                                <div class="plan-name">Monthly Subscription</div>
                                <div class="plan-price">₹199 <span>/ month</span></div>
                                <div class="plan-features">
                                    <div class="pf">New plan every month</div>
                                    <div class="pf">PDF downloads</div>
                                    <div class="pf">Progress tracker</div>
                                    <div class="pf">Priority support</div>
                                </div>
                            </div>
                        </div>

                        <button class="btn-subscribe" onclick="dcOpenModal()">Subscribe & Unlock Full Plan →</button>
                    </div>

                    <!-- Success banner (shown after payment) -->
                    <div class="success-banner" id="successBanner">
                        <div style="font-size:3rem;margin-bottom:12px">🎉</div>
                        <h3>Payment Successful! Your PDF is Ready.</h3>
                        <p>The complete 7-day plan has been sent to your email. You can also download it directly below.</p>
                        <button class="btn-generate" onclick="dcDownload()">⬇ Download PDF Chart</button>
                    </div>

                    <div class="result-actions">
                        <button class="btn-restart" onclick="dcRestart()">↩ Make Another Plan</button>
                    </div>

                </div><!-- /result-state -->
            </div><!-- /diet-card -->
        </div><!-- /stepper-wrap -->
    </section>
