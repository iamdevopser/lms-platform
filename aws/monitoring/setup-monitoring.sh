#!/bin/bash

# AWS Free Tier Monitoring Setup Script
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

log_info "Setting up Free Tier monitoring..."

# Create CloudWatch Dashboard
log_info "Creating CloudWatch Dashboard..."
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
                "title": "ECS Service Metrics"
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
                "title": "RDS Metrics"
            }
        },
        {
            "type": "metric",
            "x": 0,
            "y": 12,
            "width": 12,
            "height": 6,
            "properties": {
                "metrics": [
                    [ "AWS/ElastiCache", "CPUUtilization", "CacheClusterId", "lms-platform-free-cache" ],
                    [ "AWS/ElastiCache", "FreeableMemory", "CacheClusterId", "lms-platform-free-cache" ]
                ],
                "period": 300,
                "stat": "Average",
                "region": "us-east-1",
                "title": "ElastiCache Metrics"
            }
        }
    ]
}'

# Create billing alarms
log_info "Creating billing alarms..."

# Create SNS topic for billing alerts
aws sns create-topic --name lms-platform-billing-alerts

# Get SNS topic ARN
SNS_TOPIC_ARN=$(aws sns list-topics --query 'Topics[?contains(TopicArn, `lms-platform-billing-alerts`)].TopicArn' --output text)

# Create billing alarm for $1
aws cloudwatch put-metric-alarm \
    --alarm-name "LMS-Platform-Billing-1-Dollar" \
    --alarm-description "Alert when monthly charges exceed $1" \
    --metric-name EstimatedCharges \
    --namespace AWS/Billing \
    --statistic Maximum \
    --period 86400 \
    --threshold 1.0 \
    --comparison-operator GreaterThanThreshold \
    --evaluation-periods 1 \
    --alarm-actions $SNS_TOPIC_ARN

# Create billing alarm for $5
aws cloudwatch put-metric-alarm \
    --alarm-name "LMS-Platform-Billing-5-Dollars" \
    --alarm-description "Alert when monthly charges exceed $5" \
    --metric-name EstimatedCharges \
    --namespace AWS/Billing \
    --statistic Maximum \
    --period 86400 \
    --threshold 5.0 \
    --comparison-operator GreaterThanThreshold \
    --evaluation-periods 1 \
    --alarm-actions $SNS_TOPIC_ARN

# Create Free Tier usage alarms
log_info "Creating Free Tier usage alarms..."

# ECS CPU alarm
aws cloudwatch put-metric-alarm \
    --alarm-name "LMS-Platform-ECS-CPU-High" \
    --alarm-description "Alert when ECS CPU usage is high" \
    --metric-name CPUUtilization \
    --namespace AWS/ECS \
    --statistic Average \
    --period 300 \
    --threshold 80.0 \
    --comparison-operator GreaterThanThreshold \
    --evaluation-periods 2 \
    --dimensions Name=ServiceName,Value=lms-platform-free-service

# RDS CPU alarm
aws cloudwatch put-metric-alarm \
    --alarm-name "LMS-Platform-RDS-CPU-High" \
    --alarm-description "Alert when RDS CPU usage is high" \
    --metric-name CPUUtilization \
    --namespace AWS/RDS \
    --statistic Average \
    --period 300 \
    --threshold 80.0 \
    --comparison-operator GreaterThanThreshold \
    --evaluation-periods 2 \
    --dimensions Name=DBInstanceIdentifier,Value=lms-platform-free-database

# Create log groups
log_info "Creating CloudWatch log groups..."
aws logs create-log-group --log-group-name /ecs/lms-platform-free
aws logs create-log-group --log-group-name /aws/codebuild/lms-platform-free

# Set log retention to 7 days (Free Tier optimization)
aws logs put-retention-policy --log-group-name /ecs/lms-platform-free --retention-in-days 7
aws logs put-retention-policy --log-group-name /aws/codebuild/lms-platform-free --retention-in-days 7

log_success "Free Tier monitoring setup completed!"
echo ""
echo "📊 Monitoring Information:"
echo "   Dashboard: https://console.aws.amazon.com/cloudwatch/home?region=$AWS_REGION#dashboards:"
echo "   Alarms: https://console.aws.amazon.com/cloudwatch/home?region=$AWS_REGION#alarmsV2:"
echo "   Logs: https://console.aws.amazon.com/cloudwatch/home?region=$AWS_REGION#logsV2:"
echo ""
echo "💰 Free Tier Limits:"
echo "   CloudWatch Metrics: 10 custom metrics"
echo "   CloudWatch Alarms: 10 alarms"
echo "   CloudWatch Logs: 5GB storage"
echo "   SNS: 100 notifications"
echo ""





