<?php
/**
 * ProgenPHP Utility Functions
 * 
 * This file contains common utility functions used throughout the application.
 */

/**
 * Sanitize input data
 * 
 * @param mixed $data The data to sanitize
 * @return mixed Sanitized data
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    
    if (is_string($data)) {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    
    return $data;
}

/**
 * Generate a secure random token
 * 
 * @param int $length The length of the token
 * @return string Random token
 */
function generateToken($length = 32) {
    // Generate random bytes (half the desired length since bin2hex doubles it)
    return bin2hex(random_bytes(intval($length / 2)));
}

/**
 * Validate email address
 * 
 * @param string $email Email to validate
 * @return bool True if valid, false otherwise
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Check if request is AJAX
 * 
 * @return bool True if AJAX request, false otherwise
 */
function isAjaxRequest() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Get client IP address
 * 
 * @return string Client IP address
 */
function getClientIP() {
    $ip_keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 
                'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
    
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

/**
 * Format bytes to human readable format
 * 
 * @param int $bytes Number of bytes
 * @param int $precision Decimal precision
 * @return string Formatted string
 */
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    for ($i = 0; $bytes > 1024; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

/**
 * Redirect to a URL
 * 
 * @param string $url URL to redirect to
 * @param int $statusCode HTTP status code
 */
function redirect($url, $statusCode = 302) {
    header('Location: ' . $url, true, $statusCode);
    exit;
}

/**
 * Set flash message
 * 
 * @param string $type Message type (success, error, warning, info)
 * @param string $message Message content
 */
function setFlashMessage($type, $message) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash_messages'][] = ['type' => $type, 'message' => $message];
}

/**
 * Get and clear flash messages
 * 
 * @return array Array of flash messages
 */
function getFlashMessages() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);
    return $messages;
}

/**
 * Log message to file
 * 
 * @param string $message Message to log
 * @param string $level Log level (debug, info, warning, error)
 */
function logMessage($message, $level = 'info') {
    $config = include __DIR__ . '/../config/app.php';
    
    if (!$config['logging']['enabled']) {
        return;
    }
    
    $logFile = $config['logging']['file'];
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $clientIP = getClientIP();
    $logEntry = "[{$timestamp}] [{$level}] [{$clientIP}] {$message}" . PHP_EOL;
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

/**
 * Generate CSRF token
 * 
 * @return string CSRF token
 */
function generateCSRFToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = generateToken();
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * 
 * @param string $token Token to verify
 * @return bool True if valid, false otherwise
 */
function verifyCSRFToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Rate limiting function
 * 
 * @param string $identifier Unique identifier (IP, user ID, etc.)
 * @param int $limit Request limit per time window
 * @param int $window Time window in seconds
 * @return bool True if within limit, false otherwise
 */
function checkRateLimit($identifier, $limit = 60, $window = 60) {
    $cacheFile = __DIR__ . '/../cache/rate_limit_' . md5($identifier);
    $currentTime = time();
    
    if (file_exists($cacheFile)) {
        $data = json_decode(file_get_contents($cacheFile), true);
        
        // Clean old requests outside the time window
        $data['requests'] = array_filter($data['requests'], function($timestamp) use ($currentTime, $window) {
            return ($currentTime - $timestamp) < $window;
        });
        
        if (count($data['requests']) >= $limit) {
            return false;
        }
        
        $data['requests'][] = $currentTime;
    } else {
        $cacheDir = dirname($cacheFile);
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        $data = ['requests' => [$currentTime]];
    }
    
    file_put_contents($cacheFile, json_encode($data), LOCK_EX);
    return true;
}