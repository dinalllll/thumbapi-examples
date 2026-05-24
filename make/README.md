# Make.com example

One-click scenario template for the [ThumbAPI](https://thumbapi.dev) generate endpoint.

## Clone the scenario

Public scenario template — drops straight into your Make.com account:

**[Clone Scenario in Make.com →](https://eu1.make.com/public/shared-scenario/QQ4D5gXkxnn/integration-tools-http)**

## What it includes

- **Set variable** (placeholder trigger) — sample title for testing.
- **HTTP → Make a request** — POST to `https://api.thumbapi.dev/v1/generate` with `format: youtube` and `imageStyle: faceless`.
- **Tools → Set multiple variables** — strips the `data:image/webp;base64,` prefix, calls `toBinary()` so file modules can consume the buffer directly, and computes a unique `fileName` and `mimeType`.

## After cloning

> ⚠️ **Important:** the cloned scenario ships with the public test key `thumbapi_test` in the `x-api-key` header. That key returns a static placeholder image — useful for confirming the wiring works, but it does **not** generate real thumbnails. To generate actual content, sign up at [app.thumbapi.dev](https://app.thumbapi.dev) (free tier: 5 generations/month, no credit card) and swap it for your own API key.

1. **Sign up at [app.thumbapi.dev](https://app.thumbapi.dev)** (free, no credit card) and copy your personal API key from the dashboard.
2. Open the cloned scenario in your Make dashboard.
3. **HTTP module → Headers:** replace `thumbapi_test` with the API key you just copied. This is the only required change to start generating real thumbnails.
4. **Replace the Set variable placeholder** with your real trigger — YouTube Watch Videos, Google Sheets Watch Rows, RSS, Webhook, etc.
5. **Add your destination module** after the Set-multiple-variables step:
   - **Google Drive → Upload a file:** map `imageData` to File data, `fileName` to File name.
   - **WordPress → Update post featured image:** map `imageData`.
   - **Slack → Send a message:** include `fileName` in the message text, attach `imageData`.
6. Click **Run once** to test. Activate the scenario when it works end-to-end.

## See also

- [Make.com integration guide on ThumbAPI](https://thumbapi.dev/integrations/make) — full walkthrough including router and batch patterns.
- [n8n integration](https://thumbapi.dev/integrations/n8n) — alternative no-code workflow tool.
- [API reference](https://thumbapi.dev/docs/endpoints/generate).
