<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    echo "ID kategori tidak valid.";
    exit;
}

$stmt = mysqli_prepare($conn, "
    SELECT * FROM kategori
    WHERE id_kategori = ?
");

if (!$stmt) {
    die("Query gagal disiapkan: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$data) {
    echo "Data kategori tidak ditemukan.";
    exit;
}
?>

<?php include "../layout/header.php"; ?>
<link rel="stylesheet" href="../assets/css/data-master.css?v=3">
<?php include "../layout/navbar.php"; ?>

<div class="main-content data-master-page">
    <div class="container-fluid">

        <!-- Header Halaman -->
        <div class="data-page-header">
            <div class="data-page-icon">
                <i class="bi bi-tags"></i>
            </div>

            <div>
                <h2>Detail Kategori</h2>
                <p>Informasi lengkap kategori barang yang tersimpan.</p>
            </div>
        </div>

        <!-- Card Detail -->
        <div class="data-detail-card">

            <div class="data-detail-header">
                <h3>
                    <i class="bi bi-info-circle"></i>
                    Informasi Kategori
                </h3>
                <p>Detail data kategori barang.</p>
            </div>

            <div class="data-detail-body">

                <div class="detail-item">
                    <div class="detail-label">ID Kategori</div>
                    <div class="detail-value">
                        <?= htmlspecialchars((string) $data['id_kategori']); ?>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Nama Kategori</div>
                    <div class="detail-value">
                        <?= htmlspecialchars($data['nama_kategori'] ?? '-'); ?>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Keterangan</div>
                    <div class="detail-value">
                        <?= htmlspecialchars($data['keterangan'] ?? '-'); ?>
                    </div>
                </div>
                <!-- Tombol -->
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <a href="edit.php?id=<?= (int) $data['id_kategori']; ?>"
                       class="btn-detail-edit">
                        <i class="bi bi-pencil-square"></i>
                        Edit
                    </a>
                    <a href="index.php" class="btn-detail-back">
                        <i class="bi bi-arrow-left"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "../layout/footer.php"; ?>