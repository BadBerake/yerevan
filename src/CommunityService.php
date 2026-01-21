<?php

namespace App\Services;

class CommunityService
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Get all community groups
     */
    public function getGroups(): array
    {
        $sql = "SELECT * FROM community_groups ORDER BY member_count DESC, name ASC";
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Get a single group by slug
     */
    public function getGroupBySlug(string $slug): ?array
    {
        $sql = "SELECT * FROM community_groups WHERE slug = ?";
        $stmt = $this->db->query($sql, [$slug]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Get posts for a specific group
     */
    public function getGroupPosts(int $groupId): array
    {
        $sql = "SELECT p.*, u.username, u.avatar_url, u.is_verified 
                FROM community_posts p
                JOIN users u ON p.user_id = u.id
                WHERE p.group_id = ?
                ORDER BY p.created_at DESC";
        return $this->db->query($sql, [$groupId])->fetchAll();
    }

    /**
     * Create a new post in a group
     */
    public function createPost(int $groupId, int $userId, array $data): int
    {
        $sql = "INSERT INTO community_posts (group_id, user_id, title, content, image_url) 
                VALUES (?, ?, ?, ?, ?) RETURNING id";
        $stmt = $this->db->query($sql, [
            $groupId, 
            $userId, 
            $data['title'] ?? null, 
            $data['content'], 
            $data['image_url'] ?? null
        ]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Join a community group
     */
    public function joinGroup(int $groupId, int $userId): bool
    {
        try {
            $sql = "INSERT INTO community_members (group_id, user_id) VALUES (?, ?)";
            $this->db->query($sql, [$groupId, $userId]);
            
            // Increment member count
            $this->db->query("UPDATE community_groups SET member_count = member_count + 1 WHERE id = ?", [$groupId]);
            return true;
        } catch (\PDOException $e) {
            // Already a member or error
            return false;
        }
    }

    /**
     * Check if user is a member of a group
     */
    public function isMember(int $groupId, int $userId): bool
    {
        $sql = "SELECT 1 FROM community_members WHERE group_id = ? AND user_id = ?";
        $stmt = $this->db->query($sql, [$groupId, $userId]);
        return (bool)$stmt->fetch();
    }

    /**
     * Get comments for a post
     */
    public function getPostComments(int $post_id): array
    {
        $sql = "SELECT c.*, u.username, u.avatar_url 
                FROM community_comments c
                JOIN users u ON c.user_id = u.id
                WHERE c.post_id = ?
                ORDER BY c.created_at ASC";
        return $this->db->query($sql, [$post_id])->fetchAll();
    }

    /**
     * Add a comment to a post
     */
    public function addComment(int $postId, int $userId, string $content): bool
    {
        $sql = "INSERT INTO community_comments (post_id, user_id, content) VALUES (?, ?, ?)";
        $this->db->query($sql, [$postId, $userId, $content]);
        
        // Update comments count
        $this->db->query("UPDATE community_posts SET comments_count = comments_count + 1 WHERE id = ?", [$postId]);
        return true;
    }
}
