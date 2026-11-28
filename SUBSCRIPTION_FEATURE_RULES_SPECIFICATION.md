# NutriBuddy Subscription Feature Rules Specification

> **The Golden Architecture Rule:**  
> **"Every feature is earned by a real input."** A tier unlocks specific questionnaire sections, and the answers to those questions allow the engine to unlock corresponding features in the child's diet chart and dashboard. Plans are strictly cumulative (`Basic` $\subset$ `Plus` $\subset$ `Pro` $\subset$ `Premium`).

---

## 📑 1. Questionnaire Sections Captured (8 Sections)

| # | Section Name | Unlocked In | Questions Asked | Underlying Engine Rule | Output Result |
| :---: | :--- | :---: | :---: | :--- | :--- |
| **1** | **A · Child & Growth** | **Basic**<br>*(All Plans)* | `Q1` to `Q5` | Takes DOB, Sex, Weight, Height. Computes age in months, BMI, and references ICMR-NIN 2020 RDA tables for energy, protein, calcium, and iron targets. | Sets the foundational calorie and protein budget for the diet engine. |
| **2** | **B · Food Frame** | **Basic**<br>*(All Plans)* | `Q6` to `Q12` | Evaluates diet type (Veg/Non-veg/Vegan/Jain), allergies (milk, nuts, gluten), regional cuisine preference, and meal pattern. | Filters the eligible food pool so dangerous allergens and non-compliant diet foods are 100% excluded. |
| **3** | **C · Lifestyle** | **Plus**<br>*(Plus, Pro, Premium)* | `Q13` to `Q16` | Evaluates child's physical activity level (Sedentary, Moderate, Very active), screen time hours, sleep hours, and appetite level. | Triggers `PortionScaler` to adjust serving grams and energy targets up/down; flags sleep and screen alerts. |
| **4** | **D · What They Actually Eat** | **Plus**<br>*(Plus, Pro, Premium)* | `Q17` to `Q21` | Captures favourite foods, disliked/refused foods, food frequency snapshot, daily water intake, and eating-out habits. | Removes disliked items from the candidate pool; prioritizes favourites; establishes hydration target. |
| **5** | **E · Goal** | **Pro**<br>*(Pro, Premium)* | `Q22` | Identifies parent's primary focus: Weight Gain, Height Growth, Immunity Boost, Brain / Focus, or Stamina / Sports. | Weights food selection scoring toward goal-aligned nutrients (e.g. Zinc & Vit C for Immunity; Calcium & Protein for Height). |
| **6** | **F · Clinical Signs & Gut** | **Pro**<br>*(Pro, Premium)* | `Q23` to `Q29` | Evaluates growth trend (last 6 months), bowel habit (constipation, loose stools), illness frequency, visible signs (nails, eyes, skin), sun exposure, deworming, and current supplements. | Generates early deficiency flags, triggers gut-healing ingredients, activates scientific food combos, and provides supplement safety advice. |
| **7** | **G · History & Labs** | **Premium**<br>*(Premium Only)* | `Q30` to `Q33` | Captures birth history (preterm/full-term), stores uploaded blood report document, checks menarche (periods started) for girls 10+, and puberty growth spurt stage for teens. | Calibrates daily iron target from 15mg up to 29mg/day for adolescent girls; attaches blood report to nutritionist review queue. |
| **8** | **H · Monitoring & Context** | **Premium**<br>*(Premium Only)* | `Q34` to `Q37` | Captures family history (Diabetes, Thyroid, Heart), monthly food budget band, monthly tracking preference, and parent's biggest worry (open-ended). | Eliminates simple/refined sugars if diabetic family history exists; constrains food selection to economy/moderate/flexible cost bands; routes parent worry to 1-on-1 nutritionist chat. |

---

## 📈 2. Your Child's Report (5 Features)

