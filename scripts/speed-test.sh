#!/usr/bin/env bash
set -euo pipefail

# Safe, non-destructive speed test: CPU+IO micro-bench in parallel.
# Writes to .tmp_speedtest and cleans up.

TMP_DIR="${TMP_DIR:-.tmp_speedtest}"
JOBS="${JOBS:-auto}"
BYTES_PER_JOB="${BYTES_PER_JOB:-1048576}" # 1 MiB per job

if [ "${JOBS}" = "auto" ]; then
  if command -v nproc >/dev/null 2>&1; then
    JOBS=$(nproc)
  else
    JOBS=4
  fi
fi

mkdir -p "${TMP_DIR}"
start_ts=$(date +%s%3N || date +%s)

run_job() {
  local idx="$1"
  local file_base="job_${idx}"
  local data_file="${TMP_DIR}/${file_base}.bin"
  local gz_file="${TMP_DIR}/${file_base}.gz"
  # Generate pseudo-random data safely without blocking
  head -c "${BYTES_PER_JOB}" /dev/urandom > "${data_file}" 2>/dev/null || dd if=/dev/urandom of="${data_file}" bs=1024 count=$((BYTES_PER_JOB/1024)) status=none
  sha256sum "${data_file}" > "${data_file}.sha256" 2>/dev/null || true
  gzip -c "${data_file}" > "${gz_file}"
  sha256sum "${gz_file}" > "${gz_file}.sha256" 2>/dev/null || true
  # tiny sleep to simulate scheduling jitter
  sleep 0.$((RANDOM % 2)) 2>/dev/null || true
}

pids=()
for i in $(seq 1 "${JOBS}"); do
  run_job "$i" &
  pids+=("$!")
  # Light throttle to avoid bursty IO
  sleep 0.01
done

for pid in "${pids[@]}"; do
  wait "$pid"
done

end_ts=$(date +%s%3N || date +%s)

duration_ms=$((end_ts - start_ts))
if [ "$duration_ms" -le 0 ]; then duration_ms=1; fi
ops=$JOBS
# ops/sec with two decimals
ops_per_sec=$(awk -v o="$ops" -v ms="$duration_ms" 'BEGIN { printf "%.2f", (o/(ms/1000.0)) }')

printf -- "Speed Test Summary\n"
printf -- "- Jobs: %s\n" "$JOBS"
printf -- "- Bytes/job: %s\n" "$BYTES_PER_JOB"
printf -- "- Duration: %s ms\n" "$duration_ms"
printf -- "- Throughput: %s ops/sec\n" "$ops_per_sec"

# Cleanup
rm -f "${TMP_DIR}"/*.sha256 "${TMP_DIR}"/*.gz 2>/dev/null || true
rm -f "${TMP_DIR}"/*.bin 2>/dev/null || true
rmdir "${TMP_DIR}" 2>/dev/null || true