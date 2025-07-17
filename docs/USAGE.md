# User Guide

## Overview

Sistem Rental Kendaraan adalah aplikasi web yang memungkinkan pengguna untuk menyewa kendaraan secara online. Sistem ini memiliki dua jenis pengguna utama: **Administrator** dan **Customer**.

## Getting Started

### Accessing the Application
1. Buka browser web
2. Akses URL aplikasi: `http://localhost/rental/src/` (untuk development)
3. Anda akan diarahkan ke halaman utama

### User Roles

#### Administrator
- Mengelola seluruh sistem
- Manajemen kendaraan
- Manajemen pengguna
- Monitoring penyewaan
- Laporan dan statistik

#### Customer
- Melihat katalog kendaraan
- Melakukan booking
- Mengelola profil
- Melihat riwayat penyewaan

## User Registration and Login

### Registration (Customer)
1. Klik tombol **"Daftar"** di halaman utama
2. Isi form registrasi:
   - **Username:** Nama pengguna unik
   - **Email:** Alamat email valid
   - **Password:** Minimal 6 karakter
   - **Confirm Password:** Konfirmasi password
   - **Full Name:** Nama lengkap
   - **Phone:** Nomor telepon
   - **Address:** Alamat lengkap
3. Klik **"Daftar"**
4. Jika berhasil, Anda akan diarahkan ke halaman login

### Login
1. Klik tombol **"Login"** di halaman utama
2. Masukkan **Username** dan **Password**
3. Klik **"Login"**
4. Sistem akan mengarahkan Anda ke dashboard sesuai role:
   - Admin → Admin Dashboard
   - Customer → User Dashboard

### Default Test Accounts

#### Admin Account
- **Username:** `admin`
- **Password:** `admin123`
- **Access:** Full system administration

#### Customer Accounts
**User 1:**
- **Username:** `user1`
- **Password:** `user123`
- **Name:** John Doe

**User 2:**
- **Username:** `user2`
- **Password:** `user123`
- **Name:** Jane Smith

## Customer Features

### 1. User Dashboard
Setelah login sebagai customer, Anda akan melihat:

#### Welcome Section
- Pesan selamat datang personal
- Ringkasan akun Anda

#### Statistics Cards
- **Total Rentals:** Jumlah total penyewaan
- **Active Rentals:** Penyewaan yang sedang aktif

#### Recent Rentals
- 5 penyewaan terbaru
- Status setiap penyewaan
- Tanggal dan detail kendaraan

#### Popular Vehicles
- Kendaraan yang paling sering disewa
- Quick booking button
- Rating dan harga

### 2. Vehicle Catalog
Akses melalui menu **"Kendaraan"**

#### Browsing Vehicles
- Lihat semua kendaraan yang tersedia
- Filter berdasarkan:
  - Jenis kendaraan
  - Status ketersediaan
  - Rentang harga
- Search berdasarkan nama atau brand

#### Vehicle Details
Setiap kendaraan menampilkan:
- **Foto kendaraan**
- **Nama dan brand**
- **Model dan tahun**
- **Nomor plat**
- **Harga sewa per hari**
- **Status ketersediaan**
- **Deskripsi**
- **Tombol "Sewa Sekarang"**

### 3. Booking Process

#### Step 1: Select Vehicle
1. Pilih kendaraan dari katalog
2. Klik **"Sewa Sekarang"**

#### Step 2: Booking Form
Isi form booking dengan:
- **Start Date:** Tanggal mulai sewa
- **End Date:** Tanggal selesai sewa
- **Notes:** Catatan tambahan (opsional)

#### Step 3: Confirmation
- Review detail booking
- Cek total hari dan biaya
- Klik **"Konfirmasi Booking"**

#### Step 4: Booking Status
Setelah booking berhasil:
- Status: **"Pending"** (menunggu konfirmasi admin)
- Anda akan menerima notifikasi
- Booking dapat dilihat di dashboard

### 4. Profile Management
Akses melalui dropdown profil → **"Profil Saya"**

#### View Profile
- Lihat informasi personal
- Username, email, nama lengkap
- Nomor telepon dan alamat
- Tanggal registrasi

