<?php include $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/views/includes/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/views/includes/sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Manajemen Pembayaran</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Cari payment user,metode" id="searchInput">
                        <button class="btn btn-outline-secondary" type="button" id="searchButton">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
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
                                    <th>Pelanggan</th>
                                    <th>Kamar</th>
                                    <th>Tanggal</th>
                                    <th>Metode</th>
                                    <th>Jumlah</th>
                                    <th>Bukti</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Pembayaran.php';
                                require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Database.php';
                                $database = new Database();
                                $db = $database->getConnection();
                                $pembayaran = new Pembayaran($db);
                                $no = 1;
                                $modalData = [];

                                if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
                                    $stmt = $pembayaran->search($_GET['search']);
                                } else {
                                    $stmt = $pembayaran->readAll();
                                }

                                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                if (count($results) > 0):
                                    foreach ($results as $row):
                                        $modalData[] = ['id' => $row['id'], 'bukti' => $row['bukti_pembayaran']];
                                ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo $row['nama_pelanggan']; ?></td>
                                        <td><?php echo $row['tipe_kamar']; ?></td>
                                        <td><?php echo date('d M Y', strtotime($row['check_in'])) . ' - ' . date('d M Y', strtotime($row['check_out'])); ?></td>
                                        <td><?php echo ucfirst($row['metode_pembayaran']); ?></td>
                                        <td>Rp <?php echo number_format($row['jumlah'], 0, ',', '.'); ?></td>
                                        <td>
                                            <?php if($row['bukti_pembayaran']): ?>
                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#buktiModal<?php echo $row['id']; ?>">
                                                    <i class="fas fa-eye"></i> Lihat
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted">Tidak ada bukti</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge 
                                                <?php 
                                                switch($row['status']) {
                                                    case 'menunggu': echo 'bg-warning text-dark'; break;
                                                    case 'diverifikasi': echo 'bg-success'; break;
                                                    case 'ditolak': echo 'bg-danger'; break;
                                                }
                                                ?>">
                                                <?php echo ucfirst($row['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if($row['status'] == 'menunggu'): ?>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-success" onclick="window.location.href='http://localhost/hotel_reservation/proses/pembayaran/verifikasi.php?id=<?php echo $row['id']; ?>&status=diverifikasi'">
                                                        <i class="fas fa-check"></i> Verifikasi
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="window.location.href='http://localhost/hotel_reservation/proses/pembayaran/verifikasi.php?id=<?php echo $row['id']; ?>&status=ditolak'">
                                                        <i class="fas fa-times"></i> Tolak
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">Tidak ada aksi</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">Data pembayaran tidak ditemukan.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php foreach ($modalData as $modal): ?>
<?php if ($modal['bukti']): ?>
<div class="modal fade" id="buktiModal<?php echo $modal['id']; ?>" tabindex="-1" aria-labelledby="buktiModalLabel<?php echo $modal['id']; ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="buktiModalLabel<?php echo $modal['id']; ?>">Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="http://localhost/hotel_reservation/assets/images/payments/<?php echo $modal['bukti']; ?>" class="img-fluid" alt="Bukti Pembayaran">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php endforeach; ?>

<script>
    document.getElementById('searchButton').addEventListener('click', function() {
        const searchTerm = document.getElementById('searchInput').value;
        if(searchTerm.trim() !== '') {
            window.location.href = 'index.php?search=' + encodeURIComponent(searchTerm);
        }
    });

    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if(e.key === 'Enter') {
            const searchTerm = document.getElementById('searchInput').value;
            if(searchTerm.trim() !== '') {
                window.location.href = 'index.php?search=' + encodeURIComponent(searchTerm);
            }
        }
    });
</script>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/views/includes/footer.php'; ?>
