<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Kamar.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Database.php';

class KamarController {
    private $kamarModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->kamarModel = new Kamar($db);
    }

    private function isAdmin() {
        session_start();
        return isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin';
    }

    public function index() {
        if (!$this->isAdmin()) {
            header("Location: /hotel_reservation/views/auth/login.php");
            exit();
        }
        return $this->kamarModel->readAll();
    }

    public function create() {
        if (!$this->isAdmin()) {
            header("Location: /hotel_reservation/views/auth/login.php");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->kamarModel->setTipeKamar($_POST['tipe_kamar']);
            $this->kamarModel->setHargaPerMalam($_POST['harga_per_malam']);
            $this->kamarModel->setJumlahKamar($_POST['jumlah_kamar']);
            $this->kamarModel->setDeskripsi($_POST['deskripsi']);
            $this->kamarModel->setFasilitas($_POST['fasilitas']);

            $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/assets/images/rooms/';
            $file = $_FILES["gambar"];
            $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
            $check = getimagesize($file["tmp_name"]);

            if ($check !== false) {
                $new_filename = uniqid() . '.' . $imageFileType;
                $target_file = $target_dir . $new_filename;

                if ($file["size"] > 5000000) {
                    $_SESSION['error'] = "File terlalu besar. Maksimal 5MB.";
                } elseif (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $_SESSION['error'] = "Hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
                } elseif (move_uploaded_file($file["tmp_name"], $target_file)) {
                    $this->kamarModel->setGambar($new_filename);
                    if ($this->kamarModel->create()) {
                        $_SESSION['success'] = "Kamar berhasil ditambahkan.";
                        header("Location: /hotel_reservation/views/admin/kamar/index.php");
                        exit();
                    } else {
                        $_SESSION['error'] = "Gagal menambahkan kamar.";
                    }
                } else {
                    $_SESSION['error'] = "Gagal upload file.";
                }
            } else {
                $_SESSION['error'] = "File bukan gambar.";
            }

            header("Location: /hotel_reservation/views/admin/kamar/tambah.php");
            exit();
        }
    }

    public function edit($id) {
        if (!$this->isAdmin()) {
            header("Location: /hotel_reservation/views/auth/login.php");
            exit();
        }

        $this->kamarModel->setId($id);
        return $this->kamarModel->readOne();
    }

    public function update() {
        if (!$this->isAdmin()) {
            header("Location: /hotel_reservation/views/auth/login.php");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->kamarModel->setId($_POST['id']);
            $this->kamarModel->setTipeKamar($_POST['tipe_kamar']);
            $this->kamarModel->setHargaPerMalam($_POST['harga_per_malam']);
            $this->kamarModel->setJumlahKamar($_POST['jumlah_kamar']);
            $this->kamarModel->setDeskripsi($_POST['deskripsi']);
            $this->kamarModel->setFasilitas($_POST['fasilitas']);

            $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/assets/images/rooms/';
            $oldData = $this->kamarModel->readOne()->fetch(PDO::FETCH_ASSOC);

            if (!empty($_FILES["gambar"]["name"])) {
                $file = $_FILES["gambar"];
                $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
                $check = getimagesize($file["tmp_name"]);

                if ($check !== false) {
                    $new_filename = uniqid() . '.' . $imageFileType;
                    $target_file = $target_dir . $new_filename;

                    if ($file["size"] > 5000000) {
                        $_SESSION['error'] = "File terlalu besar. Maksimal 5MB.";
                    } elseif (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $_SESSION['error'] = "Hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
                    } elseif (move_uploaded_file($file["tmp_name"], $target_file)) {
                        if (!empty($oldData['gambar']) && file_exists($target_dir . $oldData['gambar'])) {
                            unlink($target_dir . $oldData['gambar']);
                        }
                        $this->kamarModel->setGambar($new_filename);
                    } else {
                        $_SESSION['error'] = "Gagal upload file.";
                        header("Location: /hotel_reservation/views/admin/kamar/edit.php?id=" . $this->kamarModel->getId());
                        exit();
                    }
                } else {
                    $_SESSION['error'] = "File bukan gambar.";
                    header("Location: /hotel_reservation/views/admin/kamar/edit.php?id=" . $this->kamarModel->getId());
                    exit();
                }
            } else {
                $this->kamarModel->setGambar($oldData['gambar']);
            }

            if ($this->kamarModel->update()) {
                $_SESSION['success'] = "Kamar berhasil diperbarui.";
            } else {
                $_SESSION['error'] = "Gagal memperbarui kamar.";
            }

            header("Location: /hotel_reservation/views/admin/kamar/index.php");
            exit();
        }
    }

    public function delete($id) {
        if (!$this->isAdmin()) {
            header("Location: /hotel_reservation/views/auth/login.php");
            exit();
        }

        // Cek apakah kamar masih digunakan di reservasi
        $db = $this->kamarModel->getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM reservasi WHERE kamar_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            return 'used';
        }

        // Ambil gambar untuk dihapus
        $this->kamarModel->setId($id);
        $data = $this->kamarModel->readOne()->fetch(PDO::FETCH_ASSOC);
        $gambar = $data['gambar'];
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/assets/images/rooms/';

        if ($this->kamarModel->delete()) {
            if ($gambar && file_exists($target_dir . $gambar)) {
                unlink($target_dir . $gambar);
            }
            return 'success';
        } else {
            return 'error';
        }
    }


    public function search($keywords) {
        return $this->kamarModel->search($keywords);
    }

    public function checkAvailability($check_in, $check_out) {
        return $this->kamarModel->checkAvailability($check_in, $check_out);
    }
}
