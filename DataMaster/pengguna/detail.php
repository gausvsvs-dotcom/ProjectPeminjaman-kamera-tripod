<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

$id = $_GET['id'];

$query = mysqli_query($conn, "SELECT * FROM pengguna
    WHERE id_pengguna = '$id'");

$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Data pengguna tidak ditemukan.";
    exit;
}
?>
<?php include "../layout/header.php"; ?>
<?php include "../layout/navbar.php"; ?>

<div class="main-content">
    <div class="container">
        <div class="card">

            <div class="card-header">
                <h3 class="mb-0">Detail Pengguna</h3>
            </div>

            <div class="card-body">

                <div class="mb-3">
                    <div class="detail-label">
                        ID Pengguna
                    </div>

                    <div>
                        <?= $data['id_pengguna']; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="detail-label">
                        Nama
                    </div>

                    <div>
                        <?= htmlspecialchars($data['nama']); ?>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="detail-label">
                        Username
                    </div>

                    <div>
                        <?= htmlspecialchars($data['username']); ?>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="detail-label">
                        Password
                    </div>

                    <div>
                        ••••••••
                    </div>
                </div>

                <div class="mb-3">
                    <div class="detail-label">
                        Role
                    </div>

                    <div>
                        <span class="badge" style="background-color: #A888B5;">
                            <?= htmlspecialchars($data['role']); ?>
                        </span>
                    </div>
                </div>

                <a href="edit.php?id=<?= $data['id_pengguna']; ?>"
                    class="btn btn-warning">
                    Edit
                </a>

                <a href="index.php"
                    class="btn btn-secondary">
                    Kembali
                </a>

            </div>

        </div>

    </div>
</div>
<?php include "../layout/footer.php"; ?>