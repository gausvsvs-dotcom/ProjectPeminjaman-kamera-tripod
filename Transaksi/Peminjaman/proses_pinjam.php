<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db   = "project";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Ambil data dari form dengan aman
    $nama_peminjam    = isset($_POST['nama_peminjam']) ? mysqli_real_escape_string($koneksi, $_POST['nama_peminjam']) : '';
    $no_hp            = isset($_POST['no_hp']) ? mysqli_real_escape_string($koneksi, $_POST['no_hp']) : '';
    $tgl_peminjaman   = isset($_POST['tgl_peminjaman']) ? mysqli_real_escape_string($koneksi, $_POST['tgl_peminjaman']) : '';
    $tgl_jatuh_tempo  = isset($_POST['tgl_jatuh_tempo']) ? mysqli_real_escape_string($koneksi, $_POST['tgl_jatuh_tempo']) : '';
    $alamat           = isset($_POST['alamat']) ? mysqli_real_escape_string($koneksi, $_POST['alamat']) : '';
    $jenis_peminjaman = isset($_POST['jenis_peminjaman']) ? mysqli_real_escape_string($koneksi, $_POST['jenis_peminjaman']) : 'offline';
    
    // Ambil ID user secara otomatis untuk menghindari error foreign key
    $query_user = mysqli_query($koneksi, "SELECT id_user FROM pengguna LIMIT 1");
    if (mysqli_num_rows($query_user) > 0) {
        $data_user = mysqli_fetch_assoc($query_user);
        $id_user = $data_user['id_user'];
    } else {
        mysqli_query($koneksi, "INSERT INTO pengguna (nama_user, username, role, password) VALUES ('Admin Sistem', 'admin', 'admin', '123')");
        $id_user = mysqli_insert_id($koneksi);
    }

    // Query INSERT ke tabel peminjaman
    $query = "INSERT INTO peminjaman (id_user, nama_peminjam, no_hp, tgl_peminjaman, tgl_jatuh_tempo, jenis_peminjaman, Alamat) 
              VALUES ('$id_user', '$nama_peminjam', '$no_hp', '$tgl_peminjaman', '$tgl_jatuh_tempo', '$jenis_peminjaman', '$alamat')";

    $result = mysqli_query($koneksi, $query);

    if ($result) {
        echo "<script>
                alert('Data peminjaman berhasil disimpan!');
                window.history.go(-1); 
              </script>";
    } else {
        echo "Gagal menyimpan data: " . mysqli_error($koneksi);
    }
} else {
    header("Location: form_peminjaman.php");
    exit();
}
?>