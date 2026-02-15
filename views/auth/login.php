<?php include $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/views/includes/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center animate__animated animate__fadeIn">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="card-title text-center mb-4">Login</h2>
                    
                    <?php if(isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger animate__animated animate__shakeX">
                            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(isset($_SESSION['success'])): ?>
                        <div class="alert alert-success animate__animated animate__fadeIn">
                            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="http://localhost/hotel_reservation/proses/auth/login.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/hotel_reservation/views/includes/footer.php'; ?>
