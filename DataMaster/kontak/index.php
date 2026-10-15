<?php
require_once "../config/koneksi.php";
/** @var mysqli $conn */
// Ambil seluruh data pesan kontak
$query = mysqli_query(
    $conn,
    "SELECT * FROM kontak ORDER BY id DESC"
);

if (!$query) {
    die("Query error: " . mysqli_error($conn));
}
$total_kontak = mysqli_num_rows($query);

$notifikasi = [
    'hapus' => [
        'class' => 'danger',
        'icon' => 'check-circle-fill',
        'teks' => 'Pesan kontak berhasil dihapus!'
    ],
    'jawab' => [
        'class' => 'success',
        'icon' => 'check-circle-fill',
        'teks' => 'Balasan berhasil disimpan!'
    ]
];

$pesan_notif = $_GET['pesan'] ?? '';
// Hitung status pesan
$total_belum = 0;
$total_sudah = 0;
mysqli_data_seek($query, 0);
while ($data = mysqli_fetch_assoc($query)) {
    if (($data['status_balasan'] ?? 'belum_dijawab') === 'sudah_dijawab') {
        $total_sudah++;
    } else {
        $total_belum++;
    }
}
mysqli_data_seek($query, 0);
?>
<?php include "../layout/header.php"; ?>
<link rel="stylesheet" href="../assets/css/data-master.css?v=2">
<?php include "../layout/navbar.php"; ?>
<div class="main-content data-master-page">
    <div class="container-fluid">
        <!-- HEADER HALAMAN -->
        <div class="data-page-header">
            <div class="data-page-icon">
                <i class="bi bi-chat-left-text"></i>
            </div>
            <div>
                <h2>Pesan Kontak</h2>
                <p>Kelola pesan dan balasan dari pengunjung website.</p>
            </div>
        </div>
        <!-- RINGKASAN PESAN -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="data-master-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="data-page-icon">
                            <i class="bi bi-inbox-fill"></i>
                        </div>
                        <div>
                            <small class="text-muted">Total Pesan</small>
                            <h4 class="fw-bold mb-0">
                                <?= $total_kontak; ?>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="data-master-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="data-page-icon">
                            <i class="bi bi-envelope-exclamation"></i>
                        </div>
                        <div>
                            <small class="text-muted">Belum Dijawab</small>
                            <h4 class="fw-bold mb-0">
                                <?= $total_belum; ?>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="data-master-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="data-page-icon">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div>
                            <small class="text-muted">Sudah Dijawab</small>
                            <h4 class="fw-bold mb-0">
                                <?= $total_sudah; ?>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- CARD DAFTAR PESAN -->
        <div class="data-master-card">
            <!-- HEADER CARD -->
            <div class="data-card-header">
                <div>
                    <h3 class="data-card-title">
                        <i class="bi bi-list-ul me-2"></i>
                        Daftar Pesan Kontak
                    </h3>
                    <p class="mb-0">
                        Informasi pesan yang dikirim melalui website.
                    </p>
                </div>
            </div>
            <!-- ISI CARD -->
            <div class="data-card-body">
                <!-- NOTIFIKASI -->
                <?php if (isset($notifikasi[$pesan_notif])): ?>
                    <?php $notif = $notifikasi[$pesan_notif]; ?>
                    <div class="data-notification">
                        <div
                            class="alert alert-<?= $notif['class']; ?> alert-dismissible fade show"
                            role="alert">

                            <i class="bi bi-<?= $notif['icon']; ?> me-2"></i>
                            <?= htmlspecialchars(
                                $notif['teks'],
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="alert"
                                aria-label="Tutup">
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
                <!-- PENCARIAN -->
                <div class="mb-3">
                    <input
                        type="text"
                        id="cariKontak"
                        class="form-control"
                        placeholder="Cari nama, email, atau isi pesan...">
                </div>
                <!-- TABEL PESAN -->
                <div class="data-table-wrapper">
                    <table class="data-master-table" id="tabelKontak">
                        <thead>
                            <tr>
                                <th width="65">No.</th>
                                <th>Pengirim</th>
                                <th>Pesan</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th width="130">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if ($total_kontak > 0): ?>
                                <?php $no = 1; ?>

                                <?php while ($data = mysqli_fetch_assoc($query)): ?>
                                    <?php
                                    $id = (int) $data['id'];

                                    $nama = $data['nama'] ?? '';
                                    $email = $data['email'] ?? '';
                                    $isi_pesan = $data['pesan'] ?? '';
                                    $tanggal = $data['created_at'] ?? '';

                                    $sudah_dijawab =
                                        ($data['status_balasan'] ?? '') === 'sudah_dijawab';

                                    $teks_cari = strtolower(
                                        $nama . ' ' . $email . ' ' . $isi_pesan
                                    );
                                    ?>

                                    <tr class="baris-kontak"
                                        data-cari="<?= htmlspecialchars(
                                            $teks_cari,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>">

                                        <!-- NOMOR -->
                                        <td>
                                            <span class="data-number">
                                                <?= $no++; ?>
                                            </span>
                                        </td>

                                        <!-- PENGIRIM -->
                                        <td>
                                            <span class="data-name">
                                                <?= htmlspecialchars(
                                                    $nama,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ); ?>
                                            </span>
                                            <br>
                                            <small class="text-muted">
                                                <?= htmlspecialchars(
                                                    $email,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ); ?>
                                            </small>
                                        </td>

                                        <!-- PESAN -->
                                        <td style="min-width: 220px; max-width: 350px;">
                                            <?= nl2br(htmlspecialchars(
                                                $isi_pesan,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            )); ?>
                                        </td>

                                        <!-- TANGGAL -->
                                        <td>
                                            <?= htmlspecialchars(
                                                $tanggal,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>
                                        </td>

                                        <!-- STATUS -->
                                        <td>
                                            <?php if ($sudah_dijawab): ?>
                                                <span class="badge bg-success">
                                                    Sudah Dijawab
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">
                                                    Belum Dijawab
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- AKSI -->
                                        <td>
                                            <div class="data-action">
                                                <a
                                                    href="detail.php?id=<?= $id; ?>"
                                                    class="data-detail"
                                                    title="Detail Pesan"
                                                    aria-label="Detail pesan">
                                                    <i class="bi bi-eye"></i>
                                                </a>

                                                <a
                                                    href="jawab.php?id=<?= $id; ?>"
                                                    class="data-edit"
                                                    title="Jawab Pesan"
                                                    aria-label="Jawab pesan">
                                                    <i class="bi bi-reply"></i>
                                                </a>

                                                <a
                                                    href="hapus.php?id=<?= $id; ?>"
                                                    class="data-delete"
                                                    title="Hapus Pesan"
                                                    aria-label="Hapus pesan"
                                                    onclick="return confirm('Yakin ingin menghapus pesan ini?');">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>

                                <?php endwhile; ?>

                                <!-- HASIL PENCARIAN KOSONG -->
                                <tr id="hasilKosong" style="display: none;">
                                    <td colspan="6" class="data-empty">
                                        <i class="bi bi-search"></i>
                                        <p>Pesan tidak ditemukan.</p>
                                        <span>Coba kata kunci lainnya.</span>
                                    </td>
                                </tr>

                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="data-empty">
                                        <i class="bi bi-inbox"></i>
                                        <p>Belum ada pesan kontak.</p>
                                        <span>
                                            Pesan pengunjung akan muncul di sini.
                                        </span>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- TOTAL DATA -->
                <div class="data-table-footer">
                    Total pesan:
                    <strong id="jumlahKontak"><?= $total_kontak; ?></strong>
                    data
                </div>

            </div>
        </div>

    </div>
</div>

<script>
const inputCari = document.getElementById('cariKontak');
const barisKontak = document.querySelectorAll('.baris-kontak');
const hasilKosong = document.getElementById('hasilKosong');
const jumlahKontak = document.getElementById('jumlahKontak');
inputCari.addEventListener('input', function () {
    const kataKunci = this.value.toLowerCase().trim();
    let jumlah = 0;
    barisKontak.forEach(function (baris) {
        const cocok = baris.dataset.cari.includes(kataKunci);
        baris.style.display = cocok ? '' : 'none';
        if (cocok) {
            jumlah++;
        }
    });
    if (hasilKosong) {
        hasilKosong.style.display =
            barisKontak.length > 0 && jumlah === 0 ? '' : 'none';
    }
    jumlahKontak.textContent = jumlah;
});
</script>
<?php include "../layout/footer.php"; ?>
