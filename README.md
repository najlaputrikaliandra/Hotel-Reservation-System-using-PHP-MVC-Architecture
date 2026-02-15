<h1>🏨 Hotel Reservation System – PHP MVC Web Application</h1>

<hr>

<h2>👤 Identitas</h2>

<ul>
  <li><b>Nama:</b> Najla Putri Kaliandra Sabilillah</li>
  <li><b>Project Type:</b> Fullstack Web Application</li>
  <li><b>Architecture:</b> MVC (Model-View-Controller)</li>
  <li>Ini sengaja nih ga saya kasih <b>DATABASE nya</b>, karna jantung project ini ada di <b>DATABASE</b>. kalau mau <b>DATABASE</b> DM saya di LinkedIn biar dapat akses full</li>
</ul>

<hr>

<h2>📌 Project Overview</h2>

<p>
Hotel Reservation System adalah aplikasi web berbasis PHP yang dirancang untuk
mengelola proses reservasi hotel secara online.
</p>

<p>
Project ini dibangun menggunakan arsitektur <b>MVC (Model-View-Controller)</b>
dengan pendekatan OOP untuk memisahkan logika bisnis, tampilan, dan pengelolaan data.
</p>

<hr>

<h2>🧱 System Architecture</h2>

<p>Struktur project mengikuti pola MVC:</p>

<pre>
├── config/
├── controllers/
├── models/
├── views/
├── assets/
└── index.php
</pre>

<ul>
  <li><b>Model:</b> Mengelola interaksi database</li>
  <li><b>View:</b> Menampilkan halaman ke user</li>
  <li><b>Controller:</b> Mengatur alur logika aplikasi</li>
</ul>

<hr>

<h2>🔐 Role-Based Access</h2>

<p>Sistem memiliki dua role utama:</p>

<ul>
  <li>👤 <b>Pelanggan</b></li>
  <li>🛠 <b>Admin</b></li>
</ul>

<p>
Setiap role memiliki dashboard dan hak akses berbeda.
</p>

<hr>

<h2>🚀 Fitur Utama</h2>

<h3>👤 Pelanggan</h3>
<ul>
  <li>Melihat daftar kamar</li>
  <li>Melakukan reservasi kamar</li>
  <li>Melakukan pembayaran</li>
  <li>Melihat status reservasi</li>
</ul>

<h3>🛠 Admin</h3>
<ul>
  <li>Manajemen data kamar (CRUD)</li>
  <li>Manajemen fasilitas</li>
  <li>Verifikasi pembayaran</li>
  <li>Update status reservasi</li>
  <li>Dashboard monitoring</li>
</ul>

<hr>

<h2>🗄 Database Integration</h2>

<p>
Sistem terhubung dengan database melalui konfigurasi:
</p>

<pre>
config/database.php
models/Database.php
</pre>

<p>
Menggunakan pendekatan OOP untuk pengelolaan koneksi dan query database.
</p>

<hr>

<h2>🎨 Frontend</h2>

<ul>
  <li>HTML</li>
  <li>CSS</li>
  <li>JavaScript</li>
</ul>

<p>
Frontend bertanggung jawab untuk tampilan antarmuka,
validasi input, serta interaksi pengguna.
</p>

<hr>

<h2>⚙️ Backend</h2>

<ul>
  <li>PHP Native (OOP)</li>
  <li>MVC Architecture</li>
  <li>Controller-based routing</li>
</ul>

<p>
Backend menangani autentikasi, proses reservasi,
validasi pembayaran, serta pengelolaan data hotel.
</p>

<hr>

<h2>📁 Struktur Project</h2>

<pre>
hotel_reservation/
│
├── config/
├── controllers/
├── models/
├── views/
├── assets/
├── proses/
└── index.php
</pre>

<hr>

<h2>🛠 Tech Stack</h2>

<ul>
  <li>🐘 PHP (OOP)</li>
  <li>🗄 MySQL (Database)</li>
  <li>🎨 HTML, CSS</li>
  <li>⚡ JavaScript</li>
</ul>

<hr>

<h2>🎯 Project Highlights</h2>

<ul>
  <li>✔ MVC Architecture Implementation</li>
  <li>✔ Role-Based Authentication</li>
  <li>✔ Full CRUD Operations</li>
  <li>✔ Payment Verification Flow</li>
  <li>✔ Modular & Structured Code</li>
</ul>
