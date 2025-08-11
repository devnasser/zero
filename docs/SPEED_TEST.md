# Maximum Team Speed Test

This playbook measures end-to-end execution speed and parallel efficiency.

## KPIs
- Lead time for change (PR open -> merge)
- Review latency (time to first review)
- Throughput (merged PRs/day)
- WIP and batch size (avg files/PR)

## Procedure (Team)
1. Create a small, independent change per engineer (<= 50 LoC) with tests
2. Open PRs simultaneously; assign reviewers in separate pods
3. Target: first review < 15 min; merge < 60 min; CI pass
4. Record metrics in the scoreboard and retro

## Parallel CI Sanity
- CI matrix with N jobs to validate scheduler and queue times

## Local Parallel Micro-bench (Safe)
Run the local micro-bench to gauge parallel capacity without touching project code:

```bash
scripts/speed-test.sh           # auto-detect jobs
JOBS=16 scripts/speed-test.sh   # override jobs
BYTES_PER_JOB=2097152 scripts/speed-test.sh  # 2 MiB per job
```

Outputs jobs, duration, and ops/sec. This is non-destructive and cleans up.

## Roles
- Pod Leads: coordinate reviewers and queues
- DevOps: watch CI queue time and concurrency
- Scribe: capture timings and blockers