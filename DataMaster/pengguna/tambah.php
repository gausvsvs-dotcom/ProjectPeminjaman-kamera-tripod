<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

$error = "";

if (isset($_POST['simpan'])) {

    $nama_user = trim($_POST['nama_user'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    $daftar_role = ['admin', 'petugas', 'pimpinan'];

    // Validasi input
    if ($nama_user === '' || $username === '' || $password === '') {
        $error = "Semua kolom wajib diisi.";

    } elseif (strlen($nama_user) > 100) {
        $error = "Nama maksimal 100 karakter.";

    } elseif (strlen($username) > 50) {
        $error = "Username maksimal 50 karakter.";

    } elseif (strlen($password) < 8) {
        $error = "Password minimal 8 karakter.";

    } elseif (!in_array($role, $daftar_role, true)) {
        $error = "Role yang dipilih tidak valid.";

    } else {

        // Periksa apakah username sudah digunakan
        $stmt_cek = mysqli_prepare(
            $conn,
            "SELECT id_user FROM pengguna WHERE username = ?"
        );

        mysqli_stmt_bind_param($stmt_cek, "s", $username);
        mysqli_stmt_execute($stmt_cek);

        $hasil_cek = mysqli_stmt_get_result($stmt_cek);
        $username_duplikat = mysqli_num_rows($hasil_cek) > 0;

        mysqli_stmt_close($stmt_cek);

        if ($username_duplikat) {
            $error = "Username sudah digunakan pengguna lain.";

        } else {

            // Hash password sebelum disimpan
            $password_hash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // Simpan data pengguna
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO pengguna (nama_user, username, password, role)
                 VALUES (?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "ssss",
                $nama_user,
                $username,
                $password_hash,
                $role
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);

                header("Location: index.php?pesan=tambah");
                exit;

            } else {
                $error = "Data pengguna gagal ditambahkan.";
                mysqli_stmt_close($stmt);
            }
        }
    }
}
?>

<?php include "../layout/header.php"; ?>
<link rel="stylesheet" href="../assets/css/data-master.css">
<?php include "../layout/navbar.php"; ?>

<div class="main-content data-master-page">
    <div class="container-fluid">

        <!-- Header halaman -->
        <div class="data-page-header">
            <div class="data-page-icon">
                <i class="bi bi-person-plus-fill"></i>
            </div>

            <div>
                <h2>Tambah Pengguna</h2>
                <p>Tambahkan akun pengguna baru ke dalam sistem.</p>
            </div>
        </div>

        <!-- Form tambah pengguna -->
        <div class="data-form-card">

            <div class="data-form-header">
                <div>
                    <h5 class="mb-1">Form Tambah Pengguna</h5>
                    <p class="mb-0">
                        Lengkapi informasi akun pengguna berikut.
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

                <form method="POST">

                    <!-- Nama pengguna -->
                    <div class="mb-4">
                        <label for="nama_user" class="data-form-label">
                            Nama <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            id="nama_user"
                            name="nama_user"
                            class="data-form-control"
                            placeholder="Masukkan nama pengguna"
                            maxlength="100"
                            value="<?= htmlspecialchars(
                                $_POST['nama_user'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            required
                        >
                    </div>

                    <!-- Username -->
                    <div class="mb-4">
                        <label for="username" class="data-form-label">
                            Username <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            id="username"
                            name="username"
                            class="data-form-control"
                            placeholder="Masukkan username"
                            maxlength="50"
                            value="<?= htmlspecialchars(
                                $_POST['username'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            autocomplete="username"
                            required
                        >
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="data-form-label">
                            Password <span class="text-danger">*</span>
                        </label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="data-form-control"
                            placeholder="Minimal 8 karakter"
                            minlength="8"
                            autocomplete="new-password"
                            required
                        >

                        <small class="text-muted">
                            Password akan disimpan dalam bentuk hash.
                        </small>
                    </div>

                    <!-- Role -->
                    <div class="mb-4">
                        <label for="role" class="data-form-label">
                            Role <span class="text-danger">*</span>
                        </label>

                        <select
                            id="role"
                            name="role"
                            class="data-form-control"
                            required
                        >
                            <option value="">-- Pilih Role --</option>

                            <option value="admin"
                                <?= ($_POST['role'] ?? '') === 'admin'
                                    ? 'selected' : ''; ?>>
                                Admin
                            </option>

                            <option value="petugas"
                                <?= ($_POST['role'] ?? '') === 'petugas'
                                    ? 'selected' : ''; ?>>
                                Petugas
                            </option>

                            <option value="pimpinan"
                                <?= ($_POST['role'] ?? '') === 'pimpinan'
                                    ? 'selected' : ''; ?>>
                                Pimpinan
                            </option>
                        </select>
                    </div>

                    <!-- Tombol -->
                    <div class="d-flex flex-wrap gap-2 mt-4">

                        <button
                            type="submit"
                            name="simpan"
                            class="btn-form-save">
                            <i class="bi bi-check-circle me-1"></i>
                            Simpan Pengguna
                        </button>

                        <a
                            href="index.php"
                            class="btn-form-cancel">
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
