#!/usr/bin/env python3
"""
team_capacity.py — Compute aggregate capacity for a team of individuals.

Usage:
    python team_capacity.py data.csv [--hours-per-day 8] [--days-per-week 5]

CSV format (header required):
    id,capacity_per_hour
    bc-4b7c40ee-61da-4e40-a1b5-00130ebeab05,0.50
    bc-c6ad5e89-6078-478a-bc8f-9a0a9b2eaf07,0.42
    ...

Definitions:
    capacity_per_hour:   Maximum throughput units an individual can deliver in one hour when all resources are available.

The script outputs:
    • Team capacity per hour (sum of individual capacities)
    • Team capacity per standard workday (hours_per_day)
    • Team capacity per standard workweek (hours_per_day × days_per_week)
    • Average individual capacity per hour

Note: Adjust --hours-per-day and --days-per-week to fit your organization’s calendar.
"""

from __future__ import annotations

import argparse
import csv
import statistics
import sys
from pathlib import Path
from typing import List, Tuple


def read_capacities(csv_path: Path) -> List[float]:
    """Read capacities from CSV file and return list of capacities per hour."""
    if not csv_path.exists():
        raise FileNotFoundError(f"CSV file not found: {csv_path}")

    capacities: List[float] = []
    with csv_path.open(newline="") as fp:
        reader = csv.DictReader(fp)
        # Validate header
        if "capacity_per_hour" not in reader.fieldnames:  # type: ignore[arg-type]
            raise ValueError(
                "CSV must contain a 'capacity_per_hour' column in the header."
            )

        for row in reader:
            try:
                capacities.append(float(row["capacity_per_hour"]))
            except (ValueError, KeyError):
                raise ValueError(
                    f"Invalid capacity value in row: {row}. Expected numeric 'capacity_per_hour'."
                )
    if not capacities:
        raise ValueError("No capacity data found in CSV.")
    return capacities


def compute_team_metrics(
    capacities: List[float], hours_per_day: float, days_per_week: float
) -> Tuple[float, float, float, float]:
    """Return tuple of (hourly, daily, weekly, average_individual) capacities."""
    team_hourly = sum(capacities)
    team_daily = team_hourly * hours_per_day
    team_weekly = team_daily * days_per_week
    avg_individual = statistics.mean(capacities)
    return team_hourly, team_daily, team_weekly, avg_individual


def main(argv: List[str] | None = None) -> None:  # noqa: D401
    """Parse CLI args and display team capacity metrics."""
    parser = argparse.ArgumentParser(description="Compute team capacity from CSV input.")
    parser.add_argument(
        "csv_file",
        help="Path to CSV file with columns: id, capacity_per_hour",
        type=Path,
    )
    parser.add_argument(
        "--hours-per-day",
        type=float,
        default=8,
        help="Working hours in a standard day (default: 8)",
    )
    parser.add_argument(
        "--days-per-week",
        type=float,
        default=5,
        help="Working days in a standard week (default: 5)",
    )

    args = parser.parse_args(argv)

    try:
        capacities = read_capacities(args.csv_file)
        hourly, daily, weekly, avg = compute_team_metrics(
            capacities, args.hours_per_day, args.days_per_week
        )
    except Exception as exc:
        sys.exit(f"Error: {exc}")

    print("\n=== Team Capacity Metrics ===")
    print(f"Members counted         : {len(capacities)}")
    print(f"Team capacity per hour  : {hourly:.2f} units/hour")
    print(
        f"Team capacity per day   : {daily:.2f} units (assuming {args.hours_per_day} h/day)"
    )
    print(
        f"Team capacity per week  : {weekly:.2f} units (assuming {args.days_per_week} d/week)"
    )
    print(f"Average individual/hr   : {avg:.2f} units/hour")


if __name__ == "__main__":
    main()