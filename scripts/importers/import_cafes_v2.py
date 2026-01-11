#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Import extracted cafe data into PostgreSQL database.
Updated to populate specific columns: phone, website, working_hours, metadata.
And sets a default image.
"""

import json
import psycopg2
import re
import os
from urllib.parse import urlparse, parse_qs, urlencode, urlunparse

# Configuration
DB_CONFIG = {
    'host': 'localhost',
    'port': '5432',
    'dbname': 'yerevango',
    'user': 'user',
    'password': 'user'
}

DEFAULT_IMAGE = '/assets/images/placeholder-cafe.jpg'

def get_db_connection():
    try:
        conn = psycopg2.connect(**DB_CONFIG)
        return conn
    except Exception as e:
        print(f"❌ Database connection failed: {e}")
        exit(1)

def generate_slug(text, counter=None):
    # Basic slugify for latin chars
    slug = text.lower()
    slug = re.sub(r'[^a-z0-9]+', '-', slug)
    slug = slug.strip('-')
    
    # If slug is empty (e.g. Armenian name), use a fallback base
    if not slug:
        slug = "place"
        
    return slug

import requests
import shutil

# ... (rest of imports)

import random

# Curated list of high-quality cafe images (Unsplash)
CAFE_IMAGES = [
    "https://images.unsplash.com/photo-1554118811-1e0d58224f24?q=80&w=800&auto=format&fit=crop", # Pastry/Coffee
    "https://images.unsplash.com/photo-1509042239860-f550ce710b93?q=80&w=800&auto=format&fit=crop", # Classic Cafe
    "https://images.unsplash.com/photo-1521017432531-fbd92d768814?q=80&w=800&auto=format&fit=crop", # Modern
    "https://images.unsplash.com/photo-1596073419667-9d77d59f033f?q=80&w=800&auto=format&fit=crop", # Garden/Outdoor
    "https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?q=80&w=800&auto=format&fit=crop", # Cozy
    "https://images.unsplash.com/photo-1453614512568-c4024d13c247?q=80&w=800&auto=format&fit=crop"  # Minimalist
]

def get_random_cafe_image(slug):
    """Returns a random cafe image from the list. Uses slug seed for consistency."""
    if not slug:
        return CAFE_IMAGES[0]
    
    # Use slug char sum to pick consistent image
    seed = sum(ord(c) for c in slug)
    return CAFE_IMAGES[seed % len(CAFE_IMAGES)]


def clean_2gis_url(url):
    """Removes tracking parameters from 2GIS URLs."""
    if not url:
        return ""
    try:
        parsed = urlparse(url)
        # Rebuild without query params
        cleaned = urlunparse((parsed.scheme, parsed.netloc, parsed.path, '', '', ''))
        return cleaned
    except:
        return url

def main():
    print("🔌 Connecting to database...")
    conn = get_db_connection()
    cur = conn.cursor()

    try:
        # 1. Get User/Category ID (Reuse logic)
        cur.execute("SELECT id FROM users WHERE username = 'admin'")
        user = cur.fetchone()
        if not user:
             # Fast track: check first user
             cur.execute("SELECT id FROM users LIMIT 1")
             user = cur.fetchone()
             if not user:
                 print("❌ No user found. Create one first.")
                 exit(1)
        user_id = user[0]

        cur.execute("SELECT id FROM categories WHERE slug = 'cafes'")
        category = cur.fetchone()
        category_id = category[0] if category else 1 # Fallback, risky but ok for now

        # 2. CLEAR OLD DATA (Optional, but good for clean slate)
        # Only delete items in the 'cafes' category to be safe? 
        # Or just specific ones extracted. Let's delete by slug prefix check or just overwrite?
        # Let's delete ALL items in 'Cafes' category for a clean import.
        print("🧹 Clearing old cafe data...")
        cur.execute("DELETE FROM items WHERE category_id = %s", (category_id,))
        print(f"   Deleted {cur.rowcount} old items.")

        # 3. Read JSON Data
        json_file = 'yerevan_cafes_selenium.json'
        if not os.path.exists(json_file):
            json_file = 'yerevan_cafes.json'
        
        print(f"📦 Reading data from {json_file}...")
        with open(json_file, 'r', encoding='utf-8') as f:
            cafes = json.load(f)

        # 4. Import Items
        inserted_count = 0

        for cafe in cafes:
            name = cafe.get('name', '').strip()
            if not name:
                continue

            slug = generate_slug(name)
            
            # Ensure unique slug
            original_slug = slug
            counter = 1
            while True:
                # Check DB for collision
                cur.execute("SELECT id FROM items WHERE slug = %s", (slug,))
                if not cur.fetchone():
                    break
                slug = f"{original_slug}-{counter}"
                counter += 1

            # Clean Data
            phone = cafe.get('phone', '').strip()
            # If phone is "Զանգ" (Call) or similar text without number, maybe nullify or keep?
            # User saw "Phone: +374..." so there are some numbers.
            
            website = cafe.get('website', '').strip()
            working_hours = cafe.get('working_hours', '').strip()
            
            raw_url = cafe.get('url', '')
            clean_url = clean_2gis_url(raw_url)
            
            address = cafe.get('address', '').strip() or 'Yerevan'
            
            # Metadata for extra info
            metadata = json.dumps({
                'source': '2GIS',
                'source_url': clean_url,
                'rating': cafe.get('rating'),
                'reviews_count': cafe.get('reviews_count')
            })

            # Image Handling - Use curated images for consistency and quality
            final_image_path = get_random_cafe_image(slug)

            # Description
            description = f"Experience the atmosphere of {name} in Yerevan. A perfect spot to enjoy your time."
            
            # Insert
            try:
                cur.execute("""
                    INSERT INTO items 
                    (user_id, category_id, title, slug, description, address, image_url, is_approved, phone, website, working_hours, metadata)
                    VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
                """, (
                    user_id,
                    category_id,
                    name,
                    slug,
                    description,
                    address,
                    final_image_path,  # Use downloaded image path
                    True,
                    phone,
                    website,
                    working_hours,
                    metadata
                ))
                inserted_count += 1
                print(f"   ➕ Imported: {name}")
            except Exception as e:
                print(f"   ❌ Failed to import {name}: {e}")
                conn.rollback()
                continue
            
            conn.commit()

        print("\n========================================")
        print(f"✅ Re-Import Complete! Inserted: {inserted_count}")
        print("========================================")

    except Exception as e:
        print(f"❌ Critical Error: {e}")
        conn.rollback()
    finally:
        cur.close()
        conn.close()

if __name__ == "__main__":
    main()
