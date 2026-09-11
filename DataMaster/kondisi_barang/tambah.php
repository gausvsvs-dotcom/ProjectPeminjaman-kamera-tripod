<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

if (isset($_POST['simpan'])) {

    $nama_kondisi = $_POST['nama_kondisi'];

    mysqli_query($conn, "INSERT INTO kondisi_barang (nama_kondisi)
    VALUES ('$nama_kondisi')");

    header("Location: index.php");
    exit;
}
?>
<?php include "../layout/header.php" ?>
<?php include "../layout/navbar.php" ?>
<div class="main-content">
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3 class="mb-0">Tambah Kondisi</h3>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">
                        Nama Kondisi
                    </label>
                    <input
                        type="text"
                        name="nama_kategori"
                        class="form-control"
                        placeholder="Masukkan nama kondisi"
                        required
                    >
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
<?php include "../layout/footer.php" ?>