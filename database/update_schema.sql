-- Tour Routes Table
CREATE TABLE IF NOT EXISTS tour_routes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    interest_tag VARCHAR(50), 
    estimated_time VARCHAR(50), 
    difficulty VARCHAR(20) DEFAULT 'medium',
    image_url TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    name_translations TEXT DEFAULT '{}',
    description_translations TEXT DEFAULT '{}'
);

-- Route Stops Table
CREATE TABLE IF NOT EXISTS route_stops (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    route_id INTEGER REFERENCES tour_routes(id) ON DELETE CASCADE,
    item_id INTEGER REFERENCES items(id) ON DELETE CASCADE,
    order_index INTEGER NOT NULL,
    stop_note TEXT,
    note_translations TEXT DEFAULT '{}',
    UNIQUE(route_id, order_index)
);

-- Add translation columns to items table if they don't exist
-- SQLite doesn't support IF NOT EXISTS in ADD COLUMN, so we'll just try to add them. 
-- If they fail, it usually means they exist or there's another issue, but typically safe in this constrained env.
-- To be safe, we'll ignore errors by running them one by one in shell or accepting failure.
-- However, since I control the DB, I know they aren't there yet from my previous schema.sqlite.sql
ALTER TABLE items ADD COLUMN title_translations TEXT DEFAULT '{}';
ALTER TABLE items ADD COLUMN description_translations TEXT DEFAULT '{}';

-- Add other missing columns to items table seen in item-form.php code
ALTER TABLE items ADD COLUMN latitude DECIMAL(10, 8);
ALTER TABLE items ADD COLUMN longitude DECIMAL(11, 8);
ALTER TABLE items ADD COLUMN phone VARCHAR(50);
ALTER TABLE items ADD COLUMN instagram VARCHAR(100);
ALTER TABLE items ADD COLUMN whatsapp VARCHAR(50);
ALTER TABLE items ADD COLUMN opening_hours TEXT;
ALTER TABLE items ADD COLUMN amenities TEXT;

-- Item Images (Gallery) - Found in code but not in original schema
CREATE TABLE IF NOT EXISTS item_images (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    item_id INTEGER REFERENCES items(id) ON DELETE CASCADE,
    image_url VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Reviews table - Found in code
CREATE TABLE IF NOT EXISTS reviews (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER REFERENCES users(id),
    item_id INTEGER REFERENCES items(id),
    rating INTEGER CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
