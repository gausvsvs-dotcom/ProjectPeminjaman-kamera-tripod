<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

$id = $_GET['id'];

$query = mysqli_query($conn, "SELECT * FROM kondisi_barang
    WHERE id_kondisi = '$id'");

$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Data kondisi tidak ditemukan.";
    exit;
}

if (isset($_POST['update'])) {

    $nama_kondisi = $_POST['nama_kondisi'];

    mysqli_query($conn, "UPDATE kondisi_barang SET
        nama_kondisi = '$nama_kondisi'
        WHERE id_kondisi = '$id'
    ");

    header("Location: index.php?pesan");
    exit;
}
?>
<?php include "../layout/header.php" ?>
<?php include "../layout/navbar.php" ?>
<div class="main-content">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0">Edit Kondisi</h3>
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
                            value="<?= htmlspecialchars($data['nama_kondisi']); ?>"
                            required>
                    </div>
                    <button
                        type="submit"
                        name="update"
                        class="btn btn-update">
                        Update
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