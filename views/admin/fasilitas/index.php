<?php include $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/views/includes/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/views/includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Manajemen Fasilitas</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="http://localhost/hotel_reservation/views/admin/fasilitas/tambah.php" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i> Tambah Fasilitas
                    </a>
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
                                    <th>Nama Fasilitas</th>
                                    <th>Deskripsi</th>
                                    <th>Gambar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Fasilitas.php';
                                require_once $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/models/Database.php';
                                $database = new Database();
                                $db = $database->getConnection();
                                $fasilitas = new Fasilitas($db);
                                $stmt = $fasilitas->readAll();
                                $no = 1;
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): 
                                    ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo $row['nama']; ?></td>
                                        <td><?php echo substr($row['deskripsi'], 0, 50) . '...'; ?></td>
                                        <td>
                                            <?php if($row['gambar']): ?>
                                                <img src="http://localhost/hotel_reservation/assets/images/facilities/<?php echo $row['gambar']; ?>" alt="<?php echo $row['nama']; ?>" class="img-thumbnail" style="width: 80px; height: 60px; object-fit: cover;">

                                            <?php else: ?>
                                                <span class="text-muted">Tidak ada gambar</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="http://localhost/hotel_reservation/views/admin/fasilitas/edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="http://localhost/hotel_reservation/proses/fasilitas/hapus.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus fasilitas ini?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/views/includes/footer.php'; ?>
