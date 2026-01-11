import psycopg2
import os

DB_CONFIG = {
    'host': 'localhost',
    'port': '5432',
    'dbname': 'yerevango',
    'user': 'user',
    'password': 'user'
}

def check_table(table_name):
    try:
        conn = psycopg2.connect(**DB_CONFIG)
        cur = conn.cursor()
        cur.execute(f"SELECT * FROM {table_name} LIMIT 1")
        colnames = [desc[0] for desc in cur.description]
        print(f"\nTable: {table_name}")
        print(f"Columns: {colnames}")
        
        rows = cur.fetchall()
        if rows:
            print(f"Sample Row: {rows[0]}")
        else:
            print("No data found.")
        conn.close()
    except Exception as e:
        print(f"Error checking {table_name}: {e}")

if __name__ == "__main__":
    check_table('tour_routes')
    check_table('route_stops')
