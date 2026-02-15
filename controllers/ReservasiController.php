<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Reservasi.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Kamar.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/User.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Database.php';

class ReservasiController {
    private $reservasiModel;
    private $kamarModel;
    private $userModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->reservasiModel = new Reservasi($db);
        $this->kamarModel = new Kamar($db);
        $this->userModel = new User($db);
    }

    public function index() {
        $this->authAdmin();
        return $this->reservasiModel->readAll();
    }

    public function byUser($user_id) {
        $this->authUser($user_id);
        $this->reservasiModel->setUserId($user_id);
        return $this->reservasiModel->readByUser();
    }

    public function create() {
        $this->authPelanggan();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->reservasiModel->setUserId($_SESSION['user_id']);
            $this->reservasiModel->setKamarId($_POST['kamar_id']);
            $this->reservasiModel->setCheckIn($_POST['check_in']);
            $this->reservasiModel->setCheckOut($_POST['check_out']);
            $this->reservasiModel->setJumlahKamar($_POST['jumlah_kamar']);

            $this->kamarModel->setId($_POST['kamar_id']);
            $kamar = $this->kamarModel->readOne()->fetch(PDO::FETCH_ASSOC);

            $checkIn = new DateTime($_POST['check_in']);
            $checkOut = new DateTime($_POST['check_out']);
            $nights = $checkIn->diff($checkOut)->days;

            if ($nights < 1) {
                $_SESSION['error'] = "Check-out harus minimal satu hari setelah check-in.";
                header("Location: /hotel_reservation/views/pelanggan/kamar.php");
                exit();
            }

            $totalHarga = $kamar['harga_per_malam'] * $nights * $_POST['jumlah_kamar'];
            $this->reservasiModel->setTotalHarga($totalHarga);
            $this->reservasiModel->setStatus('menunggu');

            if ($this->reservasiModel->create()) {
                $this->kamarModel->kurangiJumlahKamar($_POST['kamar_id'], $_POST['jumlah_kamar']);
                $_SESSION['success'] = "Reservasi berhasil dibuat. Silakan lakukan pembayaran.";
                header("Location: /hotel_reservation/views/pelanggan/reservasi.php");
            } else {
                $_SESSION['error'] = "Gagal membuat reservasi";
                header("Location: /hotel_reservation/views/pelanggan/kamar.php");
            }
            exit();
        }
    }

    public function detail($id) {
        $this->auth();

        $this->reservasiModel->setId($id);
        $reservasi = $this->reservasiModel->readOne()->fetch(PDO::FETCH_ASSOC);

        if ($_SESSION['role'] === 'pelanggan' && $reservasi['user_id'] != $_SESSION['user_id']) {
            header("Location: /hotel_reservation/views/pelanggan/dashboard.php");
            exit();
        }

        return $reservasi;
    }

    public function updateStatus($id, $status) {
        $this->authAdmin();

        $this->reservasiModel->setId($id);
        $this->reservasiModel->setStatus($status);

        if ($this->reservasiModel->updateStatus()) {
            $_SESSION['success'] = "Status reservasi berhasil diperbarui";
        } else {
            $_SESSION['error'] = "Gagal memperbarui status reservasi";
        }

        header("Location: /hotel_reservation/views/admin/reservasi/index.php");
        exit();
    }

    public function cancel($id) {
        $this->authPelanggan();

        $this->reservasiModel->setId($id);
        $this->reservasiModel->setUserId($_SESSION['user_id']);

        if ($this->reservasiModel->cancel()) {
            $_SESSION['success'] = "Reservasi berhasil dibatalkan";
        } else {
            $_SESSION['error'] = "Gagal membatalkan reservasi";
        }

        header("Location: /hotel_reservation/views/pelanggan/reservasi.php");
        exit();
    }

    public function search($keywords) {
        $this->authAdmin();
        return $this->reservasiModel->search($keywords);
    }

    // ========== AUTH HELPER ==========
    private function auth() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: /hotel_reservation/views/auth/login.php");
            exit();
        }
    }

    private function authAdmin() {
        $this->auth();
        if ($_SESSION['role'] !== 'admin') {
            header("Location: /hotel_reservation/views/auth/login.php");
            exit();
        }
    }

    private function authPelanggan() {
        $this->auth();
        if ($_SESSION['role'] !== 'pelanggan') {
            header("Location: /hotel_reservation/views/auth/login.php");
            exit();
        }
    }

    private function authUser($user_id) {
        $this->auth();
        if ($_SESSION['user_id'] != $user_id) {
            header("Location: /hotel_reservation/views/auth/login.php");
            exit();
        }
    }
}