#!/bin/bash

# AWS Free Tier Cost Optimization Script
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

log_info "Starting Free Tier cost optimization..."

# Check Free Tier usage
check_free_tier_usage() {
    log_info "Checking Free Tier usage..."
    
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
        --output text 2>/dev/null || echo "No data available"
    
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
        --output text 2>/dev/null || echo "No data available"
    
    # S3 usage
    echo "📊 S3 Storage Usage:"
    aws s3api list-objects-v2 \
        --bucket lms-platform-free-assets-$AWS_ACCOUNT_ID \
        --query 'Contents[].Size' \
        --output text 2>/dev/null | awk '{sum+=$1} END {printf "%.2f MB\n", sum/1024/1024}' || echo "No data available"
}

# Optimize ECS for Free Tier
optimize_ecs() {
    log_info "Optimizing ECS for Free Tier..."
    
    # Update task definition to use minimum resources
    aws ecs register-task-definition \
        --family lms-platform-free-task \
        --network-mode awsvpc \
        --requires-compatibilities FARGATE \
        --cpu 256 \
        --memory 512 \
        --execution-role-arn arn:aws:iam::$AWS_ACCOUNT_ID:role/ecsTaskExecutionRole \
        --container-definitions '[
            {
                "name": "lms-app",
                "image": "'$AWS_ACCOUNT_ID'.dkr.ecr.us-east-1.amazonaws.com/lms-platform-free:latest",
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
    log_info "Optimizing RDS for Free Tier..."
    
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
    log_info "Optimizing S3 for Free Tier..."
    
    # Set lifecycle policy to minimize costs
    aws s3api put-bucket-lifecycle-configuration \
        --bucket lms-platform-free-assets-$AWS_ACCOUNT_ID \
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

# Clean up unused resources
cleanup_unused_resources() {
    log_info "Cleaning up unused resources..."
    
    # Delete old ECR images
    aws ecr list-images \
        --repository-name lms-platform-free \
        --region us-east-1 \
        --query 'imageIds[?imageTag!=`null`]' \
        --output json | \
    jq -r '.[-5:] | .[].imageTag' > keep_images.txt
    
    aws ecr list-images \
        --repository-name lms-platform-free \
        --region us-east-1 \
        --query 'imageIds[?imageTag!=`null`]' \
        --output json | \
    jq -r '.[] | select(.imageTag | ascii_downcase | test("^[a-f0-9]{7,40}$")) | .imageDigest' | \
    while read digest; do
        if ! grep -q "$digest" keep_images.txt; then
            log_info "Deleting old image: $digest"
            aws ecr batch-delete-image \
                --repository-name lms-platform-free \
                --image-ids imageDigest=$digest \
                --region us-east-1
        fi
    done
    
    rm -f keep_images.txt
}

# Set up cost alerts
setup_cost_alerts() {
    log_info "Setting up cost alerts..."
    
    # Create SNS topic for cost alerts
    aws sns create-topic --name lms-platform-cost-alerts
    
    # Get SNS topic ARN
    SNS_TOPIC_ARN=$(aws sns list-topics --query 'Topics[?contains(TopicArn, `lms-platform-cost-alerts`)].TopicArn' --output text)
    
    # Create cost alert for $1
    aws cloudwatch put-metric-alarm \
        --alarm-name "LMS-Platform-Cost-1-Dollar" \
        --alarm-description "Alert when monthly charges exceed $1" \
        --metric-name EstimatedCharges \
        --namespace AWS/Billing \
        --statistic Maximum \
        --period 86400 \
        --threshold 1.0 \
        --comparison-operator GreaterThanThreshold \
        --evaluation-periods 1 \
        --alarm-actions $SNS_TOPIC_ARN
}

# Main optimization function
main() {
    check_free_tier_usage
    optimize_ecs
    optimize_rds
    optimize_s3
    cleanup_unused_resources
    setup_cost_alerts
    
    log_success "Free Tier cost optimization completed!"
    echo ""
    echo "💰 Cost Optimization Summary:"
    echo "   ECS: Optimized to minimum resources (256 CPU, 512MB RAM)"
    echo "   RDS: Optimized to minimum storage (20GB)"
    echo "   S3: Lifecycle policy applied"
    echo "   ECR: Old images cleaned up"
    echo "   Alerts: Cost monitoring enabled"
    echo ""
    echo "📊 Monitor costs at:"
    echo "   Billing Dashboard: https://console.aws.amazon.com/billing/home#/freetier"
    echo "   Cost Explorer: https://console.aws.amazon.com/cost-management/home#/cost-explorer"
    echo ""
}

# Run main function
main "$@"





