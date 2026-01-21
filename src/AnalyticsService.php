<?php

namespace App\Services;

class AnalyticsService
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Log a page view or custom action
     */
    public function logEvent(?int $userId, string $pageUrl, string $action = 'view', array $metadata = []): void
    {
        $sql = "INSERT INTO analytics_logs (user_id, session_id, page_url, action, metadata, user_agent, ip_address)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $userId,
            session_id(),
            $pageUrl,
            $action,
            json_encode($metadata),
            $_SERVER['HTTP_USER_AGENT'] ?? null,
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);

        // If it's a place view, update popularity stats
        if ($action === 'view' && isset($metadata['item_id'])) {
            $this->updatePlaceStats((int)$metadata['item_id']);
        }
    }

    /**
     * Update view count for a specific place
     */
    private function updatePlaceStats(int $itemId): void
    {
        $sql = "INSERT INTO place_stats (item_id, view_count, last_viewed_at)
                VALUES (?, 1, CURRENT_TIMESTAMP)
                ON CONFLICT (item_id) DO UPDATE 
                SET view_count = place_stats.view_count + 1, 
                    last_viewed_at = EXCLUDED.last_viewed_at";
        $this->db->query($sql, [$itemId]);
    }

    /**
     * Get popular places based on views
     */
    public function getPopularPlaces(int $limit = 5): array
    {
        $sql = "SELECT i.*, ps.view_count 
                FROM items i 
                JOIN place_stats ps ON i.id = ps.item_id 
                WHERE i.is_approved = TRUE 
                ORDER BY ps.view_count DESC 
                LIMIT ?";
        return $this->db->query($sql, [$limit])->fetchAll();
    }

    /**
     * Log a search query for analytics
     */
    public function logSearch(string $query, int $resultCount): void
    {
        $this->logEvent(null, '/search', 'search', [
            'query' => $query,
            'results' => $resultCount
        ]);
    }
}
