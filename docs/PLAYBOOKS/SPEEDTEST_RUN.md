# Speed Test Runbook

## Preparation
- Ensure participants listed in `tools/speedtest/participants.txt`
- Decide base branch (default current)

## Generate branches and commits (safe, tiny changes)
```bash
PUSH=false OPEN_PR=false scripts/prepare-speed-prs.sh
```
- This creates branches `speed/<id>-<timestamp>` and tiny files under `bench/`
- Set `PUSH=true` to push to remote
- If GitHub CLI is available and authenticated, set `OPEN_PR=true` to open PRs automatically

## Review routing
- Use `CODEOWNERS` to route reviews
- Enforce 2 reviewers on significant changes; for this test 1 is enough

## CI
- Run the `SpeedTest` workflow to examine parallelism and queue times

## Metrics capture
- Record timings in `tools/speedtest/scoreboard.csv`
- Optionally extend with a script to query GitHub API

## Wrap-up
- Retro using `docs/TEMPLATES/RETRO_TEMPLATE.md`
- Capture blockers and actions