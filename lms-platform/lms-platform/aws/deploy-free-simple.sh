#!/bin/bash

# LMS Platform - Tamamen Ücretsiz AWS Free Tier Deployment
# Sadece EC2 t2.micro kullanır - $0 maliyet

set -e

# Renkler
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Yapılandırma
PROJECT_NAME="lms-platform-free"
AWS_REGION="us-east-1"
STACK_NAME="${PROJECT_NAME}-simple"
INSTANCE_TYPE="t2.micro"

# Fonksiyonlar
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1" >&2
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1" >&2
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1" >&2
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1" >&2
}

# AWS Account ID
AWS_ACCOUNT_ID=$(aws sts get-caller-identity --query Account --output text)

# Key Pair kontrolü
check_key_pair() {
    KEY_PAIR_NAME="${PROJECT_NAME}-key"
    
    log_info "Key pair kontrolü yapılıyor..." >&2
    
    # Key pair var mı kontrol et
    if aws ec2 describe-key-pairs --key-names ${KEY_PAIR_NAME} --region ${AWS_REGION} &> /dev/null; then
        log_warning "Key pair ${KEY_PAIR_NAME} zaten mevcut" >&2
    else
        log_info "Key pair oluşturuluyor: ${KEY_PAIR_NAME}" >&2
        aws ec2 create-key-pair \
            --key-name ${KEY_PAIR_NAME} \
            --region ${AWS_REGION} \
            --query 'KeyMaterial' \
            --output text > ${KEY_PAIR_NAME}.pem 2>/dev/null
        
        chmod 400 ${KEY_PAIR_NAME}.pem
        log_success "Key pair oluşturuldu: ${KEY_PAIR_NAME}.pem" >&2
        log_warning "Bu dosyayı güvenli bir yerde saklayın! SSH bağlantısı için gerekli." >&2
    fi
    
    # Sadece key pair adını stdout'a yazdır
    echo "${KEY_PAIR_NAME}"
}

# CloudFormation stack oluştur
deploy_infrastructure() {
    log_info "CloudFormation stack oluşturuluyor..."
    
    KEY_PAIR_NAME=$(check_key_pair)
    
    # Stack var mı kontrol et
    if aws cloudformation describe-stacks --stack-name ${STACK_NAME} --region ${AWS_REGION} &> /dev/null; then
        log_warning "Stack ${STACK_NAME} zaten mevcut. Güncelleniyor..."
        aws cloudformation update-stack \
            --stack-name ${STACK_NAME} \
            --template-body file://free-tier-simple-infrastructure.yml \
            --capabilities CAPABILITY_IAM \
            --region ${AWS_REGION} \
            --parameters \
                ParameterKey=ProjectName,ParameterValue=${PROJECT_NAME} \
                ParameterKey=KeyPairName,ParameterValue=${KEY_PAIR_NAME}
        
        aws cloudformation wait stack-update-complete \
            --stack-name ${STACK_NAME} \
            --region ${AWS_REGION}
    else
        log_info "Yeni stack oluşturuluyor..."
        aws cloudformation create-stack \
            --stack-name ${STACK_NAME} \
            --template-body file://free-tier-simple-infrastructure.yml \
            --capabilities CAPABILITY_IAM \
            --region ${AWS_REGION} \
            --parameters \
                ParameterKey=ProjectName,ParameterValue=${PROJECT_NAME} \
                ParameterKey=KeyPairName,ParameterValue=${KEY_PAIR_NAME}
        
        log_info "Stack oluşturuluyor... (2-3 dakika sürebilir)"
        aws cloudformation wait stack-create-complete \
            --stack-name ${STACK_NAME} \
            --region ${AWS_REGION}
    fi
    
    log_success "Stack başarıyla oluşturuldu/güncellendi"
}

