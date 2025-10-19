# ProgenPHP Clean Root Deployment Guide

## 🎯 **Final Clean Deployment Structure**

This is the cleanest possible setup - your entire domain directory IS the git repository.

### **Final Directory Structure:**
```
~/domains/your-domain.com/          # This IS the git repo
├── .git/                           # Git repository files
├── public/                         # Web-accessible files
│   ├── index.php                   # Main entry point
│   └── .htaccess                   # Web security config
├── private/                        # Protected application files
│   ├── config/                     # Configuration files
│   ├── utils/                      # Utility functions
│   ├── logs/                       # Application logs
│   └── cache/                      # Cache files
├── tests/                          # Test pages (accessible at /tests/)
├── README.md                       # Documentation
└── public_html -> public/          # Symlink (entire web root)
```

## 🚀 **Deployment Commands**

### **Step 1: SSH into Hostinger**
```bash
ssh -p 65002 YOUR_USERNAME@ssh.hostinger.com
```

### **Step 2: Navigate and Backup**
```bash
# Navigate to domains directory
cd ~/domains/

# Backup existing domain folder (if it exists)
mv your-domain.com your-domain.com.backup
```

### **Step 3: Clone Repository as Domain Directory**
```bash
# Clone your repo directly as the domain folder
git clone https://github.com/mymaestro/progenphp.git your-domain.com

# Navigate into the new domain directory
cd your-domain.com
```

### **Step 4: Create Public HTML Symlink**
```bash
# Create symlink from public_html to public folder
ln -sf public public_html

# Verify the symlink
ls -la public_html
# Should show: public_html -> public
```

### **Step 5: Set Permissions**
```bash
# Set directory permissions
chmod 755 public private tests

# Set file permissions
find public -type f -name "*.php" -exec chmod 644 {} \;
find private -type f -name "*.php" -exec chmod 644 {} \;

# Secure configuration files
chmod 600 private/config/app.php
chmod 600 private/config/environment.php

# Ensure writable directories
chmod 755 private/logs private/cache
```

### **Step 6: Configure for Production**
```bash
# Edit main configuration
nano private/config/app.php

# Change these lines:
# 'debug' => false,
# 'environment' => 'production',
```

## 🌐 **Access URLs**

With this setup, your site will be accessible at:
- **Main site**: `https://your-domain.com/`
- **Environment info**: `https://your-domain.com/` (index page)
- **Access tests**: `https://your-domain.com/tests/access-test.php`
- **Security tests**: `https://your-domain.com/tests/security-test.php`

## 🔄 **Future Updates**

Updating is incredibly simple:

```bash
# SSH into your server
ssh -p 65002 YOUR_USERNAME@ssh.hostinger.com

# Navigate to domain directory (which IS the git repo)
cd ~/domains/your-domain.com

# Pull latest changes
git pull origin main

# Set permissions if needed
chmod 644 public/* private/config/*
```

## 🔒 **Security Benefits**

1. **Maximum Security**: Private files completely outside web root
2. **Clean URLs**: No subdirectories in URLs
3. **Simple Management**: Everything in one git repository
4. **Easy Updates**: Single `git pull` command
5. **Professional Structure**: Industry standard approach

## ✅ **Verification Steps**

After deployment, verify everything works:

```bash
# Check symlink is correct
ls -la public_html
# Should show: public_html -> public

# Check directory structure
ls -la
# Should see: public/, private/, tests/, .git/, etc.

# Test web access
curl -I https://your-domain.com/
# Should return 200 OK

# Test security
curl -I https://your-domain.com/private/
# Should return 404 (not accessible)
```

## 🎯 **Complete Deployment Script**

Here's the complete deployment in one script:

```bash
#!/bin/bash
# Complete ProgenPHP Root Deployment

DOMAIN="your-domain.com"
USERNAME="your-hostinger-username"

echo "🚀 Deploying ProgenPHP as root domain..."

# SSH and deploy
ssh -p 65002 $USERNAME@ssh.hostinger.com << EOF
cd ~/domains/
mv $DOMAIN $DOMAIN.backup 2>/dev/null || true
git clone https://github.com/mymaestro/progenphp.git $DOMAIN
cd $DOMAIN
ln -sf public public_html
chmod 755 public private tests
find public private -type f -name "*.php" -exec chmod 644 {} \;
chmod 600 private/config/*.php
chmod 755 private/logs private/cache
echo "✅ Deployment completed!"
echo "🌐 Visit: https://$DOMAIN/"
EOF
```

## 🎉 **You're Done!**

This is the cleanest, most professional deployment structure possible. Your entire website is now managed as a single git repository with maximum security and simplicity.

**Next Steps:**
1. Run the deployment commands above
2. Visit your domain to see the environment info
3. Test the access and security test pages
4. Remove test pages for production (optional)
5. Enable SSL certificate in Hostinger hPanel

Your ProgenPHP application is now production-ready! 🚀