<?php include $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/views/includes/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/views/includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Tambah Fasilitas</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="http://localhost/hotel_reservation/views/admin/fasilitas/index.php" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
            
            <div class="card shadow-sm animate-on-scroll">
                <div class="card-body">
                    <form action="http://localhost/hotel_reservation/proses/fasilitas/tambah.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Fasilitas</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="gambar" class="form-label">Gambar Fasilitas</label>
                            <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/views/includes/footer.php'; ?>