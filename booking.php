<?php
require_once 'connect.php';

class booking {
    public $booking_id;
    public $user_id;
    public $room_id;
    public $start_time;
    public $end_time;
    public $status;
    public $total_price;
    public $db;

    public function __construct(){
        $this->db = new Database();
    }

    public function bookingread(){
        $query = "SELECT bookings.booking_id, users.user_id, users.name, meeting_rooms.room_id, meeting_rooms.room_name, bookings.start_time, bookings.end_time, bookings.status
                    FROM users
                    INNER JOIN bookings ON users.user_id = bookings.user_id
                    INNER JOIN meeting_rooms ON meeting_rooms.room_id = bookings.room_id;";
        $result = $this->db->query($query);
        if (!$result) {
            die("Query failed: " . $this->db->conn->error);
        }
        return $result;
    }

    // Method untuk membaca booking berdasarkan user_id
    public function getBookingsByUserId($user_id) {
        $query = "SELECT bookings.booking_id, users.user_id, users.name, meeting_rooms.room_id, meeting_rooms.room_name, bookings.start_time, bookings.end_time, bookings.status
                  FROM bookings
                  INNER JOIN users ON users.user_id = bookings.user_id
                  INNER JOIN meeting_rooms ON meeting_rooms.room_id = bookings.room_id
                  WHERE bookings.user_id = ?";
        $cegah = $this->db->conn->prepare($query);
        $cegah->bind_param("i", $user_id);
        $cegah->execute();
        return $cegah->get_result();
    }

    public function search($keyword) {
        $query = "SELECT bookings.booking_id, users.user_id, users.name, meeting_rooms.room_id, meeting_rooms.room_name, bookings.start_time, bookings.end_time, bookings.status
                  FROM bookings
                  INNER JOIN users ON users.user_id = bookings.user_id
                  INNER JOIN meeting_rooms ON meeting_rooms.room_id = bookings.room_id
                  WHERE bookings.booking_id LIKE ? OR users.user_id LIKE ?  OR meeting_rooms.room_id LIKE ?  OR bookings.start_time LIKE ?  OR bookings.end_time LIKE ? OR bookings.status LIKE ? OR users.name LIKE ? OR meeting_rooms.room_name LIKE ?";
        
        $cegah = $this->db->conn->prepare($query);
        $searchKeyword = "%$keyword%";
        $cegah->bind_param("ssssssss", $searchKeyword, $searchKeyword, $searchKeyword, $searchKeyword, $searchKeyword, $searchKeyword, $searchKeyword, $searchKeyword);
        $cegah->execute();
        return $cegah->get_result();
    }

    public function add(){
        $query = "INSERT INTO bookings (user_id, room_id, start_time, end_time, status, total_price) 
        VALUES (?, ?, ?, ?, ?, ?)";
        $cegah = $this->db->conn->prepare($query);
        $cegah->bind_param("iisssi", $this->user_id, $this->room_id, $this->start_time, $this->end_time, $this->status, $this->total_price);
        return $cegah->execute();
    }

    public function getBookingById($booking_id) {
        $query = "SELECT * FROM bookings WHERE booking_id = ?";
        $stmt = $this->db->conn->prepare($query);
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function delete(){
        $query = "DELETE FROM bookings WHERE booking_id = ?";
        $cegah = $this->db->conn->prepare($query);
        $cegah->bind_param("i",  $this->booking_id);
        $result = $cegah->execute();
        if (!$result) {
            error_log("Delete failed: " . $this->db->conn->error); // Log error
        }
        return $result;
    }

    public function update(){
        $query = "UPDATE bookings SET user_id = ?, room_id = ?, start_time = ?, end_time = ?, status = ?, total_price = ? WHERE booking_id = ?";
        $cegah = $this->db->conn->prepare($query);
        
        if (!$cegah) {
            error_log("Prepare failed: " . $this->db->conn->error);
            return false;
        }  
        $cegah->bind_param("iisssii", $this->user_id, $this->room_id, $this->start_time, $this->end_time, $this->status, $this->total_price, $this->booking_id);
        $result = $cegah->execute();
        
        if (!$result) {
            error_log("Execute failed: " . $cegah->error);
        }        
        return $result;
    }
}
?>

