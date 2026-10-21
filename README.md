# Cerah Tanjungpinang

Fondasi pengambilan prakiraan cuaca Open-Meteo untuk Kota Tanjungpinang. Data darat disimpan untuk kota, empat kecamatan, dan 18 kelurahan. Data laut disimpan hanya untuk titik representatif Kota Tanjungpinang.

Panduan menjalankan aplikasi secara lokal di Windows tersedia di [PANDUAN-MENJALANKAN.md](PANDUAN-MENJALANKAN.md).

## Persiapan lokal

1. Siapkan PHP 8.2 atau lebih baru, Composer, dan PostgreSQL. Pada PHP XAMPP, aktifkan ekstensi `pdo_pgsql` dan `pgsql` di `php.ini`.
2. Buat basis data PostgreSQL bernama `cerah_weather` dan sesuaikan `DB_HOST`, `DB_PORT`, `DB_USERNAME`, serta `DB_PASSWORD` di `.env`.
3. Jalankan migrasi dan data awal:

   ```sh
   C:\xampp\php\php.exe artisan migrate --seed
   ```

4. Jalankan sinkronisasi pertama:

   ```sh
   C:\xampp\php\php.exe artisan weather:sync
   ```

5. Untuk pengembangan, jalankan scheduler Laravel:

   ```sh
   C:\xampp\php\php.exe artisan schedule:work
   ```

   Scheduler akan mengambil data setiap jam dan menghapus snapshot yang lebih lama dari 24 jam. Untuk server, jalankan `C:\xampp\php\php.exe artisan schedule:run` setiap menit melalui penjadwal sistem. Di Windows, gunakan Task Scheduler untuk menjalankan perintah tersebut.

## Endpoint

- `GET /api/weather/regions` — daftar wilayah dan koordinat.
- `GET /api/weather/regions/{kode}` — cuaca saat ini, prakiraan per jam tujuh hari, saran aktivitas, dan panel laut khusus kota.
- Contoh: `/api/weather/regions/21.72.04.1002` untuk Dompak.

Waktu disimpan dan ditampilkan dalam zona `Asia/Jakarta`. Snapshot cuaca dan laut serta catatan sinkronisasi dipertahankan selama 24 jam. Prakiraan laut bersifat informasi umum dan tidak ditujukan untuk navigasi atau penilaian keselamatan pelayaran.

## Sumber data

Data prakiraan berasal dari [Open-Meteo Weather API](https://open-meteo.com/en/docs) dan [Marine API](https://open-meteo.com/en/docs/marine-weather-api). Tampilkan atribusi Open-Meteo pada antarmuka publik sesuai ketentuan lisensi dan penggunaan layanan.
