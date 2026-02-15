<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Database.php';
$database = new Database();
$db = $database->getConnection();
?>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/views/includes/header.php'; ?>

<div class="container py-4">
    <div class="row">
        <div class="col-md-3">
            <div class="card shadow-sm mb-4 animate-on-scroll">
                <div class="card-body text-center">
                    <div class="avatar mx-auto mb-3">
                        <i class="fas fa-user-circle fa-5x text-primary"></i>
                    </div>
                    <h5 class="card-title"><?php echo $_SESSION['nama']; ?></h5>
                    <p class="text-muted mb-1"><?php echo ucfirst($_SESSION['role']); ?></p>
                    <div class="d-flex justify-content-center mt-3">
                        <a href="http://localhost/hotel_reservation/proses/auth/logout.php" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </a>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm animate-on-scroll">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0">Menu</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <a href="http://localhost/hotel_reservation/views/pelanggan/kamar.php" class="text-decoration-none">
                                <i class="fas fa-bed me-2"></i> Daftar Kamar
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="http://localhost/hotel_reservation/views/pelanggan/reservasi.php" class="text-decoration-none">
                                <i class="fas fa-calendar-check me-2"></i> Reservasi Saya
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Dashboard Pelanggan</h2>
            </div>

            <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success animate__animated animate__fadeIn">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger animate__animated animate__shakeX">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="row animate-on-scroll">
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-dark text-white">
                            <h6 class="mb-0">Reservasi Terbaru</h6>
                        </div>
                        <div class="card-body">
                            <?php 
                            require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Reservasi.php';
                            require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Pembayaran.php';

                            $reservasi = new Reservasi($db);
                            $reservasi->setUserId($_SESSION['user_id']);
                            $stmt = $reservasi->readLastByUser(); // Gunakan method baru
                            $row = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
                            if ($row) {
                            // Cek status pembayaran
                            $pembayaran = new Pembayaran($db);
                            $pembayaran->setReservasiId($row['id']);
                            $stmtPembayaran = $pembayaran->readByReservasi();
                            $pembayaranData = $stmtPembayaran->fetch(PDO::FETCH_ASSOC);

                            // Jika sudah diverifikasi, ubah status jadi dikonfirmasi
                            if ($pembayaranData) {
                                if ($row['status'] !== 'selesai' && $row['status'] !== 'dibatalkan') {
                                    if ($pembayaranData['status'] == 'diverifikasi') {
                                        $row['status'] = 'dikonfirmasi';
                                    } elseif ($pembayaranData['status'] == 'ditolak') {
                                        $row['status'] = 'selesai';
                                    }
                                }
                            }
                        }
                            
                            if($row): ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-bold">Kamar:</span>
                                    <span><?php echo $row['tipe_kamar']; ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-bold">Tanggal:</span>
                                    <span><?php echo date('d M Y', strtotime($row['check_in'])) . ' - ' . date('d M Y', strtotime($row['check_out'])); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-bold">Status:</span>
                                    <span class="badge 
                                        <?php 
                                        switch($row['status']) {
                                            case 'menunggu': echo 'bg-warning text-dark'; break;
                                            case 'dikonfirmasi': echo 'bg-success'; break;
                                            case 'dibatalkan': echo 'bg-danger'; break;
                                            case 'selesai': echo 'bg-secondary'; break;
                                        }
                                        ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-bold">Total Harga:</span>
                                    <span>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></span>
                                </div>
                                <a href="http://localhost/hotel_reservation/views/pelanggan/reservasi.php" class="btn btn-sm btn-dark mt-3 w-100">Lihat Semua</a>
                            <?php else: ?>
                                <p class="text-muted">Anda belum memiliki reservasi.</p>
                                <a href="http://localhost/hotel_reservation/views/pelanggan/kamar.php" class="btn btn-sm btn-primary mt-2">Pesan Kamar</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-dark text-white">
                            <h6 class="mb-0">Kamar Populer</h6>
                        </div>
                        <div class="card-body">
                            <?php 
                            require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Kamar.php';
                            $kamar = new Kamar($db);
                            $stmt = $kamar->readMostPopular();
                            $rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

                            if (!empty($rows)):
                                foreach ($rows as $row): ?>
                                    <div class="text-center mb-3">
                                        <img src="http://localhost/hotel_reservation/assets/images/rooms/<?php echo $row['gambar']; ?>" alt="<?php echo $row['tipe_kamar']; ?>" class="img-fluid rounded" style="max-height: 150px;">
                                    </div>
                                    <h5 class="card-title"><?php echo $row['tipe_kamar']; ?></h5>
                                    <p class="card-text">Rp <?php echo number_format($row['harga_per_malam'], 0, ',', '.'); ?> / malam</p>
                                    <hr>
                                <?php endforeach; ?>
                                <a href="http://localhost/hotel_reservation/views/pelanggan/kamar.php" class="btn btn-sm btn-dark w-100">Lihat Semua Kamar</a>
                            <?php else: ?>
                                <p class="text-muted">Tidak ada kamar tersedia.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm animate-on-scroll">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0">Fasilitas Hotel</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php 
                        require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Fasilitas.php';
                        $fasilitas = new Fasilitas($db);
                        $stmt = $fasilitas->readAll();

                        if ($stmt && $stmt->rowCount() > 0):
                            $modals = ''; // untuk menyimpan semua modal
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <?php if (!empty($row['gambar'])): ?>
                                        <img src="http://localhost/hotel_reservation/assets/images/facilities/<?php echo $row['gambar']; ?>" class="card-img-top" alt="<?php echo $row['nama']; ?>" style="height: 150px; object-fit: cover;">
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h6 class="card-title"><?php echo $row['nama']; ?></h6>
                                        <p class="card-text small"><?php echo substr($row['deskripsi'], 0, 50) . '...'; ?></p>
                                    </div>
                                    <div class="card-footer bg-white border-0">
                                        <button class="btn btn-sm btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#fasilitasModal<?php echo $row['id']; ?>">
                                            <i class="fas fa-info-circle me-2"></i> Detail
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <?php
                            // Tambahkan modal untuk setiap fasilitas
                            $modals .= "
                            <div class='modal fade' id='fasilitasModal{$row['id']}' tabindex='-1' aria-labelledby='fasilitasModalLabel{$row['id']}' aria-hidden='true'>
                                <div class='modal-dialog'>
                                    <div class='modal-content'>
                                        <div class='modal-header'>
                                            <h5 class='modal-title' id='fasilitasModalLabel{$row['id']}'>" . htmlspecialchars($row['nama']) . "</h5>
                                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                        </div>
                                        <div class='modal-body text-center'>
                                            <img src='http://localhost/hotel_reservation/assets/images/facilities/{$row['gambar']}' class='img-fluid rounded mb-3' alt='" . htmlspecialchars($row['nama']) . "'>
                                            <p>" . htmlspecialchars($row['deskripsi']) . "</p>
                                        </div>
                                        <div class='modal-footer'>
                                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>";
                            ?>
                        <?php 
                            }
                        else: 
                        ?>
                            <p class="text-muted text-center">Belum ada fasilitas yang tersedia.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $modals; ?>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/views/includes/footer.php'; ?>
