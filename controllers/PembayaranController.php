<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Pembayaran.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Reservasi.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Database.php';

class PembayaranController {
    private $pembayaranModel;
    private $reservasiModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->pembayaranModel = new Pembayaran($db);
        $this->reservasiModel = new Reservasi($db);
    }

    public function create() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelanggan') {
            header("Location: /hotel_reservation/views/auth/login.php");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->pembayaranModel->setReservasiId($_POST['reservasi_id']);
            $this->pembayaranModel->setMetodePembayaran($_POST['metode_pembayaran']);
            $this->pembayaranModel->setJumlah($_POST['jumlah']);

            $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/assets/images/payments/';
            $imageFileType = strtolower(pathinfo($_FILES["bukti_pembayaran"]["name"], PATHINFO_EXTENSION));
            $check = getimagesize($_FILES["bukti_pembayaran"]["tmp_name"]);

            if ($check !== false) {
                $new_filename = uniqid() . '.' . $imageFileType;
                $target_file = $target_dir . $new_filename;

                if ($_FILES["bukti_pembayaran"]["size"] > 5000000) {
                    $_SESSION['error'] = "Maaf, file terlalu besar. Maksimal 5MB.";
                    header("Location: /hotel_reservation/views/pelanggan/pembayaran.php?reservasi_id=" . $_POST['reservasi_id']);
                    exit();
                }

                if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $_SESSION['error'] = "Hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
                    header("Location: /hotel_reservation/views/pelanggan/pembayaran.php?reservasi_id=" . $_POST['reservasi_id']);
                    exit();
                }

                if (move_uploaded_file($_FILES["bukti_pembayaran"]["tmp_name"], $target_file)) {
                    $this->pembayaranModel->setBuktiPembayaran($new_filename);
                    $this->pembayaranModel->setStatus("menunggu");

                    if ($this->pembayaranModel->create()) {
                        $_SESSION['success'] = "Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.";
                        header("Location: /hotel_reservation/views/pelanggan/reservasi.php");
                        exit();
                    } else {
                        $_SESSION['error'] = "Gagal mengupload bukti pembayaran";
                    }
                } else {
                    $_SESSION['error'] = "Terjadi kesalahan saat mengupload file.";
                }
            } else {
                $_SESSION['error'] = "File bukan gambar.";
            }

            header("Location: /hotel_reservation/views/pelanggan/pembayaran.php?reservasi_id=" . $_POST['reservasi_id']);
            exit();
        }
    }

    public function byReservasi($reservasi_id) {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: /hotel_reservation/views/auth/login.php");
            exit();
        }

        $this->pembayaranModel->setReservasiId($reservasi_id);
        return $this->pembayaranModel->readByReservasi();
    }

    public function index() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: /hotel_reservation/views/auth/login.php");
            exit();
        }

        return $this->pembayaranModel->readAll();
    }

    public function updateStatus($id, $status) {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: /hotel_reservation/views/auth/login.php");
            exit();
        }

        $this->pembayaranModel->setId($id);
        $this->pembayaranModel->setStatus($status);

        $stmt = $this->pembayaranModel->readByReservasi();
        $pembayaran = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->pembayaranModel->setReservasiId($pembayaran['reservasi_id']);

        if ($this->pembayaranModel->updateStatus()) {
            if ($status === 'diverifikasi') {
                $this->reservasiModel->setId($pembayaran['reservasi_id']);
                $this->reservasiModel->setStatus('dikonfirmasi');
                $this->reservasiModel->updateStatus();
            }

            $_SESSION['success'] = "Status pembayaran berhasil diperbarui";
        } else {
            $_SESSION['error'] = "Gagal memperbarui status pembayaran";
        }

        header("Location: /hotel_reservation/views/admin/pembayaran/index.php");
        exit();
    }

    public function search($keywords) {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: /hotel_reservation/views/auth/login.php");
            exit();
        }

        return $this->pembayaranModel->search($keywords);
    }
}
?>