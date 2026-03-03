<?php
/**
 * Access Test Page
 * 
 * This page tests folder access permissions and demonstrates
 * proper file inclusion from private directories.
 */

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Test results array
$accessTests = [];

// Test 1: Include private configuration
try {
    $config = include __DIR__ . '/../../private/config/app.php';
    $accessTests[] = [
        'test' => 'Include Private Config',
        'status' => is_array($config) && !empty($config),
        'message' => is_array($config) ? 'Successfully included private configuration' : 'Failed to include private configuration',
    ];
} catch (Exception $e) {
    $accessTests[] = [
        'test' => 'Include Private Config',
        'status' => false,
        'message' => 'Exception: ' . $e->getMessage(),
    ];
}

// Test 2: Include utility functions
try {
    include_once __DIR__ . '/../../private/utils/functions.php';
    $tokenGenerated = function_exists('generateToken') ? generateToken(8) : false;
    $accessTests[] = [
        'test' => 'Include Utility Functions',
        'status' => function_exists('generateToken'),
        'message' => function_exists('generateToken') ? 'Utility functions loaded successfully' : 'Failed to load utility functions',
    ];
} catch (Exception $e) {
    $accessTests[] = [
        'test' => 'Include Utility Functions',
        'status' => false,
        'message' => 'Exception: ' . $e->getMessage(),
    ];
}

// Test 3: Check directory permissions
$directories = [
    'Public Directory' => __DIR__ . '/../../public/',
    'Private Directory' => __DIR__ . '/../../private/',
    'Config Directory' => __DIR__ . '/../../private/config/',
    'Utils Directory' => __DIR__ . '/../../private/utils/',
    'Logs Directory' => __DIR__ . '/../../private/logs/',
    'Cache Directory' => __DIR__ . '/../../private/cache/',
];

foreach ($directories as $name => $path) {
    $accessTests[] = [
        'test' => "Directory Access: $name",
        'status' => is_dir($path) && is_readable($path),
        'message' => is_dir($path) ? 
            (is_readable($path) ? 'Directory exists and is readable' : 'Directory exists but is not readable') :
            'Directory does not exist',
    ];
}

// Test 4: File write permissions (cache directory)
$cacheTestFile = __DIR__ . '/../../private/cache/test_' . time() . '.tmp';
try {
    $writeSuccess = file_put_contents($cacheTestFile, 'test content') !== false;
    if ($writeSuccess) {
        unlink($cacheTestFile); // Clean up
    }
    $accessTests[] = [
        'test' => 'Cache Directory Write Permission',
        'status' => $writeSuccess,
        'message' => $writeSuccess ? 'Cache directory is writable' : 'Cache directory is not writable',
    ];
} catch (Exception $e) {
    $accessTests[] = [
        'test' => 'Cache Directory Write Permission',
        'status' => false,
        'message' => 'Exception: ' . $e->getMessage(),
    ];
}

// Test 5: Log directory write permission
$logTestFile = __DIR__ . '/../../private/logs/test_' . time() . '.tmp';
try {
    $writeSuccess = file_put_contents($logTestFile, 'test log entry') !== false;
    if ($writeSuccess) {
        unlink($logTestFile); // Clean up
    }
    $accessTests[] = [
        'test' => 'Logs Directory Write Permission',
        'status' => $writeSuccess,
        'message' => $writeSuccess ? 'Logs directory is writable' : 'Logs directory is not writable',
    ];
} catch (Exception $e) {
    $accessTests[] = [
        'test' => 'Logs Directory Write Permission',
        'status' => false,
        'message' => 'Exception: ' . $e->getMessage(),
    ];
}

// Test 6: GhostScript availability
try {
    $gsAvailable = false;
    $gsVersion = '';
    
    if (function_exists('exec') && !in_array('exec', explode(',', ini_get('disable_functions')))) {
        $output = [];
        $return_var = null;
        @exec('gs --version 2>&1', $output, $return_var);
        
        if ($return_var === 0 && !empty($output)) {
            $gsAvailable = true;
            $gsVersion = isset($output[0]) ? $output[0] : 'Available';
        }
    }
    
    $accessTests[] = [
        'test' => 'GhostScript (gs) Utility',
        'status' => $gsAvailable,
        'message' => $gsAvailable ? 
            'GhostScript is available: ' . htmlspecialchars($gsVersion) : 
            'GhostScript (gs) is not available or exec() is disabled',
    ];
} catch (Exception $e) {
    $accessTests[] = [
        'test' => 'GhostScript (gs) Utility',
        'status' => false,
        'message' => 'Exception: ' . $e->getMessage(),
    ];
}

$passedTests = count(array_filter($accessTests, function($test) { return $test['status']; }));
$totalTests = count($accessTests);
$allPassed = $passedTests === $totalTests;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProgenPHP - Access Tests</title>
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

        .dashboard-summary {
            margin: 1rem;
            font-size: 0.92rem;
        }

        .dashboard-message {
            margin: 0 1rem 1rem;
        }

        .test-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .test-message {
            margin-top: 0.25rem;
        }
    </style>
</head>
<body class="has-background-dark">
    <section class="section">
        <div class="container is-max-desktop">
            <section class="hero is-light is-small mb-5 dashboard-hero">
                <div class="hero-body has-text-centered">
                    <p class="title">Access Tests</p>
                    <p class="subtitle">Testing directory access and file permissions</p>
                </div>
            </section>

            <div class="box dashboard-card">
                <h3 class="title is-5 dashboard-card-title">Test Results</h3>
                <div class="notification is-light has-text-centered dashboard-summary">
                    <p class="title is-6 mb-2">Test Summary</p>
                    <p><strong><?php echo $passedTests; ?></strong> out of <strong><?php echo $totalTests; ?></strong> tests passed</p>
                    <?php if ($allPassed): ?>
                        <p class="has-text-success-dark mt-2">All access tests passed successfully.</p>
                    <?php else: ?>
                        <p class="has-text-danger-dark mt-2">Some tests failed. Check the results below.</p>
                    <?php endif; ?>
                </div>

                <?php foreach ($accessTests as $test): ?>
                    <article class="message is-light dashboard-message">
                        <div class="message-body">
                            <div class="test-row">
                                <strong class="<?php echo $test['status'] ? 'has-text-success-dark' : 'has-text-danger-dark'; ?>"><?php echo htmlspecialchars($test['test']); ?></strong>
                                <span class="tag <?php echo $test['status'] ? 'is-success' : 'is-danger'; ?> is-light">
                                    <?php echo $test['status'] ? 'PASS' : 'FAIL'; ?>
                                </span>
                            </div>
                            <p class="test-message has-text-grey-dark"><?php echo htmlspecialchars($test['message']); ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>

                <div class="buttons is-centered mt-5 mb-4">
                    <a href="/" class="button is-link is-light">Back to Environment Info</a>
                    <a href="/tests/security-test.php" class="button is-info is-light">Security Tests</a>
                </div>
            </div>
        </div>
    </section>
</body>
</html>