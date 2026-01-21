<?php

namespace App\Services;

class BookingService
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createBooking($userId, $itemId, $data)
    {
        // Simple availability check (mock)
        if (!$this->checkAvailability($itemId, $data['date'], $data['time'])) {
            return ['status' => 'error', 'message' => 'Time slot not available'];
        }

        $sql = "INSERT INTO bookings (user_id, item_id, booking_date, booking_time, guest_count, special_requests, status) 
                VALUES (?, ?, ?, ?, ?, ?, 'pending') RETURNING id";
        
        try {
            $stmt = $this->db->query($sql, [
                $userId,
                $itemId,
                $data['date'],
                $data['time'],
                $data['guests'],
                $data['requests'] ?? ''
            ]);
            $bookingId = $stmt->fetchColumn(); // Postgres returns ID
            
            // If mysql, use lastInsertId
            if (!$bookingId) {
                 // Fallback or handle based on DB driver if needed, but assuming PG/compatible based on RETURNING
            }

            return ['status' => 'success', 'booking_id' => $bookingId];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getUserBookings($userId)
    {
        $sql = "SELECT b.*, i.title as place_name, i.image_url, i.address 
                FROM bookings b 
                JOIN items i ON b.item_id = i.id 
                WHERE b.user_id = ? 
                ORDER BY b.booking_date DESC, b.booking_time DESC";
        return $this->db->query($sql, [$userId])->fetchAll();
    }
    
    public function cancelBooking($bookingId, $userId)
    {
        $sql = "UPDATE bookings SET status = 'cancelled' WHERE id = ? AND user_id = ?";
        $this->db->query($sql, [$bookingId, $userId]);
        return ['status' => 'success'];
    }

    private function checkAvailability($itemId, $date, $time)
    {
        // Placeholder logic: assume everything is available for now
        // In verify phase, we might add a constraint
        return true; 
    }
}
