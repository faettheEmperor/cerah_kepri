#!/bin/sh
set -e

echo "=========================================="
echo "🌦️ CERAH KEPRI Starting up..."
echo "=========================================="

# 1. Pastikan file .env tersedia
if [ ! -f /var/www/html/.env ]; then
    echo "📋 Membuat .env dari .env.example..."
    cp /var/www/html/.env.example /var/www/html/.env
fi

# 2. Pastikan APP_KEY terpasang (jika belum ada di env)
if [ -z "$APP_KEY" ]; then
    if ! grep -q "^APP_KEY=base64:" /var/www/html/.env; then
        echo "🔑 Menghasilkan APP_KEY otomatis..."
        php artisan key:generate --force
    fi
fi

# 3. Sesuaikan port Nginx jika PORT didefinisikan (default: 4176)
TARGET_PORT="${PORT:-4176}"
echo "🌐 Menyetel Nginx ke Port ${TARGET_PORT}..."
sed -i "s/listen [0-9]\+;/listen ${TARGET_PORT};/g" /etc/nginx/http.d/default.conf || true

# 4. Storage symlink
php artisan storage:link --force > /dev/null 2>&1 || true

# 5. Pastikan hak akses folder storage dan cache
echo "🔒 Memperbarui permission direktori storage & bootstrap/cache..."
mkdir -p /var/www/html/storage/logs \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 6. Tunggu koneksi PostgreSQL internal Coolify (maksimal 30 detik)
echo "⏳ Memeriksa koneksi database..."
MAX_TRIES=15
COUNT=0
DB_CONNECTED=0

while [ $COUNT -lt $MAX_TRIES ]; do
    if php artisan db:show > /dev/null 2>&1; then
        echo "✅ Database PostgreSQL berhasil terhubung!"
        DB_CONNECTED=1
        break
    fi
    COUNT=$((COUNT+1))
    echo "Menunggu database siap... ($COUNT/$MAX_TRIES)"
    sleep 2
done

# 7. Jalankan migrasi & seeder jika database terhubung
if [ $DB_CONNECTED -eq 1 ]; then
    echo "📦 Menyelaraskan tabel database (php artisan migrate)..."
    php artisan migrate --force || echo "⚠️ Peringatan pada migrasi, melanjutkan..."

    echo "🌱 Memeriksa dan mengisi data wilayah Kepri (RegionSeeder)..."
    php artisan db:seed --class=RegionSeeder --force || echo "⚠️ Seeding wilayah selesai/dilewati."
else
    echo "⚠️ Database belum siap saat startup container. Migrasi dilewati sementara."
fi

# 8. Optimasi cache produksi Laravel
echo "⚡ Mengoptimalkan cache Laravel (config, route, view)..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "🚀 Menjalankan Server Nginx & PHP-FPM di port ${TARGET_PORT}..."
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
