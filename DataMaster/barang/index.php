<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

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
");
if (!$query) {
    die("Query error: " . mysqli_error($conn));
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
                <h2>Data Barang</h2>
                <p>
                    Kelola data barang kamera dan tripod pada sistem.
                </p>
            </div>
        </div>
        <!-- DATA CARD -->
        <div class="data-master-card">
            <div class="data-card-header">
                <div>
                    <h3 class="data-card-title">
                        <i class="bi bi-box-seam me-2"></i>
                        Daftar Barang
                    </h3>
                    <p>
                        Daftar barang yang tersedia dalam sistem.
                    </p>
                </div>
                <a
                    href="tambah.php"
                    class="btn-data-tambah">
                    <i class="bi bi-plus-lg me-1"></i>
                    Tambah Barang
                </a>
            </div>
            <div class="data-table-wrapper">
                <!-- NOTIFIKASI -->
                <?php
                if (isset($_GET['pesan'])) {
                    if ($_GET['pesan'] == 'tambah') {
                ?>
                        <div class="data-notification success">
                            <i class="bi bi-check-circle me-2"></i>
                            Data barang berhasil ditambahkan!
                        </div>
                <?php
                    }
                    if ($_GET['pesan'] == 'ubah') {
                ?>
                        <div class="data-notification warning">
                            <i class="bi bi-check-circle me-2"></i>
                            Data barang berhasil diubah!
                        </div>
                <?php
                    }
                    if ($_GET['pesan'] == 'hapus') {
                ?>
                        <div class="data-notification danger">
                            <i class="bi bi-check-circle me-2"></i>
                            Data barang berhasil dihapus!
                        </div>
                <?php
                    }
                }
                ?>
                <div class="table-responsive">
                    <table class="data-master-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th>Jenis</th>
                                <th>Kategori</th>
                                <th>Merk</th>
                                <th>Kondisi</th>
                                <th>Stok</th>
                                <th>Foto</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $no = 1;
                        while ($data = mysqli_fetch_assoc($query)) {
                        ?>
                            <tr>
                                <!-- NO -->
                                <td>
                                    <span class="data-number">
                                        <?= $no++; ?>
                                    </span>
                                </td>
                                <!-- KODE -->
                                <td>
                                    <span class="data-id">
                                        <?= htmlspecialchars($data['kode_barang']); ?>
                                    </span>
                                </td>
                                <!-- NAMA -->
                                <td>
                                    <span class="data-name">
                                        <?= htmlspecialchars($data['nama_barang']); ?>
                                    </span>
                                </td>
                                <!-- JENIS -->
                                <td>
                                    <?= ucfirst($data['jenis_barang']); ?>
                                </td>
                                <!-- KATEGORI -->
                                <td>
                                    <?= htmlspecialchars($data['nama_kategori']); ?>
                                </td>

                                <!-- MERK -->
                                <td>
                                    <?= htmlspecialchars($data['nama_merk']); ?>
                                </td>
                                <!-- KONDISI -->
                                <td>
                                    <?= htmlspecialchars($data['nama_kondisi']); ?>
                                </td>
                                <!-- STOK -->
                                <td>
                                    <?= $data['stok']; ?>
                                </td>
                                <!-- FOTO -->
                                <td>
                                    <?php if (!empty($data['foto'])) { ?>
                                        <img
                                            src="../uploads/barang/<?= htmlspecialchars($data['foto']); ?>"
                                            class="foto-barang"
                                            alt="Foto <?= htmlspecialchars($data['nama_barang']); ?>">
                                    <?php } else { ?>
                                        <span class="text-muted">
                                            Tidak ada foto
                                        </span>
                                    <?php } ?>
                                </td>
                                <!-- AKSI -->
                                <td class="data-action">
                                    <a
                                        href="detail.php?id=g<?= $data['id_barang']; ?>"
                                        class="data-detail">
                                        <i class="bi bi-eye"></i>
                                        Detail
                                    </a>
                                    <a
                                        href="edit.php?id=<?= $data['id_barang']; ?>"
                                        class="data-edit">
                                        <i class="bi bi-pencil-square"></i>
                                        Edit
                                    </a>
                                    <a
                                        href="hapus.php?id=<?= $data['id_barang']; ?>"
                                        class="data-delete"
                                        onclick="return confirm('Yakin ingin menghapus barang ini?')">
                                        <i class="bi bi-trash"></i>
                                        Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "../layout/footer.php"; ?>