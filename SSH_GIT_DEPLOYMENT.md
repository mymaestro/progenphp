# ProgenPHP Git Deployment via SSH on Hostinger

## 🚀 Simple SSH + Git Deployment

This is the cleanest way to deploy - just SSH into Hostinger and clone your repository directly.

## 📋 Prerequisites

1. **Hostinger Business Plan** (or higher) with SSH access
2. **SSH enabled** in hPanel → Advanced → SSH Access  
3. **Git repository** pushed to GitHub

## 🔧 Step-by-Step Deployment

### Step 1: Push Your Code to GitHub (if not done already)

```bash
# From your local machine in the progenphp directory
git init
git add .
git commit -m "ProgenPHP initial commit"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/progenphp.git
git push -u origin main
```

### Step 2: SSH into Hostinger

```bash
# Hostinger uses port 65002, not the standard 22
ssh -p 65002 YOUR_USERNAME@ssh.hostinger.com

# Example: ssh -p 65002 u123456789@ssh.hostinger.com
```

### Step 3: Navigate to Your Domain Directory

```bash
# For your main domain
cd domains/YOUR_DOMAIN.COM/public_html

# Or if it's an addon domain
cd domains/ADDON_DOMAIN.COM/public_html

# Or for primary domain (older accounts)
cd public_html
```

### Step 4: Clone Your Repository

```bash
# Clone your ProgenPHP repository
git clone https://github.com/YOUR_USERNAME/progenphp.git

# Navigate into the directory
cd progenphp
```

### Step 5: Set Proper Permissions

```bash
# Set directory permissions
chmod 755 public private tests

# Set file permissions
find . -type f -name "*.php" -exec chmod 644 {} \;
find . -type f -name "*.html" -exec chmod 644 {} \;
find . -type f -name "*.css" -exec chmod 644 {} \;
find . -type f -name "*.js" -exec chmod 644 {} \;

# Secure configuration files
chmod 600 private/config/app.php
chmod 600 private/config/environment.php

# Ensure logs and cache are writable
chmod 755 private/logs
chmod 755 private/cache
```

### Step 6: Configure for Production

```bash
# Edit the main configuration file
nano private/config/app.php
```

Change these lines:
```php
'app' => [
    'name' => 'ProgenPHP',
    'version' => '1.0.0',
    'debug' => false,              // Change from true to false
    'environment' => 'production', // Change from 'development'
],
```

Save with `Ctrl+X`, then `Y`, then `Enter`.

### Step 7: Test Your Installation

Visit your site:
- **Subdirectory**: `https://yourdomain.com/progenphp/`
- **Main domain**: `https://yourdomain.com/` (if configured)

## 🌐 Domain Configuration Options

### Option A: Keep as Subdirectory
- **Access via**: `https://yourdomain.com/progenphp/`
- **No changes needed** - works immediately

### Option B: Set up as Subdomain
1. In Hostinger hPanel → **Domains → Subdomains**
2. Create subdomain: `app` (or whatever you prefer)
3. Set document root to: `public_html/progenphp/public`
4. **Access via**: `https://app.yourdomain.com/`

### Option C: Use as Main Site
```bash
# Move files from progenphp/public/ to main directory
cd ~/domains/yourdomain.com/public_html
mv progenphp/public/* .
mv progenphp/private .
mv progenphp/tests .
mv progenphp/.htaccess .htaccess.progenphp

# Merge .htaccess files if you have existing one
cat .htaccess.progenphp >> .htaccess
rm .htaccess.progenphp

# Remove empty progenphp directory
rmdir progenphp/public
rmdir progenphp
```

## 🔒 Production Security Steps

### Remove Test Pages
```bash
# Delete the tests directory for production
rm -rf tests/

# Or restrict access instead
chmod 700 tests/
echo "deny from all" > tests/.htaccess
```

### Verify Private Folder Protection
```bash
# Check that .htaccess is in place
ls -la private/.htaccess

# If missing, create it
echo "Order Deny,Allow
Deny from all" > private/.htaccess
```

## 🔄 Future Updates

When you need to update your site:

```bash
# SSH back into Hostinger  
ssh -p 65002 YOUR_USERNAME@ssh.hostinger.com

# Navigate to your project
cd domains/YOUR_DOMAIN.COM/public_html/progenphp

# Pull latest changes
git pull origin main

# Set permissions again if needed
chmod 644 public/* private/config/* private/utils/*
```

## 🗄️ Database Setup (If Needed)

### In Hostinger hPanel:
1. Go to **Databases → MySQL Databases**
2. Create database: `YOUR_USERNAME_progenphp`  
3. Create user: `YOUR_USERNAME_dbuser`
4. Grant all privileges to the user

### Update Configuration:
```bash
nano private/config/app.php
```

Update the database section:
```php
'database' => [
    'default' => [
        'host' => 'localhost',
        'port' => 3306,
        'database' => 'YOUR_USERNAME_progenphp',
        'username' => 'YOUR_USERNAME_dbuser',
        'password' => 'YOUR_SECURE_PASSWORD',
        'charset' => 'utf8mb4',
    ],
],
```

## 📊 Verification Steps

After deployment, check these:

1. **Main page loads**: Visit your URL
2. **Environment info shows**: Server details display correctly  
3. **Private folder protected**: Try accessing `/private/config/app.php` - should get 403 error
4. **SSL working**: Ensure HTTPS is active (enable in hPanel → Security → SSL)

## 🚨 Common SSH Commands for Hostinger

```bash
# Check current directory
pwd

# List files with details
ls -la

# Check PHP version
php -v

# Check disk usage
du -sh *

# View error logs
tail -f ~/logs/error.log

# Edit files
nano filename.php

# Change permissions
chmod 755 foldername
chmod 644 filename.php
```

## 🎯 Quick Reference

**SSH Connection**:
```bash
ssh -p 65002 YOUR_USERNAME@ssh.hostinger.com
```

**Navigation**:
```bash
cd domains/YOUR_DOMAIN.COM/public_html
```

**Git Commands**:
```bash
git clone https://github.com/YOUR_USERNAME/progenphp.git
cd progenphp
git pull origin main  # For updates
```

**Permissions**:
```bash
chmod 755 public private tests
chmod 644 public/* private/config/* private/utils/*
chmod 600 private/config/app.php
```

That's it! This approach gives you full control and is the most reliable way to deploy on Hostinger. No need for complicated deployment scripts - just SSH, clone, and configure.

What's your GitHub repository URL? I can help you with the exact commands once you have it set up.