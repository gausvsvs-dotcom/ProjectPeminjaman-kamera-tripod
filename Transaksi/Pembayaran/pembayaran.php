<?php
session_start();

// Memanggil koneksi database dari folder Peminjaman
require_once "../Peminjaman/koneksi.php";

// Ambil ID / Kode Peminjaman dari URL atau Session
$kode_peminjaman = isset($_GET['id']) ? $_GET['id'] : (isset($_SESSION['id_peminjaman']) ? $_SESSION['id_peminjaman'] : null);

$data_peminjaman = null;
$detail_items = [];
$total_bayar = 0;

// Jika ada ID peminjaman, ambil data asli dari Database
if ($kode_peminjaman && isset($koneksi)) {
    // Query untuk mengambil data peminjaman
    $query = mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE id_peminjaman = '$kode_peminjaman' OR kode_peminjaman = '$kode_peminjaman'");
    
    if ($query && mysqli_num_rows($query) > 0) {
        $data_peminjaman = mysqli_fetch_assoc($query);
        
        // Query untuk mengambil detail barang yang dipinjam
        $query_detail = mysqli_query($koneksi, "SELECT * FROM detail_peminjaman WHERE id_peminjaman = '" . $data_peminjaman['id_peminjaman'] . "'");
        
        if ($query_detail && mysqli_num_rows($query_detail) > 0) {
            while ($row = mysqli_fetch_assoc($query_detail)) {
                $detail_items[] = $row;
                $total_bayar += ($row['harga_sewa'] * $row['jumlah'] * $row['lama_sewa']);
            }
        }
    }
}

// Proses Simpan Pembayaran
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode   = $_POST['kode_peminjaman'];
    $metode = $_POST['metode_bayar'] ?? 'Transfer Bank';
    
    // Simpan status pembayaran jika koneksi tersedia
    if (isset($koneksi) && $kode) {
        mysqli_query($koneksi, "UPDATE peminjaman SET status_pembayaran = 'Lunas' WHERE kode_peminjaman = '$kode' OR id_peminjaman = '$kode'");
    }

    echo "<script>
            alert('Pembayaran untuk kode " . $kode . " berhasil diproses!');
            window.location.href = '../../riwayat.php';
          </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Peminjaman</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #FFFBEB;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .card {
            max-width: 550px;
            background-color: #ffffff;
            margin: 20px auto;
            padding: 0;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            border-top: 8px solid #8174A0;
        }
        .header-box {
            background-color: #8174A0;
            color: #ffffff;
            padding: 20px 25px;
        }
        .header-box h2 {
            margin: 0 0 6px 0;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .header-box p {
            margin: 0;
            font-size: 13px;
            opacity: 0.9;
        }
        .body-content {
            padding: 25px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
            margin-bottom: 20px;
        }
        .info-label {
            font-size: 11px;
            color: #777;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .info-code {
            font-size: 16px;
            font-weight: bold;
            color: #222;
            margin-top: 2px;
        }
        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .section-title {
            font-size: 15px;
            font-weight: bold;
            color: #333;
            margin: 20px 0 12px 0;
        }
        .item-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 20px;
        }
        .item-row {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .item-img {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            background-color: #f3ebf7;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            border: 1px solid #e2d9ec;
            flex-shrink: 0;
        }
        .item-info {
            flex-grow: 1;
        }
        .item-title {
            font-weight: bold;
            font-size: 14px;
            color: #222;
            margin: 0 0 3px 0;
        }
        .item-sub {
            font-size: 12px;
            color: #777;
            margin: 0;
        }
        .item-price {
            font-weight: bold;
            font-size: 14px;
            color: #222;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #f0f0f0;
            margin-top: 15px;
        }
        .total-label {
            font-size: 15px;
            color: #444;
        }
        .total-amount {
            font-size: 20px;
            font-weight: bold;
            color: #8174A0;
        }

        .empty-box {
            text-align: center;
            padding: 30px 10px;
            color: #666;
        }
        .empty-box p {
            margin-bottom: 20px;
            font-size: 14px;
        }

        .option-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
        }
        .option-card {
            display: flex;
            align-items: flex-start;
            padding: 12px 14px;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            cursor: pointer;
            background-color: #fafafa;
        }
        .option-card input[type="radio"] {
            margin-top: 3px;
            margin-right: 12px;
            accent-color: #8174A0;
        }
        .option-title {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin: 0 0 2px 0;
        }
        .option-desc {
            font-size: 12px;
            color: #666;
            margin: 0;
        }
        .sub-options {
            margin-left: 28px;
            margin-top: 10px;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .sub-option-item {
            display: flex;
            align-items: center;
            font-size: 13px;
            color: #444;
            cursor: pointer;
        }
        .sub-option-item input {
            margin-right: 10px;
            accent-color: #8174A0;
        }

        .file-input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #e0e0e0;
            border-radius: 20px;
            background-color: #fafafa;
            font-size: 13px;
            box-sizing: border-box;
            color: #555;
            margin-bottom: 8px;
        }
        .checkbox-container {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin: 20px 0;
            font-size: 13px;
            color: #444;
            cursor: pointer;
        }
        .checkbox-container input {
            margin-top: 2px;
            accent-color: #8174A0;
        }

        .btn-submit {
            width: 100%;
            background-color: #8174A0;
            color: white;
            border: none;
            padding: 14px;
            font-size: 15px;
            font-weight: bold;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.2s ease;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            box-sizing: border-box;
        }
        .btn-submit:hover {
            background-color: #A888B5;
        }
    </style>
