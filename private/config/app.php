<?php
/**
 * ProgenPHP Application Configuration
 * 
 * This file contains the main application configuration settings.
 * Keep this file secure and never commit sensitive data to version control.
 */

return [
    // Application Settings
    'app' => [
        'name' => 'ProgenPHP',
        'version' => '1.0.0',
        'debug' => true, // Set to false in production
        'environment' => 'development', // development, staging, production
    ],

    // Timezone Configuration
    'timezone' => 'America/Chicago', // Change to your local timezone

    // Database Configuration (example)
    'database' => [
        'default' => [
            'host' => 'localhost',
            'port' => 3306,
            'database' => 'progenphp',
            'username' => 'your_username',
            'password' => 'your_password',
            'charset' => 'utf8mb4',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
        ],
    ],

    // Security Settings
    'security' => [
        'encryption_key' => 'your-32-character-encryption-key-here', // Generate a secure key
        'session_lifetime' => 3600, // 1 hour in seconds
        'csrf_protection' => true,
        'allowed_origins' => [
            'localhost',
            '127.0.0.1',
        ],
    ],

    // Logging Configuration
    'logging' => [
        'enabled' => true,
        'level' => 'info', // debug, info, warning, error
        'file' => __DIR__ . '/../logs/app.log',
        'max_size' => 10 * 1024 * 1024, // 10MB
    ],

    // File Upload Settings
    'upload' => [
        'max_size' => 2 * 1024 * 1024, // 2MB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt'],
        'upload_path' => dirname(__DIR__) . '/uploads/',
    ],

    // API Configuration
    'api' => [
        'rate_limit' => [
            'enabled' => true,
            'requests_per_minute' => 60,
        ],
        'version' => 'v1',
        'base_url' => '/api/',
    ],

    // Email Configuration (example)
    'mail' => [
        'driver' => 'smtp',
        'host' => 'smtp.example.com',
        'port' => 587,
        'username' => 'your_email@example.com',
        'password' => 'your_email_password',
        'encryption' => 'tls',
        'from' => [
            'address' => 'noreply@example.com',
            'name' => 'ProgenPHP',
        ],
    ],

    // Cache Configuration
    'cache' => [
        'default' => 'file',
        'stores' => [
            'file' => [
                'driver' => 'file',
                'path' => __DIR__ . '/../cache/',
            ],
            'redis' => [
                'driver' => 'redis',
                'host' => '127.0.0.1',
                'port' => 6379,
                'database' => 0,
            ],
        ],
    ],

    // Custom Application Settings
    'custom' => [
        'maintenance_mode' => false,
        'feature_flags' => [
            'new_ui' => false,
            'beta_features' => false,
        ],
    ],
];