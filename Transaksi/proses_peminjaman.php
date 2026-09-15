<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['simpan'])) {
    
    // 1. Menangkap data dari Form
    $kode_peminjaman         = isset($_POST['id_peminjaman']) ? $_POST['id_peminjaman'] : ('PMJ-' . date('YmdHis'));
    $id_barang               = isset($_POST['id_barang']) ? $_POST['id_barang'] : '';
    $nama_peminjam           = isset($_POST['nama_peminjam']) ? mysqli_real_escape_string($koneksi, $_POST['nama_peminjam']) : '';
    $tanggal_peminjaman      = isset($_POST['tgl_pinjam']) ? $_POST['tgl_pinjam'] : date('Y-m-d');
    $tanggal_rencana_kembali = isset($_POST['tgl_jatuh_tempo']) ? $_POST['tgl_jatuh_tempo'] : '';
    $jumlah                  = isset($_POST['jumlah']) ? (int)$_POST['jumlah'] : 1;
    $jenis_peminjaman        = 'offline';
    $status                  = 'dipinjam';
    
    $keterangan              = "ID Barang: " . $id_barang . " | Jumlah: " . $jumlah;

    // 2. Query INSERT (KOLOM id_pengguna DIHAPUS DARI DAFTAR INSERT)
    $query = "INSERT INTO peminjaman 
              (kode_peminjaman, nama_peminjam, tanggal_peminjaman, tanggal_rencana_kembali, jenis_peminjaman, status, keterangan) 
              VALUES 
              ('$kode_peminjaman', '$nama_peminjam', '$tanggal_peminjaman', '$tanggal_rencana_kembali', '$jenis_peminjaman', '$status', '$keterangan')";

    $simpan = mysqli_query($koneksi, $query);

    // 3. Jika berhasil, kurangi stok barang
    if ($simpan) {
        if (!empty($id_barang)) {
            mysqli_query($koneksi, "UPDATE barang SET stok = stok - $jumlah WHERE id_barang = '$id_barang'");
        }

        echo "<script>
            alert('Peminjaman Berhasil Disimpan!');
            window.location.href = '" . $_SERVER['PHP_SELF'] . "';
        </script>";
        exit;
    } else {
        echo "<script>
            alert('Gagal menyimpan data! Error: " . addslashes(mysqli_error($koneksi)) . "');
        </script>";
    }
}
?>