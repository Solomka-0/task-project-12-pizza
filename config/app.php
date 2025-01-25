<?php

return [
    'auth_key' => $_ENV['AUTH_KEY'] ?? 'qwerty123',
    'db' => [
        'driver' => $_ENV['DB_DRIVER'] ?? 'memory',
        'host' => $_ENV['DB_HOST'] ?? 'localhost',
        'port' => $_ENV['DB_PORT'] ?? '3306',
        'database' => $_ENV['DB_NAME'] ?? 'pizza',
        'username' => $_ENV['DB_USER'] ?? 'root',
        'password' => $_ENV['DB_PASSWORD'] ?? 'secret',
    ],
];