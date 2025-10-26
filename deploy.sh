#!/bin/bash

# Taste of Africa - Quick Deployment Script
# Run this script on your server after uploading files

echo "🚀 Starting Taste of Africa Deployment..."

# Set colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    echo -e "${YELLOW}Warning: Running as root. Consider using a regular user with sudo privileges.${NC}"
fi

# Set project directory (update this path)
PROJECT_DIR="/var/www/html/tasteofafrica"
UPLOADS_DIR="$PROJECT_DIR/uploads"

echo "📁 Setting up project directory: $PROJECT_DIR"

# Create uploads directory if it doesn't exist
if [ ! -d "$UPLOADS_DIR" ]; then
    echo "📁 Creating uploads directory..."
    mkdir -p "$UPLOADS_DIR"
fi

# Set proper permissions
echo "🔐 Setting file permissions..."
chmod -R 755 "$PROJECT_DIR"
chmod -R 777 "$UPLOADS_DIR"  # Uploads need to be writable
chmod 644 "$PROJECT_DIR/settings/db_cred.php"

# Set ownership (adjust user/group as needed)
echo "👤 Setting file ownership..."
chown -R www-data:www-data "$PROJECT_DIR" 2>/dev/null || echo "Could not set ownership (may need sudo)"

# Check PHP configuration
echo "🐘 Checking PHP configuration..."
php -m | grep -q mysqli && echo -e "${GREEN}✓ MySQLi extension found${NC}" || echo -e "${RED}✗ MySQLi extension missing${NC}"
php -m | grep -q fileinfo && echo -e "${GREEN}✓ Fileinfo extension found${NC}" || echo -e "${RED}✗ Fileinfo extension missing${NC}"
php -m | grep -q gd && echo -e "${GREEN}✓ GD extension found${NC}" || echo -e "${RED}✗ GD extension missing${NC}"

# Check upload limits
echo "📊 Checking PHP upload limits..."
UPLOAD_MAX=$(php -r "echo ini_get('upload_max_filesize');")
POST_MAX=$(php -r "echo ini_get('post_max_size');")
echo "Upload max filesize: $UPLOAD_MAX"
echo "Post max size: $POST_MAX"

if [ "$UPLOAD_MAX" = "2M" ] || [ "$POST_MAX" = "8M" ]; then
    echo -e "${YELLOW}⚠️  Upload limits may be too low. Consider increasing in php.ini${NC}"
fi

# Test database connection (if credentials are set)
echo "🗄️  Testing database connection..."
if php -r "
require_once '$PROJECT_DIR/settings/db_cred.php';
\$conn = mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);
if (\$conn) {
    echo 'Database connection successful';
    mysqli_close(\$conn);
} else {
    echo 'Database connection failed: ' . mysqli_connect_error();
    exit(1);
}
" 2>/dev/null; then
    echo -e "${GREEN}✓ Database connection successful${NC}"
else
    echo -e "${RED}✗ Database connection failed. Please check your credentials in settings/db_cred.php${NC}"
fi

# Create .htaccess if it doesn't exist
if [ ! -f "$PROJECT_DIR/.htaccess" ]; then
    echo "📝 Creating .htaccess file..."
    cp "$PROJECT_DIR/.htaccess" "$PROJECT_DIR/.htaccess" 2>/dev/null || echo "Please manually create .htaccess file"
fi

# Security check
echo "🔒 Running security checks..."
if [ -f "$PROJECT_DIR/settings/db_cred.php" ]; then
    if grep -q "your_production_username\|your_secure_production_password" "$PROJECT_DIR/settings/db_cred.php"; then
        echo -e "${RED}✗ Please update database credentials in settings/db_cred.php${NC}"
    else
        echo -e "${GREEN}✓ Database credentials appear to be configured${NC}"
    fi
else
    echo -e "${RED}✗ Database credentials file not found${NC}"
fi

# Check if error reporting is disabled
if grep -q "error_reporting(0)" "$PROJECT_DIR/settings/core.php"; then
    echo -e "${GREEN}✓ Error reporting is disabled for production${NC}"
else
    echo -e "${YELLOW}⚠️  Consider disabling error reporting for production${NC}"
fi

echo ""
echo "🎉 Deployment setup complete!"
echo ""
echo "📋 Next steps:"
echo "1. Update database credentials in settings/db_cred.php"
echo "2. Import database schema: mysql -u username -p database_name < db/dbforlab.sql"
echo "3. Test the application by visiting your domain"
echo "4. Create an admin account through registration"
echo "5. Update user role to admin in database if needed"
echo ""
echo "🔗 Useful URLs:"
echo "- Main site: http://your-domain.com/"
echo "- Admin panel: http://your-domain.com/admin/"
echo "- Brand management: http://your-domain.com/admin/brand.php"
echo "- Product management: http://your-domain.com/admin/product.php"
echo ""
echo "📚 For detailed instructions, see DEPLOYMENT_GUIDE.md"
