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
        
        # Check items for route stops
        cur.execute("""
            SELECT title, image_url 
            FROM items 
            WHERE category_id = (SELECT id FROM categories WHERE slug = 'landmarks')
            ORDER BY id DESC 
            LIMIT 15
        """)
        
        print("Landmark Items:")
        for row in cur.fetchall():
            print(f"{row[0]}: {row[1]}")
            
        conn.close()
    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    main()
