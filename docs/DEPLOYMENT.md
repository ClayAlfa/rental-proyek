# Deployment Guide

## System Requirements

### Server Requirements
- **Web Server:** Apache 2.4+ atau Nginx 1.18+
- **PHP:** Version 8.0 atau lebih tinggi
- **Database:** MySQL 8.0+ atau MariaDB 10.5+
- **Memory:** Minimum 512MB RAM
- **Storage:** Minimum 100MB disk space

### PHP Extensions Required
```bash
# Required PHP extensions
php-pdo
php-pdo-mysql
php-session
php-json
php-mbstring
php-openssl
php-curl
```

## Local Development Setup

### 1. XAMPP Installation (Windows)
```bash
# Download XAMPP dari https://www.apachefriends.org/
# Install dan jalankan Apache + MySQL
```

### 2. Project Setup
```bash
# Clone project ke htdocs
cd C:\xampp\htdocs\
git clone <repository-url> rental
cd rental
```

### 3. Database Setup
```bash
# Buka phpMyAdmin (http://localhost/phpmyadmin)
# Buat database baru: rental_kendaraan
# Import file: sql/schema.sql
```

### 4. Configuration
```php
// Edit src/includes/db.php
$host = 'localhost';
$dbname = 'rental_kendaraan';
$username = 'root';
$password = '';
```

### 5. Access Application
```
http://localhost/rental/src/
```

## Production Deployment

### 1. Server Preparation

#### Ubuntu/Debian
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Apache
sudo apt install apache2 -y

# Install PHP 8.0+
sudo apt install php8.0 php8.0-mysql php8.0-mbstring php8.0-xml php8.0-curl -y

# Install MySQL
sudo apt install mysql-server -y
```

#### CentOS/RHEL
```bash
# Update system
sudo yum update -y

# Install Apache
sudo yum install httpd -y

# Install PHP 8.0+
sudo yum install php php-mysql php-mbstring php-xml php-curl -y

# Install MySQL
sudo yum install mysql-server -y
```

### 2. Web Server Configuration

#### Apache Configuration
```apache
# /etc/apache2/sites-available/rental.conf
<VirtualHost *:80>
    ServerName rental.yourdomain.com
    DocumentRoot /var/www/html/rental/src
    
    <Directory /var/www/html/rental/src>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/rental_error.log
    CustomLog ${APACHE_LOG_DIR}/rental_access.log combined
</VirtualHost>
```

```bash
# Enable site
sudo a2ensite rental.conf
sudo a2enmod rewrite
sudo systemctl reload apache2
```

#### Nginx Configuration
```nginx
# /etc/nginx/sites-available/rental
server {
    listen 80;
    server_name rental.yourdomain.com;
    root /var/www/html/rental/src;
    index index.php index.html;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.ht {
        deny all;
    }
}
```

### 3. Database Setup (Production)

```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Create database and user
mysql -u root -p
```

```sql
CREATE DATABASE rental_kendaraan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'rental_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON rental_kendaraan.* TO 'rental_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

```bash
# Import schema
mysql -u rental_user -p rental_kendaraan < sql/schema.sql
```

### 4. File Permissions

```bash
# Set proper ownership
sudo chown -R www-data:www-data /var/www/html/rental/

# Set proper permissions
sudo find /var/www/html/rental/ -type d -exec chmod 755 {} \;
sudo find /var/www/html/rental/ -type f -exec chmod 644 {} \;

# Make specific directories writable (if needed for uploads)
sudo chmod -R 775 /var/www/html/rental/uploads/
```

### 5. Security Configuration

#### Environment Variables
```php
// Create src/includes/config.php
<?php
return [
    'database' => [
        'host' => $_ENV['DB_HOST'] ?? 'localhost',
        'name' => $_ENV['DB_NAME'] ?? 'rental_kendaraan',
        'user' => $_ENV['DB_USER'] ?? 'rental_user',
        'pass' => $_ENV['DB_PASS'] ?? 'your_password'
    ],
    'app' => [
        'debug' => $_ENV['APP_DEBUG'] ?? false,
        'url' => $_ENV['APP_URL'] ?? 'https://rental.yourdomain.com'
    ]
];
```

