#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
تولید داده‌های نمونه برای نمایش ساختار خروجی
این اسکریپت داده‌های نمونه تولید می‌کند تا ساختار خروجی را نشان دهد
"""

import json
import csv

# داده‌های نمونه کافه‌های ایروان
sample_cafes = [
    {
        "id": "70000001088928304",
        "name": "Achajour",
        "name_ex": "Աշաջուր",
        "address": "Yerevan, Mashtots Avenue, 18",
        "phone": "+374 10 523456",
        "website": "https://achajour.am",
        "latitude": "40.186547",
        "longitude": "44.506545",
        "working_hours": "Mon-Sun: 08:00-23:00",
        "rating": "4.5",
        "reviews_count": "127",
        "rubrics": "Cafe, Restaurant"
    },
    {
        "id": "70000001088928305",
        "name": "Artbridge Bookstore Cafe",
        "name_ex": "Արթբռիջ",
        "address": "Yerevan, Abovyan Street, 20",
        "phone": "+374 10 569874",
        "website": "https://artbridge.am",
        "latitude": "40.177528",
        "longitude": "44.513611",
        "working_hours": "Mon-Sat: 09:00-21:00 | Sun: 10:00-20:00",
        "rating": "4.7",
        "reviews_count": "89",
        "rubrics": "Cafe, Bookstore"
    },
    {
        "id": "70000001088928306",
        "name": "Cascade Cafe",
        "name_ex": "Կասկադ",
        "address": "Yerevan, Tamanyan Street, 10",
        "phone": "+374 10 587456",
        "website": "",
        "latitude": "40.188056",
        "longitude": "44.516389",
        "working_hours": "Mon-Sun: 10:00-22:00",
        "rating": "4.3",
        "reviews_count": "215",
        "rubrics": "Cafe"
    },
    {
        "id": "70000001088928307",
        "name": "Dargett Craft Beer",
        "name_ex": "Դարգետ",
        "address": "Yerevan, Saryan Street, 21",
        "phone": "+374 10 545789",
        "website": "https://dargett.am",
        "latitude": "40.185278",
        "longitude": "44.519444",
        "working_hours": "Mon-Thu: 12:00-23:00 | Fri-Sun: 12:00-01:00",
        "rating": "4.6",
        "reviews_count": "342",
        "rubrics": "Cafe, Bar, Brewery"
    },
    {
        "id": "70000001088928308",
        "name": "Loft Cafe",
        "name_ex": "Լոֆթ",
        "address": "Yerevan, Pushkin Street, 12",
        "phone": "+374 10 598741",
        "website": "",
        "latitude": "40.180833",
        "longitude": "44.512500",
        "working_hours": "Mon-Sun: 09:00-00:00",
        "rating": "4.4",
        "reviews_count": "178",
        "rubrics": "Cafe, Coworking"
    }
]

def save_sample_data():
    """ذخیره داده‌های نمونه در فرمت JSON و CSV"""
    
    print("=" * 60)
    print("📦 تولید داده‌های نمونه")
    print("=" * 60)
    
    # ذخیره JSON
    json_file = "sample_yerevan_cafes.json"
    with open(json_file, 'w', encoding='utf-8') as f:
        json.dump(sample_cafes, f, ensure_ascii=False, indent=2)
    print(f"\n✅ فایل JSON ایجاد شد: {json_file}")
    
    # ذخیره CSV
    csv_file = "sample_yerevan_cafes.csv"
    fieldnames = sample_cafes[0].keys()
    with open(csv_file, 'w', encoding='utf-8-sig', newline='') as f:
        writer = csv.DictWriter(f, fieldnames=fieldnames)
        writer.writeheader()
        writer.writerows(sample_cafes)
    print(f"✅ فایل CSV ایجاد شد: {csv_file}")
    
    # نمایش آمار
    print("\n" + "=" * 60)
    print("📊 آمار داده‌های نمونه:")
    print(f"   • تعداد کل کافه‌ها: {len(sample_cafes)}")
    print(f"   • کافه‌های با شماره تلفن: {sum(1 for c in sample_cafes if c['phone'])}")
    print(f"   • کافه‌های با وب‌سایت: {sum(1 for c in sample_cafes if c['website'])}")
    print(f"   • کافه‌های با رتبه‌بندی: {sum(1 for c in sample_cafes if c['rating'])}")
    print("=" * 60)
    
    # نمایش نمونه داده
    print("\n📄 نمونه اطلاعات یک کافه:\n")
    print(json.dumps(sample_cafes[0], ensure_ascii=False, indent=2))
    print("\n" + "=" * 60)

if __name__ == "__main__":
    save_sample_data()
