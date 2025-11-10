# 📦 GitHub'a Proje Yükleme Rehberi

Bu rehber, OnliNote LMS platformunu GitHub'a güvenli bir şekilde yüklemek için adım adım talimatlar içerir.

## 🔒 Güvenlik Kontrolü

GitHub'a yüklemeden önce mutlaka şunları kontrol edin:

### 1. Hassas Bilgileri Kontrol Edin

```bash
# .env dosyasının git'e eklenmediğinden emin olun
git check-ignore .env

# AWS key dosyalarını kontrol edin
git status | grep -i "\.pem\|\.key"

# Docker env dosyalarını kontrol edin
git status | grep -i "docker\.env"
```

### 2. Commit Edilmemesi Gereken Dosyalar

Aşağıdaki dosyalar `.gitignore`'da olmalı:
- ✅ `.env`
- ✅ `*.pem`
- ✅ `*.key`
- ✅ `docker.env`
- ✅ `database/database.sqlite`
- ✅ `vendor/`
- ✅ `node_modules/`
- ✅ `storage/logs/*`
- ✅ `public/storage`

## 🚀 GitHub'a Yükleme Adımları

### Adım 1: Git Repository Oluşturma

```bash
# Git repository'sini başlat (eğer henüz yapılmadıysa)
git init

# Mevcut durumu kontrol et
git status
```

### Adım 2: .gitignore Kontrolü

```bash
# .gitignore dosyasının mevcut olduğundan emin olun
cat .gitignore

# Hassas dosyaların ignore edildiğini kontrol edin
git check-ignore .env docker.env aws/*.pem
```

### Adım 3: Dosyaları Stage'e Ekleme

```bash
# Tüm dosyaları ekle (gitignore'daki dosyalar otomatik olarak atlanır)
git add .

# Stage'deki dosyaları kontrol et
git status
```

### Adım 4: İlk Commit

```bash
# İlk commit'i yap
git commit -m "Initial commit: OnliNote LMS Platform

- Laravel 11 backend
- MySQL database support
- Redis cache
- Stripe payment integration
- Docker support
- AWS Free Tier deployment scripts
- Complete LMS features"

# Commit geçmişini kontrol et
git log --oneline
```

### Adım 5: GitHub Repository Oluşturma

1. GitHub'a gidin: https://github.com
2. "New repository" butonuna tıklayın
3. Repository adı: `lms-platform` (veya istediğiniz isim)
4. Açıklama: "OnliNote LMS Platform - Learning Management System"
5. Public veya Private seçin
6. "Initialize this repository with a README" seçeneğini işaretlemeyin
7. "Create repository" butonuna tıklayın

### Adım 6: Remote Repository Ekleme

```bash
# GitHub repository URL'inizi alın (örnek: https://github.com/username/lms-platform.git)

# Remote repository'yi ekleyin
git remote add origin https://github.com/username/lms-platform.git

# Remote'u kontrol edin
git remote -v
```

### Adım 7: Dosyaları GitHub'a Push Etme

```bash
# Main branch'e push et
git branch -M main
git push -u origin main

# Veya master branch kullanıyorsanız
git branch -M master
git push -u origin master
```

## 📝 README.md Güncelleme

README.md dosyasını GitHub için güncelleyin:

```markdown
# OnliNote LMS Platform

Kapsamlı bir Learning Management System (LMS) platformu.

## 🚀 Özellikler

- ✅ Kullanıcı, eğitmen ve admin panelleri
- ✅ Kurs yönetimi ve satışı
- ✅ Stripe ödeme entegrasyonu
- ✅ Redis cache desteği
- ✅ Docker desteği
- ✅ AWS Free Tier deployment
- ✅ Modern ve responsive tasarım

## 📋 Gereksinimler

- PHP >= 8.2
- Composer
- Node.js & npm
- MySQL 8.0
- Redis
- Docker (opsiyonel)

## 🛠️ Kurulum

### Docker ile (Önerilen)

```bash
# Repository'yi klonla
git clone https://github.com/username/lms-platform.git
cd lms-platform

# Environment dosyasını oluştur
cp docker.env.example .env

