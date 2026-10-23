<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    header("Location: index.php");
    exit;
}
$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM merk WHERE id_merk = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$query = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($query);
mysqli_stmt_close($stmt);
if (!$data) {
    header("Location: index.php?pesan=tidak_ditemukan");
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
                <h2>Detail Merk</h2>
                <p>Informasi lengkap merk barang yang terdaftar.</p>
            </div>
        </div>
        <!-- Card Detail -->
        <div class="data-detail-card">
            <div class="data-detail-header">
                <div>
                    <h5 class="mb-1">Informasi Merk</h5>
                    <p class="mb-0">
                        Detail data merk barang
                    </p>
                </div>
            </div>
            <div class="data-detail-body">
                <!-- ID Merk -->
                <div class="detail-item">
                    <div class="detail-label">ID Merk</div>
                    <div class="detail-value">
                        <?= (int) $data['id_merk']; ?>
                    </div>
                </div>
                <!-- Nama Merk -->
                <div class="detail-item">
                    <div class="detail-label">Nama Merk</div>
                    <div class="detail-value">
                        <?= htmlspecialchars(
                            $data['nama_merk'],
                            ENT_QUOTES,
                            'UTF-8' ); ?>
                    </div>
                </div>
                <!-- Keterangan Merk -->
                <div class="detail-item">
                    <div class="detail-label">Keterangan</div>
                    <div class="detail-value">
                        <?= htmlspecialchars(
                            $data['keterangan'],
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>
                    </div>
                </div>
                <!-- Tombol Aksi -->
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <a
                        href="edit.php?id=<?= (int) $data['id_merk']; ?>"
                        class="btn-detail-edit">
                        <i class="bi bi-pencil-square me-1"></i>
                        Edit Data
                    </a>
                    <a href="index.php" class="btn-detail-back">
                        <i class="bi bi-arrow-left me-1"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "../layout/footer.php"; ?>