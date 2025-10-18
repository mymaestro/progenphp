# ProgenPHP Deployment on Hostinger

## 🌐 Hostinger-Specific Deployment Guide

Hostinger offers shared hosting with cPanel/hPanel and supports SSH on most plans. Here's how to deploy ProgenPHP on Hostinger.

## 📋 Prerequisites

- Hostinger hosting account (Business plan or higher recommended for SSH access)
- Domain configured in Hostinger
- SSH access enabled (available on Premium, Business, and Cloud plans)

## 🚀 Deployment Methods for Hostinger

### Method 1: SSH Deployment (Recommended - Business Plan+)

#### Step 1: Enable SSH Access
1. Log into your Hostinger hPanel
2. Go to **Advanced → SSH Access**
3. Enable SSH access
4. Note your SSH details:
   - **Host**: Usually `ssh.hostinger.com` or your domain
   - **Port**: Usually `65002` (not standard 22)
   - **Username**: Your hPanel username
   - **Password**: Your hPanel password

#### Step 2: Deploy via SSH
```bash
# SSH into Hostinger (note the custom port)
ssh -p 65002 username@ssh.hostinger.com
# Or sometimes: ssh -p 65002 username@yourdomain.com

# Navigate to your domain's public_html directory
cd domains/yourdomain.com/public_html
# Or if it's your primary domain: cd public_html

# Clone your repository
git clone https://github.com/yourusername/progenphp.git
cd progenphp

# Set permissions (Hostinger-friendly)
chmod 755 public private tests
chmod 644 public/* private/config/* private/utils/*
chmod 700 private/config/app.php  # Extra secure on shared hosting
chmod 755 private/logs private/cache
```

### Method 2: File Manager Upload (All Plans)

#### Step 1: Prepare Files Locally
```bash
# Create a deployment package
cd /home/gill/public_html/progenphp
zip -r progenphp-hostinger.zip . -x ".git/*" ".venv/*" "*.tmp" "router.php"
```

#### Step 2: Upload via hPanel
1. Log into Hostinger hPanel
2. Go to **Files → File Manager**
3. Navigate to `domains/yourdomain.com/public_html` (or just `public_html`)
4. Click **Upload** and select your `progenphp-hostinger.zip`
5. Right-click the uploaded zip and select **Extract**
6. Delete the zip file after extraction

### Method 3: Git via File Manager (Alternative)
1. In File Manager, create a new folder called `progenphp`
2. Use the **Git Clone** feature if available, or upload files manually

## 🔧 Hostinger-Specific Configuration

### 1. Domain Setup Options

#### Option A: Subdirectory Access
- Upload to: `public_html/progenphp/`
- Access via: `https://yourdomain.com/progenphp/`
- No additional configuration needed

#### Option B: Subdomain (Recommended)
1. In hPanel, go to **Domains → Subdomains**
2. Create subdomain: `app.yourdomain.com`
3. Set document root to: `public_html/progenphp/public`
4. Access via: `https://app.yourdomain.com/`

#### Option C: Main Domain
1. Move contents of `progenphp/public/` to `public_html/`
2. Move `progenphp/private/` to `public_html/private/`
3. Move `progenphp/tests/` to `public_html/tests/`
4. Update file paths in the code

### 2. PHP Configuration
Hostinger usually runs PHP 8+ by default. To check/change:
1. Go to **Advanced → PHP Configuration**
2. Select PHP 8.1 or 8.2
3. Enable extensions: `curl`, `json`, `mbstring`, `pdo_mysql`

### 3. Database Setup (if needed)
```bash
# In hPanel, go to Databases → MySQL Databases
# Create database: username_progenphp
# Create user: username_progenuser
# Grant all privileges

# Then update private/config/app.php:
'database' => [
    'default' => [
        'host' => 'localhost',
        'database' => 'username_progenphp',
        'username' => 'username_progenuser',
        'password' => 'your_secure_password',
    ],
],
```

## 🛠 Hostinger Quick Deploy Script

Create a Hostinger-specific deployment script:

