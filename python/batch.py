"""
Batch-generate thumbnails from a CSV file with one title per row.
Retries on 5xx, runs N requests concurrently via a thread pool.

CSV format (no header required):
    "10 Tips to Grow Your Channel"
    "How to Build a SaaS in 2026"

Usage:
    python batch.py titles.csv

Requires: pip install requests
"""

import base64
import csv
import os
import pathlib
import re
import sys
import time
from concurrent.futures import ThreadPoolExecutor, as_completed

import requests

API_KEY = os.environ.get("THUMBAPI_KEY")
if not API_KEY:
    sys.exit("Set THUMBAPI_KEY in your environment first.")

if len(sys.argv) < 2:
    sys.exit("Usage: python batch.py <path-to-csv>")

CSV_PATH = pathlib.Path(sys.argv[1])
CONCURRENCY = 3
MAX_RETRIES = 2
OUTPUT_DIR = pathlib.Path("output")
OUTPUT_DIR.mkdir(exist_ok=True)


def safe_file_name(title: str) -> str:
    return re.sub(r"[^a-z0-9]+", "-", title.lower())[:60]


def generate(title: str, attempt: int = 0) -> dict:
    res = requests.post(
        "https://api.thumbapi.dev/v1/generate",
        headers={"x-api-key": API_KEY, "Content-Type": "application/json"},
        json={
            "title": title,
            "format": "youtube",
            "imageStyle": "faceless",
            "outputFormat": "webp",
        },
        timeout=60,
    )

    if res.status_code == 429:
        raise RuntimeError("Rate limited (429) — slow down or upgrade.")
    if res.status_code >= 500 and attempt < MAX_RETRIES:
        time.sleep(1.5 * (attempt + 1))
        return generate(title, attempt + 1)
    if not res.ok:
        raise RuntimeError(f"HTTP {res.status_code}: {res.text}")

    return res.json()


def process_one(title: str) -> tuple[str, str | None]:
    try:
        data = generate(title)
        binary = base64.b64decode(data["image"].split(",", 1)[1])
        path = OUTPUT_DIR / f"{safe_file_name(title)}.webp"
        path.write_bytes(binary)
        dims = data["dimensions"]
        return title, f"OK -> {path} ({dims['width']}x{dims['height']})"
    except Exception as e:  # noqa: BLE001 - we want any failure surfaced
        return title, f"FAILED: {e}"


with CSV_PATH.open(newline="") as f:
    titles = [row[0].strip() for row in csv.reader(f) if row and row[0].strip()]

print(f"Generating {len(titles)} thumbnails (concurrency: {CONCURRENCY})...")

with ThreadPoolExecutor(max_workers=CONCURRENCY) as pool:
    futures = {pool.submit(process_one, t): t for t in titles}
    for i, fut in enumerate(as_completed(futures), 1):
        title, result = fut.result()
        print(f"[{i}/{len(titles)}] {title} -> {result}")

print("Done.")
