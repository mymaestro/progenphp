# ProgenPHP Secure Deployment with Symlink on Hostinger

## 🔒 Secure Deployment Method: Clone Outside Web Root

This approach clones your repository outside the web-accessible directory and creates a symbolic link to only expose the public folder. This is more secure as it keeps your private files completely outside the web root.

## 📂 Directory Structure on Hostinger

```
domains/
├── my_domain.com/
│   ├── public_html/          # Web-accessible directory
│   │   └── progenphp -> ../progenphp/public/  # Symlink to public folder
│   └── progenphp/            # Full application (outside web root)
│       ├── public/           # Only this gets linked
│       ├── private/          # Completely inaccessible via web
│       ├── tests/
│       └── ...
```

## 🚀 Step-by-Step Deployment

### Step 1: SSH into Hostinger

```bash
ssh -p 65002 YOUR_USERNAME@ssh.hostinger.com
```

### Step 2: Navigate to Domain Directory (NOT public_html)

```bash
# Navigate to your domain folder (one level up from public_html)
cd domains/my_domain.com

# Check current structure
ls -la
# You should see: public_html/
```

### Step 3: Clone Repository Outside Web Root

```bash
# Clone your repository here (alongside public_html, not inside it)
git clone https://github.com/mymaestro/progenphp.git

# Verify the structure
ls -la
# You should now see: public_html/  progenphp/
```

### Step 4: Create Symbolic Link

```bash
# Remove any existing progenphp folder in public_html (if exists)
rm -rf public_html/progenphp

# Create symbolic link from public_html to your app's public folder
ln -sf ../progenphp/public public_html/progenphp

# Verify the symlink was created
ls -la public_html/
# You should see: progenphp -> ../progenphp/public
```

### Step 5: Set Permissions

```bash
# Navigate to your app directory
cd progenphp

# Set permissions for directories
chmod 755 public private tests

# Set permissions for files
find . -type f -name "*.php" -exec chmod 644 {} \;
find . -type f -name "*.html" -exec chmod 644 {} \;

# Secure configuration files
chmod 600 private/config/*.php

# Ensure logs and cache are writable
chmod 755 private/logs private/cache
```

### Step 6: Configure for Production

```bash
# Edit configuration
nano private/config/app.php
```

Update these settings:
```php
'app' => [
    'debug' => false,              // Change to false
    'environment' => 'production', // Change to production
],
```

### Step 7: Test Your Deployment

Visit: `https://my_domain.com/progenphp/`

## 🌟 Alternative: Direct Domain Root

If you want the app to be your main site (accessible at `https://my_domain.com/`):

```bash
# Remove the default index file
rm -f public_html/index.html public_html/index.php

# Create symlink to app's public folder as the web root
# First, backup any existing files
mv public_html public_html_backup

# Create new symlink for the entire public_html
ln -sf progenphp/public public_html

# Or create individual symlinks for each file/folder in public/
# This method gives you more control:
mkdir -p public_html
cd progenphp/public
for item in *; do
    ln -sf "../../progenphp/public/$item" "../public_html/$item"
done
```

## 🔄 Future Updates

Updating is super easy with this setup:

```bash
# SSH into Hostinger
ssh -p 65002 YOUR_USERNAME@ssh.hostinger.com

# Navigate to app directory
cd domains/my_domain.com/progenphp

# Pull latest changes
git pull origin main

# Permissions might need updating after pull
chmod 644 public/* private/config/* private/utils/*
```

## 🔒 Security Benefits

This approach provides several security advantages:

1. **Private files completely outside web root**: No way to access them via HTTP
2. **No .htaccess dependency**: Security doesn't rely on web server configuration
3. **Cleaner separation**: Clear distinction between public and private code
4. **Easy updates**: Git operations don't affect web-accessible files directly

## 🛠️ Troubleshooting

### If Symlink Doesn't Work

Some shared hosting providers don't allow symlinks. If you get permission errors:

```bash
# Check if symlinks are supported
ln -sf test.txt test_link.txt
ls -la test_link.txt

# If symlinks don't work, use rsync to sync public folder
rsync -av --delete progenphp/public/ public_html/progenphp/
```

### Alternative: Copy Method (if symlinks not supported)

```bash
# Create a sync script for updates
cat > sync_public.sh << 'EOF'
#!/bin/bash
# Sync public folder to web root
rsync -av --delete progenphp/public/ public_html/progenphp/
echo "Public folder synced successfully"
EOF

chmod +x sync_public.sh

# Run after each git pull
./sync_public.sh
```

## 📊 Verification Commands

```bash
# Check symlink is working
ls -la public_html/progenphp
# Should show: public_html/progenphp -> ../progenphp/public

# Check private folder is not accessible via web root
find public_html -name "private" -type d
# Should return nothing

# Test web access
curl -I https://my_domain.com/progenphp/
# Should return 200 OK

# Test private folder is inaccessible
curl -I https://my_domain.com/progenphp/../private/
# Should return 404 or 403
```

## 🎯 Quick Commands Summary

```bash
# SSH connection
ssh -p 65002 YOUR_USERNAME@ssh.hostinger.com

# Navigate and clone
cd domains/my_domain.com
git clone https://github.com/mymaestro/progenphp.git

# Create symlink
ln -sf ../progenphp/public public_html/progenphp

# Set permissions
cd progenphp
chmod 755 public private tests
find . -type f -name "*.php" -exec chmod 644 {} \;
chmod 600 private/config/*.php
chmod 755 private/logs private/cache

# Configure for production
nano private/config/app.php
```

This method is much more secure and professional. Your private files will be completely outside the web root, making them impossible to access via HTTP requests, regardless of web server configuration!

Would you like me to walk you through any specific part of this process?