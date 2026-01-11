#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
2GIS Selenium Scraper for Yerevan Cafes
استخراج اطلاعات کافه‌های ایروان از 2GIS با استفاده از Browser Automation

استفاده:
1. نصب dependencies: pip install -r requirements.txt
2. اجرا: python 2gis_selenium_scraper.py
"""

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.options import Options
from selenium.common.exceptions import TimeoutException, NoSuchElementException
import json
import csv
import time
from typing import List, Dict, Any
import os

# ========== تنظیمات ==========
# اضافه کردن مسیر ChromeDriver به PATH
os.environ['PATH'] = os.path.expanduser('~/bin') + os.pathsep + os.environ.get('PATH', '')

BASE_URL = "https://2gis.am/yerevan"
SEARCH_QUERY = "cafe"
OUTPUT_JSON = "yerevan_cafes_selenium.json"
OUTPUT_CSV = "yerevan_cafes_selenium.csv"
MAX_CAFES = 10  # تست سریع: 10 کافه


def setup_driver():
    """راه‌اندازی و تنظیمات مرورگر Chrome"""
    chrome_options = Options()
    chrome_options.add_argument("--start-maximized")
    chrome_options.add_argument("--disable-blink-features=AutomationControlled")
    chrome_options.add_experimental_option("excludeSwitches", ["enable-automation"])
    chrome_options.add_experimental_option('useAutomationExtension', False)
    
    # برای اجرای headless (بدون نمایش مرورگر)، خط زیر را uncomment کنید
    # chrome_options.add_argument("--headless")
    
    driver = webdriver.Chrome(options=chrome_options)
    return driver


def search_cafes(driver: webdriver.Chrome) -> bool:
    """
    جستجوی کافه‌ها در 2GIS
    
    Args:
        driver: WebDriver مرورگر
        
    Returns:
        True اگر جستجو موفق باشد، در غیر این صورت False
    """
    try:
        print("🌐 بارگذاری صفحه 2GIS...")
        driver.get(BASE_URL)
        
        # انتظار برای بارگذاری صفحه - timeout افزایش یافته
        wait = WebDriverWait(driver, 30)  # از 15 به 30 ثانیه افزایش یافت
        
        print("⏳ منتظر بارگذاری کامل صفحه...")
        time.sleep(5)  # انتظار اضافی برای بارگذاری کامل
        
        # ذخیره screenshot برای debug
        driver.save_screenshot("debug_page_loaded.png")
        print("📸 Screenshot ذخیره شد: debug_page_loaded.png")
        
        # یافتن کادر جستجو با چند selector مختلف
        print("🔍 جستجوی کافه‌ها...")
        
        search_selectors = [
            "._cu5ae4",  # Selector یافت شده در تست
            "input[placeholder*='Որոնել']",  # ارمنی
            "input[type='text']",  # عمومی‌تر
            "input[type='search']",
            "input[placeholder*='Поиск']",
            "input[placeholder*='Search']",
            "input[class*='search']",
            "input[name='searchQueryInput']",
            ".search-form__input",
            "[data-testid='search-input']"
        ]
        
        search_box = None
        for selector in search_selectors:
            try:
                search_box = wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, selector)))
                print(f"✅ کادر جستجو با selector '{selector}' یافت شد")
                break
            except TimeoutException:
                continue
        
        if not search_box:
            print("❌ کادر جستجو یافت نشد. تلاش برای یافتن تمام input ها...")
            inputs = driver.find_elements(By.TAG_NAME, "input")
            print(f"📝 تعداد {len(inputs)} input یافت شد")
            for i, inp in enumerate(inputs[:5]):  # فقط 5 اولی را چک می‌کنیم
                print(f"   Input {i+1}: type={inp.get_attribute('type')}, class={inp.get_attribute('class')}")
            raise Exception("کادر جستجو پیدا نشد - لطفاً فایل debug_page_loaded.png را بررسی کنید")
        
        # وارد کردن کلمه جستجو
        search_box.clear()
        search_box.send_keys(SEARCH_QUERY)
        time.sleep(2)
        
        # کلیک روی دکمه جستجو یا فشار دادن Enter
        from selenium.webdriver.common.keys import Keys
        search_box.send_keys(Keys.RETURN)
        
        # انتظار برای بارگذاری نتایج
        time.sleep(5)
        
        # ذخیره screenshot نتایج
        driver.save_screenshot("debug_search_results.png")
        print("📸 Screenshot نتایج ذخیره شد: debug_search_results.png")
        
        print("✅ جستجو انجام شد")
        return True
        
    except TimeoutException as e:
        print(f"❌ خطا: صفحه در زمان مقرر بارگذاری نشد")
        print(f"   جزئیات: {str(e)}")
        print("💡 احتمالاً سایت 2GIS در کشور شما فیلتر است یا اتصال اینترنت کند است")
        print("   لطفاً از VPN استفاده کنید یا از API scraper استفاده کنید")
        return False
    except Exception as e:
        print(f"❌ خطا در جستجو: {e}")
        driver.save_screenshot("debug_error.png")
        print("📸 Screenshot خطا ذخیره شد: debug_error.png")
        return False


def scroll_results(driver: webdriver.Chrome, max_scrolls: int = 50):
    """
    اسکرول لیست نتایج برای بارگذاری کافه‌های بیشتر
    
    Args:
        driver: WebDriver مرورگر
        max_scrolls: حداکثر تعداد اسکرول
    """
    print("📜 اسکرول لیست نتایج...")
    
    try:
        # یافتن container لیست نتایج
        results_container = driver.find_element(By.CSS_SELECTOR, "[class*='scroll'], [class*='list'], [class*='results']")
        
        last_height = driver.execute_script("return arguments[0].scrollHeight", results_container)
        scrolls = 0
        
        while scrolls < max_scrolls:
            # اسکرول به پایین
            driver.execute_script("arguments[0].scrollTop = arguments[0].scrollHeight", results_container)
            time.sleep(1.5)
            
            # بررسی تغییر ارتفاع
            new_height = driver.execute_script("return arguments[0].scrollHeight", results_container)
            
            if new_height == last_height:
                print("✅ همه نتایج بارگذاری شدند")
                break
                
            last_height = new_height
            scrolls += 1
            
            if scrolls % 10 == 0:
                print(f"   اسکرول {scrolls}...")
                
    except NoSuchElementException:
        print("⚠️  container نتایج یافت نشد - از اسکرول صفحه استفاده می‌شود")
        
        # اسکرول کل صفحه
        for i in range(max_scrolls):
            driver.execute_script("window.scrollBy(0, 1000)")
            time.sleep(1)


def extract_cafe_list(driver: webdriver.Chrome) -> List[str]:
    """
    استخراج لیست لینک‌های کافه‌ها از نتایج جستجو
    
    Args:
        driver: WebDriver مرورگر
        
    Returns:
        لیست URLهای کافه‌ها
    """
    print("📝 استخراج لیست کافه‌ها...")
    cafe_urls = []
    
    try:
        # Scroll برای بارگذاری همه نتایج
        scroll_results(driver)
        time.sleep(2)
        
        # یافتن تمام آیتم‌های کافه
        # این selector ممکن است نیاز به تنظیم داشته باشد بسته به ساختار HTML سایت
        cafe_elements = driver.find_elements(By.CSS_SELECTOR, "a[href*='/firm/'], article a, [class*='searchResults'] a")
        
        for element in cafe_elements:
            href = element.get_attribute("href")
            if href and "/firm/" in href and href not in cafe_urls:
                cafe_urls.append(href)
                
                if len(cafe_urls) >= MAX_CAFES:
                    print(f"⚠️  رسیدن به حداکثر تعداد ({MAX_CAFES}) - متوقف شد")
                    break
        
        print(f"✅ {len(cafe_urls)} کافه یافت شد")
        
    except Exception as e:
        print(f"❌ خطا در استخراج لیست: {e}")
    
    return cafe_urls


def extract_cafe_details(driver: webdriver.Chrome, url: str) -> Dict[str, Any]:
    """
    استخراج جزئیات یک کافه از صفحه جزئیات
    
    Args:
        driver: WebDriver مرورگر
        url: آدرس صفحه کافه
        
    Returns:
        دیکشنری حاوی اطلاعات کافه
    """
    cafe_info = {
        "url": url,
        "name": "",
        "address": "",
        "phone": "",
        "website": "",
        "latitude": "",
        "longitude": "",
        "working_hours": "",
        "rating": "",
        "reviews_count": ""
    }
    
    try:
        driver.get(url)
        wait = WebDriverWait(driver, 10)
        time.sleep(2)
        
        # نام کافه
        try:
            name = wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "h1, [class*='title'], [class*='firmName']")))
            cafe_info["name"] = name.text.strip()
        except:
            pass
        
        # آدرس
        try:
            address = driver.find_element(By.CSS_SELECTOR, "[class*='address'], [itemprop='address']")
            cafe_info["address"] = address.text.strip()
        except:
            pass
        
        # شماره تلفن
        try:
            phone = driver.find_element(By.CSS_SELECTOR, "a[href^='tel:'], [class*='phone']")
            cafe_info["phone"] = phone.text.strip()
        except:
            pass
        
        # وب‌سایت
        try:
            website = driver.find_element(By.CSS_SELECTOR, "[class*='website'] a, a[class*='link'][href*='http']")
            cafe_info["website"] = website.get_attribute("href")
        except:
            pass
        
        # ساعات کاری
        try:
            schedule = driver.find_element(By.CSS_SELECTOR, "[class*='schedule'], [class*='workingHours']")
            cafe_info["working_hours"] = schedule.text.strip().replace("\n", " | ")
        except:
            pass
        
        # رتبه‌بندی
        try:
            rating = driver.find_element(By.CSS_SELECTOR, "[class*='rating'], [itemprop='ratingValue']")
            cafe_info["rating"] = rating.text.strip()
        except:
            pass
        
        # تعداد نظرات
        try:
            reviews = driver.find_element(By.CSS_SELECTOR, "[class*='reviews'], [class*='reviewsCount']")
            cafe_info["reviews_count"] = reviews.text.strip()
        except:
            pass
        
        # مختصات جغرافیایی از URL
        if "geo/" in url:
            coords = url.split("geo/")[-1].split("/")[0]
            if "," in coords:
                parts = coords.split(",")
                cafe_info["latitude"] = parts[0]
                cafe_info["longitude"] = parts[1] if len(parts) > 1 else ""

        # تصویر اصلی
        try:
            # 1. تلاش برای گرفتن از meta tag (بهترین کیفیت)
            try:
                meta_img = driver.find_element(By.CSS_SELECTOR, 'meta[property="og:image"]')
                img_src = meta_img.get_attribute("content")
                if img_src:
                    cafe_info["image_url"] = img_src
            except:
                pass

            # 2. اگر پیدا نشد، تلاش برای اولین عکس در صفحه
            if not cafe_info.get("image_url"):
                images = driver.find_elements(By.CSS_SELECTOR, "div[class*='sidebar'] img, article img")
                for img in images:
                    src = img.get_attribute("src")
                    if src and "http" in src and "icon" not in src and "logo" not in src:
                        cafe_info["image_url"] = src
                        break
        except Exception as e:
            print(f"⚠️ خطای تصویر: {e}")
        
    except Exception as e:
        print(f"⚠️  خطا در استخراج جزئیات {url}: {e}")
    
    return cafe_info


def save_to_json(cafes: List[Dict[str, Any]], filename: str):
    """ذخیره داده‌ها در فرمت JSON"""
    with open(filename, 'w', encoding='utf-8') as f:
        json.dump(cafes, f, ensure_ascii=False, indent=2)
    print(f"💾 داده‌ها در {filename} ذخیره شد")


def save_to_csv(cafes: List[Dict[str, Any]], filename: str):
    """ذخیره داده‌ها در فرمت CSV"""
    if not cafes:
        print("⚠️  هیچ داده‌ای برای ذخیره وجود ندارد")
        return
    
    fieldnames = cafes[0].keys()
    
    with open(filename, 'w', encoding='utf-8-sig', newline='') as f:
        writer = csv.DictWriter(f, fieldnames=fieldnames)
        writer.writeheader()
        writer.writerows(cafes)
    
    print(f"💾 داده‌ها در {filename} ذخیره شد")


def main():
    """تابع اصلی برنامه"""
    print("=" * 60)
    print("🇦🇲 استخراج اطلاعات کافه‌های ایروان از 2GIS (Selenium)")
    print("=" * 60)
    
    driver = None
    
    try:
        # راه‌اندازی مرورگر
        driver = setup_driver()
        
        # جستجوی کافه‌ها
        if not search_cafes(driver):
            return
        
        # استخراج لیست کافه‌ها
        cafe_urls = extract_cafe_list(driver)
        
        if not cafe_urls:
            print("\n❌ هیچ کافه‌ای یافت نشد!")
            return
        
        print(f"\n✅ {len(cafe_urls)} کافه برای استخراج جزئیات یافت شد")
        print("🔄 در حال استخراج جزئیات...")
        
        # استخراج جزئیات هر کافه
        all_cafes = []
        for i, url in enumerate(cafe_urls, 1):
            print(f"   [{i}/{len(cafe_urls)}] {url}")
            cafe_info = extract_cafe_details(driver, url)
            all_cafes.append(cafe_info)
            time.sleep(1)  # تاخیر برای جلوگیری از بلاک شدن
        
        # ذخیره داده‌ها
        print("\n💾 ذخیره داده‌ها...")
        save_to_json(all_cafes, OUTPUT_JSON)
        save_to_csv(all_cafes, OUTPUT_CSV)
        
        # نمایش آمار
        print("\n" + "=" * 60)
        print("📊 آمار نهایی:")
        print(f"   • تعداد کل کافه‌ها: {len(all_cafes)}")
        print(f"   • کافه‌های با شماره تلفن: {sum(1 for c in all_cafes if c['phone'])}")
        print(f"   • کافه‌های با وب‌سایت: {sum(1 for c in all_cafes if c['website'])}")
        print(f"   • کافه‌های با رتبه‌بندی: {sum(1 for c in all_cafes if c['rating'])}")
        print("=" * 60)
        print(f"\n✅ استخراج با موفقیت انجام شد!")
        print(f"📁 فایل‌های خروجی: {OUTPUT_JSON}, {OUTPUT_CSV}")
        
    except Exception as e:
        print(f"\n❌ خطای کلی: {e}")
        
    finally:
        if driver:
            print("\n🔒 بستن مرورگر...")
            driver.quit()


if __name__ == "__main__":
    main()
