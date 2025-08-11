# Project Setup

This repository is prepared with an operational scaffold for fast, safe collaboration.

## Getting started
1. Clone the repo and create a branch
2. Run `scripts/bootstrap.sh`
3. Commit following Conventional Commits
4. Open a PR; CI will run sanity checks

## Structure
- `.github/` — PR/Issue templates and CI
- `RUNBOOKS/` — On-call and incident procedures
- `DECISIONS/` — Architecture decisions (ADRs)
- `docs/` — Documentation, team roster, guides
- `scripts/` — Local setup helpers

## Conventions
- Trunk-based development; small PRs
- Definition of Done in `CONTRIBUTING.md`
- Code of Conduct and Security policy included

## Team
See `docs/TEAM.md` for members and pods.

## Speed Test
- Team process: see `docs/SPEED_TEST.md`
- Local micro-bench: `scripts/speed-test.sh`
- CI parallel test: GitHub Actions `SpeedTest` workflow