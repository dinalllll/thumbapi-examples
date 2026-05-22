// Batch-generate thumbnails from a CSV file with one title per row.
// Includes retry on 5xx + concurrency limit to respect rate limits.
//
// CSV format (no header required):
//   "10 Tips to Grow Your Channel"
//   "How to Build a SaaS in 2026"
//   "5 Mistakes I Made as a Founder"
//
// Usage:
//   node batch-from-csv.js titles.csv

import { readFile, writeFile, mkdir } from "node:fs/promises";
import { resolve } from "node:path";

const API_KEY = process.env.THUMBAPI_KEY;
if (!API_KEY) {
  console.error("Set THUMBAPI_KEY in your environment first.");
  process.exit(1);
}

const CSV_PATH = process.argv[2];
if (!CSV_PATH) {
  console.error("Usage: node batch-from-csv.js <path-to-csv>");
  process.exit(1);
}

const CONCURRENCY = 3;
const MAX_RETRIES = 2;
const OUTPUT_DIR = "output";

async function generate(title, attempt = 0) {
  const res = await fetch("https://api.thumbapi.dev/v1/generate", {
    method: "POST",
    headers: {
      "x-api-key": API_KEY,
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      title,
      format: "youtube",
      imageStyle: "faceless",
      outputFormat: "webp",
    }),
  });

  if (res.status === 429) {
    throw new Error("Rate limited (429) — slow down or upgrade your plan.");
  }
  if (res.status >= 500 && attempt < MAX_RETRIES) {
    await new Promise((r) => setTimeout(r, 1500 * (attempt + 1)));
    return generate(title, attempt + 1);
  }
  if (!res.ok) {
    throw new Error(`HTTP ${res.status}: ${await res.text()}`);
  }

  return res.json();
}

function safeFileName(title) {
  return title.toLowerCase().replace(/[^a-z0-9]+/g, "-").slice(0, 60);
}

async function processOne(title, index, total) {
  const label = `[${index + 1}/${total}] ${title}`;
  try {
    const { image, dimensions } = await generate(title);
    const buffer = Buffer.from(image.split(",")[1], "base64");
    const filePath = resolve(OUTPUT_DIR, `${safeFileName(title)}.webp`);
    await writeFile(filePath, buffer);
    console.log(
      `${label} -> ${filePath} (${dimensions.width}x${dimensions.height})`,
    );
  } catch (err) {
    console.error(`${label} FAILED: ${err.message}`);
  }
}

async function runWithConcurrency(items, worker, limit) {
  const iterator = items.entries();
  const workers = Array.from({ length: limit }, async () => {
    for (const [index, item] of iterator) {
      await worker(item, index, items.length);
    }
  });
  await Promise.all(workers);
}

const raw = await readFile(CSV_PATH, "utf8");
const titles = raw
  .split(/\r?\n/)
  .map((line) => line.trim().replace(/^"|"$/g, ""))
  .filter(Boolean);

await mkdir(OUTPUT_DIR, { recursive: true });
console.log(`Generating ${titles.length} thumbnails (concurrency: ${CONCURRENCY})...`);

await runWithConcurrency(titles, processOne, CONCURRENCY);

console.log("Done.");
