
<?php
session_start();
require_once "../config/koneksi.php";

/** @var mysqli $conn */

header('Content-Type: application/json; charset=utf-8');

// Pastikan pengguna sudah login
if (!isset($_SESSION['id_user'])) {
    echo json_encode([
        'status' => 'error',
        'pesan' => 'Sesi login berakhir. Silakan login kembali.'
    ]);
    exit;
}

$id = (int) $_SESSION['id_user'];

$nama = trim($_POST['nama'] ?? '');
$username = trim($_POST['username'] ?? '');

// Validasi input
if ($nama === '' || $username === '') {
    echo json_encode([
        'status' => 'error',
        'pesan' => 'Nama dan username wajib diisi!'
    ]);
    exit;
}

// Cek username digunakan akun lain
$stmt = mysqli_prepare(
    $conn,
    "SELECT id_user FROM pengguna
     WHERE username = ? AND id_user != ?"
);

mysqli_stmt_bind_param($stmt, "si", $username, $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    mysqli_stmt_close($stmt);

    echo json_encode([
        'status' => 'error',
        'pesan' => 'Username sudah digunakan akun lain!'
    ]);
    exit;
}

mysqli_stmt_close($stmt);

// Ambil data profil lama
$stmt = mysqli_prepare(
    $conn,
    "SELECT nama_user, username, foto
     FROM pengguna WHERE id_user = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$dataLama = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

$foto = $dataLama['foto'] ?? 'diana.jpg';

// Folder foto
$folder = __DIR__ . "/../assets/img/";

// Proses upload foto
if (
    isset($_FILES['foto']) &&
    $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE
) {
    $file = $_FILES['foto'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode([
            'status' => 'error',
            'pesan' => 'Upload foto gagal. Kode: ' . $file['error']
        ]);
        exit;
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        echo json_encode([
            'status' => 'error',
            'pesan' => 'Ukuran foto maksimal 2 MB.'
        ]);
        exit;
    }

    // Validasi MIME
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $format = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];

    if (!isset($format[$mime])) {
        echo json_encode([
            'status' => 'error',
            'pesan' => 'Format foto harus JPG, PNG, atau WEBP.'
        ]);
        exit;
    }

    if (!is_writable($folder)) {
        echo json_encode([
            'status' => 'error',
            'pesan' => 'Folder assets/img tidak memiliki izin menulis.'
        ]);
        exit;
    }

    // Nama foto unik
    $namaFoto = 'profil_' . uniqid() . '.' . $format[$mime];

    if (!move_uploaded_file(
        $file['tmp_name'],
        $folder . $namaFoto
    )) {
        echo json_encode([
            'status' => 'error',
            'pesan' => 'Gagal menyimpan foto.'
        ]);
        exit;
    }

    $foto = $namaFoto;
}

// Cek apakah ada perubahan
if (
    $nama === $dataLama['nama_user'] &&
    $username === $dataLama['username'] &&
    $foto === $dataLama['foto']
) {
    echo json_encode([
        'status' => 'tidak_berubah',
        'pesan' => 'Tidak ada perubahan profil.'
    ]);
    exit;
}

// Update database
$stmt = mysqli_prepare(
    $conn,
    "UPDATE pengguna
     SET nama_user = ?, username = ?, foto = ?
     WHERE id_user = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "sssi",
    $nama,
    $username,
    $foto,
    $id
);

if (mysqli_stmt_execute($stmt)) {

    // Update session
    $_SESSION['nama'] = $nama;
    $_SESSION['username'] = $username;
    $_SESSION['foto'] = $foto;

    mysqli_stmt_close($stmt);

    echo json_encode([
        'status' => 'berhasil',
        'pesan' => 'Profil berhasil diperbarui!',
        'nama' => $nama,
        'username' => $username,
        'foto' => $foto
    ]);
    exit;

} else {
    mysqli_stmt_close($stmt);

    echo json_encode([
        'status' => 'error',
        'pesan' => 'Gagal memperbarui profil.'
    ]);
    exit;
}
?>
