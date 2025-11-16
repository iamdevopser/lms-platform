# OnlyNote Logo Setup

Bu dosya, OnlyNote logosunun websitede nasıl kurulacağını açıklar.

## Logo Dosyaları

### 1. SVG Logo (Ana Logo)
- **Dosya**: `public/frontend/images/onlynote-logo.svg`
- **Açıklama**: Vektörel format, tüm boyutlarda kaliteli görünüm
- **Kullanım**: Ana logo olarak tüm sayfalarda kullanılır

### 2. Favicon
- **Dosya**: `public/frontend/images/favicon.svg`
- **Açıklama**: 32x32 boyutunda favicon
- **Kullanım**: Tarayıcı sekmesinde ve bookmark'larda görünür

### 3. PNG Logo (Fallback)
- **Dosya**: `public/frontend/images/onlynote-logo.png`
- **Açıklama**: PNG formatında logo (SVG desteklenmeyen tarayıcılar için)
- **Boyut**: En az 200x200px, tercihen 400x400px
- **Kullanım**: SVG yüklenemediğinde otomatik olarak kullanılır

## Logo Tasarımı

Logo, verdiğiniz resimdeki tasarıma göre oluşturulmuştur:
- Mavi tonlarda dairesel tasarım
- Üst sağda açık kısım
- İçeride "N" harfi
- Mavi kurdeleler ile dinamik görünüm
- 3D gölge efektleri

## Kurulum Adımları

1. **SVG Logo**: Zaten oluşturuldu
2. **Favicon**: Zaten oluşturuldu
3. **PNG Logo**: Aşağıdaki adımları takip edin:

### PNG Logo Oluşturma

1. SVG dosyasını bir görsel editörde açın (Inkscape, Adobe Illustrator, Figma)
2. PNG formatında export edin
3. En az 200x200px boyutunda kaydedin
4. `public/frontend/images/onlynote-logo.png` konumuna kaydedin

### Alternatif Yöntem

1. SVG dosyasını tarayıcıda açın
2. Sayfayı PNG olarak kaydedin
3. Gerekirse boyutu ayarlayın
4. `public/frontend/images/onlynote-logo.png` konumuna kaydedin

## Test

Logo kurulumundan sonra:
1. Ana sayfayı ziyaret edin
2. Header'da logonun göründüğünü kontrol edin
3. Footer'da logonun göründüğünü kontrol edin
4. Admin panelinde logonun göründüğünü kontrol edin
5. Farklı tarayıcılarda test edin

## Sorun Giderme

### Logo Görünmüyor
- Dosya yollarını kontrol edin
- Dosya izinlerini kontrol edin
- Tarayıcı cache'ini temizleyin

### Logo Çok Büyük/Küçük
- CSS'te `height` ve `width` değerlerini ayarlayın
- SVG viewBox değerlerini kontrol edin

### Favicon Görünmüyor
- Favicon dosyasının doğru konumda olduğunu kontrol edin
- Tarayıcı cache'ini temizleyin
- Farklı tarayıcılarda test edin

## Not

- SVG formatı modern tarayıcılarda en iyi performansı sağlar
- PNG formatı eski tarayıcılar için fallback olarak kullanılır
- Logo boyutları responsive tasarım için optimize edilmiştir







