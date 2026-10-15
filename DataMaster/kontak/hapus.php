<?php
require_once "../config/koneksi.php";

// Memastikan ID tersedia dan valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET['id'];

// Menghapus data kontak
$query = mysqli_prepare($conn, "DELETE FROM kontak WHERE id = ?");
mysqli_stmt_bind_param($query, "i", $id);

if (mysqli_stmt_execute($query)) {
    mysqli_stmt_close($query);
    header("Location: index.php?pesan=hapus");
    exit;
}

mysqli_stmt_close($query);
header("Location: index.php?pesan=gagal");
exit;
?>