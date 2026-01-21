<?php

namespace App\Services;

/**
 * Search Service - Advanced search with filters
 */
class SearchService
{
    private $db;
    
    public function __construct($db)
    {
        $this->db = $db;
    }
    
    /**
     * Perform advanced search with filters
     */
    public function search(string $query = '', array $filters = []): array
    {
        $params = [];
        $conditions = ["items.is_approved = TRUE"];
        
        // Base query with category join
        $sql = "SELECT items.*, categories.name as category_name, categories.slug as category_slug";
        
        // Add distance if user location provided
        if (!empty($filters['lat']) && !empty($filters['lng'])) {
            $sql .= ", (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance";
            $params[] = $filters['lat'];
            $params[] = $filters['lng'];
            $params[] = $filters['lat'];
        }
        
        $sql .= " FROM items LEFT JOIN categories ON items.category_id = categories.id";
        
        // Text search
        if (!empty($query)) {
            $conditions[] = "(
                items.title ILIKE ? OR 
                items.description ILIKE ? OR 
                items.address ILIKE ? OR
                to_tsvector('english', COALESCE(items.title, '') || ' ' || COALESCE(items.description, '')) @@ plainto_tsquery('english', ?)
            )";
            $searchTerm = '%' . $query . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $query;
        }
        
        // Category filter
        if (!empty($filters['category'])) {
            if (is_array($filters['category'])) {
                $placeholders = implode(',', array_fill(0, count($filters['category']), '?'));
                $conditions[] = "categories.slug IN ($placeholders)";
                $params = array_merge($params, $filters['category']);
            } else {
                $conditions[] = "categories.slug = ?";
                $params[] = $filters['category'];
            }
        }
        
        // Price range filter
        if (!empty($filters['price_range'])) {
            if (is_array($filters['price_range'])) {
                $placeholders = implode(',', array_fill(0, count($filters['price_range']), '?'));
                $conditions[] = "items.price_range IN ($placeholders)";
                $params = array_merge($params, $filters['price_range']);
            } else {
                $conditions[] = "items.price_range = ?";
                $params[] = $filters['price_range'];
            }
        }
        
        // Rating filter (minimum rating)
        if (!empty($filters['min_rating'])) {
            $conditions[] = "items.rating_average >= ?";
            $params[] = floatval($filters['min_rating']);
        }
        
        // Open now filter
        if (isset($filters['open_now']) && $filters['open_now']) {
            $conditions[] = "items.is_open_now = TRUE";
        }
        
        // Has reviews filter
        if (isset($filters['has_reviews']) && $filters['has_reviews']) {
            $conditions[] = "items.review_count > 0";
        }
        
        // Amenities filter
        if (!empty($filters['amenities'])) {
            $amenities = is_array($filters['amenities']) ? $filters['amenities'] : [$filters['amenities']];
            foreach ($amenities as $amenity) {
                $conditions[] = "items.amenities::jsonb ?? ?";
                $params[] = $amenity;
            }
        }
        
        // Build WHERE clause
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        
        // Distance filter (after WHERE, as it uses calculated distance)
        if (!empty($filters['max_distance']) && !empty($filters['lat']) && !empty($filters['lng'])) {
            $sql .= " HAVING distance <= ?";
            $params[] = floatval($filters['max_distance']);
        }
        
        // Sort order
        $sortBy = $filters['sort'] ?? 'relevance';
        switch ($sortBy) {
            case 'rating':
                $sql .= " ORDER BY items.rating_average DESC, items.review_count DESC";
                break;
            case 'distance':
                if (!empty($filters['lat']) && !empty($filters['lng'])) {
                    $sql .= " ORDER BY distance ASC";
                } else {
                    $sql .= " ORDER BY items.created_at DESC";
                }
                break;
            case 'newest':
                $sql .= " ORDER BY items.created_at DESC";
                break;
            case 'name':
                $sql .= " ORDER BY items.title ASC";
                break;
            case 'relevance':
            default:
                if (!empty($query)) {
                    $sql .= " ORDER BY ts_rank(to_tsvector('english', COALESCE(items.title, '') || ' ' || COALESCE(items.description, '')), plainto_tsquery('english', ?)) DESC";
                    $params[] = $query;
                } else {
                    $sql .= " ORDER BY items.rating_average DESC, items.created_at DESC";
                }
                break;
        }
        
