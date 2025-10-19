<?php
// Simple debug script to test server configuration
echo "<h1>Debug Info</h1>";
echo "<h2>Current Directory:</h2>";
echo "<pre>" . __DIR__ . "</pre>";

echo "<h2>File Exists Check:</h2>";
echo "access-test.php exists: " . (file_exists(__DIR__ . '/access-test.php') ? 'YES' : 'NO') . "<br>";
echo "security-test.php exists: " . (file_exists(__DIR__ . '/security-test.php') ? 'YES' : 'NO') . "<br>";

echo "<h2>Directory Contents:</h2>";
echo "<pre>";
print_r(scandir(__DIR__));
echo "</pre>";

echo "<h2>Server Info:</h2>";
echo "Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "<br>";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "<br>";
echo "Script Name: " . ($_SERVER['SCRIPT_NAME'] ?? 'Unknown') . "<br>";
echo "Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'Unknown') . "<br>";

echo "<h2>Test Links:</h2>";
echo '<a href="access-test.php">Access Test</a><br>';
echo '<a href="security-test.php">Security Test</a><br>';
echo '<a href="../">Back to Main</a>';
?>