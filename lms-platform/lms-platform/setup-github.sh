#!/bin/bash

# GitHub'a Proje Yükleme Scripti
# Bu script projeyi GitHub'a yüklemek için gerekli adımları otomatikleştirir

set -e

# Renkler
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Fonksiyonlar
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

# Git repository kontrolü
check_git_repo() {
    if [ -d .git ]; then
        log_info "Git repository zaten mevcut"
        return 0
    else
        log_info "Git repository başlatılıyor..."
        git init
        log_success "Git repository başlatıldı"
        return 1
    fi
}

# Hassas dosyaları kontrol et
check_sensitive_files() {
    log_info "Hassas dosyalar kontrol ediliyor..."
    
    # Git repository var mı kontrol et
    if [ ! -d .git ]; then
        log_warning "Git repository henüz başlatılmadı, hassas dosya kontrolü atlanıyor"
        return 0
    fi
    
    HAS_SENSITIVE=false
    
    # Git tarafından takip edilen hassas dosyaları kontrol et
    # Sadece .env ve docker.env dosyalarını kontrol et (example dosyaları hariç)
    TRACKED_ENV_FILES=$(git ls-files | grep -E "\.env$" | grep -v "\.env\.example" || true)
    TRACKED_DOCKER_ENV=$(git ls-files | grep -E "^docker\.env$" || true)
    TRACKED_PEM_FILES=$(git ls-files | grep -E "\.pem$" || true)
    TRACKED_KEY_FILES=$(git ls-files | grep -E "\.key$" | grep -v "storage" || true)
    
    # .env dosyaları kontrolü
    if [ -n "$TRACKED_ENV_FILES" ]; then
        log_error "Git tarafından takip edilen .env dosyası bulundu (önemli!):"
        echo "$TRACKED_ENV_FILES" | while read file; do
            log_error "  - $file"
        done
        HAS_SENSITIVE=true
    fi
    
    # docker.env kontrolü
    if [ -n "$TRACKED_DOCKER_ENV" ]; then
        log_error "Git tarafından takip edilen docker.env dosyası bulundu:"
        echo "$TRACKED_DOCKER_ENV" | while read file; do
            log_error "  - $file"
        done
        HAS_SENSITIVE=true
    fi
    
    # .pem dosyaları kontrolü
    if [ -n "$TRACKED_PEM_FILES" ]; then
        log_error "Git tarafından takip edilen .pem dosyası bulundu:"
        echo "$TRACKED_PEM_FILES" | while read file; do
            log_error "  - $file"
        done
        HAS_SENSITIVE=true
    fi
    
    # .key dosyaları kontrolü (storage'daki key dosyaları hariç)
    if [ -n "$TRACKED_KEY_FILES" ]; then
        log_error "Git tarafından takip edilen .key dosyası bulundu:"
        echo "$TRACKED_KEY_FILES" | while read file; do
            log_error "  - $file"
        done
        HAS_SENSITIVE=true
    fi
    
    if [ "$HAS_SENSITIVE" = true ]; then
        log_error ""
        log_error "❌ HASSAS DOSYALAR GIT'E EKLENMİŞ!"
        log_error "Lütfen şu adımları takip edin:"
        log_error "1. Bu dosyaları git'ten kaldırın: git rm --cached <dosya>"
        log_error "2. .gitignore dosyasını kontrol edin"
        log_error "3. Commit'i düzeltin veya yeni commit yapın"
        exit 1
    else
        log_success "Hassas dosya kontrolü başarılı (git tarafından takip edilen hassas dosya yok)"
        
        # Fiziksel olarak var olan ama git'te olmayan dosyaları bilgilendir
        if [ -f .env ]; then
            if git check-ignore .env > /dev/null 2>&1; then
                log_info "✓ .env dosyası fiziksel olarak mevcut ve git tarafından ignore ediliyor (doğru)"
            else
                log_warning ".env dosyası fiziksel olarak mevcut ama git tarafından ignore edilmiyor"
            fi
        fi
        
        if [ -f docker.env ]; then
            if git check-ignore docker.env > /dev/null 2>&1; then
                log_info "✓ docker.env dosyası fiziksel olarak mevcut ve git tarafından ignore ediliyor (doğru)"
            else
                log_warning "docker.env dosyası fiziksel olarak mevcut ama git tarafından ignore edilmiyor"
            fi
        fi
    fi
}

# .gitignore kontrolü
check_gitignore() {
    log_info ".gitignore dosyası kontrol ediliyor..."
    
    if [ ! -f .gitignore ]; then
        log_error ".gitignore dosyası bulunamadı!"
        exit 1
    fi
    
    # Git repo varsa ignore kontrolü yap
    if [ -d .git ]; then
        # Hassas dosyaların ignore edildiğini kontrol et
        if [ -f .env ] && git check-ignore .env > /dev/null 2>&1; then
            log_success ".env dosyası ignore ediliyor ✓"
        elif [ -f .env ]; then
            log_warning ".env dosyası ignore edilmiyor (git repo yok veya henüz eklenmemiş)"
        fi
        
        if [ -f docker.env ] && git check-ignore docker.env > /dev/null 2>&1; then
            log_success "docker.env dosyası ignore ediliyor ✓"
        elif [ -f docker.env ]; then
            log_warning "docker.env dosyası ignore edilmiyor (git repo yok veya henüz eklenmemiş)"
        fi
    else
        log_info "Git repository henüz başlatılmadı, ignore kontrolü atlanıyor"
    fi
    
    log_success ".gitignore kontrolü tamamlandı"
}