# Stack bilgilerini al
get_stack_info() {
    log_info "Stack bilgileri alınıyor..."
    
    PUBLIC_IP=$(aws cloudformation describe-stacks \
        --stack-name ${STACK_NAME} \
        --region ${AWS_REGION} \
        --query 'Stacks[0].Outputs[?OutputKey==`InstancePublicIP`].OutputValue' \
        --output text)
    
    PUBLIC_DNS=$(aws cloudformation describe-stacks \
        --stack-name ${STACK_NAME} \
        --region ${AWS_REGION} \
        --query 'Stacks[0].Outputs[?OutputKey==`InstancePublicDNS`].OutputValue' \
        --output text)
    
    S3_BUCKET=$(aws cloudformation describe-stacks \
        --stack-name ${STACK_NAME} \
        --region ${AWS_REGION} \
        --query 'Stacks[0].Outputs[?OutputKey==`S3Bucket`].OutputValue' \
        --output text)
    
    KEY_PAIR_NAME="${PROJECT_NAME}-key"
    
    echo ""
    log_success "✅ Tamamen Ücretsiz Deployment Tamamlandı! 🆓"
    echo ""
    echo "💰 Maliyet: $0 (Tamamen Free Tier)"
    echo ""
    echo "📊 Kullanılan Kaynaklar:"
    echo "   ✅ EC2 t2.micro: 750 saat/ay FREE (12 ay)"
    echo "   ✅ S3: 5GB FREE (12 ay)"
    echo "   ✅ VPC, Security Groups: FREE"
    echo "   ✅ Elastic IP: FREE (instance çalışırken)"
    echo ""
    echo "🌐 Bağlantı Bilgileri:"
    echo "   Public IP: ${PUBLIC_IP}"
    echo "   Public DNS: ${PUBLIC_DNS}"
    echo "   Application URL: http://${PUBLIC_IP}"
    echo "   S3 Bucket: ${S3_BUCKET}"
    echo ""
    echo "🔐 SSH Bağlantısı:"
    echo "   ssh -i ${KEY_PAIR_NAME}.pem ec2-user@${PUBLIC_IP}"
    echo ""
    echo "📝 Sonraki Adımlar:"
    echo "   1. SSH ile bağlan: ssh -i ${KEY_PAIR_NAME}.pem ec2-user@${PUBLIC_IP}"
    echo "   2. Projeyi klonla: git clone <your-repo-url> /home/ec2-user/lms-platform"
    echo "   3. .env dosyasını yapılandır"
    echo "   4. Docker Compose ile başlat: cd /home/ec2-user/lms-platform && docker-compose -f docker-compose.free-tier.yml up -d"
    echo "   5. Migration çalıştır: docker-compose -f docker-compose.free-tier.yml exec app php artisan migrate --force"
    echo "   6. Seeder çalıştır: docker-compose -f docker-compose.free-tier.yml exec app php artisan db:seed --force"
    echo "   7. Uygulamaya eriş: http://${PUBLIC_IP}"
    echo ""
    echo "⚠️  Önemli Notlar:"
    echo "   - Instance'ı kullanmadığınızda durdurmayı unutmayın (maliyet tasarrufu)"
    echo "   - Free Tier limiti: 750 saat/ay (31 gün = 744 saat)"
    echo "   - Test bittikten sonra stack'i silin: aws cloudformation delete-stack --stack-name ${STACK_NAME}"
    echo ""
    echo "🛑 Instance'ı Durdurma:"
    echo "   aws ec2 stop-instances --instance-ids \$(aws cloudformation describe-stack-resources --stack-name ${STACK_NAME} --logical-resource-id EC2Instance --query 'StackResources[0].PhysicalResourceId' --output text)"
    echo ""
    echo "▶️  Instance'ı Başlatma:"
    echo "   aws ec2 start-instances --instance-ids \$(aws cloudformation describe-stack-resources --stack-name ${STACK_NAME} --logical-resource-id EC2Instance --query 'StackResources[0].PhysicalResourceId' --output text)"
    echo ""
}

# Stack'i sil
delete_stack() {
    log_warning "Stack siliniyor..."
    read -p "Emin misiniz? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        aws cloudformation delete-stack --stack-name ${STACK_NAME} --region ${AWS_REGION}
        log_info "Stack siliniyor... Birkaç dakika sürebilir"
        aws cloudformation wait stack-delete-complete --stack-name ${STACK_NAME} --region ${AWS_REGION}
        log_success "Stack başarıyla silindi"
    else
        log_info "İşlem iptal edildi"
    fi
}

# Ana fonksiyon
main() {
    echo "🆓 Tamamen Ücretsiz AWS Free Tier Deployment"
    echo "💰 Maliyet: $0"
    echo ""
    
    case "${1:-deploy}" in
        deploy)
            deploy_infrastructure
            sleep 10  # Instance'ın başlaması için bekle
            get_stack_info
            ;;
        info)
            get_stack_info
            ;;
        delete)
            delete_stack
            ;;
        *)
            echo "Kullanım: $0 [deploy|info|delete]"
            echo "  deploy  - Infrastructure'ı deploy et (varsayılan)"
            echo "  info    - Stack bilgilerini göster"
            echo "  delete  - Stack'i sil"
            exit 1
            ;;
    esac
}

main "$@"

