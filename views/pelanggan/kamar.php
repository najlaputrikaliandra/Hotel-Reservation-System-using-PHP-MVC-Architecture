<?php include $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/views/includes/header.php'; ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Daftar Kamar</h2>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Cari nama kamar..." id="searchInput">
                <button class="btn btn-outline-secondary" type="button" id="searchButton">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>

    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success animate_animated animate_fadeIn">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger animate_animated animate_shakeX">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="row animate-on-scroll">
        <?php 
        require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Kamar.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Database.php';
        $database = new Database();
        $db = $database->getConnection();
        $kamar = new Kamar($db);

        if(isset($_GET['search']) && !empty($_GET['search'])) {
            $keywords = $_GET['search'];
            $stmt = $kamar->search($keywords);
        } else {
            $stmt = $kamar->readAll();
        }

        $modals = ""; // buffer untuk modal

        if ($stmt->rowCount() === 0): ?>
        <div class="col-12">
            <div class="alert alert-warning text-center">
                Kamar tidak ditemukan.
            </div>
        </div>
        <?php endif;

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): 
        ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <?php if($row['gambar']): ?>
                        <img src="http://localhost/hotel_reservation/assets/images/rooms/<?php echo $row['gambar']; ?>" class="card-img-top" alt="<?php echo $row['tipe_kamar']; ?>" style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="fas fa-bed fa-4x text-white"></i>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row['tipe_kamar']; ?></h5>
                        <h6 class="card-subtitle mb-2 text-success">Rp <?php echo number_format($row['harga_per_malam'], 0, ',', '.'); ?> / malam</h6>
                        <p class="card-text small"><?php echo substr($row['deskripsi'], 0, 100) . '...'; ?></p>
                        <div class="mb-3">
                            <h6 class="fw-bold">Fasilitas:</h6>
                            <ul class="list-unstyled small">
                                <?php 
                                $facilities = explode(',', $row['fasilitas']);
                                foreach($facilities as $facility): 
                                ?>
                                    <li><i class="fas fa-check-circle text-primary me-2"></i><?php echo trim($facility); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <?php if ($row['jumlah_kamar'] > 0): ?>
                            <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#reservasiModal<?php echo $row['id']; ?>">
                                <i class="fas fa-calendar-check me-2"></i> Pesan Sekarang
                            </button>
                        <?php else: ?>
                            <div class="btn btn-danger w-100 disabled">
                                <i class="fas fa-ban me-2"></i> Sold Out
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        <?php 
        ob_start();
        ?>
        <div class="modal fade" id="reservasiModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="reservasiModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="reservasiModalLabel<?php echo $row['id']; ?>">Reservasi Kamar <?php echo $row['tipe_kamar']; ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="http://localhost/hotel_reservation/proses/reservasi/buat.php" method="POST">
                        <input type="hidden" name="kamar_id" value="<?php echo $row['id']; ?>">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="check_in_<?php echo $row['id']; ?>" class="form-label">Check-in</label>
                                    <input type="date" class="form-control" id="check_in_<?php echo $row['id']; ?>" name="check_in" min="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="check_out_<?php echo $row['id']; ?>" class="form-label">Check-out</label>
                                    <input type="date" class="form-control" id="check_out_<?php echo $row['id']; ?>" name="check_out" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="jumlah_kamar_<?php echo $row['id']; ?>" class="form-label">Jumlah Kamar</label>
                                <input type="number" class="form-control" id="jumlah_kamar_<?php echo $row['id']; ?>" name="jumlah_kamar" min="1" max="<?php echo $row['jumlah_kamar']; ?>" required>
                                <small class="text-muted">Tersedia: <?php echo $row['jumlah_kamar']; ?> kamar</small>
                            </div>
                            <div class="alert alert-info">
                                <h6 class="fw-bold">Detail Harga:</h6>
                                <p id="hargaDetail_<?php echo $row['id']; ?>">Pilih tanggal untuk melihat total harga</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Pesan Sekarang</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
        $modals .= ob_get_clean();
        endwhile;
        ?>
    </div>
</div>

<!-- Modal semua kamar ditempatkan di luar row -->
<?php echo $modals; ?>

<script>
    document.getElementById('searchButton').addEventListener('click', function () {
        const keyword = document.getElementById('searchInput').value;
        if (keyword.trim() !== "") {
            window.location.href = "?search=" + encodeURIComponent(keyword);
        }
    });

    // Tekan Enter juga bisa search
    document.getElementById('searchInput').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('searchButton').click();
        }
    });
</script>


<?php include $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/views/includes/footer.php'; ?>