#### SSL Certificate (Let's Encrypt)
```bash
# Install Certbot
sudo apt install certbot python3-certbot-apache -y

# Get SSL certificate
sudo certbot --apache -d rental.yourdomain.com

# Auto-renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

## Docker Deployment

### Dockerfile
```dockerfile
FROM php:8.0-apache

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy application files
COPY src/ /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html/
RUN chmod -R 755 /var/www/html/

EXPOSE 80
```

### docker-compose.yml
```yaml
version: '3.8'

services:
  web:
    build: .
    ports:
      - "80:80"
    volumes:
      - ./src:/var/www/html
    depends_on:
      - db
    environment:
      - DB_HOST=db
      - DB_NAME=rental_kendaraan
      - DB_USER=rental_user
      - DB_PASS=rental_password

  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root_password
      MYSQL_DATABASE: rental_kendaraan
      MYSQL_USER: rental_user
      MYSQL_PASSWORD: rental_password
    volumes:
      - mysql_data:/var/lib/mysql
      - ./sql/schema.sql:/docker-entrypoint-initdb.d/schema.sql

volumes:
  mysql_data:
```

```bash
# Deploy with Docker
docker-compose up -d
```

## Monitoring and Maintenance

### Log Monitoring
```bash
# Apache logs
sudo tail -f /var/log/apache2/rental_error.log
sudo tail -f /var/log/apache2/rental_access.log

# MySQL logs
sudo tail -f /var/log/mysql/error.log
```

### Performance Monitoring
```bash
# Check server resources
htop
df -h
free -m

# MySQL performance
mysql -u root -p -e "SHOW PROCESSLIST;"
mysql -u root -p -e "SHOW STATUS LIKE 'Slow_queries';"
```

### Backup Strategy
```bash
#!/bin/bash
# backup.sh - Daily backup script

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/rental"
DB_NAME="rental_kendaraan"
DB_USER="rental_user"
DB_PASS="rental_password"

# Create backup directory
mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_$DATE.sql

# Files backup
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/html/rental/

# Keep only last 7 days
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete
```

```bash
# Add to crontab
sudo crontab -e
# Add: 0 2 * * * /path/to/backup.sh
```

## Troubleshooting

### Common Issues

#### 1. Database Connection Error
```bash
# Check MySQL service
sudo systemctl status mysql

# Check database credentials
mysql -u rental_user -p rental_kendaraan
```

#### 2. Permission Denied
```bash
# Fix file permissions
sudo chown -R www-data:www-data /var/www/html/rental/
sudo chmod -R 755 /var/www/html/rental/
```

#### 3. PHP Errors
```bash
# Check PHP error log
sudo tail -f /var/log/apache2/error.log

# Enable PHP error reporting (development only)
# Add to php.ini:
display_errors = On
error_reporting = E_ALL
```

#### 4. Session Issues
```bash
# Check session directory permissions
ls -la /var/lib/php/sessions/
sudo chown -R www-data:www-data /var/lib/php/sessions/
```

## Performance Optimization

### PHP Configuration
```ini
# php.ini optimizations
memory_limit = 256M
max_execution_time = 60
upload_max_filesize = 10M
post_max_size = 10M
session.gc_maxlifetime = 3600
opcache.enable = 1
opcache.memory_consumption = 128
```

### MySQL Optimization
```sql
-- Add indexes for better performance
CREATE INDEX idx_rentals_user_id ON rentals(user_id);
CREATE INDEX idx_rentals_vehicle_id ON rentals(vehicle_id);
CREATE INDEX idx_rentals_status ON rentals(status);
CREATE INDEX idx_vehicles_status ON vehicles(status);
CREATE INDEX idx_users_role_id ON users(role_id);
```

### Apache Optimization
```apache
# Enable compression
LoadModule deflate_module modules/mod_deflate.so
<Location />
    SetOutputFilter DEFLATE
    SetEnvIfNoCase Request_URI \
        \.(?:gif|jpe?g|png)$ no-gzip dont-vary
    SetEnvIfNoCase Request_URI \
        \.(?:exe|t?gz|zip|bz2|sit|rar)$ no-gzip dont-vary
</Location>

# Enable caching
LoadModule expires_module modules/mod_expires.so
ExpiresActive On
ExpiresByType text/css "access plus 1 month"
ExpiresByType application/javascript "access plus 1 month"
ExpiresByType image/png "access plus 1 month"
ExpiresByType image/jpg "access plus 1 month"
ExpiresByType image/jpeg "access plus 1 month"
```