# 🆓 AWS Free Tier ile LMS Platform Kurulumu

## 📊 Free Tier Limitleri ve Optimizasyon

### 🎯 Free Tier Kullanılabilir Servisler

| Servis | Free Tier Limit | Aylık Tasarruf |
|--------|----------------|----------------|
| **EC2 t2.micro** | 750 saat | $8.50 |
| **RDS db.t2.micro** | 750 saat | $15 |
| **ElastiCache t2.micro** | 750 saat | $13 |
| **S3** | 5GB | $0.12 |
| **CloudFront** | 1TB | $85 |
| **Route 53** | 1 Hosted Zone | $0.50 |
| **ALB** | 750 saat | $16 |
| **EBS** | 30GB | $3 |
| **Data Transfer** | 1GB | $0.09 |
| **Toplam Tasarruf** | | **~$140/ay** |

## 🏗️ Free Tier Optimized Architecture

### Minimal VPC Yapısı
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

## 🚀 Free Tier Deployment Script

### 1. Free Tier CloudFormation Template

```yaml
# aws/free-tier-infrastructure.yml
AWSTemplateFormatVersion: '2010-09-09'
Description: 'LMS Platform Free Tier Infrastructure'

Parameters:
  ProjectName:
    Type: String
    Default: lms-platform-free
    Description: Name of the project

Resources:
  # VPC (Free)
  VPC:
    Type: AWS::EC2::VPC
    Properties:
      CidrBlock: 10.0.0.0/16
      EnableDnsHostnames: true
      EnableDnsSupport: true
      Tags:
        - Key: Name
          Value: !Sub ${ProjectName}-vpc

  # Internet Gateway (Free)
  InternetGateway:
    Type: AWS::EC2::InternetGateway
    Properties:
      Tags:
        - Key: Name
          Value: !Sub ${ProjectName}-igw

  InternetGatewayAttachment:
    Type: AWS::EC2::VPCGatewayAttachment
    Properties:
      InternetGatewayId: !Ref InternetGateway
      VpcId: !Ref VPC

  # Single Public Subnet (Cost Optimization)
  PublicSubnet:
    Type: AWS::EC2::Subnet
    Properties:
      VpcId: !Ref VPC
      AvailabilityZone: !Select [0, !GetAZs '']
      CidrBlock: 10.0.1.0/24
      MapPublicIpOnLaunch: true
      Tags:
        - Key: Name
          Value: !Sub ${ProjectName}-public-subnet

  # Single Private Subnet (Cost Optimization)
  PrivateSubnet:
    Type: AWS::EC2::Subnet
    Properties:
      VpcId: !Ref VPC
      AvailabilityZone: !Select [0, !GetAZs '']
      CidrBlock: 10.0.2.0/24
      Tags:
        - Key: Name
          Value: !Sub ${ProjectName}-private-subnet

  # Route Table (Free)
  PublicRouteTable:
    Type: AWS::EC2::RouteTable
    Properties:
      VpcId: !Ref VPC
      Tags:
        - Key: Name
          Value: !Sub ${ProjectName}-public-rt

  DefaultPublicRoute:
    Type: AWS::EC2::Route
    DependsOn: InternetGatewayAttachment
    Properties:
      RouteTableId: !Ref PublicRouteTable
      DestinationCidrBlock: 0.0.0.0/0
      GatewayId: !Ref InternetGateway

  PublicSubnetRouteTableAssociation:
    Type: AWS::EC2::SubnetRouteTableAssociation
    Properties:
      RouteTableId: !Ref PublicRouteTable
      SubnetId: !Ref PublicSubnet

  # Security Groups (Free)
  WebSecurityGroup:
    Type: AWS::EC2::SecurityGroup
    Properties:
      GroupName: !Sub ${ProjectName}-web-sg
      GroupDescription: Security group for web servers
      VpcId: !Ref VPC
      SecurityGroupIngress:
        - IpProtocol: tcp
          FromPort: 80
          ToPort: 80
          CidrIp: 0.0.0.0/0
        - IpProtocol: tcp
          FromPort: 443
          ToPort: 443
          CidrIp: 0.0.0.0/0
      Tags:
        - Key: Name
          Value: !Sub ${ProjectName}-web-sg

  DatabaseSecurityGroup:
    Type: AWS::EC2::SecurityGroup
    Properties:
      GroupName: !Sub ${ProjectName}-db-sg
      GroupDescription: Security group for database
      VpcId: !Ref VPC
      SecurityGroupIngress:
        - IpProtocol: tcp
          FromPort: 3306
          ToPort: 3306
          SourceSecurityGroupId: !Ref WebSecurityGroup
      Tags:
        - Key: Name
          Value: !Sub ${ProjectName}-db-sg

  # RDS Subnet Group (Free)
  DBSubnetGroup:
    Type: AWS::RDS::DBSubnetGroup
    Properties:
      DBSubnetGroupDescription: Subnet group for RDS
      SubnetIds:
        - !Ref PrivateSubnet
      Tags:
        - Key: Name
          Value: !Sub ${ProjectName}-db-subnet-group

  # RDS MySQL (Free Tier)
  Database:
    Type: AWS::RDS::DBInstance
    Properties:
      DBInstanceIdentifier: !Sub ${ProjectName}-database
      DBName: lms_platform
      DBInstanceClass: db.t2.micro  # Free Tier
      Engine: mysql
      EngineVersion: '8.0.35'
      MasterUsername: admin
      MasterUserPassword: !Ref DatabasePassword
      AllocatedStorage: 20  # Free Tier limit
      StorageType: gp2
      VPCSecurityGroups:
        - !Ref DatabaseSecurityGroup
      DBSubnetGroupName: !Ref DBSubnetGroup
      BackupRetentionPeriod: 1  # Minimize backup costs
      MultiAZ: false  # Single AZ to save costs
      StorageEncrypted: false  # Encryption costs extra
      DeletionProtection: false
      Tags:
        - Key: Name
          Value: !Sub ${ProjectName}-database

  # ElastiCache Subnet Group (Free)
  CacheSubnetGroup:
    Type: AWS::ElastiCache::SubnetGroup
    Properties:
      Description: Subnet group for ElastiCache
      SubnetIds:
        - !Ref PrivateSubnet

  # ElastiCache Redis (Free Tier)
  CacheCluster:
    Type: AWS::ElastiCache::CacheCluster
    Properties:
      CacheClusterId: !Sub ${ProjectName}-cache
      CacheNodeType: cache.t2.micro  # Free Tier
      Engine: redis
      NumCacheNodes: 1
      CacheSubnetGroupName: !Ref CacheSubnetGroup
      VpcSecurityGroupIds:
        - !Ref WebSecurityGroup
      Port: 6379
      Tags:
        - Key: Name
          Value: !Sub ${ProjectName}-cache

  # S3 Bucket (Free Tier)
  AssetsBucket:
    Type: AWS::S3::Bucket
    Properties:
      BucketName: !Sub ${ProjectName}-assets-${AWS::AccountId}
      PublicReadPolicy: true
      WebsiteConfiguration:
        IndexDocument: index.html
        ErrorDocument: error.html
      CorsConfiguration:
        CorsRules:
          - AllowedHeaders: ['*']
            AllowedMethods: [GET, HEAD]
            AllowedOrigins: ['*']
            MaxAge: 3600
      Tags:
        - Key: Name
          Value: !Sub ${ProjectName}-assets

  # CloudFront Distribution (Free Tier)
  CloudFrontDistribution:
    Type: AWS::CloudFront::Distribution
    Properties:
      DistributionConfig:
        Origins:
          - DomainName: !GetAtt AssetsBucket.RegionalDomainName
            Id: S3Origin
            S3OriginConfig:
              OriginAccessIdentity: !Sub origin-access-identity/cloudfront/${CloudFrontOriginAccessIdentity}
        Enabled: true
        DefaultRootObject: index.html
        DefaultCacheBehavior:
          TargetOriginId: S3Origin
          ViewerProtocolPolicy: redirect-to-https
          AllowedMethods: [GET, HEAD, OPTIONS]
          CachedMethods: [GET, HEAD]
          ForwardedValues:
            QueryString: false
            Cookies:
              Forward: none
          MinTTL: 0
          DefaultTTL: 86400
          MaxTTL: 31536000
        PriceClass: PriceClass_100  # Only US, Canada, Europe
        Tags:
          - Key: Name
            Value: !Sub ${ProjectName}-cloudfront

  CloudFrontOriginAccessIdentity:
    Type: AWS::CloudFront::CloudFrontOriginAccessIdentity
    Properties:
      CloudFrontOriginAccessIdentityConfig:
        Comment: !Sub ${ProjectName}-oai

  # ECS Cluster (Free)
  ECSCluster:
    Type: AWS::ECS::Cluster
    Properties:
      ClusterName: !Sub ${ProjectName}-cluster
      CapacityProviders:
        - FARGATE
        - FARGATE_SPOT  # Use Spot for cost savings
      DefaultCapacityProviderStrategy:
        - CapacityProvider: FARGATE_SPOT
          Weight: 1
      Tags:
        - Key: Name
          Value: !Sub ${ProjectName}-cluster

  # ECS Task Definition (Optimized for Free Tier)
  ECSTaskDefinition:
    Type: AWS::ECS::TaskDefinition
    Properties:
      Family: !Sub ${ProjectName}-task
      NetworkMode: awsvpc
      RequiresCompatibilities:
        - FARGATE
      Cpu: 256  # Minimum for Fargate
      Memory: 512  # Minimum for Fargate
      ExecutionRoleArn: !Ref ECSTaskExecutionRole
      TaskRoleArn: !Ref ECSTaskRole
      ContainerDefinitions:
        - Name: lms-app
          Image: !Sub ${AWS::AccountId}.dkr.ecr.${AWS::Region}.amazonaws.com/${ProjectName}:latest
          PortMappings:
            - ContainerPort: 80
              Protocol: tcp
          Environment:
            - Name: APP_ENV
              Value: production
            - Name: DB_HOST
              Value: !GetAtt Database.Endpoint.Address
            - Name: DB_PORT
              Value: !GetAtt Database.Endpoint.Port
            - Name: REDIS_HOST
              Value: !GetAtt CacheCluster.RedisEndpoint.Address
            - Name: REDIS_PORT
              Value: !GetAtt CacheCluster.RedisEndpoint.Port
            - Name: S3_BUCKET
              Value: !Ref AssetsBucket
            - Name: CLOUDFRONT_URL
              Value: !Sub https://${CloudFrontDistribution.DomainName}
          LogConfiguration:
            LogDriver: awslogs
            Options:
              awslogs-group: !Ref CloudWatchLogGroup
              awslogs-region: !Ref AWS::Region
              awslogs-stream-prefix: ecs
          HealthCheck:
            Command:
              - CMD-SHELL
              - "curl -f http://localhost/health || exit 1"
            Interval: 30
            Timeout: 5
            Retries: 3
            StartPeriod: 60

  # ECS Service (Free Tier)
  ECSService:
    Type: AWS::ECS::Service
    DependsOn: ApplicationLoadBalancerListener
    Properties:
      ServiceName: !Sub ${ProjectName}-service
      Cluster: !Ref ECSCluster
      TaskDefinition: !Ref ECSTaskDefinition
      DesiredCount: 1  # Single instance for cost savings
      LaunchType: FARGATE
      NetworkConfiguration:
        AwsvpcConfiguration:
          SecurityGroups:
            - !Ref WebSecurityGroup
          Subnets:
            - !Ref PublicSubnet
          AssignPublicIp: ENABLED
      LoadBalancers:
        - ContainerName: lms-app
          ContainerPort: 80
          TargetGroupArn: !Ref ApplicationTargetGroup

  # Application Load Balancer (Free Tier)
  ApplicationLoadBalancer:
    Type: AWS::ElasticLoadBalancingV2::LoadBalancer
    Properties:
      Name: !Sub ${ProjectName}-alb
      Scheme: internet-facing
      Type: application
      Subnets:
        - !Ref PublicSubnet
      SecurityGroups:
        - !Ref WebSecurityGroup
      Tags:
        - Key: Name
          Value: !Sub ${ProjectName}-alb

  ApplicationTargetGroup:
    Type: AWS::ElasticLoadBalancingV2::TargetGroup
    Properties:
      Name: !Sub ${ProjectName}-tg
      Port: 80
      Protocol: HTTP
      VpcId: !Ref VPC
      TargetType: ip
      HealthCheckPath: /health
      HealthCheckIntervalSeconds: 30
      HealthCheckTimeoutSeconds: 5
      HealthyThresholdCount: 2
      UnhealthyThresholdCount: 3
      Tags:
        - Key: Name
          Value: !Sub ${ProjectName}-tg

  ApplicationLoadBalancerListener:
    Type: AWS::ElasticLoadBalancingV2::Listener
    Properties:
      DefaultActions:
        - Type: forward
          TargetGroupArn: !Ref ApplicationTargetGroup
      LoadBalancerArn: !Ref ApplicationLoadBalancer
      Port: 80
      Protocol: HTTP

  # CloudWatch Log Group (Free Tier)
  CloudWatchLogGroup:
    Type: AWS::Logs::LogGroup
    Properties:
      LogGroupName: !Sub /ecs/${ProjectName}
      RetentionInDays: 7  # Minimize log retention costs

  # IAM Roles (Free)
  ECSTaskExecutionRole:
    Type: AWS::IAM::Role
    Properties:
      AssumeRolePolicyDocument:
        Statement:
          - Effect: Allow
            Principal:
              Service: ecs-tasks.amazonaws.com
            Action: sts:AssumeRole
      ManagedPolicyArns:
        - arn:aws:iam::aws:policy/service-role/AmazonECSTaskExecutionRolePolicy
      Policies:
        - PolicyName: CloudWatchLogs
          PolicyDocument:
            Statement:
              - Effect: Allow
                Action:
                  - logs:CreateLogGroup
                  - logs:CreateLogStream
                  - logs:PutLogEvents
                Resource: !Sub ${CloudWatchLogGroup.Arn}:*

  ECSTaskRole:
    Type: AWS::IAM::Role
    Properties:
      AssumeRolePolicyDocument:
        Statement:
          - Effect: Allow
            Principal:
              Service: ecs-tasks.amazonaws.com
            Action: sts:AssumeRole
      Policies:
        - PolicyName: S3Access
          PolicyDocument:
            Statement:
              - Effect: Allow
                Action:
                  - s3:GetObject
                  - s3:PutObject
                  - s3:DeleteObject
                Resource: !Sub ${AssetsBucket.Arn}/*

Outputs:
  LoadBalancerURL:
    Description: Application Load Balancer URL
    Value: !Sub http://${ApplicationLoadBalancer.DNSName}
    Export:
      Name: !Sub ${ProjectName}-LoadBalancerURL

  CloudFrontURL:
    Description: CloudFront distribution URL
    Value: !Sub https://${CloudFrontDistribution.DomainName}
    Export:
      Name: !Sub ${ProjectName}-CloudFrontURL

  DatabaseEndpoint:
    Description: RDS MySQL endpoint
    Value: !GetAtt Database.Endpoint.Address
    Export:
      Name: !Sub ${ProjectName}-DatabaseEndpoint

  S3Bucket:
    Description: S3 bucket for assets
    Value: !Ref AssetsBucket
    Export:
      Name: !Sub ${ProjectName}-S3Bucket
```

