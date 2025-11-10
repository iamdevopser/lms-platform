# 🚀 GitHub'a Hızlı Yükleme

Bu rehber, projeyi GitHub'a hızlı bir şekilde yüklemek için adım adım talimatlar içerir.

## ⚡ Hızlı Yükleme (Script ile)

### 1. Script'i Çalıştır

```bash
# Script'i çalıştırılabilir yap
chmod +x setup-github.sh

# Script'i çalıştır
./setup-github.sh
```

Script otomatik olarak:
- ✅ Git repository'yi başlatır (yoksa)
- ✅ Hassas dosyaları kontrol eder
- ✅ .gitignore'u kontrol eder
- ✅ Dosyaları stage'e ekler
- ✅ İlk commit'i oluşturur
- ✅ GitHub repository bilgilerini sorar
- ✅ Remote repository'yi ekler
- ✅ GitHub'a push eder

## 📝 Manuel Yükleme

### 1. Git Repository Başlat

```bash
# Git repository'yi başlat (yoksa)
git init

# Mevcut durumu kontrol et
git status
```

### 2. Hassas Dosyaları Kontrol Et

```bash
# .env dosyasının ignore edildiğini kontrol et
git check-ignore .env

# AWS key dosyalarını kontrol et
git status | grep -i "\.pem\|\.key"
```

### 3. Dosyaları Ekle ve Commit Et

```bash
# Tüm dosyaları ekle
git add .

# İlk commit'i yap
git commit -m "Initial commit: OnliNote LMS Platform

- Laravel 11 backend
- MySQL database support
- Redis cache
- Stripe payment integration
- Docker support
- AWS Free Tier deployment scripts
- Complete LMS features"
```

### 4. GitHub Repository Oluştur

1. GitHub'a gidin: https://github.com
2. "New repository" butonuna tıklayın
3. Repository adı: `lms-platform`
4. Açıklama: "OnliNote LMS Platform - Learning Management System"
5. Public veya Private seçin
6. **"Initialize this repository with a README" seçeneğini işaretlemeyin**
7. "Create repository" butonuna tıklayın

### 5. Remote Repository Ekle ve Push Et

```bash
# Remote repository'yi ekle (GitHub URL'inizi kullanın)
git remote add origin https://github.com/your-username/lms-platform.git

# Branch'i ayarla
git branch -M main

# GitHub'a push et
git push -u origin main
```

## ✅ Kontrol Listesi

GitHub'a yüklemeden önce:

- [ ] `.env` dosyası commit edilmedi
- [ ] `*.pem` dosyaları commit edilmedi
- [ ] `docker.env` dosyası commit edilmedi
- [ ] `vendor/` klasörü commit edilmedi
- [ ] `node_modules/` klasörü commit edilmedi
- [ ] `storage/logs/*` commit edilmedi
- [ ] `database/database.sqlite` commit edilmedi
- [ ] Hassas bilgiler (API keys, passwords) commit edilmedi

## 🔍 Hassas Dosya Kontrolü

```bash
# Hassas bilgileri arayın
grep -r "sk_live\|sk_test\|pk_live\|pk_test" --exclude-dir=vendor --exclude-dir=node_modules
grep -r "password.*=" .env.example docker.env.example
grep -r "API_KEY\|SECRET_KEY" --exclude-dir=vendor --exclude-dir=node_modules

# Eğer hassas bilgi bulursanız, .env.example dosyalarında placeholder kullanın
```

## 📚 Sonraki Adımlar

1. ✅ GitHub repository'yi kontrol edin
2. ✅ README.md'yi gözden geçirin
3. ✅ Issues ve Pull Requests için template'ler ekleyin
4. ✅ GitHub Actions için CI/CD pipeline ekleyin (opsiyonel)
5. ✅ GitHub Pages veya dokümantasyon ekleyin (opsiyonel)

## 🆘 Sorun Giderme

### "remote origin already exists" Hatası

```bash
# Mevcut remote'u kontrol et
git remote -v

# Remote'u güncelle
git remote set-url origin https://github.com/your-username/lms-platform.git
```

### "Permission denied" Hatası

```bash
# SSH key'inizi GitHub'a ekleyin
# Veya HTTPS kullanın ve Personal Access Token kullanın
```

### "Large files" Uyarısı

```bash
# Büyük dosyaları kontrol et
git ls-files | xargs ls -la | sort -k5 -rn | head -10

# .gitignore'a ekleyin
```

## 💡 İpuçları

1. **Private Repository**: İlk başta private repository kullanın, daha sonra public yapabilirsiniz
2. **Branch Protection**: Main branch'i korumak için branch protection rules ekleyin
3. **GitHub Actions**: CI/CD pipeline ekleyin
4. **Documentation**: GitHub Pages ile dokümantasyon oluşturun
5. **Releases**: İlk release'i oluşturun

## 📖 Detaylı Rehber

Detaylı rehber için [GITHUB-SETUP.md](GITHUB-SETUP.md) dosyasına bakın.

---

**Hazır olduğunuzda yukarıdaki adımları takip ederek projenizi GitHub'a yükleyebilirsiniz!**

