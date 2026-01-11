import psycopg2
# Config is hardcoded below

# Hardcoded config as reading PHP config from Python is tricky without a parser
DB_CONFIG = {
    'host': 'localhost',
    'port': '5432',
    'dbname': 'yerevango',
    'user': 'user',
    'password': 'user'
}

def migrate():
    try:
        conn = psycopg2.connect(**DB_CONFIG)
        cur = conn.cursor()
        
        print("🔌 Connected to database.")
        
        # Add columns if they don't exist
        columns = [
            ("phone", "VARCHAR(50)"),
            ("website", "VARCHAR(255)"),
            ("working_hours", "VARCHAR(255)"),
            ("metadata", "JSONB")
        ]
        
        for col, dtype in columns:
            try:
                print(f"🛠️ Adding column: {col}...")
                cur.execute(f"ALTER TABLE items ADD COLUMN {col} {dtype}")
                conn.commit()
                print(f"   ✅ Added {col}")
            except psycopg2.errors.DuplicateColumn:
                print(f"   ⚠️ Column {col} already exists. Skipping.")
                conn.rollback()
            except Exception as e:
                print(f"   ❌ Error adding {col}: {e}")
                conn.rollback()

        # Update image_url to allow NULL or default? It's typically varchar.
        # We will handle default image in code/import.

        print("✅ Migration complete.")
        cur.close()
        conn.close()
        
    except Exception as e:
        print(f"❌ Migration failed: {e}")

if __name__ == "__main__":
    migrate()
