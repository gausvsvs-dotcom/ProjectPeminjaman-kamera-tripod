
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

// Ambil ID peminjaman dari URL
$id_peminjaman = $_GET['id'] ?? '';

$data = null;
$barang = [];
$error = '';

if (empty($id_peminjaman) || !ctype_digit((string) $id_peminjaman)) {
    $error = "ID peminjaman tidak valid atau tidak ditemukan.";
} else {

    // Ambil informasi peminjam
    $query = "
        SELECT *
        FROM peminjaman
        WHERE id_peminjaman = ?
    ";

    $stmt = mysqli_prepare($koneksi, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id_peminjaman);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        if (!$data) {
            $error = "Data peminjaman tidak ditemukan.";
        }
    } else {
        $error = "Gagal mengambil data peminjaman.";
    }

    // Ambil barang yang dipinjam
    if (!$error) {

        $queryBarang = "
            SELECT 
                b.nama_barang,
                dp.jumlah,
                dp.subtotal
            FROM detail_peminjaman dp
            JOIN barang b 
                ON dp.id_barang = b.id_barang
            WHERE dp.id_peminjaman = ?
        ";

        $stmtBarang = mysqli_prepare($koneksi, $queryBarang);

        if ($stmtBarang) {
            mysqli_stmt_bind_param($stmtBarang, "i", $id_peminjaman);
            mysqli_stmt_execute($stmtBarang);

            $resultBarang = mysqli_stmt_get_result($stmtBarang);

            while ($row = mysqli_fetch_assoc($resultBarang)) {
                $barang[] = $row;
            }

            mysqli_stmt_close($stmtBarang);
        } else {
            $error = "Gagal mengambil detail barang.";
        }
    }
}

// Status peminjaman
$status = $data['status'] ?? '-';

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

// Total harga barang
$total = 0;

foreach ($barang as $item) {
    $total += (float) $item['subtotal'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detail Peminjaman</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="style_peminjaman.css">
</head>

<body>

<div class="container-detail">

    <div class="detail-header">
        <div class="header-icon">
            <i class="bi bi-clipboard-check"></i>
        </div>

        <div>
            <h2>Detail Peminjaman</h2>
            <p>Informasi lengkap riwayat peminjaman barang</p>
        </div>
    </div>

    <div class="detail-body">

        <?php if ($error !== ''): ?>

            <div class="alert-error">
                <i class="bi bi-exclamation-circle"></i>
                <?= aman($error); ?>
            </div>

        <?php else: ?>

            <div class="id-peminjaman">
                <span>ID Peminjaman</span>
                <strong>#<?= aman($data['id_peminjaman']); ?></strong>
            </div>

            <!-- Informasi Peminjam -->
            <h3 class="section-title">
                <i class="bi bi-person-fill"></i>
                Informasi Peminjam
            </h3>

            <div class="detail-group">

                <div class="detail-item">
                    <span>Nama Peminjam</span>
                    <strong><?= aman($data['nama_peminjam']); ?></strong>
                </div>

                <div class="detail-item">
                    <span>Nomor HP</span>
                    <strong><?= aman($data['no_hp']); ?></strong>
                </div>

                <div class="detail-item">
                    <span>Alamat</span>
                    <strong><?= aman($data['Alamat']); ?></strong>
                </div>

            </div>

            <!-- Informasi Peminjaman -->
            <h3 class="section-title">
                <i class="bi bi-calendar-check"></i>
                Informasi Peminjaman
            </h3>

            <div class="detail-group">

                <div class="detail-item">
                    <span>Tanggal Peminjaman</span>
                    <strong><?= formatTanggal($data['tgl_peminjaman']); ?></strong>
                </div>

                <div class="detail-item">
                    <span>Tanggal Jatuh Tempo</span>
                    <strong><?= formatTanggal($data['tgl_jatuh_tempo']); ?></strong>
                </div>

                <div class="detail-item">
                    <span>Status Peminjaman</span>
                    <span class="status-badge <?= aman($classStatus); ?>">
                        <?= aman($labelStatus); ?>
                    </span>
                </div>

            </div>

            <!-- Barang yang Dipinjam -->
            <h3 class="section-title">
                <i class="bi bi-camera-fill"></i>
                Barang yang Dipinjam
            </h3>

            <div class="table-responsive">
                <table class="table-riwayat">

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if (!empty($barang)): ?>

                        <?php $no = 1; ?>

                        <?php foreach ($barang as $item): ?>

                            <tr>
                                <td><?= $no++; ?></td>

                                <td>
                                    <?= aman($item['nama_barang']); ?>
                                </td>

                                <td>
                                    <?= aman($item['jumlah']); ?>
                                </td>

                                <td>
                                    Rp<?= number_format((float) $item['subtotal'], 0, ',', '.'); ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="4" class="data-kosong">
                                Belum ada detail barang pada peminjaman ini.
                            </td>
                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>
            </div>

            <div class="total-peminjaman">
                <span>Total Peminjaman</span>
                <strong>
                    Rp<?= number_format($total, 0, ',', '.'); ?>
                </strong>
            </div>

        <?php endif; ?>

        <a href="riwayat_peminjaman.php" class="btn-kembali">
            <i class="bi bi-arrow-left"></i>
            Kembali ke Riwayat
        </a>

    </div>
</div>

</body>
</html>
