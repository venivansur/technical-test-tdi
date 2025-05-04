Sistem Autentikasi JWT dengan Lumen 🔒
📋 Tugas Project
Implementasi sistem autentikasi sederhana pada API menggunakan Laravel Lumen dengan JWT untuk mengelola sesi pengguna.

🛠️ Langkah-langkah Instalasi
1. Clone Repository
bash
git clone https://github.com/username/repository.git
cd repository
2. Install Dependensi
bash
composer install
3. Konfigurasi Environment
bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
4. Pengaturan Database
Buat database MySQL baru

Konfigurasi file .env:

ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=username_db
DB_PASSWORD=password_db
JWT_SECRET=secret_key_dari_artisan
Jalankan migrasi database:

bash
php artisan migrate
5. Menjalankan Server Backend
bash
php -S localhost:8000 -t public
Server akan berjalan di http://localhost:8000

🌐 Endpoint API
Berikut endpoint yang tersedia:

Registrasi Pengguna
POST /api/register
Body:

json
{
  "name": "Nama User",
  "email": "user@example.com",
  "password": "password123"
}
Login
POST /api/login
Body:

json
{
  "email": "user@example.com",
  "password": "password123"
}
Response akan mengembalikan token JWT.

Get Profile (Protected)
GET /api/profile
Header:

Authorization: Bearer [token_jwt]
🧪 Testing API
Anda dapat menguji API menggunakan:

Dengan cURL
bash
# Registrasi
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test@example.com","password":"password123"}'

# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password123"}'

# Profile
curl -X GET http://localhost:8000/api/profile \
  -H "Authorization: Bearer [token_dari_login]"
Dengan Postman
Import collection Postman yang disediakan

Atur environment variables:

base_url: http://localhost:8000

token: Isi setelah login berhasil

🚨 Troubleshooting
Jika menemui masalah:

Pastikan semua dependensi terinstall:

bash
composer install
Clear cache:

bash
php artisan cache:clear
Periksa error log:

bash
tail -f storage/logs/lumen.log
📜 Lisensi
Proyek ini menggunakan lisensi MIT.