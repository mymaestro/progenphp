# ProgenPHP Hostinger Quick Start

## 🚀 Quick Deployment to Hostinger

### Prerequisites
1. **Hostinger Account**: Business plan or higher (for SSH access)
2. **SSH Enabled**: Go to hPanel → Advanced → SSH Access and enable it
3. **Domain Configured**: Your domain should be set up in Hostinger

### SSH Connection Details for Hostinger
- **Host**: `ssh.hostinger.com` 
- **Port**: `65002` (NOT the standard 22)
- **Username**: Your hPanel username (usually starts with 'u')
- **Password**: Your hPanel password

## 🎯 One-Command Deployment

```bash
# Deploy to subdirectory (yourdomain.com/progenphp/)
./hostinger-deploy.sh u123456789 yourdomain.com

# Deploy for subdomain setup (app.yourdomain.com)
./hostinger-deploy.sh u123456789 yourdomain.com app
```

Replace:
- `u123456789` with your actual Hostinger username
- `yourdomain.com` with your actual domain

## 📱 Alternative: Manual Upload via hPanel

If you don't have SSH access or prefer using the web interface:

### Step 1: Create Upload Package
```bash
cd /home/gill/public_html/progenphp
zip -r progenphp-manual.zip . -x ".git/*" ".venv/*" "*.tmp" "router.php"
```

### Step 2: Upload via File Manager
1. Login to Hostinger hPanel
2. Go to **Files → File Manager**
3. Navigate to `domains/yourdomain.com/public_html`
4. Click **Upload** → Select `progenphp-manual.zip`
5. Right-click the zip → **Extract**
6. Rename extracted folder to `progenphp` if needed

### Step 3: Set Permissions (via File Manager)
1. Right-click `progenphp` folder → **Permissions** → `755`
2. Right-click `progenphp/private` → **Permissions** → `700`
3. Inside `progenphp/private/config/` → Select all files → **Permissions** → `600`

## 🌐 Domain Configuration Options

### Option A: Subdirectory Access (Easiest)
- **URL**: `https://yourdomain.com/progenphp/`
- **Setup**: No additional configuration needed
- **Best for**: Testing, multiple projects

### Option B: Subdomain (Recommended)
1. In hPanel → **Domains → Subdomains**
2. Create: `app.yourdomain.com`
3. Document Root: `public_html/progenphp/public`
4. **URL**: `https://app.yourdomain.com/`

### Option C: Main Domain
1. Move all files from `progenphp/public/` to `public_html/`
2. Move `progenphp/private/` to `public_html/private/`
3. Update paths in configuration files
4. **URL**: `https://yourdomain.com/`

## 🔒 Production Security Setup

### 1. Edit Configuration
Via File Manager or SSH:
```bash
# Navigate to: progenphp/private/config/app.php
# Change:
'debug' => false,
'environment' => 'production',
```

### 2. Remove Test Pages
```bash
# Via SSH:
ssh -p 65002 u123456789@ssh.hostinger.com
cd domains/yourdomain.com/public_html/progenphp
rm -rf tests/

# Or via File Manager:
# Right-click tests folder → Delete
```

### 3. Enable SSL (Free on Hostinger)
1. hPanel → **Security → SSL**
2. Enable **Free SSL Certificate**
3. Wait 15-30 minutes for activation
4. Enable **Force HTTPS**

## 🗄️ Database Setup (If Needed)

### Create Database in hPanel
1. **Databases → MySQL Databases**
2. Create database: `u123456789_progenphp`
3. Create user: `u123456789_progenuser`
4. Add user to database with all privileges

### Update Configuration
Edit `progenphp/private/config/app.php`:
```php
'database' => [
    'default' => [
        'host' => 'localhost',
        'database' => 'u123456789_progenphp',
        'username' => 'u123456789_progenuser',
        'password' => 'your_secure_password',
    ],
],
```

## 📊 Testing Your Deployment

Visit these URLs to verify everything works:
- **Main page**: `https://yourdomain.com/progenphp/`
- **Environment info**: Shows Hostinger server details
- **Tests** (before removal): `https://yourdomain.com/progenphp/tests/`

## 🚨 Common Hostinger Issues & Solutions

### Issue: SSH Connection Failed
- **Solution**: Ensure SSH is enabled in hPanel → Advanced → SSH Access
- **Note**: Only available on Premium, Business, and Cloud plans

### Issue: Permission Errors
```bash
# Fix via SSH:
chmod -R 755 progenphp/
chmod 600 progenphp/private/config/app.php
```

### Issue: PHP Errors
- **Check**: hPanel → Advanced → Error Logs
- **Change PHP version**: hPanel → Advanced → PHP Configuration

### Issue: File Upload Size
Add to `.htaccess`:
```apache
php_value upload_max_filesize 10M
php_value post_max_size 10M
```

## 📈 Performance Tips for Hostinger

1. **Enable caching** in your application
2. **Optimize images** before upload
3. **Use CDN** (Hostinger offers Cloudflare integration)
4. **Monitor resources** in hPanel dashboard
5. **Regular backups** via hPanel → Backups

## 🎯 Final Checklist

- [ ] Files uploaded to Hostinger
- [ ] Permissions set correctly
- [ ] SSL certificate enabled
- [ ] Test pages removed (production)
- [ ] Configuration set to production mode
- [ ] Database configured (if needed)
- [ ] Domain/subdomain configured
- [ ] Site tested and working

## 📞 Hostinger Support

If you encounter issues:
1. **Knowledge Base**: Hostinger's extensive documentation
2. **Live Chat**: Available 24/7 for most plans
3. **Ticket System**: For technical issues

Your ProgenPHP site should now be running smoothly on Hostinger! 🎉