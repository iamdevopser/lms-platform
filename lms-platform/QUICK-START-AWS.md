# 🚀 LMS Platform AWS Quick Start Guide

## 📋 Ön Gereksinimler

### 1. AWS Hesabı
- AWS hesabı oluşturun: https://aws.amazon.com
- Free Tier kullanabilirsiniz (12 ay ücretsiz)
- Credit card gerekli (ücretlendirme yapılmaz)

### 2. Yerel Gereksinimler
```bash
# AWS CLI kurulumu
curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"
unzip awscliv2.zip
sudo ./aws/install

# Docker kurulumu (Ubuntu)
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

## 🏃‍♂️ Hızlı Deployment (5 Dakika)

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

### Adım 3: AWS'de Deploy Edin
```bash
# Deploy script'ini çalıştırın
cd aws
chmod +x deploy.sh
./deploy.sh
```

### Adım 4: Deployment'ı Kontrol Edin
```bash
# Stack durumunu kontrol edin
aws cloudformation describe-stacks --stack-name lms-platform-infrastructure

# ECS servis durumunu kontrol edin
aws ecs describe-services --cluster lms-platform-cluster --services lms-platform-service
```

## 💰 Maliyet Optimizasyonu

### Başlangıç Maliyeti (Aylık)
- **EC2 t3.medium**: $30-40
- **RDS db.t3.micro**: $15-20
- **ElastiCache t3.micro**: $10-15
- **S3 + CloudFront**: $5-10
- **ALB**: $16
- **Toplam**: ~$80-100

### Free Tier Kullanımı
- **EC2 t2.micro**: 750 saat/ay ücretsiz
- **RDS db.t2.micro**: 750 saat/ay ücretsiz
- **S3**: 5GB ücretsiz
- **CloudFront**: 1TB ücretsiz

## 🔧 Yönetim Komutları

### Servisleri Kontrol Etme
```bash
# Tüm servislerin durumu
aws ecs describe-services --cluster lms-platform-cluster

# Logları görüntüleme
aws logs tail /ecs/lms-platform --follow

# Database durumu
aws rds describe-db-instances --db-instance-identifier lms-platform-database
```

### Scaling
```bash
# ECS servis ölçeklendirme
aws ecs update-service --cluster lms-platform-cluster --service lms-platform-service --desired-count 3

# RDS instance büyütme
aws rds modify-db-instance --db-instance-identifier lms-platform-database --db-instance-class db.t3.small
```

### Backup
```bash
# RDS snapshot oluşturma
aws rds create-db-snapshot --db-instance-identifier lms-platform-database --db-snapshot-identifier lms-backup-$(date +%Y%m%d)

# S3 backup
aws s3 sync s3://lms-platform-assets-123456789 s3://lms-platform-backup-$(date +%Y%m%d)
```

## 🚨 Sorun Giderme

### Yaygın Sorunlar

#### 1. ECS Task Başlamıyor
```bash
# Task durumunu kontrol et
aws ecs describe-tasks --cluster lms-platform-cluster --tasks $(aws ecs list-tasks --cluster lms-platform-cluster --query 'taskArns[0]' --output text)

# Logları kontrol et
aws logs get-log-events --log-group-name /ecs/lms-platform --log-stream-name ecs/lms-app/$(aws ecs list-tasks --cluster lms-platform-cluster --query 'taskArns[0]' --output text | cut -d'/' -f3)
```

#### 2. Database Bağlantı Hatası
```bash
# Security Group kurallarını kontrol et
aws ec2 describe-security-groups --group-names lms-platform-db-sg

# RDS endpoint'i kontrol et
aws rds describe-db-instances --db-instance-identifier lms-platform-database --query 'DBInstances[0].Endpoint'
```

#### 3. Yüksek CPU Kullanımı
```bash
# CloudWatch metrics
aws cloudwatch get-metric-statistics --namespace AWS/ECS --metric-name CPUUtilization --dimensions Name=ServiceName,Value=lms-platform-service --start-time 2024-01-01T00:00:00Z --end-time 2024-01-02T00:00:00Z --period 300 --statistics Average
```

## 📊 Monitoring Dashboard

### CloudWatch Dashboard
1. AWS Console → CloudWatch → Dashboards
2. "LMS-Platform-Dashboard" dashboard'ını açın
3. CPU, Memory, Database metrics'lerini görüntüleyin

### ECS Console
1. AWS Console → ECS → Clusters
2. "lms-platform-cluster" cluster'ını seçin
3. Service ve Task durumlarını kontrol edin

## 🔄 CI/CD Pipeline

### GitHub Actions
1. Repository → Settings → Secrets
2. Şu secret'ları ekleyin:
   - `AWS_ACCESS_KEY_ID`
   - `AWS_SECRET_ACCESS_KEY`
3. `main` branch'e push yaptığınızda otomatik deploy olur

### Manuel Deploy
```bash
# Yeni image build et ve push et
docker build -f Dockerfile.aws -t lms-platform:latest .
aws ecr get-login-password --region us-east-1 | docker login --username AWS --password-stdin $(aws sts get-caller-identity --query Account --output text).dkr.ecr.us-east-1.amazonaws.com
docker tag lms-platform:latest $(aws sts get-caller-identity --query Account --output text).dkr.ecr.us-east-1.amazonaws.com/lms-platform:latest
docker push $(aws sts get-caller-identity --query Account --output text).dkr.ecr.us-east-1.amazonaws.com/lms-platform:latest

# ECS service'i güncelle
aws ecs update-service --cluster lms-platform-cluster --service lms-platform-service --force-new-deployment
```

## 🛡️ Güvenlik

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

## 📈 Ölçeklendirme Stratejisi

### 0-100 Kullanıcı
- EC2: t3.medium
- RDS: db.t3.micro
- ElastiCache: cache.t3.micro

### 100-1000 Kullanıcı
- EC2: t3.large + Auto Scaling
- RDS: db.t3.small
- ElastiCache: cache.t3.small

### 1000+ Kullanıcı
- ECS Fargate: 2-10 tasks
- RDS: db.r5.large + Read Replicas
- ElastiCache: cache.r5.large

## 💡 İpuçları

1. **Maliyet Tasarrufu**: Spot Instances kullanın
2. **Performance**: CloudFront cache ayarlarını optimize edin
3. **Monitoring**: CloudWatch alarms kurun
4. **Backup**: Otomatik backup policy'leri ayarlayın
5. **Security**: Regular security updates yapın

## 📞 Destek

- AWS Support: https://console.aws.amazon.com/support
- Documentation: https://docs.aws.amazon.com
- Community: https://forums.aws.amazon.com

Bu rehber ile AWS'de production-ready LMS platformunuzu 5 dakikada kurabilirsiniz! 🎉





