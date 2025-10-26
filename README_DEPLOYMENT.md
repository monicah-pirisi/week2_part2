# Deployment Instructions

## Quick Setup

1. **Upload files** to your web server
2. **Create database** and import `db/dbforlab.sql`
3. **Update** `settings/db_cred.php` with your database credentials
4. **Set permissions**: `chmod 777 uploads/` directory
5. **Test** by visiting your domain

## Database Setup
```sql
CREATE DATABASE shoppn;
mysql -u username -p shoppn < db/dbforlab.sql
```

## Configuration
Edit `settings/db_cred.php`:
```php
define("SERVER", "localhost");
define("USERNAME", "your_username");
define("PASSWD", "your_password");
define("DATABASE", "shoppn");
```

## Features
- User registration/login
- Admin panel for brand & product management
- Image upload system
- Modern responsive UI

## Admin Access
- Register a user account
- Update user role to 1 in database for admin access
- Access admin panel at `/admin/`
