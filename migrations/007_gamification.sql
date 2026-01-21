-- Phase 3: Gamification
-- Database Migration 007_gamification.sql

-- Add points and level to users table if they don't exist
ALTER TABLE users ADD COLUMN IF NOT EXISTS points INT DEFAULT 0;
ALTER TABLE users ADD COLUMN IF NOT EXISTS level INT DEFAULT 1;

-- Achievements definition
CREATE TABLE IF NOT EXISTS achievements (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(100), -- FontAwesome icon class
    points_required INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- User earned achievements
CREATE TABLE IF NOT EXISTS user_achievements (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id) ON DELETE CASCADE,
    achievement_id INT REFERENCES achievements(id) ON DELETE CASCADE,
    earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(user_id, achievement_id)
);

-- Seed initial achievements
INSERT INTO achievements (name, description, icon, points_required) VALUES
('First Review', 'Wrote your first review!', 'fas fa-star', 50),
('Top Reviewer', 'Wrote 10 or more reviews.', 'fas fa-award', 500),
('Community Member', 'Joined your first community group.', 'fas fa-users', 10),
('Event Spree', 'Booked a ticket for an event.', 'fas fa-ticket-alt', 100),
('Local Guide', 'Contributed significantly to the community.', 'fas fa-map-marked-alt', 1000)
ON CONFLICT DO NOTHING;
