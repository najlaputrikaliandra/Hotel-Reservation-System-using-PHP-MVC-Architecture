<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/controllers/KamarController.php';

$kamarController = new KamarController();
$kamarController->update();
?>
