# 🚀 AWS Production Deployment Guide - LMS Platform

## 📊 Maliyet Analizi (Aylık)

### Başlangıç Maliyeti (Minimum)
- **EC2 t3.medium**: $30-40/ay
- **RDS db.t3.micro**: $15-20/ay
- **ElastiCache t3.micro**: $10-15/ay
- **S3 + CloudFront**: $5-10/ay
- **Route 53**: $0.50/ay
- **ALB**: $16/ay
- **Toplam**: ~$80-100/ay

### Ölçeklendirme Sonrası
- **EC2 Auto Scaling**: $100-200/ay
- **RDS Multi-AZ**: $50-100/ay
- **ElastiCache Cluster**: $30-50/ay
- **S3 + CloudFront**: $20-50/ay
- **Toplam**: ~$200-400/ay

## 🏗️ Faz 1: Temel AWS Kurulum

### 1.1 AWS Hesap Kurulumu
```bash
# AWS CLI kurulumu
curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"
unzip awscliv2.zip
sudo ./aws/install

# AWS konfigürasyonu
aws configure
# AWS Access Key ID: [your-access-key]
# AWS Secret Access Key: [your-secret-key]
# Default region name: us-east-1
# Default output format: json
```

### 1.2 Gerekli Servisleri Aktifleştir
- EC2 (Elastic Compute Cloud)
- RDS (Relational Database Service)
- ElastiCache
- S3 (Simple Storage Service)
- CloudFront
- Route 53
- Certificate Manager
- IAM (Identity and Access Management)

## 🖥️ Faz 2: EC2 Instance Kurulumu

### 2.1 EC2 Instance Oluştur
```bash
# Key pair oluştur
aws ec2 create-key-pair --key-name lms-platform-key --query 'KeyMaterial' --output text > lms-platform-key.pem
chmod 400 lms-platform-key.pem

# Security Group oluştur
aws ec2 create-security-group --group-name lms-platform-sg --description "LMS Platform Security Group"

# Security Group kuralları
aws ec2 authorize-security-group-ingress --group-name lms-platform-sg --protocol tcp --port 22 --cidr 0.0.0.0/0
aws ec2 authorize-security-group-ingress --group-name lms-platform-sg --protocol tcp --port 80 --cidr 0.0.0.0/0
aws ec2 authorize-security-group-ingress --group-name lms-platform-sg --protocol tcp --port 443 --cidr 0.0.0.0/0
```

### 2.2 EC2 Instance Başlat
```bash
# Ubuntu 22.04 LTS AMI ID (us-east-1)
AMI_ID="ami-0c02fb55956c7d316"

# Instance oluştur
aws ec2 run-instances \
    --image-id $AMI_ID \
    --count 1 \
    --instance-type t3.medium \
    --key-name lms-platform-key \
    --security-groups lms-platform-sg \
    --tag-specifications 'ResourceType=instance,Tags=[{Key=Name,Value=lms-platform-prod}]'
```

## 🗄️ Faz 3: RDS MySQL Kurulumu

### 3.1 RDS Subnet Group Oluştur
```bash
# VPC ID'yi al
VPC_ID=$(aws ec2 describe-vpcs --filters "Name=is-default,Values=true" --query 'Vpcs[0].VpcId' --output text)

# Subnet'leri al
SUBNET_IDS=$(aws ec2 describe-subnets --filters "Name=vpc-id,Values=$VPC_ID" --query 'Subnets[*].SubnetId' --output text)

# RDS Subnet Group oluştur
aws rds create-db-subnet-group \
    --db-subnet-group-name lms-platform-subnet-group \
    --db-subnet-group-description "LMS Platform RDS Subnet Group" \
    --subnet-ids $SUBNET_IDS
```

### 3.2 RDS MySQL Instance Oluştur
```bash
# Security Group oluştur (RDS için)
aws ec2 create-security-group --group-name lms-rds-sg --description "LMS RDS Security Group" --vpc-id $VPC_ID

# RDS Security Group kuralları
RDS_SG_ID=$(aws ec2 describe-security-groups --group-names lms-rds-sg --query 'SecurityGroups[0].GroupId' --output text)
APP_SG_ID=$(aws ec2 describe-security-groups --group-names lms-platform-sg --query 'SecurityGroups[0].GroupId' --output text)

aws ec2 authorize-security-group-ingress \
    --group-id $RDS_SG_ID \
    --protocol tcp \
    --port 3306 \
    --source-group $APP_SG_ID

# RDS Instance oluştur
aws rds create-db-instance \
    --db-instance-identifier lms-platform-db \
    --db-instance-class db.t3.micro \
    --engine mysql \
    --engine-version 8.0.35 \
    --master-username admin \
    --master-user-password 'YourSecurePassword123!' \
    --allocated-storage 20 \
    --storage-type gp2 \
    --vpc-security-group-ids $RDS_SG_ID \
    --db-subnet-group-name lms-platform-subnet-group \
    --backup-retention-period 7 \
    --multi-az \
    --storage-encrypted
```

