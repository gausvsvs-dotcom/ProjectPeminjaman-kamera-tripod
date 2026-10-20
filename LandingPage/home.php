<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Sistem Peminjaman Kamera dan Tripod</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=100">
</head>

<body>

    <?php include 'includes/navbar.php'; ?>

    <main>
        <section class="home-hero">
            <div class="home-header">
                <div class="animate-hero-group text-center">
                    <img src="assets/images/gambar1.png"
                        alt="Logo Sistem Peminjaman Kamera dan Tripod"
                        class="home-logo">
                    
                    <h1>PEMINJAMAN<br>
                        KAMERA DAN TRIPOD</h1>
                    
                    <p>Silakan pilih kamera dan tripod, tentukan kebutuhan Anda,
                       dan lakukan peminjaman dengan mudah.</p>
                    <a href="kamera&tripod.php" class="home-button">Lihat Kamera &amp; Tripod</a>
                </div>
            </div>

            <div class="home-image">
                <img src="assets/images/home-camera.jpg" alt="Kamera untuk peminjaman" class="animate-fade-image">
                <div class="home-image-overlay"></div>
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
</script>

</body>
</html>