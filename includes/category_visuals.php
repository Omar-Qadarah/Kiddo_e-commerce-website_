<?php
 
function shopCategoryIllustration(string $name): string
{
    $name = strtolower($name);
    $matches = [
        'ride-on' => 'vehicle', 'active play' => 'sports',
        'plush' => 'baby', 'doll' => 'doll', 'figure' => 'doll', 'vehicle' => 'vehicle', 'rc toys' => 'vehicle', 'craft' => 'crafts', 'baby' => 'baby', 'toddler' => 'baby',
        'brain' => 'puzzle', 'construct' => 'blocks', 'building' => 'blocks',
        'educat' => 'education', 'electron' => 'robot', 'gaming' => 'gaming',
        'music' => 'music', 'outdoor' => 'sports', 'sport' => 'sports',
        'pretend' => 'pretend', 'role' => 'pretend', 'puzzle' => 'puzzle',
        'board' => 'puzzle', 'science' => 'science', 'experiment' => 'science',
        'shared' => 'shared', 'water' => 'water', 'wood' => 'wooden',
        'girls' => 'doll', 'boys' => 'vehicle', 'vehicle' => 'vehicle',
    ];
    foreach ($matches as $keyword => $file) {
        if (str_contains($name, $keyword)) return BASE_URL . '/assets/images/categories/' . $file . '.svg';
    }
    return BASE_URL . '/assets/images/categories/shared.svg';
}

 
 
function shopCategoryImageUrl(?string $path): string
{
    $filename = basename(trim((string) $path));
    if ($filename === '' || $filename === '.' || $filename === '..') {
        return '';
    }

    return BASE_URL . '/uploads/categories/' . rawurlencode($filename);
}
