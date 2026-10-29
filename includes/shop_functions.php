<?php

// Small helpers shared by the storefront pages (landing, toys, toy details).

// Turns a saved path like "uploads/products/a.png" into a URL.
// Falls back to the placeholder picture when a toy has no image.
function shopImageUrl(?string $path): string
{
    $path = trim((string) $path);
    if ($path === '') {
        return BASE_URL . '/assets/images/toy-placeholder.svg';
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }

    return BASE_URL . '/' . ltrim($path, '/');
}

// All image URLs of one toy: products.image first, then the images table rows,
// without repeats. Always returns at least one URL (the placeholder).
function shopToyImages(array $toy, array $extraPaths = []): array
{
    $urls = [];
    foreach (array_merge([(string) ($toy['image'] ?? '')], $extraPaths) as $path) {
        if (trim((string) $path) !== '') {
            $urls[shopImageUrl($path)] = true;
        }
    }

    return $urls ? array_keys($urls) : [shopImageUrl('')];
}

// Price after the active discount.
function shopFinalPrice(array $toy): float
{
    $price    = (float) $toy['price'];
    $discount = (float) ($toy['discount_pct'] ?? 0);

    return $discount > 0 ? round($price * (1 - $discount / 100), 2) : $price;
}

// 7.5 -> "7.50 JOD"
function shopMoney(float $amount): string
{
    return number_format($amount, 2) . ' JOD';
}

// "boys" -> "Boys"
function shopGenderLabel(string $gender): string
{
    return ['girls' => 'Girls', 'boys' => 'Boys', 'unisex' => 'Unisex'][$gender] ?? 'Unisex';
}

// Five stars filled by the rating (CSS uses the --rating value).
function shopStars(float $rating): string
{
    $rating = max(0, min(5, round($rating, 1)));

    return '<span class="stars" style="--rating: ' . $rating . '" role="img" aria-label="Rated '
        . $rating . ' out of 5"></span>';
}
