<?php
session_start();
require_once "../config/koneksi.php";
/** @var mysqli $conn */

// Pastikan pengguna sudah login
if (!isset($_SESSION['id_pengguna'])) {
    header("Location: ../login.php");
    exit;
}

// Ambil ID akun dari session
$id = (int) $_SESSION['id_pengguna'];

// Ambil data profil akun yang sedang login
$stmt = mysqli_prepare(
    $conn,
    "SELECT id_pengguna, nama, username, role, foto
     FROM pengguna
     WHERE id_pengguna = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);

mysqli_stmt_close($stmt);

if (!$data) {
    echo "Data pengguna tidak ditemukan.";
    exit;
}
?>
<?php include "../layout/header.php"; ?>
<link rel="stylesheet" href="../assets/css/data-master.css">
<?php include "../layout/navbar.php"; ?>
<div class="main-content data-master-page">
    <div class="container-fluid">
        <!-- PAGE HEADER -->
        <div class="data-page-header">
            <div class="data-page-icon">
                <i class="bi bi-person-circle"></i>
            </div>
            <div>
                <h2>Profile Saya</h2>
                <p>
                    Informasi akun pengguna yang sedang login.
                </p>
            </div>
        </div>
        <!-- PROFILE CARD -->
        <div class="data-detail-card">
            <div class="data-detail-header">
                <h3>
                    <i class="bi bi-person-vcard me-2"></i>
                    Informasi Profile
                </h3>
                <p>
                    Detail informasi akun pengguna.
                </p>
            </div>
            <div class="data-detail-body">
                <!-- FOTO -->
                <div class="profile-detail-photo">
                    <?php if (!empty($data['foto'])) { ?>
                        <img
                            src="../uploads/pengguna/<?= htmlspecialchars($data['foto']); ?>"
                            alt="Foto Profile">
                    <?php } else { ?>
                        <div class="profile-detail-placeholder">
                            <i class="bi bi-person"></i>
                        </div>
                    <?php } ?>
                </div>
                <!-- NAMA -->
                <div class="detail-item">
                    <div class="detail-label">
                        Nama
                    </div>
                    <div class="detail-value">
                        <?= htmlspecialchars($data['nama']); ?>
                    </div>
                </div>
                <!-- USERNAME -->
                <div class="detail-item">
                    <div class="detail-label">
                        Username
                    </div>
                    <div class="detail-value">
                        <?= htmlspecialchars($data['username']); ?>
                    </div>
                </div>
                <!-- ROLE -->
                <div class="detail-item">
                    <div class="detail-label">
                        Role
                    </div>
                    <div class="detail-value">
                        <?= htmlspecialchars($data['role']); ?>
                    </div>
                </div>

                <!-- BUTTON -->
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <a
                        href="edit.php?id=<?= (int) $data['id_pengguna']; ?>"
                        class="btn-detail-edit">
                        <i class="bi bi-pencil-square me-1"></i>
                        Edit Profile
                    </a>
                    <a
                        href="ganti_password.php"
                        class="btn-detail-back">
                        <i class="bi bi-shield-lock me-1"></i>
                        Ganti Password
                    </a>
                    <a
                        href="../dashboard/index.php"
                        class="btn-detail-back">
                        <i class="bi bi-arrow-left me-1"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "../layout/footer.php"; ?>