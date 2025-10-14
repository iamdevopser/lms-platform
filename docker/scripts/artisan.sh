#!/bin/bash

# LMS Platform Docker Artisan Script

if [ -z "$1" ]; then
    echo "❌ Please provide artisan command"
    echo "Usage: ./artisan.sh <command>"
    echo "Examples:"
    echo "   ./artisan.sh migrate"
    echo "   ./artisan.sh db:seed"
    echo "   ./artisan.sh queue:work"
    echo "   ./artisan.sh tinker"
    exit 1
fi

COMMAND=$@

echo "🔧 Running artisan command: $COMMAND"
docker-compose exec app php artisan $COMMAND





