<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once __DIR__ . '/../config/koneksi.php';
/** @var mysqli $conn */

// Pastikan pengguna sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../../login.php");
    exit;
}

// Ambil ID hanya dari session pengguna yang login
$id_user = (int) $_SESSION['id_user'];

$error = "";
$success = "";

// Proses ganti password
if (isset($_POST['ganti_password'])) {

    $password_lama = $_POST['password_lama'] ?? '';
    $password_baru = $_POST['password_baru'] ?? '';
    $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';

    if (
        $password_lama === '' ||
        $password_baru === '' ||
        $konfirmasi_password === ''
    ) {
        $error = "Semua kolom password wajib diisi.";

    } elseif (strlen($password_baru) < 8) {
        $error = "Password baru minimal 8 karakter.";

    } elseif ($password_baru !== $konfirmasi_password) {
        $error = "Konfirmasi password baru tidak cocok.";

    } elseif ($password_lama === $password_baru) {
        $error = "Password baru harus berbeda dari password lama.";

    } else {

        // Ambil password akun yang sedang login
        $stmt = mysqli_prepare(
            $conn,
            "SELECT password
             FROM pengguna
             WHERE id_user = ?"
        );

        mysqli_stmt_bind_param($stmt, "i", $id_user);
        mysqli_stmt_execute($stmt);

        $hasil = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($hasil);

        mysqli_stmt_close($stmt);

        if (!$data) {
            $error = "Data akun tidak ditemukan.";

        } elseif (!password_verify($password_lama, $data['password'])) {
            $error = "Password lama yang kamu masukkan salah.";

        } else {

            // Buat hash password baru
            $hash_password = password_hash(
                $password_baru,
                PASSWORD_DEFAULT
            );

            // Update password hanya untuk akun yang sedang login
            $stmt_update = mysqli_prepare(
                $conn,
                "UPDATE pengguna
                 SET password = ?
                 WHERE id_user = ?"
            );

            mysqli_stmt_bind_param(
                $stmt_update,
                "si",
                $hash_password,
                $id_user
            );

            if (mysqli_stmt_execute($stmt_update)) {
                $success = "Password berhasil diubah.";
            } else {
                $error = "Password gagal diubah. Silakan coba lagi.";
            }

            mysqli_stmt_close($stmt_update);
        }
    }
}
?>

<?php include __DIR__ . '/../../layout/header.php'; ?>
<link rel="stylesheet" href="../../assets/css/data-master.css">
<?php include __DIR__ . '/../../layout/navbar.php'; ?>

<div class="main-content data-master-page">
    <div class="container-fluid">

        <!-- Header -->
        <div class="data-page-header">
            <div class="data-page-icon">
                <i class="bi bi-shield-lock"></i>
            </div>

            <div>
                <h2>Ganti Password</h2>
                <p>
                    Perbarui password untuk melindungi akunmu.
                </p>
            </div>
        </div>

        <!-- Form -->
        <div class="data-form-card">

            <div class="data-form-header">
                <div>
                    <h5 class="mb-1">Keamanan Akun</h5>
                    <p class="mb-0">
                        Masukkan password lama dan password baru.
                    </p>
                </div>
            </div>

            <div class="data-form-body">

                <?php if ($error !== '') { ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        <?= htmlspecialchars(
                            $error,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>
                    </div>
                <?php } ?>

                <?php if ($success !== '') { ?>
                    <div class="alert alert-success" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        <?= htmlspecialchars(
                            $success,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>
                    </div>
                <?php } ?>

                <form method="POST">

                    <div class="mb-4">
                        <label for="password_lama" class="data-form-label">
                            Password Lama
                        </label>

                        <input
                            type="password"
                            id="password_lama"
                            name="password_lama"
                            class="data-form-control"
                            placeholder="Masukkan password lama"
                            autocomplete="current-password"
                            required
                        >
                    </div>

                    <div class="mb-4">
                        <label for="password_baru" class="data-form-label">
                            Password Baru
                        </label>

                        <input
                            type="password"
                            id="password_baru"
                            name="password_baru"
                            class="data-form-control"
                            placeholder="Minimal 8 karakter"
                            minlength="8"
                            autocomplete="new-password"
                            required
                        >

                        <small class="text-muted">
                            Gunakan password yang sulit ditebak.
                        </small>
                    </div>

                    <div class="mb-4">
                        <label
                            for="konfirmasi_password"
                            class="data-form-label"
                        >
                            Konfirmasi Password Baru
                        </label>

                        <input
                            type="password"
                            id="konfirmasi_password"
                            name="konfirmasi_password"
                            class="data-form-control"
                            placeholder="Ulangi password baru"
                            minlength="8"
                            autocomplete="new-password"
                            required
                        >
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <button
                            type="submit"
                            name="ganti_password"
                            class="btn-form-save"
                        >
                            <i class="bi bi-shield-check me-1"></i>
                            Ganti Password
                        </button>

                        <a
                            href="profile.php"
                            class="btn-form-cancel"
                        >
                            <i class="bi bi-arrow-left me-1"></i>
                            Kembali ke Profil
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../layout/footer.php'; ?>
