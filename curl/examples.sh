#!/usr/bin/env bash
# Minimal cURL examples for the ThumbAPI generate endpoint.
#
# Set your API key first:
#     export THUMBAPI_KEY="your_api_key_here"
#
# Then run any of the examples below.

set -euo pipefail

: "${THUMBAPI_KEY:?Set THUMBAPI_KEY before running examples}"

ENDPOINT="https://api.thumbapi.dev/v1/generate"

# ---------------------------------------------------------------------------
# 1. Faceless YouTube thumbnail (no person, no logo)
# ---------------------------------------------------------------------------
echo "Generating faceless YouTube thumbnail..."
curl -sS -X POST "$ENDPOINT" \
  -H "x-api-key: $THUMBAPI_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "10 Tips to Grow Your YouTube Channel",
    "format": "youtube",
    "imageStyle": "faceless",
    "outputFormat": "webp"
  }' \
  | jq -r '.image' | sed 's|^data:image/webp;base64,||' | base64 -d > youtube-faceless.webp
echo "  -> youtube-faceless.webp"

# ---------------------------------------------------------------------------
# 2. Blog cover image (1200x630)
# ---------------------------------------------------------------------------
echo "Generating blog cover image..."
curl -sS -X POST "$ENDPOINT" \
  -H "x-api-key: $THUMBAPI_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Why We Migrated From Postgres to ClickHouse",
    "format": "blogpost",
    "imageStyle": "faceless",
    "outputFormat": "webp"
  }' \
  | jq -r '.image' | sed 's|^data:image/webp;base64,||' | base64 -d > blog-cover.webp
echo "  -> blog-cover.webp"

# ---------------------------------------------------------------------------
# 3. With a personal photo (requires person.jpg in current directory)
# ---------------------------------------------------------------------------
if [[ -f person.jpg ]]; then
  echo "Generating personalized thumbnail with person.jpg..."
  PERSON_B64=$(base64 < person.jpg | tr -d '\n')
  curl -sS -X POST "$ENDPOINT" \
    -H "x-api-key: $THUMBAPI_KEY" \
    -H "Content-Type: application/json" \
    -d "{
      \"title\": \"How I Learned Public Speaking in 30 Days\",
      \"format\": \"youtube\",
      \"imageStyle\": \"with-image\",
      \"personImage\": \"data:image/jpeg;base64,${PERSON_B64}\",
      \"outputFormat\": \"webp\"
    }" \
    | jq -r '.image' | sed 's|^data:image/webp;base64,||' | base64 -d > personalized.webp
  echo "  -> personalized.webp"
else
  echo "Skipping personal-image example (drop a person.jpg in this folder to enable it)."
fi

# ---------------------------------------------------------------------------
# 4. Public test key — instant static placeholder, no account needed
# ---------------------------------------------------------------------------
echo "Generating with the public test key (no real generation, instant placeholder)..."
curl -sS -X POST "$ENDPOINT" \
  -H "x-api-key: thumbapi_test" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Test request from cURL example",
    "format": "youtube",
    "imageStyle": "faceless",
    "outputFormat": "webp"
  }' \
  | jq -r '.image' | sed 's|^data:image/webp;base64,||' | base64 -d > test-output.webp
echo "  -> test-output.webp"

echo "Done."