#### Edit Profile
1. Klik **"Edit Profil"**
2. Update informasi yang diperlukan:
   - Email
   - Nama lengkap
   - Nomor telepon
   - Alamat
3. Klik **"Simpan Perubahan"**

#### Change Password
1. Klik **"Ubah Password"**
2. Masukkan:
   - Password lama
   - Password baru
   - Konfirmasi password baru
3. Klik **"Ubah Password"**

### 5. Rental History
Lihat di dashboard atau menu riwayat:

#### Rental Information
- **Vehicle Details:** Nama, brand, plat nomor
- **Rental Period:** Tanggal mulai dan selesai
- **Duration:** Total hari sewa
- **Total Cost:** Biaya total
- **Status:** Current status of rental
- **Booking Date:** Kapan booking dibuat

#### Rental Status
- **Pending:** Menunggu konfirmasi admin
- **Confirmed:** Dikonfirmasi, siap diambil
- **Active:** Sedang dalam masa sewa
- **Completed:** Sewa selesai
- **Cancelled:** Dibatalkan

## Administrator Features

### 1. Admin Dashboard
Akses setelah login sebagai admin:

#### System Statistics
- **Total Users:** Jumlah pengguna terdaftar
- **Total Vehicles:** Jumlah kendaraan
- **Total Rentals:** Jumlah penyewaan
- **Active Rentals:** Penyewaan aktif

#### Quick Actions
- Link cepat ke modul manajemen
- Statistik real-time
- Recent activities

### 2. User Management
Akses melalui **Admin → Users**

#### View Users
- Daftar semua pengguna
- Informasi: username, email, role, status
- Filter berdasarkan role atau status

#### Add New User
1. Klik **"Add New User"**
2. Isi form:
   - Username
   - Email
   - Password
   - Full name
   - Phone
   - Address
   - Role (Admin/Customer)
3. Klik **"Save"**

#### Edit User
1. Klik **"Edit"** pada user yang dipilih
2. Update informasi yang diperlukan
3. Klik **"Update"**

#### Delete User
1. Klik **"Delete"** pada user yang dipilih
2. Konfirmasi penghapusan
3. User akan dihapus dari sistem

### 3. Vehicle Management
Akses melalui **Admin → Vehicles**

#### View Vehicles
- Daftar semua kendaraan
- Informasi: nama, brand, plat, status, harga
- Filter berdasarkan status atau jenis

#### Add New Vehicle
1. Klik **"Add New Vehicle"**
2. Isi form:
   - Vehicle name
   - Brand
   - Model
   - Year
   - License plate
   - Vehicle type
   - Daily rate
   - Description
   - Upload image (opsional)
3. Klik **"Save"**

#### Edit Vehicle
1. Klik **"Edit"** pada kendaraan yang dipilih
2. Update informasi yang diperlukan
3. Klik **"Update"**

#### Vehicle Status Management
- **Available:** Tersedia untuk disewa
- **Rented:** Sedang disewa
- **Maintenance:** Dalam perawatan

### 4. Rental Management
Akses melalui **Admin → Rentals**

#### View All Rentals
- Daftar semua penyewaan
- Filter berdasarkan status atau tanggal
- Search berdasarkan user atau kendaraan

#### Rental Actions
**Confirm Booking:**
1. Pilih rental dengan status "Pending"
2. Klik **"Confirm"**
3. Status berubah menjadi "Confirmed"

**Start Rental:**
1. Pilih rental dengan status "Confirmed"
2. Klik **"Start"**
3. Status berubah menjadi "Active"

**Complete Rental:**
1. Pilih rental dengan status "Active"
2. Klik **"Complete"**
3. Status berubah menjadi "Completed"

**Cancel Rental:**
1. Pilih rental yang akan dibatalkan
2. Klik **"Cancel"**
3. Berikan alasan pembatalan
4. Status berubah menjadi "Cancelled"

### 5. Vehicle Types Management
Akses melalui **Admin → Vehicle Types**

#### Manage Categories
- Tambah kategori kendaraan baru
- Edit kategori yang ada
- Hapus kategori yang tidak digunakan

