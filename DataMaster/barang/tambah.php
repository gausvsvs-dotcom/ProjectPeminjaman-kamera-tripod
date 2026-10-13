<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once "../config/koneksi.php";
/** @var mysqli $conn */
// Mengambil data untuk dropdown
$kategori = mysqli_query($conn, "SELECT * FROM kategori");
$merk = mysqli_query($conn, "SELECT * FROM merk");
$kondisi = mysqli_query($conn, "SELECT * FROM kondisi_barang");
$error = "";

$nama_barang = "";
$jenis_barang = "";
$id_kategori = "";
$id_merk = "";
$id_kondisi = "";
$stok = "";
$harga_sewa = "";
$deskripsi = "";

function buatKodeBarang($conn, $prefix){
    $prefix_aman = mysqli_real_escape_string($conn, $prefix);
    $query = mysqli_query(
        $conn,
        "SELECT MAX(CAST(SUBSTRING(kode_barang, 4) AS UNSIGNED)) AS nomor
         FROM barang
         WHERE kode_barang REGEXP '^{$prefix_aman}[0-9]+$'"
    );
    if (!$query) {
        return false;
    }
    $data = mysqli_fetch_assoc($query);
    $nomor = (int) ($data["nomor"] ?? 0) + 1;
    return $prefix . str_pad($nomor, 3, "0", STR_PAD_LEFT);
}
// Proses simpan
if (isset($_POST["simpan"])) {
    $nama_barang = trim($_POST["nama_barang"] ?? "");
    $jenis_barang = $_POST["jenis_barang"] ?? "";
    $id_kategori = (int) ($_POST["id_kategori"] ?? 0);
    $id_merk = (int) ($_POST["id_merk"] ?? 0);
    $id_kondisi = (int) ($_POST["id_kondisi"] ?? 0);
    $stok_input = trim((string) ($_POST["stok"] ?? ""));
    $harga_input = trim((string) ($_POST["harga_sewa"] ?? ""));
    $deskripsi = trim($_POST["deskripsi"] ?? "");
    // Validasi stok
    $stok_valid = filter_var($stok_input, FILTER_VALIDATE_INT);
    // Validasi harga sewa
    $harga_valid = (
        $harga_input !== "" &&
        is_numeric($harga_input) &&
        is_finite((float) $harga_input)
    );
    $harga_sewa = $harga_valid ? (float) $harga_input : "";
    if ($nama_barang === "") {
        $error = "Nama barang wajib diisi.";
    } elseif (!in_array($jenis_barang, ["kamera", "tripod"], true)) {
        $error = "Silakan pilih jenis barang yang valid.";
    } elseif ($id_kategori <= 0 || $id_merk <= 0 || $id_kondisi <= 0) {
        $error = "Kategori, merk, dan kondisi barang wajib dipilih.";
    } elseif ($stok_valid === false || $stok_valid < 0) {
        $error = "Stok harus berupa angka bulat dan tidak boleh negatif.";
    } elseif (!$harga_valid || (float) $harga_input <= 0) {
        $error = "Harga sewa per paket harus berupa angka lebih dari 0.";
    } else {
        $stok = $stok_valid;
        $harga_sewa = (float) $harga_input;
        // Tentukan prefix kode barang
        $prefix = ($jenis_barang === "kamera") ? "CAM" : "TRI";
        $kode_barang = buatKodeBarang($conn, $prefix);
        if ($kode_barang === false) {
            $error = "Gagal membuat kode barang. Silakan coba lagi.";
        } else {
            $foto = "";
            $file_terunggah = "";
            // Proses upload foto jika ada
            if (
                isset($_FILES["foto"]) &&
                $_FILES["foto"]["error"] !== UPLOAD_ERR_NO_FILE
            ) {
                if ($_FILES["foto"]["error"] !== UPLOAD_ERR_OK) {
                    $error = "Upload foto gagal. Silakan pilih foto lagi.";
                } else {
                    $tmp_foto = $_FILES["foto"]["tmp_name"];
                    $ukuran_foto = $_FILES["foto"]["size"];
                    $nama_asli = $_FILES["foto"]["name"];

                    $info_gambar = @getimagesize($tmp_foto);
                    $ekstensi = strtolower(
                        pathinfo($nama_asli, PATHINFO_EXTENSION));
                    $ekstensi_diizinkan = [
                        "jpg", "jpeg", "png", "gif", "webp"];
                    $tipe_diizinkan = [
                        IMAGETYPE_JPEG,
                        IMAGETYPE_PNG,
                        IMAGETYPE_GIF,
                        IMAGETYPE_WEBP
                    ];
                    if (
                        $info_gambar === false ||
                        !in_array($info_gambar[2], $tipe_diizinkan, true) ||
                        !in_array($ekstensi, $ekstensi_diizinkan, true)
                    ) {
                        $error = "File harus berupa gambar JPG, PNG, GIF, atau WEBP.";
                    } elseif ($ukuran_foto > 5 * 1024 * 1024) {
                        $error = "Ukuran foto maksimal 5 MB.";
                    } else {
                        $folder_upload = __DIR__ . "/../uploads/barang/";
                        if (
                            !is_dir($folder_upload) &&
                            !mkdir($folder_upload, 0755, true)) {
                            $error = "Folder upload foto gagal dibuat.";
                        } else {
                            $nama_file = bin2hex(random_bytes(16))
                                . "." . $ekstensi;
                            $tujuan_foto = $folder_upload . $nama_file;
                            echo "<pre>";
                            if (move_uploaded_file($tmp_foto, $tujuan_foto)) {
                                $foto = $nama_file;
                                $file_terunggah = $tujuan_foto;
                            } else {
                                $error = "Foto gagal disimpan ke folder upload.";
                            }
                        }
                    }
                }
            }
            // Simpan barang jika tidak ada error upload
            if ($error === "") {
                $sql = "INSERT INTO barang (
                            kode_barang,
                            nama_barang,
                            jenis_barang,
                            id_kategori,
                            id_merk,
                            id_kondisi,
                            stok,
                            harga_sewa,
                            foto,
                            deskripsi
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                if (!$stmt) {
                    $error = "Query penyimpanan gagal disiapkan.";
                } else {
                    mysqli_stmt_bind_param(
                        $stmt,
                        "sssiiiidss",
                        $kode_barang,
                        $nama_barang,
                        $jenis_barang,
                        $id_kategori,
                        $id_merk,
                        $id_kondisi,
                        $stok,
                        $harga_sewa,
                        $foto,
                        $deskripsi
                    );
                    if (mysqli_stmt_execute($stmt)) {
                        mysqli_stmt_close($stmt);

                        header("Location: index.php?pesan=tambah");
                        exit;
                    } else {
                        $nomor_error = mysqli_stmt_errno($stmt);
                        mysqli_stmt_close($stmt);

                        if ($nomor_error === 1062) {
                            $error = "Kode barang sudah digunakan. Silakan coba simpan kembali.";
                        } else {
                            $error = "Data barang gagal disimpan. Periksa struktur tabel database.";
                        }

                        // Hapus foto jika penyimpanan database gagal
                        if ($file_terunggah !== "" && file_exists($file_terunggah)) {
                            unlink($file_terunggah);
                        }
                    }
                }

                // Hapus foto jika query gagal disiapkan
                if (
                    $stmt === false &&
                    $file_terunggah !== "" &&
                    file_exists($file_terunggah)
                ) {
                    unlink($file_terunggah);
                }
            }
        }
    }
}
?>

