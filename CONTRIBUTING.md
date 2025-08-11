# Contributing Guidelines

Thank you for taking the time to contribute!

## Workflow
- Trunk-based: short-lived branches off `main` (or `master`).
- Branch naming: `feat/…`, `fix/…`, `chore/…`, `docs/…`.
- Conventional Commits for messages: `type(scope): summary`.
- Open Draft PR early; keep PRs small and focused.

## Definition of Done
- Code formatted and linted
- Tests added/updated and passing
- Minimal docs updated (README/CHANGELOG)
- Basic monitoring/logs for new services

## Reviews
- At least 2 approvals for significant changes
- Request review from relevant CODEOWNERS
- Address all comments or resolve with rationale

## Testing
- Unit tests preferred for logic
- Integration/E2E for critical paths
- Avoid flaky tests; use retries and timeouts responsibly

## Security & Compliance
- Never commit secrets; use env variables and secret stores
- Follow least-privilege for tokens/keys

## How to start
1. Clone and create a branch
2. Run `scripts/bootstrap.sh` (if applicable)
3. Commit with Conventional Commits
4. Open a PR referencing issues