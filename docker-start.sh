#!/bin/bash

echo "🚀 PHPortal Docker Setup Starting..."

# Check if .env exists
if [ ! -f .env ]; then
    echo "📝 Creating .env file from docker.env.example..."
    cp docker.env.example .env
    echo "⚠️  Please update .env file with your configuration!"
fi

# Check if global_databases_network exists
if ! docker network ls | grep -q "global_databases_network"; then
    echo "🌐 Creating global_databases_network..."
    docker network create global_databases_network
fi

# Check if global_mysql container exists and is running
if ! docker ps | grep -q "global_mysql"; then
    echo "⚠️  global_mysql container is not running!"
    echo "Please start global_mysql container first:"
    echo "  docker start global_mysql"
    exit 1
fi

# Connect global_mysql to network if not already connected
if ! docker inspect global_mysql | grep -q "global_databases_network"; then
    echo "🔗 Connecting global_mysql to global_databases_network..."
    docker network connect global_databases_network global_mysql || true
fi

# Build and start containers
echo "🔨 Building Docker images..."
docker-compose build

echo "🚀 Starting containers..."
docker-compose up -d

# Wait for services to be ready
echo "⏳ Waiting for services to be ready..."
sleep 10

# Install dependencies
echo "📦 Installing Composer dependencies..."
docker-compose exec -T php composer install --no-interaction

# Generate application key if not exists
if ! grep -q "APP_KEY=base64" .env; then
    echo "🔑 Generating application key..."
    docker-compose exec -T php php artisan key:generate
fi

# Run migrations
echo "🗄️  Running database migrations..."
docker-compose exec -T php php artisan migrate --force

# Clear and cache config
echo "🧹 Clearing and caching configuration..."
docker-compose exec -T php php artisan config:clear
docker-compose exec -T php php artisan cache:clear
docker-compose exec -T php php artisan view:clear
docker-compose exec -T php php artisan route:clear

echo "✅ Setup complete!"
echo ""
echo "🌐 Application URL: http://localhost:8080"
echo "📊 Horizon Dashboard: http://localhost:8080/horizon"
echo ""
echo "Useful commands:"
echo "  docker-compose logs -f          # View logs"
echo "  docker-compose exec php bash    # Enter PHP container"
echo "  docker-compose down             # Stop containers"
echo "  docker-compose restart          # Restart containers"
