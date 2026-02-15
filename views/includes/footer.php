<!-- Footer -->
<footer class="bg-dark text-white py-4 mt-auto animate__animated animate__fadeInUp">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-hotel me-2"></i>Hotel Reservation</h5>
                <p>Sistem reservasi kamar hotel online yang modern dan mudah digunakan.</p>
            </div>
            <div class="col-md-4">
                <h5 class="fw-bold mb-3">Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="http://localhost/hotel_reservation/index.php" class="text-white text-decoration-none">Home</a></li>
                    <li><a href="http://localhost/hotel_reservation/views/pelanggan/kamar.php" class="text-white text-decoration-none">Kamar</a></li>
                    <?php if(!isset($_SESSION['user_id'])): ?>
                        <li><a href="http://localhost/hotel_reservation/views/auth/login.php" class="text-white text-decoration-none">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="col-md-4">
                <h5 class="fw-bold mb-3">Contact Us</h5>
                <ul class="list-unstyled">
                    <li><i class="fas fa-map-marker-alt me-2"></i> Jl. Contoh No. 123, Jakarta</li>
                    <li><i class="fas fa-phone me-2"></i> +62 123 4567 890</li>
                    <li><i class="fas fa-envelope me-2"></i> info@hotelreservation.com</li>
                </ul>
            </div>
        </div>
        <hr class="my-4">
        <div class="text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Hotel Reservation System. All rights reserved.</p>
        </div>
    </div>
</footer>

</main>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script src="http://localhost/hotel_reservation/assets/js/script.js"></script>

<!-- Animation JS -->
<script>
    // Add animation when scrolling
    document.addEventListener('DOMContentLoaded', function() {
        const animatedElements = document.querySelectorAll('.animate-on-scroll');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1
        });
        
        animatedElements.forEach(element => {
            observer.observe(element);
        });
    });
</script>

</body>
</html>
