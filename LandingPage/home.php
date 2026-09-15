<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Sistem Peminjaman Kamera dan Tripod</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=10">
</head>

<body>

    <?php include 'includes/navbar.php'; ?>

    <main>
        <section class="home-hero">
            <div class="home-header">
                <img src="assets/images/gambar1.png"
                    alt="Logo Sistem Peminjaman Kamera dan Tripod"
                    class="home-logo">
                <h1>SISTEM PEMINJAMAN<br>
                    KAMERA DAN TRIPOD</h1>
                <p>Silakan pilih kamera dan tripod, tentukan kebutuhan Anda,
                   dan lakukan peminjaman dengan mudah.</p>
                <a href="kamera&tripod.php" class="home-button">Lihat Kamera &amp; Tripod</a>
            </div>

            <div class="home-image">
                <img src="assets/images/home-camera.jpg" alt="Kamera untuk peminjaman">
                <div class="home-image-overlay"></div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js">
    </script>

</body>
</html>