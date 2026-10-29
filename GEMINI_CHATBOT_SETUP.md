# Gemini shopping assistant setup

The floating chat button is included by the shared storefront layout. Its endpoint loads the Gemini key from the project-root `.env` file and uses active, in-stock products as catalog context.

## Add a fresh API key

The key previously pasted into chat is compromised. Revoke it in [Google AI Studio](https://aistudio.google.com/app/apikey), then create a replacement. Do not paste the replacement into chat, PHP, JavaScript, or Git.

Open the existing `.env` file in this project root and set:

```ini
GEMINI_API_KEY="YOUR_NEW_KEY"
```

The project-root `.gitignore` excludes `.env`, and `.htaccess` denies browser requests for `.env` and `.env.example`. Keep both protections in place. If you move `.env`, update the path in `ai/shopping_assistant.php`.

## Requirements

- PHP cURL extension enabled.
- The `kiddos` database running and reachable.
- The active storefront includes `includes/shop_header.php` and `includes/footer.php`.

The default model is `gemini-3.5-flash-lite`. The floating icon can display without an API key; the key is needed only when a shopper sends a message. Restart Apache after changing its configuration, then reload the storefront.