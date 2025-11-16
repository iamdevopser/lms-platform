#!/bin/bash

# AWS CI/CD Pipeline Setup Script for Free Tier
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
GITHUB_REPO="your-username/lms-platform"  # Update with your GitHub repo

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

log_info "Setting up CI/CD pipeline for Free Tier..."

# Create GitHub OIDC Identity Provider
log_info "Creating GitHub OIDC Identity Provider..."
aws iam create-open-id-connect-provider \
    --url https://token.actions.githubusercontent.com \
    --thumbprint-list 6938fd4d98bab03faadb97b34396831e3780aea1 \
    --client-id-list sts.amazonaws.com

# Create IAM role for GitHub Actions
log_info "Creating IAM role for GitHub Actions..."
cat > github-actions-trust-policy.json << EOF
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Principal": {
        "Federated": "arn:aws:iam::$AWS_ACCOUNT_ID:oidc-provider/token.actions.githubusercontent.com"
      },
      "Action": "sts:AssumeRoleWithWebIdentity",
      "Condition": {
        "StringEquals": {
          "token.actions.githubusercontent.com:aud": "sts.amazonaws.com"
        },
        "StringLike": {
          "token.actions.githubusercontent.com:sub": "repo:$GITHUB_REPO:*"
        }
      }
    }
  ]
}
EOF

aws iam create-role \
    --role-name GitHubActionsRole \
    --assume-role-policy-document file://github-actions-trust-policy.json

# Create policy for GitHub Actions
log_info "Creating policy for GitHub Actions..."
cat > github-actions-policy.json << EOF
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Action": [
        "ecr:GetAuthorizationToken",
        "ecr:BatchCheckLayerAvailability",
        "ecr:GetDownloadUrlForLayer",
        "ecr:BatchGetImage",
        "ecr:PutImage",
        "ecr:InitiateLayerUpload",
        "ecr:UploadLayerPart",
        "ecr:CompleteLayerUpload",
        "ecs:UpdateService",
        "ecs:DescribeServices",
        "ecs:DescribeTaskDefinition",
        "ecs:RegisterTaskDefinition",
        "ecs:ListTasks",
        "ecs:DescribeTasks",
        "ecs:RunTask",
        "ecs:StopTask",
        "iam:PassRole",
        "logs:CreateLogGroup",
        "logs:CreateLogStream",
        "logs:PutLogEvents",
        "cloudformation:DescribeStacks",
        "cloudformation:CreateStack",
        "cloudformation:UpdateStack",
        "cloudformation:DeleteStack",
        "cloudformation:DescribeStackEvents",
        "cloudformation:DescribeStackResources",
        "s3:GetObject",
        "s3:PutObject",
        "s3:DeleteObject",
        "s3:ListBucket"
      ],
      "Resource": "*"
    }
  ]
}
EOF

aws iam create-policy \
    --policy-name GitHubActionsPolicy \
    --policy-document file://github-actions-policy.json

# Attach policy to role
aws iam attach-role-policy \
    --role-name GitHubActionsRole \
    --policy-arn arn:aws:iam::$AWS_ACCOUNT_ID:policy/GitHubActionsPolicy

# Create ECS cluster
log_info "Creating ECS cluster..."
aws ecs create-cluster \
    --cluster-name $PROJECT_NAME-cluster \
    --capacity-providers FARGATE \
    --default-capacity-provider-strategy capacityProvider=FARGATE,weight=1

# Create ECS service
log_info "Creating ECS service..."
aws ecs create-service \
    --cluster $PROJECT_NAME-cluster \
    --service-name $PROJECT_NAME-service \
    --task-definition $PROJECT_NAME-task \
    --desired-count 1 \
    --launch-type FARGATE \
    --network-configuration "awsvpcConfiguration={subnets=[subnet-12345],securityGroups=[sg-12345],assignPublicIp=ENABLED}"