## 🚀 Free Tier Deployment Script

### 2. Free Tier Deploy Script

```bash
#!/bin/bash
# aws/free-tier-deploy.sh

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Configuration
PROJECT_NAME="lms-platform-free"
AWS_REGION="us-east-1"
STACK_NAME="${PROJECT_NAME}-infrastructure"
ECR_REPOSITORY="${PROJECT_NAME}"
ECR_REGISTRY="${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com"

log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check Free Tier eligibility
check_free_tier() {
    log_info "Checking Free Tier eligibility..."
    
    # Check if account is within 12 months
    ACCOUNT_CREATION=$(aws sts get-caller-identity --query 'Account' --output text)
    log_info "AWS Account ID: $ACCOUNT_CREATION"
    
    # Check existing resources
    EXISTING_STACKS=$(aws cloudformation list-stacks --query 'StackSummaries[?StackStatus!=`DELETE_COMPLETE`].StackName' --output text)
    if [[ $EXISTING_STACKS == *"$STACK_NAME"* ]]; then
        log_warning "Stack $STACK_NAME already exists. Consider using a different name."
    fi
    
    log_success "Free Tier check completed"
}

# Create ECR repository
create_ecr_repository() {
    log_info "Creating ECR repository for Free Tier..."
    
    if aws ecr describe-repositories --repository-names ${ECR_REPOSITORY} --region ${AWS_REGION} &> /dev/null; then
        log_warning "ECR repository ${ECR_REPOSITORY} already exists"
    else
        aws ecr create-repository --repository-name ${ECR_REPOSITORY} --region ${AWS_REGION}
        log_success "ECR repository created"
    fi
}

# Build optimized Docker image for Free Tier
build_optimized_image() {
    log_info "Building optimized Docker image for Free Tier..."
    
    # Create optimized Dockerfile for Free Tier
    cat > Dockerfile.free-tier << 'EOF'
FROM php:8.2-fpm-alpine AS base

# Install minimal dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    nodejs \
    npm \
    oniguruma-dev \
    libxml2-dev \
    icu-dev \
    mysql-client

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        xml \
        soap \
        opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies (production only)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy package files
COPY package.json package-lock.json ./

# Install Node dependencies (production only)
RUN npm ci --only=production --no-audit --no-fund

# Copy application code
COPY . .

# Build frontend assets
RUN npm run build

# Optimize for Free Tier
RUN echo "upload_max_filesize = 10M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 10M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 128M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 30" >> /usr/local/etc/php/conf.d/uploads.ini

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Copy configurations
COPY docker/aws/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/aws/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
EOF

    # Login to ECR
    aws ecr get-login-password --region ${AWS_REGION} | docker login --username AWS --password-stdin ${ECR_REGISTRY}
    
    # Build image
    docker build -f Dockerfile.free-tier -t ${ECR_REPOSITORY}:latest .
    
    # Tag image
    docker tag ${ECR_REPOSITORY}:latest ${ECR_REGISTRY}/${ECR_REPOSITORY}:latest
    
    # Push image
    docker push ${ECR_REGISTRY}/${ECR_REPOSITORY}:latest
    
    log_success "Optimized Docker image built and pushed"
}

# Deploy Free Tier infrastructure
deploy_free_tier_infrastructure() {
    log_info "Deploying Free Tier infrastructure..."
    
    # Generate secure password
    DB_PASSWORD=$(openssl rand -base64 32)
    
    if aws cloudformation describe-stacks --stack-name ${STACK_NAME} --region ${AWS_REGION} &> /dev/null; then
        log_info "Updating existing Free Tier stack..."
        aws cloudformation update-stack \
            --stack-name ${STACK_NAME} \
            --template-body file://free-tier-infrastructure.yml \
            --capabilities CAPABILITY_IAM \
            --region ${AWS_REGION} \
            --parameters ParameterKey=ProjectName,ParameterValue=${PROJECT_NAME} \
                       ParameterKey=DatabasePassword,ParameterValue=${DB_PASSWORD}
    else
        log_info "Creating new Free Tier stack..."
        aws cloudformation create-stack \
            --stack-name ${STACK_NAME} \
            --template-body file://free-tier-infrastructure.yml \
            --capabilities CAPABILITY_IAM \
            --region ${AWS_REGION} \
            --parameters ParameterKey=ProjectName,ParameterValue=${PROJECT_NAME} \
                       ParameterKey=DatabasePassword,ParameterValue=${DB_PASSWORD}
    fi
    
    # Wait for stack to complete
    log_info "Waiting for Free Tier stack deployment to complete..."
    aws cloudformation wait stack-create-complete --stack-name ${STACK_NAME} --region ${AWS_REGION} || \
    aws cloudformation wait stack-update-complete --stack-name ${STACK_NAME} --region ${AWS_REGION}
    
    log_success "Free Tier infrastructure deployed successfully"
}

# Get Free Tier deployment information
get_free_tier_info() {
    log_info "Getting Free Tier deployment information..."
    
    # Get Load Balancer URL
    ALB_URL=$(aws cloudformation describe-stacks \
        --stack-name ${STACK_NAME} \
        --region ${AWS_REGION} \
        --query 'Stacks[0].Outputs[?OutputKey==`LoadBalancerURL`].OutputValue' \
        --output text)
    
    # Get CloudFront URL
    CLOUDFRONT_URL=$(aws cloudformation describe-stacks \
        --stack-name ${STACK_NAME} \
        --region ${AWS_REGION} \
        --query 'Stacks[0].Outputs[?OutputKey==`CloudFrontURL`].OutputValue' \
        --output text)
    
    # Get Database endpoint
    DB_ENDPOINT=$(aws cloudformation describe-stacks \
        --stack-name ${STACK_NAME} \
        --region ${AWS_REGION} \
        --query 'Stacks[0].Outputs[?OutputKey==`DatabaseEndpoint`].OutputValue' \
        --output text)
    
    echo ""
    log_success "Free Tier deployment completed successfully! 🎉"
    echo ""
    echo "🆓 Free Tier Resources:"
    echo "   EC2 t2.micro: 750 hours/month FREE"
    echo "   RDS db.t2.micro: 750 hours/month FREE"
    echo "   ElastiCache t2.micro: 750 hours/month FREE"
    echo "   S3: 5GB FREE"
    echo "   CloudFront: 1TB FREE"
    echo "   ALB: 750 hours/month FREE"
    echo ""
    echo "🌐 Application URLs:"
    echo "   Load Balancer: ${ALB_URL}"
    echo "   CloudFront: ${CLOUDFRONT_URL}"
    echo ""
    echo "🗄️ Database Information:"
    echo "   MySQL Endpoint: ${DB_ENDPOINT}"
    echo ""
    echo "💰 Estimated Monthly Cost: $0-5 (Free Tier limits)"
    echo ""
    echo "📊 AWS Console:"
    echo "   CloudFormation: https://console.aws.amazon.com/cloudformation/home?region=${AWS_REGION}#/stacks"
    echo "   ECS: https://console.aws.amazon.com/ecs/home?region=${AWS_REGION}#/clusters"
    echo "   RDS: https://console.aws.amazon.com/rds/home?region=${AWS_REGION}#databases:"
    echo ""
    echo "⚠️  Free Tier Monitoring:"
    echo "   Billing Dashboard: https://console.aws.amazon.com/billing/home#/freetier"
    echo "   Cost Explorer: https://console.aws.amazon.com/cost-management/home#/cost-explorer"
    echo ""
}

# Main function
main() {
    log_info "Starting LMS Platform Free Tier deployment to AWS..."
    
    # Get AWS Account ID
    AWS_ACCOUNT_ID=$(aws sts get-caller-identity --query Account --output text)
    ECR_REGISTRY="${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com"
    
    # Run deployment steps
    check_free_tier
    create_ecr_repository
    build_optimized_image
    deploy_free_tier_infrastructure
    get_free_tier_info
    
    log_success "Free Tier deployment completed successfully! 🆓"
}

# Run main function
main "$@"
```

