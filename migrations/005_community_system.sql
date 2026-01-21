-- Phase 3: Community & Engagement
-- Database Migration 005_community_system.sql

-- Groups/Communities
CREATE TABLE IF NOT EXISTS community_groups (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    category VARCHAR(100),
    image_url TEXT,
    member_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Group Memberships
CREATE TABLE IF NOT EXISTS community_members (
    group_id INT REFERENCES community_groups(id) ON DELETE CASCADE,
    user_id INT REFERENCES users(id) ON DELETE CASCADE,
    role VARCHAR(20) DEFAULT 'member', -- member, admin
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (group_id, user_id)
);

-- Community Posts
CREATE TABLE IF NOT EXISTS community_posts (
    id SERIAL PRIMARY KEY,
    group_id INT REFERENCES community_groups(id) ON DELETE CASCADE,
    user_id INT REFERENCES users(id) ON DELETE CASCADE,
    title VARCHAR(255),
    content TEXT NOT NULL,
    image_url TEXT,
    likes_count INT DEFAULT 0,
    comments_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Post Comments
CREATE TABLE IF NOT EXISTS community_comments (
    id SERIAL PRIMARY KEY,
    post_id INT REFERENCES community_posts(id) ON DELETE CASCADE,
    user_id INT REFERENCES users(id) ON DELETE CASCADE,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed some initial groups
INSERT INTO community_groups (name, slug, description, category, image_url) VALUES 
('Yerevan Foodies', 'yerevan-foodies', 'Discuss the best restaurants, hidden cafes, and local delicacies in Yerevan.', 'Food & Drink', 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=800&q=80'),
('Nightlife Seekers', 'nightlife-seekers', 'Everything about pubs, clubs, and late-night vibes in the city.', 'Nightlife', 'https://images.unsplash.com/photo-1514525253361-bee8718a74a2?auto=format&fit=crop&w=800&q=80'),
('Digital Nomads Yerevan', 'nomads-yerevan', 'A place for remote workers to share tips about co-working spaces and living in Armenia.', 'Lifestyle', 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=800&q=80'),
('Photography Yerevan', 'yerevan-photo', 'Share your stunning photos of the city and find the best photo spots.', 'Art & Culture', 'https://images.unsplash.com/photo-1452723312111-3a7d0db0e024?auto=format&fit=crop&w=800&q=80')
ON CONFLICT (slug) DO NOTHING;

-- Indexes
CREATE INDEX IF NOT EXISTS idx_posts_group ON community_posts(group_id);
CREATE INDEX IF NOT EXISTS idx_comments_post ON community_comments(post_id);
