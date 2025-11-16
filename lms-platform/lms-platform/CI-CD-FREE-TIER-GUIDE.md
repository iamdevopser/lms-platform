# 🚀 AWS CI/CD Free Tier Kurulum Rehberi

Bu rehber, LMS Platform projesi için AWS Free Tier kullanarak CI/CD pipeline kurulumunu adım adım açıklar.

## 📋 İçindekiler

1. [Ön Gereksinimler](#ön-gereksinimler)
2. [AWS Free Tier Limitleri](#aws-free-tier-limitleri)
3. [Kurulum Adımları](#kurulum-adımları)
4. [Pipeline Testi](#pipeline-testi)
5. [Monitoring ve Optimizasyon](#monitoring-ve-optimizasyon)
6. [Sorun Giderme](#sorun-giderme)

## 🔧 Ön Gereksinimler

### AWS Hesabı
- AWS Free Tier hesabı
- AWS CLI yüklü ve yapılandırılmış
- IAM kullanıcısı (programmatic access)

### GitHub Repository
- GitHub hesabı
- Repository'de GitHub Actions etkin
- AWS OIDC entegrasyonu

### Yerel Geliştirme Ortamı
- Docker yüklü
- Node.js 18+
- PHP 8.2+
- Composer

## 💰 AWS Free Tier Limitleri

### ECS Fargate
- **750 saat/ay** t2.micro eşdeğeri
- **1 GB RAM** ve **1 vCPU** dahil

### ECR
- **500 MB** depolama
- **500 MB** veri transferi

### RDS
- **750 saat/ay** db.t2.micro
- **20 GB** depolama

### ElastiCache
- **750 saat/ay** cache.t2.micro
- **1 GB** RAM

### S3
- **5 GB** depolama
- **20,000** GET isteği
- **2,000** PUT isteği

### CloudWatch
- **10** özel metrik
- **10** alarm
- **5 GB** log depolama

## 🚀 Kurulum Adımları

### 1. AWS CLI Kurulumu

```bash
# AWS CLI kurulumu (Ubuntu/Debian)
curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"
unzip awscliv2.zip
sudo ./aws/install

# AWS CLI yapılandırması
aws configure
```

### 2. GitHub Repository Ayarları

```bash
# Repository'yi klonlayın
git clone https://github.com/your-username/lms-platform.git
cd lms-platform

# GitHub Actions için gerekli dosyaları ekleyin
cp .github/workflows/ci-free-tier.yml .github/workflows/
cp .github/workflows/security-scan.yml .github/workflows/
```

### 3. AWS IAM Rolleri Oluşturma

```bash
# IAM rollerini oluşturun
./aws/ci-cd/setup-pipeline.sh
```

### 4. ECR Repository Kurulumu

```bash
# ECR repository oluşturun
./aws/ecr/setup-ecr.sh
```

### 5. CodeBuild Kurulumu

```bash
# CodeBuild projesini oluşturun
./aws/codebuild/setup-codebuild.sh
```

### 6. Monitoring Kurulumu

```bash
# CloudWatch monitoring kurulumu
./aws/monitoring/setup-monitoring.sh
```

## 🧪 Pipeline Testi

### 1. Yerel Test

```bash
# Docker build testi
docker build -f Dockerfile.free-tier -t lms-platform:test .

# Test çalıştırma
./aws/ci-cd/test-pipeline.sh
```

### 2. GitHub Actions Testi

```bash
# Kod değişikliği yapın
echo "# Test commit" >> README.md
git add .
git commit -m "Test CI/CD pipeline"
git push origin main
```

### 3. ECS Deployment Testi

```bash
# Manuel deployment testi
./deploy.sh
```

## 📊 Monitoring ve Optimizasyon

### 1. Cost Monitoring

```bash
# Maliyet optimizasyonu
./aws/monitoring/cost-optimization.sh
```

### 2. Performance Monitoring

```bash
# CloudWatch dashboard
aws cloudwatch get-dashboard --dashboard-name LMS-Platform-Free-Tier
```

### 3. Log Monitoring

```bash
# ECS logları
aws logs describe-log-streams --log-group-name /ecs/lms-platform-free
```

## 🔍 Sorun Giderme

### Yaygın Sorunlar

#### 1. ECR Push Hatası
```bash
# ECR login kontrolü
aws ecr get-login-password --region us-east-1 | docker login --username AWS --password-stdin $ECR_REGISTRY
```

#### 2. ECS Service Hatası
```bash
# ECS service durumu
aws ecs describe-services --cluster lms-platform-free-cluster --services lms-platform-free-service
```

#### 3. ALB Health Check Hatası
```bash
# Target group health
aws elbv2 describe-target-health --target-group-arn $TARGET_GROUP_ARN
```

#### 4. Free Tier Limit Aşımı
```bash
# Free Tier kullanımı
aws cloudwatch get-metric-statistics --namespace AWS/Billing --metric-name EstimatedCharges
```

### Log Dosyaları

```bash
# ECS task logları
aws logs get-log-events --log-group-name /ecs/lms-platform-free --log-stream-name ecs/lms-app/task-id

# CodeBuild logları
aws logs get-log-events --log-group-name /aws/codebuild/lms-platform-free --log-stream-name build-id
```

## 📈 Performans Optimizasyonu

### 1. Docker Image Optimizasyonu

```dockerfile
# Multi-stage build kullanın
FROM node:18-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm ci --only=production

FROM php:8.2-fpm-alpine AS backend
COPY --from=frontend /app /var/www/html
```

### 2. ECS Task Definition Optimizasyonu

```json
{
  "cpu": "256",
  "memory": "512",
  "requiresCompatibilities": ["FARGATE"],
  "networkMode": "awsvpc"
}
```

### 3. RDS Optimizasyonu

```yaml
# RDS instance class
DBInstanceClass: db.t2.micro
AllocatedStorage: 20
BackupRetentionPeriod: 1
```

## 🔐 Güvenlik

### 1. IAM Rolleri

```json
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Action": [
        "ecr:*",
        "ecs:*",
        "logs:*"
      ],
      "Resource": "*"
    }
  ]
}
```

### 2. Security Groups

```yaml
# ALB Security Group
- IpProtocol: tcp
  FromPort: 80
  ToPort: 80
  SourceSecurityGroupId: !Ref ALBSecurityGroup

# ECS Security Group
- IpProtocol: tcp
  FromPort: 80
  ToPort: 80
  SourceSecurityGroupId: !Ref ALBSecurityGroup
```

## 📚 Faydalı Komutlar

### AWS CLI Komutları

```bash
# ECS cluster durumu
aws ecs describe-clusters --clusters lms-platform-free-cluster

# ECR repository listesi
aws ecr describe-repositories

# CloudFormation stack durumu
aws cloudformation describe-stacks --stack-name lms-platform-free-infrastructure

# ALB durumu
aws elbv2 describe-load-balancers --names lms-platform-free-alb
```

### Docker Komutları

```bash
# Image build
docker build -f Dockerfile.free-tier -t lms-platform:latest .

# ECR push
docker tag lms-platform:latest $ECR_REGISTRY/lms-platform:latest
docker push $ECR_REGISTRY/lms-platform:latest

# Container çalıştırma
docker run -p 80:80 lms-platform:latest
```

## 🎯 Sonraki Adımlar

1. **Production Deployment**: Free Tier testleri tamamlandıktan sonra production ortamına geçiş
2. **Scaling**: Trafik artışına göre otomatik scaling kurulumu
3. **Monitoring**: Daha detaylı monitoring ve alerting
4. **Security**: Güvenlik taramaları ve compliance kontrolleri
5. **Backup**: Veri yedekleme ve disaster recovery planı

## 📞 Destek

- **AWS Support**: Free Tier kullanıcıları için temel destek
- **GitHub Issues**: Proje ile ilgili sorunlar için
- **Documentation**: AWS ve GitHub dokümantasyonu

## 🔗 Faydalı Linkler

- [AWS Free Tier](https://aws.amazon.com/free/)
- [ECS Fargate](https://aws.amazon.com/fargate/)
- [ECR](https://aws.amazon.com/ecr/)
- [CloudWatch](https://aws.amazon.com/cloudwatch/)
- [GitHub Actions](https://github.com/features/actions)

---

**Not**: Bu rehber Free Tier limitleri içinde kalacak şekilde optimize edilmiştir. Production ortamında daha yüksek limitler gerekebilir.





