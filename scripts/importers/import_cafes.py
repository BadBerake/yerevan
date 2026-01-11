#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Import extracted cafe data into PostgreSQL database.
"""

import json
import psycopg2
import re
import os

# Configuration (from src/config.php)
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
    text = text.lower()
    text = re.sub(r'[^a-z0-9]+', '-', text)
    return text.strip('-')

def main():
    print("🔌 Connecting to database...")
    conn = get_db_connection()
    cur = conn.cursor()

    try:
        # 1. Get or Create User
        print("👤 Checking for user...")
        cur.execute("SELECT id FROM users ORDER BY id ASC LIMIT 1")
        user = cur.fetchone()
        
        if not user:
            print("   No users found. Creating default admin...")
            cur.execute(
                "INSERT INTO users (username, email, password_hash, role) VALUES (%s, %s, %s, %s) RETURNING id",
                ('admin', 'admin@example.com', 'hashed_placeholder', 'admin')
            )
            user_id = cur.fetchone()[0]
        else:
            user_id = user[0]
        print(f"✅ Using User ID: {user_id}")

        # 2. Get or Create Category
        print("📂 Checking for 'Cafes' category...")
        cur.execute("SELECT id FROM categories WHERE slug = %s", ('cafes',))
        category = cur.fetchone()

        if not category:
            print("   Category 'Cafes' not found. Creating...")
            cur.execute(
                "INSERT INTO categories (name, slug, type) VALUES (%s, %s, %s) RETURNING id",
                ('Cafes', 'cafes', 'place')
            )
            category_id = cur.fetchone()[0]
        else:
            category_id = category[0]
        print(f"✅ Using Category ID: {category_id}")

        # 3. Read JSON Data
        json_file = 'yerevan_cafes_selenium.json'
        if not os.path.exists(json_file):
            print(f"⚠️  Selenium JSON not found. Checking {json_file}...")
            # Fallback to API JSON if exists
            json_file = 'yerevan_cafes.json'
            if not os.path.exists(json_file):
                print(f"❌ No JSON data files found ({json_file}).")
                exit(1)
        
        print(f"📦 Reading data from {json_file}...")
        with open(json_file, 'r', encoding='utf-8') as f:
            cafes = json.load(f)

        print(f"   Found {len(cafes)} cafes.")

        # 4. Import Items
        inserted_count = 0
        skipped_count = 0

        for cafe in cafes:
            name = cafe.get('name', '').strip()
            if not name:
                continue

            # Generate Slug
            slug = generate_slug(name)
            original_slug = slug
            counter = 1
            
            # Ensure unique slug
            while True:
                cur.execute("SELECT id FROM items WHERE slug = %s", (slug,))
                if not cur.fetchone():
                    break
                slug = f"{original_slug}-{counter}"
                counter += 1

            # Build Description
            description_parts = []
            if cafe.get('phone'):
                description_parts.append(f"Phone: {cafe.get('phone')}")
            if cafe.get('working_hours'):
                description_parts.append(f"Hours: {cafe.get('working_hours')}")
            if cafe.get('website'):
                description_parts.append(f"Website: {cafe.get('website')}")
            if cafe.get('url'):
                description_parts.append(f"2GIS Link: {cafe.get('url')}")
            
            description = "\n".join(description_parts)
            
            # Address
            address = cafe.get('address', '').strip()
            if not address:
                address = 'Yerevan'

            try:
                cur.execute("""
                    INSERT INTO items (user_id, category_id, title, slug, description, address, is_approved)
                    VALUES (%s, %s, %s, %s, %s, %s, %s)
                """, (
                    user_id,
                    category_id,
                    name,
                    slug,
                    description,
                    address,
                    True
                ))
                inserted_count += 1
                print(f"   ➕ Imported: {name}")
            except Exception as e:
                print(f"   ❌ Failed to import {name}: {e}")
                conn.rollback() # Rollback the failed transaction to continue
                skipped_count += 1
                continue
            
            # Commit after each success or batch? 
            # Better to commit at end, but if error happens above rollback is needed for that transaction.
            # Psycopg2 starts a transaction. ONE failure invalidates it until rollback.
            # So I should commit or savepoint.
            # Let's commit every item for simplicity/safety against one bad egg, 
            # or better: Use SAVEPOINT (but complex).
            # Simplest: conn.commit() after each item.
            conn.commit()

        print("\n========================================")
        print("✅ Import Complete!")
        print(f"📊 Inserted: {inserted_count}")
        print(f"⏭️  Skipped: {skipped_count}")
        print("========================================")

    except Exception as e:
        print(f"❌ Critical Error: {e}")
        conn.rollback()
    finally:
        cur.close()
        conn.close()

if __name__ == "__main__":
    main()
