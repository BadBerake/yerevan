#!/bin/bash

# Yerevango Final Deployment Script
# This script handles DB creation, cloning, and final configurations.

echo "🏁 Starting Final Deployment Steps..."

# --- Configuration (Change these or enter when prompted) ---
DB_NAME="yerevango"
DB_USER="yerevango_user"
read -sp "Enter Password for PostgreSQL User ($DB_USER): " DB_PASS
echo ""
REPO_URL="git@github.com:BadBerake/yerevan.git"
TARGET_DIR="/var/www/html"

# 1. Create PostgreSQL Database and User
echo "🐘 Setting up PostgreSQL database..."
sudo -u postgres psql -c "CREATE DATABASE $DB_NAME;"
sudo -u postgres psql -c "CREATE USER $DB_USER WITH PASSWORD '$DB_PASS';"
sudo -u postgres psql -c "GRANT ALL PRIVILEGES ON DATABASE $DB_NAME TO $DB_USER;"

# 2. Clone Code from GitHub
echo "📂 Cloning repository..."
# Clear directory if it has anything except hidden files to allow cloning into it
sudo rm -rf $TARGET_DIR/*
git clone $REPO_URL $TARGET_DIR

# 3. Update src/config.php with production credentials
echo "⚙️ Updating production configuration..."
CONFIG_FILE="$TARGET_DIR/src/config.php"

if [ -f "$CONFIG_FILE" ]; then
    # Using a temporary file to rebuild the PHP config array with new values
    cat <<EOF | sudo tee $CONFIG_FILE
<?php

return [
    'db' => [
        'host' => 'localhost',
        'port' => '5432',
        'dbname' => '$DB_NAME',
        'user' => '$DB_USER',
        'password' => '$DB_PASS'
    ]
];
EOF
    echo "✅ Configuration updated."
else
    echo "❌ Error: src/config.php not found at $CONFIG_FILE"
fi

# 4. Set Permissions
echo "🔐 Setting folder permissions..."
sudo chown -R www-data:www-data $TARGET_DIR
sudo chmod -R 755 $TARGET_DIR
sudo chmod -R 775 $TARGET_DIR/public/uploads

echo "🎉 Deployment Finalized! Your site should be live."
echo "Remember to run migrations if needed (e.g., import your SQL dump)."
