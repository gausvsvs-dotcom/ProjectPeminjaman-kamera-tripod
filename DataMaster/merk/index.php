<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */
// Ambil data merk
$query = mysqli_query(
    $conn,
    "SELECT * FROM merk ORDER BY id_merk ASC"
);
if (!$query) {
    die("Query error: " . mysqli_error($conn));
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
                <h2>Data Merk</h2>
                <p>Kelola daftar merk kamera dan tripod yang terdaftar.</p>
            </div>
        </div>
        <!-- Notifikasi -->
        <?php if (isset($_GET['pesan'])): ?>
            <?php if ($_GET['pesan'] === 'tambah'): ?>
                <div class="data-notification success" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    Data merk berhasil ditambahkan!
                </div>
            <?php elseif ($_GET['pesan'] === 'ubah'): ?>
                <div class="data-notification success" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    Data merk berhasil diubah!
                </div>
            <?php elseif ($_GET['pesan'] === 'hapus'): ?>
                <div class="data-notification success" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    Data merk berhasil dihapus!
                </div>
            <?php elseif ($_GET['pesan'] === 'tidak_ditemukan'): ?>
                <div class="data-notification warning" role="alert">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    Data merk tidak ditemukan.
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <!-- Card Tabel -->
        <div class="data-master-card">
            <div class="data-card-header">
                <div>
                    <h5 class="data-card-title">Daftar Merk</h5>
                    <p class="mb-0 text-muted">
                        Daftar merk yang digunakan pada data barang.
                    </p>
                </div>
                <a href="tambah.php" class="btn-data-tambah">
                    <i class="bi bi-plus-lg"></i>
                    Tambah Merk
                </a>
            </div>
            <!-- Tabel -->
            <div class="data-table-wrapper">
                <div class="table-responsive">
                    <table class="data-master-table">
                        <thead>
                            <tr>
                                <th width="65">No.</th>
                                <th>Nama Merk</th>
                                <th>Keterangan</th>
                                <th width="180">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($query) > 0): ?>
                                <?php
                                $no = 1;
                                while ($data = mysqli_fetch_assoc($query)):
                                    $id_merk = (int) $data['id_merk'];
                                ?>
                                    <tr>
                                        <td class="data-number">
                                            <?= $no++; ?>
                                        </td>

                                        <td>
                                            <span class="data-name">
                                                <?= htmlspecialchars(
                                                    $data['nama_merk'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="data-description">
                                                <?= htmlspecialchars(
                                                    $data['keterangan'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="data-action">
                                                <!-- Detail -->
                                                <a
                                                    href="detail.php?id=<?= $id_merk; ?>"
                                                    class="data-detail"
                                                    title="Detail Merk"
                                                    aria-label="Detail Merk">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <!-- Edit -->
                                                <a
                                                    href="edit.php?id=<?= $id_merk; ?>"
                                                    class="data-edit"
                                                    title="Edit Merk"
                                                    aria-label="Edit Merk">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <!-- Hapus -->
                                                <a
                                                    href="hapus.php?id=<?= $id_merk; ?>"
                                                    class="data-delete"
                                                    title="Hapus Merk"
                                                    aria-label="Hapus Merk"
                                                    onclick="return confirm('Yakin ingin menghapus merk ini?')">
                                                    <i class="bi bi-trash3"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">
                                        <div class="data-empty">
                                            <i class="bi bi-tags"></i>
                                            <h6>Belum Ada Data Merk</h6>
                                            <p>
                                                Silakan tambahkan data merk terlebih dahulu.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Footer Tabel -->
            <div class="data-table-footer">
                Menampilkan
                <strong><?= mysqli_num_rows($query); ?></strong>
                data merk
            </div>
        </div>
    </div>
</div>
<?php include "../layout/footer.php"; ?>