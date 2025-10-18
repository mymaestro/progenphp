# ProgenPHP Hosting Configuration Examples

## Quick Setup Commands

### For Most VPS/Dedicated Servers
```bash
# 1. Upload and deploy
./quick-deploy.sh user@yourserver.com /var/www/html/progenphp

# 2. SSH into server and configure
ssh user@yourserver.com
cd /var/www/html/progenphp
nano private/config/app.php  # Set debug=false, environment=production
```

### Apache Virtual Host Example
```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /var/www/html/progenphp/public
    
    <Directory /var/www/html/progenphp/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    # Redirect HTTP to HTTPS (after SSL setup)
    # Redirect permanent / https://yourdomain.com/
</VirtualHost>

<VirtualHost *:443>
    ServerName yourdomain.com
    DocumentRoot /var/www/html/progenphp/public
    
    SSLEngine on
    SSLCertificateFile /path/to/your/certificate.crt
    SSLCertificateKeyFile /path/to/your/private.key
    
    <Directory /var/www/html/progenphp/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Nginx Site Example
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl;
    server_name yourdomain.com;
    root /var/www/html/progenphp/public;
    index index.php;

    ssl_certificate /path/to/your/certificate.crt;
    ssl_certificate_key /path/to/your/private.key;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location /private {
        deny all;
    }
}
```

## Common Hosting Providers

### DigitalOcean Droplet
```bash
# Create and setup droplet
doctl compute droplet create progenphp --size s-1vcpu-1gb --image ubuntu-22-04-x64 --region nyc1

# SSH and setup
ssh root@your-droplet-ip
apt update && apt install apache2 php8.2 php8.2-mysql git -y
cd /var/www/html
git clone https://github.com/yourusername/progenphp.git
# Configure Apache virtual host (see above)
```

### AWS EC2
```bash
# Launch EC2 instance with Ubuntu/Amazon Linux
ssh -i your-key.pem ec2-user@your-instance-ip
sudo yum update -y  # Amazon Linux
sudo yum install httpd php git -y
sudo systemctl start httpd
sudo systemctl enable httpd
cd /var/www/html
sudo git clone https://github.com/yourusername/progenphp.git
sudo chown -R apache:apache progenphp/
```

### Linode
```bash
# Similar to DigitalOcean
ssh root@your-linode-ip
apt update && apt install apache2 php8.2 git -y
cd /var/www/html
git clone https://github.com/yourusername/progenphp.git
```

### cPanel Shared Hosting
```bash
# Upload via File Manager or:
# 1. SSH access (if available)
ssh username@yourserver.com
cd public_html
git clone https://github.com/yourusername/progenphp.git

# 2. Or use cPanel File Manager to upload zip
# 3. In cPanel, set subdomain/addon domain document root to:
#    public_html/progenphp/public/
```

## Production Checklist Script

Here's what to run after deployment:

```bash
#!/bin/bash
# production-setup.sh

echo "🔧 ProgenPHP Production Setup"
echo "=============================="

# Update configuration
sed -i "s/'debug' => true/'debug' => false/" private/config/app.php
sed -i "s/'environment' => 'development'/'environment' => 'production'/" private/config/app.php

# Secure test directory
chmod 700 tests/
echo "Order Deny,Allow
Deny from all
# Allow from YOUR.IP.ADDRESS.HERE" > tests/.htaccess

# Set strict permissions
chmod 600 private/config/*.php
chmod 755 private/logs private/cache
chown -R www-data:www-data private/logs private/cache  # Adjust user as needed

# Create basic logrotate
echo "/var/www/html/progenphp/private/logs/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 0644 www-data www-data
}" > /tmp/progenphp-logrotate

echo "✅ Production configuration applied"
echo "📋 Next steps:"
echo "   1. Copy /tmp/progenphp-logrotate to /etc/logrotate.d/"
echo "   2. Set up SSL certificate"
echo "   3. Configure database if needed"
echo "   4. Test your site!"
```

## SSL Setup (Let's Encrypt)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-apache  # For Apache
sudo apt install certbot python3-certbot-nginx   # For Nginx

# Get certificate
sudo certbot --apache -d yourdomain.com         # Apache
sudo certbot --nginx -d yourdomain.com          # Nginx

# Test auto-renewal
sudo certbot renew --dry-run
```

## Database Setup (if needed)

```bash
# MySQL/MariaDB
sudo apt install mysql-server php8.2-mysql
sudo mysql_secure_installation
sudo mysql -e "CREATE DATABASE progenphp; CREATE USER 'progenphp'@'localhost' IDENTIFIED BY 'secure_password'; GRANT ALL ON progenphp.* TO 'progenphp'@'localhost';"

# Then update private/config/app.php with database credentials
```

## Monitoring Commands

```bash
# Check logs
tail -f private/logs/app.log
tail -f /var/log/apache2/error.log

# Check permissions
ls -la private/
ls -la private/config/

# Test PHP
php -l public/index.php

# Check web server status
sudo systemctl status apache2
sudo systemctl status nginx
```