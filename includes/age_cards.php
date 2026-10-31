<?php
// Age cards of the Categories page, shared by:
//   products/categories.php  (shows the cards)
//   products/age_toys.php    (shows the toys of ONE card on its own page)
// Fixed here (not taken from the age_groups table), so the admin's age groups
// and the toys page filters stay exactly as they are.
// "max" of the 12+ card is 99 = "no upper limit".
$ageCards = [
    '0-3'   => ['min' => 0,  'max' => 3,  'title' => 'Babies & Toddlers', 'text' => 'Soft, safe toys for little hands exploring colors, sounds and textures.', 'color' => 'blue',   'emoji' => '🧸', 'image' => 'assets/images/products/educational-toys/rainbow-stacking-rings-1.jpg'],
    '4-6'   => ['min' => 4,  'max' => 6,  'title' => 'Preschoolers',      'text' => 'Playful toys that build first skills: shapes, letters and imagination.',  'color' => 'green',  'emoji' => '🖍️', 'image' => 'assets/images/products/educational-toys/abc-letter-blocks-28-pcs-1.jpg'],
    '7-10'  => ['min' => 7,  'max' => 10, 'title' => 'Young Explorers',   'text' => 'Building sets, puzzles and games for curious, growing minds.',            'color' => 'orange', 'emoji' => '🔬', 'image' => 'assets/images/products/boys-toys/building-bricks-set-500-pcs-2.jpg'],
    '10-12' => ['min' => 10, 'max' => 12, 'title' => 'Big Kids & Tweens', 'text' => 'Challenging kits, sports and tech toys for bigger adventures.',            'color' => 'pink',   'emoji' => '🤖', 'image' => 'assets/images/products/boys-toys/rc-off-road-rock-crawler-1.jpg'],
    '12+'   => ['min' => 12, 'max' => 99, 'title' => 'Teens',             'text' => 'Strategy games, science kits and hobbies for older kids.',                 'color' => 'purple', 'emoji' => '🎮', 'image' => 'assets/images/products/educational-toys/wooden-chess-set-for-kids-1.jpg'],
];

// "0–3" or "12+" — the text shown on the card and in headings.
function ageCardRange(array $card): string
{
    return $card['max'] >= 99 ? $card['min'] . '+' : $card['min'] . '–' . $card['max'];
}

// Link to the page that lists the toys of one age card (keeps the chosen toy type).
function ageToysUrl(string $ageKey, string $categorySlug = ''): string
{
    $query = array_filter(['age' => $ageKey, 'category' => $categorySlug], fn($value) => $value !== '');

    return BASE_URL . '/products/age_toys.php?' . http_build_query($query);
}
