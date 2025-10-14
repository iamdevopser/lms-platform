#!/bin/bash

# LMS Platform Docker Backup Script

BACKUP_DIR="./backups"
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_NAME="lms_backup_$DATE"

echo "💾 Starting backup process..."

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup database
echo "🗄️  Backing up database..."
docker-compose exec -T mysql mysqldump -u root -p${DB_ROOT_PASSWORD:-rootpassword} --all-databases > $BACKUP_DIR/database_$DATE.sql

# Backup application files
echo "📁 Backing up application files..."
docker-compose exec -T app tar -czf - /var/www/html/storage /var/www/html/public/uploads > $BACKUP_DIR/files_$DATE.tar.gz

# Backup Redis data
echo "🔴 Backing up Redis data..."
docker-compose exec -T redis redis-cli BGSAVE
docker-compose exec -T redis redis-cli LASTSAVE > $BACKUP_DIR/redis_$DATE.txt

# Create backup archive
echo "📦 Creating backup archive..."
tar -czf $BACKUP_DIR/$BACKUP_NAME.tar.gz \
    $BACKUP_DIR/database_$DATE.sql \
    $BACKUP_DIR/files_$DATE.tar.gz \
    $BACKUP_DIR/redis_$DATE.txt

# Clean up individual files
rm $BACKUP_DIR/database_$DATE.sql
rm $BACKUP_DIR/files_$DATE.tar.gz
rm $BACKUP_DIR/redis_$DATE.txt

echo "✅ Backup completed: $BACKUP_DIR/$BACKUP_NAME.tar.gz"

# Clean old backups (keep last 7 days)
echo "🧹 Cleaning old backups..."
find $BACKUP_DIR -name "lms_backup_*.tar.gz" -mtime +7 -delete

echo "🎉 Backup process finished!"





