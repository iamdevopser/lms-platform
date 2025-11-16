#!/bin/bash

# OnliNote LMS - Tek Komutla Başlatma Scripti
# Kullanım: ./start-lms.sh

set -e  # Hata durumunda dur

echo "🚀 OnliNote LMS Başlatılıyor..."
echo ""

# Renkler
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Proje dizini
PROJECT_DIR="/home/ec2-user/lms-platform"
cd "$PROJECT_DIR" || { echo -e "${RED}❌ Proje dizini bulunamadı: $PROJECT_DIR${NC}"; exit 1; }

echo -e "${GREEN}✅ Proje dizinine geçildi: $PROJECT_DIR${NC}"

# 1. Git güncellemesi (opsiyonel, hata vermemeli)
echo ""
echo -e "${YELLOW}📥 Git güncellemesi kontrol ediliyor...${NC}"
git pull origin main 2>/dev/null || echo "⚠️  Git pull atlandı (opsiyonel)"

# 2. .env dosyası kontrolü
echo ""
echo -e "${YELLOW}📝 .env dosyası kontrol ediliyor...${NC}"
if [ ! -f .env ]; then
    echo "📋 .env dosyası oluşturuluyor..."
    cp .env.example .env
    echo -e "${GREEN}✅ .env dosyası oluşturuldu${NC}"
else
    echo -e "${GREEN}✅ .env dosyası mevcut${NC}"
fi

# 3. Docker Compose servislerini başlat
echo ""
echo -e "${YELLOW}🐳 Docker servisleri başlatılıyor...${NC}"
docker-compose -f docker-compose.free-tier.yml up -d --remove-orphans
echo -e "${GREEN}✅ Docker servisleri başlatıldı${NC}"

# Servislerin hazır olmasını bekle
echo ""
echo -e "${YELLOW}⏳ Servislerin hazır olması bekleniyor (30 saniye)...${NC}"
sleep 30

# 4. Composer bağımlılıklarını kontrol et ve yükle
echo ""
echo -e "${YELLOW}📦 Composer bağımlılıkları kontrol ediliyor...${NC}"
if [ ! -d vendor ]; then
    echo "📥 Composer install çalıştırılıyor..."
    docker-compose -f docker-compose.free-tier.yml exec -T app composer install --no-interaction --prefer-dist --optimize-autoloader
    echo -e "${GREEN}✅ Composer bağımlılıkları yüklendi${NC}"
else
    echo -e "${GREEN}✅ Composer bağımlılıkları mevcut${NC}"
fi

# 5. SQLite database dosyasını oluştur
echo ""
echo -e "${YELLOW}💾 SQLite database kontrol ediliyor...${NC}"
docker-compose -f docker-compose.free-tier.yml exec -T app sh -c '
    mkdir -p /var/www/html/database &&
    touch /var/www/html/database/database.sqlite &&
    chmod 666 /var/www/html/database/database.sqlite
' || true
echo -e "${GREEN}✅ SQLite database hazır${NC}"

# 6. APP_KEY kontrolü ve oluşturma
echo ""
echo -e "${YELLOW}🔑 APP_KEY kontrol ediliyor...${NC}"
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    echo "🔑 APP_KEY oluşturuluyor..."
    docker-compose -f docker-compose.free-tier.yml exec -T app php artisan key:generate --force
    echo -e "${GREEN}✅ APP_KEY oluşturuldu${NC}"
else
    echo -e "${GREEN}✅ APP_KEY mevcut${NC}"
fi

# 7. Migration çalıştır
echo ""
echo -e "${YELLOW}🗄️  Database migration çalıştırılıyor...${NC}"
docker-compose -f docker-compose.free-tier.yml exec -T app php artisan migrate --force
echo -e "${GREEN}✅ Migration tamamlandı${NC}"

# 8. Seeder çalıştır (opsiyonel, hata vermemeli)
echo ""
echo -e "${YELLOW}🌱 Database seeder çalıştırılıyor...${NC}"
docker-compose -f docker-compose.free-tier.yml exec -T app php artisan db:seed --force 2>/dev/null || echo "⚠️  Seeder atlandı (opsiyonel)"
echo -e "${GREEN}✅ Seeder tamamlandı${NC}"

# 9. Storage link
echo ""
echo -e "${YELLOW}🔗 Storage link oluşturuluyor...${NC}"
docker-compose -f docker-compose.free-tier.yml exec -T app php artisan storage:link 2>/dev/null || echo "⚠️  Storage link zaten mevcut"
echo -e "${GREEN}✅ Storage link hazır${NC}"

# 10. İzinleri düzelt
echo ""
echo -e "${YELLOW}🔐 Dosya izinleri düzeltiliyor...${NC}"
docker-compose -f docker-compose.free-tier.yml exec -T app sh -c '
    cd /var/www/html &&
    mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache &&
    touch storage/logs/laravel.log 2>/dev/null || true &&
    chown -R application:application storage bootstrap/cache &&
    chmod -R ug+rwX storage bootstrap/cache
'
echo -e "${GREEN}✅ İzinler düzeltildi${NC}"

# 11. Cache temizle
echo ""
echo -e "${YELLOW}🧹 Cache temizleniyor...${NC}"
docker-compose -f docker-compose.free-tier.yml exec -T app php artisan cache:clear
docker-compose -f docker-compose.free-tier.yml exec -T app php artisan config:clear
docker-compose -f docker-compose.free-tier.yml exec -T app php artisan route:clear
docker-compose -f docker-compose.free-tier.yml exec -T app php artisan view:clear
echo -e "${GREEN}✅ Cache temizlendi${NC}"

# 12. Servislerin durumunu kontrol et
echo ""
echo -e "${YELLOW}📊 Servislerin durumu kontrol ediliyor...${NC}"
docker-compose -f docker-compose.free-tier.yml ps

# 13. Public IP'yi göster
echo ""
echo -e "${YELLOW}🌐 Public IP bilgisi:${NC}"
PUBLIC_IP=$(curl -s http://169.254.169.254/latest/meta-data/public-ipv4 2>/dev/null || echo "Bilinmiyor")
echo -e "${GREEN}   http://$PUBLIC_IP${NC}"

# 14. Başarı mesajı
echo ""
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}✅ LMS Platform başarıyla başlatıldı!${NC}"
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo ""
echo -e "${YELLOW}📋 Giriş Bilgileri:${NC}"
echo -e "   Admin:     ${GREEN}admin@example.com${NC} / password"
echo -e "   Instructor: ${GREEN}instructor@example.com${NC} / password"
echo -e "   User:     ${GREEN}user@example.com${NC} / password"
echo ""
echo -e "${YELLOW}🔗 Erişim Linkleri:${NC}"
echo -e "   Ana Sayfa: ${GREEN}http://$PUBLIC_IP${NC}"
echo -e "   Admin:    ${GREEN}http://$PUBLIC_IP/admin/login${NC}"
echo -e "   Login:    ${GREEN}http://$PUBLIC_IP/login${NC}"
echo ""
echo -e "${GREEN}🎉 Sistem hazır!${NC}"
echo ""

