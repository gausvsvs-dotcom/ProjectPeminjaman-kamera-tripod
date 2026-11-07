<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "../config/koneksi.php";
/** @var mysqli $conn */
$error = "";
$nama_barang = "";
$jenis_barang = "";
$id_kategori = "";
$id_merk = "";
$stok = "";
$harga_sewa = "";
$deskripsi = "";
$kondisi_unit_input = [];

$daftar_kondisi = [];
$query_kondisi = mysqli_query($conn, "
    SELECT id_kondisi, nama_kondisi
    FROM kondisi_barang
    ORDER BY id_kondisi ASC
");
if (!$query_kondisi) {
    die("Gagal mengambil data kondisi: " . mysqli_error($conn));
}
while ($row = mysqli_fetch_assoc($query_kondisi)) {
    $daftar_kondisi[] = $row;
}
$kondisi_valid = array_map(
    fn($item) => (int) $item["id_kondisi"],
    $daftar_kondisi
);

function buatKodeBarang($conn, $prefix)
{
    $stmt = mysqli_prepare($conn, "
        SELECT MAX(CAST(SUBSTRING(kode_barang, 4) AS UNSIGNED)) AS nomor
        FROM barang
        WHERE kode_barang REGEXP ?
    ");
    if (!$stmt) {
        throw new Exception(mysqli_error($conn));
    }
    $pola = "^" . $prefix . "[0-9]+$";
    mysqli_stmt_bind_param($stmt, "s", $pola);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    $nomor = (int) ($data["nomor"] ?? 0) + 1;
    return $prefix . str_pad($nomor, 3, "0", STR_PAD_LEFT);
}
// PROSES SIMPAN BARANG
if (isset($_POST["simpan"])) {
    $nama_barang = trim($_POST["nama_barang"] ?? "");
    $jenis_barang = $_POST["jenis_barang"] ?? "";
    $id_kategori = (int) ($_POST["id_kategori"] ?? 0);
    $id_merk = (int) ($_POST["id_merk"] ?? 0);
    $stok_input = trim((string) ($_POST["stok"] ?? ""));
    $harga_input = trim((string) ($_POST["harga_sewa"] ?? ""));
    $deskripsi = trim($_POST["deskripsi"] ?? "");
    $stok_valid = filter_var($stok_input, FILTER_VALIDATE_INT);
    $harga_valid = (
        $harga_input !== "" &&
        is_numeric($harga_input) &&
        is_finite((float) $harga_input)
    );
    $kondisi_unit_input = $_POST["kondisi_unit"] ?? [];
    if (!is_array($kondisi_unit_input)) {
        $kondisi_unit_input = [];
    }
    $kondisi_unit_input = array_map(
        "intval",
        array_values($kondisi_unit_input)
    );
    $foto = "";
    $file_terunggah = "";
    // VALIDASI
    if ($nama_barang === "") {
        $error = "Nama barang wajib diisi.";
    } elseif (!in_array($jenis_barang, ["kamera", "tripod"], true)) {
        $error = "Jenis barang tidak valid.";
    } elseif ($id_kategori <= 0 || $id_merk <= 0) {
        $error = "Kategori dan merk wajib dipilih.";
    } elseif (
        $stok_valid === false ||
        $stok_valid < 1 ||
        $stok_valid > 500
    ) {
        $error = "Stok harus berupa angka antara 1 sampai 500.";
    } elseif (
        count($kondisi_unit_input) !== $stok_valid ||
        count(array_diff($kondisi_unit_input, $kondisi_valid)) > 0
    ) {
        $error = "Pilih kondisi untuk setiap unit barang.";
    } elseif (!$harga_valid || (float) $harga_input <= 0) {
        $error = "Harga sewa per paket harus lebih dari 0.";
    } else {
        $stok = $stok_valid;
        $harga_sewa = (float) $harga_input;
        // KONDISI BARANG UTAMA MENGIKUTI UNIT PERTAMA
        $id_kondisi = $kondisi_unit_input[0];
        $prefix = ($jenis_barang === "kamera") ? "CAM" : "TRI";
        try {
            $kode_barang = buatKodeBarang($conn, $prefix);
        } catch (Throwable $e) {
            $error = "Kode barang gagal dibuat: " . $e->getMessage();
        }
        // UPLOAD FOTO
        if ($error === "" && isset($_FILES["foto"]) &&
            $_FILES["foto"]["error"] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES["foto"]["error"] !== UPLOAD_ERR_OK) {
                $error = "Upload foto gagal.";
            } else {
                $tmp_foto = $_FILES["foto"]["tmp_name"];
                $ukuran_foto = $_FILES["foto"]["size"];
                $nama_asli = $_FILES["foto"]["name"];
                $info_gambar = @getimagesize($tmp_foto);
                $ekstensi = strtolower(
                    pathinfo($nama_asli, PATHINFO_EXTENSION)
                );
                $ekstensi_diizinkan = [
                    "jpg", "jpeg", "png", "gif", "webp"
                ];
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
                        !mkdir($folder_upload, 0755, true)
                    ) {
                        $error = "Folder upload gagal dibuat.";
                    } else {
                        $nama_file = bin2hex(random_bytes(16))
                            . "." . $ekstensi;
                        $tujuan_foto = $folder_upload . $nama_file;
                        if (move_uploaded_file($tmp_foto, $tujuan_foto)) {
                            $foto = $nama_file;
                            $file_terunggah = $tujuan_foto;
                        } else {
                            $error = "Foto gagal disimpan.";
                        }
                    }
                }
            }
        }
        // SIMPAN BARANG DAN UNIT DALAM SATU TRANSAKSI
        if ($error === "") {
            mysqli_begin_transaction($conn);
            try {
                // INSERT BARANG UTAMA
                $sql = "
                    INSERT INTO barang (
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
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ";
                $stmt = mysqli_prepare($conn, $sql);
                if (!$stmt) {
                    throw new Exception(mysqli_error($conn));
                }
                mysqli_stmt_bind_param(
                    $stmt,
                    "sssiiidsss",
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
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception(mysqli_stmt_error($stmt));
                }
                $id_barang_baru = mysqli_insert_id($conn);

                mysqli_stmt_close($stmt);

                // INSERT SETIAP UNIT DENGAN KONDISI MASING-MASING
                $stmt_unit = mysqli_prepare($conn, "
                    INSERT INTO barang_unit (
                        id_barang,
                        kode_unit,
                        id_kondisi,
                        status_unit
                    ) VALUES (?, ?, ?, ?)
                ");

                if (!$stmt_unit) {
                    throw new Exception(mysqli_error($conn));
                }

                $status_unit = "Tersedia";

                for ($i = 0; $i < $stok; $i++) {

                    $nomor_unit = $i + 1;

                    $kode_unit = $kode_barang . "-" .
                        str_pad($nomor_unit, 3, "0", STR_PAD_LEFT);

                    $id_kondisi_unit = $kondisi_unit_input[$i];

                    mysqli_stmt_bind_param(
                        $stmt_unit,
                        "isis",
                        $id_barang_baru,
                        $kode_unit,
                        $id_kondisi_unit,
                        $status_unit
                    );

                    if (!mysqli_stmt_execute($stmt_unit)) {
                        throw new Exception(mysqli_stmt_error($stmt_unit));
                    }
                }

                mysqli_stmt_close($stmt_unit);

                mysqli_commit($conn);

                header("Location: index.php?pesan=tambah");
                exit;

            } catch (Throwable $e) {

                mysqli_rollback($conn);

                if (
                    $file_terunggah !== "" &&
                    file_exists($file_terunggah)
                ) {
                    unlink($file_terunggah);
                }

                $error = "Data barang gagal disimpan: " . $e->getMessage();
            }
        }
    }
}

// DROPDOWN
$kategori = mysqli_query($conn, "SELECT * FROM kategori");
$merk = mysqli_query($conn, "SELECT * FROM merk");

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
                                        required>

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
                                        required>

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

                            <div class="barang-field">
                                <label for="stok" class="data-form-label">
                                    Jumlah Unit <span class="required-mark">*</span>
                                </label>

                                <input
                                    type="number"
                                    name="stok"
                                    id="stok"
                                    class="data-form-control"
                                    min="1"
                                    max="500"
                                    step="1"
                                    value="<?= htmlspecialchars((string) $stok, ENT_QUOTES, "UTF-8"); ?>"
                                    placeholder="Contoh: 5"
                                    required>

                                <small>
                                    Masukkan jumlah unit barang yang ingin ditambahkan.
                                </small>
                            </div>

                            <div class="barang-field" id="daftar-kondisi-unit">
                                <label class="data-form-label">
                                    Kondisi Setiap Unit
                                    <span class="required-mark">*</span>
                                </label>

                                <div id="kondisi-unit-container"></div>

                                <small>
                                    Pilih kondisi untuk masing-masing unit barang.
                                </small>
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
                                    placeholder="Deskripsi atau informasi tambahan barang..."><?= htmlspecialchars($deskripsi, ENT_QUOTES, "UTF-8"); ?></textarea>
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
// KODE BARANG OTOMATIS
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
// PILIHAN KONDISI SETIAP UNIT
const inputStok = document.getElementById("stok");
const containerKondisi = document.getElementById("kondisi-unit-container");
const daftarKondisi = <?= json_encode(
    $daftar_kondisi,
    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
); ?>;
const kondisiSebelumnya = <?= json_encode($kondisi_unit_input); ?>;
function buatPilihanKondisi() {
    const jumlah = parseInt(inputStok.value) || 0;
    const pilihanLama = Array.from(
        containerKondisi.querySelectorAll("select")
    ).map(select => select.value);
    containerKondisi.replaceChildren();
    if (jumlah < 1 || jumlah > 500) {
        return;
    }
    for (let i = 0; i < jumlah; i++) {
        const field = document.createElement("div");
        field.className = "barang-field";
        field.style.marginBottom = "12px";
        const label = document.createElement("label");
        label.className = "data-form-label";
        label.textContent = `Kondisi Unit ${String(i + 1).padStart(3, "0")}`;
        const select = document.createElement("select");
        select.name = `kondisi_unit[${i}]`;
        select.className = "data-form-control";
        select.required = true;
        const pilihanAwal = document.createElement("option");
        pilihanAwal.value = "";
        pilihanAwal.textContent = "-- Pilih Kondisi --";
        select.appendChild(pilihanAwal);
        daftarKondisi.forEach(item => {
            const option = document.createElement("option");
            option.value = item.id_kondisi;
            option.textContent = item.nama_kondisi;
            const nilaiLama = pilihanLama[i] || kondisiSebelumnya[i];
            if (Number(nilaiLama) === Number(item.id_kondisi)) {
                option.selected = true;
            }
            select.appendChild(option);
        });
        field.append(label, select);
        containerKondisi.appendChild(field);
    }
}
inputStok.addEventListener("input", buatPilihanKondisi);
buatPilihanKondisi();
// PREVIEW FOTO
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
        "image/webp"
    ];
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