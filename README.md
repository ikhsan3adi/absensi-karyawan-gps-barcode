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

## Cara Menjalankan

Aplikasi ini **tidak mewajibkan Docker**. Tersedia dua cara yang setara, pilih salah satu:

1. **Instalasi tradisional**: PHP, Composer, Node.js/Bun, dan MySQL dipasang langsung di mesin. Lihat bagian [Instalasi](#instalasi).
2. **Docker Compose**: seluruh stack berjalan di dalam container tanpa perlu tool apa pun di host. Lihat bagian [Menjalankan dengan Docker](#menjalankan-dengan-docker) dan [Menjalankan di Produksi (Docker)](#menjalankan-di-produksi-docker).

Perubahan kode aplikasi tidak membedakan kedua mode; file-file Docker (`Dockerfile`, folder `docker/`, `docker-compose*.yml`) hanya digunakan saat menjalankan lewat Docker.

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

## Menjalankan dengan Docker

Cara ini tidak membutuhkan PHP, Composer, Node.js/Bun, maupun MySQL di mesin host. Semuanya berjalan di dalam container.

### Prasyarat

- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/)

### Menjalankan

1. Clone/download repository ini

2. Siapkan file `.env`. Jika belum ada, salin dari contoh:

    ```sh
    cp .env.example .env
    ```

    Nilai `DB_HOST`, `DB_PORT`, `DB_PASSWORD`, dan `APP_URL` akan ditimpa oleh `environment` di [docker-compose.yml](./docker-compose.yml), jadi `.env` bawaan aman digunakan.

3. Build dan jalankan stack (aplikasi + MySQL):

    ```sh
    docker compose up -d
    ```

4. Tunggu sampai container `absensi-app` selesai inisialisasi. Pantau dengan:

    ```sh
    docker compose logs -f app
    ```

    Saat pertama kali dijalankan, container secara otomatis melakukan:

    - setup permission `storage/` dan `bootstrap/cache`
    - menunggu database siap
    - generate `APP_KEY` (jika belum ada)
    - `migrate`
    - seeder dasar (`DatabaseSeeder`) hanya saat database masih kosong dan `APP_ENV=local`

5. Aplikasi bisa diakses di [http://localhost:8000](http://localhost:8000), login dengan akun hasil seeder pada tabel di atas.

### Seeder Data Dummy (FakeDataSeeder) di Docker

Seeder otomatis hanya menjalankan `DatabaseSeeder`. Untuk mengisi data dummy absensi dan karyawan:

```sh
# reset total database, lalu isi data awal + data dummy
docker compose exec app php artisan migrate:fresh --force --seed --seeder=FakeDataSeeder

# atau isi data dummy tanpa mereset data yang sudah ada
docker compose exec app php artisan db:seed FakeDataSeeder --force
```

Setelah `docker compose down -v` (hapus volume), langkah `up` berikutnya akan otomatis migrate lagi dan menjalankan seeder dasar karena database kembali kosong.

### Tools Tambahan (Opsional)

phpMyAdmin dan Redis tersedia sebagai profile `tools` agar tidak ikut jalan secara default:

```sh
docker compose --profile tools up -d
```

- phpMyAdmin: [http://localhost:8080](http://localhost:8080)
- MySQL juga terekspos ke host di port `3306` (user `root`, password `secret`)
- Redis di port `6379` (belum dipakai aplikasi, disiapkan untuk scaling queue/cache)

### Perintah yang Sering Dipakai

```sh
# menjalankan artisan di dalam container
docker compose exec app php artisan <perintah>

# membuka tinker
docker compose exec app php artisan tinker

# melihat log aplikasi
docker compose logs -f app

# me-restart proses setelah mengubah kode PHP
docker compose restart app

# memberhentikan stack, data database tetap tersimpan
docker compose down

# memberhentikan stack sekaligus menghapus volume database/storage
docker compose down -v
```

Catatan:

- Kode sumber di-bind-mount ke container sehingga perubahan langsung terlihat. Namun OPcache dikonfigurasi tanpa revalidasi (`opcache.validate_timestamps=0` untuk performa produksi), jadi setelah mengedit file PHP jalankan `docker compose restart app`.
- Konfigurasi container dibaca dari `env_file` (`docker/dev.env` untuk lokal, `docker/prod.env` untuk produksi), bukan dari file `.env`. Variabel environment selalu menang atas `.env` yang mungkin ada di host.
- Perintah `docker compose exec` berjalan sebagai root di dalam container. File yang dibuat artisan (misal hasil export XLSX) bisa berpemilik root; perbaiki dengan `chown` bila diperlukan.

## Menjalankan di Produksi (Docker)

Mode produksi memakai overlay [docker-compose.prod.yml](./docker-compose.prod.yml) yang digabung dengan compose dasar. Kredensial tidak lagi berasal dari `docker/dev.env`, melainkan dari `docker/prod.env` yang tidak pernah di-commit (sudah masuk `.gitignore`).

### Persiapan

1. Pastikan Docker dan Docker Compose terpasang di server, lalu clone repository.

2. Buat file environment produksi:

    ```sh
    cp docker/prod.env.example docker/prod.env
    chmod 600 docker/prod.env
    nano docker/prod.env   # isi semua nilai CHANGE_ME
    ```

3. Generate `APP_KEY` sebelum start pertama, lalu tempel ke `docker/prod.env`:

    ```sh
    echo "APP_KEY=base64:$(openssl rand -base64 32)"
    ```

4. Isi kredensial database. Dua aturan penting:

    - `DB_PASSWORD` harus sama dengan `MYSQL_PASSWORD`
    - `MYSQL_ROOT_PASSWORD` wajib berbeda dari keduanya

    Pasangan user `MYSQL_USER`/`MYSQL_PASSWORD` hanya dibuat oleh image MySQL saat inisialisasi volume pertama kali. Jika volume sudah terlanjur ada, buat user secara manual atau hapus volume db sebelum start.

### Menjalankan

```sh
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build
```

Saat start, entrypoint secara otomatis:

- menunggu database siap lalu menjalankan `migrate`
- membangun ulang cache konfigurasi, route, dan view (`config:cache`, `route:cache`, `view:cache`)
- menyalakan nginx, php-fpm, queue worker, dan scheduler via supervisord

Seeder **tidak** dijalankan otomatis di mode produksi. Jalankan manual bila benar-benar dibutuhkan:

```sh
docker compose exec app php artisan db:seed FakeDataSeeder --force
```

### Verifikasi

```sh
# health check internal
docker compose exec app curl -f http://localhost:8000/up

# status keempat proses
docker compose exec app supervisorctl -c /etc/supervisor/conf.d/supervisord.conf status
```

### Reverse Proxy dan HTTPS

Secara default tidak ada port yang dipublish. Buka komentar pada blok `ports` di [docker-compose.prod.yml](./docker-compose.prod.yml) agar aplikasi tersedia di `127.0.0.1:8000` host, lalu pasang reverse proxy di depannya untuk terminasi SSL:

```nginx
server {
    listen 443 ssl;
    server_name absensi.perusahaan.co.id;

    ssl_certificate     /etc/letsencrypt/live/absensi.perusahaan.co.id/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/absensi.perusahaan.co.id/privkey.pem;

    location / {
        proxy_pass http://127.0.0.1:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

Header `X-Forwarded-Proto https` diperlukan agar Laravel menghasilkan URL https.

### Update Aplikasi

```sh
git pull
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build
```

Entrypoint akan migrate skema baru dan membangun ulang semua cache saat boot, jadi tidak ada langkah manual setelah deploy.

### Backup Database

```sh
docker compose exec db sh -c 'mysqldump -uroot -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"' | gzip > backup-$(date +%F).sql.gz
```

Restore:

```sh
gunzip < backup-2026-08-25.sql.gz | docker compose exec -T db sh -c 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"'
```

### Ringkasan Variabel Sensitif di `docker/prod.env`

| Variabel                         | Fungsi                                                                         |
| -------------------------------- | ------------------------------------------------------------------------------ |
| `APP_KEY`                        | Enkripsi session dan data terenkripsi; jangan diubah setelah produksi berjalan |
| `DB_PASSWORD` + `MYSQL_PASSWORD` | Password user aplikasi; kedua nilai harus identik                              |
| `MYSQL_ROOT_PASSWORD`            | Password root MySQL untuk backup/administrasi                                  |
| `MAIL_*`                         | Kredensial SMTP untuk email keluar                                             |
| `APP_URL` / `ASSET_URL`          | Domain publik; wajib benar agar asset dan link valid                           |

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