| Feature | Plan Tier | Rule & Trigger Logic | Expected Output on User Panel |
| :--- | :---: | :--- | :--- |
| **Growth report (height, weight, BMI for age)** | **Basic**<br>*(All Plans)* | Calculated from `Q2 (DOB)`, `Q4 (Weight)`, and `Q5 (Height)` using WHO / IAP pediatric growth standards. | Growth card displaying current BMI, BMI percentile, and status badge (*Underweight*, *Healthy*, *Overweight*). |
| **Daily nutrient needs (ICMR)** | **Basic**<br>*(All Plans)* | Looks up child age & sex in `icmr_nutrition_targets` table. | Summary table showing baseline daily RDA: Energy (kcal), Protein (g), Calcium (mg), and Iron (mg). |
| **Child Health Score across 6 areas** | **Pro**<br>*(Pro, Premium)* | Computed by `NutrientGapCalculator` and `RiskFlaggingEngine` across: (1) Growth, (2) Diet, (3) Gut, (4) Immunity, (5) Energy, (6) Clinical Signs. | Visual Health Scorecard (e.g. `82 / 100 - Good Health`) with breakdown across all 6 health areas. |
| **Deficiency early-warning check** | **Pro**<br>*(Pro, Premium)* | Mapped from `Q26 (Visible signs)` and `Q27 (Sunlight)` using `VisibleSignsMap` rules. | Warning cards alerting parents to early micronutrient risks (e.g. pale nails $\rightarrow$ iron alert; white spots $\rightarrow$ zinc/calcium alert) with corrective foods. |
| **Monthly growth tracking** | **Premium**<br>*(Premium Only)* | Driven by `Q36 (Monthly tracking)`. Schedules recurring 30-day assessment updates. | Month-over-month growth charts tracking height and healthy weight progression over time. |

---

## 🍽️ 3. Diet Plan (8 Features)

| Feature | Plan Tier | Rule & Trigger Logic | Expected Output on User Panel |
| :--- | :---: | :--- | :--- |
| **Personalised daily diet chart** | **Basic**<br>*(All Plans)* | Divides total ICMR energy across meal slots: Breakfast (22%), Morning Snack (13%), Lunch (30%), Evening Snack (13%), Dinner (22%). | 5 meal slots with dish names, timings, serving grams, calories, and protein values. |
| **Allergy-safe & diet / region matched** | **Basic**<br>*(All Plans)* | `FoodFilterPipeline` queries `veg_status`, `vegan`, `jain_allowed`, `contains_egg`, `contains_dairy`, `contains_nuts`, `contains_gluten`, and `region`. | Diet chart contains 100% allergen-free meals matching the chosen regional cuisine (e.g. North, South, Pan-India). |
| **Portions matched to activity level** | **Plus**<br>*(Plus, Pro, Premium)* | `Q13 (Activity level)` applies scaling ratio: Sedentary (1.0x), Moderate (1.1x), Very Active/Sports (1.2x–1.3x). | Serving sizes scale dynamically (e.g. standard 150g serving increases to 180g–210g for active kids). |
| **Built around likes & dislikes** | **Plus**<br>*(Plus, Pro, Premium)* | `Q18 (Disliked foods)` items are excluded from candidate food pool; `Q17 (Favourites)` receive a selection boost. | Refused foods (e.g. Karela, Bhindi, Milk) never appear in the weekly schedule. |
| **7-day rotating variety** | **Plus**<br>*(Plus, Pro, Premium)* | `RuleBasedDietPlanEngine` runs for $N = 7$ days with a penalty for repeated dishes. | 7 distinct day tabs (Monday to Sunday) with unique breakfast, lunch, and dinner rotations. |
| **Goal-focused plan (immunity/brain/height...)** | **Pro**<br>*(Pro, Premium)* | `Q22 (Main goal)` boosts foods rich in goal-relevant nutrients; `ComboEngine` adds goal-booster pairs. | Meal plan prioritized for the child's specific goal, accompanied by goal explanation badges. |
| **Weekly shopping list** | **Pro**<br>*(Pro, Premium)* | Aggregates all ingredients across the 7-day meal plan and groups them by grocery category. | Consolidated grocery list (e.g. Pulses: 500g Moong, Dairy: 2L Milk, Veggies: 1kg Spinach) for weekend shopping. |
| **Budget-fit planning** | **Premium**<br>*(Premium Only)* | `Q35 (Budget band)` filters `food_master.cost_band` (economy, moderate, flexible). | If *Economy* selected, exotic expensive ingredients are replaced with high-nutrient budget desi staples (sattu, chana, peanuts, seasonal greens). |

---

## 🍲 4. Recipes (6 Features)

