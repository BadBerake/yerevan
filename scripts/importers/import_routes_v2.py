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
        slug = "route"
    return slug

import subprocess

def download_image(url, slug):
    """Downloads image to public/uploads using curl"""
    if not url:
        return "/assets/images/placeholder.jpg"
        
    try:
        filename = f"{slug}.jpg"
        filepath = f"public/uploads/{filename}"
        db_path = f"/uploads/{filename}"
        
        # Ensure dir exists (just in case)
        if not os.path.exists("public/uploads"):
            os.makedirs("public/uploads", exist_ok=True)
        
        if os.path.exists(filepath):
            # Optional: Delete existing to force re-download if using dynamic source
            os.remove(filepath)
            
        print(f"   ⬇️ Downloading image for {filename} from {url}...")
        
        # Use curl to download
        cmd = ["curl", "-L", "-s", "-o", filepath, url]
        result = subprocess.run(cmd, capture_output=True, text=True)
        
        if result.returncode == 0 and os.path.exists(filepath) and os.path.getsize(filepath) > 0:
            return db_path
        else:
            print(f"   ⚠️ Curl failed: {result.stderr}")
            return "/assets/images/placeholder.jpg"

    except Exception as e:
        print(f"   ❌ Download Error: {e}")
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

        # Get Landmarks Category
        cur.execute("SELECT id FROM categories WHERE slug = 'landmarks'")
        category = cur.fetchone()
        if not category:
            # Fallback if add_landmarks.py failed
            cur.execute("INSERT INTO categories (name, slug, type) VALUES ('Landmarks', 'landmarks', 'place') RETURNING id")
            category_id = cur.fetchone()[0]
        else:
            category_id = category[0]

        # Read JSON
        print("📦 Reading tours_data.json...")
        with open('data/tours/tours_data.json', 'r', encoding='utf-8') as f:
            tours = json.load(f)

        imported_count = 0
        for tour in tours:
            name = tour['name']
            slug = generate_slug(name)
            
            # Check if route exists to avoid duplicates (optional, or delete first)
            cur.execute("SELECT id FROM tour_routes WHERE slug = %s", (slug,))
            existing = cur.fetchone()
            if existing:
                print(f"   ⚠️ Route {name} exists, deleting to re-import...")
                cur.execute("DELETE FROM route_stops WHERE route_id = %s", (existing[0],))
                cur.execute("DELETE FROM tour_routes WHERE id = %s", (existing[0],))

            # Download Main Image
            image_url = tour.get('image_url')
            local_image = download_image(image_url, slug)
            
            # Insert Tour Route
            cur.execute("""
                INSERT INTO tour_routes 
                (name, slug, description, interest_tag, estimated_time, difficulty, image_url, name_translations, description_translations)
                VALUES (%s, %s, %s, %s, %s, %s, %s, '[]', '[]')
                RETURNING id
            """, (
                name, slug, tour['description'], 
                tour.get('interest_tag', 'Explore'), 
                tour.get('estimated_time', '3 hours'), 
                tour.get('difficulty', 'easy'),
                local_image
            ))
            route_id = cur.fetchone()[0]
            print(f"   ➕ Created Route: {name} (ID: {route_id})")

            # Process Stops
            itinerary = tour.get('itinerary', [])
            for idx, step in enumerate(itinerary):
                step_title = step['title']
                step_slug = generate_slug(step_title)
                
                # Check if this Place exists in Items
                # We try to match by exact title or slug
                cur.execute("SELECT id FROM items WHERE slug = %s", (step_slug,))
                item_res = cur.fetchone()
                
                if item_res:
                    item_id = item_res[0]
                    # Update coordinates AND image if missing
                    specific_img_url = step.get('image_url')
                    step_img = download_image(specific_img_url, step_slug) if specific_img_url else None
                    
                    if step.get('lat') and step_img:
                        cur.execute("UPDATE items SET latitude = %s, longitude = %s, image_url = %s WHERE id = %s", 
                                  (step['lat'], step['lng'], step_img, item_id))
                    elif step.get('lat'):
                        cur.execute("UPDATE items SET latitude = %s, longitude = %s WHERE id = %s", 
                                  (step['lat'], step['lng'], item_id))
                    elif step_img:
                        cur.execute("UPDATE items SET image_url = %s WHERE id = %s", (step_img, item_id))
                else:
                    # Create new Item (Landmark)
                    print(f"      📍 Creating Landmark: {step_title}")
                    step_desc = step.get('intro', '')
                    
                    # Use specific image from JSON if available, otherwise placeholder/download
                    specific_img_url = step.get('image_url')
                    step_img = download_image(specific_img_url, step_slug)
                    
                    cur.execute("""
                        INSERT INTO items (user_id, category_id, title, slug, description, image_url, latitude, longitude, is_approved)
                        VALUES (%s, %s, %s, %s, %s, %s, %s, %s, TRUE)
                        RETURNING id
                    """, (
                        user_id, category_id, step_title, step_slug, step_desc, step_img, step.get('lat'), step.get('lng')
                    ))
                    item_id = cur.fetchone()[0]

                # Link to Route
                cur.execute("""
                    INSERT INTO route_stops (route_id, item_id, order_index, stop_note, note_translations)
                    VALUES (%s, %s, %s, %s, '[]')
                """, (
                    route_id, item_id, idx, step.get('intro', '')
                ))

            imported_count += 1

        conn.commit()
        print(f"\n✅ Imported {imported_count} routes successfully!")

    except Exception as e:
        print(f"❌ Error: {e}")
        conn.rollback()
    finally:
        conn.close()

if __name__ == "__main__":
    main()
