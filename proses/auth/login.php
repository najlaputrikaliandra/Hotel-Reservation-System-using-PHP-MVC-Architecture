<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/controllers/AuthController.php';

$authController = new AuthController();
$authController->login();
?>