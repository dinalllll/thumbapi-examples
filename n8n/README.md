# n8n examples

n8n workflow JSON for the [ThumbAPI](https://thumbapi.dev) integration.

## `youtube-rss-to-drive.json`

End-to-end workflow:

1. **RSS Feed Read** polls a YouTube channel's public RSS feed.
2. On new video, **HTTP Request** POSTs the video title to ThumbAPI.
3. **Code node** decodes the base64 image response into binary.
4. **Google Drive** uploads the thumbnail to a folder.

## Import

1. Open your n8n instance.
2. Top-right menu → **Import from File** (or paste JSON).
3. Select `youtube-rss-to-drive.json`.
4. Before activating:
   - Add a **Header Auth credential** named `ThumbAPI` with `Header Name: x-api-key` and your API key as the value.
   - Replace `YOUR_CHANNEL_ID` in the RSS Feed Read node with your actual YouTube channel ID (find it in YouTube Studio → Settings → Channel → Advanced).
   - Replace `YOUR_GOOGLE_DRIVE_FOLDER_ID` in the Google Drive node, and attach your Google Drive OAuth credential.

## Test key

To preview the flow without burning real API credits, swap the credential to use the public test key (`thumbapi_test`). It returns a static placeholder image regardless of input. See [thumbapi.dev/integrations/n8n](https://thumbapi.dev/integrations/n8n) for the test workflow.

## See also

- [How to Auto-Generate YouTube Thumbnails with n8n (full walkthrough)](https://thumbapi.dev/blog/auto-generate-youtube-thumbnails-n8n)
- [n8n integration guide on ThumbAPI](https://thumbapi.dev/integrations/n8n)
- [Full API reference](https://thumbapi.dev/docs/endpoints/generate)
