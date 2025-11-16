# 🚀 CI/CD Kurulum Rehberi

## 📋 Adım Adım CI/CD Kurulumu

Bu rehber, GitHub Actions ile otomatik test ve deployment sürecini kurmanızı sağlar.

---

## ✅ Adım 1: GitHub Actions Workflow'unu Kontrol Et

Workflow dosyası oluşturuldu:
- `.github/workflows/ci.yml`

Bu dosya şunları yapar:
- ✅ Her push'ta otomatik test çalıştırır
- ✅ Docker Compose yapılandırmasını doğrular
- ✅ PHP 8.2 ile testleri çalıştırır
- ✅ SQLite + Redis ile test ortamı kurar

---

## ✅ Adım 2: Workflow'u GitHub'a Push Et

Lokal makinenizde (WSL terminal):

```bash
# Değişiklikleri commit et
git add .github/workflows/ci.yml
git commit -m "feat: add GitHub Actions CI workflow"

# GitHub'a push et
git push origin minimal-sqlite
```

---

## ✅ Adım 3: GitHub'da Actions'ı Aktifleştir

1. **GitHub Repository'ye git:**
   - `https://github.com/iamdevopser/lms-platform`

2. **Settings > Actions > General:**
   - "Allow all actions and reusable workflows" seçeneğini işaretle
   - "Workflow permissions" → "Read and write permissions" seç
   - "Save" tıkla

3. **Actions sekmesine git:**
   - Sol menüden "Actions" sekmesine tıkla
   - İlk workflow çalışmasını göreceksin (push sonrası otomatik başlar)

---

## ✅ Adım 4: Workflow'u Test Et

### Yöntem 1: Manuel Test (Push ile)

```bash
# Küçük bir değişiklik yap (örnek: README'ye bir satır ekle)
echo "# Test" >> README.md

# Commit ve push
git add README.md
git commit -m "test: trigger CI workflow"
git push origin minimal-sqlite
```

### Yöntem 2: GitHub Web UI'dan

1. **Actions** sekmesine git
2. **"CI Pipeline"** workflow'unu seç
3. **"Run workflow"** butonuna tıkla
4. Branch seç: `minimal-sqlite`
5. **"Run workflow"** tıkla

---

## ✅ Adım 5: Workflow Sonuçlarını Kontrol Et

1. **Actions** sekmesinde workflow çalışmasını gör
2. **Yeşil tik (✓)** = Başarılı
3. **Kırmızı X (✗)** = Hata (detaylar için tıkla)

### Hata Durumunda:

- **"test" job'una tıkla** → Hangi test başarısız oldu?
- **"docker-build" job'una tıkla** → Docker hatası var mı?
- Logları incele ve hatayı düzelt

---

## 🔧 Adım 6: Workflow'u Özelleştirme (İsteğe Bağlı)

### Test Coverage Ekleme:

`.github/workflows/ci.yml` dosyasında:

```yaml
- name: Run tests with coverage
  run: php artisan test --coverage
```

### Deployment Ekleme (EC2'ye otomatik deploy):

```yaml
deploy:
  needs: test
  runs-on: ubuntu-latest
  if: github.ref == 'refs/heads/main'
  steps:
    - name: Deploy to EC2
      uses: appleboy/ssh-action@master
      with:
        host: ${{ secrets.EC2_HOST }}
        username: ec2-user
        key: ${{ secrets.EC2_SSH_KEY }}
        script: |
          cd /home/ec2-user/lms-platform
          ./start-lms.sh
```

**Secrets eklemek için:**
- Repository > Settings > Secrets and variables > Actions
- "New repository secret" → `EC2_HOST`, `EC2_SSH_KEY` ekle

---

## 📊 Adım 7: Badge Ekleyeme (README'ye)

README.md dosyasına ekle:

```markdown
![CI](https://github.com/iamdevopser/lms-platform/workflows/CI%20Pipeline/badge.svg)
```

---

## ✅ Adım 8: Branch Protection (İsteğe Bağlı)

Main branch'i korumak için:

1. **Settings > Branches**
2. **"Add rule"** tıkla
3. **Branch name pattern:** `main`
4. **"Require status checks to pass before merging"** işaretle
5. **"Require branches to be up to date before merging"** işaretle
6. **Status checks:** `test`, `docker-build` seç
7. **"Create"** tıkla

---

## 🎉 Tamamlandı!

Artık:
- ✅ Her push'ta otomatik test çalışır
- ✅ Pull request'lerde test sonuçları görünür
- ✅ Hatalar otomatik tespit edilir
- ✅ Deployment otomatikleştirilebilir

---

## 📝 Notlar

- **İlk çalıştırma:** 2-3 dakika sürebilir
- **Test başarısız olursa:** Logları kontrol et, hatayı düzelt, tekrar push et
- **Workflow'u değiştirmek:** `.github/workflows/ci.yml` dosyasını düzenle

---

**Sorun mu var?** GitHub Actions loglarını kontrol et veya workflow dosyasını gözden geçir.

