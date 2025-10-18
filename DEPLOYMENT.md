# ProgenPHP Deployment Guide

## 🚀 Deployment Methods

### Method 1: Git Deployment (Recommended)

#### Step 1: Push to Git Repository
```bash
# If not already initialized
git init
git add .
git commit -m "Initial ProgenPHP implementation"
git branch -M main
git remote add origin https://github.com/yourusername/progenphp.git
git push -u origin main
```

#### Step 2: Deploy via SSH
```bash
# SSH into your hosting server
ssh username@your-server.com

# Navigate to your web directory (common paths)
cd /var/www/html          # Apache default
cd /usr/share/nginx/html  # Nginx default
cd ~/public_html          # cPanel/shared hosting
cd ~/www                  # Some providers

# Clone your repository
git clone https://github.com/yourusername/progenphp.git
cd progenphp

# Set proper permissions
chmod 755 public private tests
chmod 644 public/* private/config/* private/utils/*
chmod 755 private/logs private/cache
chmod 600 private/config/app.php  # Extra security for config
```

### Method 2: Direct File Upload via SCP/SFTP

```bash
# From your local machine, upload files
scp -r /home/gill/public_html/progenphp/ username@your-server.com:/var/www/html/

# Or using rsync (better for updates)
rsync -avz --exclude='.git' /home/gill/public_html/progenphp/ username@your-server.com:/var/www/html/progenphp/
```

## 🌐 Web Server Configuration

### Apache Configuration

#### Option A: Document Root Points to /progenphp/public/
```apache
# In your virtual host or .htaccess
DocumentRoot /var/www/html/progenphp/public

<Directory /var/www/html/progenphp/public>
    AllowOverride All
    Require all granted
</Directory>
```

#### Option B: Access via Subdirectory
If you can't change document root, the existing .htaccess will redirect to public folder:
- Access via: `https://yourdomain.com/progenphp/`

### Nginx Configuration

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/html/progenphp/public;
    index index.php index.html;

    # Security: Block access to private directory
    location /private {
        deny all;
    }

    location ~ /\.(ht|git|svn) {
        deny all;
    }

    # PHP processing
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Handle URL rewriting
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
}
```

## 🔧 Post-Deployment Configuration

### 1. Environment Configuration
```bash
# SSH into your server
ssh username@your-server.com
cd /path/to/progenphp

# Set environment variable (optional)
echo 'export APP_ENV=production' >> ~/.bashrc
source ~/.bashrc

# Or create .env file
echo 'APP_ENV=production' > .env
```

### 2. Update Configuration Files
```bash
# Edit configuration for production
nano private/config/app.php
```

Update these settings for production:
```php
'app' => [
    'debug' => false,              // Disable debug mode
    'environment' => 'production', // Set to production
],
'logging' => [
    'level' => 'warning',          // Reduce logging verbosity
],
```

### 3. Database Configuration (if needed)
```php
'database' => [
    'default' => [
        'host' => 'localhost',
        'database' => 'your_production_db',
        'username' => 'your_db_user',
        'password' => 'your_secure_password',
    ],
],
```

### 4. Set Proper File Permissions
```bash
# Make sure permissions are correct
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chmod 600 private/config/app.php
chmod 755 private/logs private/cache
```

## 🔒 Security Checklist for Production

### 1. Remove or Secure Test Pages
```bash
# Option A: Remove test pages completely
rm -rf tests/

