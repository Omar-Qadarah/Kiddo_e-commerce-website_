<?php
 
const SHOP_PAGE_SIZE = 12;
function shopPagination(int $total): array
{
    $pages = max(1, (int) ceil($total / SHOP_PAGE_SIZE));
    $requested = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT);
    $page = min($pages, max(1, (int) $requested));
    return ['total' => $total, 'pages' => $pages, 'page' => $page,
            'offset' => ($page - 1) * SHOP_PAGE_SIZE];
}
function renderShopPagination(array $pagination, array $query, bool $compact = false): void
{
    if ($pagination['pages'] <= 1) return;
    $page = $pagination['page'];
    $pages = $pagination['pages'];
    if ($compact) $query = array_filter($query, fn($value) => $value !== '');
    $url = function ($number) use ($query) {
        return '?' . htmlspecialchars(http_build_query(array_merge($query, ['page' => $number])), ENT_QUOTES, 'UTF-8');
    };
    echo '<div class="shop-pagination">';
    if ($compact) {
        echo '<p>Page ' . $page . ' of ' . $pages . '</p>';
    } else {
        echo '<p>Showing ' . ($pagination['offset'] + 1) . '–'
            . min($pagination['offset'] + SHOP_PAGE_SIZE, $pagination['total'])
            . ' of ' . $pagination['total'] . ' toys</p>';
    }
    echo '<nav aria-label="Product pages">';
    if ($page > 1) echo '<a href="' . $url($page - 1) . '">← Previous</a>';
    else echo '<span aria-disabled="true">← Previous</span>';
    $last = 0;
    for ($number = 1; $number <= $pages; $number++) {
        $nearby = abs($number - $page) <= 1
            || ($compact && $page === 1 && $number === 3)
            || ($compact && $page === $pages && $number === $pages - 2);
        if ($number !== 1 && $number !== $pages && !$nearby) continue;
        if ($last && $number > $last + 1) echo '<span class="pagination-gap">…</span>';
        echo '<a href="' . $url($number) . '"' . ($number === $page ? ' aria-current="page"' : '') . '>' . $number . '</a>';
        $last = $number;
    }
    if ($page < $pages) echo '<a href="' . $url($page + 1) . '">Next →</a>';
    else echo '<span aria-disabled="true">Next →</span>';
    echo '</nav></div>';
}
