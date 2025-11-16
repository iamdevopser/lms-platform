# 🚀 EC2'de Hızlı Başlatma Rehberi

## 📋 Tek Komutla Sistem Başlatma

EC2 instance'a bağlandıktan sonra, sistemi tek komutla başlatabilirsiniz.

### Adım 1: Script'i İndirin (İlk Sefer)

EC2 instance'da:

```bash
cd /home/ec2-user/lms-platform

# Script'i GitHub'dan çekin veya oluşturun
# (Eğer script repo'da yoksa, aşağıdaki komutla oluşturun)
```

### Adım 2: Script'i Çalıştırılabilir Yapın

```bash
chmod +x start-lms.sh
```

### Adım 3: Script'i Çalıştırın

```bash
./start-lms.sh
```

## ✅ Script Ne Yapıyor?

Script otomatik olarak şunları yapar:

1. ✅ **Git güncellemesi** (opsiyonel)
2. ✅ **.env dosyası kontrolü** (yoksa oluşturur)
3. ✅ **Docker servislerini başlatır** (app + redis)
4. ✅ **Composer bağımlılıklarını yükler** (ilk seferde)
5. ✅ **SQLite database oluşturur**
6. ✅ **APP_KEY oluşturur** (yoksa)
7. ✅ **Migration çalıştırır**
8. ✅ **Seeder çalıştırır** (varsayılan kullanıcılar)
9. ✅ **Storage link oluşturur**
10. ✅ **Dosya izinlerini düzeltir**
11. ✅ **Cache temizler**
12. ✅ **Servislerin durumunu gösterir**
13. ✅ **Public IP'yi gösterir**

## 🔄 Script'i Tekrar Çalıştırma

Script **idempotent**'tir, yani:
- ✅ Birden fazla kez çalıştırılabilir
- ✅ Mevcut durumu kontrol eder
- ✅ Sadece eksik olanları yapar
- ✅ Hata vermez

**Örnek kullanım:**
```bash
# Her zaman aynı komut
./start-lms.sh
```

## 🛠️ Manuel Kontrol (İsteğe Bağlı)

Eğer bir sorun olursa, manuel kontrol için:

```bash
# Servislerin durumunu kontrol et
docker-compose -f docker-compose.free-tier.yml ps

# Logları görüntüle
docker-compose -f docker-compose.free-tier.yml logs -f app

# Servisleri yeniden başlat
docker-compose -f docker-compose.free-tier.yml restart app
```

## 📝 Notlar

- Script yaklaşık **2-3 dakika** sürer (ilk seferde daha uzun olabilir)
- Tüm işlemler otomatiktir, müdahale gerektirmez
- Hata durumunda script durur ve hata mesajı gösterir

---

**🎉 Artık tek komutla sistemi başlatabilirsiniz!**

