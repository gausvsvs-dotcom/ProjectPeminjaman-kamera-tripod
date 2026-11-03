
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../../DataMaster/config/koneksi.php";

/** @var mysqli $conn */

// Ambil ID peminjaman
$id_peminjaman = isset($_GET['id_peminjaman'])
    ? (int) $_GET['id_peminjaman']
    : (int) ($_POST['id_peminjaman'] ?? 0);

if ($id_peminjaman <= 0) {
    die("ID peminjaman tidak valid.");
}

// Ambil data peminjaman
$stmt = mysqli_prepare($conn, "
    SELECT id_peminjaman, nama_peminjam, status
    FROM peminjaman
    WHERE id_peminjaman = ?
");

mysqli_stmt_bind_param($stmt, "i", $id_peminjaman);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$peminjaman = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$peminjaman) {
    die("Data peminjaman tidak ditemukan.");
}

// Hitung total pembayaran dari detail peminjaman
$stmt = mysqli_prepare($conn, "
    SELECT COALESCE(SUM(subtotal), 0) AS total
    FROM detail_peminjaman
    WHERE id_peminjaman = ?
");

mysqli_stmt_bind_param($stmt, "i", $id_peminjaman);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data_total = mysqli_fetch_assoc($result);

$total = (float) ($data_total['total'] ?? 0);
mysqli_stmt_close($stmt);

$pesan = "";
$error = "";
$pembayaran = null;

// Cek apakah pembayaran sudah pernah dikirim
$stmt = mysqli_prepare($conn, "
    SELECT status_pembayaran, metode_pembayaran, tgl_bayar, total
    FROM pembayaran
    WHERE id_peminjaman = ?
    ORDER BY id_pembayaran DESC
    LIMIT 1
");

mysqli_stmt_bind_param($stmt, "i", $id_peminjaman);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$pembayaran = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Proses form pembayaran
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($total <= 0) {

        $error = "Pembayaran belum tersedia karena detail peminjaman belum selesai.";

    } elseif ($pembayaran) {

        $error = "Pembayaran untuk peminjaman ini sudah dikirim.";

    } else {

        $metode_pembayaran = $_POST['metode_pembayaran'] ?? '';
        $keterangan = trim($_POST['keterangan'] ?? '');

        $metode_valid = ['Tunai', 'Transfer', 'QRIS'];

        if (!in_array($metode_pembayaran, $metode_valid, true)) {

            $error = "Silakan pilih metode pembayaran.";

        } else {

            $tgl_bayar = date('Y-m-d');
            $status_pembayaran = "Menunggu Konfirmasi";

            $stmt = mysqli_prepare($conn, "
                INSERT INTO pembayaran (
                    id_peminjaman,
                    tgl_bayar,
                    total,
                    metode_pembayaran,
                    status_pembayaran,
                    keterangan
                ) VALUES (?, ?, ?, ?, ?, ?)
            ");

            mysqli_stmt_bind_param(
                $stmt,
                "isdsss",
                $id_peminjaman,
                $tgl_bayar,
                $total,
                $metode_pembayaran,
                $status_pembayaran,
                $keterangan
            );

            if (mysqli_stmt_execute($stmt)) {

                mysqli_stmt_close($stmt);

                header(
                    "Location: pembayaran.php?id_peminjaman="
                    . $id_peminjaman . "&sukses=1"
                );
                exit;

            } else {

                $error = "Gagal menyimpan pembayaran: "
                    . mysqli_stmt_error($stmt);

                mysqli_stmt_close($stmt);
            }
        }
    }
}

// Pesan berhasil
if (isset($_GET['sukses'])) {
    $pesan = "Pembayaran berhasil dikirim! Silakan tunggu konfirmasi admin.";
}

// Ambil data pembayaran terbaru setelah proses
if (!$pembayaran) {

    $stmt = mysqli_prepare($conn, "
        SELECT status_pembayaran, metode_pembayaran, tgl_bayar, total
        FROM pembayaran
        WHERE id_peminjaman = ?
        ORDER BY id_pembayaran DESC
        LIMIT 1
    ");

    mysqli_stmt_bind_param($stmt, "i", $id_peminjaman);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $pembayaran = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pembayaran</title>
    <link rel="stylesheet" href="pembayaran.css">
</head>

<body>

<div class="form-card">

    <div class="form-header">
        <h2>Form Pembayaran</h2>
    </div>

    <form class="form-body" method="POST">

        <input
            type="hidden"
            name="id_peminjaman"
            value="<?= $id_peminjaman; ?>"
        >

        <div class="form-group">
            <label for="nama_peminjam">Nama Peminjam</label>

            <input
                type="text"
                id="nama_peminjam"
                class="form-control"
                value="<?= htmlspecialchars($peminjaman['nama_peminjam']); ?>"
                readonly
            >
        </div>

        <div class="total-box">
            <span>Total Pembayaran</span>

            <?php if ($total > 0) { ?>

                <strong>
                    Rp<?= number_format($total, 0, ',', '.'); ?>
                </strong>

            <?php } else { ?>

                <strong>Menunggu data detail peminjaman</strong>

                <p>
                    Total pembayaran akan muncul setelah detail barang
                    peminjaman tersedia.
                </p>

            <?php } ?>
        </div>

        <?php if ($pesan) { ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($pesan); ?>
            </div>
        <?php } ?>

        <?php if ($error) { ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error); ?>
            </div>
        <?php } ?>

        <?php if ($pembayaran) { ?>

            <div class="status-box">

                <strong>Status Pembayaran</strong>
                <p>
                    <?= htmlspecialchars($pembayaran['status_pembayaran']); ?>
                </p>

                <br>

                <strong>Metode Pembayaran</strong>
                <p>
                    <?= htmlspecialchars($pembayaran['metode_pembayaran']); ?>
                </p>

                <br>

                <strong>Tanggal Pembayaran</strong>
                <p>
                    <?= htmlspecialchars($pembayaran['tgl_bayar']); ?>
                </p>

                <br>

                <strong>Total Dibayarkan</strong>
                <p>
                    Rp<?= number_format($pembayaran['total'], 0, ',', '.'); ?>
                </p>

            </div>

        <?php } elseif ($total > 0) { ?>

            <div class="form-group">
                <label for="metode_pembayaran">Metode Pembayaran</label>

                <select
                    name="metode_pembayaran"
                    id="metode_pembayaran"
                    class="form-control"
                    required
                >
                    <option value="">Pilih metode pembayaran</option>
                    <option value="Tunai">Tunai / Cash</option>
                    <option value="Transfer">Transfer Bank</option>
                    <option value="QRIS">QRIS</option>
                </select>
            </div>

            <div class="form-group">
                <label for="keterangan">Keterangan (Opsional)</label>

                <textarea
                    name="keterangan"
                    id="keterangan"
                    class="form-control"
                    rows="3"
                    placeholder="Tambahkan keterangan jika diperlukan"
            ></textarea>
            </div>

            <button type="submit" class="btn-submit">
                Kirim Pembayaran
            </button>

        <?php } else { ?>

            <div class="alert alert-error">
                Form pembayaran belum tersedia karena detail
                peminjaman belum selesai.
            </div>

        <?php } ?>

    </form>

</div>

</body>
</html>