## 🔴 Faz 4: ElastiCache Redis Kurulumu

### 4.1 ElastiCache Subnet Group Oluştur
```bash
# ElastiCache Subnet Group oluştur
aws elasticache create-cache-subnet-group \
    --cache-subnet-group-name lms-platform-cache-subnet-group \
    --cache-subnet-group-description "LMS Platform ElastiCache Subnet Group" \
    --subnet-ids $SUBNET_IDS
```

### 4.2 ElastiCache Redis Cluster Oluştur
```bash
# Security Group oluştur (ElastiCache için)
aws ec2 create-security-group --group-name lms-cache-sg --description "LMS ElastiCache Security Group" --vpc-id $VPC_ID

CACHE_SG_ID=$(aws ec2 describe-security-groups --group-names lms-cache-sg --query 'SecurityGroups[0].GroupId' --output text)

aws ec2 authorize-security-group-ingress \
    --group-id $CACHE_SG_ID \
    --protocol tcp \
    --port 6379 \
    --source-group $APP_SG_ID

# ElastiCache Redis Cluster oluştur
aws elasticache create-cache-cluster \
    --cache-cluster-id lms-platform-cache \
    --cache-node-type cache.t3.micro \
    --engine redis \
    --num-cache-nodes 1 \
    --cache-subnet-group-name lms-platform-cache-subnet-group \
    --security-group-ids $CACHE_SG_ID \
    --port 6379
```

## 📦 Faz 5: S3 ve CloudFront Kurulumu

### 5.1 S3 Bucket Oluştur
```bash
# S3 Bucket oluştur (unique name gerekli)
BUCKET_NAME="lms-platform-assets-$(date +%s)"
aws s3 mb s3://$BUCKET_NAME

# Bucket policy oluştur
cat > bucket-policy.json << EOF
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "PublicReadGetObject",
            "Effect": "Allow",
            "Principal": "*",
            "Action": "s3:GetObject",
            "Resource": "arn:aws:s3:::$BUCKET_NAME/*"
        }
    ]
}
EOF

aws s3api put-bucket-policy --bucket $BUCKET_NAME --policy file://bucket-policy.json
```

### 5.2 CloudFront Distribution Oluştur
```bash
# CloudFront Distribution oluştur
aws cloudfront create-distribution \
    --distribution-config '{
        "CallerReference": "lms-platform-'$(date +%s)'",
        "Comment": "LMS Platform Assets Distribution",
        "DefaultCacheBehavior": {
            "TargetOriginId": "S3-'$BUCKET_NAME'",
            "ViewerProtocolPolicy": "redirect-to-https",
            "TrustedSigners": {
                "Enabled": false,
                "Quantity": 0
            },
            "ForwardedValues": {
                "QueryString": false,
                "Cookies": {"Forward": "none"}
            },
            "MinTTL": 0,
            "DefaultTTL": 86400,
            "MaxTTL": 31536000
        },
        "Origins": {
            "Quantity": 1,
            "Items": [
                {
                    "Id": "S3-'$BUCKET_NAME'",
                    "DomainName": "'$BUCKET_NAME'.s3.amazonaws.com",
                    "S3OriginConfig": {
                        "OriginAccessIdentity": ""
                    }
                }
            ]
        },
        "Enabled": true,
        "PriceClass": "PriceClass_100"
    }'
```

## 🐳 Faz 6: Docker ve ECS Kurulumu

