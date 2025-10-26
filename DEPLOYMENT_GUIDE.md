# Taste of Africa - Deployment Guide

## 🚀 Server Deployment Instructions

### Prerequisites
- Web server with PHP 7.4+ (Apache/Nginx)
- MySQL/MariaDB database
- PHP extensions: mysqli, fileinfo, gd (for image processing)
- SSL certificate (recommended for production)

### 1. Database Setup

#### Create Database
```sql
CREATE DATABASE shoppn CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### Import Database Schema
```bash
mysql -u your_username -p shoppn < db/dbforlab.sql
```

#### Create Database User (Optional but recommended)
```sql
CREATE USER 'tasteofafrica'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON shoppn.* TO 'tasteofafrica'@'localhost';
FLUSH PRIVILEGES;
```

### 2. File Upload

#### Upload Files to Server
```bash
# Upload all files to your web server directory
# Example: /var/www/html/tasteofafrica/
```

#### Set Proper Permissions
```bash
# Set directory permissions
chmod 755 /path/to/your/project
chmod 755 /path/to/your/project/uploads
chmod 644 /path/to/your/project/settings/db_cred.php

# Set ownership (adjust user/group as needed)
chown -R www-data:www-data /path/to/your/project
```

### 3. Configuration

#### Update Database Credentials
Edit `settings/db_cred.php`:
```php
<?php
//Database credentials
if (!defined("SERVER")) {
    define("SERVER", "localhost"); // or your database server
}

if (!defined("USERNAME")) {
    define("USERNAME", "your_database_username");
}

if (!defined("PASSWD")) {
    define("PASSWD", "your_database_password");
}

if (!defined("DATABASE")) {
    define("DATABASE", "shoppn");
}
?>
```

#### Configure PHP Settings
Create or update `.htaccess` file in project root:
```apache
# Enable error reporting for debugging (disable in production)
php_flag display_errors On
php_value error_reporting E_ALL

# Set upload limits
php_value upload_max_filesize 10M
php_value post_max_size 10M
php_value max_execution_time 300

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"

# Prevent access to sensitive files
<Files "db_cred.php">
    Order allow,deny
    Deny from all
</Files>

<Files "core.php">
    Order allow,deny
    Deny from all
</Files>
```

### 4. Security Configuration

#### Disable Error Display in Production
Edit `settings/core.php` and comment out:
```php
// Disable these lines in production
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
```

#### Update Session Security
The session configuration in `settings/core.php` is already secure for production.

### 5. Directory Structure Verification

Ensure your server has this structure:
```
your-domain.com/
├── actions/
├── admin/
├── classes/
├── controllers/
├── db/
├── js/
├── login/
├── settings/
├── uploads/          # Must be writable
├── index.php
└── .htaccess
```

### 6. Testing After Deployment

1. **Test Database Connection**
   - Visit your domain
   - Try to register a new user
   - Check if data is saved to database

2. **Test Admin Functions**
   - Login with admin account
   - Test brand management
   - Test product management
   - Test image uploads

3. **Test File Permissions**
   - Verify uploads directory is writable
   - Test image upload functionality

### 7. Production Optimizations

#### Enable Caching
Add to `.htaccess`:
```apache
# Enable compression
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

# Browser caching
<IfModule mod_expires.c>
    ExpiresActive on
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
</IfModule>
```

#### Database Optimization
```sql
-- Add indexes for better performance
ALTER TABLE products ADD INDEX idx_category (product_cat);
ALTER TABLE products ADD INDEX idx_brand (product_brand);
ALTER TABLE products ADD INDEX idx_price (product_price);
```

### 8. Monitoring & Maintenance

#### Log Files
Monitor these log files:
- Apache/Nginx error logs
- PHP error logs
- MySQL error logs

#### Regular Backups
```bash
# Database backup
mysqldump -u username -p shoppn > backup_$(date +%Y%m%d).sql

# File backup
tar -czf files_backup_$(date +%Y%m%d).tar.gz /path/to/your/project
```

### 9. Troubleshooting

#### Common Issues

1. **Database Connection Failed**
   - Check database credentials in `db_cred.php`
   - Verify database server is running
   - Check firewall settings

2. **Image Upload Not Working**
   - Check uploads directory permissions
   - Verify PHP upload settings
   - Check disk space

3. **JSON Parsing Errors**
   - Check for PHP errors in response
   - Verify all files have proper PHP tags
   - Check for BOM characters

4. **Session Issues**
   - Check session directory permissions
   - Verify session configuration
   - Clear browser cookies

### 10. Security Checklist

- [ ] Database credentials are secure
- [ ] Error reporting disabled in production
- [ ] File permissions are correct
- [ ] Sensitive files are protected
- [ ] SSL certificate is installed
- [ ] Regular backups are scheduled
- [ ] Security headers are configured
- [ ] File upload restrictions are in place

## 🎉 Deployment Complete!

Your Taste of Africa restaurant discovery platform is now ready for production use!

### Features Available:
- ✅ User Registration & Login
- ✅ Admin Authentication
- ✅ Brand Management (CRUD)
- ✅ Category Management
- ✅ Product Management (CRUD)
- ✅ Image Upload System
- ✅ Modern Responsive UI
- ✅ Form Validation
- ✅ Error Handling

### Admin Access:
- Navigate to: `your-domain.com/admin/`
- Login with admin credentials
- Manage brands, categories, and products

### User Access:
- Navigate to: `your-domain.com/`
- Register new accounts
- Browse the platform

---

**Need Help?** Check the troubleshooting section or review the code comments for detailed explanations.
