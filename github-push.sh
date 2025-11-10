#!/bin/bash

# GitHub'a Dosya Yükleme Script'i
# Bu script, projenizi GitHub'a adım adım yükler

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

# Adım 1: Git Durumunu Kontrol Et
log_info "Adım 1: Git durumunu kontrol ediliyor..."
echo ""

git status
echo ""

read -p "Devam etmek istiyor musunuz? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    log_warning "İşlem iptal edildi."
    exit 1
fi

# Adım 2: Hassas Dosyaları Kontrol Et
log_info "Adım 2: Hassas dosyalar kontrol ediliyor..."
echo ""

SENSITIVE_FILES=$(git ls-files | grep -E "\.env$|\.pem$|\.key$" || true)

if [ -z "$SENSITIVE_FILES" ]; then
    log_success "Hassas dosya bulunamadı. Güvenli!"
else
    log_error "Hassas dosyalar bulundu:"
    echo "$SENSITIVE_FILES"
    echo ""
    log_warning "Bu dosyalar Git'ten kaldırılmalı!"
    read -p "Hassas dosyaları Git tracking'den kaldırmak istiyor musunuz? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        echo "$SENSITIVE_FILES" | while read -r file; do
            if [ -n "$file" ]; then
                git rm --cached "$file" 2>/dev/null || true
                log_info "Kaldırıldı: $file"
            fi
        done
        log_success "Hassas dosyalar Git tracking'den kaldırıldı."
    fi
fi

echo ""

# Adım 3: Remote'tan Güncellemeleri Çek
log_info "Adım 3: Remote'tan güncellemeler çekiliyor..."
echo ""

git fetch origin

# Remote'ta yeni commit'ler var mı?
REMOTE_COMMITS=$(git log HEAD..origin/main --oneline 2>&1 | wc -l)

if [ "$REMOTE_COMMITS" -gt 0 ]; then
    log_warning "Remote'ta yeni commit'ler var. Pull yapılacak..."
    git pull origin main
    log_success "Remote'tan güncellemeler çekildi."
else
    log_success "Remote'ta yeni commit yok."
fi

echo ""

# Adım 4: Yeni ve Değişen Dosyaları Kontrol Et
log_info "Adım 4: Yeni ve değişen dosyalar kontrol ediliyor..."
echo ""

CHANGED_FILES=$(git status --porcelain | wc -l)

if [ "$CHANGED_FILES" -eq 0 ]; then
    log_warning "Yeni veya değişen dosya yok."
    read -p "Yine de devam etmek istiyor musunuz? (y/n) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        log_warning "İşlem iptal edildi."
        exit 1
    fi
else
    log_info "Yeni veya değişen dosyalar:"
    git status --short
    echo ""
fi

# Adım 5: Dosyaları Stage'e Ekle
log_info "Adım 5: Dosyalar stage'e ekleniyor..."
echo ""

read -p "Tüm dosyaları eklemek istiyor musunuz? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    git add .
    log_success "Tüm dosyalar stage'e eklendi."
else
    log_info "İnteraktif mod: Hangi dosyaları eklemek istersiniz?"
    echo "1. Tüm dosyalar"
    echo "2. Belirli dosyalar (manuel)"
    echo "3. İptal"
    read -p "Seçiminiz (1-3): " choice
    
    case $choice in
        1)
            git add .
            log_success "Tüm dosyalar stage'e eklendi."
            ;;
        2)
            log_info "Dosyaları manuel olarak ekleyin:"
            log_info "git add <dosya-adı>"
            exit 0
            ;;
        3)
            log_warning "İşlem iptal edildi."
            exit 1
            ;;
        *)
            log_error "Geçersiz seçim."
            exit 1
            ;;
    esac
fi

echo ""

# Adım 6: Stage'deki Dosyaları Kontrol Et
log_info "Adım 6: Stage'deki dosyalar kontrol ediliyor..."
echo ""

STAGED_FILES=$(git diff --cached --name-only | wc -l)

if [ "$STAGED_FILES" -eq 0 ]; then
    log_warning "Stage'de dosya yok."
    exit 1
else
    log_success "Stage'de $STAGED_FILES dosya var:"
    git diff --cached --name-only | head -20
    if [ "$STAGED_FILES" -gt 20 ]; then
        log_info "... ve daha fazlası"
    fi
    echo ""
fi

# Adım 7: Commit Yap
log_info "Adım 7: Commit yapılıyor..."
echo ""

read -p "Commit mesajını girin (veya Enter'a basın varsayılan mesaj için): " commit_message

if [ -z "$commit_message" ]; then
    commit_message="feat: Update project files

- Add new deployment files
- Update configuration files
- Update documentation"
fi

git commit -m "$commit_message"
log_success "Commit yapıldı: $commit_message"

echo ""

# Adım 8: GitHub'a Push Yap
log_info "Adım 8: GitHub'a push yapılıyor..."
echo ""

read -p "GitHub'a push yapmak istiyor musunuz? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    git push origin main
    log_success "GitHub'a push yapıldı!"
    echo ""
    log_info "Repository: https://github.com/iamdevopser/lms-platform"
else
    log_warning "Push yapılmadı. Manuel olarak yapabilirsiniz:"
    log_info "git push origin main"
fi

echo ""

# Adım 9: Son Kontrol
log_info "Adım 9: Son kontrol yapılıyor..."
echo ""

git fetch origin
git status

echo ""
log_success "✅ İşlem tamamlandı!"
echo ""
log_info "GitHub repository: https://github.com/iamdevopser/lms-platform"
log_info "Son commit'ler:"
git log --oneline -5

echo ""
log_success "🎉 Başarılı! Dosyalar GitHub'a yüklendi."

