<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/controllers/KamarController.php';

$kamarController = new KamarController();
$result = $kamarController->delete($_GET['id']);

if ($result === 'used') {
    echo "<script>
        alert('Kamar tidak dapat dihapus karena masih digunakan dalam reservasi.');
        window.location.href = '/hotel_reservation/views/admin/kamar/index.php';
    </script>";
} elseif ($result === 'success') {
    echo "<script>
        alert('Kamar berhasil dihapus.');
        window.location.href = '/hotel_reservation/views/admin/kamar/index.php';
    </script>";
} else {
    echo "<script>
        alert('Terjadi kesalahan saat menghapus kamar.');
        window.location.href = '/hotel_reservation/views/admin/kamar/index.php';
    </script>";
}
?>