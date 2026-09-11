<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

$query = mysqli_query($conn, "SELECT * FROM kategori");
?>

<?php include "../layout/header.php"; ?>

<?php include "../layout/navbar.php"; ?>

<div class="main-content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header py-3">
                <h3 class="mb-0">
                    Data Kategori
                </h3>
            </div>
            <div class="card-body">
                <a href="tambah.php"
                    class="btn btn-primary-custom mb-3">
                    <i class="bi bi-plus-circle"></i>
                    Tambah Kategori
                </a>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">

                        <thead>

                            <tr>

                                <th width="70">
                                    No
                                </th>

                                <th>
                                    ID Kategori
                                </th>

                                <th>
                                    Nama Kategori
                                </th>

                                <th width="220">
                                    Aksi
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php
                            $no = 1;

                            while ($data = mysqli_fetch_assoc($query)) {
                            ?>

                                <tr>

                                    <td>
                                        <?= $no++; ?>
                                    </td>

                                    <td>
                                        <?= $data['id_kategori']; ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($data['nama_kategori']); ?>
                                    </td>
                                    <td>
                                        <a href="detail.php?id=<?= $data['id_kategori']; ?>"
                                            class="btn btn-sm btn-info text-white">
                                            <i class="bi bi-eye"></i>
                                            Detail
                                        </a>
                                        <a href="edit.php?id=<?= $data['id_kategori']; ?>"
                                            class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil-square"></i>
                                            Edit
                                        </a>
                                        <a href="hapus.php?id=<?= $data['id_kategori']; ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Yakin ingin menghapus data ini?')">
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
                <!-- Notifikasi -->
                <?php
                if (isset($_GET['pesan'])) {
                    if ($_GET['pesan'] == 'tambah') {
                ?>
                        <div class="alert alert-success alert-dismissible fade show"
                            role="alert">
                            <i class="bi bi-check-circle-fill"></i>
                            Data kategori berhasil ditambahkan!
                            <button type="button"
                                class="btn-close"
                                data-bs-dismiss="alert">
                            </button>
                        </div>
                    <?php
                    }
                    if ($_GET['pesan'] == 'ubah') {
                    ?>
                        <div class="alert alert-warning alert-dismissible fade show"
                            role="alert">
                            <i class="bi bi-check-circle-fill"></i>
                            Data kategori berhasil diubah!
                            <button type="button"
                                class="btn-close"
                                data-bs-dismiss="alert">
                            </button>
                        </div>
                    <?php
                    }

                    if ($_GET['pesan'] == 'hapus') {
                    ?>
                        <div class="alert alert-danger alert-dismissible fade show"
                            role="alert">
                            <i class="bi bi-check-circle-fill"></i>
                            Data kategori berhasil dihapus!
                            <button type="button"
                                class="btn-close"
                                data-bs-dismiss="alert">
                            </button>
                        </div>
                <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>
<?php include "../layout/footer.php"; ?>