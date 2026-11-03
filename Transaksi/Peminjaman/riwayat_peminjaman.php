
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../../DataMaster/config/koneksi.php";

$query = mysqli_query($conn, "
    SELECT id_peminjaman, nama_peminjam, tgl_peminjaman, status
    FROM peminjaman
    ORDER BY id_peminjaman DESC
");

if (!$query) {
    die("Query error: " . mysqli_error($conn));
}

$data_peminjaman = mysqli_fetch_all($query, MYSQLI_ASSOC);
$total_peminjaman = count($data_peminjaman);

function formatTanggal($tanggal)
{
    if (empty($tanggal) || $tanggal === '0000-00-00') {
        return '-';
    }

    $timestamp = strtotime($tanggal);
    return $timestamp ? date('d-m-Y', $timestamp) : '-';
}

function aman($data)
{
    return htmlspecialchars((string)($data ?? '-'), ENT_QUOTES, 'UTF-8');
}
?>

<?php include "../../DataMaster/layout/header.php"; ?>

<link rel="stylesheet" href="/ProjectPeminjaman-kamera-tripod/DataMaster/assets/css/data-master.css?v=3">

<?php include "../../DataMaster/layout/navbar.php"; ?>

<div class="main-content data-master-page">
    <div class="container-fluid">

        <div class="data-page-header">
            <div class="data-page-icon">
                <i class="bi bi-clock-history"></i>
            </div>

            <div>
                <h2>Riwayat Peminjaman</h2>
                <p>Kelola dan lihat riwayat transaksi peminjaman barang.</p>
            </div>
        </div>

        <div class="data-master-card">

            <div class="data-card-header">
                <div>
                    <h3 class="data-card-title">
                        <i class="bi bi-list-ul me-2"></i>
                        Daftar Riwayat Peminjaman
                    </h3>
                    <p>Informasi seluruh transaksi peminjaman yang tersimpan.</p>
                </div>
            </div>

            <div class="data-card-body">
                <div class="data-table-wrapper">
                    <table class="data-master-table">
                        <thead>
                            <tr>
                                <th width="65">No.</th>
                                <th>Nama Peminjam</th>
                                <th>Tanggal Peminjaman</th>
                                <th>Status</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php if ($total_peminjaman > 0): ?>

                            <?php foreach ($data_peminjaman as $index => $data): ?>
                                <?php
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
                                ?>

                                <tr>
                                    <td>
                                        <span class="data-number">
                                            <?= $index + 1 ?>
                                        </span>
                                    </td>

                                    <td>
                                        <span class="data-name">
                                            <?= aman($data['nama_peminjam']) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?= formatTanggal($data['tgl_peminjaman']) ?>
                                    </td>

                                    <td>
                                        <span class="status-badge <?= aman($classStatus) ?>">
                                            <?= aman($labelStatus) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <div class="data-action">
                                            <a href="detail_peminjaman.php?id=<?= (int)$data['id_peminjaman'] ?>"
                                               class="data-detail"
                                               title="Lihat Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="data-empty">
                                    <i class="bi bi-inbox"></i>
                                    <p>Belum ada riwayat peminjaman.</p>
                                    <span>Data peminjaman akan ditampilkan setelah transaksi dilakukan.</span>
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="data-table-footer">
                    Total riwayat peminjaman:
                    <strong><?= $total_peminjaman ?></strong> data
                </div>
            </div>

        </div>
    </div>
</div>

<?php include "../../DataMaster/layout/footer.php"; ?>
