<?php
class Reservasi {
    private $conn;
    private $table_name = "reservasi";

    private $id;
    private $user_id;
    private $kamar_id;
    private $check_in;
    private $check_out;
    private $jumlah_kamar;
    private $total_harga;
    private $status;
    private $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // =================== Getter & Setter ===================
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getUserId() { return $this->user_id; }
    public function setUserId($user_id) { $this->user_id = $user_id; }

    public function getKamarId() { return $this->kamar_id; }
    public function setKamarId($kamar_id) { $this->kamar_id = $kamar_id; }

    public function getCheckIn() { return $this->check_in; }
    public function setCheckIn($check_in) { $this->check_in = $check_in; }

    public function getCheckOut() { return $this->check_out; }
    public function setCheckOut($check_out) { $this->check_out = $check_out; }

    public function getJumlahKamar() { return $this->jumlah_kamar; }
    public function setJumlahKamar($jumlah_kamar) { $this->jumlah_kamar = $jumlah_kamar; }

    public function getTotalHarga() { return $this->total_harga; }
    public function setTotalHarga($total_harga) { $this->total_harga = $total_harga; }

    public function getStatus() { return $this->status; }
    public function setStatus($status) { $this->status = $status; }

    // =================== CRUD ===================

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_id=:user_id, kamar_id=:kamar_id, 
                      check_in=:check_in, check_out=:check_out, 
                      jumlah_kamar=:jumlah_kamar, total_harga=:total_harga, 
                      status=:status";
        
        $stmt = $this->conn->prepare($query);

        $this->sanitize();

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":kamar_id", $this->kamar_id);
        $stmt->bindParam(":check_in", $this->check_in);
        $stmt->bindParam(":check_out", $this->check_out);
        $stmt->bindParam(":jumlah_kamar", $this->jumlah_kamar);
        $stmt->bindParam(":total_harga", $this->total_harga);
        $stmt->bindParam(":status", $this->status);

        return $stmt->execute();
    }

    public function readAll() {
        $query = "SELECT r.*, u.nama AS nama_pelanggan, k.tipe_kamar, k.harga_per_malam, k.gambar, k.fasilitas
                  FROM " . $this->table_name . " r
                  JOIN users u ON r.user_id = u.id
                  JOIN kamar k ON r.kamar_id = k.id
                  ORDER BY r.created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Tambahkan setelah readAll()
    public function readBelumSelesai() {
        $query = "SELECT r.*, u.nama AS nama_pelanggan, k.tipe_kamar
                FROM " . $this->table_name . " r
                JOIN users u ON r.user_id = u.id
                JOIN kamar k ON r.kamar_id = k.id
                WHERE r.status != 'selesai'
                ORDER BY r.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function search($keyword) {
        $query = "SELECT r.*, u.nama AS nama_pelanggan, k.tipe_kamar, k.harga_per_malam, k.gambar
                  FROM " . $this->table_name . " r
                  JOIN users u ON r.user_id = u.id
                  JOIN kamar k ON r.kamar_id = k.id
                  WHERE u.nama LIKE :keyword OR k.tipe_kamar LIKE :keyword
                  ORDER BY r.created_at ASC";
        
        $stmt = $this->conn->prepare($query);
        $keyword = "%" . htmlspecialchars(strip_tags($keyword)) . "%";
        $stmt->bindParam(':keyword', $keyword);
        $stmt->execute();
        return $stmt;
    }

    public function readByUser() {
        $query = "SELECT r.*, k.tipe_kamar, k.harga_per_malam, k.gambar 
                  FROM " . $this->table_name . " r
                  JOIN kamar k ON r.kamar_id = k.id
                  WHERE r.user_id = ?
                  ORDER BY r.created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        $stmt->execute();
        return $stmt;
    }

    public function readLastByUser() {
        $query = "SELECT r.*, k.tipe_kamar FROM " . $this->table_name . " r
                JOIN kamar k ON r.kamar_id = k.id
                WHERE r.user_id = :user_id
                ORDER BY r.id DESC LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();

        return $stmt;
    }

    public function readOne() {
        $query = "SELECT r.*, u.nama AS nama_pelanggan, u.email, 
                         k.tipe_kamar, k.harga_per_malam, k.deskripsi, k.fasilitas
                  FROM " . $this->table_name . " r
                  JOIN users u ON r.user_id = u.id
                  JOIN kamar k ON r.kamar_id = k.id
                  WHERE r.id = ?
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        return $stmt;
    }

    public function updateStatus() {
        $query = "UPDATE " . $this->table_name . " SET status=:status WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    public function cancel() {
        $query = "SELECT kamar_id, jumlah_kamar FROM reservasi WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return false; // data reservasi tidak ditemukan
        }

        $id_kamar = $data['kamar_id'];
        $jumlah_kamar = $data['jumlah_kamar'];

        // 1. Kembalikan kuota kamar
        $updateKamar = "UPDATE kamar SET jumlah_kamar = jumlah_kamar + :jumlah WHERE id = :id_kamar";
        $stmtKamar = $this->conn->prepare($updateKamar);
        $stmtKamar->bindParam(':jumlah', $jumlah_kamar);
        $stmtKamar->bindParam(':id_kamar', $id_kamar);
        $stmtKamar->execute();

        // 2. Ubah status jadi dibatalkan
        $updateReservasi = "UPDATE reservasi SET status = 'dibatalkan' WHERE id = :id";
        $stmtReservasi = $this->conn->prepare($updateReservasi);
        $stmtReservasi->bindParam(':id', $this->id);

        return $stmtReservasi->execute();
    }

    // =================== Helper ===================

    private function sanitize() {
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->kamar_id = htmlspecialchars(strip_tags($this->kamar_id));
        $this->check_in = htmlspecialchars(strip_tags($this->check_in));
        $this->check_out = htmlspecialchars(strip_tags($this->check_out));
        $this->jumlah_kamar = htmlspecialchars(strip_tags($this->jumlah_kamar));
        $this->total_harga = htmlspecialchars(strip_tags($this->total_harga));
        $this->status = htmlspecialchars(strip_tags($this->status));
    }
}
?>