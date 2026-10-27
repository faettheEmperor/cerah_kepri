# Cerah Kepri

Pengambilan prakiraan cuaca Open-Meteo untuk wilayah Kepulauan Riau, termasuk Kota Tanjungpinang.

Panduan menjalankan aplikasi secara lokal di Windows tersedia di [PANDUAN-MENJALANKAN.md](PANDUAN-MENJALANKAN.md).

## Persiapan lokal

1. Siapkan PHP 8.2 atau lebih baru, Composer, dan PostgreSQL. Pada PHP XAMPP, aktifkan ekstensi `pdo_pgsql` dan `pgsql` di `php.ini`.
2. Buat basis data PostgreSQL bernama `cerah_weather` dan sesuaikan `DB_HOST`, `DB_PORT`, `DB_USERNAME`, serta `DB_PASSWORD` di `.env`.
3. Jalankan migrasi dan data awal:

   ```sh
   C:\xampp\php\php.exe artisan migrate --seed
   ```

4. Jalankan sinkronisasi awal untuk wilayah yang ingin ditampilkan (contoh: Sei Jang):

   ```sh
   C:\xampp\php\php.exe artisan weather:sync --region=21.72.04.1004
   ```

5. Tarik artikel RSS pertama kali:

   ```sh
   C:\xampp\php\php.exe artisan news:sync
   ```

6. Untuk pengembangan, jalankan scheduler Laravel:

   ```sh
   C:\xampp\php\php.exe artisan schedule:work
   ```

7. Untuk melihat proses sinkronisasi terbaru:

   ```sh
   C:\xampp\php\php.exe artisan processing:logs
   ```

   Log terpusat menampilkan jumlah wilayah atau feed yang diperiksa, berhasil, gagal, jumlah baris yang ditulis, dan pesan error. Log disimpan selama 90 hari.

   Scheduler mengolah ringkasan harian dan mengambil RSS berita setiap 30 menit, menghapus snapshot yang lebih lama dari 24 jam setiap jam, serta memperbarui data historis iklim. Sinkronisasi cuaca dilakukan per wilayah yang dipilih lewat tombol pembaruan atau dengan `weather:sync --region=KODE`; sinkronisasi penuh 505 titik sekaligus berisiko melewati kuota API publik. Untuk server, jalankan `C:\xampp\php\php.exe artisan schedule:run` setiap menit melalui penjadwal sistem. Di Windows, gunakan Task Scheduler untuk menjalankan perintah tersebut.

## Endpoint

- `GET /api/weather/regions` — daftar wilayah dan koordinat.
- `GET /api/weather/regions/{kode}` — cuaca saat ini, prakiraan per jam tujuh hari, riwayat, dan saran aktivitas.
- `GET /api/news` — artikel RSS yang cocok dengan kata kunci yang diatur di `config/news.php`.
- Contoh: `/api/weather/regions/21.72.04.1002` untuk Dompak.

Waktu disimpan dan ditampilkan dalam zona `Asia/Jakarta`. Snapshot cuaca dipertahankan selama 24 jam, sedangkan log pemrosesan disimpan selama 90 hari. Data laut tidak digunakan dan tabel penyimpanannya dihapus melalui migrasi.

## Sumber data

Data prakiraan cuaca dan kualitas udara PM2.5 berasal dari [Open-Meteo](https://open-meteo.com/). Data historis harian curah hujan dan suhu bersumber dari arsip model ERA5 melalui Open-Meteo. Titik koordinat wilayah menjadi estimasi berbasis lokasi, bukan pengukuran stasiun cuaca di setiap kelurahan. Tampilkan atribusi Open-Meteo pada antarmuka publik dan tinjau ketentuan lisensi/penggunaan sebelum deployment.

Berita disaring dari RSS [ANTARA Kepri](https://kepri.antaranews.com/rss/) menggunakan daftar kata kunci di `config/news.php`. Judul, ringkasan singkat, tanggal terbit, atribusi sumber, dan tautan asli ditampilkan pada halaman Berita.

## Cakupan dan batasan

- Prakiraan darat disimpan untuk Kota Tanjungpinang, empat kecamatan, dan 18 kelurahan yang terdaftar di seeder.
- Prakiraan laut tidak termasuk dalam cakupan data aplikasi.
- Insight musim dihitung dari arsip hujan dan suhu yang tersedia. Hasilnya merupakan ringkasan historis, bukan prediksi resmi musim atau peringatan cuaca.
- Saran aktivitas dibuat dari ambang parameter cuaca dan kualitas udara. Informasi ini bukan advis keselamatan resmi untuk pelayaran maupun penerbangan.
