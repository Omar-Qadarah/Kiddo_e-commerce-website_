<?php
function adminTableEscape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function adminTableUrl($pagination, $changes = [])
{
    $query = array_merge($pagination['query'], ['page' => $pagination['page']], $changes);
    $query = array_filter($query, function ($value) { return $value !== ''; });
    return '?' . http_build_query($query);
}
