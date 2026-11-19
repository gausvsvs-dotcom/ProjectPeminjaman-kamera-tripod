<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Peminjaman Kamera & Tripod</title>
    <style>
        body { background-color: #FFFBEB; font-family: 'Segoe UI', Tahoma, sans-serif; padding: 40px 20px; margin: 0; }
        .card { max-width: 550px; margin: 0 auto; background: #fff; padding: 35px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border-top: 6px solid #8174A0; }
        h2 { color: #8174A0; margin-top: 0; text-align: center; margin-bottom: 25px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 14px; font-weight: 600; color: #555; margin-bottom: 8px; }
        input, select, textarea { width: 100%; padding: 11px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; background-color: #fafafa; font-size: 14px; }
        input:focus, select:focus, textarea:focus { border-color: #8174A0; outline: none; background-color: #fff; }
        .btn { display: block; width: 100%; background: #8174A0; color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 600; font-size: 15px; cursor: pointer; margin-top: 25px; transition: background 0.2s; }
        .btn:hover { background: #6a5d88; }
    </style>
</head>
<body>
<div class="card">
    <h2>Form Peminjaman Kamera & Tripod</h2>
    <!-- Mengarah keluar folder Peminjaman menuju konfirmasi.php di folder Transaksi -->
    <form action="../konfirmasi.php" method="POST">
        <div class="form-group">
            <label>Nama Peminjam</label>
            <input type="text" name="nama_peminjam" placeholder="Masukkan nama lengkap" required>
        </div>
        <div class="form-group">
            <label>No HP</label>
            <input type="text" name="no_hp" placeholder="Contoh: 0821xxxxxxx" required>
        </div>
        <div class="form-group">
            <label>Pilihan Kamera & Tripod</label>
            <select name="nama_alat" required>
                <option value="Kamera DSLR Canon EOS 3000D + Tripod Standart">Kamera DSLR Canon EOS 3000D + Tripod Standart (Rp 100.000/hari)</option>
                <option value="Kamera Mirrorless Sony A6000 + Tripod Pro">Kamera Mirrorless Sony A6000 + Tripod Pro (Rp 150.000/hari)</option>
                <option value="Tripod Saja (Takara / Beike)">Tripod Saja (Takara / Beike) (Rp 30.000/hari)</option>
            </select>
        </div>
        <div class="form-group">
            <label>Durasi Pinjam (Hari)</label>
            <input type="number" name="durasi" min="1" value="1" required>
        </div>
        <div class="form-group">
            <label>Tanggal Peminjaman</label>
            <input type="date" name="tgl_pinjam" required>
        </div>
        <div class="form-group">
            <label>Tanggal Jatuh Tempo (Rencana Kembali)</label>
            <input type="date" name="tgl_kembali" required>
        </div>
        <div class="form-group">
            <label>Jenis Peminjaman</label>
            <select name="jenis_pinjam">
                <option value="Offline">Offline (Datang Langsung)</option>
                <option value="Online">Online (Antar / Kurir)</option>
            </select>
        </div>
        <div class="form-group">
            <label>Alamat</label>
            <textarea name="alamat" rows="3" placeholder="Masukkan alamat peminjam" required></textarea>
        </div>
        <button type="submit" class="btn">Lanjut ke Konfirmasi</button>
    </form>
</div>
</body>
</html>