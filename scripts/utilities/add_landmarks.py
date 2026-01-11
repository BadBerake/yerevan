import psycopg2

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
        
        # Check/Add Landmarks
        cur.execute("SELECT id FROM categories WHERE slug = 'landmarks'")
        if not cur.fetchone():
            cur.execute("INSERT INTO categories (name, slug, type) VALUES ('Landmarks', 'landmarks', 'place')")
            conn.commit()
            print("✅ 'Landmarks' category added.")
        else:
            print("✅ 'Landmarks' category exists.")
            
        conn.close()
    except Exception as e:
        print(f"❌ Error: {e}")

if __name__ == "__main__":
    main()
