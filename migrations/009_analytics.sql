-- Phase 3: Analytics Integration
-- Migration 009_analytics.sql

-- Analytics Logs for page views and actions
CREATE TABLE IF NOT EXISTS analytics_logs (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id) ON DELETE SET NULL,
    session_id VARCHAR(100),
    page_url TEXT NOT NULL,
    action VARCHAR(50) DEFAULT 'view', -- view, click, search
    metadata JSONB, -- Additional data (search query, item_id, etc.)
    user_agent TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Aggregated Place popularity stats for performance
CREATE TABLE IF NOT EXISTS place_stats (
    item_id INT PRIMARY KEY REFERENCES items(id) ON DELETE CASCADE,
    view_count INT DEFAULT 0,
    search_count INT DEFAULT 0,
    last_viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_analytics_page ON analytics_logs(page_url);
CREATE INDEX IF NOT EXISTS idx_analytics_user ON analytics_logs(user_id);
CREATE INDEX IF NOT EXISTS idx_analytics_action ON analytics_logs(action);
