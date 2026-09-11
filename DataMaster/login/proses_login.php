<?php

session_start();

require_once "../config/koneksi.php";
/** @var mysqli $conn */

$username = $_POST['username'];
$password = $_POST['password'];

$query = mysqli_query($conn, "SELECT * FROM pengguna
    WHERE username = '$username'");

$data = mysqli_fetch_assoc($query);

if ($data && password_verify($password, $data['password'])) {

    $_SESSION['id_pengguna'] = $data['id_pengguna'];
    $_SESSION['nama'] = $data['nama'];
    $_SESSION['username'] = $data['username'];
    $_SESSION['role'] = $data['role'];
    $_SESSION['foto'] = $data['foto'];

    header("Location: ../dashboard/index.php");
    exit;

} else {

    header("Location: index.php?pesan=gagal");
    exit;
}