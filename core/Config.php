<?php
// ============================================================
//  core/Config.php — Chargement de variables d'environnement
// ============================================================
class Config {

    public static function loadEnv(string $filePath): void {
        if (!file_exists($filePath)) {
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            [$key, $value] = array_map('trim', explode('=', $line, 2) + [1 => '']);
            if ($key === '') {
                continue;
            }

            $value = trim($value, " \t\n\r\0\x0B\"'");
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }

    public static function get(string $key, mixed $default = null): mixed {
        $value = getenv($key);
        if ($value !== false) {
            return self::castValue($value);
        }

        if (array_key_exists($key, $_ENV)) {
            return self::castValue($_ENV[$key]);
        }

        if (array_key_exists($key, $_SERVER)) {
            return self::castValue($_SERVER[$key]);
        }

        return $default;
    }

    private static function castValue(mixed $value): mixed {
        if (!is_string($value)) {
            return $value;
        }

        $lower = strtolower($value);
        if ($lower === 'true' || $lower === '(true)') {
            return true;
        }
        if ($lower === 'false' || $lower === '(false)') {
            return false;
        }
        if ($lower === 'null' || $lower === '(null)') {
            return null;
        }
        return $value;
    }
}

// Méthode env : gère env. 
function env(string $key, mixed $default = null): mixed {
    return Config::get($key, $default);
}
