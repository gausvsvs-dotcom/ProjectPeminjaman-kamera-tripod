
<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/koneksi.php';
/** @var mysqli $conn */

function kirimRespons($status, $pesan)
{
    echo json_encode([
        'status' => $status,
        'pesan' => $pesan
    ]);
    exit;
}

if (!isset($_SESSION['id_user'])) {
    kirimRespons('gagal', 'Sesi login berakhir. Silakan login kembali.');
}

$id_user = (int) $_SESSION['id_user'];

$password_lama = $_POST['password_lama'] ?? '';
$password_baru = $_POST['password_baru'] ?? '';
$konfirmasi = $_POST['konfirmasi_password'] ?? '';

if ($password_lama === '' || $password_baru === '' || $konfirmasi === '') {
    kirimRespons('gagal', 'Semua kolom password wajib diisi.');
}

if (strlen($password_baru) < 8) {
    kirimRespons('gagal', 'Password baru minimal 8 karakter.');
}

if ($password_baru !== $konfirmasi) {
    kirimRespons('gagal', 'Konfirmasi password baru tidak cocok.');
}

if ($password_lama === $password_baru) {
    kirimRespons('gagal', 'Password baru harus berbeda dari password lama.');
}

// Ambil password akun yang sedang login
$stmt = mysqli_prepare(
    $conn,
    "SELECT password FROM pengguna WHERE id_user = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id_user);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);
mysqli_stmt_close($stmt);

if (!$data) {
    kirimRespons('gagal', 'Data akun tidak ditemukan.');
}

if (!password_verify($password_lama, $data['password'])) {
    kirimRespons('gagal', 'Password lama yang kamu masukkan salah.');
}

// Hash password baru
$hash_password = password_hash($password_baru, PASSWORD_DEFAULT);

// Update password akun yang sedang login
$stmt = mysqli_prepare(
    $conn,
    "UPDATE pengguna SET password = ? WHERE id_user = ?"
);

mysqli_stmt_bind_param($stmt, "si", $hash_password, $id_user);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    kirimRespons('berhasil', 'Password berhasil diubah!');
}

mysqli_stmt_close($stmt);
kirimRespons('gagal', 'Password gagal diubah. Silakan coba lagi.');
?>
