#!/bin/bash

# LMS Platform Docker Setup Script

echo "🚀 Setting up LMS Platform with Docker..."

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Please install Docker first."
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

# Create .env file if it doesn't exist
if [ ! -f .env ]; then
    echo "📝 Creating .env file from docker.env.example..."
    cp docker.env.example .env
    echo "⚠️  Please update .env file with your configuration before continuing."
    echo "Press any key to continue after updating .env file..."
    read -n 1 -s
fi

# Generate application key
echo "🔑 Generating application key..."
docker-compose run --rm app php artisan key:generate

# Run database migrations
echo "🗄️  Running database migrations..."
docker-compose run --rm app php artisan migrate --force

# Seed database
echo "🌱 Seeding database..."
docker-compose run --rm app php artisan db:seed --force

# Create storage symlink
echo "🔗 Creating storage symlink..."
docker-compose run --rm app php artisan storage:link

# Set permissions
echo "🔐 Setting permissions..."
docker-compose run --rm app chown -R www-data:www-data /var/www/html/storage
docker-compose run --rm app chown -R www-data:www-data /var/www/html/bootstrap/cache

# Build and start containers
echo "🏗️  Building and starting containers..."
docker-compose up -d --build

# Wait for services to be ready
echo "⏳ Waiting for services to be ready..."
sleep 30

# Check service health
echo "🏥 Checking service health..."
docker-compose ps

echo "✅ Setup complete!"
echo ""
echo "🌐 Application URLs:"
echo "   Web App: http://localhost"
echo "   Mobile API: http://localhost:3000"
echo "   Mailhog: http://localhost:8025"
echo "   Kibana: http://localhost:5601"
echo ""
echo "📊 Useful commands:"
echo "   View logs: docker-compose logs -f"
echo "   Stop services: docker-compose down"
echo "   Restart services: docker-compose restart"
echo "   Access app container: docker-compose exec app bash"
echo ""
echo "🎉 LMS Platform is now running!"





