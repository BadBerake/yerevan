#!/bin/bash

# مسیر PHP در MAMP
PHP_BIN="/Applications/MAMP/bin/php/php8.4.15/bin/php"

# پورت سرور
PORT=8000

echo "🚀 در حال راه‌اندازی سرور روی http://localhost:$PORT ..."
echo "📂 Document Root: ./public"
echo "💡 برای توقف کلید Ctrl+C را بزنید"
echo "---------------------------------------------"

# اجرا سرور
$PHP_BIN -S localhost:$PORT -t public
