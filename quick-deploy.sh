#!/bin/bash

# ProgenPHP Quick Deployment Script
# Usage: ./quick-deploy.sh [server-user@server-ip] [target-directory]

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
SERVER=$1
TARGET_DIR=${2:-"/var/www/html/progenphp"}
LOCAL_DIR="."

echo -e "${BLUE}🚀 ProgenPHP Deployment Script${NC}"
echo "=================================="

if [ -z "$SERVER" ]; then
    echo -e "${RED}❌ Usage: $0 user@server.com [target-directory]${NC}"
    echo -e "${YELLOW}Example: $0 root@myserver.com /var/www/html/progenphp${NC}"
    exit 1
fi

echo -e "${BLUE}📋 Deployment Configuration:${NC}"
echo "   Server: $SERVER"
echo "   Target: $TARGET_DIR"
echo "   Local:  $LOCAL_DIR"
echo ""

# Step 1: Test SSH connection
echo -e "${YELLOW}🔗 Testing SSH connection...${NC}"
if ssh -o BatchMode=yes -o ConnectTimeout=5 $SERVER 'echo "SSH connection successful"' >/dev/null 2>&1; then
    echo -e "${GREEN}✅ SSH connection successful${NC}"
else
    echo -e "${RED}❌ SSH connection failed. Please check your credentials and server address.${NC}"
    exit 1
fi

# Step 2: Create target directory
echo -e "${YELLOW}📁 Creating target directory...${NC}"
ssh $SERVER "mkdir -p $TARGET_DIR"
echo -e "${GREEN}✅ Directory created/verified${NC}"

# Step 3: Upload files using rsync
echo -e "${YELLOW}📤 Uploading files...${NC}"
rsync -avz --progress \
    --exclude='.git' \
    --exclude='.venv' \
    --exclude='*.tmp' \
    --exclude='router.php' \
    $LOCAL_DIR/ $SERVER:$TARGET_DIR/

echo -e "${GREEN}✅ Files uploaded successfully${NC}"

# Step 4: Set permissions
echo -e "${YELLOW}🔒 Setting file permissions...${NC}"
ssh $SERVER "cd $TARGET_DIR && \
    find . -type d -exec chmod 755 {} \; && \
    find . -type f -exec chmod 644 {} \; && \
    chmod 600 private/config/*.php && \
    chmod 755 private/logs private/cache && \
    echo 'Permissions set successfully'"
echo -e "${GREEN}✅ Permissions configured${NC}"

# Step 5: Test PHP syntax
echo -e "${YELLOW}🧪 Testing PHP syntax...${NC}"
if ssh $SERVER "cd $TARGET_DIR && php -l public/index.php >/dev/null 2>&1"; then
    echo -e "${GREEN}✅ PHP syntax check passed${NC}"
else
    echo -e "${RED}❌ PHP syntax error detected!${NC}"
    exit 1
fi

# Step 6: Create production configuration reminder
echo -e "${YELLOW}⚙️ Creating production setup reminders...${NC}"
ssh $SERVER "cd $TARGET_DIR && cat > PRODUCTION_SETUP.txt << 'EOF'
ProgenPHP Production Setup Checklist
====================================

1. Update Configuration:
   - Edit private/config/app.php
   - Set debug => false
   - Set environment => 'production'
   - Configure database settings

2. Security:
   - Remove or restrict tests/ directory
   - Set up SSL certificate
   - Configure firewall

3. Web Server:
   Apache: Point DocumentRoot to $TARGET_DIR/public/
   Nginx: See DEPLOYMENT.md for configuration

4. Test URLs:
   - Main: https://yourdomain.com/
   - Tests: https://yourdomain.com/tests/ (remove in production)

5. Monitoring:
   - Set up log rotation
   - Monitor private/logs/app.log
   - Configure backups
EOF"

echo -e "${GREEN}✅ Setup reminder created${NC}"

# Step 7: Final summary
echo ""
echo -e "${GREEN}🎉 Deployment completed successfully!${NC}"
echo "=================================="
echo -e "${BLUE}Next steps:${NC}"
echo "1. Configure your web server to point to: $TARGET_DIR/public/"
echo "2. Update configuration in: $TARGET_DIR/private/config/app.php"
echo "3. Set up SSL certificate for HTTPS"
echo "4. Test the installation at your domain"
echo "5. Remove test pages for production"
echo ""
echo -e "${YELLOW}📖 For detailed instructions, see:${NC}"
echo "   - $TARGET_DIR/DEPLOYMENT.md"
echo "   - $TARGET_DIR/PRODUCTION_SETUP.txt"
echo ""
echo -e "${BLUE}Happy hosting! 🚀${NC}"