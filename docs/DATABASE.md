# Database Documentation

## Overview
Sistem Rental Kendaraan menggunakan database MySQL dengan struktur yang dirancang untuk mendukung operasi rental kendaraan yang efisien dan aman.

## Database Schema

### Tables Structure

#### 1. roles
Tabel untuk menyimpan jenis peran pengguna dalam sistem.

```sql
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**Default Data:**
- `admin` - Administrator sistem
- `customer` - Pelanggan/pengguna biasa

#### 2. users
Tabel utama untuk menyimpan informasi pengguna.

```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role_id INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);
```

#### 3. vehicle_types
Tabel untuk kategori/jenis kendaraan.

```sql
CREATE TABLE vehicle_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### 4. vehicles
Tabel untuk menyimpan informasi kendaraan.

```sql
CREATE TABLE vehicles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    brand VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    license_plate VARCHAR(20) NOT NULL UNIQUE,
    type_id INT NOT NULL,
    daily_rate DECIMAL(10,2) NOT NULL,
    status ENUM('available', 'rented', 'maintenance') DEFAULT 'available',
    description TEXT,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (type_id) REFERENCES vehicle_types(id)
);
```

#### 5. rentals
Tabel untuk menyimpan informasi penyewaan.

```sql
CREATE TABLE rentals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_days INT NOT NULL,
    daily_rate DECIMAL(10,2) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'active', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id)
);
```

#### 6. payments
Tabel untuk menyimpan informasi pembayaran.

```sql
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    rental_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash', 'transfer', 'credit_card') NOT NULL,
    payment_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    payment_date TIMESTAMP NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rental_id) REFERENCES rentals(id)
);
```

#### 7. reviews
Tabel untuk menyimpan ulasan pelanggan.

```sql
CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    rental_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rental_id) REFERENCES rentals(id)
);
```

#### 8. logs
Tabel untuk menyimpan log aktivitas sistem.

```sql
CREATE TABLE logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

#### 9. notifications
Tabel untuk menyimpan notifikasi pengguna.

```sql
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

## Relationships

### Entity Relationship Diagram
![ERD Diagram](../erd_diagram.png)

### Key Relationships:
1. **roles → users** (1:N) - Satu role dapat dimiliki banyak users
2. **vehicle_types → vehicles** (1:N) - Satu tipe dapat memiliki banyak kendaraan
3. **users → rentals** (1:N) - Satu user dapat memiliki banyak rental
4. **vehicles → rentals** (1:N) - Satu kendaraan dapat dirental berkali-kali
5. **rentals → payments** (1:N) - Satu rental dapat memiliki banyak pembayaran
6. **rentals → reviews** (1:1) - Satu rental dapat memiliki satu review
7. **users → logs** (1:N) - Satu user dapat memiliki banyak log
8. **users → notifications** (1:N) - Satu user dapat memiliki banyak notifikasi

## Database Configuration

### Connection Settings
File konfigurasi database terletak di `src/includes/db.php`:

```php
<?php
$host = 'localhost';
$dbname = 'rental_kendaraan';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
```

## Security Considerations

### Password Security
- Passwords di-hash menggunakan `password_hash()` dengan algoritma bcrypt
- Verifikasi menggunakan `password_verify()`

### SQL Injection Prevention
- Semua query menggunakan prepared statements
- Input validation dan sanitization

### Data Integrity
- Foreign key constraints untuk menjaga referential integrity
- Check constraints untuk validasi data
- Unique constraints untuk mencegah duplikasi

## Backup and Maintenance

### Regular Backup
```bash
# Backup database
mysqldump -u username -p rental_kendaraan > backup_$(date +%Y%m%d).sql

# Restore database
mysql -u username -p rental_kendaraan < backup_file.sql
```

### Performance Optimization
- Index pada kolom yang sering di-query
- Regular ANALYZE TABLE untuk update statistics
- Monitor slow queries

## Migration Scripts

Untuk update schema, gunakan migration scripts yang terstruktur:

```sql
-- Migration example: Add new column
ALTER TABLE vehicles ADD COLUMN fuel_type VARCHAR(20) DEFAULT 'gasoline';

-- Migration example: Add index
CREATE INDEX idx_rentals_status ON rentals(status);
CREATE INDEX idx_vehicles_status ON vehicles(status);
```