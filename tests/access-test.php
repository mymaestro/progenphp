<?php
/**
 * Access Test Page
 * 
 * This page tests folder access permissions and demonstrates
 * proper file inclusion from private directories.
 */

// Test results array
$accessTests = [];

// Test 1: Include private configuration
try {
    $config = include __DIR__ . '/../private/config/app.php';
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
    include_once __DIR__ . '/../private/utils/functions.php';
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
    'Public Directory' => __DIR__ . '/../public/',
    'Private Directory' => __DIR__ . '/../private/',
    'Config Directory' => __DIR__ . '/../private/config/',
    'Utils Directory' => __DIR__ . '/../private/utils/',
    'Logs Directory' => __DIR__ . '/../private/logs/',
    'Cache Directory' => __DIR__ . '/../private/cache/',
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
$cacheTestFile = __DIR__ . '/../private/cache/test_' . time() . '.tmp';
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
$logTestFile = __DIR__ . '/../private/logs/test_' . time() . '.tmp';
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
            background: <?php echo $allPassed ? 'linear-gradient(135deg, #27ae60 0%, #2ecc71 100%)' : 'linear-gradient(135deg, #f39c12 0%, #e67e22 100%)'; ?>;
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
        .summary {
            background: #ecf0f1;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: center;
        }
        .summary h3 {
            margin-top: 0;
        }
        .test-result {
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            background: #f8f9fa;
            border-left: 4px solid;
        }
        .test-success {
            border-color: #27ae60;
            background: #d5f4e6;
        }
        .test-failure {
            border-color: #e74c3c;
            background: #ffeaa7;
        }
        .test-icon {
            margin-right: 15px;
            font-size: 1.2em;
            font-weight: bold;
        }
        .test-content {
            flex-grow: 1;
        }
        .test-name {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .test-message {
            color: #666;
            font-size: 0.9em;
        }
        .navigation {
            margin-top: 30px;
            text-align: center;
        }
        .nav-link {
            display: inline-block;
            margin: 0 10px;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .nav-link:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Access Tests</h1>
            <p>Testing directory access and file permissions</p>
        </div>
        
        <div class="content">
            <div class="summary">
                <h3>Test Summary</h3>
                <p><strong><?php echo $passedTests; ?></strong> out of <strong><?php echo $totalTests; ?></strong> tests passed</p>
                <?php if ($allPassed): ?>
                    <p style="color: #27ae60;">✓ All access tests passed successfully!</p>
                <?php else: ?>
                    <p style="color: #e74c3c;">⚠ Some tests failed. Check the results below.</p>
                <?php endif; ?>
            </div>
            
            <?php foreach ($accessTests as $test): ?>
                <div class="test-result <?php echo $test['status'] ? 'test-success' : 'test-failure'; ?>">
                    <div class="test-icon"><?php echo $test['status'] ? '✓' : '✗'; ?></div>
                    <div class="test-content">
                        <div class="test-name"><?php echo htmlspecialchars($test['test']); ?></div>
                        <div class="test-message"><?php echo htmlspecialchars($test['message']); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <div class="navigation">
                <a href="/" class="nav-link">← Environment Info</a>
                <a href="/tests/security-test.php" class="nav-link">Security Tests</a>
            </div>
        </div>
    </div>
</body>
</html>