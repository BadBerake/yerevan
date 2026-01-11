import requests
import shutil
import os

# One of the new Unsplash URLs
url = "https://images.unsplash.com/photo-1548680327-18305607b1a1?q=80&w=1200"
filename = "debug_unsplash.jpg"
filepath = os.path.abspath(f"public/uploads/{filename}")

print(f"Attempting to download to: {filepath}")

try:
    headers = {'User-Agent': 'Mozilla/5.0'}
    res = requests.get(url, headers=headers, stream=True, timeout=15)
    
    print(f"Status Code: {res.status_code}")
    
    if res.status_code == 200:
        with open(filepath, 'wb') as f:
            res.raw.decode_content = True
            shutil.copyfileobj(res.raw, f)
        print("✅ Download successful!")
        print(f"File size: {os.path.getsize(filepath)} bytes")
        
        # Verify listing
        print("Listing directory:")
        print(os.listdir(os.path.dirname(filepath)))
    else:
        print(f"❌ Failed with status {res.status_code}")
        
except Exception as e:
    print(f"❌ Exception: {e}")
