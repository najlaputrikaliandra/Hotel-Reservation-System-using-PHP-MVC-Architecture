<?php 
include $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/views/includes/header.php';

require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Kamar.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Database.php';

// Koneksi database
$database = new Database();
$db = $database->getConnection();
$kamar = new Kamar($db);

// Set ID kamar menggunakan setter, bukan akses langsung
if (isset($_GET['id'])) {
    $kamar->setId($_GET['id']);
} else {
    die('ERROR: ID tidak ditemukan.');
}

// Ambil data kamar berdasarkan ID
$stmt = $kamar->readOne();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <div class="row">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/views/includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Edit Kamar</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="http://localhost/hotel_reservation/views/admin/kamar/index.php" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
            
            <div class="card shadow-sm animate-on-scroll">
                <div class="card-body">
                    <form action="http://localhost/hotel_reservation/proses/kamar/edit.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tipe_kamar" class="form-label">Tipe Kamar</label>
                                <input type="text" class="form-control" id="tipe_kamar" name="tipe_kamar" value="<?php echo $row['tipe_kamar']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="harga_per_malam" class="form-label">Harga Per Malam</label>
                                <input type="number" class="form-control" id="harga_per_malam" name="harga_per_malam" value="<?php echo $row['harga_per_malam']; ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="jumlah_kamar" class="form-label">Jumlah Kamar</label>
                                <input type="number" class="form-control" id="jumlah_kamar" name="jumlah_kamar" value="<?php echo $row['jumlah_kamar']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="gambar" class="form-label">Gambar Kamar</label>
                                <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                                <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar</small>
                                <?php if($row['gambar']): ?>
                                    <div class="mt-2">
                                        <img src="assets/images/rooms/<?php echo $row['gambar']; ?>" alt="<?php echo $row['tipe_kamar']; ?>" class="img-thumbnail" style="width: 100px;">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required><?php echo $row['deskripsi']; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="fasilitas" class="form-label">Fasilitas Kamar</label>
                            <textarea class="form-control" id="fasilitas" name="fasilitas" rows="3" required><?php echo $row['fasilitas']; ?></textarea>
                            <small class="text-muted">Gunakan koma (,) untuk memisahkan fasilitas</small>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/views/includes/footer.php'; ?>