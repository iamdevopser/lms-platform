#!/bin/bash

# LMS Platform - GitHub'a Push Script
# Bu script projeyi temizleyip GitHub'a gönderir

set -e  # Hata durumunda dur

echo "=========================================="
echo "🚀 LMS Platform - GitHub Push Script"
echo "=========================================="
echo ""

# Renkler
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Git kontrolü
if ! command -v git &> /dev/null; then
    echo -e "${RED}❌ Git bulunamadı!${NC}"
    exit 1
fi

# Git repo kontrolü
if [ ! -d ".git" ]; then
    echo -e "${RED}❌ Bu bir git repository değil!${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Git repository bulundu${NC}"
echo ""

# 1. Branch kontrolü
CURRENT_BRANCH=$(git branch --show-current)
echo "📍 Mevcut branch: $CURRENT_BRANCH"
echo ""

# 2. Markdown dosyalarını temizle
echo "📄 Markdown dosyaları temizleniyor..."
ALLOWED_MD=("README.md" "AWS-KURULUM-ADIM-ADIM.md")

MD_FILES=$(git ls-files "*.md" 2>/dev/null || true)
REMOVED_MD=0

if [ -n "$MD_FILES" ]; then
    while IFS= read -r file; do
        # İzin verilen dosyaları atla
        SKIP=false
        for allowed in "${ALLOWED_MD[@]}"; do
            if [ "$file" == "$allowed" ]; then
                SKIP=true
                break
            fi
        done
        
        if [ "$SKIP" = false ]; then
            echo "  🗑️  Siliniyor: $file"
            git rm --cached "$file" 2>/dev/null || true
            [ -f "$file" ] && rm -f "$file" 2>/dev/null || true
            REMOVED_MD=$((REMOVED_MD + 1))
        fi
    done <<< "$MD_FILES"
fi

if [ $REMOVED_MD -eq 0 ]; then
    echo -e "  ${GREEN}✓ Silinecek markdown dosyası yok${NC}"
else
    echo -e "  ${GREEN}✓ $REMOVED_MD markdown dosyası kaldırıldı${NC}"
fi
echo ""

# 3. İç içe lms-platform klasörlerini temizle
echo "📁 İç içe klasörler temizleniyor..."
if [ -d "lms-platform" ]; then
    echo "  🗑️  lms-platform/ klasörü siliniyor..."
    
    # Git'ten kaldır
    NESTED_FILES=$(git ls-files "lms-platform/" 2>/dev/null || true)
    if [ -n "$NESTED_FILES" ]; then
        NESTED_COUNT=$(echo "$NESTED_FILES" | wc -l)
        echo "  📊 Git'te $NESTED_COUNT dosya bulundu, kaldırılıyor..."
        
        # Batch'ler halinde kaldır (çok fazla dosya varsa)
        echo "$NESTED_FILES" | while IFS= read -r file; do
            git rm --cached "$file" 2>/dev/null || true
        done
    fi
    
    # Fiziksel olarak sil
    rm -rf lms-platform/ 2>/dev/null || true
    echo -e "  ${GREEN}✓ İç içe klasör kaldırıldı${NC}"
else
    echo -e "  ${GREEN}✓ İç içe klasör yok${NC}"
fi
echo ""

# 4. .gitignore kontrolü
echo "🔍 .gitignore kontrol ediliyor..."
if ! grep -q "^lms-platform/$" .gitignore 2>/dev/null; then
    echo "lms-platform/" >> .gitignore
    echo -e "  ${GREEN}✓ .gitignore güncellendi${NC}"
else
    echo -e "  ${GREEN}✓ .gitignore zaten güncel${NC}"
fi
echo ""

# 5. Git durumu kontrolü
echo "📊 Git durumu kontrol ediliyor..."
CHANGES=$(git status --short 2>/dev/null | wc -l)

if [ "$CHANGES" -eq 0 ]; then
    echo -e "  ${YELLOW}⚠ Değişiklik yok, push edilecek bir şey yok${NC}"
    echo ""
    echo "✅ İşlem tamamlandı!"
    exit 0
fi

echo "  📝 $CHANGES değişiklik bulundu"
echo ""

# 6. Değişiklikleri göster
echo "📋 Değişiklikler:"
git status --short | head -20
if [ "$CHANGES" -gt 20 ]; then
    echo "  ... ve $((CHANGES - 20)) değişiklik daha"
fi
echo ""

# 7. Kullanıcıya sor
read -p "🤔 Bu değişiklikleri commit edip push etmek istiyor musunuz? (y/n): " -n 1 -r
echo ""

if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo -e "${YELLOW}⚠ İşlem iptal edildi${NC}"
    echo ""
    echo "Değişiklikleri manuel olarak commit etmek için:"
    echo "  git add -A"
    echo "  git commit -m 'chore: cleanup project files'"
    echo "  git push origin $CURRENT_BRANCH"
    exit 0
fi

# 8. Commit
echo "💾 Değişiklikler commit ediliyor..."
git add -A

COMMIT_MSG="chore: cleanup project - remove extra markdown docs and nested folders"
git commit -m "$COMMIT_MSG" || {
    echo -e "${YELLOW}⚠ Commit başarısız (muhtemelen değişiklik yok)${NC}"
    exit 0
}

echo -e "${GREEN}✓ Commit başarılı${NC}"
echo ""

# 9. Push
echo "🚀 GitHub'a push ediliyor..."
read -p "🤔 Hangi branch'e push edilsin? (varsayılan: $CURRENT_BRANCH): " BRANCH
BRANCH=${BRANCH:-$CURRENT_BRANCH}

git push origin "$BRANCH" || {
    echo -e "${RED}❌ Push başarısız!${NC}"
    echo ""
    echo "Manuel olarak push etmek için:"
    echo "  git push origin $BRANCH"
    exit 1
}

echo ""
echo "=========================================="
echo -e "${GREEN}✅ BAŞARILI!${NC}"
echo "=========================================="
echo ""
echo "📦 Proje GitHub'a gönderildi:"
echo "   Branch: $BRANCH"
echo "   Commit: $COMMIT_MSG"
echo ""
echo "🌐 GitHub'da kontrol edin:"
echo "   https://github.com/iamdevopser/lms-platform"
echo ""

