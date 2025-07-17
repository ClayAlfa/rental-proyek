/* ======================================================
   BUAT DATABASE
======================================================*/
DROP DATABASE IF EXISTS rental_kendaraan;
CREATE DATABASE rental_kendaraan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE rental_kendaraan;

/* ======================================================
   1.  roles
======================================================*/
CREATE TABLE roles (
    id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

INSERT INTO roles (id, role_name) VALUES
(1,'admin'),
(2,'customer');

/* ======================================================
   2.  users
======================================================*/
CREATE TABLE users (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    full_name  VARCHAR(100) NOT NULL,
    email      VARCHAR(100),
    phone      VARCHAR(20),
    role_id    INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_roles
      FOREIGN KEY (role_id) REFERENCES roles(id)
      ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

/* Admin (password: admin123) */
INSERT INTO users (username,password,full_name,email,phone,role_id) VALUES (
  'admin',
  '$2y$10$r5nMbzrb9WpL0FZYXL/FUOpEJJlOaJXtsK8zB4s4EoPlZKU5GScsK',
  'Administrator',
  'admin@rental.com',
  '081234567890',
  1
);

/* Customer dummy (password: user123) */
INSERT INTO users (username,password,full_name,email,phone,role_id) VALUES (
  'user1',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
  'John Doe',
  'john@example.com',
  '081234567891',
  2
),
(
  'user2',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
  'Jane Smith',
  'jane@example.com',
  '081234567892',
  2
);

/* ======================================================
   3.  vehicle_types
======================================================*/
CREATE TABLE vehicle_types (
    id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type_name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

INSERT INTO vehicle_types (id,type_name) VALUES
(1,'Mobil'),(2,'Motor'),(3,'Sepeda Listrik');

/* ======================================================
   4.  vehicles
======================================================*/
CREATE TABLE vehicles (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    brand         VARCHAR(100) NOT NULL,
    model         VARCHAR(100) NOT NULL,
    type_id       INT UNSIGNED NOT NULL,
    price_per_day INT UNSIGNED NOT NULL,
    seats         TINYINT      NOT NULL DEFAULT 1,
    transmission  ENUM('Manual','Automatic') DEFAULT 'Manual',
    fuel          ENUM('Bensin','Diesel','Listrik') DEFAULT 'Bensin',
    image         VARCHAR(255),
    status        ENUM('Tersedia','Disewa','Servis') DEFAULT 'Tersedia',
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_vehicle_type
      FOREIGN KEY (type_id) REFERENCES vehicle_types(id)
) ENGINE=InnoDB;

INSERT INTO vehicles
  (brand, model, type_id, price_per_day, seats, transmission, fuel, image, status) VALUES
('Toyota','Fortuner', 1, 800000, 7,'Automatic','Diesel','https://placehold.co/600x400?text=Fortuner','Tersedia'),
('Honda','HR‑V',      1, 600000, 5,'Automatic','Bensin','https://placehold.co/600x400?text=HR-V','Tersedia'),
('Toyota','Innova',   1, 500000, 7,'Manual','Diesel','https://placehold.co/600x400?text=Innova','Tersedia'),
('Yamaha','NMAX',     2, 180000, 2,'Automatic','Bensin','https://placehold.co/600x400?text=NMAX','Tersedia'),
('Honda','Vario 160', 2, 150000, 2,'Automatic','Bensin','https://placehold.co/600x400?text=Vario160','Tersedia'),
('Yadea','G5',        3, 120000, 1,'Automatic','Listrik','https://placehold.co/600x400?text=Yadea+G5','Tersedia'),
('Selis','E‑Bike',    3, 100000, 1,'Automatic','Listrik','https://placehold.co/600x400?text=Selis+EBike','Tersedia');

/* ======================================================
   5.  rentals
======================================================*/
CREATE TABLE rentals (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id      INT UNSIGNED NOT NULL,
    vehicle_id   INT UNSIGNED NOT NULL,
    rent_date    DATE NOT NULL,
    return_date  DATE NOT NULL,
    total_price  INT UNSIGNED NOT NULL,
    status       ENUM('Dipesan','Ongoing','Selesai','Dibatalkan') DEFAULT 'Dipesan',
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_rentals_users
      FOREIGN KEY (user_id)    REFERENCES users(id),
    CONSTRAINT fk_rentals_vehicles
      FOREIGN KEY (vehicle_id) REFERENCES vehicles(id)
) ENGINE=InnoDB;

/* ======================================================
   6.  payments
======================================================*/
CREATE TABLE payments (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rental_id      INT UNSIGNED NOT NULL,
    payment_date   DATE NOT NULL,
    amount_paid    INT UNSIGNED NOT NULL,
    payment_method ENUM('Cash','Transfer','E-Wallet') DEFAULT 'Transfer',
    proof_image    VARCHAR(255),
    CONSTRAINT fk_payments_rental
      FOREIGN KEY (rental_id) REFERENCES rentals(id)
      ON DELETE CASCADE
) ENGINE=InnoDB;

/* ======================================================
   7.  reviews
======================================================*/
CREATE TABLE reviews (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rental_id  INT UNSIGNED NOT NULL,
    rating     TINYINT     CHECK (rating BETWEEN 1 AND 5),
    comment    TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reviews_rental
      FOREIGN KEY (rental_id) REFERENCES rentals(id)
      ON DELETE CASCADE
) ENGINE=InnoDB;

/* ======================================================
   8.  settings
======================================================*/
CREATE TABLE settings (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key   VARCHAR(50) UNIQUE,
    setting_value TEXT
) ENGINE=InnoDB;

/* ======================================================
   9.  logs
======================================================*/
CREATE TABLE logs (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED,
    activity   TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
      ON DELETE SET NULL
) ENGINE=InnoDB;

/* ======================================================
  10. notifications
======================================================*/
CREATE TABLE notifications (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED,
    message    TEXT,
    is_read    BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
      ON DELETE CASCADE
) ENGINE=InnoDB;

/* Sample rentals untuk testing */
INSERT INTO rentals (user_id, vehicle_id, rent_date, return_date, total_price, status) VALUES
(2, 1, '2024-01-15', '2024-01-17', 1600000, 'Selesai'),
(2, 2, '2024-01-20', '2024-01-22', 1200000, 'Selesai'),
(3, 3, '2024-01-25', '2024-01-27', 1000000, 'Ongoing');
