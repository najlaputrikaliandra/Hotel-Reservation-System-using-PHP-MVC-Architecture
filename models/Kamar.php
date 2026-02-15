<?php
class Kamar {
    private $conn;
    private $table_name = "kamar";

    private $id;
    private $tipe_kamar;
    private $harga_per_malam;
    private $jumlah_kamar;
    private $deskripsi;
    private $gambar;
    private $fasilitas;
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

    public function getTipeKamar() {
        return $this->tipe_kamar;
    }
    public function setTipeKamar($tipe_kamar) {
        $this->tipe_kamar = $tipe_kamar;
    }

    public function getHargaPerMalam() {
        return $this->harga_per_malam;
    }
    public function setHargaPerMalam($harga_per_malam) {
        $this->harga_per_malam = $harga_per_malam;
    }

    public function getJumlahKamar() {
        return $this->jumlah_kamar;
    }
    public function setJumlahKamar($jumlah_kamar) {
        $this->jumlah_kamar = $jumlah_kamar;
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

    public function getFasilitas() {
        return $this->fasilitas;
    }
    public function setFasilitas($fasilitas) {
        $this->fasilitas = $fasilitas;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }
    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                SET tipe_kamar=:tipe_kamar, harga_per_malam=:harga_per_malam, 
                    jumlah_kamar=:jumlah_kamar, deskripsi=:deskripsi, 
                    gambar=:gambar, fasilitas=:fasilitas";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":tipe_kamar", $this->tipe_kamar);
        $stmt->bindParam(":harga_per_malam", $this->harga_per_malam);
        $stmt->bindParam(":jumlah_kamar", $this->jumlah_kamar);
        $stmt->bindParam(":deskripsi", $this->deskripsi);
        $stmt->bindParam(":gambar", $this->gambar);
        $stmt->bindParam(":fasilitas", $this->fasilitas);

        return $stmt->execute();
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function search($keywords) {
        $query = "SELECT * FROM " . $this->table_name . " 
                WHERE tipe_kamar LIKE :keywords 
                ORDER BY created_at ASC";

        $stmt = $this->conn->prepare($query);
        $keywords = "%{$keywords}%";
        $stmt->bindParam(":keywords", $keywords);
        $stmt->execute();
        return $stmt;
    }

    public function readMostPopular() {
        $query = "SELECT k.*, COUNT(r.kamar_id) as jumlah_pesan
                FROM kamar k
                JOIN reservasi r ON k.id = r.kamar_id
                GROUP BY k.id
                HAVING jumlah_pesan = (
                    SELECT MAX(jumlah) FROM (
                        SELECT COUNT(kamar_id) as jumlah 
                        FROM reservasi GROUP BY kamar_id
                    ) as subquery
                )";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        return $stmt;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                SET tipe_kamar=:tipe_kamar, harga_per_malam=:harga_per_malam, 
                    jumlah_kamar=:jumlah_kamar, deskripsi=:deskripsi, 
                    gambar=:gambar, fasilitas=:fasilitas
                WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":tipe_kamar", $this->tipe_kamar);
        $stmt->bindParam(":harga_per_malam", $this->harga_per_malam);
        $stmt->bindParam(":jumlah_kamar", $this->jumlah_kamar);
        $stmt->bindParam(":deskripsi", $this->deskripsi);
        $stmt->bindParam(":gambar", $this->gambar);
        $stmt->bindParam(":fasilitas", $this->fasilitas);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }

    public function kurangiJumlahKamar($id, $jumlah) {
        $query = "UPDATE " . $this->table_name . " 
                SET jumlah_kamar = jumlah_kamar - :jumlah 
                WHERE id = :id AND jumlah_kamar >= :jumlah";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":jumlah", $jumlah, PDO::PARAM_INT);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function checkAvailability($check_in, $check_out) {
        $query = "SELECT * FROM " . $this->table_name . " 
                WHERE id NOT IN (
                    SELECT kamar_id FROM reservasi 
                    WHERE (
                        (check_in <= :check_out AND check_out >= :check_in)
                        AND status IN ('menunggu', 'dikonfirmasi')
                    )
                ) AND jumlah_kamar > 0";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":check_in", $check_in);
        $stmt->bindParam(":check_out", $check_out);
        $stmt->execute();
        return $stmt;
    }

    public function getConnection() {
    return $this->conn;
    }
}
?>