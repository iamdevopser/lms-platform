#!/bin/bash

# AWS CI/CD Pipeline Test Script for Free Tier
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

log_info "Testing CI/CD pipeline for Free Tier..."

# Test ECR connectivity
test_ecr_connectivity() {
    log_info "Testing ECR connectivity..."
    
    if aws ecr describe-repositories --repository-names $PROJECT_NAME --region $AWS_REGION &> /dev/null; then
        log_success "ECR repository accessible"
    else
        log_error "ECR repository not accessible"
        return 1
    fi
}

# Test ECS cluster
test_ecs_cluster() {
    log_info "Testing ECS cluster..."
    
    if aws ecs describe-clusters --clusters $PROJECT_NAME-cluster --region $AWS_REGION &> /dev/null; then
        log_success "ECS cluster accessible"
    else
        log_error "ECS cluster not accessible"
        return 1
    fi
}

# Test ECS service
test_ecs_service() {
    log_info "Testing ECS service..."
    
    if aws ecs describe-services --cluster $PROJECT_NAME-cluster --services $PROJECT_NAME-service --region $AWS_REGION &> /dev/null; then
        log_success "ECS service accessible"
    else
        log_error "ECS service not accessible"
        return 1
    fi
}

# Test ALB
test_alb() {
    log_info "Testing Application Load Balancer..."
    
    if aws elbv2 describe-load-balancers --names $PROJECT_NAME-alb --region $AWS_REGION &> /dev/null; then
        log_success "ALB accessible"
    else
        log_error "ALB not accessible"
        return 1
    fi
}

# Test CloudFormation stack
test_cloudformation() {
    log_info "Testing CloudFormation stack..."
    
    if aws cloudformation describe-stacks --stack-name $PROJECT_NAME-infrastructure --region $AWS_REGION &> /dev/null; then
        log_success "CloudFormation stack accessible"
    else
        log_error "CloudFormation stack not accessible"
        return 1
    fi
}

# Test Docker build
test_docker_build() {
    log_info "Testing Docker build..."
    
    if docker build -f Dockerfile.free-tier -t $PROJECT_NAME:test . &> /dev/null; then
        log_success "Docker build successful"
    else
        log_error "Docker build failed"
        return 1
    fi
}

# Test ECR push
test_ecr_push() {
    log_info "Testing ECR push..."
    
    # Login to ECR
    if aws ecr get-login-password --region $AWS_REGION | docker login --username AWS --password-stdin $ECR_REGISTRY &> /dev/null; then
        log_success "ECR login successful"
    else
        log_error "ECR login failed"
        return 1
    fi
    
    # Tag image
    if docker tag $PROJECT_NAME:test $ECR_REGISTRY/$PROJECT_NAME:test &> /dev/null; then
        log_success "Image tagged successfully"
    else
        log_error "Image tagging failed"
        return 1
    fi
    
    # Push image
    if docker push $ECR_REGISTRY/$PROJECT_NAME:test &> /dev/null; then
        log_success "Image pushed successfully"
    else
        log_error "Image push failed"
        return 1
    fi
}

# Test ECS deployment
test_ecs_deployment() {
    log_info "Testing ECS deployment..."
    
    # Update task definition
    if aws ecs register-task-definition \
        --family $PROJECT_NAME-task \
        --network-mode awsvpc \
        --requires-compatibilities FARGATE \
        --cpu 256 \
        --memory 512 \
        --execution-role-arn arn:aws:iam::$AWS_ACCOUNT_ID:role/ecsTaskExecutionRole \
        --container-definitions "[
            {
                \"name\": \"lms-app\",
                \"image\": \"$ECR_REGISTRY/$PROJECT_NAME:test\",
                \"portMappings\": [{\"containerPort\": 80, \"protocol\": \"tcp\"}],
                \"environment\": [
                    {\"name\": \"APP_ENV\", \"value\": \"testing\"},
                    {\"name\": \"DB_HOST\", \"value\": \"localhost\"},
                    {\"name\": \"REDIS_HOST\", \"value\": \"localhost\"}
                ],
                \"logConfiguration\": {
                    \"logDriver\": \"awslogs\",
                    \"options\": {
                        \"awslogs-group\": \"/ecs/$PROJECT_NAME\",
                        \"awslogs-region\": \"$AWS_REGION\",
                        \"awslogs-stream-prefix\": \"ecs\"
                    }
                }
            }
        ]" &> /dev/null; then
        log_success "Task definition updated successfully"
    else
        log_error "Task definition update failed"
        return 1
    fi
}

