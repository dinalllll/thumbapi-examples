// Generate a personalized thumbnail with your face composited in.
// Requires Node.js 18+. Uses the `with-image` style and a local JPEG.

import { readFile, writeFile } from "node:fs/promises";

const API_KEY = process.env.THUMBAPI_KEY;
if (!API_KEY) {
  console.error("Set THUMBAPI_KEY in your environment first.");
  process.exit(1);
}

const PERSON_IMAGE_PATH = process.argv[2] ?? "person.jpg";

const personBuffer = await readFile(PERSON_IMAGE_PATH);
const personBase64 = personBuffer.toString("base64");
const personDataUri = `data:image/jpeg;base64,${personBase64}`;

const response = await fetch("https://api.thumbapi.dev/v1/generate", {
  method: "POST",
  headers: {
    "x-api-key": API_KEY,
    "Content-Type": "application/json",
  },
  body: JSON.stringify({
    title: "How I Learned Public Speaking in 30 Days",
    format: "youtube",
    imageStyle: "with-image",
    personImage: personDataUri,
    outputFormat: "webp",
  }),
});

if (!response.ok) {
  const error = await response.text();
  console.error(`Request failed (${response.status}):`, error);
  process.exit(1);
}

const { image, dimensions } = await response.json();
const outputBuffer = Buffer.from(image.split(",")[1], "base64");

await writeFile("personalized-thumbnail.webp", outputBuffer);
console.log(
  `Saved personalized-thumbnail.webp (${dimensions.width}x${dimensions.height})`,
);

// Tip: if you generate personalized thumbnails frequently, upload your
// photo once in the ThumbAPI dashboard under Assets > Personal Photo,
// then drop the `personImage` field and set `usePersonalPhoto: true`
// instead — no need to send the base64 on every request.
