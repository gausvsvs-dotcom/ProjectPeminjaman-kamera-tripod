<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

if (isset($_POST['simpan'])) {

    $nama_merk = $_POST['nama_merk'];

    $query = mysqli_query($conn, "INSERT INTO merk (nama_merk)
        VALUES ('$nama_merk')");

    if ($query) {
        header("Location: index.php?pesan=tambah");
        exit;
    } else {
        $error = "Data merk gagal ditambahkan: " . mysqli_error($conn);
    }
}
?>
<?php include "../layout/header.php" ?>
<?php include "../layout/navbar.php" ?>
<div class="main-content">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0">Tambah Merk</h3>
            </div>
            <div class="card-body">
                <?php if (isset($error)) { ?>
                    <div class="alert alert-danger">
                        <?= $error; ?>
                    </div>
                <?php } ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">
                            Nama Merk
                        </label>
                        <input
                            type="text"
                            name="nama_merk"
                            class="form-control"
                            placeholder="Masukkan nama merk"
                            required>
                    </div>
                    <button
                        type="submit"
                        name="simpan"
                        class="btn btn-simpan">
                        Simpan
                    </button>
                    <a href="index.php"
                        class="btn btn-secondary">
                        Kembali
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include "../layout/footer.php" ?>