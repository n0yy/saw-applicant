# Sistem Aplikasi Pelamar Kerja dengan Metode SAW

## Ringkasan Proyek

SAW Applicant System adalah aplikasi web yang dirancang untuk mengelola dan menyederhanakan proses rekrutmen kerja. Aplikasi ini menggunakan metode **Simple Additive Weighting (SAW)** untuk melakukan perangkingan pelamar secara objektif berdasarkan kriteria yang telah ditentukan.

Sistem ini memiliki dua peran utama: **Admin** sebagai pengelola lowongan dan seleksi, serta **Pelamar** sebagai pengguna yang mencari dan melamar pekerjaan. Dibuat dengan PHP native dan MySQL, serta tampilan antarmuka yang modern dan responsif menggunakan Bootstrap 5.

---

## Fitur Utama

### 👤 Fitur untuk Admin

- **Dashboard Admin**: Menampilkan statistik utama seperti total pelamar, approval yang tertunda, dan jumlah lowongan.
- **Approval Pelamar**: Admin dapat menyetujui (approve) akun pelamar baru sebelum mereka dapat melamar pekerjaan.
- **Manajemen Lowongan**:
  - Menambah lowongan baru dengan judul, deskripsi, dan persyaratan.
  - Menyertakan materi training (5-10 gambar beserta deskripsi) untuk setiap lowongan yang akan diakses oleh pelamar yang diterima.
- **Manajemen Pelamar**:
  - Melihat daftar pelamar untuk setiap lowongan.
  - Mengunduh CV pelamar.
- **Seleksi SAW**:
  - Menjalankan proses perhitungan SAW secara otomatis untuk merangking pelamar berdasarkan kriteria.
  - Memilih pemenang dari hasil perangkingan SAW.
- **Lihat Materi Training**: Admin dapat melihat materi training yang telah di-upload untuk pemenang yang terpilih.

### 👨‍💼 Fitur untuk Pelamar

- **Registrasi & Login**: Sistem otentikasi yang aman untuk pelamar. Akun baru memerlukan persetujuan admin.
- **Dashboard Pelamar**:
  - Menampilkan statistik lamaran (Total, Menunggu, Diterima, Ditolak).
  - Menampilkan daftar lowongan yang tersedia.
  - Menampilkan notifikasi jika lamaran diterima.
- **Lamar Pekerjaan**:
  - Mengisi form lamaran yang mencakup pengalaman kerja, tingkat pendidikan, dan motivasi.
  - Mengunggah CV dalam format PDF.
- **Materi Training**:
  - Jika diterima, pelamar akan mendapatkan akses ke halaman training.
  - Materi training disajikan dalam bentuk slideshow yang interaktif dengan progress bar dan navigasi keyboard.

---

## Alur Sistem

### 1. Alur Pelamar (Applicant Flow)

1.  **Registrasi**: Pelamar membuat akun baru.
2.  **Menunggu Approval**: Akun pelamar harus disetujui oleh admin terlebih dahulu.
3.  **Login**: Setelah akun disetujui, pelamar dapat login.
4.  **Mencari & Melamar**: Pelamar melihat daftar lowongan yang tersedia dan mengisi form lamaran untuk pekerjaan yang diminati.
5.  **Menunggu Hasil**: Lamaran akan direview dan dinilai oleh admin menggunakan metode SAW.
6.  **Mulai Training**: Jika diterima, pelamar akan melihat notifikasi di dashboard dan mendapatkan akses untuk memulai training online melalui slideshow.

### 2. Alur Admin (Admin Flow)

1.  **Login**: Admin masuk ke dalam sistem.
2.  **Approval Akun**: Admin melihat daftar pelamar yang baru mendaftar dan menyetujui akun mereka.
3.  **Membuat Lowongan**: Admin membuat lowongan pekerjaan baru, lengkap dengan deskripsi, persyaratan, dan materi training.
4.  **Melihat Pelamar**: Admin memantau daftar pelamar yang masuk untuk setiap lowongan.
5.  **Proses Seleksi SAW**: Setelah periode lamaran ditutup, admin menjalankan fitur seleksi SAW. Sistem akan secara otomatis menghitung skor dan merangking semua pelamar.
6.  **Memilih Pemenang**: Berdasarkan hasil ranking SAW, admin memilih satu kandidat sebagai pemenang.
7.  **Selesai**: Pemenang yang terpilih akan otomatis mendapatkan akses ke materi training di dashboard mereka.

