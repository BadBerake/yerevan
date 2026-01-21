-- Phase 3 Fix: Add event columns to items table
-- Database Migration 008_event_schema_fix.sql

ALTER TABLE items ADD COLUMN IF NOT EXISTS event_date TIMESTAMP;
ALTER TABLE items ADD COLUMN IF NOT EXISTS ticket_price DECIMAL(10, 2) DEFAULT 0;

-- Update the test events with price and date if they exist
UPDATE items SET 
    event_date = '2026-05-15 17:00:00', 
    ticket_price = 5000 
WHERE slug = 'wine-days-2026';

UPDATE items SET 
    event_date = '2026-03-20 20:30:00', 
    ticket_price = 3000 
WHERE slug = 'jazz-night-malkhas';
