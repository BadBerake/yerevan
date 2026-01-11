#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
تست سریع اسکریپت Selenium - بررسی عملکرد ChromeDriver

این اسکریپت عملکرد ChromeDriver و Selenium را بررسی می‌کند
"""

from selenium import webdriver
from selenium.webdriver.chrome.options import Options
import os

# اضافه کردن مسیر ChromeDriver به PATH
os.environ['PATH'] = os.path.expanduser('~/bin') + os.pathsep + os.environ.get('PATH', '')

print("=" * 60)
print("🧪 تست ChromeDriver و Selenium")
print("=" * 60)

try:
    print("\n🔧 راه‌اندازی Chrome WebDriver...")
    
    # تنظیمات Chrome
    chrome_options = Options()
    chrome_options.add_argument("--headless")  # بدون نمایش مرورگر
    chrome_options.add_argument("--no-sandbox")
    chrome_options.add_argument("--disable-dev-shm-usage")
    
    # ایجاد driver
    driver = webdriver.Chrome(options=chrome_options)
    
    print("✅ Chrome WebDriver با موفقیت راه‌اندازی شد!")
    
    # تست با یک صفحه ساده
    print("\n🌐 بارگذاری صفحه تست...")
    driver.get("https://www.google.com")
    
    title = driver.title
    print(f"✅ عنوان صفحه: {title}")
    
    # بستن
    driver.quit()
    
    print("\n" + "=" * 60)
    print("✅ تست با موفقیت انجام شد!")
    print("🎉 ChromeDriver آماده استفاده است!")
    print("=" * 60)
    print("\n📌 می‌توانید اسکریپت اصلی را اجرا کنید:")
    print("   python3 2gis_selenium_scraper.py")
    
except Exception as e:
    print(f"\n❌ خطا: {e}")
    print("\n💡 راه‌حل‌های احتمالی:")
    print("1. مطمئن شوید Chrome نصب شده است")
    print("2. مطمئن شوید ChromeDriver در ~/bin قرار دارد")
    print("3. بررسی کنید نسخه ChromeDriver با Chrome مطابقت دارد")
