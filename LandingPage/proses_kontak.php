<?php

include 'includes/koneksi.php';

$nama = $_POST['nama'];
$email = $_POST['email'];
$pesan = $_POST['pesan'];

$query = "INSERT INTO kontak (nama, email, pesan)
          VALUES ('$nama', '$email', '$pesan')";

if (mysqli_query($conn, $query)) {
    header("Location: kontak.php?status=success");
    exit;
} else {
    echo "Data gagal disimpan: " . mysqli_error($conn);
}

?>