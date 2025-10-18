#!/bin/bash

# ProgenPHP Hostinger Deployment Script
# Usage: ./hostinger-deploy.sh <hostinger-username> <domain.com> [subdomain-name]

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Hostinger configuration
HOSTINGER_HOST="ssh.hostinger.com"
HOSTINGER_PORT="65002"
HOSTINGER_USER=$1
HOSTINGER_DOMAIN=$2
SUBDOMAIN=$3

echo -e "${BLUE}🚀 ProgenPHP Hostinger Deployment${NC}"
echo "===================================="

if [ -z "$HOSTINGER_USER" ] || [ -z "$HOSTINGER_DOMAIN" ]; then
    echo -e "${RED}❌ Usage: $0 <hostinger-username> <domain.com> [subdomain-name]${NC}"
    echo -e "${YELLOW}Examples:${NC}"
    echo "  $0 u123456789 mysite.com"
    echo "  $0 u123456789 mysite.com app"
    exit 1
fi

# Determine deployment path
if [ -n "$SUBDOMAIN" ]; then
    DEPLOY_PATH="domains/$HOSTINGER_DOMAIN/public_html"
    ACCESS_URL="https://$SUBDOMAIN.$HOSTINGER_DOMAIN/"
    echo -e "${BLUE}📋 Deploying as subdomain: $SUBDOMAIN.$HOSTINGER_DOMAIN${NC}"
else
    DEPLOY_PATH="domains/$HOSTINGER_DOMAIN/public_html"
    ACCESS_URL="https://$HOSTINGER_DOMAIN/progenphp/"
    echo -e "${BLUE}📋 Deploying as subdirectory: $HOSTINGER_DOMAIN/progenphp/${NC}"
fi

echo "   Username: $HOSTINGER_USER"
echo "   Domain: $HOSTINGER_DOMAIN"
echo "   Path: $DEPLOY_PATH"
echo ""

# Step 1: Test SSH connection
echo -e "${YELLOW}🔗 Testing Hostinger SSH connection...${NC}"
if ssh -p $HOSTINGER_PORT -o BatchMode=yes -o ConnectTimeout=10 $HOSTINGER_USER@$HOSTINGER_HOST 'echo "Connected to Hostinger"' >/dev/null 2>&1; then
    echo -e "${GREEN}✅ SSH connection successful${NC}"
else
    echo -e "${RED}❌ SSH connection failed.${NC}"
    echo -e "${YELLOW}💡 Make sure:${NC}"
    echo "   - SSH access is enabled in hPanel"
    echo "   - You're using the correct username"
    echo "   - Your hosting plan supports SSH (Premium/Business)"
    exit 1
fi

# Step 2: Create deployment package
echo -e "${YELLOW}📦 Creating Hostinger deployment package...${NC}"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
PACKAGE_NAME="progenphp-hostinger-$TIMESTAMP.zip"

zip -r $PACKAGE_NAME . \
    -x ".git/*" \
    -x ".venv/*" \
    -x "*.tmp" \
    -x "router.php" \
    -x "hostinger-*" \
    -x "quick-deploy.sh" \
    -x "*.zip" \
    >/dev/null 2>&1

echo -e "${GREEN}✅ Package created: $PACKAGE_NAME${NC}"

# Step 3: Upload package
echo -e "${YELLOW}📤 Uploading to Hostinger...${NC}"
scp -P $HOSTINGER_PORT $PACKAGE_NAME $HOSTINGER_USER@$HOSTINGER_HOST:$DEPLOY_PATH/

echo -e "${GREEN}✅ Upload completed${NC}"

# Step 4: Extract and configure on server
echo -e "${YELLOW}📂 Configuring on Hostinger server...${NC}"

ssh -p $HOSTINGER_PORT $HOSTINGER_USER@$HOSTINGER_HOST << EOF
cd $DEPLOY_PATH

# Create progenphp directory if needed
if [ ! -d "progenphp" ]; then
    mkdir -p progenphp
fi

