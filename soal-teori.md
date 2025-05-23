# Soal Teori


# Jawaban Soal No.1 #

**Perbedaan Utama antara Laravel dan Laravel Lumen**

Laravel adalah framework PHP yang lengkap dan lebih berat, dirancang untuk membangun aplikasi web skala besar dengan berbagai fitur seperti routing, ORM (Eloquent), middleware, dan banyak lainnya. Sedangkan Laravel Lumen adalah versi mikro (lebih ringan) dari Laravel, dirancang untuk membangun aplikasi API dengan performa tinggi.

Perbedaan utama:

Ukuran dan Performa: Lumen lebih ringan dan lebih cepat karena hanya membawa fitur dasar yang dibutuhkan untuk membangun aplikasi API. Laravel memiliki banyak fitur tambahan yang lebih cocok untuk aplikasi besar.

Fitur: Laravel menyediakan lebih banyak fitur seperti Eloquent ORM, Blade templating, dan banyak lagi, yang membuatnya lebih cocok untuk aplikasi berbasis web penuh. Sementara Lumen hanya menyediakan fitur dasar seperti routing, middleware, dan beberapa komponen Laravel lainnya.

Dalam konteks aplikasi yang ringan dan performa API, Lumen sangat cocok karena lebih cepat dan lebih efisien untuk aplikasi yang hanya berfokus pada pengelolaan API tanpa perlu semua fitur tambahan dari Laravel.




**Konsep Middleware di Laravel Lumen dan Contoh Penggunaannya**


Middleware di Laravel Lumen adalah lapisan yang menangani permintaan HTTP yang masuk sebelum mencapai route atau controller. Middleware sering digunakan untuk melakukan validasi, autentikasi, dan modifikasi terhadap request atau response.

Contoh penggunaan:

Autentikasi: Middleware dapat digunakan untuk memeriksa apakah request memiliki token autentikasi yang valid sebelum mengakses route tertentu.

Contoh kode middleware untuk autentikasi di Lumen:

php
Copy
Edit
// Membuat middleware untuk autentikasi
$router->middleware('auth', function () use ($router) {
    return $router->response()->json(['message' => 'Authenticated!']);
});

// Mendefinisikan middleware di kernel
$app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
]);





** Apa itu Dependency Injection dan Bagaimana Laravel Lumen Memanfaatkannya **

Dependency Injection (DI) adalah teknik desain yang memungkinkan objek atau dependensi diberikan (diinjeksi) ke dalam suatu objek, daripada objek tersebut harus membuat atau mengelola dependensinya sendiri. DI membantu membuat kode lebih modular dan dapat diuji.

Di Laravel Lumen, dependency injection dilakukan dengan menyuntikkan dependensi seperti service, repository, atau komponen lain langsung ke dalam controller atau route. Ini mengurangi kebutuhan untuk mengelola dependensi secara manual dan mempermudah pengujian dan pengelolaan aplikasi.

Contoh penggunaan DI di Lumen:

php
Copy
Edit
// Controller dengan dependency injection
class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function show($id)
    {
        return response()->json($this->userService->find($id));
    }
}
Pada contoh di atas, UserService disuntikkan secara otomatis oleh Lumen ke dalam controller UserController, memungkinkan untuk pemisahan tanggung jawab dan pengujian unit yang lebih baik.


# Jawaban Soal No.2 #


**Apa itu Reactive Data Binding di Vue.js dan bagaimana ini membantu dalam 
pengembangan front-end?**


Reactive data binding di Vue.js adalah fitur yang memungkinkan data di aplikasi Vue secara otomatis terhubung dengan tampilan (view). Ketika data berubah, tampilan akan diperbarui secara otomatis tanpa perlu menulis kode eksplisit untuk memperbarui DOM. Ini terjadi karena Vue.js menggunakan sistem reaktivitas yang memonitor perubahan pada data dan secara otomatis melakukan pembaruan pada elemen UI yang terkait.

Manfaat:

Meningkatkan produktivitas: Pengembang tidak perlu menulis kode untuk memperbarui tampilan secara manual. Cukup dengan mengubah data, Vue akan menangani pembaruan tampilan.

Kode lebih bersih: Data binding yang reaktif mengurangi jumlah kode yang perlu ditulis, karena interaksi antara data dan tampilan diatur secara otomatis.

Memudahkan debugging dan pemeliharaan: Reaktivitas Vue membuat aplikasi lebih mudah dipahami dan dikelola, karena data selalu terhubung langsung ke tampilan.



**Sebutkan dan Jelaskan Perbedaan Utama antara Computed Properties dan Methods di Vue.js**


Computed Properties dan Methods keduanya digunakan untuk menghasilkan nilai dinamis berdasarkan data, tetapi ada perbedaan penting dalam cara mereka bekerja:

Computed Properties:

Computed properties digunakan untuk menghitung nilai yang tergantung pada data reaktif. Nilai tersebut dihitung hanya ketika salah satu dependensinya berubah. Computed properties di-cache untuk kinerja yang lebih baik, sehingga jika data tidak berubah, nilai yang sudah dihitung sebelumnya akan digunakan kembali tanpa perlu dihitung ulang.

Biasanya digunakan untuk logika yang melibatkan perhitungan berdasarkan data yang ada.

Contoh:

js
Copy
Edit
computed: {
  fullName() {
    return `${this.firstName} ${this.lastName}`;
  }
}
Methods:

