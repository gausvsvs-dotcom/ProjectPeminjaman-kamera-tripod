<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}
$id = $_GET['id'];
$query = mysqli_query($conn, "
    SELECT
        barang.*,
        kategori.nama_kategori,
        merk.nama_merk,
        kondisi_barang.nama_kondisi
    FROM barang
    INNER JOIN kategori
        ON barang.id_kategori = kategori.id_kategori
    INNER JOIN merk
        ON barang.id_merk = merk.id_merk
    INNER JOIN kondisi_barang
        ON barang.id_kondisi = kondisi_barang.id_kondisi
    WHERE barang.id_barang = '$id'
");

if (!$query) {
    die("Query error: " . mysqli_error($conn));
}
$data = mysqli_fetch_assoc($query);
if (!$data) {
    die("Data barang tidak ditemukan.");
}
?>
<?php include "../layout/header.php"; ?>
<link rel="stylesheet" href="../assets/css/data-master.css">
<?php include "../layout/navbar.php"; ?>
<div class="main-content data-master-page">
    <div class="container-fluid">
        <!-- PAGE HEADER -->
        <div class="data-page-header">
            <div class="data-page-icon">
                <i class="bi bi-camera"></i>
            </div>
            <div>
                <h2>
                    Detail Barang
                </h2>
                <p>
                    Informasi detail barang pada sistem.
                </p>
            </div>
        </div>
        <!-- DETAIL CARD -->
        <div class="data-detail-card">
            <div class="data-detail-header">
                <h3>
                    <i class="bi bi-info-circle me-2"></i>
                    Informasi Barang
                </h3>
                <p>
                    Detail data barang yang dipilih.
                </p>
            </div>
            <div class="data-detail-body">
                <!-- ID BARANG -->
                <div class="detail-item">
                    <div class="detail-label">
                        ID Barang
                    </div>
                    <div class="detail-value">
                        #<?= $data['id_barang']; ?>
                    </div>
                </div>
                <!-- KODE BARANG -->
                <div class="detail-item">
                    <div class="detail-label">
                        Kode Barang
                    </div>
                    <div class="detail-value">
                        <?= htmlspecialchars($data['kode_barang']); ?>
                    </div>
                </div>
                <!-- NAMA BARANG -->
                <div class="detail-item">
                    <div class="detail-label">
                        Nama Barang
                    </div>
                    <div class="detail-value">
                        <?= htmlspecialchars($data['nama_barang']); ?>
                    </div>
                </div>
                <!-- JENIS BARANG -->
                <div class="detail-item">
                    <div class="detail-label">
                        Jenis Barang
                    </div>
                    <div class="detail-value">
                        <?= ucfirst($data['jenis_barang']); ?>
                    </div>
                </div>
                <!-- KATEGORI -->
                <div class="detail-item">
                    <div class="detail-label">
                        Kategori
                    </div>
                    <div class="detail-value">
                        <?= htmlspecialchars($data['nama_kategori']); ?>
                    </div>
                </div>
                <!-- MERK -->
                <div class="detail-item">
                    <div class="detail-label">
                        Merk
                    </div>
                    <div class="detail-value">
                        <?= htmlspecialchars($data['nama_merk']); ?>
                    </div>
                </div>
                <!-- KONDISI -->
                <div class="detail-item">
                    <div class="detail-label">
                        Kondisi
                    </div>
                    <div class="detail-value">
                        <?= htmlspecialchars($data['nama_kondisi']); ?>
                    </div>
                </div>
                <!-- STOK -->
                <div class="detail-item">
                    <div class="detail-label">
                        Stok
                    </div>
                    <div class="detail-value">
                        <?= $data['stok']; ?>
                    </div>
                </div>
                <!-- FOTO -->
                <div class="detail-item">
                    <div class="detail-label">
                        Foto
                    </div>
                    <div class="detail-value">
                        <?php if (!empty($data['foto'])) { ?>
                            <img
                                src="../uploads/barang/<?= htmlspecialchars($data['foto']); ?>"
                                class="foto-detail">
                        <?php } else { ?>
                            <span class="text-muted">
                                Tidak ada foto
                            </span>
                        <?php } ?>
                    </div>
                </div>
                <!-- DESKRIPSI -->
                <div class="detail-item">
                    <div class="detail-label">
                        Deskripsi
                    </div>
                    <div class="detail-value">
                        <?= nl2br(
                            htmlspecialchars(
                                $data['deskripsi'] ?? ''
                            )
                        ); ?>
                    </div>
                </div>
                <!-- BUTTON -->
                <div class="mt-4">
                    <a
                        href="edit.php?id=<?= $data['id_barang']; ?>"
                        class="btn-detail-edit">
                        <i class="bi bi-pencil-square me-1"></i>
                        Edit
                    </a>
                    <a
                        href="index.php"
                        class="btn-detail-back ms-1">
                        <i class="bi bi-arrow-left me-1"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "../layout/footer.php"; ?>