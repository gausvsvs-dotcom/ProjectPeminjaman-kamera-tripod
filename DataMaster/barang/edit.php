<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

$error = "";
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: index.php");
    exit;
}
/* Ambil data barang */
$stmt = mysqli_prepare($conn, "
    SELECT * FROM barang
    WHERE id_barang = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$data) {
    die("Data barang tidak ditemukan.");
}
/* Ambil data master */
$kategori = mysqli_query($conn, "SELECT * FROM kategori");
$merk = mysqli_query($conn, "SELECT * FROM merk");
$kondisi = mysqli_query($conn, "SELECT * FROM kondisi_barang");
if (!$kategori || !$merk || !$kondisi) {
    die("Gagal mengambil data master: " . mysqli_error($conn));
}
/* Proses update */
if (isset($_POST['update'])) {
    $kode_barang = trim($_POST['kode_barang'] ?? "");
    $nama_barang = trim($_POST['nama_barang'] ?? "");
    $jenis_barang = $_POST['jenis_barang'] ?? "";
    $id_kategori = filter_input(INPUT_POST, 'id_kategori', FILTER_VALIDATE_INT);
    $id_merk = filter_input(INPUT_POST, 'id_merk', FILTER_VALIDATE_INT);
    $id_kondisi = filter_input(INPUT_POST, 'id_kondisi', FILTER_VALIDATE_INT);
    $stok = filter_input(INPUT_POST, 'stok', FILTER_VALIDATE_INT);
    $deskripsi = trim($_POST['deskripsi'] ?? "");

    $foto_lama = $data['foto'];
    $foto = $foto_lama;
    $foto_baru = "";
    /* Validasi data */
    if (
        $kode_barang === "" ||
        $nama_barang === "" ||
        !in_array($jenis_barang, ['kamera', 'tripod'], true) ||
        !$id_kategori ||
        !$id_merk ||
        !$id_kondisi ||
        $stok === false ||
        $stok === null ||
        $stok < 0) {
        $error = "Periksa kembali data yang diisi. Semua kolom wajib diisi dengan benar.";
    }
    /* Proses upload foto baru */
    if ($error === "" && !empty($_FILES['foto']['name'])) {
        if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            $error = "Foto gagal diunggah. Silakan coba kembali.";
        } else {
            $tmp_foto = $_FILES['foto']['tmp_name'];
            $ukuran_foto = $_FILES['foto']['size'];
            $info_foto = getimagesize($tmp_foto);
            $ekstensi = strtolower(
                pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION)
            );
            $ekstensi_diizinkan = ['jpg', 'jpeg', 'png', 'webp'];
            if (
                $info_foto === false ||
                !in_array($ekstensi, $ekstensi_diizinkan, true)
            ) {
                $error = "Format foto harus JPG, JPEG, PNG, atau WEBP.";
            } elseif ($ukuran_foto > 5 * 1024 * 1024) {
                $error = "Ukuran foto maksimal 5 MB.";
            } else {
                $folder = "../uploads/barang/";
                if (!is_dir($folder) && !mkdir($folder, 0777, true)) {
                    $error = "Folder upload foto gagal dibuat.";
                } else {
                    $nama_file = bin2hex(random_bytes(8)) . "." . $ekstensi;
                    $lokasi_foto = $folder . $nama_file;
                    if (move_uploaded_file($tmp_foto, $lokasi_foto)) {
                        $foto = $nama_file;
                        $foto_baru = $lokasi_foto;
                    } else {
                        $error = "Foto gagal disimpan.";
                    }
                }
            }
        }
    }
    /* Simpan perubahan */
    if ($error === "") {
        $stmt_update = mysqli_prepare($conn, "
            UPDATE barang SET
                kode_barang = ?,
                nama_barang = ?,
                jenis_barang = ?,
                id_kategori = ?,
                id_merk = ?,
                id_kondisi = ?,
                stok = ?,
                foto = ?,
                deskripsi = ?
            WHERE id_barang = ?
        ");
        if ($stmt_update) {
            mysqli_stmt_bind_param(
                $stmt_update,
                "sssiiiissi",
                $kode_barang,
                $nama_barang,
                $jenis_barang,
                $id_kategori,
                $id_merk,
                $id_kondisi,
                $stok,
                $foto,
                $deskripsi,
                $id
            );
            if (mysqli_stmt_execute($stmt_update)) {
                mysqli_stmt_close($stmt_update);
                /* Hapus foto lama setelah update berhasil */
                if (
                    $foto_baru !== "" &&
                    !empty($foto_lama) &&
                    is_file("../uploads/barang/" . $foto_lama)
                ) {
                    unlink("../uploads/barang/" . $foto_lama);
                }
                header("Location: index.php?pesan=ubah");
                exit;
            } else {
                $error = "Data barang gagal diubah: " .
                    mysqli_stmt_error($stmt_update);

                mysqli_stmt_close($stmt_update);
                /* Hapus foto baru jika update gagal */
                if ($foto_baru !== "" && is_file($foto_baru)) {
                    unlink($foto_baru);
                }
            }
        } else {
            $error = "Query update gagal disiapkan: " .
                mysqli_error($conn);

            if ($foto_baru !== "" && is_file($foto_baru)) {
                unlink($foto_baru);
            }
        }
    }
}
?>
<?php include "../layout/header.php"; ?>
<link rel="stylesheet" href="../assets/css/data-master.css?v=3">
<?php include "../layout/navbar.php"; ?>
<div class="main-content data-master-page">
    <div class="container-fluid">
        <!-- HEADER HALAMAN -->
        <div class="data-page-header">
            <div class="data-page-icon">
                <i class="bi bi-camera"></i>
            </div>
            <div>
                <h2>Edit Barang</h2>
                <p>Perbarui informasi barang kamera dan tripod.</p>
            </div>
        </div>
        <!-- FORM EDIT -->
        <div class="data-form-card">
            <div class="data-form-header">
                <h3>
                    <i class="bi bi-pencil-square"></i>
                    Form Edit Barang
                </h3>
                <p>Ubah informasi barang sesuai data terbaru.</p>
            </div>
            <div class="data-form-body">
                <?php if ($error !== "") { ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        <?= htmlspecialchars($error); ?>
                    </div>
                <?php } ?>
                <form method="POST" enctype="multipart/form-data">
                    <!-- KODE BARANG -->
                    <div class="mb-3">
                        <label class="data-form-label">Kode Barang</label>
                        <input
                            type="text"
                            name="kode_barang"
                            class="data-form-control"
                            value="<?= htmlspecialchars($data['kode_barang'] ?? ''); ?>"
                            required>
                    </div>
                    <!-- NAMA BARANG -->
                    <div class="mb-3">
                        <label class="data-form-label">Nama Barang</label>
                        <input
                            type="text"
                            name="nama_barang"
                            class="data-form-control"
                            value="<?= htmlspecialchars($data['nama_barang'] ?? ''); ?>"
                            required>
                    </div>
                    <!-- JENIS BARANG -->
                    <div class="mb-3">
                        <label class="data-form-label">Jenis Barang</label>
                        <select name="jenis_barang" class="data-form-control" required>
                            <option value="">-- Pilih Jenis Barang --</option>
                            <option value="kamera"
                                <?= ($data['jenis_barang'] ?? '') === 'kamera' ? 'selected' : ''; ?>>
                                Kamera
                            </option>
                            <option value="tripod"
                                <?= ($data['jenis_barang'] ?? '') === 'tripod' ? 'selected' : ''; ?>>
                                Tripod
                            </option>
                        </select>
                    </div>
                    <!-- KATEGORI -->
                    <div class="mb-3">
                        <label class="data-form-label">Kategori</label>
                        <select name="id_kategori" class="data-form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php while ($item = mysqli_fetch_assoc($kategori)) { ?>
                                <option
                                    value="<?= (int) $item['id_kategori']; ?>"
                                    <?= (int) $data['id_kategori'] === (int) $item['id_kategori'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($item['nama_kategori']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <!-- MERK -->
                    <div class="mb-3">
                        <label class="data-form-label">Merk</label>
                        <select name="id_merk" class="data-form-control" required>
                            <option value="">-- Pilih Merk --</option>
                            <?php while ($item = mysqli_fetch_assoc($merk)) { ?>
                                <option
                                    value="<?= (int) $item['id_merk']; ?>"
                                    <?= (int) $data['id_merk'] === (int) $item['id_merk'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($item['nama_merk']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <!-- KONDISI -->
                    <div class="mb-3">
                        <label class="data-form-label">Kondisi Barang</label>
                        <select name="id_kondisi" class="data-form-control" required>
                            <option value="">-- Pilih Kondisi --</option>
                            <?php while ($item = mysqli_fetch_assoc($kondisi)) { ?>
                                <option
                                    value="<?= (int) $item['id_kondisi']; ?>"
                                    <?= (int) $data['id_kondisi'] === (int) $item['id_kondisi'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($item['nama_kondisi']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <!-- STOK -->
                    <div class="mb-3">
                        <label class="data-form-label">Stok</label>
                        <input
                            type="number"
                            name="stok"
                            class="data-form-control"
                            min="0"
                            value="<?= htmlspecialchars((string) $data['stok']); ?>"
                            required>
                    </div>
                    <!-- FOTO -->
                    <div class="mb-3">
                        <label class="data-form-label">Foto Barang</label>
                        <input
                            type="file"
                            name="foto"
                            class="data-form-control"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                        <small class="text-muted d-block mt-2">
                            Format JPG, JPEG, PNG, atau WEBP. Maksimal 5 MB.
                            Kosongkan jika tidak ingin mengganti foto.
                        </small>
                        <?php if (!empty($data['foto'])) { ?>
                            <div class="mt-3">
                                <label class="data-form-label">Foto Saat Ini</label>
                                <div>
                                    <img
                                        src="../uploads/barang/<?= htmlspecialchars($data['foto']); ?>"
                                        alt="Foto <?= htmlspecialchars($data['nama_barang']); ?>"
                                        class="foto-detail"
                                        style="max-width: 220px; max-height: 220px;">
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <!-- DESKRIPSI -->
                    <div class="mb-4">
                        <label class="data-form-label">Deskripsi</label>
                        <textarea
                            name="deskripsi"
                            class="data-form-control"
                            rows="4"><?= htmlspecialchars($data['deskripsi'] ?? ''); ?></textarea>
                    </div>
                    <!-- TOMBOL -->
                    <div class="d-flex flex-wrap gap-2">
                        <button
                            type="submit"
                            name="update"
                            class="btn-form-save">
                            <i class="bi bi-save"></i>
                            Simpan Perubahan
                        </button>
                        <a href="index.php" class="btn-form-cancel">
                            <i class="bi bi-arrow-left"></i>
                            Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include "../layout/footer.php"; ?>