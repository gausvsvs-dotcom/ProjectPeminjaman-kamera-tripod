<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Hapus Data Kontak - Sistem Peminjaman Kamera & Tripod</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">

    <link rel="stylesheet"
          href="assets/css/style.css?v=2">
</head>

<body>

<?php include 'includes/navbar.php'; ?>

<section class="hapus-kontak-section">
    <div class="container">
        <div class="hapus-kontak-card">
            <div class="hapus-kontak-icon">
                🗑️
            </div>

            <h1 class="hapus-kontak-title">Hapus Data Kontak.</h1>

            <p class="hapus-kontak-text">Apakah kamu yakin ingin menghapus data kontak ini?</p>

            <p class="hapus-kontak-warning">Data yang akan dihapus tidak dapat dikembalikan.</p>

            <form action="proses_hapus_kontak.php" method="POST">

                <input type="hidden"
                       name="id"
                       value="<?= $id; ?>">

                <button type="submit"
                        class="btn-hapus-confirm">Ya, Hapus
                </button>

                <a href="data_kontak.php"
                   class="btn-batal-hapus">
                    Batal
                </a>
            </form>
        </div>
    </div>
</section>
</body>
</html>