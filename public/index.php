<?php
/**
 * ProgenPHP - Main Entry Point
 * 
 * This file displays hosting environment information and serves as the main
 * entry point for the application.
 */

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

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
            font-size: 1.75rem;
            letter-spacing: 0.02em;
        }

        .dashboard-hero .subtitle {
            font-size: 0.95rem;
            margin-bottom: 0.4rem;
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

        .dashboard-card .table {
            margin: 0;
            font-size: 0.9rem;
        }

        .dashboard-card .table th,
        .dashboard-card .table td {
            padding: 0.5rem 0.75rem;
            vertical-align: top;
        }

        .dashboard-footer {
            font-size: 0.9rem;
        }
    </style>
</head>
<body class="has-background-dark">
    <section class="section">
        <div class="container is-max-widescreen">
            <section class="hero is-light is-small mb-5 dashboard-hero">
                <div class="hero-body has-text-centered">
                    <p class="title">ProgenPHP</p>
                    <p class="subtitle">Hosting Environment Information</p>
                    <p>Generated on: <?php echo date('Y-m-d H:i:s T'); ?></p>
                    <div class="buttons is-centered mt-4">
                        <a href="phpinfo.php?v=<?php echo time(); ?>" class="button is-link is-light">Complete phpinfo()</a>
                        <a href="tests/access-test.php?v=<?php echo time(); ?>" class="button is-info is-light">Run Tests</a>
                        <a href="index.php?v=<?php echo time(); ?>" class="button is-light">Refresh</a>
                    </div>
                </div>
            </section>

            <div class="columns is-multiline">
                <div class="column is-12-tablet is-6-desktop is-4-widescreen">
                    <div class="box dashboard-card">
                        <h3 class="title is-5 dashboard-card-title">Server Information</h3>
                        <table class="table is-fullwidth is-hoverable is-striped">
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
                </div>

                <div class="column is-12-tablet is-6-desktop is-4-widescreen">
                    <div class="box dashboard-card">
                        <h3 class="title is-5 dashboard-card-title">PHP Environment</h3>
                        <table class="table is-fullwidth is-hoverable is-striped">
                            <tr>
                                <th>PHP Version</th>
                                <td class="<?php echo version_compare(PHP_VERSION, '7.4', '>=') ? 'has-text-success-dark' : 'has-text-warning-dark'; ?>">
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
                </div>

                <div class="column is-12-tablet is-6-desktop is-4-widescreen">
                    <div class="box dashboard-card">
                        <h3 class="title is-5 dashboard-card-title">System Information</h3>
                        <table class="table is-fullwidth is-hoverable is-striped">
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
                </div>

                <div class="column is-12-tablet is-6-desktop is-6-widescreen">
                    <div class="box dashboard-card">
                        <h3 class="title is-5 dashboard-card-title">Important Extensions</h3>
                        <table class="table is-fullwidth is-hoverable is-striped">
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
                                echo '<td class="' . ($loaded ? 'has-text-success-dark' : 'has-text-danger-dark') . '">';
                                echo $loaded ? 'Loaded' : 'Not loaded';
                                echo '</td>';
                                echo '</tr>';
                            }
                            ?>
                        </table>
                    </div>
                </div>

                <div class="column is-12-tablet is-6-desktop is-6-widescreen">
                    <div class="box dashboard-card">
                        <h3 class="title is-5 dashboard-card-title">System Utilities</h3>
                        <table class="table is-fullwidth is-hoverable is-striped">
                            <tr>
                                <th>exec() Function</th>
                                <td class="<?php echo function_exists('exec') ? 'has-text-success-dark' : 'has-text-danger-dark'; ?>">
                                    <?php echo function_exists('exec') ? 'Available' : 'Not available'; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Disabled Functions</th>
                                <td class="<?php echo strpos(ini_get('disable_functions'), 'exec') !== false ? 'has-text-danger-dark' : 'has-text-success-dark'; ?>">
                                    <?php
                                    $disabled = ini_get('disable_functions');
                                    if (empty($disabled)) {
                                        echo 'None disabled';
                                    } else {
                                        echo 'Disabled: ' . htmlspecialchars($disabled);
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                            $utilities = [
                                'curl_php' => ['name' => 'cURL (PHP Extension)', 'description' => 'HTTP requests via PHP'],
                                'imagick' => ['name' => 'Imagick (PHP Extension)', 'description' => 'Advanced image/PDF processing via PHP'],
                                'gd' => ['name' => 'GD (PHP Extension)', 'description' => 'Basic image processing via PHP'],
                                'pdf' => ['name' => 'PDF Extensions', 'description' => 'PDF generation and processing'],
                            ];
                            $system_utilities = [
                                'gs' => ['name' => 'GhostScript (gs)', 'description' => 'PDF processing (exec disabled)'],
                                'convert' => ['name' => 'ImageMagick (convert)', 'description' => 'Image processing (exec disabled)'],
                                'ffmpeg' => ['name' => 'FFmpeg', 'description' => 'Video/audio processing (exec disabled)'],
                                'git' => ['name' => 'Git', 'description' => 'Version control (exec disabled)'],
                            ];

                            foreach ($utilities as $ext => $info) {
                                $available = false;
                                $version = '';

                                if ($ext === 'curl_php') {
                                    $available = extension_loaded('curl');
                                    if ($available) {
                                        $curl_info = curl_version();
                                        $version = 'cURL ' . $curl_info['version'];
                                    }
                                } elseif ($ext === 'imagick') {
                                    $available = extension_loaded('imagick') && class_exists('Imagick');
                                    if ($available) {
                                        try {
                                            $reflection = new ReflectionClass('Imagick');
                                            $version = 'Available (ImageMagick PHP extension)';
                                        } catch (Exception $e) {
                                            $version = 'Available';
                                        }
                                    }
                                } elseif ($ext === 'gd') {
                                    $available = extension_loaded('gd');
                                    if ($available) {
                                        $gd_info = gd_info();
                                        $version = $gd_info['GD Version'];
                                    }
                                } elseif ($ext === 'pdf') {
                                    $pdf_extensions = [];

                                    if (extension_loaded('pdf')) {
                                        $pdf_extensions[] = 'PDFlib';
                                    }
                                    if (class_exists('TCPDF')) {
                                        $pdf_extensions[] = 'TCPDF';
                                    }
                                    if (class_exists('FPDF')) {
                                        $pdf_extensions[] = 'FPDF';
                                    }
                                    if (class_exists('Dompdf\\Dompdf')) {
                                        $pdf_extensions[] = 'DomPDF';
                                    }
                                    if (class_exists('Mpdf\\Mpdf')) {
                                        $pdf_extensions[] = 'mPDF';
                                    }
                                    if (extension_loaded('imagick') && class_exists('Imagick')) {
                                        try {
                                            $imagick = new ReflectionClass('Imagick');
                                            if (method_exists('Imagick', 'readImage')) {
                                                $pdf_extensions[] = 'Imagick (PDF support)';
                                            }
                                        } catch (Exception $e) {
                                        }
                                    }

                                    $available = !empty($pdf_extensions);
                                    if ($available) {
                                        $version = implode(', ', $pdf_extensions);
                                    }
                                }

                                echo '<tr>';
                                echo '<th>' . $info['name'] . '</th>';
                                echo '<td class="' . ($available ? 'has-text-success-dark' : 'has-text-danger-dark') . '">';
                                if ($available) {
                                    echo 'Available';
                                    if ($version) {
                                        echo '<br><small>' . htmlspecialchars($version) . '</small>';
                                    }
                                } else {
                                    echo 'Not available';
                                }
                                echo '<br><small>' . $info['description'] . '</small>';
                                echo '</td>';
                                echo '</tr>';
                            }

                            foreach ($system_utilities as $cmd => $info) {
                                $available = false;
                                $version = '';
                                $debugInfo = [];

                                if (!function_exists('exec')) {
                                    $debugInfo[] = 'exec() function not available';
                                } elseif (in_array('exec', explode(',', ini_get('disable_functions')))) {
                                    $debugInfo[] = 'exec() function disabled by hosting provider';
                                } else {
                                    $output = [];
                                    $return_var = null;

                                    $commands = [];
                                    if ($cmd === 'gs') {
                                        $commands = [
                                            'gs --version',
                                            '/usr/bin/gs --version',
                                            '/usr/local/bin/gs --version',
                                            'which gs'
                                        ];
                                    } elseif ($cmd === 'convert') {
                                        $commands = [
                                            'convert -version',
                                            '/usr/bin/convert -version',
                                            '/usr/local/bin/convert -version'
                                        ];
                                    } elseif ($cmd === 'curl') {
                                        $commands = [
                                            'curl --version',
                                            '/usr/bin/curl --version',
                                            '/usr/local/bin/curl --version'
                                        ];
                                    } else {
                                        $commands = [
                                            $cmd . ' --version',
                                            '/usr/bin/' . $cmd . ' --version',
                                            '/usr/local/bin/' . $cmd . ' --version'
                                        ];
                                    }

                                    foreach ($commands as $command) {
                                        $output = [];
                                        $return_var = null;
                                        @exec($command . ' 2>&1', $output, $return_var);

                                        if ($return_var === 0 && !empty($output)) {
                                            $available = true;
                                            $version = isset($output[0]) ? substr($output[0], 0, 80) : 'Available';
                                            break;
                                        }
                                    }

                                    if (!$available) {
                                        $path_output = [];
                                        @exec('echo $PATH 2>&1', $path_output, $path_return);
                                        if (!empty($path_output)) {
                                            $debugInfo[] = 'PATH: ' . substr($path_output[0], 0, 100);
                                        }

                                        $which_output = [];
                                        @exec('which ' . $cmd . ' 2>&1', $which_output, $which_return);
                                        if ($which_return === 0 && !empty($which_output)) {
                                            $debugInfo[] = 'Found at: ' . $which_output[0];
                                        } else {
                                            $debugInfo[] = 'Not found in PATH';
                                        }
                                    }
                                }

                                echo '<tr>';
                                echo '<th>' . $info['name'] . '</th>';
                                echo '<td class="' . ($available ? 'has-text-success-dark' : 'has-text-danger-dark') . '">';
                                if ($available) {
                                    echo 'Available';
                                    if ($version && $version !== 'Available') {
                                        echo '<br><small>' . htmlspecialchars($version) . '</small>';
                                    }
                                } else {
                                    echo 'Not available';
                                    if (!empty($debugInfo)) {
                                        echo '<br><small>Debug: ' . htmlspecialchars(implode('; ', $debugInfo)) . '</small>';
                                    }
                                }
                                echo '<br><small>' . $info['description'] . '</small>';
                                echo '</td>';
                                echo '</tr>';
                            }
                            ?>
                        </table>
                    </div>
                </div>

                <div class="column is-12">
                    <div class="box dashboard-card">
                        <h3 class="title is-5 dashboard-card-title">Security & Configuration</h3>
                        <table class="table is-fullwidth is-hoverable is-striped">
                            <tr>
                                <th>Private Folder Protection</th>
                                <td class="<?php echo file_exists(dirname(__DIR__) . '/private/.htaccess') ? 'has-text-success-dark' : 'has-text-warning-dark'; ?>">
                                    <?php echo file_exists(dirname(__DIR__) . '/private/.htaccess') ? 'Protected' : 'Not protected'; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Configuration File</th>
                                <td class="<?php echo file_exists($configPath) ? 'has-text-success-dark' : 'has-text-warning-dark'; ?>">
                                    <?php echo file_exists($configPath) ? 'Found' : 'Not found'; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Error Reporting</th>
                                <td><?php echo error_reporting() ? 'Enabled' : 'Disabled'; ?></td>
                            </tr>
                            <tr>
                                <th>Display Errors</th>
                                <td class="<?php echo ini_get('display_errors') ? 'has-text-warning-dark' : 'has-text-success-dark'; ?>">
                                    <?php echo ini_get('display_errors') ? 'On (Development)' : 'Off (Production)'; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="box has-text-centered dashboard-footer">
                <h3 class="title is-5">Test Pages</h3>
                <p>Use these pages to verify your installation and security configuration:</p>
                <div class="buttons is-centered mt-4">
                    <a href="/tests/access-test.php" class="button is-link is-light">Access Tests</a>
                    <a href="/tests/security-test.php" class="button is-info is-light">Security Tests</a>
                </div>
            </div>

            <div class="notification is-light has-text-centered dashboard-footer">
                <p>&copy; <?php echo date('Y'); ?> ProgenPHP - Environment Information Dashboard</p>
                <p>For security reasons, consider disabling this page in production environments.</p>
            </div>
        </div>
    </section>
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