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
<?php include "../layout/header.php" ?>
<?php include "../layout/navbar.php" ?>
<div class="main-content">
    <div class="container">
        <div class="card">
            <div class="card-header py-3">
                <h3 class="mb-0">
                    Detail Barang
                </h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="detail-label">
                        ID Barang
                    </div>
                    <div>
                        <?= $data['id_barang']; ?>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="detail-label">
                        Kode Barang
                    </div>
                    <div>
                        <?= htmlspecialchars($data['kode_barang']); ?>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="detail-label">
                        Nama Barang
                    </div>
                    <div>
                        <?= htmlspecialchars($data['nama_barang']); ?>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="detail-label">
                        Jenis Barang
                    </div>
                    <div>
                        <?= ucfirst($data['jenis_barang']); ?>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="detail-label">
                        Kategori
                    </div>
                    <div>
                        <?= htmlspecialchars($data['nama_kategori']); ?>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="detail-label">
                        Merk
                    </div>
                    <div>
                        <?= htmlspecialchars($data['nama_merk']); ?>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="detail-label">
                        Kondisi
                    </div>
                    <div>
                        <?= htmlspecialchars($data['nama_kondisi']); ?>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="detail-label">
                        Stok
                    </div>
                    <div>
                        <?= $data['stok']; ?>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="detail-label">
                        Foto
                    </div>
                    <div class="mt-2">
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
                <div class="mb-3">
                    <div class="detail-label">
                        Deskripsi
                    </div>
                    <div>
                        <?= nl2br(htmlspecialchars($data['deskripsi'] ?? '')); ?>
                    </div>
                </div>
                <a
                    href="edit.php?id=<?= $data['id_barang']; ?>"
                    class="btn btn-warning">
                    Edit
                </a>
                <a
                    href="index.php"
                    class="btn btn-secondary">
                    Kembali
                </a>
            </div>
        </div>
    </div>
</div>
<?php include "../layout/footer.php" ?>