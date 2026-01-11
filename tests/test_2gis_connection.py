#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
تست ساده اتصال به 2GIS
بررسی اینکه آیا سایت قابل دسترسی است یا خیر
"""

from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
import os
import time

# اضافه کردن مسیر ChromeDriver به PATH
os.environ['PATH'] = os.path.expanduser('~/bin') + os.pathsep + os.environ.get('PATH', '')

print("=" * 60)
print("🧪 تست اتصال به 2GIS")
print("=" * 60)

try:
    # تنظیمات Chrome
    chrome_options = Options()
    chrome_options.add_argument("--start-maximized")
    chrome_options.add_argument("--disable-blink-features=AutomationControlled")
    
    # ایجاد driver
    driver = webdriver.Chrome(options=chrome_options)
    
    print("\n🌐 در حال بارگذاری 2GIS...")
    driver.get("https://2gis.am/yerevan")
    
    print("⏳ منتظر بارگذاری...")
    time.sleep(10)
    
    # گرفتن عنوان صفحه
    title = driver.title
    print(f"✅ عنوان صفحه: {title}")
    
    # گرفتن URL فعلی
    current_url = driver.current_url
    print(f"✅ URL فعلی: {current_url}")
    
    # ذخیره screenshot
    driver.save_screenshot("test_2gis_page.png")
    print("📸 Screenshot ذخیره شد: test_2gis_page.png")
    
    # بررسی وجود input ها
    inputs = driver.find_elements(By.TAG_NAME, "input")
    print(f"\n📝 تعداد {len(inputs)} input در صفحه یافت شد")
    
    if len(inputs) > 0:
        print("\nاطلاعات 5 input اول:")
        for i, inp in enumerate(inputs[:5]):
            inp_type = inp.get_attribute('type')
            inp_class = inp.get_attribute('class')
            inp_placeholder = inp.get_attribute('placeholder')
            print(f"  {i+1}. type='{inp_type}', class='{inp_class}', placeholder='{inp_placeholder}'")
    
    # نگه داشتن مرورگر برای 5 ثانیه
    print("\n⏳ نگه داشتن مرورگر برای 5 ثانیه...")
    time.sleep(5)
    
    driver.quit()
    
    print("\n" + "=" * 60)
    print("✅ تست موفق بود!")
    print("   صفحه بارگذاری شد و screenshot گرفته شد")
    print("=" * 60)
    
except Exception as e:
    print(f"\n❌ خطا: {e}")
    print("\n💡 احتمالاً:")
    print("   1. سایت 2GIS در کشور شما فیلتر است")
    print("   2. نیاز به VPN دارید")
    print("   3. یا اتصال اینترنت مشکل دارد")
    print("\n🔧 راه‌حل:")
    print("   - از VPN استفاده کنید")
    print("   - یا از API scraper استفاده کنید (با API Key)")
