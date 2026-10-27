# CERAH — Cuaca Elok Rutin Antar Hari

CERAH adalah aplikasi cuaca lokal untuk Provinsi Kepulauan Riau. Aplikasi menampilkan kondisi saat ini, prakiraan, riwayat cuaca, insight iklim, peta wilayah, saran aktivitas, dan berita terkait.

Panduan setup Windows/XAMPP ada di [PANDUAN-MENJALANKAN.md](PANDUAN-MENJALANKAN.md). Catatan tentang sumber koordinat wilayah ada di [database/seeders/data/README.md](database/seeders/data/README.md).

## Alur aplikasi

```text
database/seeders/data/kepri_regions.json
        └─ RegionSeeder → PostgreSQL: regions
                              ├─ SyncWeather → Open-Meteo Forecast + Air Quality
                              │                  └─ weather_observations
                              │                       └─ API wilayah → antarmuka + peta
                              ├─ AggregateDailyWeather → climate_daily_summaries
                              │                            └─ ClimateInsight → insight + grafik
                              └─ batas wilayah → endpoint peta

RSS ANTARA Kepri → SyncNews → news_articles → API berita → antarmuka
Semua proses sinkronisasi → ProcessingLog → processing_logs
```

Wilayah bersumber dari JSON dalam repository, bukan diambil dari API saat halaman dibuka. Seeder memasukkan provinsi, kabupaten/kota, kecamatan, kelurahan/desa, kode induk, dan koordinat ke tabel `regions`. Frontend meminta daftar wilayah aktif melalui `GET /api/weather/regions`. Koordinat dipakai untuk permintaan ke Open-Meteo.

## Setup singkat

Butuh PHP 8.2+, Composer, PostgreSQL, dan PHP extensions `pdo_pgsql` serta `pgsql`. Pada Windows/XAMPP, gunakan PHP XAMPP dengan path lengkap jika `php` pada terminal menunjuk ke versi lain.

1. Salin `.env.example` menjadi `.env`, lalu isi koneksi PostgreSQL lokal. Jangan commit `.env` atau memasukkan password/token ke Git.
2. Buat database PostgreSQL sesuai `DB_DATABASE` di `.env`.
3. Dari root repository jalankan:

   ```powershell
   composer install
   C:\xampp\php\php.exe artisan key:generate
   C:\xampp\php\php.exe artisan migrate
   C:\xampp\php\php.exe artisan db:seed --class=RegionSeeder
   ```

   Seeder wilayah aman dijalankan ulang: data diperbarui berdasarkan kode wilayah.
4. Tarik data awal untuk satu wilayah agar kuota Open-Meteo tidak langsung habis. Contoh Sei Jang:

   ```powershell
   C:\xampp\php\php.exe artisan weather:sync --region=21.72.04.1004
   ```

5. Ambil berita awal (opsional):

   ```powershell
   C:\xampp\php\php.exe artisan news:sync
   ```

6. Jalankan aplikasi dan buka `http://127.0.0.1:8000`:

   ```powershell
   C:\xampp\php\php.exe artisan serve --host=127.0.0.1 --port=8000
   ```

Langkah lokal lengkap, scheduler Windows, dan pemecahan masalah ada di panduan menjalankan.

## Proses data dan jadwal

| Proses | Perintah | Fungsi |
| --- | --- | --- |
| Cuaca saat ini dan forecast | `php artisan weather:sync --region=KODE` | Meminta Open-Meteo Forecast dan Air Quality untuk wilayah yang ditentukan. Tanpa `--region`, memproses semua wilayah aktif dan berisiko terkena batas API. |
| Ringkasan historis harian | `php artisan weather:aggregate-daily` | Mengolah snapshot cuaca yang tersimpan menjadi ringkasan harian. |
| Arsip iklim | `php artisan climate:sync-history --years=10` | Mengambil arsip harian dari Open-Meteo Archive/ERA5; dapat memerlukan banyak waktu dan terkena batas API. |
| Berita | `php artisan news:sync` | Mengambil RSS ANTARA Kepri, menyaring kata kunci dari `config/news.php`, lalu menyimpan artikel. |
| Log pemrosesan | `php artisan processing:logs` | Menampilkan jumlah item diproses/berhasil/gagal, jumlah baris tersimpan, dan error. |

Jadwal Laravel ada di `routes/console.php`: agregasi cuaca dan sinkronisasi berita tiap 30 menit, pembersihan snapshot dan log tiap jam, pembaruan arsip historis terbaru harian, serta pengisian arsip panjang bulanan. Jalankan `php artisan schedule:work` untuk pengembangan. Di server, konfigurasikan sistem operasi agar menjalankan `php artisan schedule:run` setiap menit.

Semua proses sinkronisasi utama mencatat status ke tabel `processing_logs`; data log disimpan hingga 90 hari. Cuaca disinkronkan atas permintaan wilayah yang dipilih dari halaman, supaya aplikasi tidak mengirim permintaan untuk ratusan wilayah pada setiap refresh.

## API utama

| Endpoint | Kegunaan |
| --- | --- |
| `GET /api/weather/regions` | Daftar wilayah aktif beserta kode induk dan koordinat. |
| `GET /api/weather/regions/{kode}` | Cuaca saat ini, forecast, riwayat snapshot, dan saran aktivitas untuk wilayah. |
| `GET /api/weather/map` | Ringkasan cuaca yang tersedia untuk peta. |
| `GET /api/weather/boundaries/{level}/{kode}` | Batas administrasi yang digunakan peta. |
| `GET /api/climate/summary/{kode}?days=30` | Ringkasan iklim dan seri grafik; rentang yang didukung 7, 30, 90, dan 365 hari. |
| `GET /api/news` | Artikel berita tersaring. |
| `POST /api/weather/refresh` | Meminta sinkronisasi untuk kode wilayah terpilih. |

Contoh kode Sei Jang: `21.72.04.1004`.

## Data dan cakupan

- `regions`: hierarki wilayah Kepri dan koordinatnya.
- `weather_observations`: snapshot cuaca sekarang dan forecast; snapshot dipertahankan 24 jam.
- `climate_daily_summaries`: hasil agregasi lokal dan data historis harian.
- `news_articles`: berita yang lolos filter kata kunci.
- `processing_logs`: ringkasan pelaksanaan proses sinkronisasi.

Data forecast, kualitas udara, dan arsip iklim berasal dari Open-Meteo. Data historis harian bersumber dari reanalisis model ERA5. Koordinat wilayah menghasilkan estimasi titik model, bukan pengukuran langsung di setiap kelurahan. Insight iklim adalah ringkasan historis, bukan prediksi musim resmi atau peringatan cuaca. Saran aktivitas juga bukan advis keselamatan resmi untuk penerbangan atau pelayaran.

Berita bersumber dari RSS [ANTARA Kepri](https://kepri.antaranews.com/rss/). Batas penggunaan, atribusi, dan ketentuan lisensi layanan eksternal perlu ditinjau sebelum deployment. Data laut tidak termasuk fitur aktif.

## Kontribusi tim

1. Buat branch untuk satu tugas, misalnya `feature/climate-chart` atau `fix/region-picker`.
2. Jangan commit `.env`, credential, backup database, atau file berisi data rahasia.
3. Buat Pull Request dengan ringkasan perubahan, cara verifikasi, dan screenshot jika mengubah tampilan.
4. Minta anggota lain meninjau sebelum merge. Hindari menggabungkan perubahan langsung ke branch utama tanpa review.
5. Sertakan migration dan/atau seeder jika perubahan kode memerlukan perubahan database atau data wilayah.
