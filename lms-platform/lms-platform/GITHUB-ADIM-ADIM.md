# GitHub'a Dosya Yükleme - Adım Adım Rehber

Bu rehber, mevcut GitHub repository'nize (`lms-platform`) tüm proje dosyalarını adım adım nasıl yükleyeceğinizi gösterir.

## 📋 Mevcut Durum

✅ **Repository**: https://github.com/iamdevopser/lms-platform.git  
✅ **Branch**: main  
✅ **Remote**: origin (ayarlı)  
✅ **Hassas Dosyalar**: Güvenli (.env, *.pem git'te yok)  
✅ **Takip Edilen Dosyalar**: 1661 dosya

## 🚀 Adım Adım Yükleme

### ✅ Adım 1: Mevcut Durumu Kontrol Et

```bash
# Git durumunu kontrol et
git status

# Remote repository'yi kontrol et
git remote -v

# Branch'i kontrol et
git branch -a

# Son commit'leri görüntüle
git log --oneline -5
```

**Beklenen Çıktı:**
- Repository: `https://github.com/iamdevopser/lms-platform.git`
- Branch: `main`
- Remote: `origin` (ayarlı)

---

### ✅ Adım 2: Remote'tan Son Değişiklikleri Çek

Eğer GitHub'da başka değişiklikler varsa, önce onları çekin:

```bash
# Remote'tan son değişiklikleri çek (merge yapmadan)
git fetch origin

# Local ve remote arasındaki farkları kontrol et
git log HEAD..origin/main --oneline

# Eğer remote'ta yeni commit'ler varsa, pull yap
git pull origin main
```

**Not:** Eğer conflict olursa, önce conflict'leri çözün.

---

### ✅ Adım 3: Yeni ve Değişen Dosyaları Kontrol Et

```bash
# Yeni dosyaları görüntüle
git status

# Tüm değişiklikleri görüntüle (yeni, değişen, silinen)
git status --short

# Ignore edilen dosyaları görüntüle
git status --ignored

# Belirli bir dosyanın durumunu kontrol et
git status aws/free-tier-simple-infrastructure.yml
```

**Beklenen Durum:**
- Yeni dosyalar: `??` işareti ile gösterilir
- Değişen dosyalar: `M` işareti ile gösterilir
- Silinen dosyalar: `D` işareti ile gösterilir

---

### ✅ Adım 4: Hassas Dosyaları Kontrol Et

Git'e yanlışlıkla hassas dosyalar eklenmemeli:

```bash
# .env dosyası git'te var mı?
git ls-files | grep "\.env$"

# PEM dosyaları git'te var mı?
git ls-files | grep "\.pem$"

# KEY dosyaları git'te var mı?
git ls-files | grep "\.key$"
```

**Beklenen Çıktı:** Boş (hassas dosyalar git'te olmamalı)

**Eğer Hassas Dosya Bulunursa:**
```bash
# Git tracking'den kaldır (fiziksel dosyayı silmez)
git rm --cached .env
git rm --cached aws/*.pem

# .gitignore'u kontrol et
cat .gitignore | grep -E "\.env|\.pem|\.key"

# Commit yap
git commit -m "chore: Remove sensitive files from Git tracking"
```

---

### ✅ Adım 5: Yeni Dosyaları Stage'e Ekle

#### Seçenek 1: Tüm Dosyaları Ekle (Önerilen)

```bash
# Tüm yeni ve değişen dosyaları ekle
git add .

# Durumu kontrol et
git status
```

#### Seçenek 2: Belirli Dosyaları Ekle

```bash
# Belirli bir dosyayı ekle
git add aws/free-tier-simple-infrastructure.yml

# Belirli bir klasörü ekle
git add aws/

# Belirli bir pattern'e göre ekle
git add *.md

# Birden fazla dosya ekle
git add file1.txt file2.txt file3.txt
```

#### Seçenek 3: İnteraktif Ekleme (Önerilen - Büyük Değişiklikler İçin)

```bash
# İnteraktif olarak dosyaları seç
git add -i

# Veya
git add -p  # Patch mode (değişiklikleri parça parça ekle)
```

---

### ✅ Adım 6: Stage'deki Dosyaları Kontrol Et

```bash
# Stage'e eklenen dosyaları görüntüle
git status

# Hangi dosyaların ekleneceğini göster
git diff --cached --name-only

# Stage'deki değişiklikleri görüntüle
git diff --cached

# Belirli bir dosyanın değişikliklerini görüntüle
git diff --cached aws/free-tier-simple-infrastructure.yml
```

**Beklenen Çıktı:**
- Stage'e eklenen dosyalar `git status` çıktısında yeşil renkte gösterilir
- `git diff --cached --name-only` komutu stage'deki dosyaları listeler

---

### ✅ Adım 7: Commit Yap

#### Basit Commit

```bash
# Kısa commit mesajı
git commit -m "feat: Add AWS Free Tier deployment configuration"
```

#### Detaylı Commit (Önerilen)

```bash
# Uzun commit mesajı
git commit -m "feat: Add complete AWS Free Tier deployment

- Add CloudFormation template for EC2 and S3
- Add deployment scripts for AWS Free Tier
- Add Docker Compose configuration for free tier
- Update documentation and guides
- Fix CloudFormation template dependencies"
```

#### Commit Mesajı Best Practices

```
<type>: <subject>

<body>

<footer>
```

**Commit Type'ları:**
- `feat`: Yeni özellik
- `fix`: Hata düzeltme
- `docs`: Dokümantasyon
- `style`: Kod formatı
- `refactor`: Kod refactoring
- `test`: Test ekleme
- `chore`: Build process, araçlar

**Örnek Commit Mesajları:**
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

---

### ✅ Adım 8: GitHub'a Push Yap

#### İlk Push (Branch henüz remote'ta yoksa)

```bash
# Main branch'i ilk defa push yap
git push -u origin main
```

#### Normal Push (Branch zaten remote'ta varsa)

```bash
# Main branch'e push yap
git push origin main
```

#### Force Push (Dikkatli Kullanın!)

```bash
# Force push (sadece gerektiğinde kullanın)
git push -f origin main
```

**Not:** Force push, remote'taki commit'leri siler. Sadece gerektiğinde kullanın.

---

### ✅ Adım 9: Push Sonrası Kontrol

```bash
# GitHub'da dosyaların yüklendiğini kontrol et
# Tarayıcıda: https://github.com/iamdevopser/lms-platform

# Local ve remote branch'leri senkronize et
git fetch origin

# Durumu kontrol et
git status

# Son commit'leri görüntüle
git log --oneline -5

# Remote'taki son commit'leri görüntüle
git log origin/main --oneline -5
```

---

## 📝 Pratik Örnek: Tam İşlem Akışı

### Senaryo 1: Yeni Dosyalar Ekleme

```bash
# 1. Durumu kontrol et
git status

# 2. Yeni dosyaları ekle
git add .

# 3. Durumu kontrol et
git status

# 4. Commit yap
git commit -m "feat: Add new deployment files"

# 5. Push yap
git push origin main
```

### Senaryo 2: Mevcut Dosyaları Güncelleme

```bash
# 1. Dosyayı düzenle
# (örnek: aws/free-tier-simple-infrastructure.yml)

# 2. Değişiklikleri kontrol et
git diff aws/free-tier-simple-infrastructure.yml

# 3. Dosyayı stage'e ekle
git add aws/free-tier-simple-infrastructure.yml

# 4. Commit yap
git commit -m "fix: Update CloudFormation template dependencies"

# 5. Push yap
git push origin main
```

### Senaryo 3: Birden Fazla Dosyayı Güncelleme

```bash
# 1. Tüm değişiklikleri görüntüle
git status

# 2. Tüm değişiklikleri ekle
git add .

# 3. Commit yap
git commit -m "feat: Update deployment configuration

- Update CloudFormation template
- Update deployment scripts
- Update documentation"

# 4. Push yap
git push origin main
```

---

## 🔒 Güvenlik Kontrol Listesi

Push yapmadan önce kontrol edin:

- [ ] `.env` dosyası git'te yok
- [ ] `*.pem` dosyaları git'te yok
- [ ] `*.key` dosyaları git'te yok
- [ ] API keys içeren dosyalar git'te yok
- [ ] Database şifreleri git'te yok
- [ ] `.gitignore` dosyası güncel
- [ ] Hassas bilgiler commit mesajında yok

---

## 🛠️ Sorun Giderme

### Problem 1: Conflict (Çakışma)

**Belirti:**
```
error: Your local changes to the following files would be overwritten by merge
```

**Çözüm:**
```bash
# 1. Local değişiklikleri commit et
git add .
git commit -m "WIP: Local changes"

# 2. Remote'tan çek
git pull origin main

# 3. Conflict'leri çöz
# Dosyaları düzenle, sonra:
git add <conflict-dosyaları>
git commit -m "fix: Resolve merge conflicts"

# 4. Push yap
git push origin main
```

### Problem 2: Remote'ta Yeni Commit'ler Var

**Belirti:**
```
error: failed to push some refs to 'origin'
hint: Updates were rejected because the remote contains work that you do
hint: not have locally.
```

**Çözüm:**
```bash
# 1. Remote'tan çek
git pull origin main

# 2. Conflict'leri çöz (varsa)
# 3. Push yap
git push origin main
```

### Problem 3: Büyük Dosya Hatası

**Belirti:**
```
error: File is too large
```

**Çözüm:**
```bash
# 1. Büyük dosyayı kaldır
git rm --cached <büyük-dosya>

# 2. .gitignore'a ekle
echo "<büyük-dosya>" >> .gitignore

# 3. Commit yap
git commit -m "chore: Remove large file from Git tracking"

# 4. Push yap
git push origin main
```

### Problem 4: Yanlışlıkla Hassas Dosya Eklendi

**Çözüm:**
```bash
# 1. Git tracking'den kaldır
git rm --cached .env
git rm --cached aws/*.pem

# 2. .gitignore'a ekle (zaten ekli olmalı)
# 3. Commit yap
git commit -m "chore: Remove sensitive files from Git tracking"

# 4. Push yap
git push origin main

# 5. GitHub'da dosyayı manuel olarak sil (geçmişte kalır)
# GitHub UI'dan dosyayı sil veya:
# git filter-branch ile geçmişten temizle (ileri seviye)
```

---

## 📊 İlerleme Takibi

### Commit Geçmişi

```bash
# Son commit'leri görüntüle
git log --oneline -10

# Detaylı commit geçmişi
git log --graph --oneline --all

# Belirli bir dosyanın geçmişi
git log --follow -- aws/free-tier-simple-infrastructure.yml
```

### Dosya Değişiklikleri

```bash
# Son commit'teki değişiklikler
git show

# Belirli bir dosyadaki değişiklikler
git diff aws/free-tier-simple-infrastructure.yml

# Stage'deki değişiklikler
git diff --cached
```

### Branch Karşılaştırma

```bash
# Local ve remote arasındaki fark
git log HEAD..origin/main --oneline

# Remote'ta olup local'de olmayan commit'ler
git log origin/main..HEAD --oneline
```

---

## ✅ Hızlı Komut Referansı

```bash
# Durum kontrolü
git status
git status --short
git status --ignored

# Dosya ekleme
git add .
git add <dosya>
git add <klasör>

# Commit
git commit -m "mesaj"
git commit -m "başlık" -m "açıklama"

# Push
git push origin main
git push -u origin main  # İlk push

# Pull
git pull origin main
git fetch origin
git merge origin/main

# Geçmiş
git log --oneline -10
git log --graph --oneline --all
git show

# Farklar
git diff
git diff --cached
git diff <dosya>
```

---

## 🎯 Özet: En Sık Kullanılan Komutlar

```bash
# 1. Durumu kontrol et
git status

# 2. Dosyaları ekle
git add .

# 3. Commit yap
git commit -m "feat: Add new files"

# 4. Push yap
git push origin main
```

---

## 📚 Ek Kaynaklar

- [Git Documentation](https://git-scm.com/doc)
- [GitHub Guides](https://guides.github.com/)
- [Git Commit Best Practices](https://www.conventionalcommits.org/)
- [GitHub Flow](https://guides.github.com/introduction/flow/)

---

## 🆘 Yardım

Sorun yaşıyorsanız:

1. `git status` ile durumu kontrol edin
2. `git log` ile commit geçmişini görüntüleyin
3. GitHub'da repository'yi kontrol edin
4. Hata mesajlarını okuyun
5. Bu rehberi tekrar gözden geçirin

---

## 🎉 Başarı!

GitHub'a dosyaları başarıyla yükledikten sonra:

1. GitHub'da repository'yi kontrol edin: https://github.com/iamdevopser/lms-platform
2. Dosyaların yüklendiğini doğrulayın
3. README.md dosyasını güncelleyin (opsiyonel)
4. GitHub Pages veya dokümantasyon ekleyin (opsiyonel)
5. İlk release'i oluşturun (opsiyonel)

**İyi çalışmalar! 🚀**

