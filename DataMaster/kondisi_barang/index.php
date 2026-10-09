<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */
$query = mysqli_query($conn, "SELECT * FROM kondisi_barang");
?>
<?php include "../layout/header.php" ?>
<link rel="stylesheet" href="../assets/css/data-master.css">
<?php include "../layout/navbar.php" ?>
<div class="main-content">
    <div class="container">
        <div class="card">
            <div class="card-header py-3">
                <h3 class="mb-0">Data Kondisi</h3>
            </div>
            <div class="card-body">
                <a href="tambah.php" class="btn btn-tambah mb-3">
                    + Tambah Kondisi
                </a>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead>
                            <tr>
                                <th width="70">No</th>
                                <th>ID Kondisi</th>
                                <th>Nama Kondisi</th>
                                <th width="220">Aksi</th>
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
                                        <?= $data['id_kondisi']; ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($data['nama_kondisi']); ?>
                                    </td>
                                    <td>
                                        <a href="detail.php?id=<?= $data['id_kondisi']; ?>"
                                            class="btn btn-sm btn-info text-white">
                                            Detail
                                        </a>
                                        <a href="edit.php?id=<?= $data['id_kondisi']; ?>"
                                            class="btn btn-sm btn-warning">
                                            Edit
                                        </a>
                                        <a href="hapus.php?id=<?= $data['id_kondisi']; ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Yakin ingin menghapus data ini?')">
                                            Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                 <?php
                if (isset($_GET['pesan'])) {

                    if ($_GET['pesan'] == 'tambah') {
                        ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            Data kategori berhasil ditambahkan!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php
                    }

                    if ($_GET['pesan'] == 'ubah') {
                        ?>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            Data kategori berhasil diubah!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php
                    }

                    if ($_GET['pesan'] == 'hapus') {
                        ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            Data kategori berhasil dihapus!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php
                    }
                }
                ?>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "../layout/footer.php" ?>