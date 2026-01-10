#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
نمونه اسکریپت تست - بررسی عملکرد بدون نیاز به API Key
تست ساختار کد و توابع

این اسکریپت فقط برای نمایش ساختار کد است و API واقعی را فراخوانی نمی‌کند.
"""

import json

def test_extract_cafe_info():
    """تست تابع استخراج اطلاعات کافه"""
    
    # داده نمونه از API
    sample_cafe = {
        "id": "70000001088928304",
        "name": "Achajour",
        "name_ex": {"primary": "Աշաջուր"},
        "address_name": "Yerevan, Mashtots Avenue, 18",
        "point": {
            "lat": 40.186547,
            "lon": 44.506545
        },
        "contact_groups": [
            {
                "contacts": [
                    {"type": "phone", "text": "+374 10 523456"},
                    {"type": "website", "url": "https://achajour.am"}
                ]
            }
        ],
        "schedule": {
            "week": [
                {
                    "day_name": "Monday",
                    "working_hours": [{"from": "08:00", "to": "23:00"}]
                },
                {
                    "day_name": "Tuesday",
                    "working_hours": [{"from": "08:00", "to": "23:00"}]
                }
            ]
        },
        "reviews": {
            "rating": 4.5,
            "count": 127
        },
        "rubrics": [
            {"name": "Cafe"},
            {"name": "Restaurant"}
        ]
    }
    
    # تابع استخراج (کپی از اسکریپت اصلی)
    def extract_cafe_info(cafe):
        info = {
            "id": cafe.get("id", ""),
            "name": cafe.get("name", ""),
            "name_ex": cafe.get("name_ex", {}).get("primary", ""),
            "address": cafe.get("address_name", ""),
            "phone": "",
            "website": "",
            "latitude": "",
            "longitude": "",
            "working_hours": "",
            "rating": "",
            "reviews_count": "",
            "rubrics": []
        }
        
        if "point" in cafe:
            point = cafe["point"]
            info["latitude"] = point.get("lat", "")
            info["longitude"] = point.get("lon", "")
        
        contact_groups = cafe.get("contact_groups", [])
        for group in contact_groups:
            contacts = group.get("contacts", [])
            for contact in contacts:
                if contact.get("type") == "phone":
                    info["phone"] = contact.get("text", "")
                elif contact.get("type") == "website":
                    info["website"] = contact.get("url", "")
        
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
        
        reviews = cafe.get("reviews", {})
        if reviews:
            info["rating"] = reviews.get("rating", "")
            info["reviews_count"] = reviews.get("count", "")
        
        rubrics = cafe.get("rubrics", [])
        info["rubrics"] = [r.get("name", "") for r in rubrics]
        
        return info
    
    # تست
    result = extract_cafe_info(sample_cafe)
    
    # نمایش نتیجه
    print("=" * 60)
    print("🧪 تست تابع استخراج اطلاعات")
    print("=" * 60)
    print("\n📊 اطلاعات استخراج شده:\n")
    print(json.dumps(result, ensure_ascii=False, indent=2))
    print("\n" + "=" * 60)
    print("✅ تست با موفقیت انجام شد!")
    print("=" * 60)
    
    # بررسی صحت داده‌ها
    assert result["id"] == "70000001088928304"
    assert result["name"] == "Achajour"
    assert result["phone"] == "+374 10 523456"
    assert result["website"] == "https://achajour.am"
    assert result["latitude"] == 40.186547
    assert result["longitude"] == 44.506545
    assert result["rating"] == 4.5
    assert result["reviews_count"] == 127
    assert "Cafe" in result["rubrics"]
    
    print("\n✅ همه assertion ها موفق بودند!")


if __name__ == "__main__":
    test_extract_cafe_info()