# Dosyaları stage'e ekle
add_files() {
    log_info "Dosyalar stage'e ekleniyor..."
    
    git add .
    
    # Stage'deki dosyaları kontrol et
    STAGED_FILES=$(git diff --cached --name-only)
    if [ -z "$STAGED_FILES" ]; then
        log_warning "Stage'de dosya yok"
    else
        log_success "Dosyalar stage'e eklendi"
        log_info "Stage'deki dosya sayısı: $(echo "$STAGED_FILES" | wc -l)"
    fi
}

# İlk commit
create_initial_commit() {
    log_info "İlk commit oluşturuluyor..."
    
    git commit -m "Initial commit: OnliNote LMS Platform

- Laravel 11 backend
- MySQL database support
- Redis cache
- Stripe payment integration
- Docker support
- AWS Free Tier deployment scripts
- Complete LMS features"

    log_success "İlk commit oluşturuldu"
}

# GitHub repository bilgilerini al
get_github_info() {
    echo ""
    log_info "GitHub repository bilgileri:"
    echo ""
    read -p "GitHub kullanıcı adı: " GITHUB_USERNAME
    read -p "Repository adı (varsayılan: lms-platform): " REPO_NAME
    REPO_NAME=${REPO_NAME:-lms-platform}
    read -p "Repository private mi? (y/n, varsayılan: n): " IS_PRIVATE
    IS_PRIVATE=${IS_PRIVATE:-n}
    
    GITHUB_URL="https://github.com/${GITHUB_USERNAME}/${REPO_NAME}.git"
    
    echo ""
    log_info "Repository URL: ${GITHUB_URL}"
    echo ""
    read -p "Devam etmek istiyor musunuz? (y/n): " CONFIRM
    
    if [ "$CONFIRM" != "y" ]; then
        log_info "İşlem iptal edildi"
        exit 0
    fi
}

# Remote repository ekle
add_remote() {
    log_info "Remote repository ekleniyor..."
    
    if git remote | grep -q origin; then
        log_warning "Remote 'origin' zaten mevcut"
        read -p "Güncellemek istiyor musunuz? (y/n): " UPDATE_REMOTE
        if [ "$UPDATE_REMOTE" = "y" ]; then
            git remote set-url origin "$GITHUB_URL"
            log_success "Remote repository güncellendi"
        fi
    else
        git remote add origin "$GITHUB_URL"
        log_success "Remote repository eklendi"
    fi
}

# Branch adını ayarla
set_branch() {
    log_info "Branch ayarlanıyor..."
    
    CURRENT_BRANCH=$(git branch --show-current 2>/dev/null || echo "main")
    
    if [ "$CURRENT_BRANCH" != "main" ] && [ "$CURRENT_BRANCH" != "master" ]; then
        git checkout -b main 2>/dev/null || git branch -M main
        log_success "Branch 'main' olarak ayarlandı"
    else
        git branch -M main 2>/dev/null || true
        log_success "Branch: main"
    fi
}

# GitHub'a push et
push_to_github() {
    log_info "GitHub'a push ediliyor..."
    
    git push -u origin main
    
    log_success "Dosyalar GitHub'a push edildi!"
    echo ""
    log_success "✅ Repository başarıyla GitHub'a yüklendi!"
    echo ""
    echo "🌐 Repository URL: https://github.com/${GITHUB_USERNAME}/${REPO_NAME}"
    echo ""
}

# Ana fonksiyon
main() {
    echo "🚀 GitHub'a Proje Yükleme Scripti"
    echo "=================================="
    echo ""
    
    # Git repository kontrolü
    IS_NEW_REPO=$(check_git_repo)
    
    # .gitignore kontrolü (önce bu)
    check_gitignore
    
    # Hassas dosyaları kontrol et (sadece git repo varsa)
    if [ -d .git ]; then
        check_sensitive_files
    fi
    
    # Dosyaları stage'e ekle
    add_files
    
    # Eğer yeni repository ise veya commit yoksa
    if [ "$IS_NEW_REPO" = "1" ] || [ -z "$(git log --oneline 2>/dev/null)" ]; then
        create_initial_commit
    else
        log_warning "Zaten commit'ler var. Yeni commit oluşturmak ister misiniz?"
        read -p "Yeni commit oluştur? (y/n): " CREATE_COMMIT
        if [ "$CREATE_COMMIT" = "y" ]; then
            read -p "Commit mesajı: " COMMIT_MESSAGE
            git commit -m "$COMMIT_MESSAGE"
            log_success "Yeni commit oluşturuldu"
        fi
    fi
    
    # GitHub bilgilerini al
    get_github_info
    
    # Remote repository ekle
    add_remote
    
    # Branch ayarla
    set_branch
    
    # GitHub'a push et
    push_to_github
    
    echo ""
    log_success "🎉 İşlem tamamlandı!"
    echo ""
    echo "📝 Sonraki Adımlar:"
    echo "   1. GitHub'da repository'yi kontrol edin"
    echo "   2. README.md'yi gözden geçirin"
    echo "   3. GitHub Pages veya dokümantasyon ekleyin (opsiyonel)"
    echo ""
}

# Script'i çalıştır
main "$@"

