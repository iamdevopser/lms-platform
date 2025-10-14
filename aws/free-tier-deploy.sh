#!/bin/bash

# LMS Platform AWS Free Tier Deployment Script
set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PROJECT_NAME="lms-platform-free"
AWS_REGION="us-east-1"
STACK_NAME="${PROJECT_NAME}-infrastructure"
ECR_REPOSITORY="${PROJECT_NAME}"

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

# Check Free Tier eligibility
check_free_tier_eligibility() {
    log_info "Checking Free Tier eligibility..."
    
    # Get AWS Account ID
    AWS_ACCOUNT_ID=$(aws sts get-caller-identity --query Account --output text)
    ECR_REGISTRY="${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com"
    
    log_info "AWS Account ID: $AWS_ACCOUNT_ID"
    
    # Check if account is within 12 months (Free Tier period)
    ACCOUNT_CREATION_DATE=$(aws iam get-user --query 'User.CreateDate' --output text 2>/dev/null || echo "Unknown")
    log_info "Account creation date: $ACCOUNT_CREATION_DATE"
    
    # Check existing resources that might exceed Free Tier
    EXISTING_STACKS=$(aws cloudformation list-stacks --query 'StackSummaries[?StackStatus!=`DELETE_COMPLETE`].StackName' --output text)
    if [[ $EXISTING_STACKS == *"$STACK_NAME"* ]]; then
        log_warning "Stack $STACK_NAME already exists. Consider using a different name."
    fi
    
    # Check Free Tier usage
    log_info "Checking current Free Tier usage..."
    aws cloudwatch get-metric-statistics \
        --namespace AWS/Billing \
        --metric-name EstimatedCharges \
        --start-time $(date -d '1 month ago' -u +%Y-%m-%dT%H:%M:%S) \
        --end-time $(date -u +%Y-%m-%dT%H:%M:%S) \
        --period 86400 \
        --statistics Maximum \
        --query 'Datapoints[0].Maximum' \
        --output text 2>/dev/null || log_warning "Could not retrieve billing information"
    
    log_success "Free Tier eligibility check completed"
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
    
    # Login to ECR
    aws ecr get-login-password --region ${AWS_REGION} | docker login --username AWS --password-stdin ${ECR_REGISTRY}
    
    # Build image with Free Tier optimizations
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

# Update ECS service
update_ecs_service() {
    log_info "Updating ECS service for Free Tier..."
    
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
    
    log_success "ECS service updated for Free Tier"
}

# Set up Free Tier monitoring
setup_free_tier_monitoring() {
    log_info "Setting up Free Tier monitoring..."
    
    # Create CloudWatch dashboard for Free Tier
    aws cloudwatch put-dashboard --dashboard-name "LMS-Platform-Free-Tier" --dashboard-body '{
        "widgets": [
            {
                "type": "metric",
                "x": 0,
                "y": 0,
                "width": 12,
                "height": 6,
                "properties": {
                    "metrics": [
                        [ "AWS/ECS", "CPUUtilization", "ServiceName", "lms-platform-free-service" ],
                        [ "AWS/ECS", "MemoryUtilization", "ServiceName", "lms-platform-free-service" ]
                    ],
                    "period": 300,
                    "stat": "Average",
                    "region": "us-east-1",
                    "title": "Free Tier ECS Metrics"
                }
            },
            {
                "type": "metric",
                "x": 0,
                "y": 6,
                "width": 12,
                "height": 6,
                "properties": {
                    "metrics": [
                        [ "AWS/RDS", "CPUUtilization", "DBInstanceIdentifier", "lms-platform-free-database" ],
                        [ "AWS/RDS", "FreeableMemory", "DBInstanceIdentifier", "lms-platform-free-database" ]
                    ],
                    "period": 300,
                    "stat": "Average",
                    "region": "us-east-1",
                    "title": "Free Tier RDS Metrics"
                }
            }
        ]
    }'
    
    # Create billing alarm
    aws cloudwatch put-metric-alarm \
        --alarm-name "LMS-Platform-Free-Tier-Billing" \
        --alarm-description "Alert when monthly charges exceed $5" \
        --metric-name EstimatedCharges \
        --namespace AWS/Billing \
        --statistic Maximum \
        --period 86400 \
        --threshold 5.0 \
        --comparison-operator GreaterThanThreshold \
        --evaluation-periods 1 \
        --alarm-actions "arn:aws:sns:us-east-1:${AWS_ACCOUNT_ID}:lms-platform-billing-alerts" || \
    log_warning "Could not create billing alarm (SNS topic may not exist)"
    
    log_success "Free Tier monitoring setup completed"
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
    
    # Get S3 bucket
    S3_BUCKET=$(aws cloudformation describe-stacks \
        --stack-name ${STACK_NAME} \
        --region ${AWS_REGION} \
        --query 'Stacks[0].Outputs[?OutputKey==`S3Bucket`].OutputValue' \
        --output text)
    
    echo ""
    log_success "Free Tier deployment completed successfully! 🆓"
    echo ""
    echo "🆓 Free Tier Resources Deployed:"
    echo "   ✅ EC2 t2.micro: 750 hours/month FREE"
    echo "   ✅ RDS db.t2.micro: 750 hours/month FREE"
    echo "   ✅ ElastiCache t2.micro: 750 hours/month FREE"
    echo "   ✅ S3: 5GB FREE"
    echo "   ✅ CloudFront: 1TB FREE"
    echo "   ✅ ALB: 750 hours/month FREE"
    echo ""
    echo "🌐 Application URLs:"
    echo "   Load Balancer: ${ALB_URL}"
    echo "   CloudFront: ${CLOUDFRONT_URL}"
    echo ""
    echo "🗄️ Database Information:"
    echo "   MySQL Endpoint: ${DB_ENDPOINT}"
    echo "   S3 Bucket: ${S3_BUCKET}"
    echo ""
    echo "💰 Estimated Monthly Cost: $0-5 (Free Tier limits)"
    echo ""
    echo "📊 Monitoring & Management:"
    echo "   CloudFormation: https://console.aws.amazon.com/cloudformation/home?region=${AWS_REGION}#/stacks"
    echo "   ECS: https://console.aws.amazon.com/ecs/home?region=${AWS_REGION}#/clusters"
    echo "   RDS: https://console.aws.amazon.com/rds/home?region=${AWS_REGION}#databases:"
    echo "   CloudWatch: https://console.aws.amazon.com/cloudwatch/home?region=${AWS_REGION}#dashboards:"
    echo ""
    echo "⚠️  Free Tier Monitoring:"
    echo "   Billing Dashboard: https://console.aws.amazon.com/billing/home#/freetier"
    echo "   Cost Explorer: https://console.aws.amazon.com/cost-management/home#/cost-explorer"
    echo ""
    echo "🔧 Management Commands:"
    echo "   View logs: aws logs tail /ecs/lms-platform-free --follow"
    echo "   Scale service: aws ecs update-service --cluster lms-platform-free-cluster --service lms-platform-free-service --desired-count 1"
    echo "   Check costs: aws ce get-cost-and-usage --time-period Start=2024-01-01,End=2024-01-31 --granularity MONTHLY --metrics BlendedCost"
    echo ""
}

# Cleanup function
cleanup() {
    log_warning "Cleaning up temporary files..."
    # Add cleanup commands here if needed
}

# Main deployment function
main() {
    log_info "Starting LMS Platform Free Tier deployment to AWS..."
    echo "🆓 This deployment uses AWS Free Tier resources to minimize costs"
    echo "💰 Estimated monthly cost: $0-5"
    echo ""
    
    # Set trap for cleanup on exit
    trap cleanup EXIT
    
    # Run deployment steps
    check_free_tier_eligibility
    create_ecr_repository
    build_optimized_image
    deploy_free_tier_infrastructure
    update_ecs_service
    setup_free_tier_monitoring
    get_free_tier_info
    
    log_success "Free Tier deployment completed successfully! 🆓"
    echo ""
    echo "🎉 Your LMS Platform is now running on AWS Free Tier!"
    echo "💡 Monitor your usage regularly to stay within Free Tier limits"
    echo "📚 Check the AWS-FREE-TIER-GUIDE.md for detailed information"
}

# Run main function
main "$@"





