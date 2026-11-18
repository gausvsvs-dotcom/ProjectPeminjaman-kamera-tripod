
<?php

require_once __DIR__ . '/includes/koneksi.php';

// ==========================================
// MENGAMBIL ID TRIPOD
// ==========================================

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    http_response_code(404);
    exit("ID tripod tidak valid.");
}


// ==========================================
// MENGAMBIL DATA TRIPOD DAN STOK UNIT
// ==========================================

$query = mysqli_prepare($conn, "

    SELECT
        b.*,
        kb.nama_kondisi,

        COALESCE(unit.total_unit, b.stok) AS total_unit,

        COALESCE(unit.jumlah_dipinjam, 0) AS jumlah_dipinjam,

        COALESCE(unit.jumlah_tersedia, b.stok) AS jumlah_tersedia

    FROM barang b

    LEFT JOIN kondisi_barang kb
        ON b.id_kondisi = kb.id_kondisi

    LEFT JOIN (

        SELECT
            bu.id_barang,

            COUNT(*) AS total_unit,

            SUM(
                CASE
                    WHEN LOWER(TRIM(bu.status_unit)) = 'dipinjam'
                    THEN 1
                    ELSE 0
                END
            ) AS jumlah_dipinjam,

            SUM(
                CASE
                    WHEN LOWER(TRIM(bu.status_unit)) = 'tersedia'
                    THEN 1
                    ELSE 0
                END
            ) AS jumlah_tersedia

        FROM barang_unit bu

        GROUP BY bu.id_barang

    ) unit ON b.id_barang = unit.id_barang

    WHERE b.id_barang = ?
    AND b.jenis_barang = 'tripod'

    LIMIT 1

");

mysqli_stmt_bind_param($query, "i", $id);
mysqli_stmt_execute($query);

$result = mysqli_stmt_get_result($query);
$data = mysqli_fetch_assoc($result);


// ==========================================
// VALIDASI DATA
// ==========================================

if (!$data) {
    http_response_code(404);
    exit("Data tripod tidak ditemukan.");
}


// ==========================================
// MENGHITUNG STOK
// ==========================================

$totalUnit = (int) $data['total_unit'];
$jumlahDipinjam = (int) $data['jumlah_dipinjam'];
$jumlahTersedia = (int) $data['jumlah_tersedia'];


// ==========================================
// MENENTUKAN STATUS
// ==========================================

if ($jumlahTersedia > 0) {

    $statusBarang = 'Tersedia';
    $warnaStatus = 'success';

} elseif ($jumlahDipinjam > 0) {

    $statusBarang = 'Dipinjam';
    $warnaStatus = 'warning';

} else {

    $statusBarang = 'Tidak Tersedia';
    $warnaStatus = 'danger';

}

?>


<!DOCTYPE html>
<html lang="id">

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

                <?php if (!empty($data['foto'])): ?>

                    <div class="detail-produk-image">

                        <img
                            src="../uploads/barang/<?= htmlspecialchars($data['foto']); ?>"
                            alt="<?= htmlspecialchars($data['nama_barang']); ?>"
                            class="img-fluid"
                        >

                    </div>

                <?php endif; ?>

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

                    <?= htmlspecialchars($data['deskripsi'] ?? ''); ?>

                </p>


                <!-- INFORMASI BARANG -->

                <div class="detail-produk-info">

                    <p>

                        <strong>Kondisi:</strong>

                        <?= htmlspecialchars($data['nama_kondisi'] ?? 'Belum ada kondisi'); ?>

                    </p>


                    <p>

                        <strong>Status:</strong>

                        <span class="badge bg-<?= $warnaStatus; ?>">

                            <?= htmlspecialchars($statusBarang); ?>

                        </span>

                    </p>


                    <p>

                        <strong>Total Barang:</strong>

                        <?= $totalUnit; ?> unit

                    </p>


                    <p>

                        <strong>Barang Dipinjam:</strong>

                        <?= $jumlahDipinjam; ?> unit

                    </p>


                    <p>

                        <strong>Barang Tersisa:</strong>

                        <?= $jumlahTersedia; ?> unit

                    </p>

                </div>


                <!-- TOMBOL KEMBALI -->

                <a
                    href="kamera&tripod.php"
                    class="btn btn-detail"
                >

                    ~ Kembali ~

                </a>


                <!-- TOMBOL AJUKAN -->

                <?php if ($jumlahTersedia > 0): ?>

                    <a
                        href="/ProjectPeminjaman-kamera-tripod/Transaksi/Peminjaman/peminjaman.php?id=<?= (int) $data['id_barang']; ?>&jenis=tripod"
                        class="btn btn-ajukan"
                    >

                        ~ Ajukan Peminjaman ~

                    </a>

                <?php else: ?>

                    <button
                        type="button"
                        class="btn btn-secondary"
                        disabled
                    >

                        ~ Stok Tidak Tersedia ~

                    </button>

                <?php endif; ?>

            </div>

        </div>

    </div>

</section>


<?php include 'includes/footer.php'; ?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
