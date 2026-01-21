<?php

namespace App\Services;

/**
 * Review Service - Manages user reviews, ratings, and review images
 */
class ReviewService
{
    private $db;
    
    public function __construct($db)
    {
        $this->db = $db;
    }
    
    /**
     * Submit a new review
     */
    public function createReview(int $userId, int $itemId, int $rating, string $title, string $content, array $images = []): array
    {
        try {
            // Start transaction
            $pdo = $this->db->getPdo();
            $pdo->beginTransaction();
            
            // 1. Insert review
            $sql = "INSERT INTO reviews (item_id, user_id, rating, title, content) 
                    VALUES (?, ?, ?, ?, ?) 
                    ON CONFLICT (user_id, item_id) DO UPDATE 
                    SET rating = EXCLUDED.rating, 
                        title = EXCLUDED.title, 
                        content = EXCLUDED.content, 
                        updated_at = CURRENT_TIMESTAMP 
                    RETURNING id";
            
            $stmt = $this->db->query($sql, [$itemId, $userId, $rating, $title, $content]);
            $reviewId = $stmt->fetchColumn();
            
            // 2. Handle images
            foreach ($images as $imageUrl) {
                $this->db->query("INSERT INTO review_images (review_id, image_url) VALUES (?, ?)", [$reviewId, $imageUrl]);
            }
            
            // 3. Update item average rating and count
            $this->updateItemRating($itemId);
            
            $pdo->commit();
            
            return ['status' => 'success', 'review_id' => $reviewId];
            
        } catch (\Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Update an item's aggregate rating and review count
     */
    public function updateItemRating(int $itemId): void
    {
        $sql = "UPDATE items 
                SET rating_average = (SELECT ROUND(AVG(rating)::numeric, 1) FROM reviews WHERE item_id = ?),
                    review_count = (SELECT COUNT(*) FROM reviews WHERE item_id = ?)
                WHERE id = ?";
        
        $this->db->query($sql, [$itemId, $itemId, $itemId]);
    }
    
    /**
     * Get reviews for an item
     */
    public function getReviewsByItem(int $itemId, string $sort = 'newest', int $limit = 10, int $offset = 0): array
    {
        $orderBy = match($sort) {
            'helpful' => 'helpful_count DESC, created_at DESC',
            'highest' => 'rating DESC, created_at DESC',
            'lowest' => 'rating ASC, created_at DESC',
            default => 'created_at DESC'
        };
        
        $sql = "SELECT r.*, u.username, u.avatar_url 
                FROM reviews r 
                JOIN users u ON r.user_id = u.id 
                WHERE r.item_id = ? 
                ORDER BY $orderBy 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->query($sql, [$itemId, $limit, $offset]);
        $reviews = $stmt->fetchAll();
        
        // Add images to each review
        foreach ($reviews as &$review) {
            $review['images'] = $this->getReviewImages($review['id']);
        }
        
        return $reviews;
    }
    
    /**
     * Get images for a specific review
     */
    private function getReviewImages(int $reviewId): array
    {
        $stmt = $this->db->query("SELECT image_url, caption FROM review_images WHERE review_id = ?", [$reviewId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Vote a review as helpful or not helpful
     */
    public function voteReview(int $userId, int $reviewId, string $voteType): array
    {
        try {
            $sql = "INSERT INTO review_votes (review_id, user_id, vote_type) 
                    VALUES (?, ?, ?) 
                    ON CONFLICT (review_id, user_id) DO UPDATE 
                    SET vote_type = EXCLUDED.vote_type";
            
            $this->db->query($sql, [$reviewId, $userId, $voteType]);
            
            // Update helpful_count in reviews table
            $countSql = "UPDATE reviews 
                         SET helpful_count = (SELECT COUNT(*) FROM review_votes WHERE review_id = ? AND vote_type = 'helpful')
                         WHERE id = ?";
            $this->db->query($countSql, [$reviewId, $reviewId]);
            
            return ['status' => 'success'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Get rating distribution (e.g., how many 5 stars, 4 stars, etc.)
     */
    public function getRatingDistribution(int $itemId): array
    {
        $sql = "SELECT rating, COUNT(*) as count 
                FROM reviews 
                WHERE item_id = ? 
                GROUP BY rating 
                ORDER BY rating DESC";
        
        $stmt = $this->db->query($sql, [$itemId]);
        $dist = $stmt->fetchAll();
        
        $result = array_fill(1, 5, 0);
        foreach ($dist as $row) {
            $result[$row['rating']] = (int)$row['count'];
        }
        
        return $result;
    }
    
    /**
     * Check if a user has already reviewed an item
     */
    public function hasUserReviewed(int $userId, int $itemId): bool
    {
        $stmt = $this->db->query("SELECT id FROM reviews WHERE user_id = ? AND item_id = ?", [$userId, $itemId]);
        return (bool)$stmt->fetch();
    }
}
