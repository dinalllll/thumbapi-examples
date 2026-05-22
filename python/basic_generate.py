"""
Minimal example: generate a YouTube thumbnail and save it to disk.
Requires: pip install requests
"""

import base64
import os
import sys

import requests

API_KEY = os.environ.get("THUMBAPI_KEY")
if not API_KEY:
    sys.exit("Set THUMBAPI_KEY in your environment first.")

response = requests.post(
    "https://api.thumbapi.dev/v1/generate",
    headers={
        "x-api-key": API_KEY,
        "Content-Type": "application/json",
    },
    json={
        "title": "10 Tips to Grow Your YouTube Channel",
        "format": "youtube",
        "imageStyle": "faceless",
        "outputFormat": "webp",
    },
    timeout=60,
)

if not response.ok:
    sys.exit(f"Request failed ({response.status_code}): {response.text}")

data = response.json()
image = data["image"]
dims = data["dimensions"]

# `image` is a data URI like "data:image/webp;base64,UklGR..."
# Strip the prefix to get just the base64 payload.
base64_payload = image.split(",", 1)[1]
binary = base64.b64decode(base64_payload)

with open("thumbnail.webp", "wb") as f:
    f.write(binary)

print(f"Saved thumbnail.webp ({dims['width']}x{dims['height']}, {len(binary)} bytes)")
