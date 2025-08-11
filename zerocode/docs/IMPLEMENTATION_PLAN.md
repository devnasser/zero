# Implementation Plan (Parallelized)

Assumptions: 6-8 engineers. Workdays: 5 days/week.

## Workstreams (in parallel)
- Core & Tenancy (2 ppl, 1.5 weeks)
- Security & RBAC & Audit (1 ppl, 1 week)
- DSL Engine (parser + registry + validation) (2 ppl, 2 weeks)
- CRUD Generator (Livewire) (2 ppl, 2 weeks)
- Builders UI (Entity/Form/Table/Page) (3 ppl, 3 weeks)
- Workflow Engine & Scheduler (2 ppl, 2 weeks)
- API Publisher (1 ppl, 1 week)
- Reporting/Dashboards (1 ppl, 1 week)
- Packaging & Docs & Templates (1 ppl, 1 week, ongoing)

## Critical Path
- DSL Engine -> CRUD Generator -> Builders
- Core & Tenancy -> Security/RBAC -> API Publisher
- Workflow Engine can start after DSL MVP

## Milestones
- M1 (Week 1-2): Core auth/tenancy, DSL MVP, CRUD basic
- M2 (Week 3-4): Builders v1, Workflow MVP, API v1
- M3 (Week 5-6): Dashboards, Templates, Hardening

## ETA
- Nominal: 6 weeks
- With 8 engineers and parallelism: 5-6 weeks
- Buffer: 1 week for polish

Remaining from today: ~6 weeks (±1) to a usable 360° MVP.