#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
2GIS API Scraper for Yerevan Cafes
استخراج اطلاعات کافه‌های ایروان از 2GIS با استفاده از API رسمی

استفاده:
1. ثبت‌نام در https://platform.urbi.ae/ و دریافت API Key
2. جایگزینی YOUR_API_KEY در خط 24
3. اجرا: python 2gis_api_scraper.py
"""

import requests
import json
import csv
import time
from datetime import datetime
from typing import List, Dict, Any

# ========== تنظیمات ==========
API_KEY = "YOUR_API_KEY"  # API Key خود را اینجا قرار دهید
BASE_URL = "https://catalog.api.2gis.com/3.0"
OUTPUT_JSON = "yerevan_cafes.json"
OUTPUT_CSV = "yerevan_cafes.csv"

# مختصات تقریبی مرکز ایروان
YEREVAN_CENTER = {
    "lat": 40.1872,
    "lon": 44.5152
}

# شعاع جستجو به متر (20 کیلومتر برای پوشش کل شهر)
SEARCH_RADIUS = 20000


def search_cafes(api_key: str, page_size: int = 50) -> List[Dict[str, Any]]:
    """
    جستجوی کافه‌ها در ایروان با استفاده از 2GIS API
    
    Args:
        api_key: کلید API دریافتی از 2GIS
        page_size: تعداد نتایج در هر صفحه
        
    Returns:
        لیست کافه‌ها با تمام اطلاعات
    """
    all_cafes = []
    page = 1
    
    print("🔍 شروع جستجوی کافه‌ها در ایروان...")
    
    while True:
        # ساخت URL درخواست
        params = {
            "q": "cafe",  # جستجوی کافه
            "location": f"{YEREVAN_CENTER['lon']},{YEREVAN_CENTER['lat']}",
            "radius": SEARCH_RADIUS,
            "key": api_key,
            "page": page,
            "page_size": page_size,
            "fields": "items.point,items.address,items.contact_groups,items.schedule,items.reviews,items.rubrics"
        }
        
        try:
            response = requests.get(f"{BASE_URL}/items", params=params, timeout=30)
            response.raise_for_status()
            data = response.json()
            
            if "result" not in data or "items" not in data["result"]:
                print(f"⚠️  هیچ نتیجه‌ای در صفحه {page} یافت نشد")
                break
            
            items = data["result"]["items"]
            
            if not items:
                print(f"✅ پایان جستجو - همه کافه‌ها استخراج شدند")
                break
            
            all_cafes.extend(items)
            print(f"📄 صفحه {page}: {len(items)} کافه یافت شد (مجموع: {len(all_cafes)})")
            
            # بررسی وجود صفحه بعدی
            total = data["result"].get("total", 0)
            if len(all_cafes) >= total:
                break
                
            page += 1
            time.sleep(0.5)  # تاخیر برای جلوگیری از rate limiting
            
        except requests.exceptions.RequestException as e:
            print(f"❌ خطا در درخواست API: {e}")
            break
        except json.JSONDecodeError as e:
            print(f"❌ خطا در پردازش JSON: {e}")
            break
    
    return all_cafes


def extract_cafe_info(cafe: Dict[str, Any]) -> Dict[str, Any]:
    """
    استخراج اطلاعات مهم از داده‌های خام کافه
    
    Args:
        cafe: داده‌های خام کافه از API
        
    Returns:
        دیکشنری حاوی اطلاعات مهم کافه
    """
    info = {
        "id": cafe.get("id", ""),
        "name": cafe.get("name", ""),
        "name_ex": cafe.get("name_ex", {}).get("primary", ""),
        "address": "",
        "phone": "",
        "website": "",
        "latitude": "",
        "longitude": "",
        "working_hours": "",
        "rating": "",
        "reviews_count": "",
        "rubrics": []
    }
    
    # آدرس
    address_data = cafe.get("address_name", "")
    if address_data:
        info["address"] = address_data
    
    # مختصات جغرافیایی
    if "point" in cafe:
        point = cafe["point"]
        info["latitude"] = point.get("lat", "")
        info["longitude"] = point.get("lon", "")
    
    # شماره تلفن و وب‌سایت
    contact_groups = cafe.get("contact_groups", [])
    for group in contact_groups:
        contacts = group.get("contacts", [])
        for contact in contacts:
            if contact.get("type") == "phone":
                info["phone"] = contact.get("text", "")
            elif contact.get("type") == "website":
                info["website"] = contact.get("url", "")
    
    # ساعات کاری
    schedule = cafe.get("schedule", {})
    if schedule:
        working_hours = []
        for day in schedule.get("week", []):
            day_name = day.get("day_name", "")
            working_hours_list = day.get("working_hours", [])
            if working_hours_list:
                times = ", ".join([f"{wh.get('from', '')}-{wh.get('to', '')}" for wh in working_hours_list])
                working_hours.append(f"{day_name}: {times}")
        info["working_hours"] = " | ".join(working_hours)
    
    # رتبه‌بندی و نظرات
    reviews = cafe.get("reviews", {})
    if reviews:
        info["rating"] = reviews.get("rating", "")
        info["reviews_count"] = reviews.get("count", "")
    
    # دسته‌بندی‌ها
    rubrics = cafe.get("rubrics", [])
    info["rubrics"] = [r.get("name", "") for r in rubrics]
    
    return info


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
    
    # تبدیل لیست rubrics به رشته
    for cafe in cafes:
        if isinstance(cafe.get("rubrics"), list):
            cafe["rubrics"] = ", ".join(cafe["rubrics"])
    
    fieldnames = cafes[0].keys()
    
    with open(filename, 'w', encoding='utf-8-sig', newline='') as f:
        writer = csv.DictWriter(f, fieldnames=fieldnames)
        writer.writeheader()
        writer.writerows(cafes)
    
    print(f"💾 داده‌ها در {filename} ذخیره شد")


def main():
    """تابع اصلی برنامه"""
    print("=" * 60)
    print("🇦🇲 استخراج اطلاعات کافه‌های ایروان از 2GIS")
    print("=" * 60)
    
    if API_KEY == "YOUR_API_KEY":
        print("\n❌ خطا: لطفاً API Key خود را در خط 24 وارد کنید")
        print("📝 برای دریافت API Key به https://platform.urbi.ae/ مراجعه کنید")
        return
    
    # جستجوی کافه‌ها
    raw_cafes = search_cafes(API_KEY)
    
    if not raw_cafes:
        print("\n❌ هیچ کافه‌ای یافت نشد!")
        return
    
    print(f"\n✅ مجموع {len(raw_cafes)} کافه یافت شد")
    print("\n🔄 پردازش اطلاعات...")
    
    # استخراج اطلاعات مهم
    processed_cafes = [extract_cafe_info(cafe) for cafe in raw_cafes]
    
    # ذخیره در فایل‌ها
    print("\n💾 ذخیره داده‌ها...")
    save_to_json(processed_cafes, OUTPUT_JSON)
    save_to_csv(processed_cafes, OUTPUT_CSV)
    
    # نمایش آمار
    print("\n" + "=" * 60)
    print("📊 آمار نهایی:")
    print(f"   • تعداد کل کافه‌ها: {len(processed_cafes)}")
    print(f"   • کافه‌های با شماره تلفن: {sum(1 for c in processed_cafes if c['phone'])}")
    print(f"   • کافه‌های با وب‌سایت: {sum(1 for c in processed_cafes if c['website'])}")
    print(f"   • کافه‌های با رتبه‌بندی: {sum(1 for c in processed_cafes if c['rating'])}")
    print("=" * 60)
    print(f"\n✅ استخراج با موفقیت انجام شد!")
    print(f"📁 فایل‌های خروجی: {OUTPUT_JSON}, {OUTPUT_CSV}")


if __name__ == "__main__":
    main()
