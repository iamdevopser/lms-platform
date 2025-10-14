# 🆓 AWS Free Tier ile LMS Platform - Hızlı Başlangıç

## 🎯 Free Tier Avantajları

- **💰 Maliyet**: $0-5/ay (Free Tier limitleri içinde)
- **⏱️ Süre**: 12 ay ücretsiz kullanım
- **🚀 Hızlı**: 5 dakikada kurulum
- **📈 Ölçeklenebilir**: İhtiyaç halinde büyütülebilir

## 📋 Ön Gereksinimler

### 1. AWS Hesabı
- [AWS Free Tier hesabı oluşturun](https://aws.amazon.com/free/)
- Credit card gerekli (ücretlendirme yapılmaz)
- Telefon doğrulaması gerekli

### 2. Yerel Gereksinimler
```bash
# AWS CLI kurulumu
curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"
unzip awscliv2.zip
sudo ./aws/install

# Docker kurulumu
sudo apt-get update
sudo apt-get install docker.io
sudo usermod -aG docker $USER

# Git kurulumu
sudo apt-get install git
```

### 3. AWS Konfigürasyonu
```bash
aws configure
# AWS Access Key ID: [your-access-key]
# AWS Secret Access Key: [your-secret-key]
# Default region name: us-east-1
# Default output format: json
```

## 🚀 5 Dakikada Kurulum

### Adım 1: Projeyi Klonlayın
```bash
git clone <your-repository-url>
cd lms-platform
```

### Adım 2: Environment Dosyasını Oluşturun
```bash
cp docker.env.example .env
nano .env  # Gerekli ayarları yapın
```

### Adım 3: Free Tier'de Deploy Edin
```bash
cd aws
chmod +x free-tier-deploy.sh
./free-tier-deploy.sh
```

### Adım 4: Deployment'ı Kontrol Edin
```bash
# Stack durumunu kontrol edin
aws cloudformation describe-stacks --stack-name lms-platform-free-infrastructure

# ECS servis durumunu kontrol edin
aws ecs describe-services --cluster lms-platform-free-cluster --services lms-platform-free-service
```

## 🆓 Free Tier Limitleri

| Servis | Free Tier Limit | Aylık Tasarruf |
|--------|----------------|----------------|
| **EC2 t2.micro** | 750 saat | $8.50 |
| **RDS db.t2.micro** | 750 saat | $15 |
| **ElastiCache t2.micro** | 750 saat | $13 |
| **S3** | 5GB | $0.12 |
| **CloudFront** | 1TB | $85 |
| **ALB** | 750 saat | $16 |
| **EBS** | 30GB | $3 |
| **Toplam Tasarruf** | | **~$140/ay** |

## 🏗️ Free Tier Architecture

```
┌─────────────────────────────────────┐
│                VPC                  │
│  ┌─────────────────────────────────┐│
│  │        Public Subnet            ││
│  │  ┌─────────────┐ ┌─────────────┐││
│  │  │   ECS Task  │ │   ALB       │││
│  │  │  (Fargate)  │ │             │││
│  │  └─────────────┘ └─────────────┘││
│  └─────────────────────────────────┘│
│  ┌─────────────────────────────────┐│
│  │       Private Subnet            ││
│  │  ┌─────────────┐ ┌─────────────┐││
│  │  │     RDS     │ │ ElastiCache │││
│  │  │   MySQL     │ │   Redis     │││
│  │  └─────────────┘ └─────────────┘││
│  └─────────────────────────────────┘│
└─────────────────────────────────────┘
```

## 💰 Maliyet Optimizasyonu

### Free Tier Kullanımı
- **EC2 t2.micro**: 750 saat/ay (31 gün = 744 saat)
- **RDS db.t2.micro**: 750 saat/ay
- **ElastiCache t2.micro**: 750 saat/ay
- **S3**: 5GB storage
- **CloudFront**: 1TB data transfer
- **ALB**: 750 saat/ay

### Optimizasyon Stratejileri
1. **Single AZ**: Multi-AZ kullanmayın
2. **Minimal Resources**: Minimum CPU/Memory
3. **Short Retention**: Backup retention'ı kısaltın
4. **Spot Instances**: Development için kullanın
5. **Lifecycle Policies**: S3 için lifecycle policy

## 🔧 Yönetim Komutları

### Servisleri Kontrol Etme
```bash
# Tüm servislerin durumu
aws ecs describe-services --cluster lms-platform-free-cluster

# Logları görüntüleme
aws logs tail /ecs/lms-platform-free --follow

# Database durumu
aws rds describe-db-instances --db-instance-identifier lms-platform-free-database
```

### Scaling (Free Tier Limitleri İçinde)
```bash
# ECS servis ölçeklendirme (1 instance max)
aws ecs update-service --cluster lms-platform-free-cluster --service lms-platform-free-service --desired-count 1

# RDS instance büyütme (Free Tier limitleri içinde)
aws rds modify-db-instance --db-instance-identifier lms-platform-free-database --db-instance-class db.t2.micro
```

### Backup (Free Tier Limitleri İçinde)
```bash
# RDS snapshot oluşturma
aws rds create-db-snapshot --db-instance-identifier lms-platform-free-database --db-snapshot-identifier lms-backup-$(date +%Y%m%d)

# S3 backup
aws s3 sync s3://lms-platform-free-assets-123456789 s3://lms-platform-free-backup-$(date +%Y%m%d)
```

## 🚨 Free Tier Uyarıları

### Önemli Limitler
1. **EC2 t2.micro**: 750 saat/ay (31 gün = 744 saat)
2. **RDS db.t2.micro**: 750 saat/ay
3. **ElastiCache t2.micro**: 750 saat/ay
4. **S3**: 5GB storage
5. **CloudFront**: 1TB data transfer
6. **ALB**: 750 saat/ay
7. **EBS**: 30GB storage
8. **Data Transfer**: 1GB out

### Maliyet Uyarıları
- **$1**: Free Tier uyarısı
- **$5**: Free Tier limiti
- **$10**: Acil durdurma

## 📊 Monitoring Dashboard

### CloudWatch Dashboard
1. AWS Console → CloudWatch → Dashboards
2. "LMS-Platform-Free-Tier" dashboard'ını açın
3. CPU, Memory, Database metrics'lerini görüntüleyin

### Free Tier Usage
1. AWS Console → Billing → Free Tier
2. Kullanım durumunu kontrol edin
3. Limitlere yaklaştığınızda uyarı alın

## 🔄 CI/CD Pipeline (Free Tier)

### GitHub Actions
1. Repository → Settings → Secrets
2. Şu secret'ları ekleyin:
   - `AWS_ACCESS_KEY_ID`
   - `AWS_SECRET_ACCESS_KEY`
3. `main` branch'e push yaptığınızda otomatik deploy olur

### Manuel Deploy
```bash
# Yeni image build et ve push et
docker build -f Dockerfile.free-tier -t lms-platform-free:latest .
aws ecr get-login-password --region us-east-1 | docker login --username AWS --password-stdin $(aws sts get-caller-identity --query Account --output text).dkr.ecr.us-east-1.amazonaws.com
docker tag lms-platform-free:latest $(aws sts get-caller-identity --query Account --output text).dkr.ecr.us-east-1.amazonaws.com/lms-platform-free:latest
docker push $(aws sts get-caller-identity --query Account --output text).dkr.ecr.us-east-1.amazonaws.com/lms-platform-free:latest

# ECS service'i güncelle
aws ecs update-service --cluster lms-platform-free-cluster --service lms-platform-free-service --force-new-deployment
```

## 🛡️ Güvenlik (Free Tier)

### SSL Sertifikası
```bash
# Certificate Manager'da sertifika oluştur
aws acm request-certificate --domain-name yourdomain.com --subject-alternative-names "*.yourdomain.com" --validation-method DNS

# Route 53'te domain'i yapılandır
aws route53 create-hosted-zone --name yourdomain.com --caller-reference $(date +%s)
```

### Security Groups
- Sadece gerekli portları açın
- IP whitelist kullanın
- Regular security audit yapın

## 📈 Free Tier'den Production'a Geçiş

### 1. Free Tier Limitlerini Aştığınızda
```bash
# RDS instance büyütme
aws rds modify-db-instance --db-instance-identifier lms-platform-free-database --db-instance-class db.t3.small

# ECS task büyütme
aws ecs register-task-definition --family lms-platform-free-task --cpu 512 --memory 1024

# ElastiCache büyütme
aws elasticache modify-cache-cluster --cache-cluster-id lms-platform-free-cache --cache-node-type cache.t3.small
```

### 2. Multi-AZ'e Geçiş
```bash
# RDS Multi-AZ
aws rds modify-db-instance --db-instance-identifier lms-platform-free-database --multi-az

# ElastiCache Cluster
aws elasticache create-cache-cluster --cache-cluster-id lms-platform-free-cache-cluster --cache-node-type cache.t3.small --num-cache-nodes 2
```

## 💡 Free Tier İpuçları

1. **Maliyet Tasarrufu**: Spot Instances kullanın
2. **Performance**: CloudFront cache ayarlarını optimize edin
3. **Monitoring**: CloudWatch alarms kurun
4. **Backup**: Otomatik backup policy'leri ayarlayın
5. **Security**: Regular security updates yapın
6. **Usage**: Free Tier kullanımını düzenli kontrol edin

## 📞 Destek

- AWS Support: https://console.aws.amazon.com/support
- Free Tier Documentation: https://aws.amazon.com/free/
- Community: https://forums.aws.amazon.com

## 🎉 Sonuç

Bu rehber ile AWS Free Tier kullanarak **$0-5/ay** maliyetle LMS platformunuzu kurabilirsiniz!

### ✅ Başarılı Kurulum Sonrası
- 🌐 Web uygulaması erişilebilir
- 🗄️ Veritabanı çalışıyor
- 🔴 Redis cache aktif
- 📦 S3 file storage hazır
- 🌍 CloudFront CDN aktif
- 📊 Monitoring dashboard hazır

### 🚀 Sonraki Adımlar
1. Domain name satın alın
2. SSL sertifikası kurun
3. Monitoring ayarlarını yapılandırın
4. Backup stratejisini uygulayın
5. Free Tier limitlerini düzenli kontrol edin

**Free Tier ile başlayın, ihtiyaç halinde ölçeklendirin!** 🆓





