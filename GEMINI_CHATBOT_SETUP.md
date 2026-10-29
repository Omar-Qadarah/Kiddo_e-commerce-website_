# Gemini shopping assistant bundle

Copy the contents of this folder into the matching paths in `C:\xampp\htdocs\php-project\Kiddo_e-commerce-website_`.

## Add the files

- Copy `ai/shopping_assistant.php` to the repo's `ai/` folder.
- Copy `assets/css/assistant.css` and `assets/js/shopping-assistant.js` into the matching asset folders.
- Add the snippet in `includes/header-chatbot-link.txt` beside the storefront stylesheet link.
- Add the snippet in `includes/footer-chatbot-widget.txt` near the end of the shared storefront footer, before `</body>`.

The endpoint reads the `products` and `categories` tables for active, in-stock product context. It expects the project's existing `config/config.php` and `config/Database.php` files.

## Configure Gemini

1. Create an API key in [Google AI Studio](https://aistudio.google.com/app/apikey).
2. Set `GEMINI_API_KEY` in Apache's environment. In a local XAMPP Apache configuration, add `SetEnv GEMINI_API_KEY "your-key"` and restart Apache. Keep the key out of source files and Git.
3. The default model is `gemini-3.5-flash-lite`. You can optionally set `GEMINI_MODEL` to another model available to your API key.
4. Make sure PHP's cURL extension is enabled and the `kiddos` database is running.

The free Gemini tier has eligibility and usage limits, and Google may use free-tier prompts and responses to improve its products. The endpoint only adds public product-catalog data from the app; shoppers are reminded not to enter personal or payment details.
