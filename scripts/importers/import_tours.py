import json
import psycopg2
import re
import os
import requests
import shutil

# Configuration
DB_CONFIG = {
    'host': 'localhost',
    'port': '5432',
    'dbname': 'yerevango',
    'user': 'user',
    'password': 'user'
}

def get_db_connection():
    try:
        conn = psycopg2.connect(**DB_CONFIG)
        return conn
    except Exception as e:
        print(f"❌ Database connection failed: {e}")
        exit(1)

def generate_slug(text):
    slug = text.lower()
    slug = re.sub(r'[^a-z0-9]+', '-', slug)
    slug = slug.strip('-')
    if not slug:
        slug = "tour"
    return slug

def download_image(url, slug):
    """Downloads image to public/uploads"""
    if not url:
        return "/assets/images/placeholder.jpg"
        
    try:
        filename = f"{slug}.jpg"
        filepath = f"public/uploads/{filename}"
        db_path = f"/uploads/{filename}"
        
        if os.path.exists(filepath):
            return db_path
            
        print(f"   ⬇️ Downloading image for {slug}...")
        headers = {'User-Agent': 'Mozilla/5.0'}
        res = requests.get(url, headers=headers, stream=True, timeout=15)
        if res.status_code == 200:
            with open(filepath, 'wb') as f:
                res.raw.decode_content = True
                shutil.copyfileobj(res.raw, f)
            return db_path
        else:
            return "/assets/images/placeholder.jpg"
    except:
        return "/assets/images/placeholder.jpg"

def main():
    print("🔌 Connecting to database...")
    conn = get_db_connection()
    cur = conn.cursor()

    try:
        # Get Admin User
        cur.execute("SELECT id FROM users LIMIT 1")
        user = cur.fetchone()
        user_id = user[0] if user else 1

        # Get Tours Category
        cur.execute("SELECT id FROM categories WHERE slug = 'tours'")
        category = cur.fetchone()
        if not category:
            print("❌ 'Tours' category not found!")
            return
        category_id = category[0]

        # Clear old tours
        print("🧹 Clearing old tour data...")
        cur.execute("DELETE FROM items WHERE category_id = %s", (category_id,))
        print(f"   Deleted {cur.rowcount} old tours.")

        # Read JSON
        print("📦 Reading tours_data.json...")
        with open('tours_data.json', 'r', encoding='utf-8') as f:
            tours = json.load(f)

        imported_count = 0
        for tour in tours:
            name = tour['name']
            slug = generate_slug(name)
            
            image_url = tour.get('image_url')
            local_image = download_image(image_url, slug)
            
            description = tour['description']
            address = tour['address']
            working_hours = tour['working_hours']
            phone = tour['phone']
            website = tour['website']
            
            # Save itinerary to metadata
            itinerary = tour.get('itinerary', [])
            metadata = json.dumps({
                'type': 'tour', 
                'difficulty': 'Easy',
                'itinerary': itinerary
            })

            cur.execute("""
                INSERT INTO items 
                (user_id, category_id, title, slug, description, address, image_url, is_approved, phone, website, working_hours, metadata)
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
            """, (
                user_id, category_id, name, slug, description, address, local_image, True, phone, website, working_hours, metadata
            ))
            imported_count += 1
            print(f"   ➕ Imported: {name}")

        conn.commit()
        print(f"\n✅ Imported {imported_count} tours successfully!")

    except Exception as e:
        print(f"❌ Error: {e}")
        conn.rollback()
    finally:
        conn.close()

if __name__ == "__main__":
    main()
