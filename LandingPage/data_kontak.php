<?php
include 'includes/koneksi.php';

$query = "SELECT * FROM kontak ORDER BY id ASC";
$result = mysqli_query($conn, $query);

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
    <title>Data Kontak - Sistem Peminjaman Kamera & Tripod</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="assets/css/style.css?v=2">
</head>

<body>

<?php include 'includes/navbar.php'; ?>

<section class="data-kontak-section">
    <div class="container">
        
        <?php if (isset($_GET['status']) && $_GET['status'] == 'deleted') : ?>
            <div class="kontak-alert"><strong>Data berhasil dihapus!</strong>
                Data kontak telah berhasil dihapus.
            </div>
            
        <?php endif; ?>

        <div class="data-kontak-header">

            <h1 class="data-kontak-title">Data Kontak</h1>

            <p class="data-kontak-subtitle">
                Kelola pesan dan pertanyaan yang dikirim oleh pengguna.
            </p>
        </div>


        <div class="data-kontak-table table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Pesan</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php $no = 1; ?>

                <?php while ($data = mysqli_fetch_assoc($result)) { ?>

                    <tr>

                        <td class="text-center"><?= $no++; ?></td>
                        <td class="text-center"><?= $data['id']; ?></td>
                        <td><?= htmlspecialchars($data['nama']); ?></td>
                        <td><?= htmlspecialchars($data['email']); ?></td>
                        <td class="pesan-kontak"><?= htmlspecialchars($data['pesan']); ?></td>
                        <td><?= $data['created_at']; ?></td>

                        <td class="text-center">

                            <a href="edit_kontak.php?id=<?= $data['id']; ?>"
                               class="btn-edit-kontak">
                                Edit
                            </a>

                            <a href="hapus_kontak.php?id=<?= $data['id']; ?>"
                               class="btn-hapus-kontak">
                                Hapus
                            </a>
                        </td>
                    </tr>

                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
</body>
</html>