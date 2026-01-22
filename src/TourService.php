<?php

namespace App\Services;

class TourService {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllTours($activeOnly = true) {
        $sql = "SELECT * FROM tours";
        if ($activeOnly) {
            $sql .= " WHERE is_active = TRUE";
        }
        $sql .= " ORDER BY created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getTourBySlug($slug) {
        $stmt = $this->db->query("SELECT * FROM tours WHERE slug = ?", [$slug]);
        return $stmt->fetch();
    }

    public function getTourById($id) {
        $stmt = $this->db->query("SELECT * FROM tours WHERE id = ?", [$id]);
        return $stmt->fetch();
    }

    public function createTour($data) {
        $sql = "INSERT INTO tours (title, slug, description, short_description, price, duration, image_url, inclusions, exclusions, is_active) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        return $this->db->query($sql, [
            $data['title'],
            $data['slug'],
            $data['description'],
            $data['short_description'],
            $data['price'],
            $data['duration'],
            $data['image_url'],
            json_encode($data['inclusions'] ?? []),
            json_encode($data['exclusions'] ?? []),
            $data['is_active'] ?? true
        ]);
    }

    public function updateTour($id, $data) {
        $sql = "UPDATE tours SET title = ?, slug = ?, description = ?, short_description = ?, price = ?, duration = ?, image_url = ?, inclusions = ?, exclusions = ?, is_active = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        return $this->db->query($sql, [
            $data['title'],
            $data['slug'],
            $data['description'],
            $data['short_description'],
            $data['price'],
            $data['duration'],
            $data['image_url'],
            json_encode($data['inclusions'] ?? []),
            json_encode($data['exclusions'] ?? []),
            $data['is_active'],
            $id
        ]);
    }

    public function deleteTour($id) {
        return $this->db->query("DELETE FROM tours WHERE id = ?", [$id]);
    }
}
