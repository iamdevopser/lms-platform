# GitHub'a Dosya Yükleme Rehberi

Bu rehber, mevcut GitHub repository'nize (`lms-platform`) tüm proje dosyalarını adım adım nasıl yükleyeceğinizi gösterir.

## 📋 Mevcut Durum

- **Repository**: https://github.com/iamdevopser/lms-platform.git
- **Branch**: main
- **Remote**: origin (ayarlı)

## 🚀 Adım Adım Yükleme

### Adım 1: Mevcut Durumu Kontrol Et

```bash
# Git durumunu kontrol et
git status

# Remote repository'yi kontrol et
git remote -v

# Branch'i kontrol et
git branch -a
```

### Adım 2: Hassas Dosyaları Kontrol Et

Git'te takip edilen hassas dosyaları kontrol et:

```bash
# .env dosyası git'te takip ediliyor mu?
git ls-files | grep -E "\.env$|\.pem$|\.key$"

# Eğer hassas dosyalar varsa, bunları kaldır:
# git rm --cached .env
# git rm --cached aws/*.pem
```

### Adım 3: Yeni ve Değişen Dosyaları Kontrol Et

```bash
# Yeni dosyaları görüntüle
git status --short

# İgnore edilen dosyaları görüntüle
git status --ignored
```

### Adım 4: Tüm Değişiklikleri Stage'e Ekle

```bash
# Tüm yeni ve değişen dosyaları ekle
git add .

# Veya belirli dosyaları ekle
git add app/
git add config/
git add database/
git add routes/
git add resources/
git add public/
git add docker-compose.yml
git add Dockerfile
git add README.md
# ... diğer dosyalar
```

### Adım 5: Değişiklikleri Kontrol Et

```bash
# Stage'e eklenen dosyaları görüntüle
git status

# Hangi dosyaların ekleneceğini göster
git diff --cached --name-only
```

### Adım 6: Commit Yap

```bash
# Anlamlı bir commit mesajı ile commit yap
git commit -m "feat: Add complete LMS platform with AWS deployment, GitHub setup, and documentation"

# Veya daha detaylı commit mesajı
git commit -m "feat: Complete LMS platform deployment

- Add AWS Free Tier deployment scripts and CloudFormation templates
- Add GitHub setup scripts and documentation
- Add Docker Compose configurations
- Add comprehensive documentation (README, CONTRIBUTING, CHANGELOG)
- Add GitHub Actions workflow for CI/CD
- Update .gitignore for sensitive files
- Add health check endpoints
- Add Stripe integration seeder
- Add deployment guides and quick start documents"
```

### Adım 7: Remote Repository'den Güncellemeleri Çek (Opsiyonel)

Eğer GitHub'da başka değişiklikler varsa, önce onları çekin:

```bash
# Remote'tan son değişiklikleri çek
git fetch origin

# Main branch'i güncelle
git pull origin main

# Eğer conflict varsa, çözün ve tekrar commit yapın
```

### Adım 8: GitHub'a Push Yap

```bash
# Main branch'e push yap
git push origin main

# Veya ilk defa push yapıyorsanız:
git push -u origin main
```

### Adım 9: Push Sonrası Kontrol

```bash
# GitHub'da dosyaların yüklendiğini kontrol et
# https://github.com/iamdevopser/lms-platform

# Local ve remote branch'leri senkronize et
git fetch origin
git status
```

## 🔒 Güvenlik Kontrolü

### Hassas Dosyaları Kontrol Et

Aşağıdaki dosyaların GitHub'a yüklenmediğinden emin olun:

- `.env` dosyası
- `docker.env` dosyası
- `*.pem` dosyaları (AWS key pair)
- `*.key` dosyaları
- API keys içeren dosyalar
- Database şifreleri

### Kontrol Komutları

```bash
# .env dosyası git'te var mı?
git ls-files | grep "\.env$"

# PEM dosyaları git'te var mı?
git ls-files | grep "\.pem$"

# Eğer varsa, kaldır:
git rm --cached .env
git rm --cached aws/*.pem
git commit -m "chore: Remove sensitive files from Git tracking"
```