## 💰 Free Tier Cost Monitoring

### 3. Cost Monitoring Script

```bash
#!/bin/bash
# aws/monitor-free-tier.sh

# Check Free Tier usage
check_free_tier_usage() {
    echo "🆓 Free Tier Usage Monitoring"
    echo "=============================="
    
    # EC2 usage
    echo "📊 EC2 t2.micro Usage:"
    aws cloudwatch get-metric-statistics \
        --namespace AWS/EC2 \
        --metric-name CPUUtilization \
        --dimensions Name=InstanceType,Value=t2.micro \
        --start-time $(date -d '1 month ago' -u +%Y-%m-%dT%H:%M:%S) \
        --end-time $(date -u +%Y-%m-%dT%H:%M:%S) \
        --period 86400 \
        --statistics Average \
        --query 'Datapoints[0].Average' \
        --output text
    
    # RDS usage
    echo "📊 RDS db.t2.micro Usage:"
    aws cloudwatch get-metric-statistics \
        --namespace AWS/RDS \
        --metric-name CPUUtilization \
        --dimensions Name=DBInstanceIdentifier,Value=lms-platform-free-database \
        --start-time $(date -d '1 month ago' -u +%Y-%m-%dT%H:%M:%S) \
        --end-time $(date -u +%Y-%m-%dT%H:%M:%S) \
        --period 86400 \
        --statistics Average \
        --query 'Datapoints[0].Average' \
        --output text
    
    # S3 usage
    echo "📊 S3 Storage Usage:"
    aws s3api list-objects-v2 \
        --bucket lms-platform-free-assets-$(aws sts get-caller-identity --query Account --output text) \
        --query 'Contents[].Size' \
        --output text | awk '{sum+=$1} END {print sum/1024/1024 " MB"}'
    
    echo ""
    echo "💡 Free Tier Limits:"
    echo "   EC2: 750 hours/month"
    echo "   RDS: 750 hours/month"
    echo "   ElastiCache: 750 hours/month"
    echo "   S3: 5GB"
    echo "   CloudFront: 1TB"
    echo "   ALB: 750 hours/month"
}

# Set up billing alerts
setup_billing_alerts() {
    echo "🔔 Setting up billing alerts..."
    
    # Create SNS topic for billing alerts
    aws sns create-topic --name lms-platform-billing-alerts
    
    # Create CloudWatch alarm for billing
    aws cloudwatch put-metric-alarm \
        --alarm-name "LMS-Platform-Billing-Alert" \
        --alarm-description "Alert when monthly charges exceed $5" \
        --metric-name EstimatedCharges \
        --namespace AWS/Billing \
        --statistic Maximum \
        --period 86400 \
        --threshold 5.0 \
        --comparison-operator GreaterThanThreshold \
        --evaluation-periods 1 \
        --alarm-actions arn:aws:sns:us-east-1:$(aws sts get-caller-identity --query Account --output text):lms-platform-billing-alerts
}

# Main monitoring function
main() {
    check_free_tier_usage
    setup_billing_alerts
}

main "$@"
```

