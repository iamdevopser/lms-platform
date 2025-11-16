# 🆓 Tamamen Ücretsiz AWS Free Tier Deployment Rehberi

Bu rehber, OnliNote LMS platformunu **tamamen ücretsiz** bir şekilde AWS Free Tier üzerinde nasıl kuracağınızı anlatmaktadır. **$0 maliyet** ile demo/test için kullanabilirsiniz.

## 💰 Maliyet: $0

Bu kurulum sadece AWS Free Tier kaynaklarını kullanır:
- ✅ **EC2 t2.micro**: 750 saat/ay FREE (12 ay)
- ✅ **S3**: 5GB FREE (12 ay)
- ✅ **VPC, Security Groups**: FREE
- ✅ **Elastic IP**: FREE (instance çalışırken)
- ✅ **CloudWatch Logs**: FREE (5GB)

**Toplam Maliyet: $0**

## 📋 Ön Gereksinimler

### 1. AWS Hesabı
- Yeni bir AWS hesabı (Free Tier'e uygun)
- AWS CLI kurulu ve yapılandırılmış

### 2. Yerel Gereksinimler
- Git
- SSH client
- Text editor

### 3. AWS CLI Kurulumu ve Yapılandırması

```bash
# AWS CLI yapılandır
aws configure
# AWS Access Key ID: (IAM'den oluşturun)
# AWS Secret Access Key: (IAM'den oluşturun)
# Default region: us-east-1
# Default output format: json
```

### 4. IAM Kullanıcı Oluşturma

1. AWS Console > IAM > Users > Add users
2. Kullanıcı adı: `lms-platform-deploy`
3. Programmatic access seçin
4. Şu policy'leri ekleyin:
   - `AmazonEC2FullAccess`
   - `AmazonS3FullAccess`
   - `CloudFormationFullAccess`
   - `IAMFullAccess` (sadece stack oluşturma için)

## 🚀 Hızlı Başlangıç

### Adım 1: Projeyi Hazırlama

```bash
# Projeyi klonlayın
git clone <your-repo-url>
cd lms-platform

# Environment dosyasını hazırlayın
cp docker.env.example .env
nano .env  # Gerekli ayarları yapın
```

### Adım 2: AWS Deployment

```bash
# Deployment script'ini çalıştırın
cd aws
chmod +x deploy-free-simple.sh
./deploy-free-simple.sh deploy
```

Bu script şunları yapar:
1. EC2 key pair oluşturur
2. CloudFormation stack'i oluşturur (EC2 instance, VPC, S3)
3. Instance'a Docker ve Docker Compose kurar
4. Bağlantı bilgilerini gösterir

### Adım 3: Instance'a Bağlanma ve Kurulum

```bash
# SSH ile bağlanın (script'in gösterdiği komutu kullanın)
ssh -i lms-platform-free-key.pem ec2-user@<PUBLIC_IP>

# Projeyi klonlayın (veya SCP ile yükleyin)
git clone <your-repo-url> /home/ec2-user/lms-platform
cd /home/ec2-user/lms-platform

# .env dosyasını yapılandırın
nano .env
# DB_HOST=mysql
# REDIS_HOST=redis
# APP_URL=http://<PUBLIC_IP>
# vs.

# Docker Compose ile başlatın
docker-compose -f docker-compose.free-tier.yml up -d

# Migration çalıştırın
docker-compose -f docker-compose.free-tier.yml exec app php artisan migrate --force

# Seeder çalıştırın
docker-compose -f docker-compose.free-tier.yml exec app php artisan db:seed --force

# Storage link oluşturun
docker-compose -f docker-compose.free-tier.yml exec app php artisan storage:link
```

### Adım 4: Uygulamayı Test Etme

```bash
# Tarayıcıda açın
http://<PUBLIC_IP>

# Health check
curl http://<PUBLIC_IP>/health
```

## 🛑 Instance'ı Durdurma (Maliyet Tasarrufu)

Test bittikten sonra instance'ı durdurun:

```bash
# Instance ID'yi alın
INSTANCE_ID=$(aws cloudformation describe-stack-resources \
    --stack-name lms-platform-free-simple \
    --logical-resource-id EC2Instance \
    --query 'StackResources[0].PhysicalResourceId' \
    --output text)

# Instance'ı durdurun
aws ec2 stop-instances --instance-ids ${INSTANCE_ID}

# Instance'ı başlatın (tekrar kullanmak için)
aws ec2 start-instances --instance-ids ${INSTANCE_ID}
```

## 🗑️ Stack'i Silme (Tamamen Temizleme)

Test bittikten sonra tüm kaynakları silin:

```bash
# Stack'i sil
cd aws
./deploy-free-simple.sh delete

# Veya manuel olarak
aws cloudformation delete-stack --stack-name lms-platform-free-simple
```

## 📊 Kullanılan Kaynaklar

### EC2 Instance (t2.micro)
- **CPU**: 1 vCPU
- **RAM**: 1 GB
- **Storage**: 8 GB (EBS)
- **Maliyet**: $0 (Free Tier - 750 saat/ay)

### Docker Containers (EC2 üzerinde)
- **MySQL 8.0**: Database
- **Redis 7**: Cache
- **Laravel App**: Web uygulaması
- **Nginx**: Web server

### S3 Bucket
- **Storage**: 5 GB FREE
- **Maliyet**: $0 (Free Tier)

## 🔧 Yönetim Komutları

### Stack Bilgilerini Görüntüleme

```bash
cd aws
./deploy-free-simple.sh info
```

### Logları Görüntüleme

```bash
# SSH ile bağlanın
ssh -i lms-platform-free-key.pem ec2-user@<PUBLIC_IP>

# Docker logs
docker-compose -f docker-compose.free-tier.yml logs -f

# Laravel logs
docker-compose -f docker-compose.free-tier.yml exec app tail -f storage/logs/laravel.log
```

### Uygulamayı Yeniden Başlatma

```bash
# SSH ile bağlanın
ssh -i lms-platform-free-key.pem ec2-user@<PUBLIC_IP>

# Containers'ı yeniden başlat
cd /home/ec2-user/lms-platform
docker-compose -f docker-compose.free-tier.yml restart
```

## ⚠️ Önemli Notlar

### Free Tier Limitleri
- **EC2**: 750 saat/ay (31 gün = 744 saat)
- **S3**: 5GB storage
- **Data Transfer**: 1GB/ay
- **Süre**: 12 ay (hesap oluşturma tarihinden itibaren)

### Maliyet Uyarıları
1. **Instance'ı kullanmadığınızda durdurun** (maliyet tasarrufu)
2. **Free Tier limitlerini aşmayın** (ücretlendirme başlar)
3. **Test bittikten sonra stack'i silin** (kaynakları serbest bırakın)
4. **S3'te gereksiz dosya bırakmayın** (5GB limiti)

### Performans
- **t2.micro** düşük performanslı bir instance'dır
- Demo/test için yeterlidir
- Production için önerilmez

### Güvenlik
- **SSH key'i güvenli tutun**
- **Security group'ları kontrol edin**
- **.env dosyasında hassas bilgileri saklamayın**

## 🐛 Sorun Giderme

### Instance'a Bağlanamıyorum

```bash
# Security group'u kontrol edin
aws ec2 describe-security-groups --filters "Name=tag:Name,Values=lms-platform-free-web-sg"

# Instance durumunu kontrol edin
aws ec2 describe-instances --filters "Name=tag:Name,Values=lms-platform-free-instance"
```

### Uygulama Çalışmıyor

```bash
# SSH ile bağlanın ve logları kontrol edin
ssh -i lms-platform-free-key.pem ec2-user@<PUBLIC_IP>
docker-compose -f docker-compose.free-tier.yml logs -f app
```

### Database Bağlantı Hatası

```bash
# MySQL container'ını kontrol edin
docker-compose -f docker-compose.free-tier.yml ps mysql
docker-compose -f docker-compose.free-tier.yml logs mysql
```

### Disk Alanı Doldu

```bash
# Disk kullanımını kontrol edin
df -h

# Docker images'ları temizleyin
docker system prune -a

# Logları temizleyin
docker-compose -f docker-compose.free-tier.yml exec app php artisan log:clear
```

## 📚 Ek Kaynaklar

- [AWS Free Tier](https://aws.amazon.com/free/)
- [EC2 Free Tier](https://aws.amazon.com/ec2/pricing/free-tier/)
- [Docker Documentation](https://docs.docker.com/)
- [Laravel Documentation](https://laravel.com/docs)

## 🎉 Özet

Bu kurulum ile:
- ✅ **$0 maliyet** ile demo/test yapabilirsiniz
- ✅ **2-3 gün** kullanıp kapatabilirsiniz
- ✅ **Tamamen ücretsiz** AWS Free Tier kaynakları kullanılır
- ✅ **Kolay kurulum** ve yönetim
- ✅ **Hızlı temizleme** (stack silme)

**Test bittikten sonra mutlaka stack'i silin ve instance'ı durdurun!**

---

**Son Güncelleme**: 2024
**Maliyet**: $0 (Free Tier)
**Süre**: 2-3 gün (demo/test)

