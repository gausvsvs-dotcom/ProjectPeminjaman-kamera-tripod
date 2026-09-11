<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */

if (isset($_GET['id'])) {

    $id = $_GET['id'];

    // Ambil foto barang sebelum data dihapus
    $query = mysqli_query($conn, "SELECT foto FROM barang WHERE id_barang = '$id'");

    $data = mysqli_fetch_assoc($query);

    // Hapus file foto jika ada
    if (!empty($data['foto'])) {

        $file = "../uploads/barang/" . $data['foto'];

        if (file_exists($file)) {
            unlink($file);
        }
    }

    // Hapus data barang
    $hapus = mysqli_query($conn, "DELETE FROM barang WHERE id_barang = '$id'");

    if ($hapus) {
        header("Location: index.php?pesan=hapus");
        exit;
    } else {
        die("Data barang gagal dihapus: " . mysqli_error($conn));
    }
}

header("Location: index.php");
exit;
?>