<?php include "../layout/header.php"; ?>
<link rel="stylesheet" href="../assets/css/form-data-master.css">
<?php include "../layout/navbar.php"; ?>

<div class="main-content barang-form-page">
    <div class="container">
        <div class="barang-heading">
            <div class="barang-heading-icon">
                <i class="bi bi-box-seam"></i>
            </div>
            <div>
                <h2>Tambah Barang</h2>
                <p>Tambahkan data kamera atau tripod ke dalam sistem.</p>
            </div>
        </div>
        <div class="data-form-card">
            <div class="data-form-header">
                <h3>Informasi Barang</h3>
                <p>
                    Lengkapi informasi barang dengan benar.
                    Kode barang dibuat otomatis oleh sistem.
                </p>
            </div>
            <div class="data-form-body">
                <?php if ($error !== ""): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8"); ?>
                    </div>
                <?php endif; ?>
                <form method="POST" enctype="multipart/form-data">
                    <div class="barang-form-grid">
                        <div class="barang-form-section">
                            <div class="barang-field">
                                <label for="kode_barang" class="data-form-label">
                                    Kode Barang
                                </label>
                                <div class="kode-otomatis-box">
                                    <i class="bi bi-upc-scan"></i>
                                    <input
                                        type="text"
                                        id="kode_barang"
                                        class="data-form-control"
                                        value="Pilih jenis barang terlebih dahulu"
                                        readonly>
                                </div>
                                <small>Kode dibuat otomatis saat data disimpan.</small>
                            </div>
                            <div class="barang-field">
                                <label for="nama_barang" class="data-form-label">
                                    Nama Barang <span class="required-mark">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="nama_barang"
                                    id="nama_barang"
                                    class="data-form-control"
                                    value="<?= htmlspecialchars($nama_barang, ENT_QUOTES, "UTF-8"); ?>"
                                    placeholder="Contoh: Canon EOS 1500D"
                                    required>
                            </div>
                            <div class="barang-field">
                                <label for="jenis_barang" class="data-form-label">
                                    Jenis Barang <span class="required-mark">*</span>
                                </label>
                                <select
                                    name="jenis_barang"
                                    id="jenis_barang"
                                    class="data-form-control"
                                    required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="kamera" <?= $jenis_barang === "kamera" ? "selected" : ""; ?>>
                                        Kamera
                                    </option>
                                    <option value="tripod" <?= $jenis_barang === "tripod" ? "selected" : ""; ?>>
                                        Tripod
                                    </option>
                                </select>
                            </div>
                            <div class="barang-field-row">
                                <div class="barang-field">
                                    <label for="id_kategori" class="data-form-label">
                                        Kategori <span class="required-mark">*</span>
                                    </label>
                                    <select
                                        name="id_kategori"
                                        id="id_kategori"
                                        class="data-form-control"
                                        required  >
                                        <option value="">-- Pilih Kategori --</option>
                                        <?php while ($row = mysqli_fetch_assoc($kategori)): ?>
                                            <option
                                                value="<?= (int) $row["id_kategori"]; ?>"
                                                <?= (string) $id_kategori === (string) $row["id_kategori"] ? "selected" : ""; ?>>
                                                <?= htmlspecialchars($row["nama_kategori"], ENT_QUOTES, "UTF-8"); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="barang-field">
                                    <label for="id_merk" class="data-form-label">
                                        Merk <span class="required-mark">*</span>
                                    </label>
                                    <select
                                        name="id_merk"
                                        id="id_merk"
                                        class="data-form-control"
                                        required >
                                        <option value="">-- Pilih Merk --</option>
                                        <?php while ($row = mysqli_fetch_assoc($merk)): ?>
                                            <option
                                                value="<?= (int) $row["id_merk"]; ?>"
                                                <?= (string) $id_merk === (string) $row["id_merk"] ? "selected" : ""; ?>>
                                                <?= htmlspecialchars($row["nama_merk"], ENT_QUOTES, "UTF-8"); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="barang-field-row">
                                <div class="barang-field">
                                    <label for="id_kondisi" class="data-form-label">
                                        Kondisi <span class="required-mark">*</span>
                                    </label>
                                    <select
                                        name="id_kondisi"
                                        id="id_kondisi"
                                        class="data-form-control"
                                        required>
                                        <option value="">-- Pilih Kondisi --</option>
                                        <?php while ($row = mysqli_fetch_assoc($kondisi)): ?>
                                            <option
                                                value="<?= (int) $row["id_kondisi"]; ?>"
                                                <?= (string) $id_kondisi === (string) $row["id_kondisi"] ? "selected" : ""; ?>>
                                                <?= htmlspecialchars($row["nama_kondisi"], ENT_QUOTES, "UTF-8"); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="barang-field">
                                    <label for="stok" class="data-form-label">
                                        Stok <span class="required-mark">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        name="stok"
                                        id="stok"
                                        class="data-form-control"
                                        min="0"
                                        step="1"
                                        value="<?= htmlspecialchars((string) $stok, ENT_QUOTES, "UTF-8"); ?>"
                                        placeholder="Contoh: 5"
                                        required>
                                </div>
                            </div>
                            <div class="barang-field">
                                <label for="harga_sewa" class="data-form-label">
                                    Harga Sewa per Paket
                                    <span class="required-mark">*</span>
                                </label>
                                <input
                                    type="number"
                                    name="harga_sewa"
                                    id="harga_sewa"
                                    class="data-form-control"
                                    min="1"
                                    step="1"
                                    value="<?= htmlspecialchars((string) $harga_sewa, ENT_QUOTES, "UTF-8"); ?>"
                                    placeholder="Contoh: 100000"
                                    required>
                                <small>
                                    Masukkan harga untuk satu paket peminjaman dalam rupiah,
                                    tanpa tanda titik atau simbol Rp.
                                </small>
                            </div>
                            <div class="barang-field">
                                <label for="deskripsi" class="data-form-label">
                                    Deskripsi
                                </label>
                                <textarea
                                    name="deskripsi"
                                    id="deskripsi"
                                    class="data-form-control"
                                    rows="4"
                                    placeholder="Deskripsi atau informasi tambahan barang...">
                                    <?= htmlspecialchars($deskripsi, ENT_QUOTES, "UTF-8"); ?></textarea>
                            </div>
                        </div>
                        <div class="barang-form-section">
                            <div class="barang-photo-card">
                                <h4 class="barang-photo-title">Foto Barang</h4>
                                <p class="barang-photo-description">
                                    Unggah foto kamera atau tripod.
                                    Format JPG, PNG, GIF, atau WEBP; maksimal 5 MB.
                                </p>
                                <div
                                    class="barang-photo-preview"
                                    id="preview_foto">
                                    <div class="barang-photo-placeholder">
                                        <i class="bi bi-image"></i>
                                        <span>Pratinjau foto barang</span>
                                    </div>
                                </div>
                                <label for="foto" class="data-form-label">
                                    Pilih Foto
                                </label>
                                <input
                                    type="file"
                                    name="foto"
                                    id="foto"
                                    class="data-form-control barang-photo-input"
                                    accept=".jpg,.jpeg,.png,.gif,.webp,image/*">
                            </div>
                        </div>
                    </div>
                    <div class="barang-form-actions">
                        <button type="submit" name="simpan" class="btn-form-save">
                            <i class="bi bi-save"></i> Simpan Barang
                        </button>
                        <a href="index.php" class="btn-form-cancel">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
