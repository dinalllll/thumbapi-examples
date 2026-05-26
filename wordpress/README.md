# WordPress

There are two ways to use ThumbAPI inside WordPress.

## 👉 Most users: the official plugin

The recommended way is the official **ThumbAPI — Auto Featured Images** plugin. It adds a one-click "Generate" button to the post editor (block + classic), an integrated Media Library picker for "With image" / "With logo" styles, a settings page, a content-category selector, and full WordPress.org Plugin Directory compliance.

- **Plugin page on thumbapi.dev**: [https://thumbapi.dev/integrations/wordpress](https://thumbapi.dev/integrations/wordpress)
- **Direct download (zip)**: [https://thumbapi.dev/downloads/thumbapi-wordpress.zip](https://thumbapi.dev/downloads/thumbapi-wordpress.zip)
- **WordPress.org listing**: pending review — search "ThumbAPI" inside *WordPress admin → Plugins → Add New* once approved.

Install: WordPress admin → Plugins → Add New Plugin → Upload Plugin → choose the zip → Install Now → Activate. Then Settings → ThumbAPI → paste your API key.

## 🧑‍💻 Developers: single-file auto-on-publish example

This folder also contains a **minimal single-file example** showing how to call the ThumbAPI endpoint from PHP and set the result as a featured image. Unlike the official plugin, this example fires **automatically** on `transition_post_status` — every newly published post that doesn't already have a featured image gets one without any UI interaction.

Useful when:

- You're building a custom integration and want to see the raw `wp_remote_post` + Media Library sideload flow in one file (~100 lines).
- You want fully automated cover generation on publish, with no editor button.
- You want to fork and customize the behaviour (e.g. only certain post types, custom format/style logic, multi-site rollouts).

### What this example does

When a post transitions from any status to `publish`, the plugin:

1. Sends the post title to `https://api.thumbapi.dev/v1/generate`.
2. Decodes the returned base64 WebP.
3. Uploads it to the WordPress Media Library.
4. Sets it as the post's **featured image** (post thumbnail).

If the post already has a featured image, the plugin does nothing — it only fills in missing ones.

### Install (example)

1. Copy `thumbapi-on-publish.php` into your WordPress installation under:
   ```
   wp-content/plugins/thumbapi-on-publish/thumbapi-on-publish.php
   ```
2. In **WordPress admin → Plugins**, activate **"ThumbAPI — Auto Featured Image on Publish"**.
3. Go to **Settings → ThumbAPI on Publish** and paste your API key. Get one at [app.thumbapi.dev](https://app.thumbapi.dev).

Publish a post (or update an existing post to "Published" status) and the featured image gets generated automatically.

### Configuration

The settings page lets you pick:

- **API key** — required.
- **Format** — `blogpost` (default, 1200×630) is best for WordPress featured images and OG previews. You can also use `youtube`, `instagram`, `x`, or `linkedin`.
- **Image style** — `faceless` (default), `with-image`, or `with-logo`.

For brand-consistent output (your logo on every image), set the style to `with-logo` and upload your logo once in the [ThumbAPI dashboard](https://app.thumbapi.dev) under Assets — the plugin uses it automatically. See the [custom assets guide](https://thumbapi.dev/docs/custom-assets).

### Use cases for the example

- **Blogs publishing daily** — every post gets a unique cover without designer time.
- **Headless WordPress** — generate the OG image automatically so social previews don't look broken.
- **Multi-author sites** — consistent visual style across all authors' posts.

### How it works internally

The plugin hooks into `transition_post_status` so it only fires once per publish, not on every save. It uses the WordPress HTTP API (`wp_remote_post`), the Media API (`wp_insert_attachment`, `wp_generate_attachment_metadata`), and `set_post_thumbnail`. All standard WP — no external PHP dependencies.

## See also

- [Blog Cover API](https://thumbapi.dev/blog-cover-api) — landing page for this use case.
- [OG Image API](https://thumbapi.dev/og-image-api) — for Open Graph and Twitter cards.
- [Full API reference](https://thumbapi.dev/docs/endpoints/generate).
