<?php
// Koneksi database langsung yang aman dan pasti ketemu
$koneksi = mysqli_connect("localhost", "root", "", "project");
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Cek apakah data form dikirim melalui metode POST
if (isset($_POST['simpan']) || isset($_POST['proses_pinjam'])) {
    // Ambil data dari form peminjaman
    $nama_peminjam = isset($_POST['nama_peminjam']) ? $_POST['nama_peminjam'] : '';
    $id_barang     = isset($_POST['id_barang']) ? $_POST['id_barang'] : '';
    $tanggal_pinjam = isset($_POST['tanggal_pinjam']) ? $_POST['tanggal_pinjam'] : date('Y-m-d');
    $status        = 'Diajukan';

    // Masukkan data ke dalam tabel peminjaman
    $query = "INSERT INTO peminjaman (nama_peminjam, id_barang, tanggal_pinjam, status) VALUES ('$nama_peminjam', '$id_barang', '$tanggal_pinjam', '$status')";
    $simpan = mysqli_query($koneksi, $query);

    if ($simpan) {
        // Jika berhasil, kembalikan ke halaman peminjaman
        echo "<script>alert('Peminjaman berhasil diproses!'); window.location='peminjaman.php';</script>";
        exit();
    } else {
        echo "Gagal menyimpan data: " . mysqli_error($koneksi);
    }
} else {
    // Jika diakses secara langsung tanpa form, kembalikan ke halaman peminjaman
    header("Location: peminjaman.php");
    exit();
}
?>