```bash
#!/bin/bash
# hostinger-deploy.sh - Hostinger-specific deployment

# Configuration for Hostinger
HOSTINGER_HOST="ssh.hostinger.com"
HOSTINGER_PORT="65002"
HOSTINGER_USER=$1
HOSTINGER_DOMAIN=$2

if [ -z "$HOSTINGER_USER" ] || [ -z "$HOSTINGER_DOMAIN" ]; then
    echo "Usage: $0 <hostinger-username> <yourdomain.com>"
    echo "Example: $0 u123456789 mysite.com"
    exit 1
fi

echo "🚀 Deploying to Hostinger..."
echo "Host: $HOSTINGER_HOST:$HOSTINGER_PORT"
echo "User: $HOSTINGER_USER"
echo "Domain: $HOSTINGER_DOMAIN"

# Create deployment package
echo "📦 Creating deployment package..."
zip -r progenphp-deploy.zip . -x ".git/*" ".venv/*" "*.tmp" "router.php" "hostinger-*"

# Upload via SCP
echo "📤 Uploading files..."
scp -P $HOSTINGER_PORT progenphp-deploy.zip $HOSTINGER_USER@$HOSTINGER_HOST:domains/$HOSTINGER_DOMAIN/public_html/

# SSH and extract
echo "📂 Extracting and configuring..."
ssh -p $HOSTINGER_PORT $HOSTINGER_USER@$HOSTINGER_HOST << EOF
cd domains/$HOSTINGER_DOMAIN/public_html
unzip -q progenphp-deploy.zip -d progenphp/
rm progenphp-deploy.zip
cd progenphp
chmod 755 public private tests
chmod 644 public/* private/config/* private/utils/* 2>/dev/null || true
chmod 700 private/config/app.php
chmod 755 private/logs private/cache
echo "✅ Deployment completed!"
echo "🌐 Visit: https://$HOSTINGER_DOMAIN/progenphp/"
EOF

# Clean up local zip
rm progenphp-deploy.zip

echo "🎉 Hostinger deployment finished!"
```

Make it executable:
```bash
chmod +x hostinger-deploy.sh
```

Usage:
```bash
./hostinger-deploy.sh u123456789 yourdomain.com
```

## 🔒 Hostinger Security Configuration

### 1. Secure Private Directory
Create additional protection in your private folder:

```apache
# Add to private/.htaccess (Hostinger-specific)
Options -Indexes
<Files "*">
    Order Deny,Allow
    Deny from all
</Files>

# Block common attack patterns
RewriteEngine On
RewriteCond %{QUERY_STRING} (\<|%3C).*script.*(\>|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} GLOBALS(=|\[|\%[0-9A-Z]{0,2}) [OR]
RewriteCond %{QUERY_STRING} _REQUEST(=|\[|\%[0-9A-Z]{0,2})
RewriteRule ^(.*)$ index.php [F,L]
```

### 2. Production Configuration for Hostinger
```php
// Update private/config/app.php for Hostinger
return [
    'app' => [
        'name' => 'ProgenPHP',
        'debug' => false,  // Always false on shared hosting
        'environment' => 'production',
    ],
    
    // Hostinger-specific settings
    'logging' => [
        'enabled' => true,
        'level' => 'error',  // Reduce logging on shared hosting
        'file' => __DIR__ . '/../logs/app.log',
    ],
    
    // Shared hosting friendly upload limits
    'upload' => [
        'max_size' => 1 * 1024 * 1024, // 1MB (shared hosting limits)
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf'],
    ],
];
```

### 3. Remove Test Pages for Production
```bash
# SSH into Hostinger
ssh -p 65002 username@ssh.hostinger.com
cd domains/yourdomain.com/public_html/progenphp

# Remove test directory
rm -rf tests/

# Or restrict access
chmod 700 tests/
echo "deny from all" > tests/.htaccess
```

## 🌐 SSL Certificate on Hostinger

Hostinger provides free SSL certificates:
1. Go to **Security → SSL**
2. Enable **Free SSL Certificate**
3. Wait for activation (usually 15-30 minutes)
4. Force HTTPS redirects in hPanel

## 📊 Hostinger-Specific Monitoring

### Check PHP Error Logs
```bash
# SSH access
tail -f ~/logs/error.log

# Or via File Manager
# Navigate to: domains/yourdomain.com/logs/error.log
```

### Performance Tips for Hostinger
1. **Use caching**: Enable in `private/config/app.php`
2. **Optimize images**: Keep file sizes small
3. **Database optimization**: Use indexes, limit queries
4. **Enable compression**: Already configured in .htaccess

## 🔧 Troubleshooting Common Hostinger Issues

### Issue 1: Permission Errors
```bash
# Fix permissions
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chmod 700 private/config/
```

### Issue 2: PHP Version
- Check current version: Create `info.php` with `<?php phpinfo(); ?>`
- Change version in hPanel → Advanced → PHP Configuration

### Issue 3: Memory Limits
Add to your .htaccess:
```apache
php_value memory_limit 256M
php_value max_execution_time 300
php_value upload_max_filesize 10M
```

### Issue 4: Database Connection
- Ensure database host is `localhost`
- Use full database name: `username_dbname`
- Check MySQL version compatibility

## 📱 Quick Hostinger Checklist

After deployment:
- [ ] Visit `https://yourdomain.com/progenphp/`
- [ ] Check environment info page loads
- [ ] Verify private folder is protected
- [ ] Test database connection (if configured)
- [ ] Enable SSL certificate
- [ ] Remove or secure test pages
- [ ] Set up error monitoring
- [ ] Configure backups in hPanel

## 🎯 Example URLs for Hostinger

- **Main site**: `https://yourdomain.com/progenphp/`
- **Subdomain**: `https://app.yourdomain.com/`
- **Environment info**: Shows server details and Hostinger-specific information
- **Tests**: `https://yourdomain.com/progenphp/tests/` (remove for production)

This guide should get you up and running on Hostinger quickly! Let me know if you need help with any specific part of the deployment.