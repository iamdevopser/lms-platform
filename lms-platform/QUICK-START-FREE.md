# 🆓 Tamamen Ücretsiz AWS Deployment - Hızlı Başlangıç

## 💰 Maliyet: $0 (Tamamen Free Tier)

Bu kurulum **sadece AWS Free Tier** kaynaklarını kullanır. Demo/test için idealdir.

## ⚡ 3 Adımda Kurulum

### 1. AWS CLI Yapılandır

```bash
aws configure
# AWS Access Key ID: (IAM'den oluştur)
# AWS Secret Access Key: (IAM'den oluştur)
# Default region: us-east-1
# Default output format: json
```

### 2. Deploy Et

```bash
cd aws
chmod +x deploy-free-simple.sh
./deploy-free-simple.sh deploy
```

### 3. Instance'a Bağlan ve Kur

```bash
# Script'in gösterdiği SSH komutunu kullan
ssh -i lms-platform-free-key.pem ec2-user@<PUBLIC_IP>

# Projeyi klonla
git clone <your-repo-url> /home/ec2-user/lms-platform
cd /home/ec2-user/lms-platform

# .env dosyasını yapılandır
cp docker.env.example .env
nano .env  # DB_HOST=mysql, REDIS_HOST=redis, APP_URL=http://<PUBLIC_IP>

# Docker Compose ile başlat
docker-compose -f docker-compose.free-tier.yml up -d

# Migration ve seeder
docker-compose -f docker-compose.free-tier.yml exec app php artisan migrate --force
docker-compose -f docker-compose.free-tier.yml exec app php artisan db:seed --force
docker-compose -f docker-compose.free-tier.yml exec app php artisan storage:link
```

## ✅ Uygulamaya Eriş

```bash
# Tarayıcıda aç
http://<PUBLIC_IP>

# Health check
curl http://<PUBLIC_IP>/health
```

## 🛑 Test Bittiğinde

```bash
# Instance'ı durdur (maliyet tasarrufu)
INSTANCE_ID=$(aws cloudformation describe-stack-resources \
    --stack-name lms-platform-free-simple \
    --logical-resource-id EC2Instance \
    --query 'StackResources[0].PhysicalResourceId' \
    --output text)

aws ec2 stop-instances --instance-ids ${INSTANCE_ID}

# VEYA stack'i tamamen sil
cd aws
./deploy-free-simple.sh delete
```

## 📊 Kullanılan Kaynaklar

- ✅ **EC2 t2.micro**: 750 saat/ay FREE (12 ay)
- ✅ **S3**: 5GB FREE (12 ay)
- ✅ **VPC, Security Groups**: FREE
- ✅ **Elastic IP**: FREE (instance çalışırken)

**Toplam Maliyet: $0**

## ⚠️ Önemli Notlar

1. **Test bittikten sonra mutlaka instance'ı durdurun veya stack'i silin**
2. **Free Tier limiti**: 750 saat/ay (31 gün = 744 saat)
3. **Sadece demo/test için** kullanın, production için değil
4. **2-3 gün** kullanıp kapatabilirsiniz

## 📚 Detaylı Rehber

Detaylı kurulum rehberi için `AWS-FREE-DEPLOYMENT.md` dosyasına bakın.

---

**💰 Maliyet: $0 | ⏱️ Süre: 2-3 gün | 🎯 Amaç: Demo/Test**

