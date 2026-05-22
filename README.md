# ThumbAPI Examples

Working code samples for the [ThumbAPI](https://thumbapi.dev) thumbnail generation REST API. Generate AI-powered, platform-optimized thumbnails for YouTube, Instagram, X, LinkedIn, blog posts, and Open Graph images from any title with a single POST request.

> **What is ThumbAPI?** One REST endpoint, multiple platform formats, three styles (faceless / with-image / with-logo). Production-ready images returned in under 25 seconds. Free tier available — no credit card required. See [thumbapi.dev/docs](https://thumbapi.dev/docs) for full reference.

## Quick start

```bash
curl -X POST https://api.thumbapi.dev/v1/generate \
  -H "x-api-key: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "10 Tips to Grow Your Channel",
    "format": "youtube",
    "imageStyle": "faceless"
  }'
```

Response:

```json
{
  "image": "data:image/webp;base64,UklGR...",
  "format": "youtube",
  "outputFormat": "webp",
  "dimensions": { "width": 1280, "height": 720 }
}
```

## Examples in this repo

| Language / tool | What it shows |
| --- | --- |
| [Node.js](./nodejs) | Basic generate, with-image style, batch from CSV |
| [Python](./python) | Basic generate, batch with concurrency + retry |
| [cURL](./curl) | Minimal shell examples for every style and format |
| [WordPress](./wordpress) | Single-file plugin: auto-generate the featured image on post publish |
| [n8n](./n8n) | Importable workflow JSON: YouTube RSS → ThumbAPI → Google Drive |

## Getting an API key

1. Sign up at [app.thumbapi.dev](https://app.thumbapi.dev) (free tier: 5 generations/month, no credit card).
2. Copy your API key from the dashboard.
3. Set it as `THUMBAPI_KEY` in your environment, or pass it directly via the `x-api-key` header.

A public test key `thumbapi_test` is available that returns a static placeholder image — useful for wiring up integrations before you've signed up. See [thumbapi.dev/integrations/n8n](https://thumbapi.dev/integrations/n8n) for an example.

## Supported formats

| `format` | Dimensions | Use case |
| --- | --- | --- |
| `youtube` | 1280×720 | YouTube video thumbnails |
| `instagram` | 1080×1080 | Instagram feed posts |
| `x` | 1200×675 | X (Twitter) link previews |
| `linkedin` | 1200×627 | LinkedIn post images |
| `blogpost` | 1200×630 | Blog cover images, OG/Twitter cards |

## Supported image styles

| `imageStyle` | Description |
| --- | --- |
| `faceless` | Text-and-graphic design, no people. Best for automated content and faceless channels. |
| `with-image` | Composites a person's photo into the thumbnail. Send `personImage` as base64 or use a saved asset with `usePersonalPhoto: true`. |
| `with-logo` | Overlays a brand logo. Send `personImage` as base64 PNG or use a saved asset. |

## Use cases

- [YouTube Thumbnail API](https://thumbapi.dev/youtube-thumbnail-api) — 1280×720 thumbnails from a title.
- [OG Image API](https://thumbapi.dev/og-image-api) — 1200×630 Open Graph and Twitter cards.
- [Blog Cover API](https://thumbapi.dev/blog-cover-api) — Cover images for articles.
- [Personalized Thumbnails](https://thumbapi.dev/use-cases/personalized-thumbnails) — Your face on every image.
- [Branded Thumbnails](https://thumbapi.dev/use-cases/branded-thumbnails) — Brand logo on every image.
- [Faceless Thumbnails](https://thumbapi.dev/features/faceless-thumbnails) — Text-only thumbnail designs.

## Workflow integrations

- [n8n integration guide](https://thumbapi.dev/integrations/n8n) — copy/paste workflow JSON included in this repo.
- [Make.com integration guide](https://thumbapi.dev/integrations/make)
- [Zapier integration guide](https://thumbapi.dev/integrations/zapier)

## Documentation

- Full [API reference](https://thumbapi.dev/docs/endpoints/generate)
- [Authentication](https://thumbapi.dev/docs/authentication)
- [Rate limits](https://thumbapi.dev/docs/rate-limits)
- [Custom asset datasets](https://thumbapi.dev/docs/custom-assets) — upload reference images for brand-consistent generation
- [Error codes](https://thumbapi.dev/docs/errors)
- [Changelog](https://thumbapi.dev/changelog)

## License

Examples in this repository are released under the MIT license — use them freely in your own projects. See [LICENSE](./LICENSE).

## Contributing

Found a bug in an example, or want to add a sample in a new language (Go, Rust, PHP, Ruby)? Open a pull request. Or open an issue if you'd like to see a specific integration example.

---

Built and maintained by [Aldin Kozica](https://www.linkedin.com/in/aldin-kozica-074b111ab/), creator of [ThumbAPI](https://thumbapi.dev).
