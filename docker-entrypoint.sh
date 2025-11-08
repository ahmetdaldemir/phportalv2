#!/bin/bash

# Set proper permissions for Laravel
echo "Setting Laravel permissions..."

# Create necessary directories if they don't exist
mkdir -p /var/www/storage/logs
mkdir -p /var/www/storage/framework/cache
mkdir -p /var/www/storage/framework/sessions
mkdir -p /var/www/storage/framework/views
mkdir -p /var/www/storage/app/public
mkdir -p /var/www/bootstrap/cache

# Set ownership
chown -R www-data:www-data /var/www/storage
chown -R www-data:www-data /var/www/bootstrap/cache

# Set permissions
chmod -R 775 /var/www/storage
chmod -R 775 /var/www/bootstrap/cache
chmod -R 775 /var/www/storage/logs
chmod -R 775 /var/www/storage/framework/cache
chmod -R 775 /var/www/storage/framework/sessions
chmod -R 775 /var/www/storage/framework/views
chmod -R 775 /var/www/storage/app/public

echo "Permissions set successfully!"

# Execute the main command
exec "$@"
