#!/bin/bash
# ==================================================================
# Post-deployment commands for production server
# Run these commands on the production server after git pull
# ==================================================================

echo "🔄 Clearing Laravel caches..."

# Clear route cache
php artisan route:clear
echo "✓ Route cache cleared"

# Clear config cache
php artisan config:clear
echo "✓ Config cache cleared"

# Clear view cache
php artisan view:clear
echo "✓ View cache cleared"

# Clear general cache
php artisan cache:clear
echo "✓ Application cache cleared"

# Run database migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force
echo "✓ Migrations completed"

# Rebuild optimized caches for production
echo "⚡ Rebuilding optimized caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✓ Caches rebuilt"

# Set proper permissions (adjust paths as needed)
echo "🔐 Setting permissions..."
chmod -R 775 storage bootstrap/cache
echo "✓ Permissions set"

echo ""
echo "✅ Deployment completed successfully!"
echo ""
