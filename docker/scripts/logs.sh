#!/bin/bash

# LMS Platform Docker Logs Script

SERVICE=${1:-""}

if [ -z "$SERVICE" ]; then
    echo "📋 Available services:"
    echo "   app, mysql, redis, queue, scheduler, mobile, nginx, mailhog, elasticsearch, kibana"
    echo ""
    echo "Usage: ./logs.sh [service_name]"
    echo "Example: ./logs.sh app"
    exit 1
fi

echo "📊 Showing logs for service: $SERVICE"
echo "Press Ctrl+C to stop following logs"
echo ""

# Follow logs for specified service
docker-compose logs -f $SERVICE