# Docker Compose ile başlat
docker-compose -f docker-compose.dev.yml up -d

# Migration ve seeder çalıştır
docker-compose -f docker-compose.dev.yml exec app php artisan migrate --force
docker-compose -f docker-compose.dev.yml exec app php artisan db:seed --force
```

### Manuel Kurulum

```bash
# Bağımlılıkları yükle
composer install
npm install

# Environment dosyasını oluştur
cp .env.example .env
php artisan key:generate

# Migration ve seeder
php artisan migrate
php artisan db:seed

# Frontend build
npm run build

# Uygulamayı başlat
php artisan serve
```

## ☁️ AWS Free Tier Deployment

Tamamen ücretsiz AWS Free Tier deployment için:

```bash
cd aws
./deploy-free-simple.sh deploy
```

Detaylı rehber için `AWS-FREE-DEPLOYMENT.md` dosyasına bakın.

## 📚 Dokümantasyon

- [AWS Deployment Guide](AWS-FREE-DEPLOYMENT.md)
- [Quick Start Guide](QUICK-START-FREE.md)
- [Docker Setup](DOCKER-README.md)

## 🤝 Katkıda Bulunma

1. Fork edin
2. Feature branch oluşturun (`git checkout -b feature/AmazingFeature`)
3. Commit edin (`git commit -m 'Add some AmazingFeature'`)
4. Push edin (`git push origin feature/AmazingFeature`)
5. Pull Request oluşturun

## 📄 Lisans

MIT License

## 👥 Yazarlar

- Your Name - [GitHub](https://github.com/username)

## 🙏 Teşekkürler

- Laravel Framework
- Stripe
- AWS Free Tier
```

## 🔍 Son Kontroller

### Commit Öncesi Kontrol Listesi

- [ ] `.env` dosyası commit edilmedi
- [ ] `*.pem` dosyaları commit edilmedi
- [ ] `docker.env` dosyası commit edilmedi
- [ ] `vendor/` klasörü commit edilmedi
- [ ] `node_modules/` klasörü commit edilmedi
- [ ] `storage/logs/*` commit edilmedi
- [ ] `database/database.sqlite` commit edilmedi
- [ ] Hassas bilgiler (API keys, passwords) commit edilmedi

### Güvenlik Kontrolü

```bash
# Hassas bilgileri arayın
grep -r "sk_live\|sk_test\|pk_live\|pk_test" --exclude-dir=vendor --exclude-dir=node_modules
grep -r "password.*=" .env.example docker.env.example
grep -r "API_KEY\|SECRET_KEY" --exclude-dir=vendor --exclude-dir=node_modules

# Eğer hassas bilgi bulursanız, .env.example dosyalarında placeholder kullanın
```

## 📦 GitHub Actions (Opsiyonel)

CI/CD için GitHub Actions ekleyebilirsiniz:

```yaml
# .github/workflows/tests.yml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      - name: Install Dependencies
        run: composer install
      - name: Run Tests
        run: php artisan test
```

## 🎯 Sonraki Adımlar

1. ✅ GitHub repository'yi oluşturun
2. ✅ Dosyaları push edin
3. ✅ README.md'yi güncelleyin
4. ✅ GitHub Pages veya dokümantasyon ekleyin (opsiyonel)
5. ✅ Issues ve Pull Requests için template'ler ekleyin (opsiyonel)

## ⚠️ Önemli Notlar

1. **Hassas bilgileri asla commit etmeyin**
2. **.env dosyalarını asla commit etmeyin**
3. **AWS key dosyalarını asla commit etmeyin**
4. **Production veritabanı bilgilerini asla commit etmeyin**
5. **Private repository kullanıyorsanız bile dikkatli olun**

## 🆘 Yardım

Sorun yaşarsanız:
1. `.gitignore` dosyasını kontrol edin
2. `git status` ile dosyaları kontrol edin
3. `git check-ignore <dosya>` ile ignore edilip edilmediğini kontrol edin
4. GitHub documentation'a bakın: https://docs.github.com

---

**Hazır olduğunuzda yukarıdaki adımları takip ederek projenizi GitHub'a yükleyebilirsiniz!**

