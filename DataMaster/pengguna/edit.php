<?php
session_start();
require_once "../config/koneksi.php";
/** @var mysqli $conn */

// Pastikan pengguna sudah login
if (!isset($_SESSION['id_pengguna'])) {
    header("Location: ../login.php");
    exit;
}

// Validasi ID pengguna
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: index.php");
    exit;
}

// Ambil data pengguna
$stmt = mysqli_prepare(
    $conn,
    "SELECT id_pengguna, nama, username, role
     FROM pengguna
     WHERE id_pengguna = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$data) {
    header("Location: index.php?pesan=tidak_ditemukan");
    exit;
}

$error = "";
$nama = $data['nama'];
$username = $data['username'];
$role = $data['role'];

// Proses update data pengguna
if (isset($_POST['update'])) {

    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $role = $_POST['role'] ?? '';

    $daftar_role = ['admin', 'petugas', 'pimpinan'];

    if ($nama === '' || $username === '') {
        $error = "Nama dan username wajib diisi.";

    } elseif (strlen($nama) > 100) {
        $error = "Nama maksimal 100 karakter.";

    } elseif (strlen($username) > 50) {
        $error = "Username maksimal 50 karakter.";

    } elseif (!in_array($role, $daftar_role, true)) {
        $error = "Role yang dipilih tidak valid.";

    } else {

        // Periksa username duplikat
        $stmt_cek = mysqli_prepare(
            $conn,
            "SELECT id_pengguna
             FROM pengguna
             WHERE username = ?
             AND id_pengguna != ?"
        );

        mysqli_stmt_bind_param(
            $stmt_cek,
            "si",
            $username,
            $id
        );

        mysqli_stmt_execute($stmt_cek);
        $hasil_cek = mysqli_stmt_get_result($stmt_cek);
        $username_duplikat = mysqli_num_rows($hasil_cek) > 0;

        mysqli_stmt_close($stmt_cek);

        if ($username_duplikat) {
            $error = "Username sudah digunakan pengguna lain.";

        } else {

            // Update hanya data profil, bukan password
            $stmt_update = mysqli_prepare(
                $conn,
                "UPDATE pengguna
                 SET nama = ?, username = ?, role = ?
                 WHERE id_pengguna = ?"
            );

            mysqli_stmt_bind_param(
                $stmt_update,
                "sssi",
                $nama,
                $username,
                $role,
                $id
            );

            if (mysqli_stmt_execute($stmt_update)) {

                mysqli_stmt_close($stmt_update);

                // Perbarui session jika profil sendiri yang diedit
                if ((int) $_SESSION['id_pengguna'] === $id) {
                    $_SESSION['nama'] = $nama;
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = $role;
                }

                header("Location: index.php?pesan=ubah");
                exit;

            } else {
                $error = "Data pengguna gagal diubah.";
                mysqli_stmt_close($stmt_update);
            }
        }
    }
}
?>

<?php include "../layout/header.php"; ?>
<?php include "../layout/navbar.php"; ?>

<div class="main-content data-master-page">
    <div class="container-fluid">

        <!-- Header Halaman -->
        <div class="data-page-header">
            <div class="data-page-icon">
                <i class="bi bi-person-gear"></i>
            </div>

            <div>
                <h2>Edit Pengguna</h2>
                <p>Perbarui informasi akun pengguna.</p>
            </div>
        </div>

        <!-- Form Edit -->
        <div class="data-form-card">

            <div class="data-form-header">
                <div>
                    <h5 class="mb-1">Form Edit Pengguna</h5>
                    <p class="mb-0">
                        Ubah nama, username, dan role pengguna.
                    </p>
                </div>
            </div>

            <div class="data-form-body">

                <?php if ($error !== ''): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        <?= htmlspecialchars(
                            $error,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">

                    <!-- ID Pengguna -->
                    <div class="mb-4">
                        <label class="data-form-label">
                            ID Pengguna
                        </label>

                        <input
                            type="text"
                            class="data-form-control"
                            value="<?= (int) $data['id_pengguna']; ?>"
                            disabled
                        >
                    </div>

                    <!-- Nama -->
                    <div class="mb-4">
                        <label for="nama" class="data-form-label">
                            Nama
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            id="nama"
                            name="nama"
                            class="data-form-control"
                            value="<?= htmlspecialchars(
                                $nama,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            maxlength="100"
                            required
                        >
                    </div>

                    <!-- Username -->
                    <div class="mb-4">
                        <label for="username" class="data-form-label">
                            Username
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            id="username"
                            name="username"
                            class="data-form-control"
                            value="<?= htmlspecialchars(
                                $username,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            maxlength="50"
                            required
                        >
                    </div>

                    <!-- Role -->
                    <div class="mb-4">
                        <label for="role" class="data-form-label">
                            Role
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            id="role"
                            name="role"
                            class="data-form-control"
                            required
                        >
                            <option value="">-- Pilih Role --</option>

                            <option value="admin"
                                <?= $role === 'admin' ? 'selected' : ''; ?>>
                                Admin
                            </option>

                            <option value="petugas"
                                <?= $role === 'petugas' ? 'selected' : ''; ?>>
                                Petugas
                            </option>

                            <option value="pimpinan"
                                <?= $role === 'pimpinan' ? 'selected' : ''; ?>>
                                Pimpinan
                            </option>
                        </select>
                    </div>

                    <!-- Informasi Password -->
                    <div class="alert alert-info">
                        <i class="bi bi-shield-lock me-2"></i>
                        Password tidak diubah melalui halaman ini.
                        Untuk mengganti password, gunakan menu
                        <strong>Ganti Password</strong> pada profil sendiri.
                    </div>

                    <!-- Tombol -->
                    <div class="d-flex flex-wrap gap-2 mt-4">

                        <button
                            type="submit"
                            name="update"
                            class="btn-form-save"
                        >
                            <i class="bi bi-check-circle me-1"></i>
                            Simpan Perubahan
                        </button>

                        <a
                            href="index.php"
                            class="btn-form-cancel"
                        >
                            <i class="bi bi-arrow-left me-1"></i>
                            Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include "../layout/footer.php"; ?>