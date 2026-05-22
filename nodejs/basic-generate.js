// Minimal example: generate a YouTube thumbnail and save it to disk.
// Requires Node.js 18+ for built-in fetch.

import { writeFile } from "node:fs/promises";

const API_KEY = process.env.THUMBAPI_KEY;
if (!API_KEY) {
  console.error("Set THUMBAPI_KEY in your environment first.");
  process.exit(1);
}

const response = await fetch("https://api.thumbapi.dev/v1/generate", {
  method: "POST",
  headers: {
    "x-api-key": API_KEY,
    "Content-Type": "application/json",
  },
  body: JSON.stringify({
    title: "10 Tips to Grow Your YouTube Channel",
    format: "youtube",
    imageStyle: "faceless",
    outputFormat: "webp",
  }),
});

if (!response.ok) {
  const error = await response.text();
  console.error(`Request failed (${response.status}):`, error);
  process.exit(1);
}

const { image, format, dimensions } = await response.json();

// `image` is a data URI like "data:image/webp;base64,UklGR..."
// Strip the prefix to get just the base64 payload.
const base64 = image.split(",")[1];
const buffer = Buffer.from(base64, "base64");

await writeFile("thumbnail.webp", buffer);

console.log(
  `Saved thumbnail.webp (${format}, ${dimensions.width}x${dimensions.height}, ${buffer.length} bytes)`,
);
