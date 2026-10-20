<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

// Ambil ID pengguna dari URL
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    die("ID pengguna tidak valid.");
}

// Ambil data pengguna
$query = mysqli_query(
    $conn,
    "SELECT id_user, nama_user, username, role
     FROM pengguna
     WHERE id_user = $id"
);

if (!$query) {
    die("Query error: " . mysqli_error($conn));
}

$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Data pengguna tidak ditemukan.");
}

$error = "";

// Isi awal form
$nama_user = $data['nama_user'];
$username = $data['username'];
$role = $data['role'];

// Proses simpan perubahan
if (isset($_POST['update'])) {
    $nama_user = trim($_POST['nama_user'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $role = $_POST['role'] ?? '';

    if ($nama_user === '' || $username === '' || $role === '') {
        $error = "Semua kolom wajib diisi.";
    } else {
        // Cek apakah username sudah dipakai pengguna lain
        $stmt = mysqli_prepare(
            $conn,
            "SELECT id_user
             FROM pengguna
             WHERE username = ? AND id_user != ?"
        );

        mysqli_stmt_bind_param($stmt, "si", $username, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        $duplikat = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);

        if ($duplikat) {
            $error = "Username sudah digunakan.";
        } else {
            // Update data pengguna tanpa mengubah password
            $stmt = mysqli_prepare(
                $conn,
                "UPDATE pengguna
                 SET nama_user = ?, username = ?, role = ?
                 WHERE id_user = ?"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "sssi",
                $nama_user,
                $username,
                $role,
                $id
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);

                header("Location: index.php?pesan=ubah");
                exit;
            } else {
                $error = "Gagal mengubah data: " . mysqli_error($conn);
                mysqli_stmt_close($stmt);
            }
        }
    }
}
?>

<?php include "../layout/header.php"; ?>
<link rel="stylesheet" href="../assets/css/data-master.css?v=2">
<?php include "../layout/navbar.php"; ?>

<div class="main-content data-master-page">
    <div class="container-fluid">

        <div class="data-page-header">
            <div class="data-page-icon">
                <i class="bi bi-person-gear"></i>
            </div>
            <div>
                <h2>Edit Pengguna</h2>
                <p>Ubah informasi akun pengguna.</p>
            </div>
        </div>

        <div class="data-form-card">
            <div class="data-form-header">
                <div>
                    <h5 class="mb-1">Form Edit Pengguna</h5>
                    <p class="mb-0">Silakan ubah data di bawah ini.</p>
                </div>
            </div>

            <div class="data-form-body">

                <?php if ($error !== ''): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-4">
                        <label class="data-form-label">ID Pengguna</label>
                        <input
                            type="text"
                            class="data-form-control"
                            value="<?= (int) $data['id_user']; ?>"
                            disabled
                        >
                    </div>

                    <div class="mb-4">
                        <label for="nama_user" class="data-form-label">
                            Nama Pengguna
                        </label>
                        <input
                            type="text"
                            id="nama_user"
                            name="nama_user"
                            class="data-form-control"
                            value="<?= htmlspecialchars($nama_user, ENT_QUOTES, 'UTF-8'); ?>"
                            required
                        >
                    </div>

                    <div class="mb-4">
                        <label for="username" class="data-form-label">
                            Username
                        </label>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            class="data-form-control"
                            value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>"
                            required
                        >
                    </div>

                    <div class="mb-4">
                        <label for="role" class="data-form-label">
                            Role
                        </label>
                        <select
                            id="role"
                            name="role"
                            class="data-form-control"
                            required
                        >
                            <option value="">-- Pilih Role --</option>
                            <?php foreach (['admin', 'petugas', 'pimpinan'] as $pilihan): ?>
                                <option
                                    value="<?= $pilihan; ?>"
                                    <?= $role === $pilihan ? 'selected' : ''; ?>
                                >
                                    <?= ucfirst($pilihan); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="alert alert-info">
                        Password tidak diubah melalui form ini.
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <button
                            type="submit"
                            name="update"
                            class="btn-form-save"
                        >
                            <i class="bi bi-check-circle me-1"></i>
                            Simpan Perubahan
                        </button>

                        <a href="index.php" class="btn-form-cancel">
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
