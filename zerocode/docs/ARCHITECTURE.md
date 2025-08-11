# ZeroCode Platform — Architecture (Laravel + Livewire + SQLite, no Node)

## Goals
- 360° zero-code builder with CRUD, workflows, APIs, dashboards, multi-tenant
- Technology: Laravel 11, PHP 8.2+, SQLite3, Livewire 3, Bootstrap 5 via CDN
- No Node/npm: only Composer, CDN assets or pre-bundled assets

## High-level
- Core services: Auth (Fortify or native), RBAC (spatie/permission), Audit (activitylog)
- DSL: JSON describing entities, relations, forms, tables, pages, workflows
- Engines: CRUD generator (Livewire), Workflow engine (jobs + scheduler), API publisher
- Tenancy: Single DB with tenant_id or per-tenant SQLite file

## Modules
- Core: Users, Orgs, Tenants, Roles/Permissions, Settings
- Builder: Entity Designer, Form Builder, Table/View Builder, Page/Layout, API
- Automation: Triggers, Conditions, Actions (Email, Webhook, Record ops), Scheduler
- Reporting: Dashboards, Charts, Exports (CSV/Excel)
- Integrations: SMTP, Webhooks, REST connectors

## Data
- SQLite with WAL mode, FTS5 for full-text, cautious transactions
- Migration strategy supports upgrade to MySQL/PostgreSQL later

## UI
- Blade + Livewire components, Bootstrap CDN + SRI, Icons via Bootstrap Icons CDN
- Components: DataTable, FormSection, Modal, Drawer, Toast, ChartWidget

## Security
- Sanctum API tokens, spatie/permission policies, validation guards, rate limits
- CSP and SRI for assets, encrypted casts for sensitive fields

## Observability
- activitylog for audit, app logs, health checks, job monitor (horizon optional later)

## Packaging
- composer only, scripts to pull CDN assets into public/vendor (optional)

## Extensibility
- Connectors pattern for actions and data sources, template packs for DSL