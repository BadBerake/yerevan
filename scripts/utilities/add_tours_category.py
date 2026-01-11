import psycopg2
import os

DB_CONFIG = {
    'host': 'localhost',
    'port': '5432',
    'dbname': 'yerevango',
    'user': 'user',
    'password': 'user'
}

def main():
    try:
        conn = psycopg2.connect(**DB_CONFIG)
        cur = conn.cursor()
        
        # Check if exists
        cur.execute("SELECT id FROM categories WHERE slug = 'tours'")
        if cur.fetchone():
            print("✅ 'Tours' category already exists.")
        else:
            cur.execute("INSERT INTO categories (name, slug, type) VALUES ('Tours', 'tours', 'place')")
            conn.commit()
            print("✅ 'Tours' category added successfully.")
            
        conn.close()
    except Exception as e:
        print(f"❌ Error: {e}")

if __name__ == "__main__":
    main()
