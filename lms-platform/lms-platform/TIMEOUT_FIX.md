# 🔧 504 Gateway Timeout Hatası Düzeltmesi

## Sorun
Web uygulaması `http://localhost:8000` adresinde 504 Gateway Timeout hatası veriyor.

## Neden
504 Gateway Timeout hatası genellikle şu nedenlerden kaynaklanır:
1. PHP-FPM timeout ayarları çok kısa
2. Nginx fastcgi timeout ayarları çok kısa
3. Laravel uygulaması çok yavaş yanıt veriyor
4. Veritabanı bağlantı sorunları

## Çözüm

### 1. Nginx FastCGI Timeout Ayarları
`docker/nginx/default.conf` dosyasında PHP-FPM konfigürasyonuna timeout ayarları eklendi:

```nginx
location ~ \.php$ {
    fastcgi_pass 127.0.0.1:9000;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    include fastcgi_params;
    fastcgi_hide_header X-Powered-By;
    fastcgi_read_timeout 300;
    fastcgi_send_timeout 300;
    fastcgi_connect_timeout 300;
}
```

### 2. PHP-FPM Performance Ayarları
`docker/php/php-fpm.conf` dosyasında timeout ve memory ayarları artırıldı:

```ini
php_admin_value[memory_limit] = 512M
php_admin_value[max_execution_time] = 600
php_admin_value[max_input_time] = 600
php_admin_value[default_socket_timeout] = 600
```

## Yapılan Değişiklikler

1. ✅ Nginx fastcgi timeout ayarları eklendi (300 saniye)
2. ✅ PHP-FPM memory limit artırıldı (256M → 512M)
3. ✅ PHP-FPM execution time artırıldı (300 → 600 saniye)
4. ✅ PHP-FPM socket timeout ayarlandı (600 saniye)

## Kontrol

Container'ı yeniden başlattıktan sonra:
```bash
# Container'ı yeniden başlat
docker compose -f docker-compose.dev.yml restart app

# Web uygulamasını test et
curl http://localhost:8000

# Logları kontrol et
docker compose -f docker-compose.dev.yml logs app
```

## Notlar

- Container içindeki dosyalar volume mount ile güncelleniyor, bu yüzden container'ı yeniden başlatmak yeterli olmalı
- Eğer hala sorun varsa, veritabanı bağlantısını kontrol edin
- Laravel loglarını kontrol edin: `docker compose -f docker-compose.dev.yml exec app cat storage/logs/laravel.log`


