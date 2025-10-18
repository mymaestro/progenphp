# .htaccess Files with Symlink Deployment Method

## 🔍 Analysis of Current .htaccess Files

With the symlink deployment method, the behavior of .htaccess files changes significantly:

### **Current .htaccess Files:**

1. **Root .htaccess** (`/.htaccess`) - **WILL CAUSE ISSUES**
2. **Private .htaccess** (`/private/.htaccess`) - **NOT NEEDED** (but harmless)
3. **Public .htaccess** (`/public/.htaccess`) - **NEEDED** (if exists)

## ⚠️ **Issues with Symlink Method**

### **Problem with Root .htaccess:**
The current root .htaccess has this redirect rule:
```apache
RewriteCond %{REQUEST_URI} !^/public/
RewriteRule ^(.*)$ public/$1 [L]
```

**This will break the symlink setup** because:
- Symlink: `public_html/progenphp -> ../progenphp/public/`  
- Root .htaccess tries to redirect to `public/` subfolder
- Creates infinite redirect loops or 404 errors

## 🛠️ **Solutions**

### **Option 1: Remove Problematic .htaccess (Recommended)**

Create a symlink-optimized version without the redirect rules:

```apache
# ProgenPHP Symlink-Optimized .htaccess
# This version works when public/ is the actual document root via symlink

# Security: Block access to sensitive files
<FilesMatch "(^#.*#|\.(bak|config|dist|fla|inc|ini|log|psd|sh|sql|sw[op])|~)$">
    Order allow,deny
    Deny from all
    Satisfy All
</FilesMatch>

# Block access to version control directories
RedirectMatch 404 /\.git
RedirectMatch 404 /\.svn
RedirectMatch 404 /\.hg

# Security headers (if mod_headers is available)
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options SAMEORIGIN
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>

# Disable server signature
ServerSignature Off

# Hide PHP version
<IfModule mod_headers.c>
    Header unset X-Powered-By
</IfModule>

# Enable GZIP compression (if mod_deflate is available)
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

# Set cache headers for static assets (if mod_expires is available)
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/gif "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/pdf "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType application/x-javascript "access plus 1 month"
    ExpiresByType application/x-shockwave-flash "access plus 1 month"
    ExpiresByType image/x-icon "access plus 1 year"
    ExpiresDefault "access plus 2 days"
</IfModule>
```

### **Option 2: Conditional .htaccess**

Create a smart .htaccess that detects if it's running via symlink:

```apache
# ProgenPHP Smart .htaccess - Works with both methods

RewriteEngine On

# Check if we're in a symlinked environment
RewriteCond %{DOCUMENT_ROOT} -d
RewriteCond %{DOCUMENT_ROOT}/index.php -f

# Only redirect to public/ if we're NOT in a symlinked setup
RewriteCond %{REQUEST_URI} !^/public/
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{DOCUMENT_ROOT}/public/index.php -f
RewriteRule ^(.*)$ public/$1 [L]

# Rest of security rules...
```

## 🚀 **Recommended Deployment Steps for Symlink Method**

### **Step 1: Create Symlink-Optimized .htaccess**

Before deployment, replace the root .htaccess:

```bash
# In your local progenphp directory
cp .htaccess .htaccess.backup

# Create new symlink-friendly version
cat > public/.htaccess << 'EOF'
# ProgenPHP Public Directory .htaccess
# This file is in the actual document root via symlink

# Security: Block access to sensitive files
<FilesMatch "(^#.*#|\.(bak|config|dist|fla|inc|ini|log|psd|sh|sql|sw[op])|~)$">
    Order allow,deny
    Deny from all
    Satisfy All
</FilesMatch>

# Security headers
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options SAMEORIGIN
    Header always set X-XSS-Protection "1; mode=block"
</IfModule>

# Enable compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain text/html text/css application/javascript
</IfModule>

# PHP settings for shared hosting
php_value memory_limit 256M
php_value max_execution_time 300
EOF
```

### **Step 2: Remove Root .htaccess (for symlink deployment)**

```bash
# Remove or rename the problematic root .htaccess
mv .htaccess .htaccess.non-symlink

# Or create a minimal one for the app directory
cat > .htaccess << 'EOF'
# Minimal .htaccess for symlinked deployment
# The main .htaccess is in public/ directory

# Just basic security for the app directory
<Files "*">
    Order Deny,Allow
    Deny from all
</Files>
EOF
```

### **Step 3: Keep Private .htaccess (Optional)**

The private/.htaccess doesn't hurt with symlinks, but it's redundant since private/ is outside web root:

```bash
# You can remove it since private/ is not web-accessible anyway
rm private/.htaccess

# Or keep it as defense-in-depth
# (won't affect anything but provides extra protection if symlink breaks)
```

## 📋 **Updated Deployment Process**

```bash
# 1. Optimize .htaccess files locally
mv .htaccess .htaccess.backup
mv public/.htaccess.new public/.htaccess  # If you created new one
git add .
git commit -m "Optimize .htaccess for symlink deployment"
git push origin main

# 2. SSH and deploy
ssh -p 65002 username@ssh.hostinger.com
cd domains/my_domain.com
git clone https://github.com/mymaestro/progenphp.git
ln -sf ../progenphp/public public_html/progenphp

# 3. Test - should work without redirect issues
curl -I https://my_domain.com/progenphp/
```

## 🔍 **Testing Your Setup**

After deployment, verify:

```bash
# Check symlink
ls -la public_html/progenphp
# Should show: progenphp -> ../progenphp/public

# Test main page
curl -I https://my_domain.com/progenphp/
# Should return 200 OK (not redirect loops)

# Test security
curl -I https://my_domain.com/private/
# Should return 404 (directory doesn't exist in web root)
```

## 🎯 **Summary**

**Yes, the current root .htaccess WILL cause issues** with the symlink method because:

1. **Redirect rules conflict** with symlink structure
2. **Creates infinite loops** or 404 errors
3. **Private folder protection is redundant** (already outside web root)

**Solution**: Use the optimized .htaccess files I've provided above, or simply move the current root .htaccess out of the way before deployment.

Would you like me to create the optimized versions for you to commit before deployment?