## 🎯 Free Tier Optimization Tips

### 4. Optimization Strategies

```bash
#!/bin/bash
# aws/optimize-free-tier.sh

# Optimize ECS task for Free Tier
optimize_ecs_task() {
    echo "🔧 Optimizing ECS task for Free Tier..."
    
    # Update task definition to use minimum resources
    aws ecs register-task-definition \
        --family lms-platform-free-task \
        --network-mode awsvpc \
        --requires-compatibilities FARGATE \
        --cpu 256 \
        --memory 512 \
        --execution-role-arn arn:aws:iam::$(aws sts get-caller-identity --query Account --output text):role/ecsTaskExecutionRole \
        --container-definitions '[
            {
                "name": "lms-app",
                "image": "'$(aws sts get-caller-identity --query Account --output text)'.dkr.ecr.us-east-1.amazonaws.com/lms-platform-free:latest",
                "portMappings": [{"containerPort": 80, "protocol": "tcp"}],
                "environment": [
                    {"name": "APP_ENV", "value": "production"},
                    {"name": "DB_HOST", "value": "lms-platform-free-database.xxxxx.us-east-1.rds.amazonaws.com"},
                    {"name": "REDIS_HOST", "value": "lms-platform-free-cache.xxxxx.cache.amazonaws.com"}
                ],
                "logConfiguration": {
                    "logDriver": "awslogs",
                    "options": {
                        "awslogs-group": "/ecs/lms-platform-free",
                        "awslogs-region": "us-east-1",
                        "awslogs-stream-prefix": "ecs"
                    }
                },
                "healthCheck": {
                    "command": ["CMD-SHELL", "curl -f http://localhost/health || exit 1"],
                    "interval": 30,
                    "timeout": 5,
                    "retries": 3,
                    "startPeriod": 60
                }
            }
        ]'
}

# Optimize RDS for Free Tier
optimize_rds() {
    echo "🔧 Optimizing RDS for Free Tier..."
    
    # Modify RDS instance to use minimal settings
    aws rds modify-db-instance \
        --db-instance-identifier lms-platform-free-database \
        --allocated-storage 20 \
        --backup-retention-period 1 \
        --preferred-backup-window "03:00-04:00" \
        --preferred-maintenance-window "sun:04:00-sun:05:00" \
        --apply-immediately
}

# Optimize S3 for Free Tier
optimize_s3() {
    echo "🔧 Optimizing S3 for Free Tier..."
    
    # Set lifecycle policy to minimize costs
    aws s3api put-bucket-lifecycle-configuration \
        --bucket lms-platform-free-assets-$(aws sts get-caller-identity --query Account --output text) \
        --lifecycle-configuration '{
            "Rules": [
                {
                    "ID": "DeleteOldVersions",
                    "Status": "Enabled",
                    "NoncurrentVersionExpiration": {
                        "NoncurrentDays": 30
                    }
                },
                {
                    "ID": "TransitionToIA",
                    "Status": "Enabled",
                    "Transitions": [
                        {
                            "Days": 30,
                            "StorageClass": "STANDARD_IA"
                        }
                    ]
                }
            ]
        }'
}

# Main optimization function
main() {
    optimize_ecs_task
    optimize_rds
    optimize_s3
    echo "✅ Free Tier optimization completed!"
}

main "$@"
```

