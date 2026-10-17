# Nutribuddy Project Agent Rules & Architecture Guidelines

This file defines the project instructions, architectural patterns, conventions, and safety standards for AI assistants and pair programmers working in the **Nutribuddy** codebase.

---

## 1. Project Overview & Tech Stack
- **Framework:** Laravel 11.x (PHP 8.2+)
- **Architecture:** MVC with dedicated Service & Engine layers (e.g., `App\Services\Nutrition\...`)
- **Database / ORM:** Eloquent ORM, MySQL / SQLite
- **Authorization & Roles:** Spatie Laravel-Permission (`spatie/laravel-permission`)
- **Exports & PDFs:** DomPDF (`barryvdh/laravel-dompdf`), Maatwebsite Excel (`maatwebsite/excel`)
- **Frontend & Assets:** Laravel Blade templates, Vite (`vite.config.js`), Vanilla CSS/JS & component-specific stylesheets in `public/css/` and `resources/views/`
- **Environment:** Windows (PowerShell environment when running artisan/composer/npm commands)

---

## 2. Core Architectural Patterns & Code Style

### A. Business Logic in Services
- **Do not bloat Controllers:** Heavy business calculations (e.g., calorie formulas, diet target generation, nutrition parsing, lead onboarding) belong in `App\Services\` (e.g., `TargetEngine`, dedicated nutrition services) rather than controllers.
- Controllers should focus on:
  1. Validating request inputs via Form Requests or `$request->validate(...)`.
  2. Calling appropriate Service/Engine methods.
  3. Returning clean Blade views or JSON responses.

### B. Eloquent Models & Database Safety
- **Null Safety:** Guard against `null` timestamps or optional relations (e.g., check `->created_at?->diffForHumans()` or verify existence before calling Carbon date formatting methods).
- **Eager Loading:** Always prevent N+1 query problems in admin listings and dashboard queries using `with(['relation1', 'relation2'])`.
- **Database Migrations:** Never alter production table columns destructively without safe fallback migrations. Always test rollbacks if writing complex migrations.

### C. Blade Views & Frontend Aesthetics
- **Blade Conventions:** Keep UI organized in `resources/views/admin/` for back-office and `resources/views/frontend/` for public/client-facing interfaces.
- **Visual Excellence:** Maintain modern, clean UI typography, proper spacing, smooth transitions, and distinct status badges. Avoid unstyled default inputs.
- **Responsive Layouts:** Ensure questionnaires, diet charts, and admin tables are responsive on mobile and desktop viewports.

---

## 3. Tool & Command Execution Standards

- **Environment Shell:** PowerShell on Windows.
  - Use Windows-compatible paths (`\` or sanitized `/`).
  - Do not propose standalone `cd` commands. Use the command execution tool's `Cwd` parameter.
  - Do not run interactive prompts in automated commands (e.g., pass `--no-interaction` or answer flags where applicable).
- **Artisan Commands:**
  - Clear cache when editing configuration or routes: `php artisan route:clear`, `php artisan config:clear`, `php artisan view:clear`.
  - Format code using Laravel Pint when requested: `vendor/bin/pint`.

---

## 4. Documentation & Code Integrity Rules

1. **Preserve Existing Comments & Logic:** Never strip out existing business notes, medical/nutrition formulas, or docstrings unless explicitly asked.
2. **Fail Fast with Clear Errors:** Validate incoming payloads thoroughly and handle edge cases gracefully (e.g., missing dietary preferences, empty lead profiles).
3. **Traceability:** When creating new services or models, add descriptive PHPDoc blocks specifying input and return types.

---

## 5. Diet Chart Generation & Admin Rules Display

When modifying or displaying diet generation logic, align with the system rules displayed in the Admin Panel (`route('admin.nutrition.diet-rules.index')` / `resources/views/admin/nutrition/rules/index.blade.php`):
- **Safety Filters (`FoodFilterPipeline`):** Strict exclusion by diet type (Vegan, Jain, Eggetarian, Vegetarian), declared allergens (dairy, nuts, gluten, egg), and disliked dishes.
- **Timing & Slots:** Match food `slot`/`also_suitable_for` to daily structure (`3 meals`, `2 + snacks`, or default 5 occasions) across Monday–Sunday.
- **Nutrient Gap Targeting (`TargetEngine`):** Prioritize foods that close gaps against ICMR RDA targets for the child's age/sex; apply dairy cap penalties if daily dairy intake $\ge 2$ glasses.
- **Variety & Preferences:** Enforce repetition gaps (minimum 3 days between same dish with -20 penalty), give score bonuses for matching culinary region (+5), high kid acceptance (+1 to +5), and cooking medium (+5).
- **Clinical Review Gate (`RiskFlaggingEngine`):** Map clinical symptoms via `DeficiencyRule` and flag medical conditions for professional nutritionist review before dispatch.
- **Detailed Rule Specification:** Refer to `.agent/rules/diet-chart-rules.md`.

---

## 6. Controller Optimization & Zero-Breakage Standards

When refactoring or creating controllers, follow the strict guidelines in `.agent/rules/controller-optimization-and-refactor.md`:
- **Zero-Breakage Contract:** Absolutely never break existing routes, Blade view contracts, or JSON response shapes for Product, Diet Chart, or Assessment/Quiz sections.
- **Domain Sub-namespaces:** Group controllers under `Admin\Ecommerce\`, `Admin\Nutrition\`, `Frontend\Nutrition\`, `Api\Nutrition\`, etc.
- **Thin Controllers (150-Line Rule):** Controllers must strictly orchestrate HTTP requests. Move validation to Form Requests (`App\Http\Requests\<Domain>\...`) and business logic to Services (`App\Services\<Domain>\...`).
- **Regression Testing:** Always run dedicated feature regression tests (`php artisan test`) before and after any controller modifications.

## 7. Frontend CSS & JavaScript Standards

Follow `.agent/rules/frontend-assets.md` when auditing or changing CSS, Sass, JavaScript, or Blade asset loading. In particular, preserve existing asset URLs and DOM hooks, keep product/payment decisions server-authoritative, and verify that edited assets are part of the actual Blade or Vite loading path.



## 7. Critical Product, Nutrition, Assessment, Subscription & Authentication Review

Before modifying Products/checkout/coins, Nutrition/diet charts, Assessments, subscriptions/renewals, or customer/admin login, roles, sessions, or tokens, follow `.agent/rules/critical-domain-review.md` in addition to the relevant focused rules above. It defines the zero-breakage contracts, security and lifecycle checks, regression coverage, and staged migration boundaries for these domains.
