<?php
/**
 * Environment-specific Configuration
 * 
 * This file contains environment-specific settings that override
 * the default configuration based on the current environment.
 */

// Detect environment
$environment = $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? 'development';

switch ($environment) {
    case 'production':
        return [
            'app' => [
                'debug' => false,
                'environment' => 'production',
            ],
            'logging' => [
                'level' => 'warning',
            ],
            'security' => [
                'session_lifetime' => 7200, // 2 hours
            ],
        ];

    case 'staging':
        return [
            'app' => [
                'debug' => true,
                'environment' => 'staging',
            ],
            'logging' => [
                'level' => 'info',
            ],
        ];

    case 'development':
    default:
        return [
            'app' => [
                'debug' => true,
                'environment' => 'development',
            ],
            'logging' => [
                'level' => 'debug',
            ],
            'security' => [
                'session_lifetime' => 3600, // 1 hour
            ],
        ];
}