#!/usr/bin/env bash
# monitor.sh – Generates a snapshot report of system + container metrics in simple table form.
# Usage: ./scripts/monitor.sh
set -euo pipefail

# Colours disabled for raw pipe output

printf "\n===== SYSTEM =====\n"
free -m | awk 'NR==1{printf "|%s|%s|%s|%s|%s|%s|\n",$1,$2,$3,$4,$6,$7}; NR==2{printf "|Mem|%s MiB|%s MiB|%s MiB|%s MiB|%s MiB|\n",$2,$3,$4,$5,$7}'

printf "\n===== DISK =====\n"
df -h / | awk 'NR==1{print "|FS|Size|Used|Avail|Use%|Mounted|"}; NR==2{printf "|%s|%s|%s|%s|%s|%s|\n",$1,$2,$3,$4,$5,$6}'

printf "\n===== CONTAINERS =====\n"
podman ps --format "table {{.Names}}\t{{.Status}}\t{{.Image}}" | cat

# Airflow DAG progress (team_tasks)
printf "\n===== TEAM_TASKS PROGRESS =====\n"
POSTGRES_C=team-infra_postgres_1
if podman ps --format "{{.Names}}" | grep -q "^${POSTGRES_C}$"; then
  # Try to fetch counts; suppress errors if schema not yet present
  TOTAL=$(podman exec ${POSTGRES_C} psql -U airflow -d airflow -t -A -c "select count(*) from task_instance where dag_id='team_tasks';" 2>/dev/null | tr -d '[:space:]' || echo "0")
  SUCCESS=$(podman exec ${POSTGRES_C} psql -U airflow -d airflow -t -A -c "select count(*) from task_instance where dag_id='team_tasks' and state='success';" 2>/dev/null | tr -d '[:space:]' || echo "0")
  RUNNING=$(podman exec ${POSTGRES_C} psql -U airflow -d airflow -t -A -c "select count(*) from task_instance where dag_id='team_tasks' and state='running';" 2>/dev/null | tr -d '[:space:]' || echo "0")
  FAILED=$(podman exec ${POSTGRES_C} psql -U airflow -d airflow -t -A -c "select count(*) from task_instance where dag_id='team_tasks' and state='failed';" 2>/dev/null | tr -d '[:space:]' || echo "0")

  if [[ -z "$TOTAL" || "$TOTAL" == "" ]]; then TOTAL=0; fi
  if [[ $TOTAL -gt 0 ]]; then
    PCT=$(( 100 * SUCCESS / TOTAL ))
  else
    PCT=0
  fi

  printf "|الحالة|العدد|\n"
  printf "|نجاح|%s|\n" "$SUCCESS"
  printf "|تشغيل|%s|\n" "$RUNNING"
  printf "|فشل|%s|\n" "$FAILED"
  printf "|الإجمالي|%s|\n" "$TOTAL"
  printf "\n> نسبة الإنجاز: %s%%\n" "$PCT"
else
  echo "Postgres container غير متاح حالياً."
fi

printf "\n===== LOAD =====\n"
uptime | sed 's/^/| /' | sed 's/  / | /g'