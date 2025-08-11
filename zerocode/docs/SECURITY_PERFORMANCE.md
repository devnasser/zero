# Security & Performance

## Security
- Auth: Laravel auth + 2FA, Sanctum for API tokens
- RBAC: spatie/permission; use policies and guards per tenant
- Input: Strict validation, mass-assignment guarded, rate limiters
- Headers: CSP, SRI for CDN assets, HSTS, secure cookies
- Data: Encrypted casts for secrets, audit logging for changes

## Performance
- SQLite: WAL mode, short transactions, indexes on filters/sorts, FTS5 for search
- Livewire: defer/debounce, pagination, chunked loading, memoize computed
- Caching: config/route/view cache; file cache store; preload common data
- Queues: database/sync; prefer small jobs, retry/backoff

## Reliability
- Backups: periodic SQLite snapshots + DSL export; verify restore
- Health: artisan health checks for DB, queues, scheduler
- Observability: activity logs, request logs, job logs