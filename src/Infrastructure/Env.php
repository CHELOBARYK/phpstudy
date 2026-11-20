<?php
namespace App\Infrastructure;

class Env {
    public static function get(string $key, $default = null) {
        $rawValue = $_ENV[$key] ?? $default;
        $value = match (strtolower((string)$rawValue)) {
            'true', '(true)'   => true,
            'false', '(false)' => false,
            'null', '(null)'   => null,
            'empty', '(empty)' => '',
            default            => $rawValue,
        };

        return $value;
    }
}