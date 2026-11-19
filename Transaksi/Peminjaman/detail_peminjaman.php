<?php
// Koneksi database langsung yang aman dan pasti ketemu
$koneksi = mysqli_connect("localhost", "root", "", "project");
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

$id = isset($_GET['id']) ? $_GET['id'] : '';
$query = mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE id_peminjaman = '$id'");
$data = mysqli_fetch_array($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Peminjaman</title>
</head>
<body style="font-family: sans-serif; background-color: #FFFBEB; padding: 20px;">
    <div style="width: 600px; margin: 30px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        
        <h2 style="text-align: center; color: #8174A0; margin-bottom: 20px;">Detail Peminjaman Alat</h2>
        <hr style="border: 0; border-top: 1px solid #ddd; margin-bottom: 20px;">

        <?php if ($data) { ?>
            <table style="width: 100%; line-height: 2; font-size: 15px;">
                <tr>
                    <td style="font-weight: bold; color: #555; width: 40%;">ID Peminjaman</td>
                    <td>: <?php echo $data['id_peminjaman']; ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold; color: #555;">Nama Peminjam</td>
                    <td>: <?php echo $data['nama_peminjam']; ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold; color: #555;">ID Barang / Kamera</td>
                    <td>: <?php echo isset($data['id_barang']) ? $data['id_barang'] : '-'; ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold; color: #555;">Status</td>
                    <td>: <span style="font-weight: bold; color: #8174A0;"><?php echo isset($data['status']) ? $data['status'] : 'Dipinjam'; ?></span></td>
                </tr>
            </table>
        <?php } else { ?>
            <p style="text-align: center; color: #d9534f; font-weight: bold;">Data peminjaman tidak ditemukan.</p>
        <?php } ?>

        <div style="text-align: center; margin-top: 25px;">
            <a href="peminjaman.php" style="background-color: #8174A0; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">
                ← Kembali ke Daftar Peminjaman
            </a>
        </div>

    </div>
</body>
</html>