const jenisBarang = document.getElementById("jenis_barang");
const kodeBarang = document.getElementById("kode_barang");
function tampilkanPrefix() {
    if (jenisBarang.value === "kamera") {
        kodeBarang.value = "CAM (otomatis)";
    } else if (jenisBarang.value === "tripod") {
        kodeBarang.value = "TRI (otomatis)";
    } else {
        kodeBarang.value = "Pilih jenis barang terlebih dahulu";
    }
}
jenisBarang.addEventListener("change", tampilkanPrefix);
tampilkanPrefix();
const inputFoto = document.getElementById("foto");
const previewFoto = document.getElementById("preview_foto");
function tampilkanPlaceholder() {
    previewFoto.replaceChildren();
    const placeholder = document.createElement("div");
    placeholder.className = "barang-photo-placeholder";
    const icon = document.createElement("i");
    icon.className = "bi bi-image";
    const teks = document.createElement("span");
    teks.textContent = "Pratinjau foto barang";
    placeholder.append(icon, teks);
    previewFoto.appendChild(placeholder);
}
inputFoto.addEventListener("change", function () {
    previewFoto.replaceChildren();
    const file = this.files[0];
    if (!file) {
        tampilkanPlaceholder();
        return;
    }
    const tipeDiizinkan = [
        "image/jpeg",
        "image/png",
        "image/gif",
        "image/webp"];
    if (!tipeDiizinkan.includes(file.type)) {
        this.value = "";
        tampilkanPlaceholder();
        alert("Pilih foto berformat JPG, PNG, GIF, atau WEBP.");
        return;
    }
    if (file.size > 5 * 1024 * 1024) {
        this.value = "";
        tampilkanPlaceholder();
        alert("Ukuran foto maksimal 5 MB.");
        return;
    }
    const img = document.createElement("img");
    img.alt = "Pratinjau foto barang";
    const urlFoto = URL.createObjectURL(file);
    img.src = urlFoto;
    img.onload = function () {
        URL.revokeObjectURL(urlFoto);
    };
    img.onerror = function () {
        URL.revokeObjectURL(urlFoto);
        tampilkanPlaceholder();
        alert("Foto tidak dapat ditampilkan. Coba pilih foto lain.");
    };
    previewFoto.appendChild(img);
});
</script>
<?php include "../layout/footer.php"; ?>
