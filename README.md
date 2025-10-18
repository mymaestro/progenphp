# ProgenPHP

A secure PHP project template with proper directory structure and security configurations for testing web host functionality and developing PHP applications.

## 🚀 Features

- **Secure Directory Structure**: Separate public and private folders
- **Environment Information Display**: Comprehensive hosting environment details
- **Security Configuration**: .htaccess protection for sensitive files
- **Configuration Management**: Centralized configuration system
- **Utility Functions**: Common PHP utility functions and database helpers
- **Test Suite**: Built-in security and access tests
- **Logging System**: Structured logging with configurable levels
- **CSRF Protection**: Built-in CSRF token generation and verification

## 📁 Directory Structure

```
progenphp/
├── .htaccess                    # Root security configuration
├── README.md                    # This file
├── public/                      # Web-accessible files
│   └── index.php               # Main entry point with environment info
├── private/                     # Protected from web access
│   ├── .htaccess               # Blocks direct web access
│   ├── config/                 # Configuration files
│   │   ├── app.php            # Main application configuration
│   │   └── environment.php    # Environment-specific settings
│   ├── utils/                  # Utility functions and classes
│   │   ├── functions.php      # Common utility functions
│   │   └── Database.php       # Database helper class
│   ├── logs/                   # Application log files
│   │   └── README.md          # Logging documentation
│   └── cache/                  # Cache and temporary files
│       └── README.md          # Cache documentation
└── tests/                      # Test pages
    ├── access-test.php         # Directory access tests
    └── security-test.php       # Security configuration tests
```

## 🔒 Security Features

### Directory Protection
- **Private Folder**: Protected by .htaccess rules preventing direct web access
- **Configuration Files**: Stored outside the web root
- **Sensitive Data**: Logs and cache directories are protected

### Access Controls
- CSRF token generation and verification
- Input sanitization functions
- Rate limiting capabilities
- Secure session management

### Security Headers
- X-Content-Type-Options
- X-Frame-Options
- X-XSS-Protection
- Referrer-Policy

## ⚙️ Configuration

### Application Configuration (`private/config/app.php`)
```php
return [
    'app' => [
        'name' => 'ProgenPHP',
        'debug' => true, // Set to false in production
        'environment' => 'development',
    ],
    'database' => [
        // Database configuration
    ],
    'security' => [
        // Security settings
    ],
    // ... more settings
];
```

### Environment-Specific Settings (`private/config/environment.php`)
Automatically detects and applies settings based on:
- `$_ENV['APP_ENV']`
- `$_SERVER['APP_ENV']`

## 🛠️ Usage

### 1. Basic Setup
1. Upload files to your web server
2. Ensure proper file permissions (755 for directories, 644 for files)
3. Configure your web server to point to the `public/` directory as the document root
4. Update configuration files in `private/config/`

### 2. Web Server Configuration

#### Apache (Recommended)
The included `.htaccess` files provide:
- Security headers
- Directory protection
- URL rewriting
- Compression and caching

#### Nginx
For Nginx, add equivalent configurations:
```nginx
location /private/ {
    deny all;
}

location ~ /\.(ht|git|svn) {
    deny all;
}
```

### 3. Testing Your Installation

Visit your site and use the built-in test pages:

1. **Environment Information**: `/public/index.php`
   - Displays server and PHP environment details
   - Shows security status
   - Lists available PHP extensions

2. **Access Tests**: `/tests/access-test.php`
   - Verifies directory permissions
   - Tests file inclusion capabilities
   - Checks write permissions for logs and cache

3. **Security Tests**: `/tests/security-test.php`
   - Validates private folder protection
   - Tests .htaccess configurations
   - Verifies CSRF token functionality

## 📊 Monitoring and Maintenance

### Logging
- Logs are stored in `private/logs/`
- Configure logging level in `private/config/app.php`
- Implement log rotation to prevent large files

### Cache Management
- Cache files stored in `private/cache/`
- Regularly clean old cache files
- Monitor disk usage

### Security Updates
- Regularly update PHP version
- Review and update security configurations
- Monitor access logs for suspicious activity

## 🔧 Development

### Adding New Features
1. Add configuration to `private/config/app.php`
2. Create utility functions in `private/utils/`
3. Add public-facing files to `public/`
4. Update tests in `tests/` directory

### Database Setup
1. Update database configuration in `private/config/app.php`
2. Use the Database helper class from `private/utils/Database.php`
3. Example usage:
```php
include_once '../private/utils/Database.php';
$db = new Database();
$results = $db->query("SELECT * FROM users WHERE active = ?", [1]);
```

### Custom Utility Functions
Add your functions to `private/utils/functions.php` or create new files in the utils directory.

## 🚨 Production Deployment

### Security Checklist
- [ ] Set `debug => false` in configuration
- [ ] Use strong database passwords
- [ ] Generate secure encryption keys
- [ ] Configure proper file permissions
- [ ] Set up SSL/TLS certificates
- [ ] Enable error logging (disable display_errors)
- [ ] Configure firewall rules
- [ ] Set up regular backups

### Performance Optimization
- Enable PHP opcache
- Configure proper caching headers
- Use GZIP compression
- Optimize database queries
- Monitor resource usage

## 📖 API Reference

### Utility Functions
See `private/utils/functions.php` for available functions:
- `sanitize($data)` - Sanitize input data
- `generateToken($length)` - Generate secure random tokens
- `validateEmail($email)` - Email validation
- `getClientIP()` - Get client IP address
- `logMessage($message, $level)` - Log messages
- `generateCSRFToken()` - Generate CSRF tokens
- `verifyCSRFToken($token)` - Verify CSRF tokens

### Database Helper
See `private/utils/Database.php` for database operations:
- `query($sql, $params)` - Execute SELECT queries
- `queryOne($sql, $params)` - Get single row
- `execute($sql, $params)` - Execute INSERT/UPDATE/DELETE
- `beginTransaction()` - Start transaction
- `commit()` - Commit transaction
- `rollback()` - Rollback transaction

## 📝 License

This project is open source and available under the [MIT License](LICENSE).

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Add tests for new functionality
4. Ensure all tests pass
5. Submit a pull request

## 📞 Support

For issues, questions, or contributions:
1. Check the test pages for configuration issues
2. Review the logs in `private/logs/`
3. Create an issue on GitHub
4. Review the documentation in each directory

---

**⚠️ Important Security Note**: Always remove or secure the test pages (`tests/` directory) and environment information page in production environments to prevent information disclosure.
