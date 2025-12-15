# Fix Storage Upload Error di Production

## Masalah
Error 500 saat upload foto KTP di https://kitorangpeduli.id/register

## Penyebab
1. Collection name tidak match: controller menggunakan 'ktp' tapi model menggunakan 'ktp_image'
2. Storage symlink mungkin belum dibuat
3. Permission folder storage tidak tepat
4. Disk storage tidak dikonfigurasi dengan benar

## Solusi yang Sudah Diterapkan

### 1. Fix Collection Name
✅ Sudah diubah di `app/Http/Controllers/AuthController.php`:
```php
// Sebelumnya: ->toMediaCollection('ktp');
// Sekarang: ->toMediaCollection('ktp_image');
```

### 2. Add Error Handling
✅ Sudah ditambahkan try-catch di register method untuk menangkap error

### 3. Set Disk Explicitly
✅ Sudah ditambahkan di `app/Models/Respondent.php`:
```php
public function registerMediaCollections(): void
{
    $this
        ->addMediaCollection('ktp_image')
        ->useDisk('public')  // Explicitly use public disk
        ->singleFile();
}
```

## Langkah Deploy ke Production

### 1. Connect ke Server
```bash
ssh root@103.185.52.124
cd /var/www/kitorangpeduli.id
```

### 2. Pull Latest Code
```bash
git pull origin main
```

### 3. Buat Storage Symlink (Jika Belum Ada)
```bash
/usr/bin/php8.2 artisan storage:link
```

### 4. Set Permission yang Benar
```bash
# Set owner
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache

# Set permission
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Pastikan folder media ada
mkdir -p storage/app/public/media
chmod -R 775 storage/app/public
```

### 5. Clear Cache
```bash
/usr/bin/php8.2 artisan config:clear
/usr/bin/php8.2 artisan cache:clear
/usr/bin/php8.2 artisan view:clear
```

### 6. Re-cache untuk Production
```bash
/usr/bin/php8.2 artisan config:cache
/usr/bin/php8.2 artisan route:cache
/usr/bin/php8.2 artisan view:cache
```

### 7. Restart Services
```bash
systemctl restart php8.2-fpm
systemctl restart apache2
```

## Testing

1. Buka https://kitorangpeduli.id/register
2. Isi form registrasi
3. Upload foto KTP (pastikan < 2MB)
4. Submit form
5. Seharusnya tidak error lagi

## Check Logs Jika Masih Error

```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check Apache error logs
tail -f /var/log/apache2/error.log

# Check PHP-FPM logs
tail -f /var/log/php8.2-fpm.log
```

## Verifikasi Storage Symlink

```bash
ls -la public/storage
# Seharusnya menunjukkan symlink ke storage/app/public
```

## Alternative: Jika Masih Error, Gunakan Local Disk Sementara

Edit `.env` di production:
```env
# Ubah dari s3 ke public untuk testing
FILESYSTEM_DISK=public
```

Kemudian:
```bash
/usr/bin/php8.2 artisan config:clear
/usr/bin/php8.2 artisan config:cache
```

## Monitoring

Setelah deploy, monitor error dengan:
```bash
tail -f storage/logs/laravel.log | grep "Registration error"
```
