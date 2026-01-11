import requests
import shutil
import os

url = "https://upload.wikimedia.org/wikipedia/commons/thumb/5/52/Republic_Square_Yerevan_2019.jpg/1280px-Republic_Square_Yerevan_2019.jpg"
filename = "debug_test.jpg"
filepath = f"public/uploads/{filename}"

print(f"Attempting to download {url} to {filepath}")

# Ensure directory exists
os.makedirs("public/uploads", exist_ok=True)

try:
    headers = {'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)'}
    res = requests.get(url, headers=headers, stream=True, timeout=15)
    
    print(f"Status Code: {res.status_code}")
    
    if res.status_code == 200:
        with open(filepath, 'wb') as f:
            res.raw.decode_content = True
            shutil.copyfileobj(res.raw, f)
        print("✅ Download successful!")
        print(f"File size: {os.path.getsize(filepath)} bytes")
    else:
        print(f"❌ Failed with status {res.status_code}")
        print(res.text[:200])
        
except Exception as e:
    print(f"❌ Exception: {e}")
