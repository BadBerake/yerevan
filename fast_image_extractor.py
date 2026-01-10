import json
import requests
from bs4 import BeautifulSoup
import time
import random

JSON_FILE = "yerevan_cafes_selenium.json"
HEADERS = {
    'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
}

def main():
    try:
        with open(JSON_FILE, 'r', encoding='utf-8') as f:
            cafes = json.load(f)
    except FileNotFoundError:
        print("❌ JSON file not found!")
        return

    updated_count = 0
    total = len(cafes)
    
    print(f"🚀 Starting fast image extraction for {total} cafes...")

    for i, cafe in enumerate(cafes):
        url = cafe.get('url')
        name = cafe.get('name')
        
        # Skip if already has image (and it's not empty)
        if cafe.get('image_url'):
            print(f"   [{i+1}/{total}] ✅ Already has image: {name}")
            continue
            
        if not url:
            print(f"   [{i+1}/{total}] ⚠️ No URL for: {name}")
            continue

        print(f"   [{i+1}/{total}] 🌍 Fetching: {name}...")
        
        try:
            # Add delay to be nice
            time.sleep(random.uniform(1.0, 2.5))
            
            resp = requests.get(url, headers=HEADERS, timeout=10)
            if resp.status_code == 200:
                soup = BeautifulSoup(resp.text, 'html.parser')
                
                # Try og:image
                og_image = soup.find("meta", property="og:image")
                image_url = ""
                
                if og_image and og_image.get("content"):
                    image_url = og_image["content"]
                
                # Fallback: specific 2GIS classes? Hard to guess, but og:image usually exists
                
                if image_url:
                    cafe['image_url'] = image_url
                    updated_count += 1
                    print(f"      📸 Found: {image_url[:40]}...")
                else:
                    print("      ⚠️ No og:image found.")
            else:
                print(f"      ❌ HTTP Status: {resp.status_code}")
                
        except Exception as e:
            print(f"      ❌ Error: {e}")

        # Save periodically
        if (i+1) % 2 == 0:
             with open(JSON_FILE, 'w', encoding='utf-8') as f:
                json.dump(cafes, f, ensure_ascii=False, indent=2)

    # Final save
    with open(JSON_FILE, 'w', encoding='utf-8') as f:
        json.dump(cafes, f, ensure_ascii=False, indent=2)
        
    print(f"\n✅ Done! Updated {updated_count} images.")

if __name__ == "__main__":
    main()
