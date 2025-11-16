# 🔗 GitHub Workflow Direkt Linki

## Yeni Workflow'u Görmek İçin

GitHub'da şu linke git:

```
https://github.com/iamdevopser/lms-platform/actions/workflows/ci.yml
```

Bu link **doğrudan yeni workflow'u** gösterir.

---

## Adım Adım

1. **Yukarıdaki linke tıkla** (veya tarayıcıya yapıştır)

2. **Eğer "No workflow runs" görürsen:**
   - Sağ üstte **"Run workflow"** butonuna tıkla
   - Branch: `minimal-sqlite` seç
   - **"Run workflow"** tıkla

3. **Workflow çalışmaya başlar:**
   - 2 job görünür: `test` ve `docker-build`
   - Her job adım adım çalışır
   - Yeşil tik = Başarılı ✅
   - Kırmızı X = Hata ❌

---

## Alternatif: GitHub'da Manuel Arama

1. GitHub repository'ye git: `https://github.com/iamdevopser/lms-platform`
2. **Actions** sekmesine tıkla
3. Sol sidebar'da **"All workflows"** altında **"CI Pipeline"** workflow'unu ara
4. Eğer görünmüyorsa, sayfayı yenile (F5)

---

## Not

- Eski workflow'lar (`CI Pipeline (Free Tier Optimized)`, `Deploy to AWS`) `main` branch'inden geliyor
- Yeni workflow (`CI Pipeline`) `minimal-sqlite` branch'inde
- Workflow dosyası: `.github/workflows/ci.yml`

