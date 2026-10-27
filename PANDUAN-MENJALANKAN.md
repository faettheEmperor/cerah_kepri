# Panduan Menjalankan Cerah Tanjungpinang

Panduan ini untuk menjalankan website secara lokal di Windows/XAMPP. Website lokal hanya dapat dibuka di komputer sendiri kecuali server dikonfigurasi khusus untuk akses jaringan atau internet.

## Yang perlu aktif

- PostgreSQL 18 dengan database `cerah_weather`.
- PHP XAMPP dari `C:\xampp\php\php.exe`. Di komputer ini perintah `php` biasa menunjuk ke `C:\php\php.exe`, yang tidak memiliki driver PostgreSQL, jadi gunakan path XAMPP secara eksplisit.
- Satu proses terminal untuk website. Scheduler bersifat opsional untuk pengolahan dan pembersihan data.

## Menjalankan website dan pembaruan otomatis

### 1. Pastikan PostgreSQL hidup

Tekan `Win + R`, ketik `services.msc`, lalu tekan Enter. Cari layanan **postgresql-x64-18** dan pastikan statusnya **Running/Berjalan**. Jika berhenti, klik kanan layanan itu lalu pilih **Start/Mulai**.

Database `cerah_weather`, migrasi tabel, dan data wilayah Kepulauan Riau sudah dibuat. Jadi langkah `migrate --seed` hanya diperlukan saat menyiapkan database baru atau setelah database dihapus, bukan setiap kali membuka website.

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

Biarkan terminal ini terbuka jika ingin menjalankan pekerjaan terjadwal. Scheduler mengolah ringkasan harian, mengambil RSS berita setiap 30 menit, membersihkan snapshot lama, dan memperbarui arsip historis. Pengambilan cuaca Open-Meteo dilakukan untuk wilayah yang dipilih, bukan untuk semua 505 wilayah sekaligus, agar tidak menghabiskan kuota API.

### 5. Ambil data saat itu juga (opsional)

Untuk menarik data terbaru lewat terminal, tentukan kode wilayah yang dipilih. Contoh berikut memperbarui Sei Jang:

```powershell
cd C:\xampp\htdocs\cerahv1
& "C:\xampp\php\php.exe" artisan weather:sync --region=21.72.04.1004
```

Di website, pilih wilayah lalu tekan tombol perbarui di bagian atas. Sistem hanya meminta data cuaca untuk wilayah itu.

Untuk mengambil berita tanpa menunggu jadwal, jalankan:

```powershell
& "C:\xampp\php\php.exe" artisan news:sync
```

Untuk melihat log proses cuaca, histori iklim, dan berita:

```powershell
& "C:\xampp\php\php.exe" artisan processing:logs
```

Log memuat jumlah item yang diperiksa, berhasil, gagal, jumlah baris yang ditulis, dan pesan error. Catatan sinkronisasi cuaca lama disalin ke log terpusat saat migrasi; tabel wilayah tetap dipertahankan.

Kata kunci dan sumber RSS dapat disesuaikan di `config/news.php`.

## Memastikan data masuk

- Buka `http://127.0.0.1:8000` untuk halaman website.
- API daftar wilayah: `http://127.0.0.1:8000/api/weather/regions`
- API cuaca Kota Tanjungpinang: `http://127.0.0.1:8000/api/weather/regions/21.72`
- API cuaca Dompak: `http://127.0.0.1:8000/api/weather/regions/21.72.04.1002`
- Pada halaman, gunakan tombol panah melingkar untuk memperbarui data wilayah yang sedang dipilih. Tampilan memeriksa database setiap lima menit; pemeriksaan berkala ini tidak mengirim permintaan baru ke Open-Meteo.

Riwayat snapshot cuaca disimpan 24 jam. Prakiraan terbaru berisi 168 jam (7 hari). Data laut tidak termasuk dalam cakupan aplikasi.

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

Task Scheduler akan memanggil Laravel setiap menit untuk menjalankan tugas pemeliharaan yang sudah waktunya. Pembaruan cuaca wilayah tetap diminta dari tombol website atau perintah `weather:sync --region=KODE`. Gunakan cara ini sebagai pengganti terminal `schedule:work`, jangan jalankan dua scheduler bersamaan.

## Menghentikan proses

- Untuk menghentikan website atau `schedule:work` di terminal, fokuskan terminal terkait lalu tekan `Ctrl + C`.
- Untuk tugas Windows terjadwal, buka Task Scheduler, cari `Cerah Weather Scheduler`, lalu pilih **Disable** atau **End**.

## Jika muncul error

- **`could not find driver`**: pastikan menjalankan PHP XAMPP dengan path lengkap `C:\xampp\php\php.exe`, bukan perintah `php` biasa.
- **Koneksi PostgreSQL ditolak**: pastikan layanan `postgresql-x64-18` berjalan dan nilai `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, serta `DB_PASSWORD` pada `.env` benar. Jangan membagikan isi password.
- **Belum ada data cuaca**: perbarui wilayah yang dipilih dari website atau jalankan `C:\xampp\php\php.exe artisan weather:sync --region=KODE`.
- **Open-Meteo membalas HTTP 429**: tunggu selama waktu yang disebut di pesan error, lalu coba perbarui satu wilayah saja.
- **Gagal terhubung ke Open-Meteo**: periksa koneksi internet dan coba lagi.
- **Port 8000 sudah dipakai**: gunakan port lain, misalnya `--port=8010`, lalu buka `http://127.0.0.1:8010`.

## Menyiapkan database dari awal

Untuk database yang benar-benar baru, setelah mengisi koneksi `.env`, jalankan:

```powershell
cd C:\xampp\htdocs\cerahv1
& "C:\xampp\php\php.exe" artisan migrate --seed
& "C:\xampp\php\php.exe" artisan weather:sync --region=21.72.04.1004
```

Perintah contoh mengisi data cuaca Sei Jang. Wilayah lain dapat diperbarui satu per satu lewat tombol website atau dengan mengganti kode wilayah setelah `--region=`. Jangan menjalankan `migrate:fresh` pada database yang berisi data penting karena perintah itu menghapus semua tabel sebelum membuat ulang.
