# 🔍 504 Gateway Timeout Sorun Giderme Rehberi

## ✅ Kontrol Edilenler

1. **Container Durumu**: ✅ Çalışıyor
2. **Nginx**: ✅ Çalışıyor (Master + 8 Worker process)
3. **PHP-FPM**: ✅ Çalışıyor (Master + 2 Worker process)
4. **Supervisor**: ✅ Çalışıyor

## 🔧 Yapılan Düzeltmeler

1. ✅ Nginx FastCGI timeout ayarları eklendi (300 saniye)
2. ✅ PHP-FPM memory limit artırıldı (512M)
3. ✅ PHP-FPM execution time artırıldı (600 saniye)
4. ✅ Cache temizlendi

## 🔍 Olası Sorunlar ve Çözümler

### 1. Laravel Uygulaması Hata Veriyor

**Kontrol:**
```bash
# Laravel loglarını kontrol et
docker exec lms_app_dev tail -50 /var/www/html/storage/logs/laravel.log

# Veya WSL terminal'de:
docker compose -f docker-compose.dev.yml exec app cat storage/logs/laravel.log | tail -50
```

**Çözüm:**
- Log dosyasındaki hataları düzeltin
- Laravel cache'ini temizleyin

### 2. Veritabanı Bağlantı Sorunu

**Kontrol:**
```bash
# Veritabanı bağlantısını test et
docker exec lms_app_dev php artisan db:show

# Migration durumunu kontrol et
docker exec lms_app_dev php artisan migrate:status
```

**Çözüm:**
- `.env` dosyasındaki DB ayarlarını kontrol edin
- MySQL container'ının çalıştığından emin olun
- Veritabanı bağlantısını test edin

### 3. Storage Permissions Sorunu

**Kontrol:**
```bash
# Storage permissions kontrol et
docker exec lms_app_dev ls -la /var/www/html/storage

# Bootstrap cache permissions kontrol et
docker exec lms_app_dev ls -la /var/www/html/bootstrap/cache
```

**Çözüm:**
```bash
# Permissions ayarla
docker compose -f docker-compose.dev.yml exec app chmod -R 775 storage bootstrap/cache
docker compose -f docker-compose.dev.yml exec app chown -R www-data:www-data storage bootstrap/cache
```

### 4. PHP-FPM Timeout Sorunu

**Kontrol:**
```bash
# PHP-FPM process'lerini kontrol et
docker exec lms_app_dev ps aux | grep php-fpm
```

**Çözüm:**
- `docker/php/php-fpm.conf` dosyasındaki timeout ayarları zaten artırıldı
- Container'ı yeniden başlatın

## 🚀 Hızlı Çözüm Adımları

### Adım 1: Cache Temizle
```bash
docker exec lms_app_dev php artisan config:clear
docker exec lms_app_dev php artisan cache:clear
docker exec lms_app_dev php artisan route:clear
docker exec lms_app_dev php artisan view:clear
```

### Adım 2: Container'ı Yeniden Başlat
```bash
docker compose -f docker-compose.dev.yml restart app
```

### Adım 3: Logları Kontrol Et
```bash
# Container logları
docker compose -f docker-compose.dev.yml logs app --tail=100

# Laravel logları
docker exec lms_app_dev tail -100 /var/www/html/storage/logs/laravel.log
```

### Adım 4: Basit Test
```bash
# PHP çalışıyor mu?
docker exec lms_app_dev php -v

# Laravel route'ları çalışıyor mu?
docker exec lms_app_dev php artisan route:list | head -10
```

## 📝 PowerShell Kullanıyorsanız

PowerShell'de `grep` yerine `Select-String` kullanın:

```powershell
# Process'leri kontrol et
docker exec lms_app_dev ps aux | Select-String "php-fpm"

# Logları kontrol et
docker exec lms_app_dev cat /var/www/html/storage/logs/laravel.log | Select-String "error"
```

VEYA WSL terminal kullanın (daha kolay):
```bash
# WSL terminal'de çalıştırın
docker exec lms_app_dev ps aux | grep php-fpm
```

## 🔗 İlgili Dosyalar

- `docker/nginx/default.conf` - Nginx konfigürasyonu (timeout ayarları eklendi)
- `docker/php/php-fpm.conf` - PHP-FPM konfigürasyonu (timeout ayarları artırıldı)
- `TIMEOUT_FIX.md` - Timeout düzeltmeleri detayları


