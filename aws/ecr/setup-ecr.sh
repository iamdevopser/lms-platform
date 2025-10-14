#!/bin/bash

# AWS ECR Free Tier Setup Script
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
ECR_REPOSITORY_NAME="${PROJECT_NAME}"

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

# Get AWS Account ID
AWS_ACCOUNT_ID=$(aws sts get-caller-identity --query Account --output text)
ECR_REGISTRY="${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com"

log_info "Setting up ECR repository for Free Tier..."

# Create ECR repository
log_info "Creating ECR repository..."
if aws ecr describe-repositories --repository-names ${ECR_REPOSITORY_NAME} --region ${AWS_REGION} &> /dev/null; then
    log_warning "ECR repository ${ECR_REPOSITORY_NAME} already exists"
else
    aws ecr create-repository \
        --repository-name ${ECR_REPOSITORY_NAME} \
        --region ${AWS_REGION} \
        --image-scanning-configuration scanOnPush=true \
        --encryption-configuration encryptionType=AES256
    log_success "ECR repository created"
fi

# Set lifecycle policy for Free Tier optimization
log_info "Setting ECR lifecycle policy for Free Tier optimization..."
cat > ecr-lifecycle-policy.json << EOF
{
  "rules": [
    {
      "rulePriority": 1,
      "description": "Keep only 10 most recent images",
      "selection": {
        "tagStatus": "any",
        "countType": "imageCountMoreThan",
        "countNumber": 10
      },
      "action": {
        "type": "expire"
      }
    },
    {
      "rulePriority": 2,
      "description": "Delete untagged images older than 1 day",
      "selection": {
        "tagStatus": "untagged",
        "countType": "sinceImagePushed",
        "countUnit": "days",
        "countNumber": 1
      },
      "action": {
        "type": "expire"
      }
    }
  ]
}
EOF

aws ecr put-lifecycle-policy \
    --repository-name ${ECR_REPOSITORY_NAME} \
    --lifecycle-policy-text file://ecr-lifecycle-policy.json \
    --region ${AWS_REGION}

# Set repository policy for Free Tier
log_info "Setting ECR repository policy..."
cat > ecr-repository-policy.json << EOF
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Sid": "AllowPushPull",
      "Effect": "Allow",
      "Principal": {
        "AWS": "arn:aws:iam::${AWS_ACCOUNT_ID}:root"
      },
      "Action": [
        "ecr:GetDownloadUrlForLayer",
        "ecr:BatchGetImage",
        "ecr:BatchCheckLayerAvailability",
        "ecr:PutImage",
        "ecr:InitiateLayerUpload",
        "ecr:UploadLayerPart",
        "ecr:CompleteLayerUpload"
      ]
    },
    {
      "Sid": "AllowCodeBuild",
      "Effect": "Allow",
      "Principal": {
        "AWS": "arn:aws:iam::${AWS_ACCOUNT_ID}:role/${PROJECT_NAME}-codebuild-role"
      },
      "Action": [
        "ecr:GetDownloadUrlForLayer",
        "ecr:BatchGetImage",
        "ecr:BatchCheckLayerAvailability",
        "ecr:PutImage",
        "ecr:InitiateLayerUpload",
        "ecr:UploadLayerPart",
        "ecr:CompleteLayerUpload"
      ]
    }
  ]
}
EOF

aws ecr set-repository-policy \
    --repository-name ${ECR_REPOSITORY_NAME} \
    --policy-text file://ecr-repository-policy.json \
    --region ${AWS_REGION}

# Create image scanning configuration
log_info "Configuring image scanning..."
aws ecr put-image-scanning-configuration \
    --repository-name ${ECR_REPOSITORY_NAME} \
    --image-scanning-configuration scanOnPush=true \
    --region ${AWS_REGION}

# Create image tag mutability configuration
log_info "Configuring image tag mutability..."
aws ecr put-image-tag-mutability \
    --repository-name ${ECR_REPOSITORY_NAME} \
    --image-tag-mutability MUTABLE \
    --region ${AWS_REGION}

log_success "ECR setup completed!"
echo ""
echo "📦 ECR Repository Information:"
echo "   Repository Name: ${ECR_REPOSITORY_NAME}"
echo "   Registry URL: ${ECR_REGISTRY}"
echo "   Region: ${AWS_REGION}"
echo ""
echo "🔗 AWS Console:"
echo "   ECR: https://console.aws.amazon.com/ecr/repositories?region=${AWS_REGION}"
echo ""
echo "💰 Free Tier Limits:"
echo "   Storage: 500MB/month"
echo "   Data Transfer: 500MB/month"
echo ""
echo "🐳 Docker Commands:"
echo "   Login: aws ecr get-login-password --region ${AWS_REGION} | docker login --username AWS --password-stdin ${ECR_REGISTRY}"
echo "   Pull: docker pull ${ECR_REGISTRY}/${ECR_REPOSITORY_NAME}:latest"
echo "   Push: docker push ${ECR_REGISTRY}/${ECR_REPOSITORY_NAME}:latest"
echo ""





