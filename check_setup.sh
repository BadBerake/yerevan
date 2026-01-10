#!/bin/bash
# اسکریپت نصب و راه‌اندازی سریع

echo "🔧 نصب و راه‌اندازی scrapers برای 2GIS"
echo "============================================"

# بررسی Python
echo ""
echo "✅ بررسی Python..."
python3 --version

# بررسی Selenium
echo ""
echo "✅ بررسی Selenium..."
python3 -c "import selenium; print('Selenium نصب شده - نسخه:', selenium.__version__)" 2>/dev/null

if [ $? -ne 0 ]; then
    echo "❌ Selenium نصب نیست!"
    echo "📥 نصب dependencies..."
    pip3 install --user -r requirements.txt
else
    echo "✅ Selenium نصب است"
fi

# بررسی ChromeDriver
echo ""
echo "🔍 بررسی ChromeDriver..."
if command -v chromedriver &> /dev/null; then
    echo "✅ ChromeDriver یافت شد"
    chromedriver --version
else
    echo "⚠️  ChromeDriver یافت نشد"
    echo ""
    echo "برای استفاده از اسکریپت Selenium، ChromeDriver را نصب کنید:"
    echo "  brew install chromedriver"
    echo ""
    echo "یا از اسکریپت API استفاده کنید (نیاز به API Key دارد)"
fi

echo ""
echo "============================================"
echo "📚 راهنماهای موجود:"
echo "  - README_SCRAPER.md  : راهنمای کامل"
echo "  - QUICK_START.md     : شروع سریع"
echo ""
echo "🧪 تست اسکریپت:"
echo "  python3 test_scraper.py"
echo ""
echo "🎬 تولید داده نمونه:"
echo "  python3 generate_sample_data.py"
echo "============================================"
