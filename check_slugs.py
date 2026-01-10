import psycopg2
# Hardcoded config
DB_CONFIG = {
    'host': 'localhost',
    'port': '5432',
    'dbname': 'yerevango',
    'user': 'user',
    'password': 'user'
}

def list_items():
    try:
        conn = psycopg2.connect(**DB_CONFIG)
        cur = conn.cursor()
        
        cur.execute("SELECT id, title, slug, category_id FROM items")
        rows = cur.fetchall()
        
        print(f"📊 Total Items Found: {len(rows)}")
        print("-" * 50)
        print(f"{'ID':<5} | {'Title':<30} | {'Slug':<30}")
        print("-" * 50)
        
        for row in rows:
            print(f"{row[0]:<5} | {row[1][:30]:<30} | {row[2]:<30}")
            
        cur.close()
        conn.close()
    except Exception as e:
        print(f"❌ Error: {e}")

if __name__ == "__main__":
    list_items()
