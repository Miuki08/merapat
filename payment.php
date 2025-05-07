<?php
require_once 'connect.php';

class Payment {
    public $payment_id;
    public $booking_id;
    public $payment_date;
    public $user_file;
    public $payment_method;
    public $amount;
    public $status;
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

    public function isPaid($booking_id) {
        $query = "SELECT payment_id FROM payments WHERE booking_id = ?";
        $persiap = $this->db->conn->prepare($query);
        $persiap->bind_param("i", $booking_id);
        $persiap->execute();
        $result = $persiap->get_result();
        return ($result->num_rows > 0);
    }

    public function getPaymentByUserId($user_id) {
        $query = "SELECT bookings.booking_id, users.user_id, users.name, meeting_rooms.room_id, meeting_rooms.room_name, bookings.start_time, bookings.end_time, bookings.status
                  FROM bookings
                  INNER JOIN users ON users.user_id = bookings.user_id
                  INNER JOIN meeting_rooms ON meeting_rooms.room_id = bookings.room_id
                  WHERE bookings.user_id = ?";
        $persiap = $this->db->conn->prepare($query);
        $persiap->bind_param("i", $user_id);
        $persiap->execute();
        return $persiap->get_result();
    }

    public function search($keyword) {
        $query = "SELECT payments.payment_id,payments.payment_date,payments.payment_method,bookings.start_time,bookings.end_time,users.name,meeting_rooms.room_name
                  FROM payments
                  INNER JOIN bookings ON payments.booking_id = bookings.booking_id
                  INNER JOIN meeting_rooms ON bookings.room_id = meeting_rooms.room_id
                  INNER JOIN users ON bookings.user_id = users.user_id
                  WHERE payments.payment_id LIKE ?  OR bookings.booking_id LIKE ? OR users.name LIKE ? OR meeting_rooms.room_name LIKE ? OR payments.payment_method LIKE ?";
        $persiap = $this->db->conn->prepare($query);
        $searchKeyword = "%$keyword%";
        $persiap->bind_param("sssss", $searchKeyword, $searchKeyword, $searchKeyword, $searchKeyword, $searchKeyword);
        $persiap->execute();
        return $persiap->get_result();
    }
    
    public function getPaymentByBookingId($booking_id) {
        $query = "SELECT * FROM payments WHERE booking_id = ?";
        $persiap = $this->db->conn->prepare($query);
        $persiap->bind_param("i", $booking_id);
        $persiap->execute();
        return $persiap->get_result();
    }

    public function getPaymentId($payment_id) {
        $query = "SELECT * FROM payments WHERE payment_id = ?";
        $persiap = $this->db->conn->prepare($query);
        $persiap->bind_param("i", $payment_id);
        $persiap->execute();
        return $persiap->get_result(); // Mengembalikan mysqli result object
    }

    public function add() {
        $query = "INSERT INTO payments (booking_id, payment_date, user_file, payment_method, status, amount) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $persiap = $this->db->conn->prepare($query);
        $persiap->bind_param("issssd", $this->booking_id, $this->payment_date, $this->user_file, $this->payment_method, $this->status, $this->amount);
        return $persiap->execute();
    }

    public function delete() {
        $query = "DELETE FROM payments WHERE payment_id = ?";
        $persiap = $this->db->conn->prepare($query);
        $persiap->bind_param("i", $this->payment_id);
        $result = $persiap->execute();
        if (!$result) {
            error_log("Delete failed: " . $this->db->conn->error);
        }
        return $result;
    }

    public function update() {
        $query = "UPDATE payments SET booking_id = ?, payment_date = ?, user_file = ?, payment_method = ?, status = ? WHERE payment_id = ?";
        $persiap = $this->db->conn->prepare($query);
        $persiap->bind_param("isssi", $this->booking_id, $this->payment_date, $this->user_file, $this->payment_method,  $this->status, $this->payment_id);
        return $persiap->execute();
    }
}
?>