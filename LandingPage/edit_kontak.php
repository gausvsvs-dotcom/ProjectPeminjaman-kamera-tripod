<?php
include 'includes/koneksi.php';

$id = $_GET['id'];

$query = "SELECT * FROM kontak WHERE id = '$id'";
$result = mysqli_query($conn, $query);

$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "Data kontak tidak ditemukan.";
    exit;
}

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Data Kontak</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<?php include 'includes/navbar.php'; ?>

<div class="container py-5">
    <h2 class="mb-4">Edit Data Kontak</h2>
    <form action="proses_edit_kontak.php" method="POST">

        <input type="hidden"
               name="id"
               value="<?= $data['id']; ?>">

        <div class="mb-3">
            <label class="form-label">Nama</label>

            <input type="text"
                   name="nama"
                   class="form-control"
                   value="<?= $data['nama']; ?>"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>

            <input type="email"
                   name="email"
                   class="form-control"
                   value="<?= $data['email']; ?>"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Pesan</label>

            <textarea name="pesan"
                      class="form-control"
                      rows="5"
                      required><?= $data['pesan']; ?></textarea>
        </div>

        <button type="submit"
                class="btn btn-primary">
            Edit
        </button>

        <a href="data_kontak.php"
           class="btn btn-secondary">
            Batal
        </a>
    </form>
</div>
</body>
</html>