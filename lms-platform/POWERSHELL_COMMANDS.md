# 🔧 PowerShell Komutları - Docker Kontrolü

## ⚠️ Sorun: Docker Desktop Bağlantı Hatası

PowerShell'de Docker komutları çalıştırırken şu hatayı alıyorsunuz:
```
error during connect: Get "http://%2F%2F.%2Fpipe%2FdockerDesktopLinuxEngine/v1.49/containers/json?all=1&filters=...": open //./pipe/dockerDesktopLinuxEngine: The system cannot find the file specified.
```

## Çözüm

### 1. Docker Desktop'ı Başlatın
- Docker Desktop uygulamasını açın
- Docker Desktop'ın tamamen başlamasını bekleyin
- System tray'de Docker ikonunun yeşil olduğundan emin olun

### 2. WSL Üzerinden Komutları Çalıştırın

PowerShell'de `grep` komutu yok. Bunun yerine:

**WSL Terminal'de çalıştırın:**
```bash
# Container durumunu kontrol et
docker ps --filter "name=lms"

# Logları görüntüle
docker compose -f docker-compose.dev.yml logs app

# Process'leri kontrol et
docker compose -f docker-compose.dev.yml exec app ps aux | grep php-fpm
```

**VEYA PowerShell'de:**
```powershell
# Container durumunu kontrol et
docker ps --filter "name=lms"

# Logları görüntüle
docker compose -f docker-compose.dev.yml logs app

# Process'leri kontrol et (grep yerine Select-String kullanın)
docker compose -f docker-compose.dev.yml exec app ps aux | Select-String "php-fpm"
```

### 3. Docker Desktop WSL Integration Kontrolü

Docker Desktop ayarlarından:
1. Settings → Resources → WSL Integration
2. "Enable integration with my default WSL distro" seçeneğini aktif edin
3. Kullandığınız WSL distro'yu seçin (örn: Ubuntu)
4. "Apply & Restart" butonuna tıklayın

## 🔍 504 Gateway Timeout Sorunu İçin Kontrol

### WSL Terminal'de:
```bash
# Container loglarını kontrol et
docker compose -f docker-compose.dev.yml logs app --tail=100

# PHP-FPM çalışıyor mu?
docker compose -f docker-compose.dev.yml exec app ps aux | grep php-fpm

# Nginx çalışıyor mu?
docker compose -f docker-compose.dev.yml exec app ps aux | grep nginx

# Laravel route'larını test et
docker compose -f docker-compose.dev.yml exec app php artisan route:list

# Basit bir test
docker compose -f docker-compose.dev.yml exec app php -v
```

### PowerShell'de (Select-String ile):
```powershell
# Container loglarını kontrol et
docker compose -f docker-compose.dev.yml logs app --tail=100

# PHP-FPM çalışıyor mu?
docker compose -f docker-compose.dev.yml exec app ps aux | Select-String "php-fpm"

# Nginx çalışıyor mu?
docker compose -f docker-compose.dev.yml exec app ps aux | Select-String "nginx"
```

## 📝 Notlar

- PowerShell'de `grep` yerine `Select-String` kullanın
- Docker Desktop'ın çalıştığından emin olun
- WSL Integration'ın aktif olduğundan emin olun
- Komutları WSL terminal'de çalıştırmak daha kolay olabilir


