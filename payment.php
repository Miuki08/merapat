<?php
require_once 'connect.php';

class Payment {
    public $payment_id;
    public $booking_id;
    public $payment_date;
    public $user_file;
    public $payment_method;
    public $amount;
    public $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function paymentread() {
        $query = "SELECT payments.*, bookings.start_time, bookings.end_time, users.name, meeting_rooms.room_name
                  FROM payments
                  INNER JOIN bookings ON payments.booking_id = bookings.booking_id
                  INNER JOIN meeting_rooms ON bookings.room_id = meeting_rooms.room_id
                  INNER JOIN users ON bookings.user_id = users.user_id";
        $result = $this->db->query($query);
        if (!$result) {
            die("Query failed: " . $this->db->conn->error);
        }
        return $result;
    }

    public function getPaymentByUserId($user_id) {
        $query = "SELECT bookings.booking_id, users.user_id, users.name, meeting_rooms.room_id, meeting_rooms.room_name, bookings.start_time, bookings.end_time, bookings.status
                  FROM bookings
                  INNER JOIN users ON users.user_id = bookings.user_id
                  INNER JOIN meeting_rooms ON meeting_rooms.room_id = bookings.room_id
                  WHERE bookings.user_id = ?";
        $stmt = $this->db->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function search($keyword) {
        $query = "SELECT payments.payment_id,payments.payment_date,payments.payment_method,bookings.start_time,bookings.end_time,users.name,meeting_rooms.room_name
                  FROM payments
                  INNER JOIN bookings ON payments.booking_id = bookings.booking_id
                  INNER JOIN meeting_rooms ON bookings.room_id = meeting_rooms.room_id
                  INNER JOIN users ON bookings.user_id = users.user_id
                  WHERE payments.payment_id LIKE ?  OR bookings.booking_id LIKE ? OR users.name LIKE ? OR meeting_rooms.room_name LIKE ? OR payments.payment_method LIKE ?";
        $stmt = $this->db->conn->prepare($query);
        $searchKeyword = "%$keyword%";
        $stmt->bind_param("sssss", $searchKeyword, $searchKeyword, $searchKeyword, $searchKeyword, $searchKeyword);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function add() {
        $query = "INSERT INTO payments (booking_id, payment_date, user_file, payment_method, amount) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->conn->prepare($query);
        $stmt->bind_param("isssd", $this->booking_id, $this->payment_date, $this->user_file, $this->payment_method, $this->amount);
        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM payments WHERE payment_id = ?";
        $stmt = $this->db->conn->prepare($query);
        $stmt->bind_param("i", $this->payment_id);
        $result = $stmt->execute();
        if (!$result) {
            error_log("Delete failed: " . $this->db->conn->error);
        }
        return $result;
    }

    public function update() {
        $query = "UPDATE payments SET booking_id = ?, payment_date = ?, user_file = ?, payment_method = ? WHERE payment_id = ?";
        $stmt = $this->db->conn->prepare($query);
        $stmt->bind_param("isssi", $this->booking_id, $this->payment_date, $this->user_file, $this->payment_method, $this->payment_id);
        return $stmt->execute();
    }
}
?>