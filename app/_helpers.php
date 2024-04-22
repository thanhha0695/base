<?php

if (!function_exists('array_get_value')) {
    function array_get_value(array $data, string $key, $default = null) {
        return isset($data[$key]) ? $data[$key] : $default;
    }
}

if (!function_exists('array_get_string')) {
    function array_get_string(array $input, string $key, string $default = null)
    {
        if (isset($input[$key])) {
            return (string)$input[$key];
        }
        return $default;
    }
}

if (!function_exists('array_get_int')) {
    function array_get_int(array $input, string $key, int $default = null)
    {
        if (isset($input[$key]) && preg_match('/(?<=\s|^)\d+(?=\s|$)/', (string)$input[$key], $matches)) {
            return intval($input[$key]);
        }
        return $default;
    }
}

if (!function_exists('array_get_float')) {
    function array_get_float(array $input, string $key, float $default = null)
    {
        if (isset($input[$key]) && preg_match('/-?[0-9]+(\.[0-9]+)?/', $input[$key])) {
            return floatval($input[$key]);
        }
        return $default;
    }
}

if (!function_exists('array_get_bool')) {
    function array_get_bool(array $input, string $key, bool $default = null)
    {
        if (isset($input[$key])) {
            return (bool)$input[$key];
        }
        return $default;
    }
}

if (!function_exists('array_get_array')) {
    function array_get_array(array $input, string $key, array $default = [])
    {
        if (isset($input[$key]) && gettype($input[$key]) === 'array') {
            return $input[$key];
        }
        return $default;
    }
}
