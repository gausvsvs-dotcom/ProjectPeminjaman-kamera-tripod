<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

$query = mysqli_query($conn, "SELECT * FROM merk");

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
                <h3 class="mb-0">Data Merk</h3>
            </div>
            <div class="card-body">
                <?php
                if (isset($_GET['pesan'])) {

                    if ($_GET['pesan'] == 'tambah') {
                ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            Data merk berhasil ditambahkan!
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php
                    }
                    if ($_GET['pesan'] == 'ubah') {
                    ?>
                        <div class="alert alert-warning alert-dismissible fade show">
                            Data merk berhasil diubah!
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php
                    }
                    if ($_GET['pesan'] == 'hapus') {
                    ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            Data merk berhasil dihapus!
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                <?php
                    }
                }
                ?>
                <a href="tambah.php" class="btn btn-tambah mb-3">
                    + Tambah Merk
                </a>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead>
                            <tr>
                                <th width="70">No</th>
                                <th>ID Merk</th>
                                <th>Nama Merk</th>
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
                                    <td><?= $data['id_merk']; ?></td>
                                    <td>
                                        <?= htmlspecialchars($data['nama_merk']); ?>
                                    </td>
                                    <td>
                                        <a href="detail.php?id=<?= $data['id_merk']; ?>"
                                            class="btn btn-info btn-sm text-white">
                                            Detail
                                        </a>
                                        <a href="edit.php?id=<?= $data['id_merk']; ?>"
                                            class="btn btn-warning btn-sm">
                                            Edit
                                        </a>
                                        <a href="hapus.php?id=<?= $data['id_merk']; ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Yakin ingin menghapus merk ini?')">
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php include "../layout/footer.php" ?>