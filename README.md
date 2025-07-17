# 🚗 Sistem Rental Kendaraan

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-orange.svg)](https://mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple.svg)](https://getbootstrap.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

Sistem Rental Kendaraan adalah aplikasi web modern yang dirancang untuk mengelola penyewaan kendaraan secara efisien. Aplikasi ini menyediakan interface yang intuitif untuk administrator dan pelanggan dengan fitur-fitur lengkap untuk manajemen rental.

## ✨ Fitur Utama

### 👨‍💼 Administrator
- **Dashboard Komprehensif** - Statistik real-time dan overview sistem
- **Manajemen Kendaraan** - CRUD lengkap untuk data kendaraan
- **Manajemen Pengguna** - Kontrol akses dan data pengguna
- **Manajemen Penyewaan** - Monitoring dan kontrol status rental
- **Sistem Logging** - Tracking aktivitas dan audit trail
- **Laporan & Analytics** - Insight bisnis dan performa

### 👤 Customer
- **Dashboard Personal** - Overview penyewaan dan statistik personal
- **Katalog Kendaraan** - Browse dan filter kendaraan tersedia
- **Sistem Booking** - Proses reservasi yang mudah dan cepat
- **Riwayat Penyewaan** - Tracking semua transaksi rental
- **Manajemen Profil** - Update informasi personal dan keamanan

## 🛠️ Teknologi

- **Backend:** PHP 8.0+ dengan PDO
- **Database:** MySQL 8.0+ / MariaDB 10.5+
- **Frontend:** Bootstrap 5.3, HTML5, CSS3
- **Authentication:** Session-based dengan bcrypt hashing
- **Icons:** Bootstrap Icons
- **Security:** Prepared statements, input validation, CSRF protection

## 📋 Persyaratan Sistem

- **Web Server:** Apache 2.4+ atau Nginx 1.18+
- **PHP:** Version 8.0 atau lebih tinggi
- **Database:** MySQL 8.0+ atau MariaDB 10.5+
- **Memory:** Minimum 512MB RAM
- **Storage:** Minimum 100MB disk space

### PHP Extensions Required
```
php-pdo
php-pdo-mysql
php-session
php-json
php-mbstring
php-openssl
php-curl
```

## 🚀 Quick Start

### 1. Clone Repository
```bash
git clone <repository-url> rental
cd rental
```

### 2. Setup Database
```sql
CREATE DATABASE rental_kendaraan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. Import Schema
```bash
mysql -u username -p rental_kendaraan < sql/schema.sql
```

### 4. Configure Database
Edit `src/includes/db.php`:
```php
$host = 'localhost';
$dbname = 'rental_kendaraan';
$username = 'your_username';
$password = 'your_password';
```

### 5. Access Application
```
http://localhost/rental/src/
```

## 📚 Dokumentasi

| Dokumen | Deskripsi |
|---------|-----------|
| [📖 Installation Guide](docs/INSTALLATION.md) | Panduan instalasi lengkap untuk berbagai environment |
| [🚀 Deployment Guide](docs/DEPLOYMENT.md) | Panduan deployment untuk production |
| [💾 Database Documentation](docs/DATABASE.md) | Struktur database dan relationship |
| [📋 User Guide](docs/USAGE.md) | Panduan penggunaan untuk admin dan customer |

## 🏗️ Struktur Proyek

```
rental/
├── docs/                          # 📚 Dokumentasi
│   ├── DATABASE.md
│   ├── DEPLOYMENT.md
│   ├── INSTALLATION.md
│   ├── USAGE.md
│   └── erd_diagram.png
├── sql/                           # 🗄️ Database
│   └── schema.sql
├── src/                           # 💻 Source Code
│   ├── admin/                     # 👨‍💼 Admin Panel
│   │   ├── dashboard.php
│   │   ├── users/
│   │   ├── vehicles/
│   │   ├── vehicle_types/
│   │   ├── rentals/
│   │   ├── logs/
│   │   └── settings/
│   ├── assets/                    # 🎨 Static Assets
│   │   ├── css/
│   │   └── js/
│   ├── includes/                  # 🔧 Core Files
│   │   ├── auth.php
│   │   ├── db.php
│   │   ├── functions.php
│   │   ├── header.php
│   │   ├── navbar.php
│   │   └── footer.php
│   ├── index.php                  # 🏠 Landing Page
│   ├── login.php                  # 🔐 Authentication
│   ├── register.php
│   ├── user_dashboard.php         # 👤 User Dashboard
│   ├── vehicles.php               # 🚗 Vehicle Catalog
│   ├── booking.php                # 📝 Booking System
│   └── profile.php                # 👤 Profile Management
└── README.md                      # 📄 This file
```

## 🔐 Default Accounts

### Administrator
```
Username: admin
Password: admin123
Access: Full system administration
```

### Customer Test Accounts
```
Username: user1          Username: user2
Password: user123        Password: user123
Name: John Doe          Name: Jane Smith
```

> ⚠️ **Security Notice:** Ubah password default setelah instalasi!

## 🔒 Keamanan

- **Password Hashing:** bcrypt dengan salt
- **SQL Injection Protection:** Prepared statements
- **XSS Prevention:** Input sanitization dan output escaping
- **Session Security:** Secure session management
- **Access Control:** Role-based permissions
- **Audit Trail:** Comprehensive logging system

## 🎨 Screenshots

### Admin Dashboard
![Admin Dashboard](docs/screenshots/admin-dashboard.png)

### User Dashboard
![User Dashboard](docs/screenshots/user-dashboard.png)

### Vehicle Catalog
![Vehicle Catalog](docs/screenshots/vehicle-catalog.png)

## 🗄️ Database Schema

![ERD Diagram](docs/erd_diagram.png)

### Key Tables
- **users** - User accounts and profiles
- **vehicles** - Vehicle inventory
- **rentals** - Rental transactions
- **payments** - Payment records
- **logs** - System activity logs

## 🚀 Deployment

### Development (XAMPP)
```bash
# Start XAMPP services
# Copy project to htdocs/rental
# Import database schema
# Configure database connection
```

### Production (Linux)
```bash
# Install LAMP stack
sudo apt install apache2 php8.0 mysql-server
# Configure virtual host
# Setup SSL certificate
# Configure security settings
```

### Docker
```bash
docker-compose up -d
```

Lihat [Deployment Guide](docs/DEPLOYMENT.md) untuk instruksi lengkap.

## 🧪 Testing

### Manual Testing
1. Test user registration dan login
2. Test booking flow
3. Test admin functions
4. Test security features

### Test Accounts
Gunakan akun default yang tersedia untuk testing fitur-fitur aplikasi.

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📝 Changelog

### Version 1.0.0
- ✅ Initial release
- ✅ User authentication system
- ✅ Vehicle management
- ✅ Booking system
- ✅ Admin dashboard
- ✅ User dashboard

## 🐛 Known Issues

- [ ] File upload untuk gambar kendaraan (dalam development)
- [ ] Email notification system (planned)
- [ ] Payment gateway integration (planned)

## 📞 Support

Untuk bantuan dan support:

- 📧 Email: support@rental.com
- 📱 Phone: +62 xxx-xxxx-xxxx
- 💬 Issues: [GitHub Issues](https://github.com/username/rental/issues)

## 📄 License

Distributed under the MIT License. See `LICENSE` for more information.

## 👥 Team

- **Lead Developer** - [Your Name](https://github.com/username)
- **UI/UX Designer** - [Designer Name](https://github.com/designer)
- **Database Architect** - [DBA Name](https://github.com/dba)

## 🙏 Acknowledgments

- [Bootstrap](https://getbootstrap.com) - UI Framework
- [Bootstrap Icons](https://icons.getbootstrap.com) - Icon library
- [Unsplash](https://unsplash.com) - Stock photos
- [PHP](https://php.net) - Server-side scripting
- [MySQL](https://mysql.com) - Database management

---

<div align="center">
  <p>Made with ❤️ by the Rental Team</p>
  <p>© 2024 Sistem Rental Kendaraan. All rights reserved.</p>
</div>
