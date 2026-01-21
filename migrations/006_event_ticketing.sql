-- Phase 3: Events & Ticketing
-- Database Migration 006_event_ticketing.sql

-- Event Bookings
CREATE TABLE IF NOT EXISTS event_bookings (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id) ON DELETE CASCADE,
    event_id INT REFERENCES items(id) ON DELETE CASCADE,
    ticket_count INT DEFAULT 1,
    total_price DECIMAL(10, 2),
    status VARCHAR(20) DEFAULT 'confirmed', -- confirmed, cancelled
    booking_code VARCHAR(50) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index for performance
CREATE INDEX IF NOT EXISTS idx_bookings_user ON event_bookings(user_id);
CREATE INDEX IF NOT EXISTS idx_bookings_event ON event_bookings(event_id);

-- Add some dummy events if none exist
-- First find the category ID for events
DO $$
DECLARE
    event_cat_id INT;
BEGIN
    SELECT id INTO event_cat_id FROM categories WHERE slug = 'events' LIMIT 1;
    
    IF event_cat_id IS NOT NULL THEN
        -- Add a few test events
        INSERT INTO items (title, slug, description, category_id, event_date, ticket_price, address, image_url, is_approved)
        VALUES 
        ('Yerevan Wine Days 2026', 'wine-days-2026', 'Annual wine festival on Saryan street.', event_cat_id, '2026-05-15 17:00:00', 5000, 'Saryan St, Yerevan', 'https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?auto=format&fit=crop&w=800&q=80', TRUE),
        ('Jazz Night at Malkhas', 'jazz-night-malkhas', 'A soulful evening of jazz with Levon Malkhasyan.', event_cat_id, '2026-03-20 20:30:00', 3000, 'Pushkin St, Yerevan', 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=800&q=80', TRUE)
        ON CONFLICT (slug) DO NOTHING;
    END IF;
END $$;
