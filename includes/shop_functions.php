<?php

require_once __DIR__ . '/category_visuals.php';

 

 
 
function shopImageUrl(?string $path): string
{
    $path = trim((string) $path);
    if ($path === '') {
        return BASE_URL . '/assets/images/toy-placeholder.svg';
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }

    $relative = ltrim($path, '/');
    if (!is_file(dirname(__DIR__) . '/' . $relative)) {
        return BASE_URL . '/assets/images/toy-placeholder.svg';
    }
    return BASE_URL . '/' . $relative;
}

 
 
function shopToyImages(array $toy, array $extraPaths = []): array
{
    $urls = [];
    foreach (array_merge([(string) ($toy['image'] ?? '')], $extraPaths) as $path) {
        if (trim((string) $path) !== '') {
            $urls[shopImageUrl($path)] = true;
        }
    }

    return $urls ? array_keys($urls) : [shopCategoryIllustration($toy['category_name'] ?? '')];
}

 
function shopFinalPrice(array $toy): float
{
    $price    = (float) $toy['price'];
    $discount = (float) ($toy['discount_pct'] ?? 0);

    return $discount > 0 ? round($price * (1 - $discount / 100), 2) : $price;
}

 
function shopMoney(float $amount): string
{
    return number_format($amount, 2) . ' JOD';
}

 
function shopGenderLabel(string $gender): string
{
    return ['girls' => 'Girls', 'boys' => 'Boys', 'unisex' => 'Unisex'][$gender] ?? 'Unisex';
}

 
function shopStars(float $rating): string
{
    $rating = max(0, min(5, round($rating, 1)));

    return '<span class="stars" style="--rating: ' . $rating . '" role="img" aria-label="Rated '
        . $rating . ' out of 5"></span>';
}
