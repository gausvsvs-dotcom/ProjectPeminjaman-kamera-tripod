<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include 'includes/koneksi.php';

$query_kontak = "SELECT COUNT(*) AS jumlah FROM kontak";
$result_kontak = mysqli_query($conn, $query_kontak);
$data_kontak = mysqli_fetch_assoc($result_kontak);
$jumlah_kontak = $data_kontak['jumlah'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Dashboard Admin - Sistem Peminjaman Kamera & Tripod</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
    <link rel=  "stylesheet" href="assets/css/style.css?v=2">
    
</head>

<body>

<?php include 'includes/navbar.php'; ?>


<section class="admin-dashboard">
    <div class="container">

        <!-- JUDUL DASHBOARD -->

        <div class="text-center">

            <h1 class="admin-title">
                Dashboard Admin
            </h1>

            <p class="admin-subtitle">
                Selamat datang, <?= $_SESSION['admin']; ?>.
                Anda dapat mengelola data sistem peminjaman dengan mudah.
            </p>
        </div>


        <!-- CARD ADMIN -->
        <div class="row justify-content-center g-4">
            <div class="col-md-6 col-lg-4">
                <div class="admin-card text-center">
                    <div class="admin-card-icon">
                        📩
                    </div>

                    <h4 class="admin-card-title">
                        Data Kontak
                    </h4>

                    <div class="admin-card-number">
                        <?= $jumlah_kontak; ?>
                    </div>

                    <p class="admin-card-text">
                        Jumlah data kontak pesan yang masuk
                        dari pengguna website.
                    </p>

                    <a href="data_kontak.php"
                       class="btn admin-btn">
                        Lihat Data Kontak
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>