<?php
// Koneksi database langsung ke database 'project'
$koneksi = mysqli_connect("localhost", "root", "", "project");
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Proses update langsung ketika tombol 'Kembalikan' diklik
if (isset($_GET['aksi']) && $_GET['aksi'] == 'proses') {
    $id = $_GET['id'];
    mysqli_query($koneksi, "UPDATE peminjaman SET status = 'Dikembalikan' WHERE id_peminjaman = '$id'");
    header("Location: pengembalian.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengembalian Kamera & Tripod</title>
</head>
<body style="font-family: sans-serif; background-color: #FFFBEB; padding: 20px;">
    <div style="width: 80%; margin: 20px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        
        <h2 style="color: #8174A0;">Data Pengembalian Kamera & Tripod</h2>
        <hr style="border: 0; border-top: 1px solid #ddd; margin-bottom: 20px;">

        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th>No</th>
                    <th>ID Peminjaman</th>
                    <th>Nama Peminjam</th>
                    <th>ID Barang / Kamera</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                $query = mysqli_query($koneksi, "SELECT * FROM peminjaman");
                
                if ($query && mysqli_num_rows($query) > 0) {
                    while ($row = mysqli_fetch_array($query)) { 
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo isset($row['id_peminjaman']) ? $row['id_peminjaman'] : ''; ?></td>
                    <td><?php echo isset($row['nama_peminjam']) ? $row['nama_peminjam'] : ''; ?></td>
                    <td>
                        <?php 
                        if (isset($row['id_barang'])) {
                            echo $row['id_barang'];
                        } elseif (isset($row['nama_barang'])) {
                            echo $row['nama_barang'];
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                    <td>
                        <?php 
                        if (isset($row['status'])) {
                            echo $row['status']; 
                        } else {
                            echo 'Dipinjam';
                        }
                        ?>
                    </td>
                    <td>
                        <?php if (!isset($row['status']) || $row['status'] != 'Dikembalikan') { ?>
                            <a href="pengembalian.php?aksi=proses&id=<?php echo $row['id_peminjaman']; ?>" 
                               onclick="return confirm('Yakin ingin memproses pengembalian barang ini?');" 
                               style="background-color: #d9534f; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; display: inline-block;">
                               Kembalikan
                            </a>
                        <?php } else { ?>
                            <span style="color: green; font-weight: bold;">Selesai</span>
                        <?php } ?>
                    </td>
                </tr>
                <?php 
                    }
                } else {
                    echo '<tr><td colspan="6" style="text-align: center;">Belum ada data peminjaman.</td></tr>';
                }
                ?>
            </tbody>
        </table>

    </div>
</body>
</html>