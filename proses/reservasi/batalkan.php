<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/controllers/ReservasiController.php';

$reservasiController = new ReservasiController();
$reservasiController->cancel($_GET['id']);
?>
