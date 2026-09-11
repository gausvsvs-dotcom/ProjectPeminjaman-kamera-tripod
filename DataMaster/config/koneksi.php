<?php
$host = "localhost";
$user = "root";
$pw = "";
$db = "project";

$conn = mysqli_connect($host, $user, $pw, $db);

if(!$conn){
    die("Koneksi db gagal".mysqli_connect_errno());
}
?>