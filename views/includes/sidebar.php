<!-- Sidebar -->
<div class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse animate__animated animate__fadeInLeft" id="sidebarMenu">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="http://localhost/hotel_reservation/views/admin/dashboard.php">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
            </li>
            <?php if($_SESSION['role'] == 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link text-white <?php echo strpos($_SERVER['PHP_SELF'], 'kamar') !== false ? 'active' : ''; ?>" href="http://localhost/hotel_reservation/views/admin/kamar/index.php">
                        <i class="fas fa-bed me-2"></i>Manajemen Kamar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?php echo strpos($_SERVER['PHP_SELF'], 'fasilitas') !== false ? 'active' : ''; ?>" href="http://localhost/hotel_reservation/views/admin/fasilitas/index.php">
                        <i class="fas fa-swimming-pool me-2"></i>Manajemen Fasilitas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?php echo strpos($_SERVER['PHP_SELF'], 'reservasi') !== false ? 'active' : ''; ?>" href="http://localhost/hotel_reservation/views/admin/reservasi/index.php">
                        <i class="fas fa-calendar-check me-2"></i>Manajemen Reservasi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?php echo strpos($_SERVER['PHP_SELF'], 'pembayaran') !== false ? 'active' : ''; ?>" href="http://localhost/hotel_reservation/views/admin/pembayaran/index.php">
                        <i class="fas fa-money-bill-wave me-2"></i>Manajemen Pembayaran
                    </a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'kamar.php' ? 'active' : ''; ?>" href="http://localhost/hotel_reservation/views/pelanggan/kamar.php">
                        <i class="fas fa-bed me-2"></i>Daftar Kamar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'reservasi.php' ? 'active' : ''; ?>" href="http://localhost/hotel_reservation/views/pelanggan/reservasi.php">
                        <i class="fas fa-calendar-check me-2"></i>Reservasi Saya
                    </a>
                </li>
            <?php endif; ?>
        </ul>
        
        <hr class="bg-secondary">
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white" href="http://localhost/hotel_reservation/proses/auth/logout.php">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </li>
        </ul>
    </div>
</div>
