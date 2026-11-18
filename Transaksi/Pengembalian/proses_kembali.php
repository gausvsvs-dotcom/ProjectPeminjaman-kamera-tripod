<?php
$koneksi = mysqli_connect("localhost", "root", "", "project");
$id = isset($_GET['id']) ? $_GET['id'] : '';

// Ambil data peminjaman berdasarkan ID untuk ditampilkan di form
$query = mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE id_peminjaman = '$id'");
$data = mysqli_fetch_array($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Pengembalian Alat</title>
</head>
<body style="font-family: sans-serif; background-color: #FFFBEB; padding: 20px;">
    <div style="width: 500px; margin: 30px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        <h2 style="text-align: center; color: #8174A0;">Form Pengembalian Alat</h2>
        
        <!-- Form mengarah langsung ke proses_kembali.php di folder yang sama -->
        <form action="proses_kembali.php" method="POST">
            <!-- Kirim ID peminjaman secara hidden -->
            <input type="hidden" name="id_peminjaman" value="<?php echo $id; ?>">

            <label style="color: #555; font-weight: bold;">Nama Peminjam</label>
            <input type="text" value="<?php echo isset($data['nama_peminjam']) ? $data['nama_peminjam'] : ''; ?>" readonly style="width: 100%; padding: 10px; margin: 8px 0 15px 0; background: #f9f9f9; border: 1px solid #ccc; border-radius: 6px;">

            <label style="color: #555; font-weight: bold;">Alat yang Dikembalikan</label>
            <input type="text" value="<?php echo isset($data['id_barang']) ? $data['id_barang'] : ''; ?>" readonly style="width: 100%; padding: 10px; margin: 8px 0 15px 0; background: #f9f9f9; border: 1px solid #ccc; border-radius: 6px;">

            <label style="color: #555; font-weight: bold;">Tanggal Pengembalian Aktual (Hari Ini)</label>
            <input type="text" value="<?php echo date('d M Y'); ?>" readonly style="width: 100%; padding: 10px; margin: 8px 0 15px 0; background: #f9f9f9; border: 1px solid #ccc; border-radius: 6px;">

            <label style="color: #555; font-weight: bold;">Kondisi Alat Saat Dikembalikan</label>
            <select name="kondisi" style="width: 100%; padding: 10px; margin: 8px 0 20px 0; border: 1px solid #ccc; border-radius: 6px;">
                <option value="Baik & Lengkap">Baik & Lengkap</option>
                <option value="Ada Kerusakan / Kurang">Ada Kerusakan / Kurang</option>
            </select>

            <button type="submit" name="proses_selesai" style="width: 100%; background-color: #8174A0; color: white; padding: 12px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
                Proses Pengembalian Selesai
            </button>
        </form>
    </div>
</body>
</html>
```[cite: 7, 8]

Dan pastikan file **`proses_kembali.php`** di sebelah file form ini berisi kode penangkap data POST seperti ini[cite: 7, 8]:

```php
<?php
$koneksi = mysqli_connect("localhost", "root", "", "project");

if (isset($_POST['proses_selesai']) || isset($_GET['id'])) {
    // Bisa menerima dari form POST atau dari link GET langsung
    $id_peminjaman = isset($_POST['id_peminjaman']) ? $_POST['id_peminjaman'] : $_GET['id'];

    // Update status di database menjadi Dikembalikan
    $update = mysqli_query($koneksi, "UPDATE peminjaman SET status = 'Dikembalikan' WHERE id_peminjaman = '$id_peminjaman'");

    if ($update) {
        header("Location: pengembalian.php");
        exit();
    } else {
        echo "Gagal update: " . mysqli_error($koneksi);
    }
} else {
    header("Location: pengembalian.php");
    exit();
}
?>