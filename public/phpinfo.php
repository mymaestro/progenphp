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
echo "<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bulma@1.0.2/css/bulma.min.css'>";
echo "<style>.hero,.box,.notification,.message,.message-body,.button,.tag,.input,.textarea,.select select,.table{border-radius:0!important}.box,.notification,.message,.hero{box-shadow:none!important}.box,.notification,.message,.hero,.table{border:1px solid hsl(0,0%,86%)}</style>";
echo "<div class='has-background-dark'>";
echo "<section class='section'><div class='container is-max-desktop'>";
echo "<div class='box'>";
echo "<h1 class='title has-text-centered'>ProgenPHP - Complete PHP Information</h1>";
echo "<div class='has-text-centered'>";
echo "<a href='index.php' class='button is-link is-light'>Back to Main Page</a>";
echo "</div>";
echo "</div>";
echo "</div></section>";
echo "</div>";

// Display complete phpinfo
phpinfo();
?>