# Kiddo's e-commerce website

## Gemini shopping assistant

The storefront includes a floating toy-helper chat button. The PHP endpoint sends the shopper's message and a short list of active, in-stock products to Gemini. It does not send account, order, address, or payment information.

1. Create an API key in [Google AI Studio](https://aistudio.google.com/app/apikey).
2. Keep the key out of source files and Git. Configure it as the `GEMINI_API_KEY` environment variable for Apache. For a local XAMPP setup, add `SetEnv GEMINI_API_KEY "your-key"` to the Apache server configuration and restart Apache.
3. Optionally set `GEMINI_MODEL` to another model available to your API key. The default is `gemini-3.5-flash-lite`.
4. Make sure PHP's cURL extension is enabled and the `kiddos` database is running.

The free Gemini tier has project eligibility and usage limits. Google may use content sent through its free tier to improve its products, so the assistant is deliberately limited to public product-catalog information.