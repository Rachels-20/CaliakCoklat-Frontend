# Caliak Coklat Frontend

Frontend untuk **Caliak Coklat**, sebuah sistem monitoring perkebunan kakao berbasis Internet of Things (IoT). Aplikasi web ini dikembangkan sebagai antarmuka bagi pengguna untuk memantau kondisi lingkungan perkebunan, mengelola perangkat, melihat riwayat data sensor, serta memperoleh informasi dan rekomendasi berdasarkan hasil pengukuran yang dikirimkan oleh perangkat IoT

## Tentang Project

Caliak Coklat merupakan project **Project Based Learning (PBL)** yang bertujuan membantu petani memantau kondisi perkebunan kakao secara lebih mudah melalui teknologi Internet of Things (IoT).

Data dari berbagai sensor pada perangkat ESP32 dikirimkan secara **berkala** ke backend, kemudian ditampilkan melalui aplikasi web sehingga pengguna dapat memantau kondisi lahan, melihat riwayat pengukuran, serta memperoleh informasi yang mendukung pengambilan keputusan.


## Fitur

* Autentikasi pengguna
* Dashboard monitoring kondisi lahan
* Manajemen perangkat IoT
* Riwayat data sensor
* Laporan mingguan
* Sistem notifikasi
* Pengelolaan profil pengguna
* Halaman dokumentasi sistem

---

## Teknologi yang Digunakan

### Frontend

* PHP Native
* HTML5
* CSS3
* JavaScript

### Komunikasi Data

* REST API
* JSON

### Backend

Frontend ini terhubung dengan repository backend:

**CaliakCoklat-Backend**

---

## Struktur Project

```text
CaliakCoklat-Frontend
├── assets/
├── config/
├── private/
│   ├── assets/
│   ├── helper/
│   ├── js/
│   ├── pages/
│   └── proses/
├── contact.php
├── dokumentasi.php
├── index.php
└── style.css
```

## Halaman Utama

Aplikasi menyediakan beberapa halaman utama, di antaranya:

* Landing Page
* Dashboard Monitoring
* Manajemen Perangkat
* Riwayat Data Sensor
* Laporan Mingguan
* Profil Pengguna
* Halaman Dokumentasi

---

## Integrasi Sistem

Frontend berkomunikasi dengan backend menggunakan REST API untuk:

* Login dan autentikasi pengguna
* Manajemen perangkat
* Menampilkan data sensor
* Menampilkan data dashboard
* Menampilkan notifikasi
* Menampilkan laporan mingguan

## Kontributor

* Rachel Setiawan
* Tim Project Based Learning (PBL)



Project ini dikembangkan sebagai bagian dari **Project Based Learning (PBL)** di **Politeknik Negeri Padang** untuk tujuan pembelajaran dan pengembangan sistem monitoring perkebunan kakao berbasis IoT.
