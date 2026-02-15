<?php
include $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/views/includes/header.php';

require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Kamar.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Reservasi.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Pembayaran.php';

$database = new Database();
$db = $database->getConnection();

$kamar = new Kamar($db);
$reservasi = new Reservasi($db);
$pembayaran = new Pembayaran($db);
?>

<div class="container-fluid">
    <div class="row">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/views/includes/sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard Admin</h1>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success animate__animated animate__fadeIn">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger animate__animated animate__shakeX">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="row animate-on-scroll">
                <!-- Total Kamar -->
                <div class="col-md-4 mb-4">
                    <div class="card bg-primary text-white h-100 shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-uppercase">Total Kamar</h6>
                                    <h2 class="mb-0">
                                        <?php
                                        $stmt = $kamar->readAll();
                                        echo $stmt->rowCount();
                                        ?>
                                    </h2>
                                </div>
                                <i class="fas fa-bed fa-3x opacity-50"></i>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-top-0">
                            <a href="/hotel_reservation/views/admin/kamar/index.php" class="text-white stretched-link">Lihat Detail</a>
                        </div>
                    </div>
                </div>

                <!-- Reservasi Baru -->
                <div class="col-md-4 mb-4">
                    <div class="card bg-success text-white h-100 shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-uppercase">Reservasi Baru</h6>
                                    <h2 class="mb-0">
                                        <?php
                                        $stmt = $reservasi->readAll();
                                        $count = 0;
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            if ($row['status'] === 'menunggu') {
                                                $count++;
                                            }
                                        }
                                        echo $count;
                                        ?>
                                    </h2>
                                </div>
                                <i class="fas fa-calendar-check fa-3x opacity-50"></i>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-top-0">
                            <a href="/hotel_reservation/views/admin/reservasi/index.php" class="text-white stretched-link">Lihat Detail</a>
                        </div>
                    </div>
                </div>

                <!-- Pembayaran Menunggu -->
                <div class="col-md-4 mb-4">
                    <div class="card bg-warning text-dark h-100 shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-uppercase">Pembayaran Menunggu</h6>
                                    <h2 class="mb-0">
                                        <?php
                                        $stmt = $pembayaran->readAll();
                                        $count = 0;
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            if ($row['status'] === 'menunggu') {
                                                $count++;
                                            }
                                        }
                                        echo $count;
                                        ?>
                                    </h2>
                                </div>
                                <i class="fas fa-money-bill-wave fa-3x opacity-50"></i>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-top-0">
                            <a href="/hotel_reservation/views/admin/pembayaran/index.php" class="text-dark stretched-link">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reservasi Terbaru & Kamar Tersedia -->
            <div class="row animate-on-scroll">
                <!-- Reservasi Terbaru -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow">
                        <div class="card-header bg-dark text-white">
                            <h6 class="mb-0">Reservasi Terbaru</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Pelanggan</th>
                                            <th>Kamar</th>
                                            <th>Tanggal</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $stmt = $reservasi->readAll();
                                        $count = 0;
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            if ($row['status'] === 'selesai') continue;
                                            if ($count >= 5) break;

                                            // Cek status pembayaran jika status reservasi masih "menunggu"
                                            if ($row['status'] === 'menunggu') {
                                                $pembayaran->setReservasiId($row['id']);
                                                $stmtPembayaran = $pembayaran->readByReservasi();
                                                $pembayaranRow = $stmtPembayaran->fetch(PDO::FETCH_ASSOC);

                                                if ($pembayaranRow) {
                                                    if ($pembayaranRow['status'] === 'diverifikasi') {
                                                        $row['status'] = 'dikonfirmasi';
                                                    } elseif ($pembayaranRow['status'] === 'ditolak') {
                                                        $row['status'] = 'selesai';
                                                    }
                                                }
                                            }

                                            $count++;
                                            ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['nama_pelanggan']); ?></td>
                                                <td><?php echo htmlspecialchars($row['tipe_kamar']); ?></td>
                                                <td><?php echo date('d M Y', strtotime($row['check_in'])) . ' - ' . date('d M Y', strtotime($row['check_out'])); ?></td>
                                                <td>
                                                    <span class="badge 
                                                        <?php
                                                        switch ($row['status']) {
                                                            case 'menunggu': echo 'bg-warning text-dark'; break;
                                                            case 'dikonfirmasi': echo 'bg-success'; break;
                                                            case 'dibatalkan': echo 'bg-danger'; break;
                                                            case 'selesai': echo 'bg-secondary'; break;
                                                            default: echo 'bg-light';
                                                        }
                                                        ?>">
                                                        <?php echo ucfirst($row['status']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php
                                        }

                                        if ($count === 0) {
                                            echo '<tr><td colspan="4">Tidak ada data reservasi.</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <a href="/hotel_reservation/views/admin/reservasi/index.php" class="btn btn-sm btn-dark mt-2">Lihat Semua</a>
                        </div>
                    </div>
                </div>

                <!-- Kamar Tersedia -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow">
                        <div class="card-header bg-dark text-white">
                            <h6 class="mb-0">Kamar Tersedia</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tipe Kamar</th>
                                            <th>Harga</th>
                                            <th>Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $stmt = $kamar->readAll();
                                        $count = 0;
                                        while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) && $count < 5):
                                            ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['tipe_kamar']); ?></td>
                                                <td>Rp <?php echo number_format($row['harga_per_malam'], 0, ',', '.'); ?></td>
                                                <td><?php echo htmlspecialchars($row['jumlah_kamar']); ?></td>
                                            </tr>
                                            <?php
                                            $count++;
                                        endwhile;
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <a href="/hotel_reservation/views/admin/kamar/index.php" class="btn btn-sm btn-dark mt-2">Lihat Semua</a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/views/includes/footer.php'; ?>