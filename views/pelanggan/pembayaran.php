<?php 
include $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/views/includes/header.php';

if(!isset($_GET['reservasi_id']) || empty($_GET['reservasi_id'])) {
    header("Location: http://localhost/hotel_reservation/views/pelanggan/reservasi.php");
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Reservasi.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Pembayaran.php';

$database = new Database();
$db = $database->getConnection();

$reservasi = new Reservasi($db);
$reservasi->setId($_GET['reservasi_id']);
$stmt = $reservasi->readOne();
$reservasiData = $stmt->fetch(PDO::FETCH_ASSOC);

if($reservasiData['user_id'] != $_SESSION['user_id']) {
    header("Location: http://localhost/hotel_reservation/views/pelanggan/reservasi.php");
    exit();
}

$pembayaran = new Pembayaran($db);
$pembayaran->setReservasiId($reservasiData['id']);
$stmtPembayaran = $pembayaran->readByReservasi();
$pembayaranData = $stmtPembayaran->fetch(PDO::FETCH_ASSOC);
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Pembayaran Reservasi</h2>
        <a href="http://localhost/hotel_reservation/views/pelanggan/reservasi.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
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
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0">Detail Reservasi</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold">Kamar:</span>
                        <span><?php echo $reservasiData['tipe_kamar']; ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold">Check-in:</span>
                        <span><?php echo date('d M Y', strtotime($reservasiData['check_in'])); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold">Check-out:</span>
                        <span><?php echo date('d M Y', strtotime($reservasiData['check_out'])); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold">Jumlah Kamar:</span>
                        <span><?php echo $reservasiData['jumlah_kamar']; ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold">Total Harga:</span>
                        <span>Rp <?php echo number_format($reservasiData['total_harga'], 0, ',', '.'); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold">Status:</span>
                        <span class="badge 
                            <?php 
                            switch($reservasiData['status']) {
                                case 'menunggu': echo 'bg-warning text-dark'; break;
                                case 'dikonfirmasi': echo 'bg-success'; break;
                                case 'dibatalkan': echo 'bg-danger'; break;
                                case 'selesai': echo 'bg-secondary'; break;
                            }
                            ?>">
                            <?php echo ucfirst($reservasiData['status']); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0">Informasi Pembayaran</h6>
                </div>
                <div class="card-body">
                    <?php if($pembayaranData): ?>
                        <div class="alert alert-info">
                            <p>Anda sudah mengupload bukti pembayaran untuk reservasi ini. Status pembayaran: 
                                <span class="badge 
                                    <?php 
                                    switch($pembayaranData['status']) {
                                        case 'menunggu': echo 'bg-warning text-dark'; break;
                                        case 'diverifikasi': echo 'bg-success'; break;
                                        case 'ditolak': echo 'bg-danger'; break;
                                    }
                                    ?>">
                                    <?php echo ucfirst($pembayaranData['status']); ?>
                                </span>
                            </p>
                            <?php if($pembayaranData['bukti_pembayaran']): ?>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#buktiModal">
                                    <i class="fas fa-eye me-1"></i> Lihat Bukti Pembayaran
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <form action="http://localhost/hotel_reservation/proses/pembayaran/proses.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="reservasi_id" value="<?php echo $reservasiData['id']; ?>">
                            <input type="hidden" name="jumlah" value="<?php echo $reservasiData['total_harga']; ?>">
                            
                            <div class="mb-3">
                                <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                                <select class="form-select" id="metode_pembayaran" name="metode_pembayaran" required>
                                    <option value="" selected disabled>Pilih metode pembayaran</option>
                                    <option value="transfer bank">Transfer Bank</option>
                                    <option value="kartu kredit">Kartu Kredit</option>
                                    <option value="e-wallet">E-Wallet</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="bukti_pembayaran" class="form-label">Bukti Pembayaran</label>
                                <input type="file" class="form-control" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/*" required>
                                <small class="text-muted">Upload bukti transfer atau pembayaran (format: JPG, PNG, maks 5MB)</small>
                            </div>
                            
                            <div class="alert alert-warning">
                                <h6 class="fw-bold">Instruksi Pembayaran:</h6>
                                <ol class="small">
                                    <li>Lakukan pembayaran sebesar <strong>Rp <?php echo number_format($reservasiData['total_harga'], 0, ',', '.'); ?></strong></li>
                                    <li>Upload bukti pembayaran pada form di atas</li>
                                    <li>Admin akan memverifikasi pembayaran Anda dalam waktu 1x24 jam</li>
                                    <li>Status reservasi akan berubah menjadi "dikonfirmasi" setelah pembayaran diverifikasi</li>
                                </ol>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-1"></i> Kirim Bukti Pembayaran
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="buktiModal" tabindex="-1" aria-labelledby="buktiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="buktiModalLabel">Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="http://localhost/hotel_reservation/assets/images/payments/<?php echo $pembayaranData['bukti_pembayaran']; ?>" class="img-fluid" alt="Bukti Pembayaran">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/views/includes/footer.php'; ?>
