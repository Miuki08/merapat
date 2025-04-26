<?php 

require_once 'connect.php';

class Users {
    public $user_id ;
    public $name;
    public $email;
    public $password;
    public $role;
    public $created_at;
    public $db;

    public function __construct(){
        $this->db = new Database();
    }

    public function userread(){
        $query = "SELECT * FROM users";
        $result = $this->db->query($query);
        if (!$result) {
            die("Query failed: " . $this->db->conn->error);
        }
        return $result;
    }
    
    public function search($keyword) {
        $query = "SELECT * FROM users WHERE name LIKE ? OR email LIKE ? OR role LIKE ?";
        $cegah = $this->db->conn->prepare($query);
        $searchKeyword = "%$keyword%";
        $cegah->bind_param("sss", $searchKeyword, $searchKeyword, $searchKeyword);
        $cegah->execute();
        return $cegah->get_result();
    }

    public function add(){
        $query = "INSERT INTO users (name, email, password, role) 
        VALUES (?, ?, ?, ?)";
        $cegah = $this->db->conn->prepare($query);
        $cegah->bind_param("ssss", $this->name, $this->email, $this->password, $this->role);
        $cegah->execute();
    }

    public function delete(){
        $query = "DELETE FROM users WHERE user_id = '$this->user_id'";
        $cegah = $this->db->conn->prepare($query);
        $cegah->execute();
    }

    public function getByID() {
        $query = "SELECT * FROM users WHERE user_id = ?";
        $cegah = $this->db->conn->prepare($query);
        $cegah->bind_param("i", $this->user_id);
        $cegah->execute();
        return $cegah->get_result();
    }

    public function update() {
        $query = "UPDATE users SET name = ?, email = ?, password = ?, role = ? WHERE user_id = ?";
        $cegah = $this->db->conn->prepare($query);
        $cegah->bind_param("ssssi", $this->name, $this->email, $this->password, $this->role, $this->user_id);
        $cegah->execute();
    }

    public function login($email, $password) {
        $query = "SELECT * FROM users WHERE email = ?";
        $cegah = $this->db->conn->prepare($query);
        $cegah->bind_param("s", $email);
        $cegah->execute();
        $result = $cegah->get_result();
    
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            if ($password === $user['password']) {
                // Login berhasil
                return $user;
            } else {
                // Password salah
                return false;
            }
        } else {
            // Email tidak ditemukan
            return false;
        }
    }
}
// var cegah digunakan untuk menghalau SQl Injection