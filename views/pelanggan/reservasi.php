<?php include $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/views/includes/header.php'; ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Reservasi Saya</h2>
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

    <div class="card shadow-sm animate-on-scroll">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Kamar</th>
                            <th>Tanggal</th>
                            <th>Jumlah Kamar</th>
                            <th>Total Harga</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Reservasi.php';
                        require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Database.php';
                        require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Pembayaran.php';
                        $database = new Database();
                        $db = $database->getConnection();
                        $reservasi = new Reservasi($db);
                        $reservasi->setUserId($_SESSION['user_id']);
                        $stmt = $reservasi->readByUser();
                        $no = 1;
                        $reservasiList = [];
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                            $pembayaran = new Pembayaran($db);
                            $pembayaran->setReservasiId($row['id']);
                            $stmtPembayaran = $pembayaran->readByReservasi();
                            $row['pembayaran'] = $stmtPembayaran->fetch(PDO::FETCH_ASSOC);
                            if ($row['pembayaran']) {
                                if ($row['status'] !== 'selesai' && $row['status'] !== 'dibatalkan') {
                                    if ($row['pembayaran']['status'] == 'diverifikasi') {
                                        $row['status'] = 'dikonfirmasi';
                                    } elseif ($row['pembayaran']['status'] == 'ditolak') {
                                        $row['status'] = 'selesai'; // jika admin tolak
                                    }
                                }
                            }
                            $reservasiList[] = $row;
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if (!empty($row['gambar'])): ?>
                                        <img src="http://localhost/hotel_reservation/assets/images/rooms/<?php echo $row['gambar']; ?>" alt="<?php echo $row['tipe_kamar']; ?>" class="img-thumbnail me-2" style="width: 60px; height: 45px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="img-thumbnail me-2 bg-secondary d-flex justify-content-center align-items-center" style="width: 60px; height: 45px;">
                                            <i class="fas fa-bed text-white"></i>
                                        </div>
                                    <?php endif; ?>
                                    <span><?php echo $row['tipe_kamar']; ?></span>
                                </div>
                            </td>
                            <td><?php echo date('d M Y', strtotime($row['check_in'])) . ' - ' . date('d M Y', strtotime($row['check_out'])); ?></td>
                            <td><?php echo $row['jumlah_kamar']; ?></td>
                            <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                            <td>
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
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#detailModal<?php echo $row['id']; ?>">
                                            <i class="fas fa-eye me-1"></i> Detail
                                        </a></li>
                                        <?php if($row['status'] == 'menunggu'): ?>
                                            <li><a class="dropdown-item" href="http://localhost/hotel_reservation/views/pelanggan/pembayaran.php?reservasi_id=<?php echo $row['id']; ?>">
                                                <i class="fas fa-money-bill-wave me-1"></i> Bayar
                                            </a></li>
                                            <?php if (!$row['pembayaran']): ?>
                                                <li><a class="dropdown-item" href="http://localhost/hotel_reservation/proses/reservasi/batalkan.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin membatalkan reservasi ini?')">
                                                    <i class="fas fa-times me-1"></i> Batalkan
                                                </a></li>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php foreach($reservasiList as $row): ?>
    <div class="modal fade" id="detailModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="detailModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel<?php echo $row['id']; ?>">Detail Reservasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Informasi Kamar</h6>
                            <?php if (!empty($row['gambar'])): ?>
                                <div class="mb-3">
                                    <img src="http://localhost/hotel_reservation/assets/images/rooms/<?php echo $row['gambar']; ?>" alt="<?php echo $row['tipe_kamar']; ?>" class="img-fluid rounded">
                                </div>
                            <?php endif; ?>
                            <p><strong>Tipe Kamar:</strong> <?php echo $row['tipe_kamar']; ?></p>
                            <p><strong>Harga Per Malam:</strong> Rp <?php echo number_format($row['harga_per_malam'], 0, ',', '.'); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Detail Reservasi</h6>
                            <p><strong>Check-in:</strong> <?php echo date('d M Y', strtotime($row['check_in'])); ?></p>
                            <p><strong>Check-out:</strong> <?php echo date('d M Y', strtotime($row['check_out'])); ?></p>
                            <p><strong>Jumlah Kamar:</strong> <?php echo $row['jumlah_kamar']; ?></p>
                            <p><strong>Total Harga:</strong> Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></p>
                            <p><strong>Status:</strong> 
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
                            </p>
                        </div>
                    </div>

                    <?php if ($row['pembayaran']): ?>
                    <hr>
                    <h6>Informasi Pembayaran</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Metode Pembayaran:</strong> <?php echo ucfirst($row['pembayaran']['metode_pembayaran']); ?></p>
                            <p><strong>Jumlah:</strong> Rp <?php echo number_format($row['pembayaran']['jumlah'], 0, ',', '.'); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong> 
                                <span class="badge 
                                    <?php 
                                    switch($row['pembayaran']['status']) {
                                        case 'menunggu': echo 'bg-warning text-dark'; break;
                                        case 'diverifikasi': echo 'bg-success'; break;
                                        case 'ditolak': echo 'bg-danger'; break;
                                    }
                                    ?>">
                                    <?php echo ucfirst($row['pembayaran']['status']); ?>
                                </span>
                            </p>
                            <?php if($row['pembayaran']['bukti_pembayaran']): ?>
                                <p><strong>Bukti Pembayaran:</strong> 
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#buktiModal<?php echo $row['pembayaran']['id']; ?>">
                                        <i class="fas fa-eye me-1"></i> Lihat
                                    </button>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <?php if(isset($row['pembayaran']['bukti_pembayaran'])): ?>
    <div class="modal fade" id="buktiModal<?php echo $row['pembayaran']['id']; ?>" tabindex="-1" aria-labelledby="buktiModalLabel<?php echo $row['pembayaran']['id']; ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="buktiModalLabel<?php echo $row['pembayaran']['id']; ?>">Bukti Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="http://localhost/hotel_reservation/assets/images/payments/<?php echo $row['pembayaran']['bukti_pembayaran']; ?>" class="img-fluid" alt="Bukti Pembayaran">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
<?php endforeach; ?>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/views/includes/footer.php'; ?>
