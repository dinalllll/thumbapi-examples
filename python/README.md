# Python examples

Working Python examples for the [ThumbAPI](https://thumbapi.dev) generate endpoint.

## Setup

```bash
# Python 3.9+ recommended.
pip install requests

export THUMBAPI_KEY="your_api_key_here"
```

Get an API key at [app.thumbapi.dev](https://app.thumbapi.dev) — free tier is 5 generations/month, no credit card required.

## Examples

- **`basic_generate.py`** — minimal POST request, save the WebP to disk.
- **`batch.py`** — batch-generate from a CSV with retry and a thread pool.

## Run

```bash
python basic_generate.py
python batch.py titles.csv
```

## See also

- [Batch thumbnail generation in Python — full guide](https://thumbapi.dev/blog/batch-thumbnail-generation-python)
- [Full API reference](https://thumbapi.dev/docs/endpoints/generate)
