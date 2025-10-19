<?php
/**
 * ProgenPHP - Pure phpinfo() Output
 * 
 * This page displays the complete PHP configuration information
 * using the native phpinfo() function.
 * 
 * Access: /phpinfo.php
 */

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Security check - only allow access from localhost or specific IPs if needed
// Uncomment the following lines if you want to restrict access:
/*
$allowed_ips = ['127.0.0.1', '::1']; // Add your IP addresses here
if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
    http_response_code(403);
    die('Access denied');
}
*/

// Set page title in the output
echo "<h1 style='text-align: center; color: #2c3e50; margin-bottom: 30px;'>ProgenPHP - Complete PHP Information</h1>";
echo "<div style='text-align: center; margin-bottom: 20px;'>";
echo "<a href='index.php' style='background: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>← Back to Main Page</a>";
echo "</div>";

// Display complete phpinfo
phpinfo();
?>