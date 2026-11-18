<?php
include '../../koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Peminjaman</title>
</head>
<body>
    <div style="width: 80%; margin: 20px auto; font-family: sans-serif;">
        <h2>Daftar Peminjaman Kamera & Tripod</h2>
        <hr>
        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th>No</th>
                    <th>ID Peminjaman</th>
                    <th>Nama Peminjam</th>
                    <th>ID Barang</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                $query = mysqli_query($koneksi, "SELECT * FROM peminjaman");
                while ($row = mysqli_fetch_array($query)) { 
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $row['id_peminjaman']; ?></td>
                    <td><?php echo $row['nama_peminjam']; ?></td>
                    <td><?php echo $row['id_barang']; ?></td>
                    <td><?php echo isset($row['status']) ? $row['status'] : 'Dipinjam'; ?></td>
                    <td>
                        <!-- Jalur relatif langsung ke folder Pengembalian -->
                        <a href="../Pengembalian/proses_kembali.php?id=<?php echo $row['id_peminjaman']; ?>" 
                           onclick="return confirm('Yakin ingin mengembalikan barang ini?');" 
                           style="background-color: #d9534f; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px;">
                           Kembalikan
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>


<?php
include '../../koneksi.php';

if (isset($_GET['id'])) {
    $id_peminjaman = $_GET['id'];

    // Update status peminjaman jadi Dikembalikan
    $update = mysqli_query($koneksi, "UPDATE peminjaman SET status = 'Dikembalikan' WHERE id_peminjaman = '$id_peminjaman'");

    if ($update) {
        // Kembali ke halaman peminjaman
        header("Location: ../Peminjaman/peminjaman.php");
        exit();
    } else {
        echo "Gagal update database: " . mysqli_error($koneksi);
    }
} else {
    header("Location: ../Peminjaman/peminjaman.php");
    exit();
}
?>