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
<?php include "../layout/header.php" ?>
<?php include "../layout/navbar.php" ?>

<div class="main-content">
<div class="container">
    <div class="card">
        <div class="card-header py-3">
            <h3 class="mb-0">Data Barang</h3>
        </div>
        <div class="card-body">
            <?php
            if (isset($_GET['pesan'])) {

                if ($_GET['pesan'] == 'tambah') {
                    ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        Data barang berhasil ditambahkan!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php
                }

                if ($_GET['pesan'] == 'ubah') {
                    ?>
                    <div class="alert alert-warning alert-dismissible fade show">
                        Data barang berhasil diubah!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php
                }

                if ($_GET['pesan'] == 'hapus') {
                    ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        Data barang berhasil dihapus!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php
                }
            }
            ?>
            <a href="tambah.php" class="btn btn-tambah mb-3">
                + Tambah Barang
            </a>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
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
                            <td><?= $no++; ?></td>
                            <td>
                                <?= htmlspecialchars($data['kode_barang']); ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($data['nama_barang']); ?>
                            </td>
                            <td>
                                <?= ucfirst($data['jenis_barang']); ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($data['nama_kategori']); ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($data['nama_merk']); ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($data['nama_kondisi']); ?>
                            </td>
                            <td>
                                <?= $data['stok']; ?>
                            </td>
                            <td>
                                <?php if (!empty($data['foto'])) { ?>
                                    <img
                                        src="../uploads/barang/<?= htmlspecialchars($data['foto']); ?>"
                                        class="foto-barang"
                                    >
                                <?php } else { ?>
                                    <span class="text-muted">
                                        Tidak ada foto
                                    </span>
                                <?php } ?>
                            </td>
                            <td>
                                <a href="detail.php?id=<?= $data['id_barang']; ?>"
                                   class="btn btn-info btn-sm text-white">
                                    Detail
                                </a>
                                <a href="edit.php?id=<?= $data['id_barang']; ?>"
                                   class="btn btn-warning btn-sm">
                                    Edit
                                </a>
                                <a href="hapus.php?id=<?= $data['id_barang']; ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Yakin ingin menghapus barang ini?')">
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php include "../layout/footer.php" ?>