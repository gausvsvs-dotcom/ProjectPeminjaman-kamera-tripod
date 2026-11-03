
<?php
session_start();

require_once "koneksi.php";

// Pastikan hanya form POST yang bisa menjalankan proses
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: peminjaman.php");
    exit;
}

// Ambil data dari form
$nama_peminjam   = trim($_POST['nama_peminjam'] ?? '');
$no_hp           = trim($_POST['no_hp'] ?? '');
$tgl_peminjaman   = trim($_POST['tgl_peminjaman'] ?? '');
$tgl_jatuh_tempo = trim($_POST['tgl_jatuh_tempo'] ?? '');
$alamat           = trim($_POST['alamat'] ?? '');

// Simpan input jika terjadi kesalahan
$_SESSION['old'] = [
    'nama_peminjam'   => $nama_peminjam,
    'no_hp'           => $no_hp,
    'tgl_peminjaman'   => $tgl_peminjaman,
    'tgl_jatuh_tempo' => $tgl_jatuh_tempo,
    'alamat'           => $alamat
];

// Validasi
if (
    $nama_peminjam === '' ||
    $no_hp === '' ||
    $tgl_peminjaman === '' ||
    $tgl_jatuh_tempo === '' ||
    $alamat === ''
) {
    $_SESSION['pesan_error'] = "Semua data peminjaman wajib diisi.";
    header("Location: peminjaman.php");
    exit;
}

if (!preg_match('/^[0-9+\-\s]{10,20}$/', $no_hp)) {
    $_SESSION['pesan_error'] = "Nomor HP tidak valid. Periksa kembali.";
    header("Location: peminjaman.php");
    exit;
}

if ($tgl_jatuh_tempo < $tgl_peminjaman) {
    $_SESSION['pesan_error'] = "Tanggal pengembalian tidak boleh sebelum tanggal peminjaman.";
    header("Location: peminjaman.php");
    exit;
}

// Konsumen belum login
$id_user = null;

// Status awal sesuai ENUM database
$status = "Diajukan";

// Jenis peminjaman diisi otomatis karena tidak ditampilkan di form
$jenis_peminjaman = "online";

// Simpan data
$query = "INSERT INTO peminjaman
    (id_user, nama_peminjam, no_hp, tgl_peminjaman,
     tgl_jatuh_tempo, status, jenis_peminjaman, Alamat)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($koneksi, $query);

if (!$stmt) {
    $_SESSION['pesan_error'] = "Terjadi kesalahan saat menyiapkan penyimpanan data.";
    header("Location: peminjaman.php");
    exit;
}

mysqli_stmt_bind_param(
    $stmt,
    "isssssss",
    $id_user,
    $nama_peminjam,
    $no_hp,
    $tgl_peminjaman,
    $tgl_jatuh_tempo,
    $status,
    $jenis_peminjaman,
    $alamat
);


if (mysqli_stmt_execute($stmt)) {

    $id_baru = mysqli_insert_id($koneksi);

    unset($_SESSION['old']);

    mysqli_stmt_close($stmt);
    mysqli_close($koneksi);

    header("Location: detail_peminjaman.php?id=" . $id_baru);
    exit;

} else {

    $_SESSION['pesan_error'] = "Gagal menyimpan pengajuan peminjaman: "
        . mysqli_stmt_error($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($koneksi);

    header("Location: peminjaman.php");
    exit;
}