# Create Application Load Balancer
log_info "Creating Application Load Balancer..."
aws elbv2 create-load-balancer \
    --name $PROJECT_NAME-alb \
    --subnets subnet-12345 subnet-67890 \
    --security-groups sg-12345

# Create target group
log_info "Creating target group..."
aws elbv2 create-target-group \
    --name $PROJECT_NAME-tg \
    --protocol HTTP \
    --port 80 \
    --vpc-id vpc-12345 \
    --target-type ip \
    --health-check-path /health

# Create listener
log_info "Creating ALB listener..."
aws elbv2 create-listener \
    --load-balancer-arn arn:aws:elasticloadbalancing:$AWS_REGION:$AWS_ACCOUNT_ID:loadbalancer/app/$PROJECT_NAME-alb/1234567890123456 \
    --protocol HTTP \
    --port 80 \
    --default-actions Type=forward,TargetGroupArn=arn:aws:elasticloadbalancing:$AWS_REGION:$AWS_ACCOUNT_ID:targetgroup/$PROJECT_NAME-tg/1234567890123456

# Create CloudFormation stack for infrastructure
log_info "Creating CloudFormation stack..."
aws cloudformation create-stack \
    --stack-name $PROJECT_NAME-infrastructure \
    --template-body file://aws/infrastructure.yml \
    --capabilities CAPABILITY_IAM \
    --parameters ParameterKey=ProjectName,ParameterValue=$PROJECT_NAME

# Wait for stack creation
log_info "Waiting for CloudFormation stack creation..."
aws cloudformation wait stack-create-complete \
    --stack-name $PROJECT_NAME-infrastructure

# Create deployment script
log_info "Creating deployment script..."
cat > deploy.sh << 'EOF'
#!/bin/bash
set -e

# Get AWS Account ID
AWS_ACCOUNT_ID=$(aws sts get-caller-identity --query Account --output text)
AWS_REGION="us-east-1"
PROJECT_NAME="lms-platform-free"
ECR_REGISTRY="${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com"

# Login to ECR
aws ecr get-login-password --region $AWS_REGION | \
docker login --username AWS --password-stdin $ECR_REGISTRY

# Build and push image
docker build -f Dockerfile.free-tier -t $PROJECT_NAME:latest .
docker tag $PROJECT_NAME:latest $ECR_REGISTRY/$PROJECT_NAME:latest
docker push $ECR_REGISTRY/$PROJECT_NAME:latest

# Update ECS service
aws ecs update-service \
    --cluster $PROJECT_NAME-cluster \
    --service $PROJECT_NAME-service \
    --force-new-deployment

echo "Deployment completed successfully!"
EOF

chmod +x deploy.sh

log_success "CI/CD pipeline setup completed!"
echo ""
echo "🚀 Pipeline Information:"
echo "   ECS Cluster: $PROJECT_NAME-cluster"
echo "   ECS Service: $PROJECT_NAME-service"
echo "   ECR Repository: $PROJECT_NAME"
echo "   ALB: $PROJECT_NAME-alb"
echo ""
echo "🔧 Next Steps:"
echo "   1. Update GitHub repository settings with AWS role ARN"
echo "   2. Add GitHub secrets:"
echo "      - AWS_ROLE_ARN: arn:aws:iam::$AWS_ACCOUNT_ID:role/GitHubActionsRole"
echo "      - AWS_REGION: $AWS_REGION"
echo "      - ECR_REGISTRY: $ECR_REGISTRY"
echo "   3. Push code to trigger pipeline"
echo ""
echo "📊 Monitor pipeline:"
echo "   ECS: https://console.aws.amazon.com/ecs/home?region=$AWS_REGION#/clusters"
echo "   ECR: https://console.aws.amazon.com/ecr/repositories?region=$AWS_REGION"
echo "   ALB: https://console.aws.amazon.com/ec2/v2/home?region=$AWS_REGION#LoadBalancers:"
echo ""