#### Default Categories
- Mobil
- Motor
- Truck
- Van

### 6. System Logs
Akses melalui **Admin → Logs**

#### Activity Monitoring
- Login/logout activities
- Booking activities
- System changes
- Error logs

#### Log Information
- **User:** Siapa yang melakukan aksi
- **Action:** Jenis aksi yang dilakukan
- **Timestamp:** Kapan aksi dilakukan
- **IP Address:** Alamat IP pengguna
- **Details:** Detail tambahan

### 7. Settings
Akses melalui **Admin → Settings**

#### System Configuration
- Site name and description
- Contact information
- Email settings
- Backup settings

#### Security Settings
- Password policies
- Session timeout
- Login attempt limits

## Navigation Guide

### Main Navigation (All Users)
- **Home:** Halaman utama
- **Vehicles:** Katalog kendaraan (untuk customer)
- **Login/Register:** Untuk guest users

### Customer Navigation
- **Dashboard:** User dashboard
- **Vehicles:** Katalog kendaraan
- **Profile Dropdown:**
  - Profil Saya
  - Logout

### Admin Navigation
- **Dashboard Admin:** Admin dashboard
- **Admin Dropdown:**
  - Users
  - Vehicles
  - Vehicle Types
  - Rentals
  - Logs
  - Settings
- **Profile Dropdown:**
  - Logout

## Best Practices

### For Customers

#### Booking Tips
1. **Plan Ahead:** Book kendaraan jauh-jauh hari
2. **Check Availability:** Pastikan kendaraan tersedia di tanggal yang diinginkan
3. **Read Description:** Baca deskripsi kendaraan dengan teliti
4. **Contact Admin:** Hubungi admin jika ada pertanyaan

#### Profile Management
1. **Keep Information Updated:** Selalu update informasi kontak
2. **Strong Password:** Gunakan password yang kuat
3. **Regular Check:** Cek dashboard secara berkala untuk update

### For Administrators

#### User Management
1. **Regular Monitoring:** Monitor aktivitas pengguna secara berkala
2. **Quick Response:** Respon cepat terhadap booking request
3. **Data Backup:** Lakukan backup data secara rutin

#### Vehicle Management
1. **Accurate Information:** Pastikan informasi kendaraan akurat
2. **Status Updates:** Update status kendaraan secara real-time
3. **Maintenance Schedule:** Jadwalkan perawatan kendaraan

#### Security
1. **Regular Password Change:** Ganti password admin secara berkala
2. **Monitor Logs:** Periksa log sistem untuk aktivitas mencurigakan
3. **User Verification:** Verifikasi identitas pengguna baru

## Troubleshooting

### Common Issues

#### Login Problems
**Problem:** Cannot login
**Solution:**
1. Check username and password
2. Ensure account is active
3. Clear browser cache
4. Contact administrator

#### Booking Issues
**Problem:** Cannot complete booking
**Solution:**
1. Check vehicle availability
2. Ensure dates are valid
3. Check if you're logged in
4. Try refreshing the page

#### Profile Update Failed
**Problem:** Cannot update profile
**Solution:**
1. Check required fields
2. Ensure email format is correct
3. Check password requirements
4. Try again later

### Error Messages

#### "Database Connection Failed"
- Check internet connection
- Contact system administrator
- Try refreshing the page

#### "Access Denied"
- Ensure you're logged in
- Check if you have proper permissions
- Contact administrator if needed

#### "Session Expired"
- Login again
- Check if cookies are enabled
- Clear browser cache

## Support and Contact

### Getting Help
1. **Check Documentation:** Review this user guide
2. **Contact Administrator:** Use contact form or email
3. **Report Issues:** Report bugs or problems
4. **Feature Requests:** Suggest new features

### System Maintenance
- **Scheduled Maintenance:** Usually announced in advance
- **Emergency Maintenance:** May occur without notice
- **Backup Schedule:** Daily automatic backups

### Updates and Changes
- **Feature Updates:** New features added regularly
- **Security Updates:** Applied automatically
- **User Notifications:** Important changes will be announced

---

**Note:** This user guide is regularly updated. Please check for the latest version periodically.