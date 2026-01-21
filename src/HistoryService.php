<?php

namespace App\Services;

/**
 * History & Favorites Service
 * Manages user visit history and bookmarks/favorites
 */
class HistoryService
{
    private $db;
    
    public function __construct($db)
    {
        $this->db = $db;
    }
    
    /**
     * Record a visit to a place
     */
    public function recordVisit(int $userId, int $itemId): void
    {
        $sql = "INSERT INTO visit_history (user_id, item_id, viewed_at) 
                VALUES (?, ?, CURRENT_TIMESTAMP) 
                ON CONFLICT (user_id, item_id) DO UPDATE 
                SET viewed_at = CURRENT_TIMESTAMP";
        
        $this->db->query($sql, [$userId, $itemId]);
    }
    
    /**
     * Get recently viewed places for a user
     */
    public function getRecentlyViewed(int $userId, int $limit = 6): array
    {
        $sql = "SELECT i.*, v.viewed_at, c.name as category_name 
                FROM visit_history v 
                JOIN items i ON v.item_id = i.id 
                LEFT JOIN categories c ON i.category_id = c.id 
                WHERE v.user_id = ? 
                ORDER BY v.viewed_at DESC 
                LIMIT ?";
        
        $stmt = $this->db->query($sql, [$userId, $limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get all favorite places for a user
     */
    public function getFavorites(int $userId): array
    {
        $sql = "SELECT i.*, c.name as category_name 
                FROM favorites f 
                JOIN items i ON f.item_id = i.id 
                LEFT JOIN categories c ON i.category_id = c.id 
                WHERE f.user_id = ? 
                ORDER BY f.created_at DESC";
        
        $stmt = $this->db->query($sql, [$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Toggle favorite status
     */
    public function toggleFavorite(int $userId, int $itemId): string
    {
        $stmt = $this->db->query("SELECT id FROM favorites WHERE user_id = ? AND item_id = ?", [$userId, $itemId]);
        $exists = $stmt->fetch();
        
        if ($exists) {
            $this->db->query("DELETE FROM favorites WHERE user_id = ? AND item_id = ?", [$userId, $itemId]);
            return 'removed';
        } else {
            $this->db->query("INSERT INTO favorites (user_id, item_id) VALUES (?, ?)", [$userId, $itemId]);
            return 'added';
        }
    }
    
    /**
     * Get statistics for user dashboard
     */
    public function getUserStats(int $userId): array
    {
        $stats = [];
        
        // Favorite count
        $stmt = $this->db->query("SELECT COUNT(*) FROM favorites WHERE user_id = ?", [$userId]);
        $stats['favorites_count'] = (int)$stmt->fetchColumn();
        
        // Reviews count
        $stmt = $this->db->query("SELECT COUNT(*) FROM reviews WHERE user_id = ?", [$userId]);
        $stats['reviews_count'] = (int)$stmt->fetchColumn();
        
        // Visit count
        $stmt = $this->db->query("SELECT COUNT(*) FROM visit_history WHERE user_id = ?", [$userId]);
        $stats['visits_count'] = (int)$stmt->fetchColumn();
        
        return $stats;
    }
}