        // Pagination
        $page = $filters['page'] ?? 1;
        $perPage = $filters['per_page'] ?? 20;
        $offset = ($page - 1) * $perPage;
        
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        // Execute query
        $stmt = $this->db->query($sql, $params);
        $results = $stmt->fetchAll();
        
        return $results;
    }
    
    /**
     * Get search count (for pagination)
     */
    public function getSearchCount(string $query = '', array $filters = []): int
    {
        $params = [];
        $conditions = ["items.is_approved = TRUE"];
        
        $sql = "SELECT COUNT(*) FROM items LEFT JOIN categories ON items.category_id = categories.id";
        
        // Apply same filters as search (simplified, without distance)
        if (!empty($query)) {
            $conditions[] = "(items.title ILIKE ? OR items.description ILIKE ? OR items.address ILIKE ?)";
            $searchTerm = '%' . $query . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($filters['category'])) {
            if (is_array($filters['category'])) {
                $placeholders = implode(',', array_fill(0, count($filters['category']), '?'));
                $conditions[] = "categories.slug IN ($placeholders)";
                $params = array_merge($params, $filters['category']);
            } else {
                $conditions[] = "categories.slug = ?";
                $params[] = $filters['category'];
            }
        }
        
        if (!empty($filters['price_range'])) {
            if (is_array($filters['price_range'])) {
                $placeholders = implode(',', array_fill(0, count($filters['price_range']), '?'));
                $conditions[] = "items.price_range IN ($placeholders)";
                $params = array_merge($params, $filters['price_range']);
            } else {
                $conditions[] = "items.price_range = ?";
                $params[] = $filters['price_range'];
            }
        }
        
        if (!empty($filters['min_rating'])) {
            $conditions[] = "items.rating_average >= ?";
            $params[] = floatval($filters['min_rating']);
        }
        
        if (isset($filters['open_now']) && $filters['open_now']) {
            $conditions[] = "items.is_open_now = TRUE";
        }
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchColumn();
    }
    
    /**
     * Get search suggestions (autocomplete)
     */
    public function getSuggestions(string $partial, int $limit = 10): array
    {
        $searchTerm = $partial . '%';
        
        $sql = "SELECT DISTINCT title, slug, id 
                FROM items 
                WHERE is_approved = TRUE AND (
                    title ILIKE ? OR 
                    address ILIKE ?
                )
                LIMIT ?";
        
        $stmt = $this->db->query($sql, [$searchTerm, $searchTerm, $limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Save search to history
     */
    public function saveSearchHistory(?int $userId, string $query, array $filters, int $resultCount): void
    {
        if (!$userId) {
            return; // Don't save for anonymous users
        }
        
        $sql = "INSERT INTO search_history (user_id, query, filters, result_count) 
                VALUES (?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $userId,
            $query,
            json_encode($filters),
            $resultCount
        ]);
    }
    
    /**
     * Get user's recent searches
     */
    public function getRecentSearches(int $userId, int $limit = 10): array
    {
        $sql = "SELECT DISTINCT query, filters, created_at 
                FROM search_history 
                WHERE user_id = ? AND query != '' 
                ORDER BY created_at DESC 
                LIMIT ?";
        
        $stmt = $this->db->query($sql, [$userId, $limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get popular searches (global)
     */
    public function getPopularSearches(int $limit = 10): array
    {
        $sql = "SELECT query, COUNT(*) as search_count 
                FROM search_history 
                WHERE query != '' AND created_at > NOW() - INTERVAL '30 days'
                GROUP BY query 
                ORDER BY search_count DESC 
                LIMIT ?";
        
        $stmt = $this->db->query($sql, [$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get trending places (most searched)
     */
    public function getTrendingPlaces(int $limit = 10): array
    {
        $sql = "SELECT items.*, COUNT(sh.id) as search_mentions
                FROM items
                LEFT JOIN search_history sh ON (sh.query ILIKE '%' || items.title || '%')
                WHERE items.is_approved = TRUE
                  AND sh.created_at > NOW() - INTERVAL '7 days'
                GROUP BY items.id
                ORDER BY search_mentions DESC, items.rating_average DESC
                LIMIT ?";
        
        $stmt = $this->db->query($sql, [$limit]);
        return $stmt->fetchAll();
    }
}
