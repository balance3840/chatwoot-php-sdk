<?php

declare(strict_types=1);

// System Guzzle autoloaders (available via: apt install php-guzzlehttp-guzzle)
foreach ([
    '/usr/share/php/GuzzleHttp/autoload.php',
    '/usr/share/php/GuzzleHttp/Psr7/autoload.php',
    '/usr/share/php/GuzzleHttp/Promise/autoload.php',
] as $path) {
    if (file_exists($path)) {
        require_once $path;
    }
}

// PSR-4 loader for src/ and tests/
spl_autoload_register(function (string $class): void {
    $map = [
        'RamiroEstrella\\ChatwootPhpSdk\\Tests\\' => __DIR__ . '/../tests/',
        'RamiroEstrella\\ChatwootPhpSdk\\'        => __DIR__ . '/../src/',
    ];
    foreach ($map as $prefix => $dir) {
        if (str_starts_with($class, $prefix)) {
            $file = $dir . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
            if (file_exists($file)) {
                require $file;
            }
            return;
        }
    }
});
