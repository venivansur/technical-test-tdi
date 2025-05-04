# 📦 RESTful API dengan Laravel Lumen

Ini adalah proyek implementasi **RESTful API** sederhana menggunakan **Laravel Lumen** untuk mengelola data produk, pesanan, dan item pesanan. Dibuat sebagai jawaban untuk **Soal 1** pada tugas Fullstack Developer.

---

## ✨ Fitur yang Diimplementasikan

1. **Produk (Products)**
   - `GET /products` → Mengembalikan daftar semua produk
   - `POST /products` → Menambahkan produk baru
   - `PUT /products/{id}` → Mengupdate data produk berdasarkan ID
   - `DELETE /products/{id}` → Menghapus produk berdasarkan ID

2. **Pesanan (Orders)**
   - `GET /orders` → Mengembalikan daftar semua pesanan beserta rincian itemnya
   - `POST /orders` → Membuat pesanan baru, dengan validasi dan pengurangan stok produk yang sesuai

3. **Validasi**
   - Pada endpoint `POST /products`, validasi `name` minimal 3 karakter, `price` lebih besar dari 0, dan `stock` tidak boleh negatif.
   - Pada endpoint `POST /orders`, validasi setiap `product_id` harus valid dan `quantity` harus lebih besar dari 0. Jika stok produk tidak mencukupi, pesan kesalahan ditampilkan.

4. **Relasi Tabel**
   - Tabel `orders` memiliki relasi dengan tabel `products` melalui `order_items` sebagai relasi many-to-many.

---

## 🔧 Langkah Instalasi & Setup

Untuk menjalankan proyek ini, ikuti langkah-langkah berikut:

1. **Clone Repository & Masuk Folder Proyek**
   ```bash
   git clone https://github.com/username/lumen-restful-api.git
   cd lumen-restful-api


2. **Install Dependensi**

composer install


3. **Salin dan Konfigurasi File Environment**

cp .env.example .env

Lalu sesuaikan konfigurasi database Anda di file .env, contoh:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=orders_products_db
DB_USERNAME=root
DB_PASSWORD=

4.  **Jalankan Migrasi**

php artisan migrate


5. **Jalankan Server**

php -S localhost:8000 -t public


 ## 🧪  Cara Menggunakan API 


 1. **Produk**

GET /products
Mengembalikan daftar semua produk yang tersedia.


POST /products
Menambahkan produk baru dengan validasi.


POST /products
Content-Type: application/json

{
  "name": "Produk A",
  "price": 100000,
  "stock": 10
}


PUT /products/{id}
Mengupdate data produk berdasarkan ID.


PUT /products/{id}
Content-Type: application/json

{
  "name": "Produk A Updated",
  "price": 150000,
  "stock": 8
}

DELETE /products/{id}
Menghapus produk berdasarkan ID.


2. **Pesanan**
GET /orders
Mengembalikan daftar semua pesanan beserta rincian itemnya.


POST /orders
Membuat pesanan baru. Setiap produk harus valid dan quantity lebih dari 0.

Content-Type: application/json

{
  "customer_name": "John Doe",
  "order_date": "2024-11-30",
  "items": [
    {
      "product_id": 1,
      "quantity": 2
    },
    {
      "product_id": 2,
      "quantity": 1
    }
  ]
}

Jika stok produk tidak mencukupi, API akan mengembalikan pesan kesalahan seperti berikut:


{
  "error": "Insufficient stock for product ID 1"
}

## 📌 Struktur Database

**Tabel products**

id (integer, primary key, auto increment)

name (string)

price (decimal)

stock (integer)

**Tabel orders**

id (integer, primary key, auto increment)

customer_name (string)

order_date (datetime)

**Tabel order_items**

id (integer, primary key, auto increment)

order_id (foreign key to orders.id)

product_id (foreign key to products.id)

quantity (integer)

price (decimal, harga per unit produk pada saat order)