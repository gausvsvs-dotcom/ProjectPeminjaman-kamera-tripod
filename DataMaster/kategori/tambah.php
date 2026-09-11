<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

if (isset($_POST['simpan'])) {

    $nama_kategori = $_POST['nama_kategori'];

    mysqli_query($conn, "INSERT INTO kategori (nama_kategori)
    VALUES ('$nama_kategori')");

    header("Location: index.php?pesan=tambah");
    exit;
}
?>
<?php include "../layout/header.php"; ?>
<?php include "../layout/navbar.php"; ?>
<div class="main-content">
    <div class="container">

        <div class="card">

            <div class="card-header">
                <h3 class="mb-0">Tambah Kategori</h3>
            </div>

            <div class="card-body">

                <form method="POST">

                    <div class="mb-3">
                        <label class="form-label">
                            Nama Kategori
                        </label>

                        <input
                            type="text"
                            name="nama_kategori"
                            class="form-control"
                            placeholder="Masukkan nama kategori"
                            required>
                    </div>
                    <button
                        type="submit"
                        name="simpan"
                        class="btn btn-simpan">
                        Simpan
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        Kembali
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include "../layout/footer.php"; ?>