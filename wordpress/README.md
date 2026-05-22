# WordPress example

A single-file WordPress plugin that automatically generates a featured image for every new post via [ThumbAPI](https://thumbapi.dev). Drop the post title in, get a published cover image back.

## What this does

When a post transitions from any status to `publish`, the plugin:

1. Sends the post title to `https://api.thumbapi.dev/v1/generate`.
2. Decodes the returned base64 WebP.
3. Uploads it to the WordPress Media Library.
4. Sets it as the post's **featured image** (post thumbnail).

If the post already has a featured image, the plugin does nothing — it only fills in missing ones.

## Install

1. Copy `thumbapi-on-publish.php` into your WordPress installation under:
   ```
   wp-content/plugins/thumbapi-on-publish/thumbapi-on-publish.php
   ```
2. In **WordPress admin → Plugins**, activate **"ThumbAPI — Auto Featured Image on Publish"**.
3. Go to **Settings → ThumbAPI** and paste your API key. Get one at [app.thumbapi.dev](https://app.thumbapi.dev).

That's it. Publish a post (or update an existing post to "Published" status) and the featured image gets generated automatically.

## Configuration

The settings page lets you pick:

- **API key** — required.
- **Format** — `blogpost` (default, 1200×630) is best for WordPress feature images and OG previews. You can also use `youtube`, `instagram`, `x`, or `linkedin`.
- **Image style** — `faceless` (default), `with-image`, or `with-logo`.

For brand-consistent output (your logo on every image), set the style to `with-logo` and upload your logo once in the [ThumbAPI dashboard](https://app.thumbapi.dev) under Assets — the plugin uses it automatically. See the [custom assets guide](https://thumbapi.dev/docs/custom-assets).

## Use cases

- **Blogs publishing daily** — every post gets a unique cover without designer time.
- **Headless WordPress** — generate the OG image automatically so social previews don't look broken.
- **Multi-author sites** — consistent visual style across all authors' posts.

## How it works internally

The plugin hooks into `transition_post_status` so it only fires once per publish, not on every save. It uses the WordPress HTTP API (`wp_remote_post`), the Media API (`wp_insert_attachment`, `wp_generate_attachment_metadata`), and `set_post_thumbnail`. All standard WP — no external PHP dependencies.

## See also

- [Blog Cover API](https://thumbapi.dev/blog-cover-api) — landing page for this use case.
- [OG Image API](https://thumbapi.dev/og-image-api) — for Open Graph and Twitter cards.
- [Full API reference](https://thumbapi.dev/docs/endpoints/generate).
