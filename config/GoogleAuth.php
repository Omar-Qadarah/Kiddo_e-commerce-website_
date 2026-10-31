<?php
 
final class GoogleAuth
{
    private const AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';
    private const TOKEN_URL = 'https://oauth2.googleapis.com/token';
    private const DISCOVERY_URL = 'https://accounts.google.com/.well-known/openid-configuration';

    private static function env(string $name): string
    {
        $value = getenv($name);
        if ($value !== false && $value !== '') {
            return trim($value, " \t\n\r\0\x0B\"'");
        }
        $envFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';
        if (!is_file($envFile) || !is_readable($envFile)) {
            return '';
        }
        foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
            if (preg_match('/^\s*' . preg_quote($name, '/') . '\s*=\s*(.*?)\s*$/', $line, $match)) {
                return trim($match[1], " \t\"'");
            }
        }
        return '';
    }

    public static function isConfigured(): bool
    {
        return self::env('GOOGLE_CLIENT_ID') !== '' && self::env('GOOGLE_CLIENT_SECRET') !== '';
    }

    private static function settings(): array
    {
        $clientId = self::env('GOOGLE_CLIENT_ID');
        $clientSecret = self::env('GOOGLE_CLIENT_SECRET');
        if ($clientId === '' || $clientSecret === '') {
            throw new RuntimeException('Google sign-in is not configured.');
        }
        $redirectUri = self::env('GOOGLE_REDIRECT_URI');
        if ($redirectUri === '') {
            $redirectUri = 'http://localhost' . BASE_URL . '/auth/google_callback.php';
        }
        return [$clientId, $clientSecret, $redirectUri];
    }

    public static function authorizationUrl(string $state, string $nonce): string
    {
        [$clientId, , $redirectUri] = self::settings();
        return self::AUTH_URL . '?' . http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'nonce' => $nonce,
            'prompt' => 'select_account',
        ], '', '&', PHP_QUERY_RFC3986);
    }

    public static function verifyAuthorizationCode(string $code, string $expectedNonce): array
    {
        [$clientId, $clientSecret, $redirectUri] = self::settings();
        $tokenResponse = self::requestJson(self::TOKEN_URL, [
            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ]);
        $idToken = $tokenResponse['id_token'] ?? '';
        if (!is_string($idToken) || $idToken === '') {
            throw new RuntimeException('Google did not return an identity token.');
        }

         
        $parts = explode('.', $idToken);
        if (count($parts) !== 3) {
            throw new RuntimeException('Google returned an invalid identity token.');
        }
        [$encodedHeader, $encodedClaims, $encodedSignature] = $parts;
        $header = self::decodeJwtPart($encodedHeader);
        $claims = self::decodeJwtPart($encodedClaims);
        $signature = self::base64UrlDecode($encodedSignature);
        if (($header['alg'] ?? '') !== 'RS256' || empty($header['kid']) || $signature === false) {
            throw new RuntimeException('Google returned an unsupported identity token.');
        }

        $discovery = self::requestJson(self::DISCOVERY_URL);
        if (($discovery['issuer'] ?? '') !== 'https://accounts.google.com' ||
            empty($discovery['jwks_uri']) || !is_string($discovery['jwks_uri'])) {
            throw new RuntimeException('Could not load Google signing keys.');
        }
        $keySet = self::requestJson($discovery['jwks_uri']);
        $jwk = null;
        foreach (($keySet['keys'] ?? []) as $key) {
            if (($key['kid'] ?? '') === $header['kid'] && ($key['kty'] ?? '') === 'RSA' &&
                ($key['alg'] ?? 'RS256') === 'RS256') {
                $jwk = $key;
                break;
            }
        }
        if (!$jwk || empty($jwk['n']) || empty($jwk['e'])) {
            throw new RuntimeException('Could not find the Google signing key.');
        }
        if (!function_exists('openssl_verify')) {
            throw new RuntimeException('PHP OpenSSL is required for Google sign-in.');
        }
        $publicKey = openssl_pkey_get_public(self::rsaJwkToPem($jwk['n'], $jwk['e']));
        if ($publicKey === false) {
            throw new RuntimeException('Could not read the Google signing key.');
        }
        $verifiedSignature = openssl_verify(
            $encodedHeader . '.' . $encodedClaims,
            $signature,
            $publicKey,
            OPENSSL_ALGO_SHA256
        );
        if ($verifiedSignature !== 1) {
            throw new RuntimeException('Google identity signature is invalid.');
        }

        $verifiedEmail = $claims['email_verified'] ?? false;
        if (!in_array($claims['iss'] ?? '', ['accounts.google.com', 'https://accounts.google.com'], true) ||
            !hash_equals($clientId, (string) ($claims['aud'] ?? '')) ||
            (int) ($claims['exp'] ?? 0) <= time() ||
            (int) ($claims['iat'] ?? PHP_INT_MAX) > time() + 60 ||
            !is_string($claims['nonce'] ?? null) || !hash_equals($expectedNonce, $claims['nonce']) ||
            !in_array($verifiedEmail, [true, 'true', 1, '1'], true) ||
            empty($claims['sub']) || empty($claims['email'])) {
            throw new RuntimeException('Google identity claims could not be verified.');
        }
        return $claims;
    }

    private static function decodeJwtPart(string $part): array
    {
        $decoded = self::base64UrlDecode($part);
        $value = $decoded === false ? null : json_decode($decoded, true);
        if (!is_array($value)) {
            throw new RuntimeException('Google returned an invalid identity token.');
        }
        return $value;
    }

    private static function base64UrlDecode(string $value)
    {
        $base64 = strtr($value, '-_', '+/');
        $base64 .= str_repeat('=', (4 - strlen($base64) % 4) % 4);
        return base64_decode($base64, true);
    }

    private static function rsaJwkToPem(string $modulus, string $exponent): string
    {
        $n = self::base64UrlDecode($modulus);
        $e = self::base64UrlDecode($exponent);
        if ($n === false || $e === false) {
            throw new RuntimeException('Google signing key is malformed.');
        }
        $rsaKey = self::derSequence(self::derInteger($n) . self::derInteger($e));
        $algorithm = hex2bin('300d06092a864886f70d0101010500');
        $subjectPublicKey = "\x03" . self::derLength(strlen($rsaKey) + 1) . "\x00" . $rsaKey;
        $spki = self::derSequence($algorithm . $subjectPublicKey);
        return "-----BEGIN PUBLIC KEY-----\n" . chunk_split(base64_encode($spki), 64, "\n") . "-----END PUBLIC KEY-----\n";
    }

    private static function derInteger(string $value): string
    {
        $value = ltrim($value, "\x00");
        if ($value === '' || (ord($value[0]) & 0x80) !== 0) {
            $value = "\x00" . $value;
        }
        return "\x02" . self::derLength(strlen($value)) . $value;
    }

    private static function derSequence(string $value): string
    {
        return "\x30" . self::derLength(strlen($value)) . $value;
    }

    private static function derLength(int $length): string
    {
        if ($length < 128) {
            return chr($length);
        }
        $encoded = '';
        while ($length > 0) {
            $encoded = chr($length & 0xff) . $encoded;
            $length >>= 8;
        }
        return chr(0x80 | strlen($encoded)) . $encoded;
    }

    private static function requestJson(string $url, ?array $form = null): array
    {
        if (!function_exists('curl_init')) {
            throw new RuntimeException('PHP cURL is required for Google sign-in.');
        }
        $curl = curl_init($url);
        $headers = ['Accept: application/json'];
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ];
        if ($form !== null) {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = http_build_query($form, '', '&', PHP_QUERY_RFC3986);
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        }
        $options[CURLOPT_HTTPHEADER] = $headers;
        curl_setopt_array($curl, $options);
        $body = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);
        if (!is_string($body) || $status < 200 || $status >= 300) {
            error_log('Google OAuth request failed (HTTP ' . $status . '): ' . $error);
            throw new RuntimeException('Google sign-in could not be completed.');
        }
        $decoded = json_decode($body, true);
        if (!is_array($decoded)) {
            throw new RuntimeException('Google returned an invalid response.');
        }
        return $decoded;
    }
}