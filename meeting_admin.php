<?php 

require_once 'connect.php';

class Meet {
    public $room_id;
    public $room_name;
    public $location;
    public $capacity;
    public $facilities;
    public $status;
    public $created_at;
    public $updated_at;
    public $total_price;
    public $db;

    public function __construct(){
        $this->db = new Database();
    }

    public function meetread(){
        $query = "SELECT * FROM meeting_rooms";
        $result = $this->db->query($query);
        if (!$result) {
            die("Query failed: " . $this->db->conn->error);
        }
        return $result;
    }

    public function search($keyword) {
        $query = "SELECT * FROM meeting_rooms WHERE room_name LIKE ? OR location LIKE ? OR facilities LIKE ?";
        $cegah = $this->db->conn->prepare($query);
        $searchKeyword = "%$keyword%";
        $cegah->bind_param("sss", $searchKeyword, $searchKeyword, $searchKeyword);
        $cegah->execute();
        return $cegah->get_result();
    }

    public function add(){
        $query = "INSERT INTO meeting_rooms (room_name, location, capacity, facilities, status, total_price) 
        VALUES (?, ?, ?, ?, ?, ?)";
        $cegah = $this->db->conn->prepare($query);
        $cegah->bind_param("ssisss", $this->room_name, $this->location, $this->capacity, $this->facilities, $this->status, $this->total_price);
        $cegah->execute();
    }
    public function delete(){
        $query = "DELETE FROM meeting_rooms WHERE room_id = '$this->room_id'";
        $cegah = $this->db->conn->prepare($query);
        $cegah->execute();
    }

    public function getByID() {
        $query = "SELECT * FROM meeting_rooms WHERE room_id = ?";
        $cegah = $this->db->conn->prepare($query);
        $cegah->bind_param("i", $this->room_id);
        $cegah->execute();
        return $cegah->get_result();
    }

    public function getRoomByID($room_id) {
        // Validasi input
        if (!is_numeric($room_id)) {
            throw new Exception("Id Room harus berupa angka.");
        }
    
        // Query untuk mengambil data ruangan
        $query = "SELECT * FROM meeting_rooms WHERE room_id = ?";
        $persiapan = $this->db->conn->prepare($query);
    
        // Jika prepare gagal, lempar exception
        if (!$persiapan) {
            throw new Exception("Gagal menyiapkan query: " . $this->db->conn->error);
        }
    
        // Bind parameter dan execute
        $persiapan->bind_param("i", $room_id);
        $persiapan->execute();
    
        // Ambil hasil query
        $result = $persiapan->get_result();
    
        // Jika tidak ada data, kembalikan null
        if ($result->num_rows === 0) {
            return null;
        }
    
        // Kembalikan data dalam bentuk array asosiatif
        return $result->fetch_assoc();
    }

    public function update() {
        $query = "UPDATE meeting_rooms SET room_name = ?, location = ?, capacity = ?, facilities = ?, status = ?, total_price = ?, update_at = NOW() WHERE room_id = ?";
        $cegah = $this->db->conn->prepare($query);
        $cegah->bind_param("ssisssi", $this->room_name, $this->location, $this->capacity, $this->facilities, $this->status, $this->total_price, $this->room_id);
        $cegah->execute();
    }
}
// var cegah digunakan untuk menghalau SQl Injection

?>