| Feature | Plan Tier | Rule & Trigger Logic | Expected Output on User Panel |
| :--- | :---: | :--- | :--- |
| **Dish names in the plan** | **Basic**<br>*(All Plans)* | Reads `food_name` and `regional_name` from `food_master`. | Meal cards display clear dish titles (e.g. *Moong Dal Chilla*, *Palak Khichdi*). |
| **Full step-by-step recipes (ingredients + method)** | **Pro**<br>*(Pro, Premium)* | Reads `ingredients` (JSON) and `method` (JSON) columns from `food_master`. | Clicking any dish opens a detailed drawer showing measured ingredients and child-friendly cooking instructions. |
| **Access to 1,000+ Indian recipe library** | **Pro**<br>*(Pro, Premium)* | Unlocks full catalog browsing access to active recipes in the `food_master` database. | Searchable recipe directory filtered by meal slot, cuisine, prep time, and health benefits. |
| **Goal-boosting recipes & food combos** | **Pro**<br>*(Pro, Premium)* | Generated by `ComboEngine` using food synergy science (e.g. Iron + Vitamin C; Complementary proteins). | Highlighted **💡 Food Pairing Notes** on meal cards showing why two foods work better together. |
| **Seasonal & festival recipe packs** | **Premium**<br>*(Premium Only)* | Filtered by `currentIndianSeason()` (Vasant, Greeshma, Varsha, Sharad, Hemant, Shishir) and festive tags. | Dedicated seasonal packs (Winter warming recipes, Summer cooling drinks, Healthy sugar-free festival treats). |
| **Custom recipe requests** | **Premium**<br>*(Premium Only)* | Routed through the assigned nutritionist channel. | Parents can request custom recipes for difficult eating habits (e.g. healthy spinach pasta for picky eaters). |

---

## 🩺 5. Guidance & Expert Support (7 Features)

| Feature | Plan Tier | Rule & Trigger Logic | Expected Output on User Panel |
| :--- | :---: | :--- | :--- |
| **Lifestyle tips (sleep / screen / water)** | **Plus**<br>*(Plus, Pro, Premium)* | Derived from `Q14 (Screen time)`, `Q15 (Sleep)`, and `Q20 (Water intake)`. | Guidelines on bedtime blue-light cutoff, melatonin support foods, and daily water hydration targets. |
| **Gut & immunity guidance** | **Pro**<br>*(Pro, Premium)* | Derived from `Q24 (Bowel habits)` and `Q25 (Illness frequency)`. | Specific dietary interventions: soluble fiber for constipation; natural probiotics and immune modulators for frequent illnesses. |
| **Honest supplement advice (only if truly needed)** | **Pro**<br>*(Pro, Premium)* | Derived from `Q26 (Visible signs)`, `Q27 (Sunlight)`, and `Q29 (Current supplements)`. | Transparent advice: tells parents when supplements are unnecessary ("Food First"), and provides safe pediatric dosing alerts only when deficiency risk is genuine. |
| **Blood-report fine-tuning** | **Premium**<br>*(Premium Only)* | Triggered by file upload in `Q31 (Latest blood report)`. Reviewed by qualified dietician. | Diet chart calibrated to exact lab numbers (e.g. adjusting bioavailable iron, separating calcium from iron meals for anemic kids). |
| **Puberty / teen-girl iron adjustments** | **Premium**<br>*(Premium Only)* | Triggered by `Q32 (Menarche/Periods)` for girls 10+ and `Q33 (Puberty stage)`. | Recalibrates daily iron target to **29mg/day (ICMR guideline)** to compensate for menstrual blood loss. |
| **Family-history prevention guidance** | **Premium**<br>*(Premium Only)* | Triggered by `Q34 (Family history: Diabetes, Thyroid, Cholesterol, Obesity)`. | Enforces preventive nutrition: eliminates refined sugars; mandates low-GI complex grains (jowar, bajra, ragi). |
| **1-on-1 nutritionist chat** | **Premium**<br>*(Premium Only)* | Direct dedicated messaging channel with a certified pediatric nutritionist. | Direct chat access for ongoing guidance, wedding/travel food planning, and sickness adjustments. |

---

## 🏷️ Summary Matrix: What Each Plan Receives

* **Basic Plan (12 Questions):**  
  * *Features:* Personalised daily chart, Allergy safety filter, Region match, ICMR baseline targets, Dish names.
  * *Focus:* A safe, correct standard chart.

* **Plus Plan (21 Questions):**  
  * *Features:* Everything in Basic + Likes/Dislikes exclusion, Activity-scaled portions, 7-day rotating variety, Lifestyle tips (Sleep/Screen/Water).
  * *Focus:* Personalised to how the child really eats and lives.

* **Pro Plan (29 Questions):**  
  * *Features:* Everything in Plus + Goal-focused plan, Health Score (6 areas), Deficiency early warnings, Nutrient Gaps progress bars, Step-by-step recipes, 1,000+ recipe library, Goal-boosting combos, Gut & immunity guidance, Honest supplement advice, Weekly shopping list.
  * *Focus:* A goal-focused plan with comprehensive health scoring.

* **Premium Plan (37 Questions):**  
  * *Features:* Everything in Pro + Blood report calibration, Budget-fit planning, Monthly growth tracking, Seasonal/festival packs, Custom recipe requests, Puberty/teen iron adjustments, Family history prevention, **1-on-1 Nutritionist Chat**.
  * *Focus:* Full clinical precision, ongoing tracking, and direct expert care.
