<?php

namespace App\Services;

class GamificationService
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Award points to a user
     */
    public function awardPoints(int $userId, string $action): int
    {
        $pointsMap = [
            'review_submitted' => 50,
            'post_created' => 20,
            'comment_added' => 10,
            'ticket_booked' => 100,
            'group_joined' => 15,
        ];

        $points = $pointsMap[$action] ?? 0;
        if ($points === 0) return 0;

        $sql = "UPDATE users SET points = points + ? WHERE id = ? RETURNING points";
        $stmt = $this->db->query($sql, [$points, $userId]);
        $newPoints = (int)$stmt->fetchColumn();

        $this->updateLevel($userId, $newPoints);
        $this->checkAchievements($userId, $newPoints);

        return $points;
    }

    /**
     * Update user level based on points
     */
    private function updateLevel(int $userId, int $points): void
    {
        $level = 1;
        if ($points >= 2000) $level = 5;
        elseif ($points >= 1000) $level = 4;
        elseif ($points >= 500) $level = 3;
        elseif ($points >= 200) $level = 2;

        $this->db->query("UPDATE users SET level = ? WHERE id = ? AND level < ?", [$level, $userId, $level]);
    }

    /**
     * Check and award achievements
     */
    private function checkAchievements(int $userId, int $points): void
    {
        // 1. Point-based achievements
        $sql = "INSERT INTO user_achievements (user_id, achievement_id)
                SELECT ?, id FROM achievements
                WHERE points_required <= ?
                ON CONFLICT (user_id, achievement_id) DO NOTHING";
        $this->db->query($sql, [$userId, $points]);

        // 2. Action-based achievements (Review count, etc.)
        $reviewCount = (int)$this->db->query("SELECT COUNT(*) FROM reviews WHERE user_id = ?", [$userId])->fetchColumn();
        if ($reviewCount >= 10) {
            $this->awardAchievementByName($userId, 'Top Reviewer');
        } elseif ($reviewCount >= 1) {
            $this->awardAchievementByName($userId, 'First Review');
        }
    }

    private function awardAchievementByName(int $userId, string $name): void
    {
        $sql = "INSERT INTO user_achievements (user_id, achievement_id)
                SELECT ?, id FROM achievements WHERE name = ?
                ON CONFLICT DO NOTHING";
        $this->db->query($sql, [$userId, $name]);
    }

    /**
     * Get user achievements
     */
    public function getUserAchievements(int $userId): array
    {
        $sql = "SELECT a.*, ua.earned_at 
                FROM achievements a
                JOIN user_achievements ua ON a.id = ua.achievement_id
                WHERE ua.user_id = ?
                ORDER BY ua.earned_at DESC";
        return $this->db->query($sql, [$userId])->fetchAll();
    }

    /**
     * Get points leaderboard
     */
    public function getLeaderboard(int $limit = 10): array
    {
        $limit_int = (int)$limit;
        $sql = "SELECT id, username, avatar_url, points, level 
                FROM users 
                ORDER BY points DESC, username ASC 
                LIMIT $limit_int";
        return $this->db->query($sql)->fetchAll();
    }
}
