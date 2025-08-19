# Project Magang Bank DKI

Aplikasi berbasis **Laravel** untuk kebutuhan magang di Bank DKI.  
Fokus pada manajemen file dan folder, dashboard, serta sistem autentikasi pengguna.

---

## ✨ Fitur Utama
- Autentikasi (login/registrasi, verifikasi email, ubah password)
- Dashboard
- Manajemen **File** (upload, edit, hapus, kategori, folder)
- Manajemen **Folder** dan **Category**
- Seeder contoh data (cek `database/seeders`)

---

## 📋 Prasyarat
Pastikan sudah terpasang di komputer kamu:
- PHP 8.1+ dan Composer
- Node.js 18+ dan npm
- MySQL/MariaDB
- Git

Cek cepat:
```bash
php -v
composer -V
node -v
npm -v
mysql --version
```

---

## 🚀 Instalasi Lokal

### 1) Clone repository
```bash
git clone https://github.com/tsabitaamlh/project-magang-bankdki.git
cd project-magang-bankdki
```

### 2) Install dependency backend
```bash
composer install
```

### 3) Salin file .env & generate key aplikasi
**Windows (PowerShell/CMD):**
```bash
copy .env.example .env
php artisan key:generate
```

**macOS/Linux:**
```bash
cp .env.example .env
php artisan key:generate
```

### 4) Edit konfigurasi database di file .env
```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_kamu
DB_USERNAME=user_database_kamu
DB_PASSWORD=password_database_kamu
```

### 5) Jalankan migrasi database
```bash
php artisan migrate --seed
```

### 6) Jalankan storage link lokal
```bash
php artisan storage:link
```

### 7) Install dependency frontend & jalankan Vite
```bash
npm install
npm run dev
```

### 8) Jalankan server lokal
```bash
php artisan serve
```

Akses aplikasi di: [http://127.0.0.1:8000](http://127.0.0.1:8000)
