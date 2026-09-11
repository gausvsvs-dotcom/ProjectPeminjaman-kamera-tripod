<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

$query = mysqli_query($conn, "SELECT * FROM merk
    WHERE id_merk = '$id'");

if (!$query) {
    die("Query error: " . mysqli_error($conn));
}

$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Data merk tidak ditemukan.");
}

if (isset($_POST['update'])) {

    $nama_merk = $_POST['nama_merk'];

    $query_update = mysqli_query($conn, "UPDATE merk SET
        nama_merk = '$nama_merk'
        WHERE id_merk = '$id'
    ");

    if ($query_update) {
        header("Location: index.php?pesan=ubah");
        exit;
    } else {
        $error = "Data merk gagal diubah: " . mysqli_error($conn);
    }
}
?>
<?php include "../layout/header.php" ?>
<?php include "../layout/navbar.php" ?>
<div class="main-content">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0">Edit Merk</h3>
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
                            value="<?= htmlspecialchars($data['nama_merk']); ?>"
                            required>
                    </div>
                    <button
                        type="submit"
                        name="update"
                        class="btn btn-update">
                        Update
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