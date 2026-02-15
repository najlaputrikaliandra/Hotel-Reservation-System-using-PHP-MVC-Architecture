<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Fasilitas.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Database.php';

class FasilitasController {
    private $fasilitasModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->fasilitasModel = new Fasilitas($db);
    }

    public function index() {
        session_start();
        if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
            header("Location: http://localhost/hotel_reservation/views/auth/login.php");
            exit();
        }

        return $this->fasilitasModel->readAll();
    }

    public function create() {
        session_start();
        if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
            header("Location: http://localhost/hotel_reservation/views/auth/login.php");
            exit();
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->fasilitasModel->setNama($_POST['nama']);
            $this->fasilitasModel->setDeskripsi($_POST['deskripsi']);

            $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/assets/images/facilities/';
            $imageFileType = strtolower(pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION));
            $new_filename = uniqid() . '.' . $imageFileType;
            $target_file = $target_dir . $new_filename;

            $check = getimagesize($_FILES["gambar"]["tmp_name"]);
            if($check !== false) {
                if ($_FILES["gambar"]["size"] > 5000000) {
                    $_SESSION['error'] = "Maaf, file terlalu besar. Maksimal 5MB.";
                    header("Location: /hotel_reservation/views/admin/fasilitas/tambah.php");
                    exit();
                }

                if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $_SESSION['error'] = "Maaf, hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
                    header("Location: /hotel_reservation/views/admin/fasilitas/tambah.php");
                    exit();
                }

                if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                    $this->fasilitasModel->setGambar($new_filename);

                    if($this->fasilitasModel->create()) {
                        $_SESSION['success'] = "Fasilitas berhasil ditambahkan";
                        header("Location: /hotel_reservation/views/admin/fasilitas/index.php");
                        exit();
                    } else {
                        $_SESSION['error'] = "Gagal menambahkan fasilitas";
                        header("Location: /hotel_reservation/views/admin/fasilitas/tambah.php");
                        exit();
                    }
                } else {
                    $_SESSION['error'] = "Terjadi kesalahan saat mengupload file.";
                    header("Location: /hotel_reservation/views/admin/fasilitas/tambah.php");
                    exit();
                }
            } else {
                $_SESSION['error'] = "File bukan gambar.";
                header("Location: /hotel_reservation/views/admin/fasilitas/tambah.php");
                exit();
            }
        }
    }

    public function edit($id) {
        session_start();
        if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
            header("Location: /hotel_reservation/views/auth/login.php");
            exit();
        }

        $this->fasilitasModel->setId($id);
        return $this->fasilitasModel->readOne();
    }

    public function update() {
        session_start();
        if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
            header("Location: /hotel_reservation/views/auth/login.php");
            exit();
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->fasilitasModel->setId($_POST['id']);
            $this->fasilitasModel->setNama($_POST['nama']);
            $this->fasilitasModel->setDeskripsi($_POST['deskripsi']);

            $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/assets/images/facilities/';
            $old_image = $this->fasilitasModel->readOne()->fetch(PDO::FETCH_ASSOC)['gambar'];

            if(!empty($_FILES["gambar"]["name"])) {
                $imageFileType = strtolower(pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION));
                $new_filename = uniqid() . '.' . $imageFileType;
                $target_file = $target_dir . $new_filename;

                $check = getimagesize($_FILES["gambar"]["tmp_name"]);
                if($check !== false) {
                    if ($_FILES["gambar"]["size"] > 5000000) {
                        $_SESSION['error'] = "Maaf, file terlalu besar. Maksimal 5MB.";
                        header("Location: /hotel_reservation/views/admin/fasilitas/edit.php?id=" . $_POST['id']);
                        exit();
                    }

                    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $_SESSION['error'] = "Maaf, hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
                        header("Location: /hotel_reservation/views/admin/fasilitas/edit.php?id=" . $_POST['id']);
                        exit();
                    }

                    if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                        if($old_image && file_exists($target_dir . $old_image)) {
                            unlink($target_dir . $old_image);
                        }

                        $this->fasilitasModel->setGambar($new_filename);
                    } else {
                        $_SESSION['error'] = "Terjadi kesalahan saat mengupload file.";
                        header("Location: /hotel_reservation/views/admin/fasilitas/edit.php?id=" . $_POST['id']);
                        exit();
                    }
                } else {
                    $_SESSION['error'] = "File bukan gambar.";
                    header("Location: /hotel_reservation/views/admin/fasilitas/edit.php?id=" . $_POST['id']);
                    exit();
                }
            } else {
                $this->fasilitasModel->setGambar($old_image);
            }

            if($this->fasilitasModel->update()) {
                $_SESSION['success'] = "Fasilitas berhasil diperbarui";
                header("Location: /hotel_reservation/views/admin/fasilitas/index.php");
                exit();
            } else {
                $_SESSION['error'] = "Gagal memperbarui fasilitas";
                header("Location: /hotel_reservation/views/admin/fasilitas/edit.php?id=" . $_POST['id']);
                exit();
            }
        }
    }

    public function delete($id) {
        session_start();
        if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
            header("Location: /hotel_reservation/views/auth/login.php");
            exit();
        }

        $this->fasilitasModel->setId($id);
        $stmt = $this->fasilitasModel->readOne();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $image = $row['gambar'];

        $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/assets/images/facilities/';
        if($this->fasilitasModel->delete()) {
            if($image && file_exists($target_dir . $image)) {
                unlink($target_dir . $image);
            }

            $_SESSION['success'] = "Fasilitas berhasil dihapus";
        } else {
            $_SESSION['error'] = "Gagal menghapus fasilitas";
        }

        header("Location: /hotel_reservation/views/admin/fasilitas/index.php");
        exit();
    }

    public function search($keywords) {
        return $this->fasilitasModel->search($keywords);
    }
}