<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "project";

$conn = mysqli_connect($host, $user, $password, $database);

if(!$conn) {
    die("Koneksi db gagal". mysqli_connect_errno());
}
?>