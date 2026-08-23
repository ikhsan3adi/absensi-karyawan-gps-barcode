# Aplikasi Web Absensi Karyawan QR Code GPS

<a href="https://github.com/ikhsan3adi/absensi-karyawan-gps-barcode/actions/workflows/laravel.yml">
    <img src="https://img.shields.io/github/actions/workflow/status/ikhsan3adi/absensi-karyawan-gps-barcode/laravel.yml?branch=master&style=for-the-badge&label=Continuous%20Integration&labelColor=%23934eb6&logo=github" alt="Continuous Integration">
</a>
<a href="https://github.com/ikhsan3adi/absensi-karyawan-gps-barcode/stargazers">
    <img src="https://img.shields.io/github/stars/ikhsan3adi/absensi-karyawan-gps-barcode?style=for-the-badge&labelColor=%23934eb6&color=%23ec73a9&logo=github" alt="GitHub Repo stars">
</a>
<a href="https://github.com/ikhsan3adi/absensi-karyawan-gps-barcode/graphs/contributors">
    <img src="https://img.shields.io/github/contributors-anon/ikhsan3adi/absensi-karyawan-gps-barcode?style=for-the-badge&labelColor=%23934eb6&color=%23ec73a9&logo=github" alt="GitHub Contributors">
</a>
<a href="https://github.com/ikhsan3adi/absensi-karyawan-gps-barcode/network/members">
    <img src="https://img.shields.io/github/forks/ikhsan3adi/absensi-karyawan-gps-barcode?style=for-the-badge&labelColor=%23934eb6&color=%23ec73a9&logo=github" alt="GitHub forks">
</a>
<a href="https://github.com/ikhsan3adi/absensi-karyawan-gps-barcode/watchers">
    <img src="https://img.shields.io/github/watchers/ikhsan3adi/absensi-karyawan-gps-barcode?style=for-the-badge&labelColor=%23934eb6&color=%23ec73a9&logo=github" alt="GitHub watchers">
</a>
<a href="#teknologi-yang-digunakan">
    <img src="https://img.shields.io/badge/12-%23FFF.svg?style=for-the-badge&label=Laravel&labelColor=%23934eb6&color=%23ec73a9&logo=laravel&logoColor=%23FFF" alt="Laravel">
</a>
<a href="#teknologi-yang-digunakan">
    <img src="https://img.shields.io/badge/8.3-%23FFF.svg?style=for-the-badge&label=PHP&labelColor=%23934eb6&color=%23ec73a9&logo=php&logoColor=%23FFF" alt="PHP">
</a>

| ![Aplikasi Web Absensi Karyawan QR Code GPS](./screenshots/absensi-gps-barcode-social-preview.png) |
| -------------------------------------------------------------------------------------------------- |

Aplikasi web absensi karyawan menggunakan QR Code dan GPS.

## Teknologi yang Digunakan

