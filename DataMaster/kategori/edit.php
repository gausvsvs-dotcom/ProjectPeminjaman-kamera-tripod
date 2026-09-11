<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

$id = $_GET['id'];

$query = mysqli_query($conn, "SELECT * FROM kategori
    WHERE id_kategori = '$id'");

$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Data kategori tidak ditemukan.";
    exit;
}

if (isset($_POST['update'])) {

    $nama_kategori = $_POST['nama_kategori'];

    mysqli_query($conn, "UPDATE kategori SET
        nama_kategori = '$nama_kategori'
        WHERE id_kategori = '$id'
    ");

    header("Location: index.php?pesan=edit");
    exit;
}
?>
<?php include "../layout/header.php"; ?>
<?php include "../layout/navbar.php"; ?>
<div class="main-content">
<div class="container">

    <div class="card">

        <div class="card-header">
            <h3 class="mb-0">Edit Kategori</h3>
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
                        value="<?= htmlspecialchars($data['nama_kategori']); ?>"
                        required
                    >

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
<?php include "../layout/footer.php"; ?>