
<?php
session_start();
require_once "koneksi.php";

function aman($data)
{
    return htmlspecialchars((string)($data ?? '-'), ENT_QUOTES, 'UTF-8');
}

function formatTanggal($tanggal)
{
    if (empty($tanggal) || $tanggal === '0000-00-00') {
        return '-';
    }

    $timestamp = strtotime($tanggal);
    return $timestamp ? date('d-m-Y', $timestamp) : '-';
}

// Ambil seluruh riwayat peminjaman
$query = "
    SELECT 
        p.id_peminjaman,
        p.nama_peminjam,
        p.tgl_peminjaman,
        p.tgl_jatuh_tempo,
        p.status
    FROM peminjaman p
    ORDER BY p.id_peminjaman DESC
";

$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Gagal mengambil data riwayat peminjaman: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Riwayat Peminjaman</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="style_peminjaman.css">
</head>

<body>

<div class="container-detail">

    <div class="detail-header">
        <div class="header-icon">
            <i class="bi bi-clock-history"></i>
        </div>

        <div>
            <h2>Riwayat Peminjaman</h2>
            <p>Daftar seluruh riwayat transaksi peminjaman barang</p>
        </div>
    </div>

    <div class="detail-body">

        <div class="table-responsive">
            <table class="table-riwayat">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Peminjam</th>
                        <th>Tanggal Peminjaman</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (mysqli_num_rows($result) > 0): ?>

                    <?php $no = 1; ?>

                    <?php while ($row = mysqli_fetch_assoc($result)): ?>

                        <?php
                        $status = $row['status'] ?? '-';

                        if ($status === 'Selesai' || $status === 'Dikembalikan') {
                            $classStatus = 'status-dikembalikan';
                            $labelStatus = 'Dikembalikan';
                        } elseif ($status === 'Dipinjam') {
                            $classStatus = 'status-dipinjam';
                            $labelStatus = 'Dipinjam';
                        } else {
                            $classStatus = 'status-menunggu';
                            $labelStatus = $status;
                        }
                        ?>

                        <tr>
                            <td><?= $no++; ?></td>

                            <td>
                                <?= aman($row['nama_peminjam']); ?>
                            </td>

                            <td>
                                <?= formatTanggal($row['tgl_peminjaman']); ?>
                            </td>

                            <td>
                                <span class="status-badge <?= aman($classStatus); ?>">
                                    <?= aman($labelStatus); ?>
                                </span>
                            </td>

                            <td>
                                <a href="detail_peminjaman.php?id=<?= aman($row['id_peminjaman']); ?>"
                                   class="btn-detail">
                                    <i class="bi bi-eye"></i>
                                    Detail
                                </a>
                            </td>
                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="5" class="data-kosong">
                            <i class="bi bi-inbox"></i>
                            <p>Belum ada riwayat peminjaman.</p>
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>
        </div>

    </div>

</div>

</body>
</html>