- [Laravel 11/12](https://laravel.com/)
- [Laravel Jetstream](https://jetstream.laravel.com/)
- [Endroid QR Code](https://github.com/endroid/qr-code)
- [Leaflet.js](https://leafletjs.com/)
- [OpenStreetMap](https://www.openstreetmap.org/)
- MySQL/MariaDB

## Instalasi

### Prasyarat

- [Composer](https://getcomposer.org)
- [NPM & Node.js](https://nodejs.org) atau [Bun](https://bun.com/)
- PHP 8.3+
- MySQL/MariaDB/SQlite

---

1. Clone/download repository ini

2. Buat database (jika tidak menggunakan SQLite)

    ```sql
    -- nama database sesuaikan dengan yang ada di .env
    CREATE DATABASE db_absensi_karyawan;
    ```

3. Jalankan perintah

    ```sh
    # untuk membuat file `.env`
    composer run-script post-root-package-install # atau cp .env.example .env

    # untuk instalasi dependency php
    composer install

    # untuk instalasi dependency javascript
    npm install # atau bun install

    # untuk membuat key aplikasi
    php artisan key:generate --ansi --force

    # untuk menghubungkan storage ke public
    php artisan storage:link

    # untuk membuat tabel database [BUAT DATABASE DAHULU]
    php artisan migrate

    # untuk membuat file css dan javascript yang diperlukan
    npm run build # atau bun run build
    ```

    Menjalankan aplikasi (local)

    ```sh
    php artisan serve
    ```

### Seeder

Pilih salah satu opsi berikut:

1. Jalankan perintah berikut untuk menyiapkan data awal

    ```sh
    php artisan db:seed DatabaseSeeder
    ```

2. (Recommended untuk development) Jalankan perintah berikut untuk menyiapkan data awal beserta data dummy (absensi & karyawan)

    ```sh
    php artisan db:seed FakeDataSeeder
    ```

Akun default hasil seeder:

| Role        | Email                    | Password     |
| ----------- | ------------------------ | ------------ |
| Super Admin | `superadmin@example.com` | `superadmin` |
| Admin       | `admin@example.com`      | `admin`      |

## Fitur & Pratinjau

### User/Karyawan

| Scan Page                                | Scan Page (Mobile)                                     |
| ---------------------------------------- | ------------------------------------------------------ |
| ![Scan](./screenshots/presensi-scan.png) | ![Scan mobile](./screenshots/presensi-scan-mobile.png) |

| Riwayat Absensi Karyawan                             | Pengajuan Izin                                      | Riwayat Pengajuan Izin                                              |
| ---------------------------------------------------- | --------------------------------------------------- | ------------------------------------------------------------------- |
| ![Riwayat Absensi](./screenshots/presensi-user.jpeg) | ![Pengajuan Izin](./screenshots/pengajuan-izin.png) | ![Riwayat Pengajuan Izin](./screenshots/riwayat-pengajuan-izin.png) |

### Admin & Superadmin

| Dashboard Admin                                  | Dashboard Admin Dark                                 |
| ------------------------------------------------ | ---------------------------------------------------- |
| ![Dashboard](./screenshots/dashboard-light.jpeg) | ![Dashboard Dark](./screenshots/dashboard-dark.jpeg) |

| Barcode                                | Create/Edit Barcode                                            |
| -------------------------------------- | -------------------------------------------------------------- |
| ![Barcode](./screenshots/barcode.jpeg) | ![Create Edit Barcode](./screenshots/create-edit-barcode.jpeg) |

| Absensi Karyawan                                    |                                                         |                                                       |
| --------------------------------------------------- | ------------------------------------------------------- | ----------------------------------------------------- |
| Absensi per hari                                    | Absensi per minggu                                      | Absensi per bulan                                     |
| ![Absensi per hari](./screenshots/absensi-hari.png) | ![Absensi per minggu](./screenshots/absensi-minggu.png) | ![Absensi per bulan](./screenshots/absensi-bulan.png) |

| Data Karyawan                                 | Create/Edit Data Karyawan                                            |
| --------------------------------------------- | -------------------------------------------------------------------- |
| ![Data Karyawan](./screenshots/karyawan.jpeg) | ![Create Edit Data Karyawan](./screenshots/create-edit-karyawan.png) |

| Export/Import from/to XLSX                                       |                                                                                   |
| ---------------------------------------------------------------- | --------------------------------------------------------------------------------- |
| Export/Import Data Karyawan & User                               | Export/Import Data Karyawan & User + Preview Data                                 |
| ![Export/Import Data Karyawan](./screenshots/export-user.jpeg)   | ![Export/Import Data Karyawan + Preview](./screenshots/export-user-preview.jpeg)  |
| Export/Import Data Absensi & User                                | Export/Import Data Absensi & User + Preview Data                                  |
| ![Export/Import Data Absensi](./screenshots/export-absensi.jpeg) | ![Export/Import Data Absensi + Preview](./screenshots/export-absensi-preview.png) |

| Pengajuan Izin (Admin)                                             | Detail Pengajuan Izin                                             |
| ------------------------------------------------------------------ | ----------------------------------------------------------------- |
| ![Pengajuan Izin (Admin)](./screenshots/daftar-pengajuan-izin.png) | ![Detail Pengajuan Izin](./screenshots/detail-pengajuan-izin.png) |

### Device Restriction

Fitur pembatasan login perangkat untuk mencegah titip absen. Device token (UUID) otomatis digenerate dan disimpan di localStorage browser saat login.

- Login dari perangkat berbeda ditolak
- Admin dapat mereset perangkat dari menu Employee
- Admin & superadmin tidak terpengaruh
- Dapat dinonaktifkan via .env:

    ```env
    DEVICE_RESTRICTION_ENABLED=false
    ```

### Status Incomplete

Karyawan yang absen masuk tapi pulang sebelum jam shift berakhir akan mendapatkan status 'Tidak Tuntas' (incomplete), bukan 'Hadir'. Deteksi otomatis saat scan keluar.

### Alur Pengajuan Izin

Pengajuan izin tidak langsung disetujui, melainkan melalui proses approval:

1. Karyawan mengajukan izin/sakit via form
2. Status pengajuan: **Menunggu** (pending)
3. Admin menyetujui atau menolak pengajuan
4. Jika disetujui, data absensi otomatis terisi untuk rentang tanggal tersebut
5. Karyawan dapat melihat status pengajuannya di halaman Riwayat Izin

## Donasi ❤

[![Donate trakteer](https://img.shields.io/badge/Donate-Trakteer-red?style=for-the-badge&link=https%3A%2F%2Ftrakteer.id%2Fikhsan3adi%2Ftip)](https://trakteer.id/ikhsan3adi/tip)
[![Donate saweria](https://img.shields.io/badge/Donate-Saweria-red?style=for-the-badge&link=https%3A%2F%2Fsaweria.co%2Fxiboxann)](https://saweria.co/xiboxann)

Atau, beri star...⭐⭐⭐⭐

<!-- ![Aplikasi Web Absensi Karyawan QR Code GPS](./screenshots/hero.png) -->
