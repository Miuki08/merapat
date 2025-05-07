<?php
require_once 'connect.php';

class Review {
    public $review_id;
    public $user_id;
    public $room_id;
    public $rating;
    public $review_text;
    public $created_at;
    public $response;
    public $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllReviews() {
        $query = "SELECT reviews.*, users.name as user_name, meeting_rooms.room_name
                  FROM reviews
                  INNER JOIN users ON reviews.user_id = users.user_id
                  INNER JOIN meeting_rooms ON reviews.room_id = meeting_rooms.room_id";
        $result = $this->db->query($query);
        if (!$result) {
            die("Query failed: " . $this->db->conn->error);
        }
        return $result;
    }

    public function getReviewById($review_id) {
        $query = "SELECT * FROM reviews WHERE review_id = ?";
        $stmt = $this->db->conn->prepare($query);
        $stmt->bind_param("i", $review_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getReviewsByUserId($user_id) {
        $query = "SELECT reviews.*, meeting_rooms.room_name 
                  FROM reviews
                  INNER JOIN meeting_rooms ON reviews.room_id = meeting_rooms.room_id
                  WHERE user_id = ?";
        $stmt = $this->db->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getReviewsByRoomId($room_id) {
        $query = "SELECT reviews.*, users.name as user_name 
                  FROM reviews
                  INNER JOIN users ON reviews.user_id = users.user_id
                  WHERE room_id = ?";
        $stmt = $this->db->conn->prepare($query);
        $stmt->bind_param("i", $room_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function searchReviews($keyword) {
        $query = "SELECT reviews.*, users.name as user_name, meeting_rooms.room_name
                  FROM reviews
                  INNER JOIN users ON reviews.user_id = users.user_id
                  INNER JOIN meeting_rooms ON reviews.room_id = meeting_rooms.room_id
                  WHERE reviews.review_text LIKE ? OR users.name LIKE ? OR meeting_rooms.room_name LIKE ?";
        $stmt = $this->db->conn->prepare($query);
        $searchKeyword = "%$keyword%";
        $stmt->bind_param("sss", $searchKeyword, $searchKeyword, $searchKeyword);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function add() {
        $query = "INSERT INTO reviews (user_id, room_id, rating, review_text, response) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->conn->prepare($query);
        $stmt->bind_param("iiiss", $this->user_id, $this->room_id, $this->rating, $this->review_text, $this->response);
        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM reviews WHERE review_id = ?";
        $stmt = $this->db->conn->prepare($query);
        $stmt->bind_param("i", $this->review_id);
        $result = $stmt->execute();
        if (!$result) {
            error_log("Delete failed: " . $this->db->conn->error);
        }
        return $result;
    }

    public function update() {
        $query = "UPDATE reviews SET user_id = ?, room_id = ?,  rating = ?, review_text = ?, response = ? WHERE review_id = ?";
        $stmt = $this->db->conn->prepare($query);
        $stmt->bind_param("iiissi", $this->user_id, $this->room_id, $this->rating, $this->review_text, $this->response, $this->review_id);
        return $stmt->execute();
    }

    public function addResponse() {
        $query = "UPDATE reviews SET response = ? WHERE review_id = ?";
        $stmt = $this->db->conn->prepare($query);
        $stmt->bind_param("si", $this->response, $this->review_id);
        return $stmt->execute();
    }
}
?>