<?php
/**
 * ProgenPHP - Main Entry Point
 * 
 * This file displays hosting environment information and serves as the main
 * entry point for the application.
 */

// Include configuration if available
$configPath = dirname(__DIR__) . '/private/config/app.php';
$config = file_exists($configPath) ? include $configPath : [];

// Set default timezone
date_default_timezone_set($config['timezone'] ?? 'UTC');

// Start output buffering for cleaner output
ob_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProgenPHP - Environment Info</title>
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
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 2.5em;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .info-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            border-left: 4px solid #3498db;
        }
        .info-card h3 {
            margin-top: 0;
            color: #2c3e50;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .info-table th,
        .info-table td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .info-table th {
            background-color: #ecf0f1;
            font-weight: 600;
        }
        .status-ok { color: #27ae60; }
        .status-warning { color: #f39c12; }
        .status-error { color: #e74c3c; }
        .footer {
            background: #ecf0f1;
            padding: 20px;
            text-align: center;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ProgenPHP</h1>
            <p>Hosting Environment Information</p>
            <p>Generated on: <?php echo date('Y-m-d H:i:s T'); ?></p>
        </div>
        
        <div class="content">
            <div class="info-grid">
                <!-- Server Information -->
                <div class="info-card">
                    <h3>Server Information</h3>
                    <table class="info-table">
                        <tr>
                            <th>Server Software</th>
                            <td><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></td>
                        </tr>
                        <tr>
                            <th>Server Name</th>
                            <td><?php echo $_SERVER['SERVER_NAME'] ?? 'Unknown'; ?></td>
                        </tr>
                        <tr>
                            <th>Server IP</th>
                            <td><?php echo $_SERVER['SERVER_ADDR'] ?? 'Unknown'; ?></td>
                        </tr>
                        <tr>
                            <th>Server Port</th>
                            <td><?php echo $_SERVER['SERVER_PORT'] ?? 'Unknown'; ?></td>
                        </tr>
                        <tr>
                            <th>Document Root</th>
                            <td><?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?></td>
                        </tr>
                    </table>
                </div>

                <!-- PHP Information -->
                <div class="info-card">
                    <h3>PHP Environment</h3>
                    <table class="info-table">
                        <tr>
                            <th>PHP Version</th>
                            <td class="<?php echo version_compare(PHP_VERSION, '7.4', '>=') ? 'status-ok' : 'status-warning'; ?>">
                                <?php echo PHP_VERSION; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>PHP SAPI</th>
                            <td><?php echo php_sapi_name(); ?></td>
                        </tr>
                        <tr>
                            <th>Memory Limit</th>
                            <td><?php echo ini_get('memory_limit'); ?></td>
                        </tr>
                        <tr>
                            <th>Max Execution Time</th>
                            <td><?php echo ini_get('max_execution_time'); ?>s</td>
                        </tr>
                        <tr>
                            <th>Upload Max Filesize</th>
                            <td><?php echo ini_get('upload_max_filesize'); ?></td>
                        </tr>
                    </table>
                </div>

                <!-- System Information -->
                <div class="info-card">
                    <h3>System Information</h3>
                    <table class="info-table">
                        <tr>
                            <th>Operating System</th>
                            <td><?php echo PHP_OS; ?></td>
                        </tr>
                        <tr>
                            <th>System Load</th>
                            <td>
                                <?php 
                                if (function_exists('sys_getloadavg')) {
                                    $load = sys_getloadavg();
                                    echo implode(', ', array_map(function($l) { return number_format($l, 2); }, $load));
                                } else {
                                    echo 'Not available';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Disk Free Space</th>
                            <td><?php echo formatBytes(disk_free_space('.')); ?></td>
                        </tr>
                        <tr>
                            <th>Current Directory</th>
                            <td><?php echo getcwd(); ?></td>
                        </tr>
                    </table>
                </div>

                <!-- Extensions Status -->
                <div class="info-card">
                    <h3>Important Extensions</h3>
                    <table class="info-table">
                        <?php
                        $important_extensions = [
                            'curl' => 'cURL',
                            'json' => 'JSON',
                            'mbstring' => 'Multibyte String',
                            'pdo' => 'PDO',
                            'openssl' => 'OpenSSL',
                            'zip' => 'ZIP',
                            'gd' => 'GD',
                            'xml' => 'XML'
                        ];
                        
                        foreach ($important_extensions as $ext => $name) {
                            $loaded = extension_loaded($ext);
                            echo '<tr>';
                            echo '<th>' . $name . '</th>';
                            echo '<td class="' . ($loaded ? 'status-ok' : 'status-error') . '">';
                            echo $loaded ? '✓ Loaded' : '✗ Not loaded';
                            echo '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </table>
                </div>

                <!-- System Utilities -->
                <div class="info-card">
                    <h3>System Utilities</h3>
                    <table class="info-table">
                        <?php
                        // Check for system utilities
                        $utilities = [
                            'gs' => ['name' => 'GhostScript (gs)', 'description' => 'PDF processing'],
                            'convert' => ['name' => 'ImageMagick (convert)', 'description' => 'Image processing'],
                            'ffmpeg' => ['name' => 'FFmpeg', 'description' => 'Video/audio processing'],
                            'git' => ['name' => 'Git', 'description' => 'Version control'],
                            'curl' => ['name' => 'cURL', 'description' => 'HTTP client'],
                        ];
                        
                        foreach ($utilities as $cmd => $info) {
                            // Check if utility is available
                            $available = false;
                            $version = '';
                            
                            if (function_exists('exec') && !in_array('exec', explode(',', ini_get('disable_functions')))) {
                                $output = [];
                                $return_var = null;
                                
                                // Try different version commands
                                if ($cmd === 'gs') {
                                    @exec('gs --version 2>&1', $output, $return_var);
                                } elseif ($cmd === 'convert') {
                                    @exec('convert -version 2>&1', $output, $return_var);
                                } elseif ($cmd === 'ffmpeg') {
                                    @exec('ffmpeg -version 2>&1', $output, $return_var);
                                } else {
                                    @exec($cmd . ' --version 2>&1', $output, $return_var);
                                }
                                
                                if ($return_var === 0 && !empty($output)) {
                                    $available = true;
                                    $version = isset($output[0]) ? substr($output[0], 0, 50) : 'Available';
                                }
                            }
                            
                            echo '<tr>';
                            echo '<th>' . $info['name'] . '</th>';
                            echo '<td class="' . ($available ? 'status-ok' : 'status-error') . '">';
                            if ($available) {
                                echo '✓ Available';
                                if ($version && $version !== 'Available') {
                                    echo '<br><small>' . htmlspecialchars($version) . '</small>';
                                }
                            } else {
                                echo '✗ Not available';
                            }
                            echo '<br><small>' . $info['description'] . '</small>';
                            echo '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </table>
                </div>
            </div>

            <!-- Security Information -->
            <div class="info-card">
                <h3>Security & Configuration</h3>
                <table class="info-table">
                    <tr>
                        <th>Private Folder Protection</th>
                        <td class="<?php echo file_exists(dirname(__DIR__) . '/private/.htaccess') ? 'status-ok' : 'status-warning'; ?>">
                            <?php echo file_exists(dirname(__DIR__) . '/private/.htaccess') ? '✓ Protected' : '⚠ Not protected'; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Configuration File</th>
                        <td class="<?php echo file_exists($configPath) ? 'status-ok' : 'status-warning'; ?>">
                            <?php echo file_exists($configPath) ? '✓ Found' : '⚠ Not found'; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Error Reporting</th>
                        <td><?php echo error_reporting() ? 'Enabled' : 'Disabled'; ?></td>
                    </tr>
                    <tr>
                        <th>Display Errors</th>
                        <td class="<?php echo ini_get('display_errors') ? 'status-warning' : 'status-ok'; ?>">
                            <?php echo ini_get('display_errors') ? 'On (Development)' : 'Off (Production)'; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Test Navigation -->
        <div style="background: #ecf0f1; padding: 20px; text-align: center; margin: 0;">
            <h3 style="margin-top: 0; color: #2c3e50;">Test Pages</h3>
            <p>Use these pages to verify your installation and security configuration:</p>
            <div style="margin: 20px 0;">
                <a href="/tests/access-test.php" style="display: inline-block; margin: 0 10px; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px;">
                    Access Tests
                </a>
                <a href="/tests/security-test.php" style="display: inline-block; margin: 0 10px; padding: 10px 20px; background: #27ae60; color: white; text-decoration: none; border-radius: 5px;">
                    Security Tests
                </a>
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; <?php echo date('Y'); ?> ProgenPHP - Environment Information Dashboard</p>
            <p>For security reasons, consider disabling this page in production environments.</p>
        </div>
    </div>
</body>
</html>

<?php
/**
 * Helper function to format bytes into human readable format
 */
function formatBytes($size, $precision = 2) {
    $base = log($size, 1024);
    $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];
    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
}

// End output buffering and send content
ob_end_flush();
?>