
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Sistem Peminjaman Kamera dan Tripod</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=104">
</head>

<body>

    <?php include 'includes/navbar.php'; ?>

    <main>

        <!-- HERO SECTION -->
        <section class="home-hero">
            <div class="home-header">
                <div class="animate-hero-group text-center">
                    <img src="assets/images/gambar1.png"
                        alt="Logo Sistem Peminjaman Kamera dan Tripod"
                        class="home-logo">

                    <h1>PEMINJAMAN<br>
                        KAMERA DAN TRIPOD</h1>

                    <p>
                        Silakan pilih kamera dan tripod, tentukan kebutuhan Anda,
                        dan lakukan peminjaman dengan mudah.
                    </p>
                </div>
            </div>

            <div class="home-image">
                <img src="assets/images/home-camera.jpg"
                    alt="Kamera untuk peminjaman"
                    class="animate-fade-image">

                <div class="home-image-overlay"></div>
            </div>
        </section>


        <!-- KATALOG KAMERA DAN TRIPOD -->
        <section class="home-catalog scroll-blur">
            <div class="container">
                <div class="home-section-heading">
                    <h2>Jelajahi Peralatan Kami</h2>
                    <p>
                        Temukan berbagai pilihan kamera dan tripod
                        yang tersedia untuk kebutuhan Anda.
                    </p>
                </div>

                <a href="kamera&tripod.php" class="home-catalog-card">
                    <img src="assets/images/bg2.jpg"
                        alt="Pilihan kamera dan tripod">

                    <div class="home-catalog-overlay">
                        <div>
                            <h3>Kamera &amp; Tripod</h3>
                            <p>
                                Lihat berbagai pilihan peralatan,
                                informasi, dan ketersediaan barang.
                            </p>
                            <span>
                                Lihat Selengkapnya
                                <i class="fas fa-arrow-right"></i>
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        </section>


        <!-- KEUNGGULAN LAYANAN -->
        <section class="home-benefits scroll-blur">
            <div class="container">
                <div class="home-section-heading">
                    <h2>Keunggulan Layanan</h2>
                    <p>
                        Nikmati kemudahan dalam mencari informasi
                        peralatan melalui website kami.
                    </p>
                </div>

                <div class="row g-4">

                    <div class="col-md-4">
                        <div class="home-benefit-card">
                            <div class="home-benefit-icon">
                                <i class="fas fa-camera"></i>
                            </div>
                            <h3>Pilihan Peralatan</h3>
                            <p>
                                Menyediakan informasi berbagai pilihan
                                kamera dan tripod dalam satu website.
                            </p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="home-benefit-card">
                            <div class="home-benefit-icon">
                                <i class="fas fa-circle-info"></i>
                            </div>
                            <h3>Informasi Lengkap</h3>
                            <p>
                                Pengunjung dapat melihat detail,
                                kondisi, dan ketersediaan peralatan.
                            </p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="home-benefit-card">
                            <div class="home-benefit-icon">
                                <i class="fas fa-laptop"></i>
                            </div>
                            <h3>Akses Mudah</h3>
                            <p>
                                Informasi peralatan dan layanan dapat
                                diakses melalui website dengan mudah.
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </section>


        <!-- HUBUNGI KAMI -->
        <section class="home-contact scroll-blur">
            <div class="container">
                <div class="home-contact-content">
                    <div>
                        <h2>Butuh Informasi Lebih Lanjut?</h2>
                        <p>
                            Silakan hubungi kami jika memiliki pertanyaan
                            atau ingin menyampaikan pesan.
                        </p>
                    </div>

                    <a href="kontak.php" class="home-contact-button">
                        Hubungi Kami
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </section>

    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const logoHome = document.querySelector('.home-logo');

        if (logoHome) {
            logoHome.addEventListener('click', function () {
                this.classList.toggle('logo-pause');
            });
        }


const blurSections = document.querySelectorAll('.scroll-blur');

const blurObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.classList.add('show');
            blurObserver.unobserve(entry.target);
        }
    });
}, {
    threshold: 0.15
});

blurSections.forEach((section) => {
    blurObserver.observe(section);
});

    </script>
</body>
</html>
