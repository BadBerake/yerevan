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
        
        # Check route_stops with their images
        cur.execute("""
            SELECT rs.route_id, r.name, i.title, i.image_url 
            FROM route_stops rs
            JOIN tour_routes r ON rs.route_id = r.id
            JOIN items i ON rs.item_id = i.id
            WHERE rs.route_id IN (17, 18, 19)
            ORDER BY rs.route_id, rs.order_index
        """)
        
        print("Stop Images:")
        for row in cur.fetchall():
            print(f"Route {row[0]} ({row[1]}): {row[2]} -> {row[3]}")
            
        conn.close()
    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    main()
