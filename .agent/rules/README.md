# Nutribuddy Agent Rules Index

This folder contains modular rules that guide AI assistants operating within the Nutribuddy repository.

## Rule Files

1. [architecture-services.md](file:///e:/website-project/Nutribuddy/.agent/rules/architecture-services.md)
   - Service layer segregation, keeping controllers thin, form requests, and type-hinting.
2. [database-eloquent.md](file:///e:/website-project/Nutribuddy/.agent/rules/database-eloquent.md)
   - Null safety on dates/relationships (e.g. `diffForHumans`), eager loading (`with(...)`) to prevent N+1 queries, safe migration guidelines.
3. [frontend-blade.md](file:///e:/website-project/Nutribuddy/.agent/rules/frontend-blade.md)
   - View separation (`admin/` vs `frontend/`), modern aesthetics and styling conventions, responsive layout expectations.
4. [diet-chart-generation.md](file:///e:/website-project/Nutribuddy/.agent/rules/diet-chart-generation.md)
   - Rules, algorithms, pipelines, safety constraints (allergies/diet types), and data persistence for both rule-based and AI diet chart generation.
5. [scalable-architecture-2m.md](file:///e:/website-project/Nutribuddy/.agent/rules/scalable-architecture-2m.md)
   - Comprehensive blueprint for scaling Nutribuddy to 2M+ users: bottlenecks, clean modular directory structure, Redis caching, async queues, DB indexes, and controller refactoring.
6. [environment-tooling.md](file:///e:/website-project/Nutribuddy/.agent/rules/environment-tooling.md)
   - Windows PowerShell standards, non-interactive artisan commands, cache clearing, and code integrity guidelines.
7. [controller-optimization-and-refactor.md](file:///e:/website-project/Nutribuddy/.agent/rules/controller-optimization-and-refactor.md)
   - Zero-breakage invariants, domain directory organization, thin controller (150-line rule), form requests, and regression testing standards.
8. [critical-domain-review.md](file:///e:/website-project/Nutribuddy/.agent/rules/critical-domain-review.md)
   - Review and regression standards for Products/checkout/coins, Nutrition/diet charts, Assessments, subscription lifecycle/renewals, and customer/admin authentication, sessions, tokens, and permissions.
