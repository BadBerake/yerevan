#!/bin/bash

# Yerevango Server Setup Script
# Target OS: Ubuntu 22.04 LTS
# Installs: Nginx, PHP 8.2, PostgreSQL, Git, Python

echo "🚀 Starting Yerevango Server Setup..."

# 1. Update System
sudo apt update && sudo apt upgrade -y

# 2. Install Nginx
sudo apt install nginx -y

# 3. Install PHP 8.2 & Extensions
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install php8.2-fpm php8.2-pgsql php8.2-gd php8.2-curl php8.2-mbstring php8.2-xml php8.2-zip -y

# 4. Install PostgreSQL
sudo apt install postgresql postgresql-contrib -y

# 5. Install Git & Python
sudo apt install git python3 python3-pip -y

# 6. Configure Nginx for Yerevango (Basic)
cat <<EOF | sudo tee /etc/nginx/sites-available/yerevango
server {
    listen 80;
    server_name _; # Change this to your domain
    root /var/www/html/public;
    index index.php index.html;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
EOF

sudo ln -s /etc/nginx/sites-available/yerevango /etc/nginx/sites-enabled/
sudo rm /etc/nginx/sites-enabled/default
sudo nginx -t && sudo systemctl restart nginx

echo "✅ Basic Setup Complete!"
echo "------------------------------------------------"
echo "Next Steps:"
echo "1. Create PostgreSQL database and user."
echo "2. Clone your code from GitHub to /var/www/html."
echo "3. Update src/config.php with production DB credentials."
echo "4. Set permissions: sudo chown -R www-data:www-data /var/www/html/public/uploads"
echo "------------------------------------------------"
