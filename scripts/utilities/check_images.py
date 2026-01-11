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
        
        # Check last 5 items (Landmarks)
        cur.execute("SELECT title, image_url FROM items ORDER BY id DESC LIMIT 5")
        rows = cur.fetchall()
        print("Recent Items (title, image_url):")
        for row in rows:
            print(row)
            
        conn.close()
    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    main()
