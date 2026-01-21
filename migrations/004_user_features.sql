-- Phase 2 Sprint 2.3: User Features
-- Database migration script

-- Visit History table
CREATE TABLE IF NOT EXISTS visit_history (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id) ON DELETE CASCADE,
    item_id INT REFERENCES items(id) ON DELETE CASCADE,
    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(user_id, item_id) -- We update the timestamp if they visit again
);

CREATE INDEX IF NOT EXISTS idx_visit_history_user ON visit_history(user_id);
CREATE INDEX IF NOT EXISTS idx_visit_history_viewed ON visit_history(viewed_at);

COMMENT ON TABLE visit_history IS 'Tracks places visited/viewed by logged-in users for "Recently Viewed" feature';
