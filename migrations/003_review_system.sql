-- Phase 2 Sprint 2.2: Review System
-- Database migration script

-- Reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id SERIAL PRIMARY KEY,
    item_id INT REFERENCES items(id) ON DELETE CASCADE,
    user_id INT REFERENCES users(id) ON DELETE CASCADE,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    title VARCHAR(255),
    content TEXT,
    helpful_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(user_id, item_id) -- One review per user per place
);

CREATE INDEX IF NOT EXISTS idx_reviews_item ON reviews(item_id);
CREATE INDEX IF NOT EXISTS idx_reviews_user ON reviews(user_id);
CREATE INDEX IF NOT EXISTS idx_reviews_rating ON reviews(rating);

-- Review images
CREATE TABLE IF NOT EXISTS review_images (
    id SERIAL PRIMARY KEY,
    review_id INT REFERENCES reviews(id) ON DELETE CASCADE,
    image_url VARCHAR(500),
    caption TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_review_images_review ON review_images(review_id);

-- Review helpfulness votes
CREATE TABLE IF NOT EXISTS review_votes (
    id SERIAL PRIMARY KEY,
    review_id INT REFERENCES reviews(id) ON DELETE CASCADE,
    user_id INT REFERENCES users(id) ON DELETE CASCADE,
    vote_type VARCHAR(10) CHECK (vote_type IN ('helpful', 'not_helpful')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(review_id, user_id)
);

COMMENT ON TABLE reviews IS 'User reviews and ratings for places';
COMMENT ON TABLE review_images IS 'Images uploaded by users along with their reviews';
COMMENT ON TABLE review_votes IS 'Helpfulness votes for reviews to rank the most relevant ones';
