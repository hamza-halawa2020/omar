<?php

if (!function_exists('preserveOtherPagination')) {
    function preserveOtherPagination(string $currentPaginator): array
    {
        return collect(request()->query())
            ->filter(fn($value, $key) => $key !== $currentPaginator && str_ends_with($key, '_page'))
            ->all();
    }
}
