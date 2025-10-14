#!/bin/bash

# LMS Platform Docker Restore Script

if [ -z "$1" ]; then
    echo "❌ Please provide backup file path"
    echo "Usage: ./restore.sh <backup_file.tar.gz>"
    exit 1
fi

BACKUP_FILE=$1

if [ ! -f "$BACKUP_FILE" ]; then
    echo "❌ Backup file not found: $BACKUP_FILE"
    exit 1
fi

echo "🔄 Starting restore process from: $BACKUP_FILE"

# Extract backup
echo "📦 Extracting backup..."
TEMP_DIR=$(mktemp -d)
tar -xzf $BACKUP_FILE -C $TEMP_DIR

# Stop services
echo "⏹️  Stopping services..."
docker-compose down

# Start only database
echo "🗄️  Starting database..."
docker-compose up -d mysql redis

# Wait for database to be ready
echo "⏳ Waiting for database to be ready..."
sleep 30

# Restore database
echo "🗄️  Restoring database..."
docker-compose exec -T mysql mysql -u root -p${DB_ROOT_PASSWORD:-rootpassword} < $TEMP_DIR/database_*.sql

# Restore application files
echo "📁 Restoring application files..."
docker-compose exec -T app tar -xzf - -C / < $TEMP_DIR/files_*.tar.gz

# Start all services
echo "🚀 Starting all services..."
docker-compose up -d

# Clean up
rm -rf $TEMP_DIR

echo "✅ Restore completed!"
echo "🎉 LMS Platform has been restored from backup!"