### 6.1 ECS Cluster Oluştur
```bash
# ECS Cluster oluştur
aws ecs create-cluster --cluster-name lms-platform-cluster

# ECS Task Definition oluştur
cat > task-definition.json << EOF
{
    "family": "lms-platform-task",
    "networkMode": "awsvpc",
    "requiresCompatibilities": ["FARGATE"],
    "cpu": "512",
    "memory": "1024",
    "executionRoleArn": "arn:aws:iam::$(aws sts get-caller-identity --query Account --output text):role/ecsTaskExecutionRole",
    "containerDefinitions": [
        {
            "name": "lms-app",
            "image": "your-account.dkr.ecr.us-east-1.amazonaws.com/lms-platform:latest",
            "portMappings": [
                {
                    "containerPort": 80,
                    "protocol": "tcp"
                }
            ],
            "environment": [
                {"name": "APP_ENV", "value": "production"},
                {"name": "DB_HOST", "value": "lms-platform-db.xxxxx.us-east-1.rds.amazonaws.com"},
                {"name": "REDIS_HOST", "value": "lms-platform-cache.xxxxx.cache.amazonaws.com"}
            ],
            "logConfiguration": {
                "logDriver": "awslogs",
                "options": {
                    "awslogs-group": "/ecs/lms-platform",
                    "awslogs-region": "us-east-1",
                    "awslogs-stream-prefix": "ecs"
                }
            }
        }
    ]
}
EOF

aws ecs register-task-definition --cli-input-json file://task-definition.json
```

## 🔄 Faz 7: CI/CD Pipeline Kurulumu

### 7.1 GitHub Actions Workflow
```yaml
# .github/workflows/deploy.yml
name: Deploy to AWS

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Configure AWS credentials
      uses: aws-actions/configure-aws-credentials@v2
      with:
        aws-access-key-id: ${{ secrets.AWS_ACCESS_KEY_ID }}
        aws-secret-access-key: ${{ secrets.AWS_SECRET_ACCESS_KEY }}
        aws-region: us-east-1
    
    - name: Login to Amazon ECR
      id: login-ecr
      uses: aws-actions/amazon-ecr-login@v1
    
    - name: Build, tag, and push image to Amazon ECR
      env:
        ECR_REGISTRY: ${{ steps.login-ecr.outputs.registry }}
        ECR_REPOSITORY: lms-platform
        IMAGE_TAG: ${{ github.sha }}
      run: |
        docker build -t $ECR_REGISTRY/$ECR_REPOSITORY:$IMAGE_TAG .
        docker push $ECR_REGISTRY/$ECR_REPOSITORY:$IMAGE_TAG
    
    - name: Deploy to ECS
      run: |
        aws ecs update-service --cluster lms-platform-cluster --service lms-platform-service --force-new-deployment
```

## 📊 Faz 8: Monitoring ve Logging

### 8.1 CloudWatch Log Groups
```bash
# Log Group oluştur
aws logs create-log-group --log-group-name /ecs/lms-platform

# CloudWatch Dashboard oluştur
aws cloudwatch put-dashboard --dashboard-name "LMS-Platform-Dashboard" --dashboard-body '{
    "widgets": [
        {
            "type": "metric",
            "x": 0,
            "y": 0,
            "width": 12,
            "height": 6,
            "properties": {
                "metrics": [
                    [ "AWS/ECS", "CPUUtilization", "ServiceName", "lms-platform-service" ],
                    [ "AWS/ECS", "MemoryUtilization", "ServiceName", "lms-platform-service" ]
                ],
                "period": 300,
                "stat": "Average",
                "region": "us-east-1",
                "title": "ECS Service Metrics"
            }
        }
    ]
}'
```

## 🔒 Faz 9: SSL ve Domain Konfigürasyonu

### 9.1 Route 53 Hosted Zone
```bash
# Hosted Zone oluştur
aws route53 create-hosted-zone --name yourdomain.com --caller-reference $(date +%s)

# SSL Sertifikası oluştur
aws acm request-certificate \
    --domain-name yourdomain.com \
    --subject-alternative-names "*.yourdomain.com" \
    --validation-method DNS
```

## 💾 Faz 10: Backup ve Disaster Recovery

### 10.1 RDS Automated Backups
```bash
# RDS backup ayarları (zaten yukarıda yapıldı)
# backup-retention-period: 7 gün
# multi-az: true
```

### 10.2 S3 Cross-Region Replication
```bash
# Cross-region replication için bucket oluştur
aws s3 mb s3://lms-platform-backup-us-west-2

# Replication configuration
cat > replication-config.json << EOF
{
    "Role": "arn:aws:iam::$(aws sts get-caller-identity --query Account --output text):role/replication-role",
    "Rules": [
        {
            "ID": "ReplicateToWest2",
            "Status": "Enabled",
            "Prefix": "",
            "Destination": {
                "Bucket": "arn:aws:s3:::lms-platform-backup-us-west-2",
                "StorageClass": "STANDARD_IA"
            }
        }
    ]
}
EOF
```

