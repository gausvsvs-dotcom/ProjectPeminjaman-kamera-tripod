<?php
require_once('koneksi.php');

$query = mysqli_query($conn, "SELECT peminjaman.*, barang.nama_barang 
                              FROM peminjaman 
                              LEFT JOIN barang ON peminjaman.id_barang = barang.id_barang 
                              ORDER BY peminjaman.id_peminjaman DESC");
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background-color: #FFFBEB; /* Soft Cream */
            padding: 40px 20px;
            margin: 0;
            display: flex;
            justify-content: center;
        }
        .card {
            width: 100%;
            max-width: 900px;
            background-color: #EFB6C8; /* Soft Pink */
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .card-header {
            background-color: #8174A0; /* Purplish Gray */
            padding: 20px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card-header h2 {
            color: #ffffff;
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }
        .btn-tambah {
            background-color: #A888B5; /* Muted Purple */
            color: #ffffff;
            padding: 10px 18px;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            transition: background-color 0.2s;
        }
        .btn-tambah:hover {
            background-color: #6C5F8A;
        }
        .card-body {
            padding: 24px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
        }
        th, td {
            padding: 14px 16px;
            text-align: left;
            font-size: 14px;
        }
        th {
            background-color: #8174A0; /* Purplish Gray */
            color: #ffffff;
            font-weight: 600;
        }
        td {
            border-bottom: 1px solid #f0e6ed;
            color: #333333;
        }
        tr:last-child td {
            border-bottom: none;
        }
        .badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background-color: #A888B5; /* Muted Purple */
            color: #ffffff;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="card">
    <div class="card-header">
        <h2>Riwayat Peminjaman Barang</h2>
        <a href="Peminjaman/peminjaman.php" class="btn-tambah">+ Tambah Peminjaman</a>
    </div>
    <div class="card-body">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Peminjam</th>
                    <th>Barang</th>
                    <th>No. HP</th>
                    <th>Jumlah</th>
                    <th>Tgl Pinjam</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                if ($query && mysqli_num_rows($query) > 0) :
                    while ($row = mysqli_fetch_assoc($query)) : 
                ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['nama_peminjam']); ?></td>
                        <td><?= htmlspecialchars($row['nama_barang'] ?? 'Barang Dihapus'); ?></td>
                        <td><?= htmlspecialchars($row['no_hp']); ?></td>
                        <td><?= $row['jumlah']; ?> Unit</td>
                        <td><?= date('d-m-Y', strtotime($row['tgl_pinjam'])); ?></td>
                        <td><span class="badge"><?= htmlspecialchars($row['status']); ?></span></td>
                    </tr>
                <?php 
                    endwhile;
                else :
                ?>
                    <tr>
                        <td colspan="7" style="text-align:center; color:#888; padding: 20px;">Belum ada data peminjaman.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>