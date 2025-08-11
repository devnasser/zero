# High-Throughput Sprint Playbook

## Principles
- Small, independent vertical slices
- Aggressive review SLAs; pre-assigned reviewers per pod
- CI as guardrail, not gatekeeper delays

## Setup
- Enable SpeedTest workflow (dispatch)
- Pre-assign CODEOWNERS per area
- Create PR templates and checklist

## Execution
- WIP limit: 1 task/engineer
- PR size: <= 200 added lines
- Time to first review: < 15 min
- Merge time: < 60 min if green

## Metrics
- Throughput/day, Cycle time, Review latency
- Flaky test count, CI queue time

## Retro
- Capture top 3 blockers and concrete actions