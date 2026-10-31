<?php
require_once __DIR__ . '/init.php';
require_once __DIR__ . '/../classes/ToyCatalog.php';
$ageCardCatalog = new ToyCatalog($pdo);
$storeAgeGroups = $ageCardCatalog->getAgeGroupsWithToyCount();
$ageCards = [];
$ageColors = ['blue', 'green', 'orange', 'pink', 'purple'];
$ageEmojis = ['🧸', '🖍️', '🔬', '🤖', '🎮'];
foreach ($storeAgeGroups as $i => $ageGroup) {
    $ageCards[$ageGroup['slug']] = [
        'min' => (int) $ageGroup['min_age'], 'max' => (int) $ageGroup['max_age'],
        'title' => $ageGroup['name'], 'text' => $ageGroup['description'],
        'color' => $ageColors[$i % count($ageColors)], 'emoji' => $ageEmojis[$i % count($ageEmojis)],
        'image' => $ageGroup['image'], 'count' => (int) $ageGroup['toy_count'],
    ];
}
// Compatibility for links shared before the age navigation was unified.
$legacyAgeCards = [
    '0-3'   => ['min' => 0,  'max' => 3,  'title' => 'Babies & Toddlers', 'text' => 'Soft, safe toys for little hands exploring colors, sounds and textures.', 'color' => 'blue',   'emoji' => '🧸'],
    '4-6'   => ['min' => 4,  'max' => 6,  'title' => 'Preschoolers',      'text' => 'Playful toys that build first skills: shapes, letters and imagination.',  'color' => 'green',  'emoji' => '🖍️'],
    '7-10'  => ['min' => 7,  'max' => 10, 'title' => 'Young Explorers',   'text' => 'Building sets, puzzles and games for curious, growing minds.',            'color' => 'orange', 'emoji' => '🔬'],
    '10-12' => ['min' => 10, 'max' => 12, 'title' => 'Big Kids & Tweens', 'text' => 'Challenging kits, sports and tech toys for bigger adventures.',            'color' => 'pink',   'emoji' => '🤖'],
    '12+'   => ['min' => 12, 'max' => 99, 'title' => 'Teens',             'text' => 'Strategy games, science kits and hobbies for older kids.',                 'color' => 'purple', 'emoji' => '🎮'],
];
function ageCardRange(array $card): string
{
    return $card['max'] >= 99 ? $card['min'] . '+' : $card['min'] . '–' . $card['max'];
}
function ageToysUrl(string $ageKey, string $categorySlug = ''): string
{
    return BASE_URL . '/products/age_toys.php?' . http_build_query(array_filter([
        'age' => $ageKey, 'category' => $categorySlug,
    ], fn($value) => $value !== ''));
}
