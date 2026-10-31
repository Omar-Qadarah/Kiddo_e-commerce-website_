<?php
require_once __DIR__ . '/init.php';
require_once __DIR__ . '/../classes/AgeGroup.php';
require_once __DIR__ . '/../classes/ToyCatalog.php';
$ageGroupRows = (new AgeGroup($pdo))->getAll();
$ageCatalog = new ToyCatalog($pdo);
$storeAgeGroups = $ageGroupRows;
$ageCards = [];
$ageColors = ['blue','green','orange','pink','purple'];
foreach ($ageGroupRows as $i => $group) {
    $ageCards[$group['slug']] = [
        'id'=>(int)$group['id'], 'min'=>(int)$group['min_age'], 'max'=>(int)$group['max_age'],
        'title'=>$group['name'], 'text'=>$group['description'], 'image'=>$group['image'],
        'color'=>$ageColors[$i % count($ageColors)],
        'count'=>$ageCatalog->countFilteredToys(['age'=>['min_age'=>(int)$group['min_age'],'max_age'=>(int)$group['max_age']]]),
    ];
}
 
$legacyAgeCards = [];
function ageCardRange(array $card): string
{
    return $card['max'] >= 99 ? $card['min'].'+' : $card['min'].'–'.$card['max'];
}
function ageToysUrl(string $ageKey, string $categorySlug = ''): string
{
    return BASE_URL.'/products/age_toys.php?'.http_build_query(array_filter(['age'=>$ageKey,'category'=>$categorySlug],fn($value)=>$value!==''));
}
