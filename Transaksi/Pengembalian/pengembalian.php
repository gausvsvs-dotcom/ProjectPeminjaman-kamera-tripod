<?php
// Menangkap data nama dan alat yang dikirimkan dari riwayat.php
$nama_peminjam = isset($_GET['nama']) ? $_GET['nama'] : 'Tamu';
$nama_alat     = isset($_GET['alat']) ? $_GET['alat'] : 'Kamera & Tripod';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pengembalian Alat</title>
    <style>
        body { background-color: #FFFBEB; font-family: 'Segoe UI', Tahoma, sans-serif; padding: 40px 20px; margin: 0; }
        .card { max-width: 550px; margin: 0 auto; background: #fff; padding: 35px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border-top: 6px solid #8174A0; }
        h2 { color: #8174A0; margin-top: 0; text-align: center; margin-bottom: 25px; font-size: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 14px; font-weight: 600; color: #555; margin-bottom: 8px; }
        input, select { width: 100%; padding: 11px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; background-color: #fafafa; font-size: 14px; }
        input:focus, select:focus { border-color: #8174A0; outline: none; background-color: #fff; }
        .btn { display: block; width: 100%; background: #8174A0; color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 600; font-size: 15px; cursor: pointer; margin-top: 25px; transition: background 0.2s; }
        .btn:hover { background: #6a5d88; }
        .back-link { text-align: center; margin-top: 15px; }
        .back-link a { font-size: 13px; color: #8174A0; text-decoration: none; }
        .back-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="card">
    <h2>Form Pengembalian Alat</h2>
    <form action="riwayat.php" method="POST">
        <div class="form-group">
            <label>Nama Peminjam</label>
            <input type="text" name="nama_peminjam" value="<?= htmlspecialchars($nama_peminjam); ?>" readonly required>
        </div>
        <div class="form-group">
            <label>Alat yang Dikembalikan</label>
            <input type="text" name="nama_alat" value="<?= htmlspecialchars($nama_alat); ?>" readonly required>
        </div>
        <div class="form-group">
            <label>Tanggal Pengembalian Aktual (Hari Ini)</label>
            <input type="date" name="tgl_dikembalikan" value="<?= date('Y-m-d'); ?>" required>
        </div>
        <div class="form-group">
            <label>Kondisi Alat Saat Dikembalikan</label>
            <select name="kondisi_alat" required>
                <option value="Baik">Baik & Lengkap</option>
                <option value="Ada Kerusakan Ringan">Ada Kerusakan Ringan</option>
                <option value="Hilang / Rusak Parah">Hilang / Rusak Parah</option>
            </select>
        </div>
        <button type="submit" class="btn">Proses Pengembalian Selesai</button>
    </form>

    <div class="back-link">
        <a href="riwayat.php">← Kembali ke Riwayat Transaksi</a>
    </div>
</div>
</body>
</html>