# Extract files
unzip -q $PACKAGE_NAME -d progenphp/
rm $PACKAGE_NAME

# Navigate to progenphp directory
cd progenphp

# Set Hostinger-friendly permissions
echo "Setting permissions..."
chmod 755 public private tests
find . -type f -name "*.php" -exec chmod 644 {} \; 2>/dev/null || true
find . -type f -name "*.html" -exec chmod 644 {} \; 2>/dev/null || true
chmod 700 private/config/app.php
chmod 755 private/logs private/cache

# Create Hostinger-specific configuration
echo "Creating production configuration..."
sed -i "s/'debug' => true/'debug' => false/g" private/config/app.php 2>/dev/null || true
sed -i "s/'environment' => 'development'/'environment' => 'production'/g" private/config/app.php 2>/dev/null || true

# Create .htaccess for additional security
echo "Options -Indexes
<Files \"*.log\">
    Order Allow,Deny
    Deny from all
</Files>" > private/.htaccess

# Create info file for reference
cat > HOSTINGER_INFO.txt << 'HOSTINGER_EOF'
ProgenPHP on Hostinger - Deployment Info
========================================

Deployment Date: $(date)
Access URL: $ACCESS_URL
Server Path: $DEPLOY_PATH/progenphp/

Next Steps:
1. Visit your site to test: $ACCESS_URL
2. Configure SSL in hPanel (Security → SSL)
3. Set up subdomain if needed (Domains → Subdomains)
4. Remove tests directory for production: rm -rf tests/
5. Configure database if needed (Databases → MySQL)

Hostinger-Specific Notes:
- PHP version can be changed in hPanel → Advanced → PHP Configuration  
- Error logs are in ~/logs/ directory
- File Manager available in hPanel for easy file access
- Free SSL available in Security section

Security Status:
- Debug mode: DISABLED
- Environment: PRODUCTION
- Private folder: PROTECTED
- Test pages: ENABLED (remove for production)
HOSTINGER_EOF

echo "✅ Hostinger configuration completed!"
EOF

# Step 5: Clean up local package
rm $PACKAGE_NAME

# Step 6: Final instructions
echo ""
echo -e "${GREEN}🎉 Hostinger deployment completed successfully!${NC}"
echo "=================================================="
echo ""
echo -e "${BLUE}🌐 Your ProgenPHP site is now available at:${NC}"
echo -e "${GREEN}   $ACCESS_URL${NC}"
echo ""
echo -e "${BLUE}📋 Next steps in Hostinger hPanel:${NC}"

if [ -n "$SUBDOMAIN" ]; then
    echo -e "${YELLOW}   1. Create subdomain:${NC}"
    echo "      - Go to Domains → Subdomains"
    echo "      - Add: $SUBDOMAIN.$HOSTINGER_DOMAIN"
    echo "      - Document root: public_html/progenphp/public"
    echo ""
fi

echo -e "${YELLOW}   2. Enable SSL Certificate:${NC}"
echo "      - Go to Security → SSL"
echo "      - Enable 'Free SSL Certificate'"
echo ""
echo -e "${YELLOW}   3. Configure PHP (if needed):${NC}"
echo "      - Go to Advanced → PHP Configuration"
echo "      - Ensure PHP 8.1+ is selected"
echo ""
echo -e "${YELLOW}   4. Production security:${NC}"
echo "      - SSH back in and run: rm -rf progenphp/tests/"
echo "      - Or restrict access via .htaccess"
echo ""
echo -e "${BLUE}📊 To monitor your site:${NC}"
echo "   - Check error logs in hPanel → Advanced → Error Logs"
echo "   - Use File Manager for easy file access"
echo "   - Monitor resource usage in hPanel dashboard"
echo ""
echo -e "${BLUE}🔧 SSH back in anytime with:${NC}"
echo -e "${GREEN}   ssh -p $HOSTINGER_PORT $HOSTINGER_USER@$HOSTINGER_HOST${NC}"
echo ""
echo -e "${BLUE}Happy hosting with Hostinger! 🚀${NC}"