<?php
// Karena koneksi.php berada di folder yang sama, panggil langsung namanya!
require_once "koneksi.php";

// Memastikan form dikirim melalui metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Menangkap data dari form peminjaman.php
    $nama_peminjam    = mysqli_real_escape_string($conn, $_POST['nama_peminjam']);
    $no_hp            = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $tgl_peminjaman   = mysqli_real_escape_string($conn, $_POST['tgl_peminjaman']);
    $tgl_jatuh_tempo  = mysqli_real_escape_string($conn, $_POST['tgl_jatuh_tempo']);
    $jenis_peminjaman = mysqli_real_escape_string($conn, $_POST['jenis_peminjaman']);
    $Alamat           = mysqli_real_escape_string($conn, $_POST['Alamat']);
    
    // Status default saat data disimpan
    $status           = 'Diajukan'; 

    // Query simpan data ke tabel peminjaman
    $query = "INSERT INTO peminjaman (nama_peminjam, no_hp, tgl_peminjaman, tgl_jatuh_tempo, jenis_peminjaman, Alamat, status) 
              VALUES ('$nama_peminjam', '$no_hp', '$tgl_peminjaman', '$tgl_jatuh_tempo', '$jenis_peminjaman', '$Alamat', '$status')";

    $result = mysqli_query($conn, $query);

    if ($result) {
        echo "<script>
                alert('Data peminjaman berhasil disimpan!');
                window.location='peminjaman.php';
              </script>";
    } else {
        echo "Gagal menyimpan data: " . mysqli_error($conn);
    }
} else {
    header("Location: peminjaman.php");
    exit();
}
?>