# NutriBuddy Subscription Plans — Manual Testing & Verification Guide

> **Purpose:** This document provides the complete, step-by-step verification checklist for testing all 4 subscription plans (`Basic`, `Plus`, `Pro`, `Premium`). Use this guide to manually purchase and verify each plan one by one to ensure the questionnaire questions and diet chart generation outputs strictly follow the design rules.

---

## 🎯 The Core Architecture Rule

> **"Each plan = the questionnaire sections it unlocks."**  
> Every feature in the diet chart is earned by a real input from the questionnaire. Plans are strictly **cumulative** — each tier includes everything from the tiers below it.

```
┌────────────────────────────────────────────────────────────────────────┐
│  Basic   → Sections A + B                                 = 12 Qs      │
│  Plus    → Basic + Sections C + D                         = 21 Qs      │
│  Pro     → Plus + Sections E + F                          = 29 Qs      │
│  Premium → Pro + Sections G + H (Complete Questionnaire)   = 37 Qs      │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 🛠️ How to Test (3-Step Workflow)

For each plan, follow these 3 steps:

1. **Step 1: Purchase / Select Plan**
   * Go to `/product` or select the plan during onboarding (`Basic`, `Plus`, `Pro`, or `Premium`).
2. **Step 2: Fill the Questionnaire**
   * URL: `/diet-chart/questionnaire?plan=<slug>` (e.g. `plan=basic`, `plan=plus`, `plan=pro`, `plan=premium`).
   * Count the questions and verify that only the expected questions for that tier appear.
3. **Step 3: Approve & View Diet Chart**
   * *Admin Approval:* Open `/admin/nutrition/lead-queue` and click **Approve** on the new lead.
   * *User View:* Open `/child-profile/{child_id}/diet-chart` to verify the generated chart against the checklist below.

---

## 🟢 Plan 1: BASIC PLAN (Safe Standard Chart)

### 1. Questions Asked (Exactly 12 Questions)

#### Section A · Child & Growth
* [ ] **Q1:** Child's name *(Text)*
* [ ] **Q2:** Age / Date of birth *(Date)*
* [ ] **Q3:** Sex *(Boy / Girl)*
* [ ] **Q4:** Current weight in kg *(Number)*
* [ ] **Q5:** Current height in cm or ft/in *(Number)*

#### Section B · Food Frame
* [ ] **Q6:** Diet type *(Vegetarian, Non-veg, Eggetarian, Vegan, Jain)*
* [ ] **Q7:** Allergies & intolerances *(Milk/Dairy, Nuts, Gluten, Egg, None)*
* [ ] **Q8:** Region / cuisine liked *(North, South, East, West, Pan India)*
* [ ] **Q9:** Usual meal pattern *(3 meals, 2 meals + snacks, 5 small meals)*
* [ ] **Q10:** Milk & dairy taken per day *(Cups / None)*
* [ ] **Q11:** Cooking oil / medium at home *(Mustard, Groundnut, Ghee, Refined, Coconut)*
* [ ] **Q12:** Any diagnosed condition or medicine? *(Free text / None)*

---

### 2. What Must Be Generated (User Panel Output)
* [ ] **Personalised Daily Diet Chart:** 5 standard meal slots (Breakfast 7 AM, Morning Snack 10 AM, Lunch 12 PM, Evening Snack 4 PM, Dinner 7 PM).
* [ ] **Standard Portions:** Baseline serving size in grams (e.g. 150g).
* [ ] **Allergy Safety Filter:** If Milk/Nuts/Gluten selected in Q7, those ingredients **must never appear** in any meal.
* [ ] **Cuisine Match:** Meals align with the selected Indian region (Q8).
* [ ] **Daily Nutrient Needs (ICMR 2020):** Shows baseline Energy (kcal) and Protein (g) target according to child age & weight.

### 3. What Must NOT Appear (Locked Features)
* ❌ No Dislike exclusions (Q18 was not asked).
* ❌ No activity-based portion scaling (Q13 was not asked).
* ❌ No multi-day variety / rotating weekly tabs.
* ❌ No Goal-boosting combos or clinical guidance.
* ❌ No step-by-step recipe drawer.

---

## 🟡 Plan 2: PLUS PLAN (Personalised to Real Eating & Lifestyle)

### 1. Questions Asked (Cumulative 21 Questions)
* **Includes all 12 Basic Questions (Q1 to Q12), PLUS the following 9 Questions:**

#### Section C · Lifestyle
* [ ] **Q13:** Activity level *(Sedentary, Moderate, Very active / Sports)*
* [ ] **Q14:** Screen time *(< 1 hr, 1-2 hrs, 3+ hrs)*
* [ ] **Q15:** Sleep at night *(< 7 hrs, 8-9 hrs, 10+ hrs)*
* [ ] **Q16:** Appetite *(Picky / Low, Normal, High)*

#### Section D · What They Actually Eat
* [ ] **Q17:** Favourite / must-have foods *(Text / Multiselect)*
* [ ] **Q18:** Disliked / refused foods *(e.g. Karela, Bhindi, Milk, Tinda, Eggs)*
* [ ] **Q19:** Food-frequency snapshot *(Vegetables, Fruits, Junk/Fried frequency)*
* [ ] **Q20:** Water intake *(Glasses per day)*
* [ ] **Q21:** Eating out / tiffin habits *(Daily, 2-3x/week, Rarely)*

---

### 2. What Must Be Generated (User Panel Output)
* [ ] **Everything from Basic.**
* [ ] **Built Around Likes & Dislikes:** Test by selecting a specific disliked food in `Q18` (e.g. *Bhindi* or *Karela*). Verify that this food is **strictly absent** across all meals.
* [ ] **Portions Scaled to Activity Level:**
  * If *Sedentary* selected: Standard serving grams.
  * If *Very Active / Sports* selected: Portions scaled up (e.g. 150g increases to 180g–210g) with higher energy kcal allocation.
* [ ] **7-Day Rotating Variety:** Monday to Sunday tabs appear with distinct rotating meal combinations.
* [ ] **Lifestyle Guidance:** Actionable tips for sleep, screen time cutoff, and daily water hydration targets based on Q14, Q15, and Q20.

### 3. What Must NOT Appear (Locked Features)
* ❌ No Goal-boosting food pairings (Q22 was not asked).
* ❌ No Gut & digestion clinical warnings.
* ❌ No Recipe step-by-step drawer.
* ❌ No Weekly shopping list tab.

---

## 🔵 Plan 3: PRO PLAN (Goal-Focused Plan + Health Score)

### 1. Questions Asked (Cumulative 29 Questions)
* **Includes all 21 Plus Questions (Q1 to Q21), PLUS the following 8 Questions:**

#### Section E · Goal
* [ ] **Q22:** Main goal *(Weight Gain, Height Growth, Immunity Boost, Brain / Focus, Stamina / Sports)*

#### Section F · Clinical Signs & Gut
* [ ] **Q23:** Growth trend in last 6 months *(Normal, Slow, Stalled)*
* [ ] **Q24:** Bowel habit *(Regular, Constipated, Loose stools, Gas / Bloating)*
* [ ] **Q25:** How often falls ill *(Rarely, Seasonally, Very frequently)*
* [ ] **Q26:** Visible signs you notice *(Pale nails, Dark circles, Dry skin, Hair fall, Fatigue, None)*
* [ ] **Q27:** Sunlight / outdoor time daily *(< 15 mins, 30 mins, 1+ hr)*
* [ ] **Q28:** Deworming in last 6 months? *(Yes / No)*
* [ ] **Q29:** Currently on any supplement? *(Iron, Calcium, Vitamin D, Multivitamin, None)*

---

### 2. What Must Be Generated (User Panel Output)
* [ ] **Everything from Plus.**
* [ ] **Goal-Focused Priority Planning:** Meals prioritize nutrients matching the chosen goal in `Q22` (e.g. if *Height Growth* is selected, high-bioavailable Calcium and Protein foods dominate the slots).
* [ ] **Detected Nutrient Gaps Bar Chart:** Progress bars for Energy, Protein, Calcium, and Iron showing Target vs. Achieved amounts with adequacy percentages.
* [ ] **Goal-Boosting Food Combos & Science Tips:** Each meal card displays a science-backed pairing note (e.g. *“Spinach paired with lemon for 3x iron absorption”*).
* [ ] **Gut & Immunity Corrective Foods:** If *Constipation* was marked in Q24, high-fiber soluble foods (papaya, figs, oats) appear; if *Illness frequency* is high, natural immune boosters appear.
* [ ] **Deficiency Early-Warning Alert:** Flags potential deficiencies based on Q26 visible signs with food-first solutions.

### 3. What Must NOT Appear (Locked Features)
* ❌ No Blood test report attachment/upload (Q31).
* ❌ No Budget band restriction (Q35).
* ❌ No 1-on-1 Nutritionist direct chat.

---

## 🟣 Plan 4: PREMIUM PLAN (Clinical Precision & Nutritionist Support)

### 1. Questions Asked (All 37 Questions)
* **Includes all 29 Pro Questions (Q1 to Q29), PLUS the final 8 Questions:**

#### Section G · History & Labs
* [ ] **Q30:** Birth history *(Full term, Preterm, Low birth weight)*
* [ ] **Q31:** Latest blood report *(File Upload: PDF, JPG, PNG)*
* [ ] **Q32:** Periods started? *(For girls 10+ years: Yes / No / Not yet)*
* [ ] **Q33:** Growth-spurt / puberty stage *(Teens: Early, Peak, Late)*

#### Section H · Monitoring & Context
* [ ] **Q34:** Family history *(Type 2 Diabetes, Thyroid, High Cholesterol, Obesity, None)*
* [ ] **Q35:** Monthly food budget band *(Economy / Desi-power, Moderate, Flexible / Premium)*
* [ ] **Q36:** Monthly growth update preference *(Yes, remind me)*
* [ ] **Q37:** Your biggest worry *(Open text message to the nutritionist)*

---

### 2. What Must Be Generated (User Panel Output)
* [ ] **Everything from Pro.**
* [ ] **Blood Report Integration:** The file uploaded in `Q31` is stored on the private disk and attached to the nutritionist lead queue for clinical calibration.
* [ ] **Budget-Fit Food Filtering:** If *Economy* selected in Q35, expensive exotic foods are replaced by budget-friendly nutrient powerhouses (sattu, roasted chana, seasonal greens, peanuts).
* [ ] **Puberty / Teen Iron Adjustment:** If girl is 10+ and periods started (Q32), the iron target automatically recalibrates from 15mg up to **29mg/day** (ICMR guidelines).
* [ ] **Family-History Preventive Rules:** If *Diabetes* checked in Q34, simple sugars and refined flour are eliminated; complex low-GI grains (jowar, bajra, ragi) are enforced.
* [ ] **Nutritionist Review & Verified Badge:** Verified by dietician with personalized review notes displayed at the top of the chart.
* [ ] **1-on-1 Nutritionist Chat Support:** Direct access channel to consult with the assigned personal nutritionist.

---

## 📋 Comprehensive Feature vs. Plan Matrix

| Category | Feature | Basic (12 Qs) | Plus (21 Qs) | Pro (29 Qs) | Premium (37 Qs) |
| :--- | :--- | :---: | :---: | :---: | :---: |
| **Questions** | Questionnaire Sections Unlocked | **A, B** | **A, B, C, D** | **A to F** | **A to H (All)** |
| **Report** | Growth Report (BMI for age) | ✅ | ✅ | ✅ | ✅ |
| | Daily ICMR 2020 Nutrient Targets | ✅ | ✅ | ✅ | ✅ |
| | Child Health Score (6 Areas) | ❌ | ❌ | ✅ | ✅ |
| | Deficiency Early-Warning Check | ❌ | ❌ | ✅ | ✅ |
| | Monthly Growth Tracking | ❌ | ❌ | ❌ | ✅ |
| **Diet Plan** | Personalized Daily Meal Schedule | ✅ | ✅ | ✅ | ✅ |
| | Allergy-Safe & Region Matched | ✅ | ✅ | ✅ | ✅ |
| | Activity-Scaled Portions (grams) | ❌ | ✅ | ✅ | ✅ |
| | Likes & Dislikes Exclusion | ❌ | ✅ | ✅ | ✅ |
| | 7-Day Rotating Variety | ❌ | ✅ | ✅ | ✅ |
| | Goal-Focused Plan (Height/Immunity) | ❌ | ❌ | ✅ | ✅ |
| | Weekly Shopping List | ❌ | ❌ | ✅ | ✅ |
| | Budget-Fit Food Planning | ❌ | ❌ | ❌ | ✅ |
| **Recipes** | Dish Names in Schedule | ✅ | ✅ | ✅ | ✅ |
| | Step-by-Step Recipes (Ingredients+Method)| ❌ | ❌ | ✅ | ✅ |
| | 1,000+ Indian Recipe Library | ❌ | ❌ | ✅ | ✅ |
| | Goal-Boosting Food Combos | ❌ | ❌ | ✅ | ✅ |
| | Seasonal & Festival Recipe Packs | ❌ | ❌ | ❌ | ✅ |
| | Custom Recipe Requests | ❌ | ❌ | ❌ | ✅ |
| **Support** | Lifestyle Guidance (Sleep/Screen/Water) | ❌ | ✅ | ✅ | ✅ |
| | Gut & Immunity Corrective Plan | ❌ | ❌ | ✅ | ✅ |
| | Honest Supplement Advice | ❌ | ❌ | ✅ | ✅ |
| | Blood-Report Clinical Fine-Tuning | ❌ | ❌ | ❌ | ✅ |
| | Teen / Puberty Iron Adjustments | ❌ | ❌ | ❌ | ✅ |
| | Family-History Prevention Guidance | ❌ | ❌ | ❌ | ✅ |
| | 1-on-1 Nutritionist Chat Support | ❌ | ❌ | ❌ | ✅ |

---

## 🧪 Quick Test Data Recommendations

When manually testing each plan, use these distinct sample inputs to quickly confirm the system is reacting to your answers:

1. **Test Likes/Dislikes (Plus+):**
   * Put `Bhindi` or `Karela` in Disliked Foods (`Q18`). Check Day 1–7 meals: none of them should contain Bhindi or Karela.
2. **Test Activity Scaling (Plus+):**
   * Choose `Very active / Sports` in `Q13`. Check portion size: meal grams should be scaled above 150g (e.g. 180g–210g).
3. **Test Goal Optimization (Pro+):**
   * Choose `Immunity Boost` in `Q22`. Check Food Combos: look for Vitamin C + Zinc pairings (e.g. amla, citrus, seeds).
4. **Test Budget Fit (Premium):**
   * Choose `Economy` in `Q35`. Check meal choices: high-cost ingredients (avocado, quinoa, imported berries) are swapped with high-protein desi staples (sattu, roasted chana, peanuts, seasonal greens).
