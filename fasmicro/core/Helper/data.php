<?php

if (!function_exists('_viewData')) {
    function _viewData(?array $data = null): array
    {
        static $store = [];

        if ($data !== null) {
            $store = $data;
        }

        return $store;
    }
}

if (!function_exists('setValue')) {
    function setValue(string $key, mixed $default = ''): mixed
    {
        $source = _viewData() ?? [];

        foreach (explode(".", $key) as $segment) {
            if (!is_array($source) || !array_key_exists($segment, $source)) {
                return $default;
            }
            $source = $source[$segment];
        }

        return $source ?? $default;
    }
}
