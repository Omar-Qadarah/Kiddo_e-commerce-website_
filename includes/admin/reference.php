<?php
function adminOrderReference(int $id): string
{
    return 'ORD-' . strtoupper(substr(hash('sha256', 'kiddo-order:' . $id), 0, 10));
}
