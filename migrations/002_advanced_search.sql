-- Phase 2 Sprint 2.1: Advanced Search & Filters
-- Database migration script

-- Search history table
CREATE TABLE IF NOT EXISTS search_history (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id) ON DELETE CASCADE,
    query VARCHAR(255),
    filters JSONB,
    result_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_search_history_user ON search_history(user_id);
CREATE INDEX IF NOT EXISTS idx_search_query ON search_history(query);

-- Add search-related columns to items
ALTER TABLE items ADD COLUMN IF NOT EXISTS price_range VARCHAR(20);
ALTER TABLE items ADD COLUMN IF NOT EXISTS is_open_now BOOLEAN DEFAULT TRUE;
ALTER TABLE items ADD COLUMN IF NOT EXISTS rating_average DECIMAL(3,2) DEFAULT 0;
ALTER TABLE items ADD COLUMN IF NOT EXISTS review_count INT DEFAULT 0;

-- Add full-text search index
CREATE INDEX IF NOT EXISTS idx_items_search ON items USING GIN(
    to_tsvector('english', COALESCE(title, '') || ' ' || COALESCE(description, '') || ' ' || COALESCE(address, ''))
);

-- Add rating index for filtering
CREATE INDEX IF NOT EXISTS idx_items_rating ON items(rating_average);

-- Add location index for distance queries
CREATE INDEX IF NOT EXISTS idx_items_location ON items(latitude, longitude);

COMMENT ON TABLE search_history IS 'Stores user search history for analytics and suggestions';
COMMENT ON COLUMN items.price_range IS 'Price range: budget, moderate, expensive, luxury';
COMMENT ON COLUMN items.is_open_now IS 'Whether the place is currently open';