Methods digunakan untuk menjalankan fungsi ketika dipanggil. Setiap kali method dipanggil, kode di dalamnya dieksekusi ulang, tanpa caching. Ini berarti, meskipun data tidak berubah, metode tetap akan dieksekusi setiap kali dipanggil.

Biasanya digunakan untuk logika yang perlu dieksekusi setiap kali suatu aksi terjadi, seperti ketika pengguna mengklik tombol atau berinteraksi dengan elemen lainnya.

Contoh:

js
Copy
Edit
methods: {
  greet() {
    return `Hello, ${this.firstName}`;
  }
}


 **Apa Keuntungan Menggunakan Vuex dalam Aplikasi Vue.js?**

Vuex adalah state management pattern + library untuk aplikasi Vue.js yang digunakan untuk mengelola status atau data aplikasi secara terpusat.

Keuntungannya:

Centralized State Management: Vuex membantu mengelola state aplikasi di satu tempat terpusat, yang membuatnya lebih mudah untuk mengontrol dan memelihara data aplikasi, terutama dalam aplikasi Vue yang lebih besar.

Predictable State: Semua perubahan state terjadi melalui mutation yang dapat dilacak, sehingga memudahkan debugging dan pemahaman alur data dalam aplikasi.

Interoperability: Vuex memungkinkan pengelolaan state yang konsisten antara berbagai komponen, sehingga komponen-komponen dalam aplikasi bisa saling berbagi data dengan cara yang lebih efisien dan terstruktur.

Dengan Vuex, aplikasi Vue.js menjadi lebih mudah diorganisasi, terutama saat berhadapan dengan banyak komponen yang saling berinteraksi.


# Jawaban Soal No.3 #


**Apa itu Indexing di MySQL, dan bagaimana ini mempengaruhi performa query?**

Indexing di MySQL adalah teknik yang digunakan untuk meningkatkan kecepatan pencarian data dalam tabel. Index bekerja seperti daftar isi pada buku, yang memungkinkan database untuk menemukan data lebih cepat tanpa harus memindai seluruh tabel. Saat query dijalankan, MySQL akan menggunakan indeks untuk menemukan baris yang relevan dengan lebih efisien.

Pengaruh terhadap performa query:

Percepatan pencarian: Dengan indeks, MySQL dapat mencari data lebih cepat, terutama pada tabel besar, karena tidak perlu memeriksa setiap baris.

Pengurangan waktu eksekusi: Query yang menggunakan kolom yang diindeks (misalnya dengan WHERE, JOIN, atau ORDER BY) biasanya dieksekusi lebih cepat.

Namun, penurunan performa pada operasi insert/update/delete: Karena MySQL harus memperbarui indeks setiap kali data diubah, ini dapat memperlambat operasi yang melibatkan perubahan data.


**Apa Perbedaan antara INNER JOIN dan LEFT JOIN? Berikan contoh kasus penggunaan keduanya.**


INNER JOIN:

Mengembalikan hanya baris yang memiliki kecocokan di kedua tabel yang digabungkan.

Baris yang tidak memiliki kecocokan di kedua tabel akan diabaikan.

Contoh kasus penggunaan INNER JOIN:
Misalkan ada dua tabel, employees dan departments, dan kita ingin mendapatkan daftar karyawan yang memiliki departemen:

sql
Copy
Edit
SELECT employees.name, departments.name
FROM employees
INNER JOIN departments ON employees.department_id = departments.id;
Query ini hanya akan mengembalikan karyawan yang memiliki departemen yang cocok di tabel departments.

LEFT JOIN (atau LEFT OUTER JOIN):

Mengembalikan semua baris dari tabel kiri (tabel pertama), dan hanya baris yang cocok dari tabel kanan (tabel kedua). Jika tidak ada kecocokan di tabel kanan, hasilnya akan tetap mengembalikan baris dari tabel kiri dengan nilai NULL untuk kolom yang berasal dari tabel kanan.

Contoh kasus penggunaan LEFT JOIN:
Misalkan kita ingin mendapatkan daftar semua karyawan, bahkan jika mereka tidak memiliki departemen:

sql
Copy
Edit
SELECT employees.name, departments.name
FROM employees
LEFT JOIN departments ON employees.department_id = departments.id;
Query ini akan mengembalikan semua karyawan, dan untuk karyawan yang tidak memiliki departemen, kolom departemen akan berisi NULL.


**Apa Tujuan dari Normalisasi Database? Sebutkan Salah Satu Kelemahannya.**

Tujuan normalisasi adalah untuk mengorganisir data dalam database agar mengurangi redundansi (data yang berulang) dan meningkatkan konsistensi. Normalisasi memastikan bahwa setiap data disimpan hanya di satu tempat dan mengurangi kemungkinan terjadinya inkonsistensi dalam penyimpanan data.

Langkah-langkah normalisasi mencakup membagi tabel besar menjadi tabel-tabel yang lebih kecil dan menghubungkannya dengan relasi yang jelas. Beberapa bentuk normalisasi yang umum adalah First Normal Form (1NF), Second Normal Form (2NF), dan Third Normal Form (3NF).

Kelemahan normalisasi:

Performa query bisa menurun: Proses normalisasi yang memecah data ke dalam beberapa tabel bisa menyebabkan lebih banyak operasi JOIN yang diperlukan saat query dijalankan, sehingga memperlambat performa query dalam beberapa kasus, terutama jika jumlah tabel yang terlibat sangat banyak.