# Option B: Restrict access with .htaccess
echo "Order Deny,Allow
Deny from all
Allow from YOUR_IP_ADDRESS" > tests/.htaccess
```

### 2. Secure Configuration Files
```bash
# Make config files read-only
chmod 600 private/config/*.php
chown www-data:www-data private/config/*.php  # If using Apache
```

### 3. Set Up Log Rotation
```bash
# Create logrotate configuration
sudo nano /etc/logrotate.d/progenphp

# Add this content:
/var/www/html/progenphp/private/logs/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 0644 www-data www-data
}
```

### 4. SSL/TLS Certificate
```bash
# If using Let's Encrypt (Certbot)
sudo certbot --apache -d yourdomain.com
# or for Nginx
sudo certbot --nginx -d yourdomain.com
```

## 🔄 Automated Deployment Script

Create a deployment script for easy updates:

```bash
# Create deploy.sh
nano deploy.sh
```

Add this content:
```bash
#!/bin/bash
# ProgenPHP Deployment Script

echo "🚀 Starting ProgenPHP Deployment..."

# Pull latest changes
echo "📡 Pulling latest code..."
git pull origin main

# Set permissions
echo "🔒 Setting permissions..."
chmod 755 public private tests
chmod 644 public/* private/config/* private/utils/*
chmod 600 private/config/app.php
chmod 755 private/logs private/cache

# Clear cache (if implemented)
echo "🧹 Clearing cache..."
rm -f private/cache/*.tmp

# Test configuration
echo "🧪 Testing configuration..."
php -l public/index.php
if [ $? -eq 0 ]; then
    echo "✅ Syntax check passed"
else
    echo "❌ Syntax error found!"
    exit 1
fi

echo "✅ Deployment completed successfully!"
echo "🌐 Visit: https://yourdomain.com/progenphp/"
```

Make it executable:
```bash
chmod +x deploy.sh
```

## 🐳 Docker Deployment (Advanced)

If your host supports Docker:

```bash
# Create Dockerfile
nano Dockerfile
```

```dockerfile
FROM php:8.2-apache

# Enable required PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Copy application files
COPY . /var/www/html/progenphp/

# Set document root to public directory
ENV APACHE_DOCUMENT_ROOT /var/www/html/progenphp/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Enable Apache modules
RUN a2enmod rewrite

# Set permissions
RUN chown -R www-data:www-data /var/www/html/progenphp/private/logs /var/www/html/progenphp/private/cache
RUN chmod 755 /var/www/html/progenphp/private/logs /var/www/html/progenphp/private/cache

EXPOSE 80
```

Deploy with Docker:
```bash
docker build -t progenphp .
docker run -d -p 80:80 --name progenphp-app progenphp
```

## 🚨 Common Hosting Provider Examples

### cPanel/Shared Hosting
```bash
# Upload to public_html/progenphp/
# Set document root to public_html/progenphp/public/ in cPanel
# Or access via yourdomain.com/progenphp/
```

### DigitalOcean/VPS
```bash
ssh root@your-droplet-ip
cd /var/www/html
git clone https://github.com/yourusername/progenphp.git
# Follow Apache/Nginx config above
```

### AWS EC2
```bash
ssh -i your-key.pem ec2-user@your-instance
sudo su
cd /var/www/html
git clone https://github.com/yourusername/progenphp.git
# Configure Apache/Nginx as above
```

## 📊 Post-Deployment Testing

After deployment, test these URLs:
- `https://yourdomain.com/progenphp/` - Main environment info
- `https://yourdomain.com/progenphp/tests/access-test.php` - Access tests
- `https://yourdomain.com/progenphp/tests/security-test.php` - Security tests
- `https://yourdomain.com/progenphp/private/config/app.php` - Should return 403/404

## 📱 Monitoring and Maintenance

### Log Monitoring
```bash
# Monitor application logs
tail -f private/logs/app.log

# Monitor web server logs
tail -f /var/log/apache2/access.log  # Apache
tail -f /var/log/nginx/access.log    # Nginx
```

### Health Checks
Create a simple health check endpoint:
```php
// public/health.php
<?php
http_response_code(200);
echo json_encode([
    'status' => 'ok',
    'timestamp' => time(),
    'version' => '1.0.0'
]);
```

## 🔄 Update Workflow

For future updates:
```bash
# SSH into server
ssh username@your-server.com
cd /path/to/progenphp

# Pull updates
git pull origin main

# Run deployment script
./deploy.sh
```

This comprehensive guide should cover most deployment scenarios. Let me know which hosting provider you're using for more specific instructions!