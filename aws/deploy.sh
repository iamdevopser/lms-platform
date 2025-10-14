#!/bin/bash

# LMS Platform AWS Deployment Script
set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PROJECT_NAME="lms-platform"
AWS_REGION="us-east-1"
STACK_NAME="${PROJECT_NAME}-infrastructure"
ECR_REPOSITORY="${PROJECT_NAME}"
ECR_REGISTRY="${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com"

# Functions
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

# Check prerequisites
check_prerequisites() {
    log_info "Checking prerequisites..."
    
    # Check AWS CLI
    if ! command -v aws &> /dev/null; then
        log_error "AWS CLI is not installed. Please install it first."
        exit 1
    fi
    
    # Check Docker
    if ! command -v docker &> /dev/null; then
        log_error "Docker is not installed. Please install it first."
        exit 1
    fi
    
    # Check jq
    if ! command -v jq &> /dev/null; then
        log_error "jq is not installed. Please install it first."
        exit 1
    fi
    
    # Check AWS credentials
    if ! aws sts get-caller-identity &> /dev/null; then
        log_error "AWS credentials not configured. Run 'aws configure' first."
        exit 1
    fi
    
    # Get AWS Account ID
    AWS_ACCOUNT_ID=$(aws sts get-caller-identity --query Account --output text)
    ECR_REGISTRY="${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com"
    
    log_success "Prerequisites check passed"
}

# Create ECR repository
create_ecr_repository() {
    log_info "Creating ECR repository..."
    
    if aws ecr describe-repositories --repository-names ${ECR_REPOSITORY} --region ${AWS_REGION} &> /dev/null; then
        log_warning "ECR repository ${ECR_REPOSITORY} already exists"
    else
        aws ecr create-repository --repository-name ${ECR_REPOSITORY} --region ${AWS_REGION}
        log_success "ECR repository created"
    fi
}

# Build and push Docker image
build_and_push_image() {
    log_info "Building and pushing Docker image..."
    
    # Login to ECR
    aws ecr get-login-password --region ${AWS_REGION} | docker login --username AWS --password-stdin ${ECR_REGISTRY}
    
    # Build image
    docker build -f Dockerfile.aws -t ${ECR_REPOSITORY}:latest .
    
    # Tag image
    docker tag ${ECR_REPOSITORY}:latest ${ECR_REGISTRY}/${ECR_REPOSITORY}:latest
    
    # Push image
    docker push ${ECR_REGISTRY}/${ECR_REPOSITORY}:latest
    
    log_success "Docker image built and pushed"
}

# Deploy infrastructure
deploy_infrastructure() {
    log_info "Deploying infrastructure with CloudFormation..."
    
    # Check if stack exists
    if aws cloudformation describe-stacks --stack-name ${STACK_NAME} --region ${AWS_REGION} &> /dev/null; then
        log_info "Updating existing stack..."
        aws cloudformation update-stack \
            --stack-name ${STACK_NAME} \
            --template-body file://infrastructure.yml \
            --capabilities CAPABILITY_IAM \
            --region ${AWS_REGION} \
            --parameters ParameterKey=ProjectName,ParameterValue=${PROJECT_NAME} \
                       ParameterKey=Environment,ParameterValue=production \
                       ParameterKey=DatabasePassword,ParameterValue=$(openssl rand -base64 32) \
                       ParameterKey=InstanceType,ParameterValue=t3.medium \
                       ParameterKey=DatabaseInstanceClass,ParameterValue=db.t3.micro
    else
        log_info "Creating new stack..."
        aws cloudformation create-stack \
            --stack-name ${STACK_NAME} \
            --template-body file://infrastructure.yml \
            --capabilities CAPABILITY_IAM \
            --region ${AWS_REGION} \
            --parameters ParameterKey=ProjectName,ParameterValue=${PROJECT_NAME} \
                       ParameterKey=Environment,ParameterValue=production \
                       ParameterKey=DatabasePassword,ParameterValue=$(openssl rand -base64 32) \
                       ParameterKey=InstanceType,ParameterValue=t3.medium \
                       ParameterKey=DatabaseInstanceClass,ParameterValue=db.t3.micro
    fi
    
    # Wait for stack to complete
    log_info "Waiting for stack deployment to complete..."
    aws cloudformation wait stack-create-complete --stack-name ${STACK_NAME} --region ${AWS_REGION} || \
    aws cloudformation wait stack-update-complete --stack-name ${STACK_NAME} --region ${AWS_REGION}
    
    log_success "Infrastructure deployed successfully"
}

# Update ECS service
update_ecs_service() {
    log_info "Updating ECS service..."
    
    # Get cluster name from CloudFormation output
    CLUSTER_NAME=$(aws cloudformation describe-stacks \
        --stack-name ${STACK_NAME} \
        --region ${AWS_REGION} \
        --query 'Stacks[0].Outputs[?OutputKey==`ECSCluster`].OutputValue' \
        --output text)
    
    # Get service name
    SERVICE_NAME=$(aws ecs list-services --cluster ${CLUSTER_NAME} --region ${AWS_REGION} \
        --query 'serviceArns[0]' --output text | cut -d'/' -f3)
    
    # Force new deployment
    aws ecs update-service \
        --cluster ${CLUSTER_NAME} \
        --service ${SERVICE_NAME} \
        --force-new-deployment \
        --region ${AWS_REGION}
    
    log_success "ECS service updated"
}

# Get deployment information
get_deployment_info() {
    log_info "Getting deployment information..."
    
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
    
    # Get Cache endpoint
    CACHE_ENDPOINT=$(aws cloudformation describe-stacks \
        --stack-name ${STACK_NAME} \
        --region ${AWS_REGION} \
        --query 'Stacks[0].Outputs[?OutputKey==`CacheEndpoint`].OutputValue' \
        --output text)
    
    echo ""
    log_success "Deployment completed successfully!"
    echo ""
    echo "🌐 Application URLs:"
    echo "   Load Balancer: ${ALB_URL}"
    echo "   CloudFront: ${CLOUDFRONT_URL}"
    echo ""
    echo "🗄️ Database Information:"
    echo "   MySQL Endpoint: ${DB_ENDPOINT}"
    echo "   Redis Endpoint: ${CACHE_ENDPOINT}"
    echo ""
    echo "📊 AWS Console:"
    echo "   CloudFormation: https://console.aws.amazon.com/cloudformation/home?region=${AWS_REGION}#/stacks"
    echo "   ECS: https://console.aws.amazon.com/ecs/home?region=${AWS_REGION}#/clusters"
    echo "   RDS: https://console.aws.amazon.com/rds/home?region=${AWS_REGION}#databases:"
    echo ""
}

# Cleanup function
cleanup() {
    log_warning "Cleaning up..."
    # Add cleanup commands here if needed
}

# Main deployment function
main() {
    log_info "Starting LMS Platform deployment to AWS..."
    
    # Set trap for cleanup on exit
    trap cleanup EXIT
    
    # Run deployment steps
    check_prerequisites
    create_ecr_repository
    build_and_push_image
    deploy_infrastructure
    update_ecs_service
    get_deployment_info
    
    log_success "Deployment completed successfully! 🎉"
}

# Run main function
main "$@"





