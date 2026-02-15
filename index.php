<?php include $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/views/includes/header.php'; ?>

<div class="hero-section animate-on-scroll">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h1 class="display-4 fw-bold mb-4">Temukan Kenyamanan Terbaik di Hotel Kami</h1>
                <p class="lead mb-4">Nikmati pengalaman menginap yang tak terlupakan dengan fasilitas terbaik dan pelayanan istimewa dari tim kami.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="#kamar" class="btn btn-outline-primary btn-lg px-4">
                        <i class="fas fa-calendar-check me-2"></i> Lihat Kamar
                    </a>
                    <a href="#fasilitas" class="btn btn-outline-primary btn-lg px-4">
                        <i class="fas fa-info-circle me-2"></i> Lihat Fasilitas
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner rounded-3 shadow-lg">
                        <div class="carousel-item active">
                            <img src="assets/images/rooms/deluxe.jpg" class="d-block w-100" alt="Deluxe Room">
                        </div>
                        <div class="carousel-item">
                            <img src="assets/images/rooms/executive.jpg" class="d-block w-100" alt="Executive Room">
                        </div>
                        <div class="carousel-item">
                            <img src="assets/images/rooms/suite.jpg" class="d-block w-100" alt="Suite Room">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="kamar" class="container py-5">
    <div class="text-center mb-5 animate-on-scroll">
        <h2 class="fw-bold">Tipe Kamar Kami</h2>
        <p class="text-muted">Pilih kamar yang sesuai dengan kebutuhan dan budget Anda</p>
    </div>

    <div class="row g-4 animate-on-scroll">
        <?php 
        require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Kamar.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Database.php';
        $database = new Database();
        $db = $database->getConnection();
        $kamar = new Kamar($db);
        $stmt = $kamar->readAll();
        $count = 0;

        if ($stmt && $stmt->rowCount() > 0):
            while ($count < 3 && $row = $stmt->fetch(PDO::FETCH_ASSOC)):
                $count++;
        ?>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <img src="assets/images/rooms/<?php echo $row['gambar']; ?>" class="card-img-top" alt="<?php echo $row['tipe_kamar']; ?>" style="height: 250px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $row['tipe_kamar']; ?></h5>
                    <h6 class="card-subtitle mb-2 text-success">Rp <?php echo number_format($row['harga_per_malam'], 0, ',', '.'); ?> / malam</h6>
                    <p class="card-text"><?php echo substr($row['deskripsi'], 0, 100) . '...'; ?></p>
                    <div class="mb-3">
                        <h6 class="fw-bold">Fasilitas:</h6>
                        <ul class="list-unstyled small">
                            <?php 
                            $facilities = explode(',', $row['fasilitas']);
                            foreach(array_slice($facilities, 0, 3) as $facility): ?>
                                <li><i class="fas fa-check-circle text-primary me-2"></i><?php echo trim($facility); ?></li>
                            <?php endforeach; ?>
                            <?php if(count($facilities) > 3): ?>
                                <li><i class="fas fa-plus-circle text-muted me-2"></i>dan <?php echo count($facilities) - 3; ?> lainnya</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <div class="card-footer bg-white border-0">
                    <a href="http://localhost/hotel_reservation/views/auth/login.php" class="btn btn-primary w-100">
                        <i class="fas fa-calendar-check me-2"></i> Pesan Sekarang
                    </a>
                </div>
            </div>
        </div>
        <?php endwhile; endif; ?>
    </div>

    <div class="text-center mt-4 animate-on-scroll">
        <a href="http://localhost/hotel_reservation/views/auth/login.php" class="btn btn-outline-primary">
            <i class="fas fa-bed me-2"></i> Login untuk melihat semua kamar
        </a>
    </div>
</div>

<div id="fasilitas" class="bg-light py-5">
    <div class="container">
        <div class="text-center mb-5 animate-on-scroll">
            <h2 class="fw-bold">Fasilitas Hotel</h2>
            <p class="text-muted">Nikmati berbagai fasilitas terbaik selama menginap di hotel kami</p>
        </div>

        <div class="row g-4 animate-on-scroll">
            <?php 
            require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Fasilitas.php';
            $fasilitas = new Fasilitas($db);
            $stmtFasilitas = $fasilitas->readAll();
            $fasilitasCount = 0;
            $modals = '';

            if ($stmtFasilitas && $stmtFasilitas->rowCount() > 0):
                while ($fasilitasCount < 3 && $row = $stmtFasilitas->fetch(PDO::FETCH_ASSOC)):
                    $fasilitasCount++;
            ?>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <img src="assets/images/facilities/<?php echo $row['gambar']; ?>" class="card-img-top" alt="<?php echo $row['nama']; ?>" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row['nama']; ?></h5>
                        <p class="card-text"><?php echo substr($row['deskripsi'], 0, 100) . '...'; ?></p>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <button class="btn btn-sm btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#fasilitasModal<?php echo $row['id']; ?>">
                            <i class="fas fa-info-circle me-2"></i> Detail
                        </button>
                    </div>
                </div>
            </div>
            <?php
            // Simpan modal untuk ditampilkan nanti
            $modals .= "<div class='modal fade' id='fasilitasModal{$row['id']}' tabindex='-1' aria-labelledby='fasilitasModalLabel{$row['id']}' aria-hidden='true'>
                <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                        
                            <h5 class='modal-title' id='fasilitasModalLabel{$row['id']}'>" . htmlspecialchars($row['nama']) . "</h5>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Tutup'></button>
                        </div>
                        <div class='modal-body text-center'>
                            <img src='assets/images/facilities/{$row['gambar']}' class='img-fluid rounded mb-3' alt='" . htmlspecialchars($row['nama']) . "'>
                            <p>" . htmlspecialchars($row['deskripsi']) . "</p>
                        </div>
                        <div class='modal-footer'>
                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Tutup</button>
                        </div>
                    </div>
                </div>
            </div>";
            endwhile; endif; ?>
        </div>

        <div class="text-center mt-4 animate-on-scroll">
            <a href="http://localhost/hotel_reservation/views/auth/login.php" class="btn btn-outline-primary">
                <i class="fas fa-sign-in-alt me-2"></i> Login untuk melihat semua fasilitas
            </a>
        </div>

        <!-- Cetak semua modal di luar grid -->
        <?php echo $modals; ?>
    </div>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/views/includes/footer.php'; ?>