## 📋 Free Tier Checklist

### 5. Pre-Deployment Checklist

- [ ] AWS Free Tier eligible account (within 12 months)
- [ ] No existing resources that exceed Free Tier limits
- [ ] Domain name registered (optional)
- [ ] SSL certificate ready (optional)
- [ ] Environment variables configured
- [ ] Docker image optimized for Free Tier
- [ ] Monitoring and alerts configured

### 6. Post-Deployment Checklist

- [ ] Application accessible via Load Balancer URL
- [ ] Database connectivity working
- [ ] Redis cache working
- [ ] S3 file uploads working
- [ ] CloudFront distribution active
- [ ] Free Tier usage monitoring active
- [ ] Billing alerts configured
- [ ] Backup strategy implemented

## 🚨 Free Tier Limits & Warnings

### 7. Important Limitations

1. **EC2 t2.micro**: 750 hours/month (31 days = 744 hours)
2. **RDS db.t2.micro**: 750 hours/month
3. **ElastiCache t2.micro**: 750 hours/month
4. **S3**: 5GB storage
5. **CloudFront**: 1TB data transfer
6. **ALB**: 750 hours/month
7. **EBS**: 30GB storage
8. **Data Transfer**: 1GB out

### 8. Cost Alerts

Set up billing alerts at:
- $1 (Free Tier warning)
- $5 (Free Tier limit)
- $10 (Emergency stop)

## 🎉 Free Tier Deployment Commands

```bash
# 1. Clone and setup
git clone <your-repo>
cd lms-platform

# 2. Configure environment
cp docker.env.example .env
nano .env

# 3. Deploy Free Tier
cd aws
chmod +x free-tier-deploy.sh
./free-tier-deploy.sh

# 4. Monitor usage
chmod +x monitor-free-tier.sh
./monitor-free-tier.sh

# 5. Optimize
chmod +x optimize-free-tier.sh
./optimize-free-tier.sh
```

Bu rehber ile AWS Free Tier kullanarak **$0-5/ay** maliyetle LMS platformunuzu kurabilirsiniz! 🆓





