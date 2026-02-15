<?php
class Pembayaran {
    private $conn;
    private $table_name = "pembayaran";

    private $id;
    private $reservasi_id;
    private $metode_pembayaran;
    private $jumlah;
    private $bukti_pembayaran;
    private $status;
    private $tanggal_pembayaran;

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

    public function getReservasiId() {
        return $this->reservasi_id;
    }

    public function setReservasiId($reservasi_id) {
        $this->reservasi_id = $reservasi_id;
    }

    public function getMetodePembayaran() {
        return $this->metode_pembayaran;
    }

    public function setMetodePembayaran($metode) {
        $this->metode_pembayaran = $metode;
    }

    public function getJumlah() {
        return $this->jumlah;
    }

    public function setJumlah($jumlah) {
        $this->jumlah = $jumlah;
    }

    public function getBuktiPembayaran() {
        return $this->bukti_pembayaran;
    }

    public function setBuktiPembayaran($bukti) {
        $this->bukti_pembayaran = $bukti;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getTanggalPembayaran() {
        return $this->tanggal_pembayaran;
    }

    public function setTanggalPembayaran($tanggal) {
        $this->tanggal_pembayaran = $tanggal;
    }

    // Method untuk membuat pembayaran
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                SET reservasi_id=:reservasi_id, metode_pembayaran=:metode_pembayaran, 
                    jumlah=:jumlah, bukti_pembayaran=:bukti_pembayaran, status=:status";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":reservasi_id", $this->reservasi_id);
        $stmt->bindParam(":metode_pembayaran", $this->metode_pembayaran);
        $stmt->bindParam(":jumlah", $this->jumlah);
        $stmt->bindParam(":bukti_pembayaran", $this->bukti_pembayaran);
        $stmt->bindParam(":status", $this->status);

        return $stmt->execute();
    }

    // Method untuk membaca pembayaran berdasarkan reservasi
    public function readByReservasi() {
        $query = "SELECT * FROM " . $this->table_name . " 
                WHERE reservasi_id = ? 
                LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->reservasi_id);
        $stmt->execute();
        return $stmt;
    }

    // Method untuk membaca semua pembayaran
    public function readAll() {
        $query = "SELECT p.*, r.check_in, r.check_out, u.nama as nama_pelanggan, k.tipe_kamar
                FROM " . $this->table_name . " p
                JOIN reservasi r ON p.reservasi_id = r.id
                JOIN users u ON r.user_id = u.id
                JOIN kamar k ON r.kamar_id = k.id
                ORDER BY p.tanggal_pembayaran ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Method untuk update status pembayaran
    public function updateStatus() {
        $query = "UPDATE " . $this->table_name . " 
                SET status=:status 
                WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            // Jika status menjadi diverifikasi, update status reservasi
            if ($this->status == 'diverifikasi') {
                $query_reservasi = "UPDATE reservasi SET status='dikonfirmasi' WHERE id=:reservasi_id";
                $stmt_reservasi = $this->conn->prepare($query_reservasi);
                $stmt_reservasi->bindParam(":reservasi_id", $this->reservasi_id);
                $stmt_reservasi->execute();
            }
            return true;
        }
        return false;
    }

    // Method untuk mencari pembayaran
    public function search($keywords) {
        $query = "SELECT p.*, r.check_in, r.check_out, u.nama AS nama_pelanggan, k.tipe_kamar
                FROM " . $this->table_name . " p
                JOIN reservasi r ON p.reservasi_id = r.id
                JOIN users u ON r.user_id = u.id
                JOIN kamar k ON r.kamar_id = k.id
                WHERE u.nama LIKE :keyword OR p.metode_pembayaran LIKE :keyword
                ORDER BY p.tanggal_pembayaran ASC";

        $stmt = $this->conn->prepare($query);
        $keyword = "%" . $keywords . "%";
        $stmt->bindParam(":keyword", $keyword);
        $stmt->execute();
        return $stmt;
    }
}
?>