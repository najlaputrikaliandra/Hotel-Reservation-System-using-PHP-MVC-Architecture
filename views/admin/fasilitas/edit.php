<?php 
include $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/views/includes/header.php';

require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Fasilitas.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Database.php';

$database = new Database();
$db = $database->getConnection();
$fasilitas = new Fasilitas($db);

// Gunakan setter untuk mengatur ID
$fasilitas->setId(isset($_GET['id']) ? $_GET['id'] : die('ERROR: ID tidak ditemukan.'));

// Ambil data fasilitas
$stmt = $fasilitas->readOne();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <div class="row">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/views/includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Edit Fasilitas</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="http://localhost/hotel_reservation/views/admin/fasilitas/index.php" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
            
            <div class="card shadow-sm animate-on-scroll">
                <div class="card-body">
                    <form action="http://localhost/hotel_reservation/proses/fasilitas/edit.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                        
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Fasilitas</label>
                            <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($row['nama']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required><?php echo htmlspecialchars($row['deskripsi']); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="gambar" class="form-label">Gambar Fasilitas</label>
                            <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                            <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar</small>
                            
                            <?php if(!empty($row['gambar'])): ?>
                                <div class="mt-2">
                                    <img src="http://localhost/hotel_reservation/assets/images/facilities/<?php echo htmlspecialchars($row['gambar']); ?>" alt="<?php echo htmlspecialchars($row['nama']); ?>" class="img-thumbnail" style="width: 100px;">
                                </div>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/views/includes/footer.php'; ?>