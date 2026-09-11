<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

if (isset($_POST['simpan'])) {

    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $query = mysqli_query($conn, "INSERT INTO pengguna
        (nama, username, password, role)
        VALUES
        ('$nama', '$username', '$password_hash', '$role')
    ");

    if ($query) {
        header("Location: index.php?pesan=tambah");
        exit;
    } else {
        $error = "Data pengguna gagal ditambahkan: " . mysqli_error($conn);
    }
}
?>
<?php include "../layout/header.php" ?>
<?php include "../layout/navbar.php" ?>
<div class="main-content">
<div class="container">

    <div class="card">

        <div class="card-header">
            <h3 class="mb-0">Tambah Pengguna</h3>
        </div>

        <div class="card-body">

            <?php if (isset($error)) { ?>
                <div class="alert alert-danger">
                    <?= $error; ?>
                </div>
            <?php } ?>

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label">Nama</label>

                    <input
                        type="text"
                        name="nama"
                        class="form-control"
                        placeholder="Masukkan nama"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Username</label>

                    <input
                        type="text"
                        name="username"
                        class="form-control"
                        placeholder="Masukkan username"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>

                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="Masukkan password"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>

                    <select name="role" class="form-select" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="admin">Admin</option>
                        <option value="petugas">Petugas</option>
                        <option value="pimpinan">Pimpinan</option>
                    </select>
                </div>

                <button
                    type="submit"
                    name="simpan"
                    class="btn btn-simpan">
                    Simpan
                </button>

                <a href="index.php" class="btn btn-secondary">
                    Kembali
                </a>

            </form>

        </div>

    </div>

</div>
</div>
<?php include "../layout/footer.php" ?>