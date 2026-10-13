<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */
// Validasi ID kondisi
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id <= 0) {
    header("Location: index.php");
    exit;
}
// Ambil data kondisi
$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM kondisi_barang WHERE id_kondisi = ?"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$query = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($query);
mysqli_stmt_close($stmt);
if (!$data) {
    echo "Data kondisi barang tidak ditemukan.";
    exit;
}
$nama_kondisi = $data['nama_kondisi'];
$error = "";
// Proses update
if (isset($_POST['update'])) {
    $nama_kondisi = trim($_POST['nama_kondisi'] ?? "");
    // Validasi wajib diisi
    if ($nama_kondisi === "") {
        $error = "Nama kondisi wajib diisi.";
    }
    // Validasi panjang nama
    elseif (strlen($nama_kondisi) > 50) {
        $error = "Nama kondisi maksimal 50 karakter.";
    }
    // Validasi duplikat, kecuali data yang sedang diedit
    else {
        $stmt = mysqli_prepare(
            $conn,
            "SELECT id_kondisi
             FROM kondisi_barang
             WHERE LOWER(nama_kondisi) = LOWER(?)
             AND id_kondisi != ?"
        );
        mysqli_stmt_bind_param($stmt, "si", $nama_kondisi, $id);
        mysqli_stmt_execute($stmt);
        $hasil = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($hasil) > 0) {
            $error = "Nama kondisi tersebut sudah digunakan.";
        }
        mysqli_stmt_close($stmt);
    }
    // Simpan jika tidak ada error
    if ($error === "") {
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE kondisi_barang
             SET nama_kondisi = ?
             WHERE id_kondisi = ?"
        );
        mysqli_stmt_bind_param($stmt, "si", $nama_kondisi, $id);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: index.php?pesan=ubah");
            exit;
        } else {
            $error = "Data kondisi gagal diperbarui.";
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<?php include "../layout/header.php"; ?>
<link rel="stylesheet" href="../assets/css/data-master.css?v=3">
<?php include "../layout/navbar.php"; ?>
<div class="main-content data-master-page">
    <div class="container-fluid">
        <!-- PAGE HEADER -->
        <div class="data-page-header">

            <div class="data-page-icon">
                <i class="bi bi-clipboard2-pulse"></i>
            </div>
            <div>
                <h2>Edit Kondisi Barang</h2>
                <p>
                    Perbarui informasi kondisi barang pada sistem.
                </p>
            </div>
        </div>
        <!-- FORM CARD -->
        <div class="data-form-card">
            <div class="data-form-header">
                <h3>
                    <i class="bi bi-pencil-square me-2"></i>
                    Form Edit Kondisi
                </h3>
                <p>
                    Ubah nama kondisi barang sesuai kebutuhan.
                </p>
            </div>
            <div class="data-form-body">
                <!-- PESAN ERROR -->
                <?php if ($error !== "") { ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        <?= htmlspecialchars($error); ?>
                    </div>
                <?php } ?>
                <form method="POST">
                    <!-- ID KONDISI -->
                    <div class="mb-3">
                        <label class="data-form-label">
                            ID Kondisi
                        </label>
                        <input
                            type="text"
                            class="data-form-control"
                            value="<?= (int) $data['id_kondisi']; ?>"
                            disabled>
                    </div>
                    <!-- NAMA KONDISI -->
                    <div class="mb-4">
                        <label for="nama_kondisi" class="data-form-label">
                            Nama Kondisi
                            <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            id="nama_kondisi"
                            name="nama_kondisi"
                            class="data-form-control"
                            placeholder="Contoh: Tersedia"
                            maxlength="50"
                            value="<?= htmlspecialchars($nama_kondisi); ?>"
                            required>
                        <small class="text-muted">
                            Masukkan nama kondisi barang, misalnya
                            Tersedia, Dipinjam, atau Perawatan.
                        </small>
                    </div>
                    <!-- KATEGORI -->
                    <div class="mb-4">
                        <label for="kategori" class="data-form-label">
                            Kategori
                            <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            id="kategori"
                            name="kategori"
                            class="data-form-control"
                            placeholder="Contoh: Kamera"
                            maxlength="50"
                            value="<?= htmlspecialchars($kategori); ?>"
                            required>
                        <small class="text-muted">
                            Masukkan kategori barang, misalnya
                            Kamera atau Tripod.
                        </small>
                    </div>
                    <!-- BUTTON -->
                    <div class="d-flex gap-2 flex-wrap">
                        <button
                            type="submit"
                            name="update"
                            class="btn-form-save">
                            <i class="bi bi-check-circle me-1"></i>
                            Simpan Perubahan
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
