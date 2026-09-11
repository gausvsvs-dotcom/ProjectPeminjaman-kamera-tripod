<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

$id = $_GET['id'];

$query = mysqli_query($conn, "SELECT * FROM kondisi_barang
    WHERE id_kondisi = '$id'");

$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Data kondisi tidak ditemukan.";
    exit;
}
?>
<?php include "../layout/header.php" ?>
<?php include "../layout/navbar.php" ?>
<div class="main-content">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0">Detail Kondisi</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="detail-label">ID Kondisi</div>
                    <div>
                        <?= $data['id_kondisi']; ?>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="detail-label">Nama Kondisi</div>
                    <div>
                        <?= htmlspecialchars($data['nama_kondisi']); ?>
                    </div>
                </div>
                <a href="edit.php?id=<?= $data['id_kondisi']; ?>"
                    class="btn btn-warning">
                    Edit
                </a>
                <a href="index.php"
                    class="btn btn-secondary">
                    Kembali
                </a>
            </div>
        </div>
    </div>
</div>
<?php include "../layout/footer.php" ?>