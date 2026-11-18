
<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

// Ambil ID pengguna dari URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    die("ID pengguna tidak valid.");
}

// Ambil data pengguna
$stmt = mysqli_prepare(
    $conn,
    "SELECT id_user, nama_user, username, role
     FROM pengguna
     WHERE id_user = ?"
);

if (!$stmt) {
    die("Query gagal disiapkan: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$query = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($query);

mysqli_stmt_close($stmt);

if (!$data) {
    die("Data pengguna tidak ditemukan.");
}

// Simpan data awal untuk pembanding
$data_awal = $data;

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

    // Validasi wajib diisi
    if ($nama_user === '' || $username === '' || $role === '') {
        $error = "Semua kolom wajib diisi.";
    }

    // Validasi role
    elseif (!in_array($role, ['admin', 'petugas', 'pimpinan'], true)) {
        $error = "Role yang dipilih tidak valid.";
    }

    // Cek username duplikat
    if ($error === "") {

        $stmt = mysqli_prepare(
            $conn,
            "SELECT id_user
             FROM pengguna
             WHERE username = ? AND id_user != ?"
        );

        if (!$stmt) {
            $error = "Query gagal disiapkan: " . mysqli_error($conn);
        } else {

            mysqli_stmt_bind_param($stmt, "si", $username, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            $duplikat = mysqli_stmt_num_rows($stmt) > 0;

            mysqli_stmt_close($stmt);

            if ($duplikat) {
                $error = "Username sudah digunakan.";
            }
        }
    }

    // Cek apakah ada perubahan
    if ($error === "") {

        $ada_perubahan =
            $nama_user !== (string) $data_awal['nama_user'] ||
            $username !== (string) $data_awal['username'] ||
            $role !== (string) $data_awal['role'];

        if (!$ada_perubahan) {
            $error = "Tidak ada perubahan data. Silakan ubah minimal satu informasi sebelum menyimpan.";
        }
    }

    // Update jika tidak ada error dan ada perubahan
    if ($error === "") {

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE pengguna
             SET nama_user = ?, username = ?, role = ?
             WHERE id_user = ?"
        );

        if (!$stmt) {
            $error = "Query gagal disiapkan: " . mysqli_error($conn);
        } else {

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
                $error = "Gagal mengubah data: "
                    . mysqli_stmt_error($stmt);

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
