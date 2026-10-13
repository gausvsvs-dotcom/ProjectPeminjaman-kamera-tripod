<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

// Ambil seluruh data kondisi barang
$query = mysqli_query($conn, "
    SELECT *
    FROM kondisi_barang
    ORDER BY id_kondisi ASC");

if (!$query) {
    die("Query error: " . mysqli_error($conn));
}
// Hitung jumlah data
$total_data = mysqli_num_rows($query);
?>
<?php include "../layout/header.php"; ?>
<link rel="stylesheet" href="../assets/css/data-master.css?v=3">
<?php include "../layout/navbar.php"; ?>
<div class="main-content data-master-page">
    <div class="container-fluid">
        <!-- NOTIFIKASI -->
        <?php if (isset($_GET['pesan'])) { ?>
            <?php if ($_GET['pesan'] === 'tambah') { ?>
                <div class="data-notification success">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    Data kondisi barang berhasil ditambahkan!
                </div>
            <?php } ?>
            <?php if ($_GET['pesan'] === 'ubah') { ?>
                <div class="data-notification warning">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    Data kondisi barang berhasil diubah!
                </div>
            <?php } ?>
            <?php if ($_GET['pesan'] === 'hapus') { ?>
                <div class="data-notification danger">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    Data kondisi barang berhasil dihapus!
                </div>
            <?php } ?>
        <?php } ?>
        <!-- PAGE HEADER -->
        <div class="data-page-header">
            <div class="data-page-icon">
                <i class="bi bi-clipboard2-pulse"></i>
            </div>
            <div>
                <h2>Data Kondisi Barang</h2>
                <p>
                    Kelola informasi kondisi kamera dan tripod
                    yang tersedia dalam sistem peminjaman.
                </p>
            </div>
        </div>
        <!-- DATA CARD -->
        <div class="data-master-card">
            <!-- CARD HEADER -->
            <div class="data-card-header">

                <div>
                    <h3 class="data-card-title">
                        <i class="bi bi-list-check me-2"></i>
                        Daftar Kondisi Barang
                    </h3>
                    <p class="mb-0 text-muted">
                        Total <?= $total_data; ?> data kondisi barang
                    </p>
                </div>
                <a href="tambah.php" class="btn-data-tambah">
                    <i class="bi bi-plus-lg me-1"></i>
                    Tambah Kondisi
                </a>
            </div>
            <!-- TABLE -->
            <div class="data-table-wrapper">
                <table class="data-master-table">
                    <thead>
                        <tr>
                            <th width="70">No</th>
                            <th>ID Kondisi</th>
                            <th>Nama Kondisi</th>
                            <th>Kategori</th>
                            <th width="180">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total_data > 0) { ?>
                            <?php
                            $no = 1;
                            while ($data = mysqli_fetch_assoc($query)) {
                            ?>
                                <tr>
                                    <!-- NOMOR -->
                                    <td class="data-number">
                                        <?= $no++; ?>
                                    </td>
                                    <!-- ID KONDISI -->
                                    <td>
                                        <span class="data-id">
                                            #<?= (int) $data['id_kondisi']; ?>
                                        </span>
                                    </td>
                                    <!-- NAMA KONDISI -->
                                    <td>
                                        <span class="data-name">
                                            <?= htmlspecialchars($data['nama_kondisi']); ?>
                                        </span>
                                    </td>
                                    <!-- KATEGORI --> 
                                    <td>
                                        <span class="data-category">
                                            <?= htmlspecialchars($data['kategori']); ?>
                                        </span>
                                    </td>                                       
                                    <!-- AKSI -->
                                    <td>
                                        <div class="data-action">
                                            <a
                                                href="detail.php?id=<?= (int) $data['id_kondisi']; ?>"
                                                class="data-detail"
                                                title="Detail kondisi"
                                                aria-label="Detail kondisi">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a
                                                href="edit.php?id=<?= (int) $data['id_kondisi']; ?>"
                                                class="data-edit"
                                                title="Edit kondisi"
                                                aria-label="Edit kondisi">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <a
                                                href="hapus.php?id=<?= (int) $data['id_kondisi']; ?>"
                                                class="data-delete"
                                                title="Hapus kondisi"
                                                aria-label="Hapus kondisi"
                                                onclick="return confirm('Yakin ingin menghapus data kondisi barang ini?')">
                                                <i class="bi bi-trash3"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="5">
                                    <div class="data-empty">
                                        <i class="bi bi-clipboard2-x"></i>
                                        <h5>Belum Ada Data Kondisi</h5>
                                        <p>
                                            Silakan tambahkan data kondisi
                                            barang terlebih dahulu.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <!-- CARD FOOTER -->
            <div class="data-table-footer">
                <span>
                    Menampilkan <?= $total_data; ?> data kondisi barang
                </span>
            </div>
        </div>
    </div>
</div>
<?php include "../layout/footer.php"; ?>
