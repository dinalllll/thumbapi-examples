# Node.js examples

Working Node.js examples for the [ThumbAPI](https://thumbapi.dev) generate endpoint.

## Setup

```bash
# Requires Node.js 18+ (built-in fetch). Tested on Node 20 and 22.
export THUMBAPI_KEY="your_api_key_here"
```

Get an API key at [app.thumbapi.dev](https://app.thumbapi.dev) — free tier is 5 generations/month, no credit card required.

## Examples

- **`basic-generate.js`** — minimal POST request, save the WebP to disk.
- **`with-personal-image.js`** — `with-image` style: include a person's face from a local JPEG.
- **`batch-from-csv.js`** — read titles from a CSV, generate one thumbnail per row with retry + concurrency control.

## Run

```bash
node basic-generate.js
node with-personal-image.js
node batch-from-csv.js titles.csv
```

## See also

- [JavaScript / Node.js guide on the ThumbAPI blog](https://thumbapi.dev/blog/generate-thumbnails-javascript-nodejs)
- [Full API reference](https://thumbapi.dev/docs/endpoints/generate)
