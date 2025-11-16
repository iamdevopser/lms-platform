#!/bin/bash

# Amazon Linux 2023 AMI ID'sini al
# Kullanım: ./get-ami-id.sh <region>

REGION=${1:-us-east-1}

AMI_ID=$(aws ec2 describe-images \
    --owners amazon \
    --filters "Name=name,Values=al2023-ami-2023.*-x86_64" "Name=state,Values=available" \
    --query 'Images | sort_by(@, &CreationDate) | [-1].ImageId' \
    --output text \
    --region ${REGION})

echo "Region: ${REGION}"
echo "AMI ID: ${AMI_ID}"
echo ""
echo "CloudFormation template'inde şu satırı güncelleyin:"
echo "ImageId: ${AMI_ID}"

