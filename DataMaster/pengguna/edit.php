<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */
$id = (int) $_GET['id'];

// Ambil data pengguna
$query = mysqli_query($conn, "SELECT * FROM pengguna
    WHERE id_pengguna = $id");

$data = mysqli_fetch_assoc($query);
if (!$data) {
    echo "Data pengguna tidak ditemukan.";
    exit;
}

// PROSES UPDATE
if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Jika password baru diisi
    if ($password !== "") {
        $password_hash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );
        $query_update = mysqli_query($conn, "UPDATE pengguna SET
            nama = '$nama',
            username = '$username',
            password = '$password_hash',
            role = '$role'
            WHERE id_pengguna = $id
        ");
    } else {
        // Password kosong → password lama tidak diubah
        $query_update = mysqli_query($conn, "UPDATE pengguna SET
            nama = '$nama',
            username = '$username',
            role = '$role'
            WHERE id_pengguna = $id
        ");
    }
    if ($query_update) {
        header("Location: index.php?pesan=ubah");
        exit;
    } else {
        $error = "Data pengguna gagal diubah: "
            . mysqli_error($conn);
    }
}
?>

<?php include "../layout/header.php"; ?>
<?php include "../layout/navbar.php"; ?>

<div class="main-content">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3 class="mb-0">
                    Edit Pengguna
                </h3>
            </div>
            <div class="card-body">
                <?php if (isset($error)) { ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error); ?>
                    </div>
                <?php } ?>
                <form method="POST">
                    <!-- NAMA -->
                    <div class="mb-3">
                        <label class="form-label">
                            Nama
                        </label>
                        <input
                            type="text"
                            name="nama"
                            class="form-control"
                            value="<?= htmlspecialchars($data['nama']); ?>"
                            required>
                    </div>
                    <!-- USERNAME -->
                    <div class="mb-3">
                        <label class="form-label">
                            Username
                        </label>
                        <input
                            type="text"
                            name="username"
                            class="form-control"
                            value="<?= htmlspecialchars($data['username']); ?>"
                            required>
                    </div>
                    <!-- PASSWORD -->
                    <div class="mb-3">
                        <label class="form-label">
                            Password
                        </label>
                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            placeholder="Kosongkan jika tidak ingin mengubah password">
                        <small class="text-muted">
                            Kosongkan jika password tidak ingin diubah.
                        </small>
                    </div>
                    <!-- ROLE -->
                    <div class="mb-3">
                        <label class="form-label">
                            Role
                        </label>
                        <select
                            name="role"
                            class="form-select"
                            required>
                            <option value="">
                                -- Pilih Role --
                            </option>
                            <option
                                value="admin"
                                <?= $data['role'] == 'admin' ? 'selected' : ''; ?>>
                                Admin
                            </option>
                            <option
                                value="petugas"
                                <?= $data['role'] == 'petugas' ? 'selected' : ''; ?>>
                                Petugas
                            </option>
                            <option
                                value="pimpinan"
                                <?= $data['role'] == 'pimpinan' ? 'selected' : ''; ?>>
                                Pimpinan
                            </option>
                        </select>
                    </div>
                    <!-- BUTTON -->
                    <button
                        type="submit"
                        name="update"
                        class="btn btn-update">
                        Update
                    </button>

                    <a
                        href="index.php"
                        class="btn btn-secondary">
                        Kembali
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include "../layout/footer.php"; ?>