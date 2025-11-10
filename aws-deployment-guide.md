# 🚀 AWS Free Tier - LMS Platform Kurulum Rehberi

Bu rehber, OnliNote LMS platformunu AWS Free Tier kullanarak tamamen ücretsiz bir şekilde nasıl kuracağınızı adım adım anlatmaktadır.

## 📋 İçindekiler

1. [Ön Gereksinimler](#ön-gereksinimler)
2. [AWS Free Tier Limitleri](#aws-free-tier-limitleri)
3. [Hazırlık Adımları](#hazırlık-adımları)
4. [AWS Kurulum Adımları](#aws-kurulum-adımları)
5. [Uygulama Deployment](#uygulama-deployment)
6. [Veritabanı Kurulumu](#veritabanı-kurulumu)
7. [Test ve Doğrulama](#test-ve-doğrulama)
8. [Maliyet Optimizasyonu](#maliyet-optimizasyonu)
9. [Sorun Giderme](#sorun-giderme)

## 🎯 Ön Gereksinimler

### 1. AWS Hesabı
- Yeni bir AWS hesabı oluşturun (12 ay içinde Free Tier'e uygunsunuz)
- AWS hesabınızda kredi kartı bilgisi olmalı (kullanılmayacak, sadece doğrulama için)
- AWS Console'a giriş yapın: https://console.aws.amazon.com

### 2. Yerel Gereksinimler
- Git kurulu
- Docker Desktop kurulu (Windows/Mac) veya Docker Engine (Linux)
- AWS CLI v2 kurulu
- Bir text editor (VS Code önerilir)

### 3. AWS CLI Kurulumu

#### Windows (PowerShell)
```powershell
# AWS CLI v2 indirme ve kurulum
# https://aws.amazon.com/cli/ adresinden indirin
aws --version
```

#### macOS
```bash
brew install awscli
aws --version
```

#### Linux
```bash
curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"
unzip awscliv2.zip
sudo ./aws/install
aws --version
```

### 4. AWS CLI Yapılandırması
```bash
aws configure
```
Şunları girin:
- AWS Access Key ID: (IAM'den oluşturun)
- AWS Secret Access Key: (IAM'den oluşturun)
- Default region: `us-east-1` (Free Tier için önerilir)
- Default output format: `json`

### 5. IAM Kullanıcı ve Access Key Oluşturma

1. AWS Console'da IAM servisine gidin
2. "Users" > "Add users" tıklayın
3. Kullanıcı adı: `lms-platform-deploy`
4. "Programmatic access" seçin
5. "Attach existing policies directly" seçin
6. Şu policy'leri ekleyin:
   - `AmazonEC2FullAccess`
   - `AmazonRDSFullAccess`
   - `AmazonECS_FullAccess`
   - `AmazonElastiCacheFullAccess`
   - `AmazonS3FullAccess`
   - `AmazonEC2ContainerRegistryFullAccess`
   - `CloudFormationFullAccess`
   - `IAMFullAccess`
   - `AmazonCloudWatchFullAccess`
7. Access Key ID ve Secret Access Key'i kaydedin

## 💰 AWS Free Tier Limitleri

| Servis | Free Tier Limit | Süre |
|--------|----------------|------|
| **EC2 t3.micro** | 750 saat/ay | 12 ay |
| **RDS db.t3.micro** | 750 saat/ay | 12 ay |
| **ElastiCache cache.t3.micro** | 750 saat/ay | 12 ay |
| **S3** | 5GB storage | 12 ay |
| **Data Transfer** | 1GB/ay | 12 ay |
| **CloudWatch** | 10 metrik, 1M API isteği | 12 ay |

**⚠️ Önemli Notlar:**
- Application Load Balancer (ALB) Free Tier'de yok, aylık ~$16 maliyet
- CloudFront Free Tier'de 1TB transfer var ama kullanmayacağız
- Free Tier limitlerini aşarsanız ücretlendirme başlar

## 🛠️ Hazırlık Adımları

### 1. Projeyi Klonlayın
```bash
git clone <your-repo-url>
cd lms-platform
```

### 2. Proje Yapısını Kontrol Edin
```bash
# Dockerfile.free-tier dosyasının var olduğunu kontrol edin
ls -la Dockerfile.free-tier

# AWS deployment scriptlerini kontrol edin
ls -la aws/free-tier-deploy.sh
ls -la aws/free-tier-infrastructure.yml
```

### 3. Environment Dosyasını Hazırlayın
```bash
# docker.env.example'dan kopyalayın
cp docker.env.example .env

# .env dosyasını düzenleyin
nano .env
```

Gerekli değişkenler:
```env
APP_NAME="OnliNote LMS"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-alb-url.us-east-1.elb.amazonaws.com

DB_CONNECTION=mysql
DB_HOST=<RDS-endpoint>
DB_PORT=3306
DB_DATABASE=lms_platform
DB_USERNAME=admin
DB_PASSWORD=<secure-password>

REDIS_HOST=<ElastiCache-endpoint>
REDIS_PORT=6379

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
```

## 🚀 AWS Kurulum Adımları

### Adım 1: ECR Repository Oluşturma

```bash
# AWS hesap ID'sini alın
AWS_ACCOUNT_ID=$(aws sts get-caller-identity --query Account --output text)
AWS_REGION="us-east-1"
ECR_REPOSITORY="lms-platform-free"

# ECR repository oluşturun
aws ecr create-repository \
    --repository-name ${ECR_REPOSITORY} \
    --region ${AWS_REGION} \
    --image-scanning-configuration scanOnPush=true \
    --image-tag-mutability MUTABLE
```

### Adım 2: Docker Image Build ve Push

```bash
# ECR'ye login olun
aws ecr get-login-password --region ${AWS_REGION} | \
    docker login --username AWS --password-stdin \
    ${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com

# Docker image'ı build edin
docker build -f Dockerfile.free-tier -t ${ECR_REPOSITORY}:latest .

# Image'ı tag'leyin
docker tag ${ECR_REPOSITORY}:latest \
    ${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com/${ECR_REPOSITORY}:latest

# Image'ı ECR'ye push edin
docker push ${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com/${ECR_REPOSITORY}:latest
```

### Adım 3: CloudFormation Stack Oluşturma

```bash
# Güvenli bir database şifresi oluşturun
DB_PASSWORD=$(openssl rand -base64 32)

# CloudFormation stack'i oluşturun
cd aws
aws cloudformation create-stack \
    --stack-name lms-platform-free-infrastructure \
    --template-body file://free-tier-infrastructure.yml \
    --capabilities CAPABILITY_IAM \
    --region ${AWS_REGION} \
    --parameters \
        ParameterKey=ProjectName,ParameterValue=lms-platform-free \
        ParameterKey=DatabasePassword,ParameterValue=${DB_PASSWORD}

# Stack'in oluşturulmasını bekleyin (15-20 dakika sürebilir)
aws cloudformation wait stack-create-complete \
    --stack-name lms-platform-free-infrastructure \
    --region ${AWS_REGION}
```

### Adım 4: Stack Output'larını Alma

```bash
# Stack output'larını alın
aws cloudformation describe-stacks \
    --stack-name lms-platform-free-infrastructure \
    --region ${AWS_REGION} \
    --query 'Stacks[0].Outputs'

# Önemli değerleri kaydedin
DB_ENDPOINT=$(aws cloudformation describe-stacks \
    --stack-name lms-platform-free-infrastructure \
    --region ${AWS_REGION} \
    --query 'Stacks[0].Outputs[?OutputKey==`DatabaseEndpoint`].OutputValue' \
    --output text)

REDIS_ENDPOINT=$(aws cloudformation describe-stacks \
    --stack-name lms-platform-free-infrastructure \
    --region ${AWS_REGION} \
    --query 'Stacks[0].Outputs[?OutputKey==`RedisEndpoint`].OutputValue' \
    --output text)

ALB_URL=$(aws cloudformation describe-stacks \
    --stack-name lms-platform-free-infrastructure \
    --region ${AWS_REGION} \
    --query 'Stacks[0].Outputs[?OutputKey==`LoadBalancerURL`].OutputValue' \
    --output text)

echo "Database Endpoint: ${DB_ENDPOINT}"
echo "Redis Endpoint: ${REDIS_ENDPOINT}"
echo "Load Balancer URL: ${ALB_URL}"
```

## 🗄️ Veritabanı Kurulumu

### Adım 1: RDS'e Bağlanma

```bash
# MySQL client ile bağlanın (yerel MySQL client gerekli)
mysql -h ${DB_ENDPOINT} -u admin -p

# Veya AWS Systems Manager Session Manager kullanın
```

### Adım 2: Laravel Migration ve Seeder Çalıştırma

ECS task'ı içinde migration çalıştırmak için:

```bash
# ECS task'ı oluşturun (geçici olarak)
aws ecs run-task \
    --cluster lms-platform-free-cluster \
    --task-definition lms-platform-free-task \
    --launch-type FARGATE \
    --network-configuration "awsvpcConfiguration={subnets=[subnet-xxx],securityGroups=[sg-xxx],assignPublicIp=ENABLED}" \
    --overrides '{
        "containerOverrides": [{
            "name": "lms-app",
            "command": ["php", "artisan", "migrate", "--force"]
        }]
    }'

# Seeder çalıştırın
aws ecs run-task \
    --cluster lms-platform-free-cluster \
    --task-definition lms-platform-free-task \
    --launch-type FARGATE \
    --network-configuration "awsvpcConfiguration={subnets=[subnet-xxx],securityGroups=[sg-xxx],assignPublicIp=ENABLED}" \
    --overrides '{
        "containerOverrides": [{
            "name": "lms-app",
            "command": ["php", "artisan", "db:seed", "--force"]
        }]
    }'
```

**Alternatif Yöntem:** ECS task definition'ına startup script ekleyin:

```yaml
# ECS Task Definition'a ekleyin
ContainerDefinitions:
  - Name: lms-app
    EntryPoint: ["/bin/sh", "-c"]
    Command:
      - |
        php artisan migrate --force &&
        php artisan db:seed --force &&
        /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
```

## 🧪 Test ve Doğrulama

### 1. Health Check
```bash
# Health endpoint'i test edin
curl http://${ALB_URL}/health

# Beklenen yanıt:
# {"status":"healthy","timestamp":"...","checks":{"database":"healthy","cache":"healthy","redis":"healthy"}}
```

### 2. Uygulamayı Açın
```bash
# Tarayıcıda açın
echo "Uygulama URL: http://${ALB_URL}"
```

### 3. Log Kontrolü
```bash
# CloudWatch loglarını kontrol edin
aws logs tail /ecs/lms-platform-free --follow --region ${AWS_REGION}
```

## 💡 Maliyet Optimizasyonu

### 1. Free Tier Limitlerini İzleme
```bash
# AWS Cost Explorer'ı kontrol edin
# https://console.aws.amazon.com/cost-management/home#/cost-explorer
```

### 2. Billing Alarm Oluşturma
```bash
# SNS topic oluşturun
aws sns create-topic --name lms-platform-billing-alerts

# CloudWatch alarm oluşturun
aws cloudwatch put-metric-alarm \
    --alarm-name lms-platform-billing-alert \
    --alarm-description "Alert when charges exceed $5" \
    --metric-name EstimatedCharges \
    --namespace AWS/Billing \
    --statistic Maximum \
    --period 86400 \
    --threshold 5.0 \
    --comparison-operator GreaterThanThreshold \
    --evaluation-periods 1
```

### 3. Kullanılmayan Kaynakları Temizleme
```bash
# Stack'i silmek için
aws cloudformation delete-stack \
    --stack-name lms-platform-free-infrastructure \
    --region ${AWS_REGION}
```

## 🔧 Sorun Giderme

### Problem 1: ECS Task Başlamıyor
```bash
# Task loglarını kontrol edin
aws logs tail /ecs/lms-platform-free --follow

# Task durumunu kontrol edin
aws ecs describe-tasks \
    --cluster lms-platform-free-cluster \
    --tasks <task-id>
```

### Problem 2: Veritabanı Bağlantı Hatası
```bash
# Security group kurallarını kontrol edin
aws ec2 describe-security-groups \
    --filters "Name=tag:Name,Values=lms-platform-free-db-sg"

# RDS endpoint'i kontrol edin
aws rds describe-db-instances \
    --db-instance-identifier lms-platform-free-database
```

### Problem 3: Redis Bağlantı Hatası
```bash
# ElastiCache endpoint'i kontrol edin
aws elasticache describe-cache-clusters \
    --cache-cluster-id lms-platform-free-cache \
    --show-cache-node-info
```

### Problem 4: ALB Health Check Başarısız
```bash
# Target group health'i kontrol edin
aws elbv2 describe-target-health \
    --target-group-arn <target-group-arn>

# Health check endpoint'ini test edin
curl http://<private-ip>/health
```

## 📊 Monitoring

### CloudWatch Dashboard
```bash
# Dashboard oluşturun (AWS Console'dan)
# https://console.aws.amazon.com/cloudwatch/home?region=us-east-1#dashboards:
```

### Önemli Metrikler
- ECS: CPUUtilization, MemoryUtilization
- RDS: CPUUtilization, FreeableMemory, DatabaseConnections
- ElastiCache: CPUUtilization, NetworkBytesIn, NetworkBytesOut
- ALB: TargetResponseTime, HealthyHostCount

## 🎉 Başarılı Kurulum Sonrası

1. ✅ Uygulama çalışıyor
2. ✅ Veritabanı bağlantısı başarılı
3. ✅ Redis cache çalışıyor
4. ✅ Health check başarılı
5. ✅ Migration ve seeder çalıştırıldı
6. ✅ S3 bucket hazır
7. ✅ Logging aktif

## 📝 Sonraki Adımlar

1. **Domain Name Ekleme:** Route 53 ile domain ekleyin
2. **SSL Certificate:** ACM ile SSL sertifikası ekleyin
3. **CDN:** CloudFront ekleyin (Free Tier 1TB)
4. **Backup:** RDS snapshot'ları otomatikleştirin
5. **Monitoring:** CloudWatch alarm'ları ekleyin
6. **Scaling:** Auto Scaling yapılandırın (Free Tier limitlerini aşmadan)

## 🔐 Güvenlik Önerileri

1. **Security Groups:** Sadece gerekli portları açın
2. **IAM Roles:** Minimum yetki prensibi
3. **Secrets Management:** AWS Secrets Manager kullanın
4. **Encryption:** RDS ve S3'te encryption aktif edin
5. **Backup:** Düzenli backup alın

## 💰 Tahmini Maliyet (Free Tier İçinde)

- **EC2/ECS Fargate:** $0 (Free Tier)
- **RDS:** $0 (Free Tier)
- **ElastiCache:** $0 (Free Tier)
- **S3:** $0 (5GB'a kadar)
- **ALB:** ~$16/ay (Free Tier'de yok)
- **Data Transfer:** $0 (1GB'a kadar)
- **CloudWatch:** $0 (Free Tier limitlerinde)

**Toplam:** ~$16/ay (sadece ALB için)

## 🆘 Yardım

Sorun yaşarsanız:
1. CloudWatch loglarını kontrol edin
2. AWS Support'a başvurun (Free Tier hesaplar için temel destek)
3. GitHub Issues'da sorun bildirin

## 📚 Kaynaklar

- [AWS Free Tier](https://aws.amazon.com/free/)
- [AWS ECS Documentation](https://docs.aws.amazon.com/ecs/)
- [AWS RDS Documentation](https://docs.aws.amazon.com/rds/)
- [Laravel Documentation](https://laravel.com/docs)

---

**Not:** Bu rehber Free Tier limitleri dahilinde çalışmak için optimize edilmiştir. Production ortamı için ek güvenlik ve performans ayarları yapılmalıdır.
