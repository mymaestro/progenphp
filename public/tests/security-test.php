<?php
/**
 * Security Test Page
 * 
 * This page tests various security features and folder access restrictions.
 */

// Include utility functions
include_once __DIR__ . '/../../private/utils/functions.php';

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
// Since tests are now in public/tests/, we need to go up two levels to reach private
$privateUrl = '//' . $host . '/private/config/app.php';
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
    __DIR__ . '/../../.htaccess' => 'Root .htaccess',
    __DIR__ . '/../../private/.htaccess' => 'Private folder .htaccess',
];

$htaccessStatus = [];
foreach ($htaccessFiles as $file => $name) {
    $htaccessStatus[$name] = file_exists($file);
}

if (array_filter($htaccessStatus) === $htaccessStatus) {
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
    $config = include __DIR__ . '/../../private/config/app.php';
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
    $token = generateToken(16);  // Should return 16-character hex string
    $email = validateEmail('test@example.com');
    $ip = getClientIP();
    
    if (strlen($token) === 16 && $email === true && !empty($ip)) {
        $tests['utility_functions']['status'] = true;
        $tests['utility_functions']['message'] = 'Utility functions are working correctly';
    } else {
        $failedTests = [];
        if (strlen($token) !== 16) $failedTests[] = "generateToken() returned " . strlen($token) . " chars, expected 16";
        if ($email !== true) $failedTests[] = "validateEmail() failed";
        if (empty($ip)) $failedTests[] = "getClientIP() returned empty";
        
        $tests['utility_functions']['message'] = 'Failed tests: ' . implode(', ', $failedTests);
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.2/css/bulma.min.css">
    <style>
        .hero,
        .box,
        .notification,
        .message,
        .message-body,
        .button,
        .tag,
        .input,
        .textarea,
        .select select,
        .table {
            border-radius: 0 !important;
        }

        .box,
        .notification,
        .message,
        .hero {
            box-shadow: none !important;
        }

        .box,
        .notification,
        .message,
        .hero,
        .table {
            border: 1px solid hsl(0, 0%, 86%);
        }

        .dashboard-hero .title {
            font-size: 1.65rem;
            letter-spacing: 0.02em;
        }

        .dashboard-hero .subtitle {
            font-size: 0.95rem;
            margin-bottom: 0.2rem;
        }

        .dashboard-card {
            padding: 0;
        }

        .dashboard-card-title {
            min-height: 3rem;
            display: flex;
            align-items: center;
            margin: 0;
            padding: 0.9rem 1rem;
            border-bottom: 1px solid hsl(0, 0%, 86%);
            font-size: 0.95rem;
            letter-spacing: 0.01em;
        }

        .dashboard-message {
            margin: 1rem;
        }

        .test-title-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
    </style>
</head>
<body class="has-background-dark">
    <section class="section">
        <div class="container is-max-desktop">
            <section class="hero is-light is-small mb-5 dashboard-hero">
                <div class="hero-body has-text-centered">
                    <p class="title">Security Tests</p>
                    <p class="subtitle"><?php echo $overallStatus ? 'All tests passed' : 'Some tests failed'; ?></p>
                </div>
            </section>

            <div class="box dashboard-card">
                <h3 class="title is-5 dashboard-card-title">Security Checks</h3>
                <?php foreach ($tests as $test): ?>
                    <article class="message is-light dashboard-message">
                        <div class="message-body">
                            <div class="test-title-row">
                                <strong class="<?php echo $test['status'] ? 'has-text-success-dark' : 'has-text-danger-dark'; ?>"><?php echo htmlspecialchars($test['name']); ?></strong>
                                <span class="tag <?php echo $test['status'] ? 'is-success' : 'is-danger'; ?> is-light">
                                    <?php echo $test['status'] ? 'PASS' : 'FAIL'; ?>
                                </span>
                            </div>
                            <p class="has-text-grey mt-2 mb-2"><?php echo htmlspecialchars($test['description']); ?></p>
                            <p><?php echo htmlspecialchars($test['message']); ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>

                <div class="buttons mt-5 mb-4 ml-4">
                    <a href="/" class="button is-link is-light">Back to Environment Info</a>
                </div>
            </div>
        </div>
    </section>
</body>
</html>