
<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

// Validasi ID pengguna
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: index.php");
    exit;
}

// Ambil data pengguna tanpa menampilkan password
$stmt = mysqli_prepare(
    $conn,
    "SELECT id_user, nama_user, username, role
     FROM pengguna
     WHERE id_user = ?"
);

if (!$stmt) {
    die("Gagal menyiapkan query pengguna.");
}

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$data) {
    header("Location: index.php?pesan=tidak_ditemukan");
    exit;
}
?>

<?php include "../layout/header.php"; ?>
<link rel="stylesheet" href="../assets/css/data-master.css?v=2">
<?php include "../layout/navbar.php"; ?>

<div class="main-content data-master-page">
    <div class="container-fluid">

        <!-- HEADER HALAMAN -->
        <div class="data-page-header">
            <div class="data-page-icon">
                <i class="bi bi-person-vcard"></i>
            </div>

            <div>
                <h2>Detail Pengguna</h2>
                <p>Informasi akun dan hak akses pengguna sistem.</p>
            </div>
        </div>

        <!-- DETAIL PENGGUNA -->
        <div class="data-detail-card">

            <div class="data-detail-header">
                <h5 class="mb-0">
                    <i class="bi bi-person-lines-fill me-2"></i>
                    Informasi Pengguna
                </h5>
            </div>

            <div class="data-detail-body">

                <div class="detail-item">
                    <div class="detail-label">ID Pengguna</div>
                    <div class="detail-value">
                        #<?= (int) $data['id_user']; ?>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Nama Pengguna</div>
                    <div class="detail-value">
                        <?= htmlspecialchars(
                            $data['nama_user'],
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Username</div>
                    <div class="detail-value">
                        <?= htmlspecialchars(
                            $data['username'],
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Role</div>
                    <div class="detail-value">
                        <span class="data-id">
                            <?= htmlspecialchars(
                                ucfirst($data['role']),
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>
                        </span>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-4">
                    <a
                        href="edit.php?id=<?= (int) $data['id_user']; ?>"
                        class="btn-detail-edit"
                    >
                        <i class="bi bi-pencil-square me-1"></i>
                        Edit Pengguna
                    </a>

                    <a href="index.php" class="btn-detail-back">
                        <i class="bi bi-arrow-left me-1"></i>
                        Kembali
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>

<?php include "../layout/footer.php"; ?>
