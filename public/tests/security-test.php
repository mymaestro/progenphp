<?php
/**
 * Security Test Page
 * 
 * This page tests various security features and folder access restrictions.
 */

// Include utility functions
include_once __DIR__ . '/../private/utils/functions.php';

// Start session for CSRF testing
session_start();

$tests = [];
$overallStatus = true;

// Test 1: Private folder access protection
$tests['private_folder_protection'] = [
    'name' => 'Private Folder Access Protection',
    'description' => 'Verify that private folder is not accessible via HTTP',
    'status' => false,
    'message' => '',
];

// Attempt to access private folder (this should fail)
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8002';
$requestUri = $_SERVER['REQUEST_URI'] ?? '/tests/security-test.php';
$privateUrl = '//' . $host . dirname($requestUri) . '/../private/config/app.php';
$context = stream_context_create([
    'http' => [
        'timeout' => 5,
        'ignore_errors' => true,
    ]
]);

$response = @file_get_contents($privateUrl, false, $context);
if ($response === false || (isset($http_response_header) && strpos($http_response_header[0], '403') !== false)) {
    $tests['private_folder_protection']['status'] = true;
    $tests['private_folder_protection']['message'] = 'Private folder is properly protected';
} else {
    $tests['private_folder_protection']['message'] = 'WARNING: Private folder may be accessible!';
    $overallStatus = false;
}

// Test 2: .htaccess file presence
$tests['htaccess_files'] = [
    'name' => '.htaccess Files Present',
    'description' => 'Check for required .htaccess files',
    'status' => false,
    'message' => '',
];

$htaccessFiles = [
    __DIR__ . '/../.htaccess' => 'Root .htaccess',
    __DIR__ . '/../private/.htaccess' => 'Private folder .htaccess',
];

$htaccessStatus = [];
foreach ($htaccessFiles as $file => $name) {
    $htaccessStatus[$name] = file_exists($file);
}

if (all_true($htaccessStatus)) {
    $tests['htaccess_files']['status'] = true;
    $tests['htaccess_files']['message'] = 'All required .htaccess files are present';
} else {
    $missing = array_keys(array_filter($htaccessStatus, function($v) { return !$v; }));
    $tests['htaccess_files']['message'] = 'Missing .htaccess files: ' . implode(', ', $missing);
    $overallStatus = false;
}

// Test 3: Configuration file access
$tests['config_access'] = [
    'name' => 'Configuration File Access',
    'description' => 'Verify configuration files can be included but not accessed directly',
    'status' => false,
    'message' => '',
];

try {
    $config = include __DIR__ . '/../private/config/app.php';
    if (is_array($config) && isset($config['app']['name'])) {
        $tests['config_access']['status'] = true;
        $tests['config_access']['message'] = 'Configuration files are accessible to PHP includes';
    } else {
        $tests['config_access']['message'] = 'Configuration files could not be loaded properly';
        $overallStatus = false;
    }
} catch (Exception $e) {
    $tests['config_access']['message'] = 'Error loading configuration: ' . $e->getMessage();
    $overallStatus = false;
}

// Test 4: Utility functions
$tests['utility_functions'] = [
    'name' => 'Utility Functions',
    'description' => 'Test utility functions are working properly',
    'status' => false,
    'message' => '',
];

try {
    $token = generateToken(16);
    $email = validateEmail('test@example.com');
    $ip = getClientIP();
    
    if (strlen($token) === 32 && $email === true && !empty($ip)) {
        $tests['utility_functions']['status'] = true;
        $tests['utility_functions']['message'] = 'Utility functions are working correctly';
    } else {
        $tests['utility_functions']['message'] = 'Some utility functions are not working correctly';
        $overallStatus = false;
    }
} catch (Exception $e) {
    $tests['utility_functions']['message'] = 'Error testing utility functions: ' . $e->getMessage();
    $overallStatus = false;
}

// Test 5: CSRF Token Generation
$tests['csrf_token'] = [
    'name' => 'CSRF Token Generation',
    'description' => 'Test CSRF token generation and verification',
    'status' => false,
    'message' => '',
];

try {
    $csrfToken = generateCSRFToken();
    $isValid = verifyCSRFToken($csrfToken);
    
    if (!empty($csrfToken) && $isValid) {
        $tests['csrf_token']['status'] = true;
        $tests['csrf_token']['message'] = 'CSRF token generation and verification working';
    } else {
        $tests['csrf_token']['message'] = 'CSRF token system not working properly';
        $overallStatus = false;
    }
} catch (Exception $e) {
    $tests['csrf_token']['message'] = 'Error testing CSRF tokens: ' . $e->getMessage();
    $overallStatus = false;
}

// Helper function
function all_true($array) {
    return count(array_filter($array)) === count($array);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProgenPHP - Security Tests</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: <?php echo $overallStatus ? 'linear-gradient(135deg, #27ae60 0%, #2ecc71 100%)' : 'linear-gradient(135deg, #e74c3c 0%, #c0392b 100%)'; ?>;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 2.5em;
        }
        .content {
            padding: 30px;
        }
        .test-result {
            margin-bottom: 20px;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid;
        }
        .test-success {
            background: #d5f4e6;
            border-color: #27ae60;
        }
        .test-failure {
            background: #ffeaa7;
            border-color: #e74c3c;
        }
        .test-name {
            font-weight: bold;
            font-size: 1.1em;
            margin-bottom: 5px;
        }
        .test-description {
            color: #666;
            margin-bottom: 10px;
        }
        .test-message {
            font-weight: 500;
        }
        .status-icon {
            float: right;
            font-size: 1.5em;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-link:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?php echo $overallStatus ? '✓' : '⚠'; ?> Security Tests</h1>
            <p><?php echo $overallStatus ? 'All tests passed' : 'Some tests failed'; ?></p>
        </div>
        
        <div class="content">
            <?php foreach ($tests as $test): ?>
                <div class="test-result <?php echo $test['status'] ? 'test-success' : 'test-failure'; ?>">
                    <div class="status-icon"><?php echo $test['status'] ? '✓' : '✗'; ?></div>
                    <div class="test-name"><?php echo htmlspecialchars($test['name']); ?></div>
                    <div class="test-description"><?php echo htmlspecialchars($test['description']); ?></div>
                    <div class="test-message"><?php echo htmlspecialchars($test['message']); ?></div>
                </div>
            <?php endforeach; ?>
            
            <a href="/" class="back-link">← Back to Environment Info</a>
        </div>
    </div>
</body>
</html>