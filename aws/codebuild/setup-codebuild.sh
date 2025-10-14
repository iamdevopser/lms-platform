#!/bin/bash

# AWS CodeBuild Free Tier Setup Script
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
CODEBUILD_PROJECT_NAME="${PROJECT_NAME}-codebuild"
CODEBUILD_ROLE_NAME="${PROJECT_NAME}-codebuild-role"
S3_BUCKET_NAME="lms-platform-codebuild-$(date +%s)"

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

log_info "Setting up AWS CodeBuild for Free Tier..."

# Create S3 bucket for CodeBuild artifacts
log_info "Creating S3 bucket for CodeBuild artifacts..."
aws s3 mb s3://$S3_BUCKET_NAME --region $AWS_REGION

# Create IAM role for CodeBuild
log_info "Creating IAM role for CodeBuild..."
aws iam create-role \
    --role-name $CODEBUILD_ROLE_NAME \
    --assume-role-policy-document file://codebuild-policy.json

# Attach policy to role
log_info "Attaching policy to CodeBuild role..."
aws iam put-role-policy \
    --role-name $CODEBUILD_ROLE_NAME \
    --policy-name CodeBuildPolicy \
    --policy-document file://codebuild-role-policy.json

# Wait for role to be available
log_info "Waiting for IAM role to be available..."
sleep 10

# Create CodeBuild project
log_info "Creating CodeBuild project..."
aws codebuild create-project \
    --name $CODEBUILD_PROJECT_NAME \
    --description "LMS Platform Free Tier CodeBuild Project" \
    --source type=GITHUB,location=https://github.com/$(git config --get remote.origin.url | sed 's/.*github.com[:/]\([^.]*\).*/\1/'),git-clone-depth=1 \
    --artifacts type=S3,location=$S3_BUCKET_NAME,path=artifacts,packaging=ZIP \
    --environment type=LINUX_CONTAINER,image=aws/codebuild/standard:7.0,compute-type=BUILD_GENERAL1_SMALL \
    --service-role arn:aws:iam::$AWS_ACCOUNT_ID:role/$CODEBUILD_ROLE_NAME \
    --timeout-in-minutes=20 \
    --queued-timeout-in-minutes=30 \
    --environment-variables name=IMAGE_REPO_NAME,value=$PROJECT_NAME name=ECS_TASK_DEFINITION,value=$PROJECT_NAME-task name=ECS_CLUSTER,value=$PROJECT_NAME-cluster name=ECS_SERVICE,value=$PROJECT_NAME-service

# Create webhook for GitHub
log_info "Creating GitHub webhook..."
aws codebuild create-webhook \
    --project-name $CODEBUILD_PROJECT_NAME \
    --filter-groups '[
        {
            "filters": [
                {
                    "type": "EVENT",
                    "pattern": "PUSH"
                },
                {
                    "type": "HEAD_REF",
                    "pattern": "^refs/heads/main$"
                }
            ]
        }
    ]'

log_success "CodeBuild setup completed!"
echo ""
echo "📊 CodeBuild Information:"
echo "   Project Name: $CODEBUILD_PROJECT_NAME"
echo "   S3 Bucket: $S3_BUCKET_NAME"
echo "   IAM Role: $CODEBUILD_ROLE_NAME"
echo "   Region: $AWS_REGION"
echo ""
echo "🔗 AWS Console:"
echo "   CodeBuild: https://console.aws.amazon.com/codebuild/home?region=$AWS_REGION#/projects"
echo "   S3: https://console.aws.amazon.com/s3/home?region=$AWS_REGION"
echo ""
echo "💰 Free Tier Limits:"
echo "   Build minutes: 100 minutes/month"
echo "   Storage: 5GB S3"
echo "   Logs: 5GB CloudWatch"
echo ""





