<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/controllers/FasilitasController.php';

$fasilitasController = new FasilitasController();
$fasilitasController->update();
?>
