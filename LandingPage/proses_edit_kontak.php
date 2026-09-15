<?php

include 'includes/koneksi.php';

$id = $_POST['id'];
$nama = $_POST['nama'];
$email = $_POST['email'];
$pesan = $_POST['pesan'];

$query = "UPDATE kontak SET
          nama = '$nama',
          email = '$email',
          pesan = '$pesan'
          WHERE id = '$id'";

if (mysqli_query($conn, $query)) {
    header("Location: data_kontak.php");
    exit;

} else {

    echo "Data gagal diubah: " . mysqli_error($conn);
}

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
?>