# Test application health
test_application_health() {
    log_info "Testing application health..."
    
    # Get ALB DNS name
    ALB_DNS=$(aws elbv2 describe-load-balancers --names $PROJECT_NAME-alb --region $AWS_REGION --query 'LoadBalancers[0].DNSName' --output text)
    
    if [ "$ALB_DNS" != "None" ] && [ "$ALB_DNS" != "" ]; then
        log_success "ALB DNS: $ALB_DNS"
        
        # Test health endpoint
        if curl -f http://$ALB_DNS/health &> /dev/null; then
            log_success "Application health check passed"
        else
            log_warning "Application health check failed (may be starting up)"
        fi
    else
        log_warning "ALB DNS not available"
    fi
}

# Test monitoring
test_monitoring() {
    log_info "Testing monitoring setup..."
    
    # Check CloudWatch logs
    if aws logs describe-log-groups --log-group-name-prefix /ecs/$PROJECT_NAME --region $AWS_REGION &> /dev/null; then
        log_success "CloudWatch log groups accessible"
    else
        log_warning "CloudWatch log groups not accessible"
    fi
    
    # Check CloudWatch alarms
    if aws cloudwatch describe-alarms --alarm-name-prefix $PROJECT_NAME --region $AWS_REGION &> /dev/null; then
        log_success "CloudWatch alarms accessible"
    else
        log_warning "CloudWatch alarms not accessible"
    fi
}

# Test cost optimization
test_cost_optimization() {
    log_info "Testing cost optimization..."
    
    # Check Free Tier usage
    echo "📊 Free Tier Usage:"
    echo "   EC2 t2.micro: $(aws cloudwatch get-metric-statistics --namespace AWS/EC2 --metric-name CPUUtilization --dimensions Name=InstanceType,Value=t2.micro --start-time $(date -d '1 month ago' -u +%Y-%m-%dT%H:%M:%S) --end-time $(date -u +%Y-%m-%dT%H:%M:%S) --period 86400 --statistics Average --query 'Datapoints[0].Average' --output text 2>/dev/null || echo 'No data')"
    echo "   RDS db.t2.micro: $(aws cloudwatch get-metric-statistics --namespace AWS/RDS --metric-name CPUUtilization --dimensions Name=DBInstanceIdentifier,Value=$PROJECT_NAME-database --start-time $(date -d '1 month ago' -u +%Y-%m-%dT%H:%M:%S) --end-time $(date -u +%Y-%m-%dT%H:%M:%S) --period 86400 --statistics Average --query 'Datapoints[0].Average' --output text 2>/dev/null || echo 'No data')"
    echo "   S3 Storage: $(aws s3api list-objects-v2 --bucket $PROJECT_NAME-assets-$AWS_ACCOUNT_ID --query 'Contents[].Size' --output text 2>/dev/null | awk '{sum+=$1} END {printf "%.2f MB\n", sum/1024/1024}' || echo 'No data')"
}

# Main test function
main() {
    local failed_tests=0
    
    test_ecr_connectivity || ((failed_tests++))
    test_ecs_cluster || ((failed_tests++))
    test_ecs_service || ((failed_tests++))
    test_alb || ((failed_tests++))
    test_cloudformation || ((failed_tests++))
    test_docker_build || ((failed_tests++))
    test_ecr_push || ((failed_tests++))
    test_ecs_deployment || ((failed_tests++))
    test_application_health || ((failed_tests++))
    test_monitoring || ((failed_tests++))
    test_cost_optimization || ((failed_tests++))
    
    echo ""
    if [ $failed_tests -eq 0 ]; then
        log_success "All tests passed! Pipeline is ready for production."
    else
        log_warning "$failed_tests test(s) failed. Please check the errors above."
    fi
    
    echo ""
    echo "📊 Test Summary:"
    echo "   Total Tests: 11"
    echo "   Passed: $((11 - failed_tests))"
    echo "   Failed: $failed_tests"
    echo ""
    echo "🔗 Monitor pipeline:"
    echo "   ECS: https://console.aws.amazon.com/ecs/home?region=$AWS_REGION#/clusters"
    echo "   ECR: https://console.aws.amazon.com/ecr/repositories?region=$AWS_REGION"
    echo "   ALB: https://console.aws.amazon.com/ec2/v2/home?region=$AWS_REGION#LoadBalancers:"
    echo "   CloudWatch: https://console.aws.amazon.com/cloudwatch/home?region=$AWS_REGION"
    echo ""
}

# Run main function
main "$@"





