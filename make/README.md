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

1. Open the cloned scenario in your Make dashboard.
2. **HTTP module → Headers:** replace `YOUR_API_KEY` with your real key from [app.thumbapi.dev](https://app.thumbapi.dev).
3. **Replace the Set variable placeholder** with your real trigger — YouTube Watch Videos, Google Sheets Watch Rows, RSS, Webhook, etc.
4. **Add your destination module** after the Set-multiple-variables step:
   - **Google Drive → Upload a file:** map `imageData` to File data, `fileName` to File name.
   - **WordPress → Update post featured image:** map `imageData`.
   - **Slack → Send a message:** include `fileName` in the message text, attach `imageData`.
5. Click **Run once** to test. Activate the scenario when it works end-to-end.

## See also

- [Make.com integration guide on ThumbAPI](https://thumbapi.dev/integrations/make) — full walkthrough including router and batch patterns.
- [n8n integration](https://thumbapi.dev/integrations/n8n) — alternative no-code workflow tool.
- [API reference](https://thumbapi.dev/docs/endpoints/generate).
