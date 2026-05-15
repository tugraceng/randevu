<?php

declare(strict_types=1);

function abort(int $code, string $message = ''): void
{
    http_response_code($code);
    echo $message ?: http_response_code();
    exit;
}
