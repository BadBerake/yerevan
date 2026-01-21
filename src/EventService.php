<?php

namespace App\Services;

class EventService
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Get upcoming events
     */
    public function getUpcomingEvents(int $limit = 10): array
    {
        $sql = "SELECT i.*, c.name as category_name 
                FROM items i 
                JOIN categories c ON i.category_id = c.id 
                WHERE c.slug = 'events' 
                AND i.is_approved = TRUE 
                AND i.event_date >= CURRENT_TIMESTAMP
                ORDER BY i.event_date ASC 
                LIMIT ?";
        return $this->db->query($sql, [$limit])->fetchAll();
    }

    /**
     * Create a booking
     */
    public function bookTicket(int $userId, int $eventId, int $count): string
    {
        $event = $this->db->query("SELECT ticket_price FROM items WHERE id = ?", [$eventId])->fetch();
        if (!$event) return "";

        $price = (float)($event['ticket_price'] ?? 0);
        $totalPrice = $price * $count;
        $bookingCode = 'YVG-' . strtoupper(substr(md5(uniqid()), 0, 8));

        $sql = "INSERT INTO event_bookings (user_id, event_id, ticket_count, total_price, booking_code) 
                VALUES (?, ?, ?, ?, ?) RETURNING id";
        $this->db->query($sql, [
            $userId,
            $eventId,
            $count,
            $totalPrice,
            $bookingCode
        ]);

        return $bookingCode;
    }

    /**
     * Get user bookings
     */
    public function getUserBookings(int $userId): array
    {
        $sql = "SELECT b.*, i.title as event_title, i.event_date, i.address, i.image_url
                FROM event_bookings b
                JOIN items i ON b.event_id = i.id
                WHERE b.user_id = ?
                ORDER BY b.created_at DESC";
        return $this->db->query($sql, [$userId])->fetchAll();
    }

    /**
     * Get booking details
     */
    public function getBookingByCode(string $code): ?array
    {
        $sql = "SELECT b.*, i.title as event_title, i.event_date, i.address, u.username
                FROM event_bookings b
                JOIN items i ON b.event_id = i.id
                JOIN users u ON b.user_id = u.id
                WHERE b.booking_code = ?";
        $stmt = $this->db->query($sql, [$code]);
        return $stmt->fetch() ?: null;
    }
}
