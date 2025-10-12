#!/bin/bash

# Docker Startup Script for Laravel Application
# This script sets up the Laravel application in Docker

set -e

echo "🚀 Starting PHP Portal Docker Setup..."

# Wait for services to be ready
echo "⏳ Waiting for services to be ready..."

# Wait for MySQL
echo "📦 Waiting for MySQL..."
until docker-compose exec -T mysql mysqladmin ping -h"localhost" --silent; do
    echo "MySQL is unavailable - sleeping"
    sleep 2
done
echo "✅ MySQL is ready!"

# Wait for Redis
echo "📦 Waiting for Redis..."
until docker-compose exec -T redis redis-cli ping; do
    echo "Redis is unavailable - sleeping"
    sleep 2
done
echo "✅ Redis is ready!"

# Wait for MongoDB
echo "📦 Waiting for MongoDB..."
until docker-compose exec -T mongodb mongosh --eval "db.adminCommand('ping')" > /dev/null 2>&1; do
    echo "MongoDB is unavailable - sleeping"
    sleep 2
done
echo "✅ MongoDB is ready!"

# Wait for RabbitMQ
echo "📦 Waiting for RabbitMQ..."
until docker-compose exec -T rabbitmq rabbitmq-diagnostics ping > /dev/null 2>&1; do
    echo "RabbitMQ is unavailable - sleeping"
    sleep 2
done
echo "✅ RabbitMQ is ready!"

echo "🎉 All services are ready!"

# Copy environment file
echo "📝 Setting up environment..."
if [ ! -f .env ]; then
    cp docker.env .env
    echo "✅ Environment file created"
else
    echo "ℹ️  Environment file already exists"
fi

# Install Composer dependencies
echo "📦 Installing Composer dependencies..."
docker-compose exec app composer install --no-interaction --optimize-autoloader

# Generate application key
echo "🔑 Generating application key..."
docker-compose exec app php artisan key:generate

# Run database migrations
echo "🗄️  Running database migrations..."
docker-compose exec app php artisan migrate --force

# Seed database
echo "🌱 Seeding database..."
docker-compose exec app php artisan db:seed --force

# Clear and cache configuration
echo "🧹 Clearing and caching configuration..."
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan view:cache

# Set proper permissions
echo "🔐 Setting proper permissions..."
docker-compose exec app chown -R phportal:phportal /var/www/storage
docker-compose exec app chown -R phportal:phportal /var/www/bootstrap/cache
docker-compose exec app chmod -R 775 /var/www/storage
docker-compose exec app chmod -R 775 /var/www/bootstrap/cache

# Create storage link
echo "🔗 Creating storage link..."
docker-compose exec app php artisan storage:link

# Install Laravel Horizon (if not already installed)
echo "📊 Setting up Laravel Horizon..."
docker-compose exec app composer require laravel/horizon --no-interaction

# Publish Horizon assets
docker-compose exec app php artisan horizon:install

# Clear all caches one more time
echo "🧹 Final cache clearing..."
docker-compose exec app php artisan optimize:clear

echo "🎉 Setup completed successfully!"
echo ""
echo "📋 Service URLs:"
echo "   🌐 Web Application: http://localhost"
echo "   🗄️  MySQL Database: localhost:3310"
echo "   🔴 Redis Cache: localhost:6379"
echo "   🍃 MongoDB: localhost:27017"
echo "   🐰 RabbitMQ Management: http://localhost:15672"
echo "   📊 Laravel Horizon: http://localhost/horizon"
echo ""
echo "🔑 Default Credentials:"
echo "   MySQL: phportal_user / phportal_password"
echo "   MongoDB: phportal_user / phportal_password"
echo "   RabbitMQ: phportal_user / phportal_password"
echo ""
echo "🚀 You can now access your Laravel application!"
