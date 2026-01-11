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
        cur.execute("SELECT * FROM categories WHERE name = 'Events'")
        colnames = [desc[0] for desc in cur.description]
        print(f"Columns: {colnames}")
        rows = cur.fetchall()
        print("Sample Row:")
        for row in rows:
            print(row)
        conn.close()
    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    main()
