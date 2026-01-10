from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options
import json
import time
import os

JSON_FILE = "yerevan_cafes_selenium.json"

def setup_driver():
    chrome_options = Options()
    chrome_options.add_argument("--start-maximized")
    chrome_options.add_argument("--disable-blink-features=AutomationControlled")
    chrome_options.add_experimental_option("excludeSwitches", ["enable-automation"])
    chrome_options.add_experimental_option('useAutomationExtension', False)
    # chrome_options.add_argument("--headless") # Optional
    
    # Path to chromedriver if needed, but assuming it's in PATH now
    os.environ['PATH'] = os.path.expanduser('~/bin') + os.pathsep + os.environ.get('PATH', '')
    
    return webdriver.Chrome(options=chrome_options)

def main():
    if not os.path.exists(JSON_FILE):
        print("❌ JSON file not found!")
        return

    with open(JSON_FILE, 'r', encoding='utf-8') as f:
        cafes = json.load(f)
        
    driver = setup_driver()
    
    try:
        updated_count = 0
        for i, cafe in enumerate(cafes):
            url = cafe.get('url')
            if not url:
                continue
                
            print(f"[{i+1}/{len(cafes)}] Visiting {cafe.get('name')}...")
            
            try:
                driver.get(url)
                time.sleep(3) # Wait for load
                
                image_url = ""
                
                # 1. Meta tag
                try:
                    meta = driver.find_element(By.CSS_SELECTOR, 'meta[property="og:image"]')
                    image_url = meta.get_attribute("content")
                except:
                    pass
                
                # 2. Gallery images
                if not image_url:
                    try:
                        imgs = driver.find_elements(By.CSS_SELECTOR, "div[class*='sidebar'] img, article img")
                        for img in imgs:
                            src = img.get_attribute("src")
                            if src and "http" in src and "icon" not in src and "logo" not in src:
                                image_url = src
                                break
                    except:
                        pass
                
                if image_url:
                    cafe['image_url'] = image_url
                    print(f"   ✅ Found image: {image_url[:50]}...")
                    updated_count += 1
                else:
                    print("   ⚠️ No image found.")
                    
            except Exception as e:
                print(f"   ❌ Error visiting {url}: {e}")
                
            # Save incrementally just in case
            if (i+1) % 1 == 0:
                with open(JSON_FILE, 'w', encoding='utf-8') as f:
                    json.dump(cafes, f, ensure_ascii=False, indent=2)

    finally:
        driver.quit()
        print(f"\n✅ Finished! Updated {updated_count} cafes.")

if __name__ == "__main__":
    main()
