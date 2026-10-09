<?php
require_once 'koneksi.php';

$query = "SELECT p.*, u.nama_user, b.nama_barang 
          FROM peminjaman p
          JOIN users u ON p.id_user = u.id_user
          JOIN barang b ON p.id_barang = b.id_barang
          ORDER BY p.id_peminjaman DESC";
$data = $pdo->query($query)->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman Barang</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #FFFBEB; 
            margin: 0; 
            padding: 40px 20px; 
            color: #333333;
        }
        .container { 
            max-width: 1000px; 
            margin: 0 auto; 
            background: #EFB6C8; 
            padding: 0; 
            border-radius: 16px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.08); 
            overflow: hidden;
        }
        .header-section {
            background-color: #8174A0;
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        h2 { 
            color: #ffffff; 
            margin: 0; 
            font-size: 1.4rem;
            font-weight: 600;
        }
        .btn-tambah { 
            background-color: #A888B5; 
            color: #ffffff; 
            text-decoration: none; 
            padding: 10px 18px; 
            border-radius: 10px; 
            font-weight: 600; 
            font-size: 0.95rem;
            transition: background-color 0.2s ease;
            display: inline-block;
        }
        .btn-tambah:hover {
            background-color: #6C5F8A;
        }
        .table-container {
            padding: 25px;
        }
        table { 
            width: 100%; 
            border-collapse: separate; 
            border-spacing: 0 10px;
        }
        th { 
            color: #2D2738; 
            font-weight: 700;
            padding: 12px 16px;
            text-align: left;
            font-size: 0.95rem;
        }
        td { 
            padding: 14px 16px; 
            text-align: left; 
            font-size: 0.95rem;
            color: #ffffff;
            background-color: #A888B5;
        }
        td:first-child {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }
        td:last-child {
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }
        tbody tr {
            transition: transform 0.1s ease;
        }
        tbody tr:hover td { 
            background-color: #9674A3; 
        }
        .badge { 
            padding: 6px 14px; 
            border-radius: 8px; 
            font-size: 0.85rem; 
            font-weight: 600; 
            display: inline-block;
            text-align: center;
        }
        .dipinjam { 
            background-color: #8174A0; 
            color: #ffffff; 
        }
        .dikembalikan { 
            background-color: #FFFBEB; 
            color: #512B3A; 
        }
        .btn-kembali { 
            background-color: #8174A0; 
            color: #ffffff; 
            padding: 8px 16px; 
            text-decoration: none; 
            border-radius: 8px; 
            font-size: 0.85rem; 
            font-weight: 600; 
            transition: background-color 0.2s ease;
            display: inline-block;
        }
        .btn-kembali:hover { 
            background-color: #6C5F8A; 
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header-section">
        <h2>Riwayat Peminjaman Barang</h2>
        <a href="peminjaman.php" class="btn-tambah">+ Tambah Peminjaman</a>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Peminjam</th>
                    <th>Barang</th>
                    <th class="text-center">Jumlah</th>
                    <th>Tgl Pinjam</th>
                    <th>Tgl Kembali (Rencana)</th>
                    <th class="text-center">Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($data) > 0): ?>
                    <?php $no = 1; foreach ($data as $row): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['nama_user']); ?></td>
                            <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                            <td class="text-center"><?= $row['jumlah']; ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tgl_pinjam'])); ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tgl_kembali_rencana'])); ?></td>
                            <td class="text-center">
                                <span class="badge <?= strtolower($row['status']); ?>">
                                    <?= $row['status']; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row['status'] === 'Dipinjam'): ?>
                                    <a href="pengembalian.php?id=<?= $row['id_peminjaman']; ?>" class="btn-kembali">Kembalikan</a>
                                <?php else: ?>
                                    <span style="color: #E2D9EE; margin-left: 10px;">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 25px; color: #ffffff; background-color: #A888B5; border-radius: 10px;">Belum ada data peminjaman.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>