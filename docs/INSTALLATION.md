# Installation Guide

## Prerequisites

Sebelum memulai instalasi, pastikan sistem Anda memenuhi persyaratan berikut:

### System Requirements
- **Operating System:** Windows 10/11, macOS 10.15+, atau Linux (Ubuntu 18.04+)
- **Web Server:** Apache 2.4+ atau Nginx 1.18+
- **PHP:** Version 8.0 atau lebih tinggi
- **Database:** MySQL 8.0+ atau MariaDB 10.5+
- **Memory:** Minimum 512MB RAM
- **Storage:** Minimum 100MB disk space

### Required PHP Extensions
```bash
php-pdo
php-pdo-mysql
php-session
php-json
php-mbstring
php-openssl
php-curl
php-gd (optional, untuk manipulasi gambar)
```

## Installation Methods

### Method 1: XAMPP (Recommended for Development)

#### Step 1: Download and Install XAMPP
1. Kunjungi [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Download XAMPP untuk sistem operasi Anda
3. Install XAMPP dengan pengaturan default
4. Jalankan XAMPP Control Panel

#### Step 2: Start Services
```bash
# Melalui XAMPP Control Panel, start:
- Apache
- MySQL
```

#### Step 3: Download Project
```bash
# Option A: Download ZIP
# Download project dari repository dan extract ke folder htdocs

# Option B: Git Clone (jika Git tersedia)
cd C:\xampp\htdocs\  # Windows
cd /Applications/XAMPP/htdocs/  # macOS
cd /opt/lampp/htdocs/  # Linux

git clone <repository-url> rental
```

#### Step 4: Database Setup
1. Buka browser dan akses `http://localhost/phpmyadmin`
2. Klik "New" untuk membuat database baru
3. Nama database: `rental_kendaraan`
4. Collation: `utf8mb4_unicode_ci`
5. Klik "Create"
6. Pilih database yang baru dibuat
7. Klik tab "Import"
8. Pilih file `sql/schema.sql` dari folder project
9. Klik "Go" untuk import

#### Step 5: Configuration
Edit file `src/includes/db.php`:
```php
<?php
$host = 'localhost';
$dbname = 'rental_kendaraan';
$username = 'root';
$password = '';  // Kosong untuk XAMPP default

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
```

#### Step 6: Access Application
Buka browser dan akses: `http://localhost/rental/src/`

### Method 2: Manual Installation (Linux/Ubuntu)

#### Step 1: Update System
```bash
sudo apt update && sudo apt upgrade -y
```

#### Step 2: Install Apache
```bash
sudo apt install apache2 -y
sudo systemctl start apache2
sudo systemctl enable apache2
```

#### Step 3: Install PHP
```bash
sudo apt install php8.0 php8.0-mysql php8.0-mbstring php8.0-xml php8.0-curl php8.0-gd libapache2-mod-php8.0 -y
```

#### Step 4: Install MySQL
```bash
sudo apt install mysql-server -y
sudo mysql_secure_installation
```

#### Step 5: Configure MySQL
```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE rental_kendaraan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'rental_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON rental_kendaraan.* TO 'rental_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### Step 6: Download and Setup Project
```bash
cd /var/www/html/
sudo git clone <repository-url> rental
sudo chown -R www-data:www-data rental/
sudo chmod -R 755 rental/
```

#### Step 7: Import Database
```bash
mysql -u rental_user -p rental_kendaraan < /var/www/html/rental/sql/schema.sql
```

#### Step 8: Configure Database Connection
```bash
sudo nano /var/www/html/rental/src/includes/db.php
```

```php
<?php
$host = 'localhost';
$dbname = 'rental_kendaraan';
$username = 'rental_user';
$password = 'your_secure_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
```

#### Step 9: Configure Apache
```bash
sudo nano /etc/apache2/sites-available/rental.conf
```

```apache
<VirtualHost *:80>
    ServerName localhost
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
sudo a2ensite rental.conf
sudo a2enmod rewrite
sudo systemctl reload apache2
```

### Method 3: Docker Installation

#### Step 1: Install Docker
```bash
# Ubuntu/Debian
sudo apt install docker.io docker-compose -y

# Windows/macOS
# Download Docker Desktop dari https://www.docker.com/products/docker-desktop
```

#### Step 2: Create Docker Files
Create `Dockerfile` in project root:
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

Create `docker-compose.yml`:
```yaml
version: '3.8'

services:
  web:
    build: .
    ports:
      - "8080:80"
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
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql
      - ./sql/schema.sql:/docker-entrypoint-initdb.d/schema.sql

volumes:
  mysql_data:
```

#### Step 3: Run Docker
```bash
docker-compose up -d
```

#### Step 4: Access Application
Buka browser dan akses: `http://localhost:8080`

## Post-Installation Setup

### 1. Verify Installation
Akses aplikasi melalui browser dan pastikan:
- Halaman login dapat diakses
- Database connection berhasil
- Tidak ada error PHP

### 2. Test Default Accounts
Login dengan akun default:

**Admin Account:**
- Username: `admin`
- Password: `admin123`

**Customer Account:**
- Username: `user1`
- Password: `user123`

### 3. Security Configuration

#### Change Default Passwords
```sql
-- Update admin password
UPDATE users SET password = '$2y$10$newhashedpassword' WHERE username = 'admin';

-- Update user passwords
UPDATE users SET password = '$2y$10$newhashedpassword' WHERE username = 'user1';
```

#### File Permissions (Linux)
```bash
# Set proper ownership
sudo chown -R www-data:www-data /var/www/html/rental/

# Set proper permissions
sudo find /var/www/html/rental/ -type d -exec chmod 755 {} \;
sudo find /var/www/html/rental/ -type f -exec chmod 644 {} \;
```

### 4. Optional Configurations

#### Enable Error Logging
Edit `src/includes/functions.php` dan tambahkan:
```php
// Enable error logging for debugging
ini_set('log_errors', 1);
ini_set('error_log', '../logs/php_errors.log');
```

#### Create Upload Directory
```bash
mkdir -p src/uploads/vehicles
chmod 755 src/uploads/vehicles
```

#### Configure Email (Optional)
Edit `src/includes/config.php`:
```php
<?php
return [
    'email' => [
        'smtp_host' => 'smtp.gmail.com',
        'smtp_port' => 587,
        'smtp_user' => 'your-email@gmail.com',
        'smtp_pass' => 'your-app-password',
        'from_email' => 'noreply@rental.com',
        'from_name' => 'Rental System'
    ]
];
```

## Troubleshooting

### Common Installation Issues

#### 1. Database Connection Failed
**Error:** `Connection failed: SQLSTATE[HY000] [1045] Access denied`

**Solution:**
```bash
# Check MySQL service
sudo systemctl status mysql

# Reset MySQL password
sudo mysql -u root -p
ALTER USER 'root'@'localhost' IDENTIFIED BY 'new_password';
FLUSH PRIVILEGES;
```

#### 2. Permission Denied
**Error:** `Permission denied` atau `403 Forbidden`

**Solution:**
```bash
# Fix file permissions
sudo chown -R www-data:www-data /var/www/html/rental/
sudo chmod -R 755 /var/www/html/rental/
```

#### 3. PHP Extensions Missing
**Error:** `Call to undefined function PDO()`

**Solution:**
```bash
# Install missing PHP extensions
sudo apt install php8.0-mysql php8.0-pdo -y
sudo systemctl restart apache2
```

#### 4. Apache Not Starting
**Error:** Apache fails to start

**Solution:**
```bash
# Check Apache error log
sudo tail -f /var/log/apache2/error.log

# Check port conflicts
sudo netstat -tlnp | grep :80

# Restart Apache
sudo systemctl restart apache2
```

#### 5. Database Import Failed
**Error:** SQL import errors

**Solution:**
```bash
# Check SQL file syntax
mysql -u root -p --force rental_kendaraan < sql/schema.sql

# Import with verbose output
mysql -u root -p -v rental_kendaraan < sql/schema.sql
```

### Performance Optimization

#### PHP Configuration
Edit `/etc/php/8.0/apache2/php.ini`:
```ini
memory_limit = 256M
max_execution_time = 60
upload_max_filesize = 10M
post_max_size = 10M
```

#### MySQL Configuration
Edit `/etc/mysql/mysql.conf.d/mysqld.cnf`:
```ini
[mysqld]
innodb_buffer_pool_size = 128M
query_cache_size = 32M
query_cache_limit = 2M
```

## Verification Checklist

Setelah instalasi selesai, pastikan semua item berikut berfungsi:

- [ ] Aplikasi dapat diakses melalui browser
- [ ] Database connection berhasil
- [ ] Login admin berfungsi
- [ ] Login customer berfungsi
- [ ] Dashboard admin dapat diakses
- [ ] Dashboard user dapat diakses
- [ ] Navigasi antar halaman berfungsi
- [ ] Form registration berfungsi
- [ ] Logout berfungsi
- [ ] Session management berfungsi
- [ ] Error handling berfungsi
- [ ] File permissions sudah benar
- [ ] Log files dapat ditulis (jika diaktifkan)

## Next Steps

Setelah instalasi berhasil:

1. Baca [USAGE.md](USAGE.md) untuk panduan penggunaan
2. Baca [DATABASE.md](DATABASE.md) untuk memahami struktur database
3. Baca [DEPLOYMENT.md](DEPLOYMENT.md) untuk deployment ke production
4. Customize aplikasi sesuai kebutuhan
5. Setup backup dan monitoring

## Support

Jika mengalami masalah selama instalasi:

1. Periksa log error di `/var/log/apache2/error.log`
2. Periksa log PHP error
3. Pastikan semua service berjalan
4. Verifikasi konfigurasi database
5. Periksa file permissions

Untuk bantuan lebih lanjut, silakan buat issue di repository atau hubungi tim development.