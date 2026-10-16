
<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

// Ambil data pengguna tanpa mengambil kolom password
$query = mysqli_query(
    $conn,
    "SELECT id_pengguna, nama, username, role
     FROM pengguna
     ORDER BY id_pengguna DESC"
);

if (!$query) {
    die("Query error: " . mysqli_error($conn));
}

// Hitung jumlah pengguna
$total_pengguna = mysqli_num_rows($query);

// Daftar notifikasi
$notifikasi = [
    'tambah' => [
        'class' => 'success',
        'pesan' => 'Data pengguna berhasil ditambahkan!'
    ],
    'ubah' => [
        'class' => 'warning',
        'pesan' => 'Data pengguna berhasil diubah!'
    ],
    'hapus' => [
        'class' => 'danger',
        'pesan' => 'Data pengguna berhasil dihapus!'
    ]
];
?>

<?php include "../layout/header.php"; ?>
<link rel="stylesheet" href="../assets/css/data-master.css">
<?php include "../layout/navbar.php"; ?>

<div class="main-content">
    <div class="container-fluid data-master-page">

        <!-- Header halaman -->
        <div class="data-page-header">
            <div class="data-page-icon">
                <i class="bi bi-people-fill"></i>
            </div>

            <div>
                <h2>Data Pengguna</h2>
                <p>Kelola data akun dan hak akses pengguna sistem.</p>
            </div>
        </div>

        <!-- Notifikasi -->
        <?php
        if (isset($_GET['pesan']) && isset($notifikasi[$_GET['pesan']])) {
            $pesan = $notifikasi[$_GET['pesan']];
        ?>
            <div class="data-notification <?= htmlspecialchars($pesan['class']); ?>">
                <i class="bi bi-check-circle-fill"></i>
                <span><?= htmlspecialchars($pesan['pesan']); ?></span>
            </div>
        <?php } ?>

        <!-- Tabel pengguna -->
        <div class="data-master-card">

            <div class="data-card-header">
                <div>
                    <h5 class="data-card-title">Daftar Pengguna</h5>
                    <p>Jumlah seluruh pengguna: <?= $total_pengguna; ?> akun</p>
                </div>

                <a href="tambah.php" class="btn-data-tambah">
                    <i class="bi bi-plus-lg"></i>
                    Tambah Pengguna
                </a>
            </div>

            <div class="data-table-wrapper">
                <table class="data-master-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Pengguna</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if ($total_pengguna > 0) { ?>
                            <?php
                            $no = 1;

                            while ($data = mysqli_fetch_assoc($query)) {
                                $id = (int) $data['id_pengguna'];
                            ?>
                                <tr>
                                    <td>
                                        <span class="data-number">
                                            <?= $no++; ?>
                                        </span>
                                    </td>

                                    <td>
                                        <span class="data-id">
                                            <?= $id; ?>
                                        </span>
                                    </td>

                                    <td>
                                        <span class="data-name">
                                            <?= htmlspecialchars(
                                                $data['nama'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $data['username'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>
                                    </td>

                                    <td>
                                        <span class="data-id">
                                            <?= htmlspecialchars(
                                                $data['role'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <div class="data-action">
                                            <a
                                                href="detail.php?id=<?= $id; ?>"
                                                class="data-detail"
                                            >
                                                <i class="bi bi-eye"></i>
                                                Detail
                                            </a>

                                            <a
                                                href="edit.php?id=<?= $id; ?>"
                                                class="data-edit"
                                            >
                                                <i class="bi bi-pencil-square"></i>
                                                Edit
                                            </a>

                                            <a
                                                href="hapus.php?id=<?= $id; ?>"
                                                class="data-delete"
                                                onclick="return confirm('Yakin ingin menghapus pengguna ini?')"
                                            >
                                                <i class="bi bi-trash"></i>
                                                Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>

                        <?php } else { ?>
                            <tr>
                                <td colspan="6" class="data-empty">
                                    <i class="bi bi-people"></i>
                                    <p>Belum ada data pengguna.</p>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="data-table-footer">
                Menampilkan <?= $total_pengguna; ?> pengguna
            </div>

        </div>
    </div>
</div>

<?php include "../layout/footer.php"; ?>