## 🚀 Deployment Script

### deployment.sh
```bash
#!/bin/bash

echo "🚀 Starting LMS Platform AWS Deployment..."

# 1. Environment variables
export AWS_DEFAULT_REGION="us-east-1"
export PROJECT_NAME="lms-platform"
export DOMAIN_NAME="yourdomain.com"

# 2. Deploy infrastructure
echo "📦 Deploying infrastructure..."
aws cloudformation deploy \
    --template-file infrastructure.yml \
    --stack-name $PROJECT_NAME-infrastructure \
    --capabilities CAPABILITY_IAM

# 3. Build and push Docker image
echo "🐳 Building and pushing Docker image..."
aws ecr get-login-password --region $AWS_DEFAULT_REGION | docker login --username AWS --password-stdin $(aws sts get-caller-identity --query Account --output text).dkr.ecr.$AWS_DEFAULT_REGION.amazonaws.com

docker build -t $PROJECT_NAME .
docker tag $PROJECT_NAME:latest $(aws sts get-caller-identity --query Account --output text).dkr.ecr.$AWS_DEFAULT_REGION.amazonaws.com/$PROJECT_NAME:latest
docker push $(aws sts get-caller-identity --query Account --output text).dkr.ecr.$AWS_DEFAULT_REGION.amazonaws.com/$PROJECT_NAME:latest

# 4. Deploy application
echo "🚀 Deploying application..."
aws ecs update-service --cluster $PROJECT_NAME-cluster --service $PROJECT_NAME-service --force-new-deployment

echo "✅ Deployment completed!"
echo "🌐 Application URL: https://$DOMAIN_NAME"
```

## 📈 Ölçeklendirme Stratejisi

### Başlangıç (0-100 kullanıcı)
- EC2: t3.medium (2 vCPU, 4GB RAM)
- RDS: db.t3.micro (1 vCPU, 1GB RAM)
- ElastiCache: cache.t3.micro (1 vCPU, 0.5GB RAM)

### Orta Ölçek (100-1000 kullanıcı)
- EC2: t3.large (2 vCPU, 8GB RAM) + Auto Scaling
- RDS: db.t3.small (2 vCPU, 2GB RAM)
- ElastiCache: cache.t3.small (1 vCPU, 1.4GB RAM)

### Yüksek Ölçek (1000+ kullanıcı)
- ECS Fargate: 2-10 tasks
- RDS: db.r5.large (2 vCPU, 16GB RAM) + Read Replicas
- ElastiCache: cache.r5.large (2 vCPU, 13.07GB RAM)

## 💰 Maliyet Optimizasyonu

### 1. Reserved Instances
- 1 yıllık rezervasyon: %30-40 tasarruf
- 3 yıllık rezervasyon: %50-60 tasarruf

### 2. Spot Instances
- Development ortamı için: %70-90 tasarruf
- Non-critical workloads için

### 3. S3 Lifecycle Policies
- Eski dosyaları IA/Glacier'a taşı
- Aylık %50-80 tasarruf

### 4. CloudWatch Monitoring
- Unused resources'ları tespit et
- Right-sizing önerilerini uygula

## 🔧 Troubleshooting

### Yaygın Sorunlar
1. **High CPU Usage**: Auto Scaling Group ayarlarını kontrol et
2. **Database Connection Issues**: Security Group kurallarını kontrol et
3. **Slow Response**: CloudFront cache ayarlarını optimize et
4. **Memory Issues**: Container memory limitlerini artır

### Monitoring Commands
```bash
# ECS service durumu
aws ecs describe-services --cluster lms-platform-cluster --services lms-platform-service

# RDS durumu
aws rds describe-db-instances --db-instance-identifier lms-platform-db

# CloudWatch metrics
aws cloudwatch get-metric-statistics --namespace AWS/ECS --metric-name CPUUtilization --dimensions Name=ServiceName,Value=lms-platform-service --start-time 2024-01-01T00:00:00Z --end-time 2024-01-02T00:00:00Z --period 300 --statistics Average
```

Bu rehber ile AWS'de production-ready bir LMS platformu kurabilirsiniz. Her adımı sırasıyla takip ederek minimum maliyetle başlayıp ihtiyaca göre ölçeklendirebilirsiniz.





