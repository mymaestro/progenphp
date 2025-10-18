# Logs Directory

This directory contains application log files.

**Security Notice:** This directory is protected by .htaccess rules to prevent direct web access.

## Log Files

- `app.log` - Main application log file
- `error.log` - PHP error log (if configured)
- `access.log` - Custom access log (if implemented)

## Log Rotation

Consider implementing log rotation to prevent log files from growing too large:

```bash
# Example logrotate configuration
/path/to/logs/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 0644 www-data www-data
}
```