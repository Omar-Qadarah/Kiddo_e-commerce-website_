# Google sign-in setup

1. In Google Cloud Console, create or select a project and configure the OAuth consent screen.
2. Create an OAuth client with application type Web application.
3. Add http://localhost to Authorized JavaScript origins.
4. Add this exact URL to Authorized redirect URIs:
   http://localhost/php-project/Kiddo_e-commerce-website_/auth/google_callback.php
5. Add GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET from that OAuth client to the existing .env. Preserve any settings already there, including the chatbot key. Keep .env private and do not commit it. Use .env.example as a reference.
6. If the project runs at a different host or URL, update GOOGLE_REDIRECT_URI in .env and enter the exact same URL in Google Cloud Console.
7. Ensure the XAMPP PHP extensions curl and openssl are enabled, then restart Apache.

The callback validates Google's signed ID token on the server. Existing users with a matching Google-verified email are linked without changing their role; new Google users receive the database's customer default role.