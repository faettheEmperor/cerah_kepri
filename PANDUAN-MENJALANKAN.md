# Panduan Menjalankan Cerah Tanjungpinang

Panduan ini untuk menjalankan website secara lokal di Windows/XAMPP. Website lokal hanya dapat dibuka di komputer sendiri kecuali server dikonfigurasi khusus untuk akses jaringan atau internet.

## Yang perlu aktif

- PostgreSQL 18 dengan database `cerah_weather`.
- PHP XAMPP dari `C:\xampp\php\php.exe`. Di komputer ini perintah `php` biasa menunjuk ke `C:\php\php.exe`, yang tidak memiliki driver PostgreSQL, jadi gunakan path XAMPP secara eksplisit.
- Dua proses terminal saat pengembangan: satu untuk website dan satu untuk jadwal otomatis.

## Menjalankan website dan pembaruan otomatis

### 1. Pastikan PostgreSQL hidup

Tekan `Win + R`, ketik `services.msc`, lalu tekan Enter. Cari layanan **postgresql-x64-18** dan pastikan statusnya **Running/Berjalan**. Jika berhenti, klik kanan layanan itu lalu pilih **Start/Mulai**.

Database `cerah_weather`, migrasi tabel, dan 23 wilayah sudah dibuat. Jadi langkah `migrate --seed` hanya diperlukan saat menyiapkan database baru atau setelah database dihapus, bukan setiap kali membuka website.

### 2. Buka terminal proyek

Buka PowerShell, lalu jalankan:

```powershell
cd C:\xampp\htdocs\cerahv1
```

### 3. Jalankan website

Di terminal pertama, jalankan:

```powershell
& "C:\xampp\php\php.exe" artisan serve --host=127.0.0.1 --port=8000
```

Biarkan terminal ini terbuka. Buka website di browser:

```text
http://127.0.0.1:8000
```

### 4. Jalankan jadwal otomatis

Buka PowerShell kedua, masuk ke folder proyek yang sama, lalu jalankan:

```powershell
cd C:\xampp\htdocs\cerahv1
& "C:\xampp\php\php.exe" artisan schedule:work
```

Biarkan terminal ini terbuka juga. Sinkronisasi Open-Meteo dan pembersihan data terjadwal berjalan setiap pergantian jam. Pesan `No scheduled commands are ready to run` berarti jadwal berikutnya belum jatuh tempo; itu normal.

### 5. Ambil data saat itu juga (opsional)

Jadwal otomatis menunggu sampai waktu berikutnya. Untuk menarik data terbaru tanpa menunggu, buka PowerShell ketiga di folder proyek dan jalankan:

```powershell
cd C:\xampp\htdocs\cerahv1
& "C:\xampp\php\php.exe" artisan weather:sync
```

Perintah ini mengambil cuaca untuk kota, kecamatan, dan kelurahan serta kondisi laut untuk titik Kota Tanjungpinang.

## Memastikan data masuk

- Buka `http://127.0.0.1:8000` untuk halaman website.
- API daftar wilayah: `http://127.0.0.1:8000/api/weather/regions`
- API cuaca Kota Tanjungpinang: `http://127.0.0.1:8000/api/weather/regions/21.72`
- API cuaca Dompak: `http://127.0.0.1:8000/api/weather/regions/21.72.04.1002`
- Pada halaman, gunakan tombol panah melingkar untuk memuat ulang data. Tampilan juga memeriksa data baru setiap lima menit; ini hanya memuat ulang tampilan, sedangkan pengambilan data dari Open-Meteo tetap mengikuti jadwal.

Riwayat snapshot disimpan 24 jam. Prakiraan terbaru berisi 168 jam (7 hari). Kondisi laut hanya ditampilkan pada pilihan Kota Tanjungpinang, bukan kecamatan atau kelurahan.

## Mengaktifkan otomatisasi saat komputer dinyalakan

Selama pengembangan, membiarkan `schedule:work` terbuka adalah cara paling sederhana. Jika ingin scheduler hidup otomatis setelah masuk Windows:

1. Buka **Task Scheduler** dari menu Start.
2. Pilih **Create Task…** (bukan *Create Basic Task*).
3. Tab **General**: beri nama `Cerah Weather Scheduler`; pilih **Run only when user is logged on**.
4. Tab **Triggers** → **New…**: pilih **At log on**, lalu centang **Repeat task every: 1 minute** dan set durasi **Indefinitely**.
5. Tab **Actions** → **New…**:
   - **Program/script**: `C:\xampp\php\php.exe`
   - **Add arguments**: `artisan schedule:run`
   - **Start in**: `C:\xampp\htdocs\cerahv1`
6. Tab **Conditions**: jika laptop, matikan pilihan yang hanya menjalankan tugas saat tersambung ke listrik bila kamu ingin scheduler tetap aktif saat memakai baterai.
7. Simpan tugas, lalu klik kanan tugas itu dan pilih **Run** untuk mulai sekarang.

Task Scheduler akan memanggil Laravel setiap menit. Laravel sendiri hanya menjalankan pengambilan cuaca ketika jadwal per jamnya tiba. Gunakan cara ini sebagai pengganti terminal `schedule:work`, jangan jalankan dua scheduler bersamaan.

## Menghentikan proses

- Untuk menghentikan website atau `schedule:work` di terminal, fokuskan terminal terkait lalu tekan `Ctrl + C`.
- Untuk tugas Windows terjadwal, buka Task Scheduler, cari `Cerah Weather Scheduler`, lalu pilih **Disable** atau **End**.

## Jika muncul error

- **`could not find driver`**: pastikan menjalankan PHP XAMPP dengan path lengkap `C:\xampp\php\php.exe`, bukan perintah `php` biasa.
- **Koneksi PostgreSQL ditolak**: pastikan layanan `postgresql-x64-18` berjalan dan nilai `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, serta `DB_PASSWORD` pada `.env` benar. Jangan membagikan isi password.
- **Belum ada data cuaca**: jalankan `C:\xampp\php\php.exe artisan weather:sync` secara manual dan periksa pesan hasilnya.
- **Gagal terhubung ke Open-Meteo**: periksa koneksi internet dan coba lagi.
- **Port 8000 sudah dipakai**: gunakan port lain, misalnya `--port=8010`, lalu buka `http://127.0.0.1:8010`.

## Menyiapkan database dari awal

Untuk database yang benar-benar baru, setelah mengisi koneksi `.env`, jalankan:

```powershell
cd C:\xampp\htdocs\cerahv1
& "C:\xampp\php\php.exe" artisan migrate --seed
& "C:\xampp\php\php.exe" artisan weather:sync
```

Jangan menjalankan `migrate:fresh` pada database yang berisi data penting karena perintah itu menghapus semua tabel sebelum membuat ulang.