</head>
<body>

<div class="card">
    <div class="header-box">
        <h2>📷 Pembayaran</h2>
        <p>Selesaikan pembayaran peminjaman kamera dan tripod Anda.</p>
    </div>

    <div class="body-content">
        <?php if (!$kode_peminjaman || !$data_peminjaman): ?>
            <!-- TAMPILAN JIKA BELUM ADA DATA PEMINJAMAN -->
            <div class="empty-box">
                <p>Belum ada data transaksi peminjaman yang dipilih atau data belum tersedia.</p>
                <a href="../Peminjaman/peminjaman.php" class="btn-submit">Ke Menu Peminjaman</a>
            </div>
        <?php else: ?>
            <!-- TAMPILAN JIKA DATA ADA DARI DATABASE -->
            <div class="info-row">
                <div>
                    <div class="info-label">Kode Peminjaman</div>
                    <div class="info-code"><?php echo htmlspecialchars($data_peminjaman['kode_peminjaman'] ?? $kode_peminjaman); ?></div>
                </div>
                <span class="badge-warning"><?php echo htmlspecialchars($data_peminjaman['status_pembayaran'] ?? 'Menunggu pembayaran'); ?></span>
            </div>

            <!-- Ringkasan Peminjaman dari Database -->
            <div class="section-title">Ringkasan Peminjaman</div>
            <div class="item-list">
                <?php if (!empty($detail_items)): ?>
                    <?php foreach ($detail_items as $item): 
                        $subtotal = $item['harga_sewa'] * $item['jumlah'] * $item['lama_sewa'];
                    ?>
                        <div class="item-row">
                            <div class="item-img">📦</div>
                            <div class="item-info">
                                <p class="item-title"><?php echo htmlspecialchars($item['nama_barang']); ?></p>
                                <p class="item-sub"><?php echo $item['lama_sewa']; ?> hari × Rp<?php echo number_format($item['harga_sewa'], 0, ',', '.'); ?>/hari</p>
                            </div>
                            <div class="item-price">Rp<?php echo number_format($subtotal, 0, ',', '.'); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="font-size: 13px; color: #888;">Detail barang belum diinputkan.</p>
                <?php endif; ?>
            </div>

            <!-- Total Pembayaran -->
            <div class="total-row">
                <span class="total-label">Total pembayaran</span>
                <span class="total-amount">Rp<?php echo number_format($total_bayar, 0, ',', '.'); ?></span>
            </div>

            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="kode_peminjaman" value="<?php echo htmlspecialchars($kode_peminjaman); ?>">

                <!-- Metode Pembayaran -->
                <div class="section-title">Metode Pembayaran</div>
                <div class="option-group">
                    <label class="option-card">
                        <input type="radio" name="metode_bayar" value="Transfer Bank" checked>
                        <div>
                            <div class="option-title">🏦 Transfer Bank</div>
                            <div class="option-desc">Transfer ke rekening penyedia rental</div>
                        </div>
                    </label>

                    <label class="option-card">
                        <input type="radio" name="metode_bayar" value="QRIS">
                        <div>
                            <div class="option-title">📱 QRIS</div>
                            <div class="option-desc">Bayar melalui aplikasi yang mendukung QRIS</div>
                        </div>
                    </label>

                    <label class="option-card">
                        <input type="radio" name="metode_bayar" value="Tunai">
                        <div>
                            <div class="option-title">💵 Tunai</div>
                            <div class="option-desc">Pembayaran langsung sesuai prosedur rental</div>
                        </div>
                    </label>
                </div>

                <!-- Rekening Tujuan -->
                <div class="section-title">Rekening tujuan</div>
                <div class="sub-options">
                    <label class="sub-option-item">
                        <input type="radio" name="rekening" value="BCA" checked> BCA — 1234567890 (a.n. Rental Kamera)
                    </label>
                    <label class="sub-option-item">
                        <input type="radio" name="rekening" value="BRI"> BRI — 0987654321 (a.n. Rental Kamera)
                    </label>
                </div>

                <!-- Bukti Pembayaran -->
                <div class="section-title">Bukti Pembayaran</div>
                <input type="file" name="bukti_bayar" class="file-input" required>

                <!-- Checkbox Konfirmasi -->
                <label class="checkbox-container">
                    <input type="checkbox" required>
                    <span>Saya memastikan informasi pembayaran yang saya berikan benar.</span>
                </label>

                <!-- Tombol Submit -->
                <button type="submit" class="btn-submit">Lanjutkan Pembayaran →</button>
            </form>
        <?php endif; ?>
    </div>
</div>

</body>
</html>