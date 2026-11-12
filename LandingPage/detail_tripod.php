
<?php

require_once __DIR__ . '/includes/koneksi.php';

// Mengambil ID kamera dari URL
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Mengambil data kamera berdasarkan ID
$query = mysqli_prepare($conn, "
    SELECT 
        b.*,
        kb.nama_kondisi
    FROM barang b
    LEFT JOIN kondisi_barang kb 
        ON b.id_kondisi = kb.id_kondisi
    WHERE b.id_barang = ?
    AND b.jenis_barang = 'tripod'
    LIMIT 1
");

mysqli_stmt_bind_param($query, "i", $id);
mysqli_stmt_execute($query);

$result = mysqli_stmt_get_result($query);
$data = mysqli_fetch_assoc($result);

// Jika data kamera tidak ditemukan
if (!$data) {
    http_response_code(404);
    exit("Data kamera tidak ditemukan.");
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detail Tripod - Sistem Peminjaman Kamera dan Tripod</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="assets/css/style.css?v=100">
</head>

<body>

<?php include 'includes/navbar.php'; ?>

<section class="detail-produk-section py-5">
    <div class="container">
        <div class="row align-items-center g-5">

            <!-- FOTO TRIPOD -->
            <div class="col-lg-6">

                <div class="detail-produk-image">
                    <img src="<?= htmlspecialchars($data['foto']); ?>"
                    alt="<?= htmlspecialchars($data['nama_barang']); ?>"
                    class="img-fluid">
                </div>

            </div>


            <!-- INFORMASI TRIPOD -->
            <div class="col-lg-6">

                <span class="badge bg-primary mb-3">
                    Tripod
                </span>

                <h1 class="detail-produk-title">
                    <?= htmlspecialchars($data['nama_barang']); ?>
                </h1>

                 <p class="detail-produk-description">
                    <?= htmlspecialchars($data['deskripsi']); ?>
                </p>

                <div class="detail-produk-info">
                     <p>
                        <strong>Status:</strong>
                        <?= htmlspecialchars($data['nama_kondisi']); ?>
                    </p>
                </div>

                <a href="kamera&tripod.php"
                   class="btn btn-detail">
                   ~ Kembali ~
                </a>

                <a href="/ProjectPeminjaman-kamera-tripod/Transaksi/Peminjaman/peminjaman.php?id=<?= $id ?>&jenis=kamera" class="btn btn-ajukan">
                    ~ Ajukan Peminjaman ~
                </a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js">
</script>

</body>
</html>