## 📝 İyi Commit Mesajları

### Commit Mesaj Formatı

```
<type>: <subject>

<body>

<footer>
```

### Commit Type'ları

- `feat`: Yeni özellik
- `fix`: Hata düzeltme
- `docs`: Dokümantasyon
- `style`: Kod formatı (fonksiyonellik değişikliği yok)
- `refactor`: Kod refactoring
- `test`: Test ekleme/düzeltme
- `chore`: Build process, araçlar vb.

### Örnek Commit Mesajları

```bash
# Yeni özellik
git commit -m "feat: Add AWS Free Tier deployment configuration"

# Hata düzeltme
git commit -m "fix: Resolve CloudFormation template dependency issues"

# Dokümantasyon
git commit -m "docs: Add comprehensive GitHub setup guide"

# Birden fazla değişiklik
git commit -m "feat: Add complete deployment infrastructure

- Add CloudFormation template for EC2 and S3
- Add deployment scripts for AWS Free Tier
- Add Docker Compose configuration
- Add deployment documentation"
```

## 🛠️ Sorun Giderme

### Conflict Çözme

Eğer push sırasında conflict olursa:

```bash
# Remote'tan güncellemeleri çek
git fetch origin

# Merge yap
git merge origin/main

# Conflict'leri çöz
# Dosyaları düzenle, sonra:
git add <conflict-dosyaları>
git commit -m "fix: Resolve merge conflicts"

# Tekrar push yap
git push origin main
```

### Büyük Dosyaları Kaldırma

Eğer yanlışlıkla büyük dosyalar eklendiyse:

```bash
# Git history'den dosyayı kaldır
git rm --cached <dosya-adı>

# Commit yap
git commit -m "chore: Remove large file from Git tracking"

# Push yap
git push origin main
```

### Remote Repository'yi Güncelleme

Eğer remote repository URL'i değiştiyse:

```bash
# Mevcut remote'u kontrol et
git remote -v

# Remote URL'ini değiştir
git remote set-url origin https://github.com/iamdevopser/lms-platform.git

# Yeni URL'i doğrula
git remote -v
```

## 📊 İlerleme Takibi

### Commit Geçmişi

```bash
# Son commit'leri görüntüle
git log --oneline -10

# Detaylı commit geçmişi
git log --graph --oneline --all

# Belirli bir dosyanın geçmişi
git log --follow -- <dosya-adı>
```

### Dosya Değişiklikleri

```bash
# Son commit'teki değişiklikler
git show

# Belirli bir dosyadaki değişiklikler
git diff <dosya-adı>

# Stage'deki değişiklikler
git diff --cached
```

## ✅ Kontrol Listesi

Deploy etmeden önce kontrol edin:

- [ ] Hassas dosyalar (.env, *.pem) git'te yok
- [ ] .gitignore dosyası güncel
- [ ] Tüm önemli dosyalar eklendi
- [ ] Commit mesajı anlamlı
- [ ] Remote repository doğru
- [ ] Branch doğru (main)
- [ ] Conflict yok
- [ ] Test edildi (opsiyonel)

## 🎯 Hızlı Komutlar

```bash
# Tüm değişiklikleri ekle ve commit yap
git add . && git commit -m "feat: Update project files"

# Push yap
git push origin main

# Durumu kontrol et
git status

# Son commit'leri görüntüle
git log --oneline -5
```

## 📚 Ek Kaynaklar

- [Git Documentation](https://git-scm.com/doc)
- [GitHub Guides](https://guides.github.com/)
- [Git Commit Best Practices](https://www.conventionalcommits.org/)

## 🆘 Yardım

Sorun yaşıyorsanız:

1. `git status` ile durumu kontrol edin
2. `git log` ile commit geçmişini görüntüleyin
3. GitHub'da repository'yi kontrol edin
4. Hata mesajlarını okuyun
5. Bu rehberi tekrar gözden geçirin

