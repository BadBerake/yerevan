<?php

namespace App\Services;

class AdminService
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getDashboardStats()
    {
        $stats = [];
        
        // Total Users
        $stats['total_users'] = $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        
        // Total Reviews
        $stats['total_reviews'] = $this->db->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
        
        // Total Events
        $stats['total_events'] = $this->db->query("SELECT COUNT(*) FROM items i JOIN categories c ON i.category_id = c.id WHERE c.slug = 'events'")->fetchColumn();
        
        // Total Page Views (All time)
        try {
            $stats['total_views'] = $this->db->query("SELECT COUNT(*) FROM page_views")->fetchColumn();
        } catch (\Exception $e) {
             $stats['total_views'] = 0;
        }

        // New Users (Last 24h)
        $stats['new_users_24h'] = $this->db->query("SELECT COUNT(*) FROM users WHERE created_at > NOW() - INTERVAL '24 HOURS'")->fetchColumn();

        return $stats;
    }

    public function getAllUsers($limit = 50, $offset = 0)
    {
        $sql = "SELECT id, username, email, role, created_at, points, level, is_verified 
                FROM users 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?";
        return $this->db->query($sql, [$limit, $offset])->fetchAll();
    }

    public function verifyUser($userId)
    {
        return $this->db->query("UPDATE users SET is_verified = 1 WHERE id = ?", [$userId]);
    }

    public function banUser($userId)
    {
        // Assuming we have a status column, or we can use another method. 
        // For now, let's assume specific role 'banned' or status 'banned'
        return $this->db->query("UPDATE users SET status = 'banned' WHERE id = ?", [$userId]);
    }

    public function getTopPages($limit = 10)
    {
        try {
            $sql = "SELECT page_url, COUNT(*) as views 
                    FROM page_views 
                    GROUP BY page_url 
                    ORDER BY views DESC 
                    LIMIT ?";
            return $this->db->query($sql, [$limit])->fetchAll();
        } catch (\Exception $e) {
            return [];
        }
    }
    
    public function getRecentActivity($limit = 10)
    {
        // Combine recent users and reviews for a feed
        // This is a simplified example
        $users = $this->db->query("SELECT id, username as name, 'joined' as type, created_at FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();
        $reviews = $this->db->query("SELECT r.id, u.username as name, 'reviewed' as type, r.created_at FROM reviews r JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC LIMIT 5")->fetchAll();
        
        $activity = array_merge($users, $reviews);
        usort($activity, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return array_slice($activity, 0, $limit);
    }
}
