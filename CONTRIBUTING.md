# 🤝 Katkıda Bulunma Rehberi

OnliNote LMS Platform'a katkıda bulunmak istediğiniz için teşekkürler! Bu rehber, projeye nasıl katkıda bulunabileceğinizi açıklar.

## 📋 İçindekiler

- [Code of Conduct](#code-of-conduct)
- [Nasıl Katkıda Bulunulur?](#nasıl-katkıda-bulunulur)
- [Development Setup](#development-setup)
- [Coding Standards](#coding-standards)
- [Commit Mesajları](#commit-mesajları)
- [Pull Request Süreci](#pull-request-süreci)

## 📜 Code of Conduct

Bu proje, tüm katkıda bulunanlar için açık ve hoş bir ortam sağlamak için bir Code of Conduct'a bağlıdır. Projeye katılarak bu kurallara uymayı kabul edersiniz.

## 🚀 Nasıl Katkıda Bulunulur?

### Bug Report

1. [Issues](https://github.com/your-username/lms-platform/issues) sayfasında yeni bir issue oluşturun
2. Bug'ı detaylı bir şekilde açıklayın
3. Tekrarlama adımlarını ekleyin
4. Ekran görüntüleri ekleyin (varsa)

### Feature Request

1. [Issues](https://github.com/your-username/lms-platform/issues) sayfasında yeni bir feature request oluşturun
2. Özelliği detaylı bir şekilde açıklayın
3. Neden bu özelliğe ihtiyaç duyulduğunu açıklayın

### Code Contribution

1. Repository'yi fork edin
2. Feature branch oluşturun (`git checkout -b feature/AmazingFeature`)
3. Değişikliklerinizi yapın
4. Testleri çalıştırın
5. Commit edin (`git commit -m 'Add some AmazingFeature'`)
6. Push edin (`git push origin feature/AmazingFeature`)
7. Pull Request oluşturun

## 🛠️ Development Setup

```bash
# Repository'yi klonla
git clone https://github.com/your-username/lms-platform.git
cd lms-platform

# Branch oluştur
git checkout -b feature/your-feature-name

# Bağımlılıkları yükle
composer install
npm install

# Environment dosyasını oluştur
cp .env.example .env
php artisan key:generate

# Veritabanını yapılandır
php artisan migrate
php artisan db:seed

# Development server'ı başlat
php artisan serve
```

## 📐 Coding Standards

### PHP

- PSR-12 coding standard'ını takip edin
- PHPStan veya Psalm kullanın
- Type hints kullanın
- DocBlocks ekleyin

### JavaScript

- ESLint kurallarına uyun
- Modern JavaScript syntax kullanın
- Comments ekleyin

### Database

- Migration dosyalarını kullanın
- Foreign key'leri tanımlayın
- Index'leri ekleyin

## 💬 Commit Mesajları

Commit mesajlarınız açık ve açıklayıcı olmalıdır:

```
feat: Add user authentication
fix: Fix payment processing bug
docs: Update README.md
refactor: Refactor user service
test: Add user service tests
chore: Update dependencies
```

## 🔄 Pull Request Süreci

1. **Branch Oluşturma**
   - Feature branch oluşturun
   - Açıklayıcı bir isim kullanın

2. **Kod Yazma**
   - Coding standards'a uyun
   - Testleri yazın
   - Dokümantasyonu güncelleyin

3. **Test Etme**
   - Tüm testleri çalıştırın
   - Manuel test yapın
   - Code review yapın

4. **Pull Request Oluşturma**
   - Açıklayıcı bir başlık kullanın
   - Değişiklikleri açıklayın
   - İlgili issue'ları bağlayın
   - Screenshot'lar ekleyin (varsa)

5. **Review Süreci**
   - Feedback'i dikkate alın
   - Gerekli değişiklikleri yapın
   - Review'ları yanıtlayın

## ✅ Checklist

Pull Request göndermeden önce:

- [ ] Kod standartlarına uygun
- [ ] Testler eklendi/güncellendi
- [ ] Tüm testler geçiyor
- [ ] Dokümantasyon güncellendi
- [ ] Commit mesajları açıklayıcı
- [ ] Breaking change varsa CHANGELOG.md güncellendi
- [ ] Kendi kodumu test ettim

## 🐛 Bug Fix

Bug fix yaparken:

1. Bug'ı reproduce edin
2. Root cause'u bulun
3. Fix'i yapın
4. Test ekleyin
5. Dokümantasyonu güncelleyin

## ✨ New Feature

Yeni özellik eklerken:

1. Feature request issue'sunu oluşturun
2. Design'ı planlayın
3. Implementation'ı yapın
4. Testleri yazın
5. Dokümantasyonu güncelleyin

## 📚 Dokümantasyon

Dokümantasyon güncellemeleri:

- README.md
- API Documentation
- Code Comments
- CHANGELOG.md

## 🙏 Teşekkürler

Katkıda bulunduğunuz için teşekkürler! Projeyi daha iyi hale getirmenize yardımcı olduğunuz için minnettarız.

---

Sorularınız için [Issues](https://github.com/your-username/lms-platform/issues) sayfasında soru sorabilirsiniz.

