<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/controllers/PembayaranController.php';

$pembayaranController = new PembayaranController();
$pembayaranController->updateStatus($_GET['id'], $_GET['status']);
?>
