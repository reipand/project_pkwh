    </main>

    <!-- Footer -->
    <footer class="footer bg-dark text-light py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="mb-3">SMP Bina Informatika</h5>
                    <p class="mb-3">Sekolah humanis yang mengedepankan kompetensi anak untuk berkembang sesuai dengan bakat dan minat masing-masing individu.</p>
                    <div class="social-links">
                        <a href="#" class="me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="me-3"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="me-3"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="mb-3">Informasi Kontak</h5>
                    <div class="contact-info">
                        <div class="d-flex mb-2">
                            <i class="fas fa-map-marker-alt me-3 mt-1"></i>
                            <p class="mb-0">Jl. Tegal Rotan Raya No.8 A, Sawah Baru, Kec. Ciputat, Kota Tangerang Selatan, Banten 15412</p>
                        </div>
                        <div class="d-flex mb-2">
                            <i class="fas fa-phone me-3 mt-1"></i>
                            <p class="mb-0">083896226790 (Rizam Nuruzaman)</p>
                        </div>
                        <div class="d-flex mb-2">
                            <i class="fas fa-envelope me-3 mt-1"></i>
                            <p class="mb-0">info@smpbinainformatika.sch.id</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="mb-3">Link Cepat</h5>
                    <ul class="list-unstyled">
                        <li><a href="#pendaftaran" class="text-light text-decoration-none">Pendaftaran Siswa Baru</a></li>
                        <li><a href="#prosedur" class="text-light text-decoration-none">Tata Cara Pendaftaran</a></li>
                        <li><a href="#informasi" class="text-light text-decoration-none">Informasi Sekolah</a></li>
                        <li><a href="#gallery" class="text-light text-decoration-none">Galeri Kegiatan</a></li>
                        <li><a href="login.php" class="text-light text-decoration-none">Login Siswa</a></li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> SMP Bina Informatika. Semua hak dilindungi.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">Dibuat dengan <i class="fas fa-heart text-danger"></i> untuk pendidikan Indonesia</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="backToTop" class="btn btn-primary back-to-top" title="Kembali ke atas">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="loading-spinner d-none">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- JavaScript Files -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/animations.js"></script>
    
    <!-- SweetAlert2 untuk notifikasi -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom Scripts -->
    <script>
        // Back to Top functionality
        $(window).scroll(function() {
            if ($(this).scrollTop() > 300) {
                $('#backToTop').fadeIn();
            } else {
                $('#backToTop').fadeOut();
            }
        });

        $('#backToTop').click(function() {
            $('html, body').animate({scrollTop: 0}, 800);
            return false;
        });

        // Smooth scrolling untuk anchor links
        $('a[href^="#"]').on('click', function(event) {
            var target = $(this.getAttribute('href'));
            if (target.length) {
                event.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 80
                }, 1000);
            }
        });

        // Loading spinner
        function showLoading() {
            $('#loadingSpinner').removeClass('d-none');
        }

        function hideLoading() {
            $('#loadingSpinner').addClass('d-none');
        }

        // Global AJAX setup
        $(document).ajaxStart(function() {
            showLoading();
        }).ajaxStop(function() {
            hideLoading();
        });

        // Toast notification function
        function showToast(message, type = 'success') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                icon: type,
                title: message
            });
        }

        // Form validation helper
        function validateForm(formId) {
            const form = document.getElementById(formId);
            if (!form.checkValidity()) {
                form.reportValidity();
                return false;
            }
            return true;
        }
    </script>

    <?php if (isset($custom_scripts)): ?>
        <?php foreach ($custom_scripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html> 