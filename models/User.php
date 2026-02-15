<?php
class User {
    private $conn;
    private $table_name = "users";

    private $id;
    private $nama;
    private $email;
    private $password;
    private $role;
    private $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Getter & Setter
    public function getId() {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;
    }

    public function getNama() {
        return $this->nama;
    }
    public function setNama($nama) {
        $this->nama = $nama;
    }

    public function getEmail() {
        return $this->email;
    }
    public function setEmail($email) {
        $this->email = $email;
    }

    public function getPassword() {
        return $this->password;
    }
    public function setPassword($password) {
        $this->password = $password;
    }

    public function getRole() {
        return $this->role;
    }
    public function setRole($role) {
        $this->role = $role;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }
    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }

    // Method untuk login
    public function login() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->execute();
        return $stmt;
    }

    // Get user by ID
    public function getUserById() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        return $stmt;
    }
}
?>
