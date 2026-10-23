<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

// Ambil data pengguna tanpa menampilkan password
$query = mysqli_query(
    $conn,
    "SELECT id_user, nama_user, username, role
     FROM pengguna
     ORDER BY id_user DESC"
);

if (!$query) {
    die("Query error: " . mysqli_error($conn));
}

$data_pengguna = mysqli_fetch_all($query, MYSQLI_ASSOC);
$total_pengguna = count($data_pengguna);

// Notifikasi
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

$pesan = $_GET['pesan'] ?? '';
?>

<?php include "../layout/header.php"; ?>
<link rel="stylesheet" href="../assets/css/data-master.css?v=2">
<?php include "../layout/navbar.php"; ?>
<div class="main-content data-master-page">
    <div class="container-fluid">
        <!-- HEADER HALAMAN -->
        <div class="data-page-header">
            <div class="data-page-icon">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <h2>Data Pengguna</h2>
                <p>Kelola data akun dan hak akses pengguna sistem.</p>
            </div>
        </div>
        <!-- CARD DATA PENGGUNA -->
        <div class="data-master-card">
            <!-- HEADER CARD -->
            <div class="data-card-header">
                <div>
                    <h3 class="data-card-title">
                        <i class="bi bi-list-ul me-2"></i>
                        Daftar Pengguna
                    </h3>
                    <p class="mb-0">
                        Jumlah seluruh pengguna:
                        <strong><?= $total_pengguna; ?></strong> akun
                    </p>
                </div>
                <a href="tambah.php" class="btn-data-tambah">
                    <i class="bi bi-plus-circle me-1"></i>
                    Tambah Pengguna
                </a>
            </div>
            <!-- ISI CARD -->
            <div class="data-card-body">
                <!-- NOTIFIKASI -->
                <?php if (isset($notifikasi[$pesan])): ?>
                    <?php $notif = $notifikasi[$pesan]; ?>
                    <div class="data-notification">
                        <div
                            class="alert alert-<?= htmlspecialchars($notif['class'], ENT_QUOTES, 'UTF-8'); ?> alert-dismissible fade show"
                            role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <?= htmlspecialchars(
                                $notif['pesan'],
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="alert"
                                aria-label="Tutup"
                            ></button>
                        </div>
                    </div>
                <?php endif; ?>
                <!-- TABEL PENGGUNA -->
                <div class="data-table-wrapper">
                    <table class="data-master-table">
                        <thead>
                            <tr>
                                <th width="65">No.</th>
                                <th>Nama Pengguna</th>
                                <th>Username</th>
                                <th width="200">Role</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($total_pengguna > 0): ?>
                                <?php foreach ($data_pengguna as $index => $data): ?>
                                    <?php $id = (int) $data['id_user']; ?>
                                    <tr>
                                        <!-- NOMOR -->
                                        <td>
                                            <span class="data-number">
                                                <?= $index + 1; ?>
                                            </span>
                                        </td>
                                        <!-- NAMA -->
                                        <td>
                                            <span class="data-name">
                                                <?= htmlspecialchars(
                                                    $data['nama_user'] ?? '',
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ); ?>
                                            </span>
                                        </td>

                                        <!-- USERNAME -->
                                        <td>
                                            <?= htmlspecialchars(
                                                $data['username'] ?? '',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>
                                        </td>

                                        <!-- ROLE -->
                                        <td>
                                            <span class="data-id">
                                                <?= htmlspecialchars(
                                                    $data['role'] ?? '',
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ); ?>
                                            </span>
                                        </td>

                                        <!-- AKSI -->
                                        <td>
                                            <div class="data-action">

                                                <a
                                                    href="detail.php?id=<?= (int) $data['id_user']; ?>"
                                                    class="data-detail"
                                                    title="Detail pengguna"
                                                    aria-label="Detail pengguna">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a
                                                    href="edit.php?id=<?= (int) $data['id_user']; ?>"
                                                    class="data-edit"
                                                    title="Edit pengguna"
                                                    aria-label="Edit pengguna">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <a
                                                    href="hapus.php?id=<?= (int) $data['id_user']; ?>"
                                                    class="data-delete"
                                                    title="Hapus pengguna"
                                                    aria-label="Hapus pengguna"
                                                    onclick="return confirm('Yakin ingin menghapus pengguna ini?');">
                                                    <i class="bi bi-trash"></i>
                                                </a>

                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="data-empty">
                                        <i class="bi bi-people"></i>
                                        <p>Belum ada data pengguna.</p>
                                        <span>
                                            Silakan tambahkan pengguna baru.
                                        </span>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>

                    </table>
                </div>

                <!-- TOTAL DATA -->
                <div class="data-table-footer">
                    Menampilkan
                    <strong><?= $total_pengguna; ?></strong>
                    pengguna
                </div>

            </div>
        </div>

    </div>
</div>

<?php include "../layout/footer.php"; ?>
