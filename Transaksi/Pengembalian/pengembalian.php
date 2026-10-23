<?php
// Memanggil file koneksi.php dari folder Peminjaman sebelah
require_once "../Peminjaman/koneksi.php";

// -------------------------------------------------------------
// LOGIKA PEMROSESAN FORM PENGEMBALIAN
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Tangkap data dari form
    $id_peminjaman  = mysqli_real_escape_string($conn, $_POST['id_peminjaman']);
    $tgl_kembali    = mysqli_real_escape_string($conn, $_POST['tgl_kembali']);
    $status_barang  = mysqli_real_escape_string($conn, $_POST['status_barang']);
    $denda          = mysqli_real_escape_string($conn, $_POST['denda']);
    
    // Status baru setelah barang dikembalikan
    $status_peminjaman = 'Selesai'; 

    // Query update status peminjaman di database
    $query = "UPDATE peminjaman SET 
              status = '$status_peminjaman' 
              WHERE id_peminjaman = '$id_peminjaman'";

    $result = mysqli_query($conn, $query);

    if ($result) {
        // Setelah klik Oke, otomatis memuat ulang halaman ini tanpa error 404
        echo "<script>
                alert('Pengembalian berhasil diproses!');
                window.location.href = 'pengembalian.php';
              </script>";
        exit();
    } else {
        echo "<script>
                alert('Gagal memproses pengembalian: " . mysqli_error($conn) . "');
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Transaksi Pengembalian</title>
    <!-- Styling Sederhana & Elegan -->
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #FFFBEB;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            background-color: #ffffff;
            margin: 40px auto;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border-top: 6px solid #8174A0;
        }
        h2 {
            text-align: center;
            color: #8174A0;
            margin-bottom: 25px;
        }
        .form-group {
            margin-bottom: 18px;
        }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #555;
        }
        input, select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 14px;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #A888B5;
        }
        .btn-submit {
            width: 100%;
            background-color: #8174A0;
            color: white;
            border: none;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s ease;
            margin-top: 10px;
        }
        .btn-submit:hover {
            background-color: #A888B5;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Form Pengembalian</h2>
    
    <!-- Action diubah ke "" agar form memproses ke dirinya sendiri -->
    <form action="" method="POST">
        <div class="form-group">
            <label for="id_peminjaman">ID Peminjaman / Kode Transaksi</label>
            <input type="text" id="id_peminjaman" name="id_peminjaman" placeholder="Masukkan ID Peminjaman" required>
        </div>

        <div class="form-group">
            <label for="tgl_kembali">Tanggal Pengembalian</label>
            <input type="date" id="tgl_kembali" name="tgl_kembali" value="<?php echo date('Y-m-d'); ?>" required>
        </div>

        <div class="form-group">
            <label for="status_barang">Kondisi Barang</label>
            <select id="status_barang" name="status_barang" required>
                <option value="Baik">Baik / Normal</option>
                <option value="Rusak">Rusak</option>
                <option value="Hilang">Hilang</option>
            </select>
        </div>

        <div class="form-group">
            <label for="denda">Denda (Rp)</label>
            <input type="number" id="denda" name="denda" value="0" min="0">
        </div>

        <button type="submit" class="btn-submit">Proses Pengembalian</button>
    </form>
</div>

</body>
</html>