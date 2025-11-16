#!/bin/bash

# ECR Image Management Script for Free Tier
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

# Function to list images
list_images() {
    log_info "Listing ECR images..."
    aws ecr list-images \
        --repository-name ${ECR_REPOSITORY_NAME} \
        --region ${AWS_REGION} \
        --query 'imageIds[*].[imageTag,imageDigest,imagePushedAt]' \
        --output table
}

# Function to get image details
get_image_details() {
    local image_tag=${1:-"latest"}
    log_info "Getting details for image: ${image_tag}"
    
    aws ecr describe-images \
        --repository-name ${ECR_REPOSITORY_NAME} \
        --image-ids imageTag=${image_tag} \
        --region ${AWS_REGION} \
        --query 'imageDetails[0].[imageTags,imageSizeInBytes,imagePushedAt,imageScanStatus]' \
        --output table
}

# Function to get image scan results
get_scan_results() {
    local image_tag=${1:-"latest"}
    log_info "Getting scan results for image: ${image_tag}"
    
    aws ecr describe-image-scan-findings \
        --repository-name ${ECR_REPOSITORY_NAME} \
        --image-id imageTag=${image_tag} \
        --region ${AWS_REGION} \
        --query 'imageScanFindings.findingCounts' \
        --output table
}

# Function to clean up old images
cleanup_old_images() {
    log_info "Cleaning up old images (keeping last 5)..."
    
    # Get all images sorted by push date
    aws ecr list-images \
        --repository-name ${ECR_REPOSITORY_NAME} \
        --region ${AWS_REGION} \
        --query 'imageIds[?imageTag!=`null`]' \
        --output json > images.json
    
    # Keep only the last 5 images
    jq -r '.[-5:] | .[].imageTag' images.json > keep_images.txt
    
    # Delete old images
    aws ecr list-images \
        --repository-name ${ECR_REPOSITORY_NAME} \
        --region ${AWS_REGION} \
        --query 'imageIds[?imageTag!=`null`]' \
        --output json | \
    jq -r '.[] | select(.imageTag | ascii_downcase | test("^[a-f0-9]{7,40}$")) | .imageDigest' | \
    while read digest; do
        if ! grep -q "$digest" keep_images.txt; then
            log_info "Deleting image: $digest"
            aws ecr batch-delete-image \
                --repository-name ${ECR_REPOSITORY_NAME} \
                --image-ids imageDigest=$digest \
                --region ${AWS_REGION}
        fi
    done
    
    rm -f images.json keep_images.txt
    log_success "Old images cleaned up"
}

# Function to get repository size
get_repository_size() {
    log_info "Getting repository size..."
    
    aws ecr describe-images \
        --repository-name ${ECR_REPOSITORY_NAME} \
        --region ${AWS_REGION} \
        --query 'imageDetails[].imageSizeInBytes' \
        --output text | \
    awk '{sum+=$1} END {printf "Total size: %.2f MB\n", sum/1024/1024}'
}

# Function to build and push image
build_and_push() {
    local image_tag=${1:-"latest"}
    log_info "Building and pushing image: ${image_tag}"
    
    # Login to ECR
    aws ecr get-login-password --region ${AWS_REGION} | \
    docker login --username AWS --password-stdin ${ECR_REGISTRY}
    
    # Build image
    docker build -f Dockerfile.free-tier -t ${ECR_REPOSITORY_NAME}:${image_tag} .
    
    # Tag image
    docker tag ${ECR_REPOSITORY_NAME}:${image_tag} ${ECR_REGISTRY}/${ECR_REPOSITORY_NAME}:${image_tag}
    docker tag ${ECR_REPOSITORY_NAME}:${image_tag} ${ECR_REGISTRY}/${ECR_REPOSITORY_NAME}:latest
    
    # Push image
    docker push ${ECR_REGISTRY}/${ECR_REPOSITORY_NAME}:${image_tag}
    docker push ${ECR_REGISTRY}/${ECR_REPOSITORY_NAME}:latest
    
    log_success "Image pushed successfully"
}

# Function to pull image
pull_image() {
    local image_tag=${1:-"latest"}
    log_info "Pulling image: ${image_tag}"
    
    # Login to ECR
    aws ecr get-login-password --region ${AWS_REGION} | \
    docker login --username AWS --password-stdin ${ECR_REGISTRY}
    
    # Pull image
    docker pull ${ECR_REGISTRY}/${ECR_REPOSITORY_NAME}:${image_tag}
    
    log_success "Image pulled successfully"
}

# Function to show help
show_help() {
    echo "ECR Image Management Script for Free Tier"
    echo ""
    echo "Usage: $0 [COMMAND] [OPTIONS]"
    echo ""
    echo "Commands:"
    echo "  list                    List all images"
    echo "  details [TAG]          Show image details"
    echo "  scan [TAG]             Show scan results"
    echo "  cleanup                Clean up old images"
    echo "  size                   Show repository size"
    echo "  build [TAG]            Build and push image"
    echo "  pull [TAG]             Pull image"
    echo "  help                   Show this help"
    echo ""
    echo "Examples:"
    echo "  $0 list"
    echo "  $0 details latest"
    echo "  $0 build v1.0.0"
    echo "  $0 pull latest"
    echo "  $0 cleanup"
}

# Main function
main() {
    case "${1:-help}" in
        list)
            list_images
            ;;
        details)
            get_image_details "$2"
            ;;
        scan)
            get_scan_results "$2"
            ;;
        cleanup)
            cleanup_old_images
            ;;
        size)
            get_repository_size
            ;;
        build)
            build_and_push "$2"
            ;;
        pull)
            pull_image "$2"
            ;;
        help|*)
            show_help
            ;;
    esac
}

# Run main function
main "$@"





