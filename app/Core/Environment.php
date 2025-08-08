<?php

namespace App\Core;

class Environment
{
    private static array $vars = [];
    private static bool $loaded = false;

    public static function load(): void
    {
        if (self::$loaded) {
            return;
        }

        $envFile = __DIR__ . '/../../config.env';
        
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
                
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);
                
                // Remove aspas se existirem
                if (preg_match('/^"(.*)"$/', $value, $matches)) {
                    $value = $matches[1];
                }
                
                self::$vars[$name] = $value;
                putenv("$name=$value");
            }
        }
        
        self::$loaded = true;
    }

    public static function get(string $key, $default = null)
    {
        return self::$vars[$key] ?? getenv($key) ?: $default;
    }

    public static function set(string $key, string $value): void
    {
        self::$vars[$key] = $value;
        putenv("$key=$value");
    }
}
