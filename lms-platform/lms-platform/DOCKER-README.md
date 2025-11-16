# 🐳 LMS Platform Docker Setup

Bu proje, OnliNote LMS platformunu Docker container'larında çalıştırmak için gerekli tüm konfigürasyonları içerir.

## 🚀 Hızlı Başlangıç

### 1. Gereksinimler
- Docker 20.10+
- Docker Compose 2.0+
- En az 4GB RAM
- En az 10GB disk alanı

### 2. Kurulum

```bash
# Projeyi klonlayın
git clone <repository-url>
cd lms-platform

# Environment dosyasını oluşturun
cp docker.env.example .env

# .env dosyasını düzenleyin (gerekli ayarları yapın)
nano .env

# Otomatik kurulum scriptini çalıştırın
./docker/scripts/setup.sh
```

### 3. Manuel Kurulum

```bash
# Container'ları oluştur ve başlat
docker-compose up -d --build

# Veritabanı migration'larını çalıştır
docker-compose exec app php artisan migrate

# Veritabanını seed'le
docker-compose exec app php artisan db:seed

# Storage link oluştur
docker-compose exec app php artisan storage:link
```

## 🏗️ Servis Yapısı

### Ana Servisler
- **app**: Laravel uygulaması (PHP-FPM + Nginx)
- **mysql**: MySQL 8.0 veritabanı
- **redis**: Redis cache ve session store
- **queue**: Laravel queue worker
- **scheduler**: Laravel task scheduler
- **mobile**: React Native mobile app
- **nginx**: Reverse proxy

### Geliştirme Servisleri
- **mailhog**: Email test servisi
- **elasticsearch**: Arama motoru
- **kibana**: Elasticsearch görselleştirme

## 📊 Servis Portları

| Servis | Port | Açıklama |
|--------|------|----------|
| Web App | 80 | Ana LMS uygulaması |
| Mobile API | 3000 | React Native API |
| MySQL | 3306 | Veritabanı |
| Redis | 6379 | Cache servisi |
| Mailhog | 8025 | Email test arayüzü |
| Kibana | 5601 | Elasticsearch dashboard |
| Elasticsearch | 9200 | Arama motoru API |

## 🛠️ Kullanışlı Komutlar

### Temel Komutlar
```bash
# Tüm servisleri başlat
docker-compose up -d

# Servisleri durdur
docker-compose down

# Logları görüntüle
docker-compose logs -f

# Belirli servisin loglarını görüntüle
./docker/scripts/logs.sh app

# Container'a bağlan
docker-compose exec app bash
```

### Laravel Komutları
```bash
# Artisan komutlarını çalıştır
./docker/scripts/artisan.sh migrate
./docker/scripts/artisan.sh db:seed
./docker/scripts/artisan.sh queue:work
./docker/scripts/artisan.sh tinker
```

### Backup ve Restore
```bash
# Backup oluştur
./docker/scripts/backup.sh

# Backup'tan geri yükle
./docker/scripts/restore.sh backups/lms_backup_20240101_120000.tar.gz
```

## 🔧 Geliştirme Ortamı

Geliştirme için ayrı bir compose dosyası kullanın:

```bash
# Geliştirme ortamını başlat
docker-compose -f docker-compose.dev.yml up -d

# Debug portları:
# - Web App: 8000
# - MySQL: 3307
# - Redis: 6380
# - Mailhog: 8026
```

## 📁 Docker Dosya Yapısı

```
docker/
├── nginx/
│   └── default.conf          # Nginx konfigürasyonu
├── php/
│   ├── php-fpm.conf          # PHP-FPM ayarları
│   └── php.ini               # PHP ayarları
├── mysql/
│   └── my.cnf                # MySQL konfigürasyonu
├── redis/
│   └── redis.conf            # Redis ayarları
├── supervisor/
│   └── supervisord.conf      # Process yöneticisi
└── scripts/
    ├── setup.sh              # Otomatik kurulum
    ├── backup.sh             # Backup scripti
    ├── restore.sh            # Restore scripti
    ├── logs.sh               # Log görüntüleme
    └── artisan.sh            # Laravel komutları
```

## 🔒 Güvenlik

### Production Ortamı
- Tüm servisler internal network'te çalışır
- Sadece gerekli portlar expose edilir
- Security headers aktif
- File permissions optimize edilmiş

### Environment Variables
- Hassas bilgiler .env dosyasında saklanır
- Docker secrets kullanılabilir
- Production'da güçlü şifreler kullanın

## 📈 Performans Optimizasyonu

### PHP-FPM
- Dynamic process management
- OPcache aktif
- Memory limit: 256MB

### MySQL
- InnoDB buffer pool: 256MB
- Query cache aktif
- Slow query logging

### Redis
- Memory limit: 256MB
- LRU eviction policy
- Persistence aktif

### Nginx
- Gzip compression
- Static file caching
- Client max body size: 100MB

## 🐛 Sorun Giderme

### Servis Sağlık Kontrolü
```bash
# Tüm servislerin durumunu kontrol et
docker-compose ps

# Servis loglarını incele
docker-compose logs [service_name]

# Container resource kullanımını kontrol et
docker stats
```

### Yaygın Sorunlar

1. **Port çakışması**: .env dosyasında portları değiştirin
2. **Permission hatası**: `chmod -R 755 storage bootstrap/cache`
3. **Database bağlantı hatası**: MySQL container'ının hazır olmasını bekleyin
4. **Memory hatası**: Docker Desktop'ta memory limitini artırın

### Log Dosyaları
- Application logs: `docker-compose logs app`
- Nginx logs: `docker-compose logs nginx`
- MySQL logs: `docker-compose logs mysql`
- Redis logs: `docker-compose logs redis`

## 🔄 Güncelleme

```bash
# Kodu güncelle
git pull origin main

# Container'ları yeniden oluştur
docker-compose down
docker-compose up -d --build

# Migration'ları çalıştır
docker-compose exec app php artisan migrate
```

## 📞 Destek

Sorunlarınız için:
1. Log dosyalarını kontrol edin
2. GitHub Issues'da arama yapın
3. Yeni issue oluşturun

## 📄 Lisans

MIT License - Detaylar için LICENSE dosyasına bakın.
