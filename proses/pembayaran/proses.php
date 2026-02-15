<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/controllers/PembayaranController.php';

$pembayaranController = new PembayaranController();
$pembayaranController->create();
?>
