<?php
class Fasilitas {
    private $conn;
    private $table_name = "fasilitas";

    private $id;
    private $nama;
    private $deskripsi;
    private $gambar;
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

    public function getDeskripsi() {
        return $this->deskripsi;
    }

    public function setDeskripsi($deskripsi) {
        $this->deskripsi = $deskripsi;
    }

    public function getGambar() {
        return $this->gambar;
    }

    public function setGambar($gambar) {
        $this->gambar = $gambar;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }

    // Method untuk menambahkan fasilitas
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                SET nama=:nama, deskripsi=:deskripsi, gambar=:gambar";

        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->nama = htmlspecialchars(strip_tags($this->nama));
        $this->deskripsi = htmlspecialchars(strip_tags($this->deskripsi));
        $this->gambar = htmlspecialchars(strip_tags($this->gambar));

        // Bind values
        $stmt->bindParam(":nama", $this->nama);
        $stmt->bindParam(":deskripsi", $this->deskripsi);
        $stmt->bindParam(":gambar", $this->gambar);

        return $stmt->execute();
    }

    // Method untuk membaca semua fasilitas
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Method untuk membaca satu fasilitas
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        return $stmt;
    }

    // Method untuk update fasilitas
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                SET nama=:nama, deskripsi=:deskripsi, gambar=:gambar
                WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->nama = htmlspecialchars(strip_tags($this->nama));
        $this->deskripsi = htmlspecialchars(strip_tags($this->deskripsi));
        $this->gambar = htmlspecialchars(strip_tags($this->gambar));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind values
        $stmt->bindParam(":nama", $this->nama);
        $stmt->bindParam(":deskripsi", $this->deskripsi);
        $stmt->bindParam(":gambar", $this->gambar);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    // Method untuk menghapus fasilitas
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }

    // Method untuk mencari fasilitas
    public function search($keywords) {
        $query = "SELECT * FROM " . $this->table_name . " 
                WHERE nama LIKE ? OR deskripsi LIKE ?
                ORDER BY created_at ASC";

        $stmt = $this->conn->prepare($query);

        $keywords = htmlspecialchars(strip_tags($keywords));
        $keywords = "%{$keywords}%";

        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);

        $stmt->execute();
        return $stmt;
    }
}
?>