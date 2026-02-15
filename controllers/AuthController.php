<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/User.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Database.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->userModel = new User($db);
    }

    public function login() {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Ambil input dari form
            $inputEmail = $_POST['email'];
            $inputPassword = $_POST['password'];

            // Set email ke model
            $this->userModel->setEmail($inputEmail);

            // Ambil data user berdasarkan email
            $stmt = $this->userModel->login();

            if ($stmt && $stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                // Verifikasi password polos (tanpa password_verify)
                if ($inputPassword === $row['password']) {
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['nama'] = $row['nama'];
                    $_SESSION['role'] = $row['role'];

                    // Arahkan berdasarkan role
                    if ($row['role'] == 'admin') {
                        header("Location: http://localhost/hotel_reservation/views/admin/dashboard.php");
                    } else {
                        header("Location: http://localhost/hotel_reservation/views/pelanggan/dashboard.php");
                    }
                    exit();
                } else {
                    $_SESSION['error'] = "Email atau password salah";
                    header("Location: http://localhost/hotel_reservation/views/auth/login.php");
                    exit();
                }
            } else {
                $_SESSION['error'] = "Email atau password salah";
                header("Location: http://localhost/hotel_reservation/views/auth/login.php");
                exit();
            }
        }
    }

    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header("Location: http://localhost/hotel_reservation/index.php");
        exit();
    }
}
?>