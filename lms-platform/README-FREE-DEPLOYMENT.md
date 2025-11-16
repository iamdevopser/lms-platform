# 🆓 Tamamen Ücretsiz AWS Deployment - Hızlı Başlangıç

## 💰 Maliyet: $0

Bu kurulum **tamamen ücretsiz** AWS Free Tier kaynaklarını kullanır. Demo/test için idealdir.

## 🚀 3 Adımda Kurulum

### 1. AWS CLI Yapılandırma

```bash
aws configure
# AWS Access Key ID: (IAM'den oluşturun)
# AWS Secret Access Key: (IAM'den oluşturun)
# Default region: us-east-1
# Default output format: json
```

### 2. Deployment

```bash
cd aws
chmod +x deploy-free-simple.sh
./deploy-free-simple.sh deploy
```

### 3. Instance'a Bağlan ve Kur

```bash
# Script'in gösterdiği SSH komutunu kullanın
ssh -i lms-platform-free-key.pem ec2-user@<PUBLIC_IP>

# Projeyi klonla
git clone <your-repo-url> /home/ec2-user/lms-platform
cd /home/ec2-user/lms-platform

# .env dosyasını yapılandır
nano .env

# Docker Compose ile başlat
docker-compose -f docker-compose.free-tier.yml up -d

# Migration ve seeder
docker-compose -f docker-compose.free-tier.yml exec app php artisan migrate --force
docker-compose -f docker-compose.free-tier.yml exec app php artisan db:seed --force
```

## 🛑 Test Bittiğinde

```bash
# Instance'ı durdur (maliyet tasarrufu)
aws ec2 stop-instances --instance-ids <INSTANCE_ID>

# Veya stack'i tamamen sil
cd aws
./deploy-free-simple.sh delete
```

## 📊 Kullanılan Kaynaklar

- ✅ EC2 t2.micro: 750 saat/ay FREE
- ✅ S3: 5GB FREE
- ✅ VPC, Security Groups: FREE
- ✅ Elastic IP: FREE (instance çalışırken)

**Toplam: $0**

## 📚 Detaylı Rehber

Detaylı kurulum rehberi için `AWS-FREE-DEPLOYMENT.md` dosyasına bakın.

## ⚠️ Önemli

- Test bittikten sonra **mutlaka instance'ı durdurun veya stack'i silin**
- Free Tier limiti: **750 saat/ay** (31 gün = 744 saat)
- Sadece **demo/test** için kullanın, production için değil

---

**Maliyet: $0 | Süre: 2-3 gün | Amaç: Demo/Test**