---

## Implementasi Metode SAW

Metode Simple Additive Weighting (SAW) diimplementasikan dalam `controllers/SAWController.php` untuk merangking kandidat.

### Kriteria yang Digunakan

1.  **Pengalaman Kerja (`experience_years`)** - (C1): Jumlah pengalaman kerja dalam tahun. Tipe: _Benefit_ (semakin tinggi semakin baik).
2.  **Tingkat Pendidikan (`education_level`)** - (C2): Jenjang pendidikan terakhir. Tipe: _Benefit_ (semakin tinggi semakin baik).

### Bobot Kriteria

Bobot telah ditentukan di dalam controller dan dapat dikonfigurasi sesuai kebutuhan:

- **W1 (Pengalaman)**: `60%` (0.6)
- **W2 (Pendidikan)**: `40%` (0.4)

### Proses Perhitungan

1.  **Konversi Kualitatif ke Kuantitatif**:
    Tingkat pendidikan dikonversi menjadi skor numerik:

    - SMA/SMK: **1**
    - Diploma: **2**
    - Sarjana (S1): **3**
    - Magister (S2): **4**
    - Doktor (S3): **5**

2.  **Normalisasi Matriks**:
    Karena kedua kriteria bersifat _benefit_, rumus normalisasi yang digunakan adalah:
    \[ R*{ij} = \frac{X*{ij}}{\max(X\_{ij})} \]
    Di mana:

    - `R_ij` adalah nilai normalisasi.
    - `X_ij` adalah nilai kriteria `j` dari pelamar `i`.
    - `max(X_ij)` adalah nilai terbesar dari kriteria `j` di antara semua pelamar.

3.  **Perhitungan Skor Akhir**:
    Skor akhir untuk setiap pelamar dihitung dengan rumus:
    \[ V*i = \sum*{j=1}^{n} (W*j \times R*{ij}) \]
    Atau secara spesifik:
    \[ V*i = (W_1 \times R*{i1}) + (W*2 \times R*{i2}) \]
    Pelamar dengan nilai `V_i` tertinggi akan menempati ranking pertama.

---

## Struktur Database

Aplikasi ini menggunakan 4 tabel utama:

- `users`: Menyimpan data pengguna (admin dan pelamar), termasuk status approval.
- `jobs`: Menyimpan detail lowongan pekerjaan, termasuk deskripsi dan persyaratan.
- `applications`: Menyimpan data lamaran yang diajukan oleh pelamar, termasuk nilai kriteria dan status lamaran (diterima, ditolak, dll).
- `training`: Menyimpan path gambar dan deskripsi untuk materi training yang terkait dengan setiap lowongan.

---

## Teknologi yang Digunakan

- **Backend**: PHP 8.x (Native)
- **Frontend**: HTML, CSS, JavaScript, Bootstrap 5
- **Database**: MySQL (dikelola melalui phpMyAdmin)
- **Web Server**: Apache (via XAMPP)
- **Icons**: Font Awesome

---

## Panduan Instalasi

1.  **Clone Repository**
    ```bash
    git clone https://github.com/username/saw-applicant-system.git
    cd saw-applicant-system
    ```
2.  **Setup Web Server**:

    - Pindahkan folder proyek ke dalam direktori `htdocs` (untuk XAMPP) atau `www` (untuk WAMP).

3.  **Database**:

    - Buka **phpMyAdmin** dan buat database baru dengan nama `saw_applicant`.
    - Import file `saw_applicant.sql` (jika tersedia) atau buat tabel secara manual sesuai dengan struktur yang dijelaskan di atas.
    - Konfigurasi koneksi database di file `config/db.php`.
      ```php
      $servername = "localhost";
      $username = "root";
      $password = "";
      $dbname = "saw_applicant";
      ```

4.  **Jalankan Aplikasi**:
    - Buka browser dan akses `http://localhost/saw-applicant-system`.

---

## Akun Demo

Anda dapat menggunakan akun berikut untuk menguji sistem:

- **Admin**:
  - Email: `admin@example.com`
  - Password: `password`
- **Pelamar**:
  - Email: `pelamar@example.com`
  - Password: `password`
    _(Catatan: Akun pelamar mungkin perlu di-approve oleh admin terlebih dahulu setelah registrasi)_
