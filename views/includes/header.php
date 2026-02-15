<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Reservation System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <script src="http://localhost/hotel_reservation/assets/css/style.css"></script>
    
    <!-- Animation CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top animate__animated animate__fadeInDown">
        <div class="container">
            <a class="navbar-brand fw-bold" href="http://localhost/hotel_reservation/index.php">
                <i class="fas fa-hotel me-2"></i>Hotel Reservation
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if(isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="http://localhost/hotel_reservation/views/admin/dashboard.php">Dashboard</a>
                        </li>
                    <?php elseif(isset($_SESSION['user_id']) && $_SESSION['role'] == 'pelanggan'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="http://localhost/hotel_reservation/views/pelanggan/kamar.php">Kamar</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="http://localhost/hotel_reservation/views/pelanggan/dashboard.php">Dashboard</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="http://localhost/hotel_reservation/index.php">Home</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i> <?php echo $_SESSION['nama']; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php if($_SESSION['role'] == 'pelanggan'): ?>
                                    <li><a class="dropdown-item" href="http://localhost/hotel_reservation/views/pelanggan/dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                                    <li><a class="dropdown-item" href="http://localhost/hotel_reservation/views/pelanggan/reservasi.php"><i class="fas fa-calendar-check me-2"></i>Reservasi</a></li>
                                <?php else: ?>
                                    <li><a class="dropdown-item" href="http://localhost/hotel_reservation/views/admin/dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="http://localhost/hotel_reservation/proses/auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="http://localhost/hotel_reservation/views/auth/login.php"><i class="fas fa-sign-in-alt me-1"></i>Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="flex-grow-1">
