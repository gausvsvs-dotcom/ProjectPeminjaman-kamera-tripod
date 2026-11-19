
<?php

session_start();

require_once __DIR__ . '/includes/koneksi.php';

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}


// ==========================================
// FUNGSI MENGAMBIL STOK TERSEDIA
// ==========================================

function ambilStokTersedia($conn, $id_barang)
{
    $query = mysqli_prepare($conn, "

        SELECT
            CASE
                WHEN COUNT(bu.id_unit) = 0
                THEN b.stok

                ELSE SUM(
                    CASE
                        WHEN LOWER(TRIM(bu.status_unit)) = 'tersedia'
                        THEN 1
                        ELSE 0
                    END
                )
            END AS stok_tersedia

        FROM barang b

        LEFT JOIN barang_unit bu
            ON b.id_barang = bu.id_barang

        WHERE b.id_barang = ?

        GROUP BY b.id_barang, b.stok

    ");

    mysqli_stmt_bind_param($query, "i", $id_barang);
    mysqli_stmt_execute($query);

    $result = mysqli_stmt_get_result($query);
    $data = mysqli_fetch_assoc($result);

    return (int) ($data['stok_tersedia'] ?? 0);
}


// ==========================================
// FUNGSI MENGAMBIL JUMLAH UNIT DIPINJAM
// ==========================================

function ambilJumlahDipinjam($conn, $id_barang)
{
    $query = mysqli_prepare($conn, "

        SELECT COUNT(*) AS jumlah_dipinjam

        FROM barang_unit

        WHERE id_barang = ?

        AND LOWER(TRIM(status_unit)) = 'dipinjam'

    ");

    mysqli_stmt_bind_param($query, "i", $id_barang);
    mysqli_stmt_execute($query);

    $result = mysqli_stmt_get_result($query);
    $data = mysqli_fetch_assoc($result);

    return (int) ($data['jumlah_dipinjam'] ?? 0);
}


// MENGAMBIL KONDISI BARANG
function ambilKondisiBarang($conn, $id_barang)
{
    $query = mysqli_prepare($conn, "
        SELECT kb.nama_kondisi
        FROM barang b
        LEFT JOIN kondisi_barang kb ON b.id_kondisi = kb.id_kondisi
        WHERE b.id_barang = ?
    ");

    mysqli_stmt_bind_param($query, "i", $id_barang);
    mysqli_stmt_execute($query);

    $result = mysqli_stmt_get_result($query);
    $data = mysqli_fetch_assoc($result);

    return $data['nama_kondisi'] ?? '';
}

// ==========================================
// MENGUBAH JUMLAH BARANG
// ==========================================

if (isset($_POST['ubah_jumlah'])) {

    $id_barang = (int) ($_POST['id_barang'] ?? 0);
    $aksi = $_POST['aksi'] ?? '';

    if (isset($_SESSION['keranjang'][$id_barang])) {

        if ($aksi === 'tambah') {

            $stokMaks = ambilStokTersedia($conn, $id_barang);

            if (
                $_SESSION['keranjang'][$id_barang]['jumlah'] < $stokMaks
            ) {

                $_SESSION['keranjang'][$id_barang]['jumlah']++;

            }

        } elseif ($aksi === 'kurang') {

            if (
                $_SESSION['keranjang'][$id_barang]['jumlah'] > 1
            ) {

                $_SESSION['keranjang'][$id_barang]['jumlah']--;

            }

        }

    }

    header("Location: keranjang.php");
    exit;
}


// ==========================================
// MENGHAPUS BARANG
// ==========================================

if (isset($_POST['hapus_keranjang'])) {

    $id_barang = (int) $_POST['hapus_keranjang'];

    unset($_SESSION['keranjang'][$id_barang]);

    header("Location: keranjang.php");
    exit;
}


// ==========================================
// JUMLAH JENIS BARANG DI KERANJANG
// ==========================================

$jumlah_barang = count($_SESSION['keranjang']);

?>


<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Keranjang Peminjaman</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css?v=103">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>


<body>

<?php include 'includes/navbar.php'; ?>


<div class="container keranjang-container">

    <div class="keranjang-header">

        <h2>Keranjang</h2>

        <p>
            Periksa kembali kamera dan tripod yang ingin Anda pinjam.
        </p>

    </div>


    <?php if ($jumlah_barang > 0): ?>


        <!-- PILIH SEMUA -->

        <div class="keranjang-pilih-semua">

            <label>

                <input type="checkbox" id="pilihSemua">

                <span>
                    Pilih Semua (<?= $jumlah_barang; ?> barang)
                </span>

            </label>

        </div>


        <div class="keranjang-daftar">


            <?php foreach ($_SESSION['keranjang'] as $barang): ?>

                <?php

                $idBarang = (int) $barang['id'];

                $jumlahKeranjang = (int) $barang['jumlah'];

                $stokMaks = ambilStokTersedia($conn, $idBarang);

                $jumlahDipinjam = ambilJumlahDipinjam($conn, $idBarang);

                $kondisiBarang = ambilKondisiBarang($conn, $idBarang);

                // MENENTUKAN STATUS BARANG
                if ($stokMaks > 0) {

                    $statusBarang = 'Tersedia';
                    $warnaStatus = 'success';

                } elseif ($jumlahDipinjam > 0) {

                    $statusBarang = 'Dipinjam';
                    $warnaStatus = 'warning';

                } else {

                    $statusBarang = 'Tidak Tersedia';
                    $warnaStatus = 'danger';

                }


                // VALIDASI PEMILIHAN
                $bisaDipilih = (
                    $stokMaks > 0 &&
                    $jumlahKeranjang <= $stokMaks
                );

                ?>


                <div class="keranjang-item">


                    <!-- CHECKBOX BARANG -->

                    <input type="checkbox"
                        class="checkbox-barang"
                        value="<?= $idBarang; ?>"
                        data-kondisi="<?= htmlspecialchars($kondisiBarang); ?>"
                    <?= !$bisaDipilih ? 'disabled' : ''; ?> >


                    <!-- GAMBAR BARANG -->

                    <?php if (!empty($barang['gambar'])): ?>

                        <img
                            src="../uploads/barang/<?= htmlspecialchars($barang['gambar']); ?>"
                            alt="<?= htmlspecialchars($barang['nama']); ?>"
                            class="keranjang-gambar"
                        >

                    <?php endif; ?>


                    <div class="keranjang-info">


                        <h5>

                            <?= htmlspecialchars($barang['nama']); ?>

                        </h5>


                        <span class="keranjang-kategori">

                            <?= htmlspecialchars($barang['kategori']); ?>

                        </span>


                        <!-- STATUS BARANG -->

                        <span class="keranjang-status">

                            <i class="fas fa-circle-check"></i>

                            <span class="badge bg-<?= $warnaStatus; ?>">

                                <?= htmlspecialchars($statusBarang); ?>

                            </span>

                        </span>


                        <!-- INFORMASI STOK -->

                        <p class="mt-2 mb-2">

                            <small>

                                <?= $jumlahDipinjam; ?> barang dipinjam,
                                tersisa <?= $stokMaks; ?> barang.

                            </small>

                        </p>


                        <?php if ($stokMaks === 0): ?>

                            <p class="text-danger small">

                                Stok sedang tidak tersedia.

                            </p>

                        <?php elseif ($jumlahKeranjang > $stokMaks): ?>

                            <p class="text-danger small">

                                Jumlah di keranjang melebihi stok yang tersedia.
                                Kurangi jumlah barang terlebih dahulu.

                            </p>

                        <?php endif; ?>


                        <div class="keranjang-aksi">


                            <!-- TOMBOL KURANG -->

                            <form method="POST" action="keranjang.php">

                                <input
                                    type="hidden"
                                    name="id_barang"
                                    value="<?= $idBarang; ?>"
                                >

                                <input
                                    type="hidden"
                                    name="aksi"
                                    value="kurang"
                                >

                                <button
                                    type="submit"
                                    name="ubah_jumlah"
                                    class="btn-jumlah"
                                    <?= $jumlahKeranjang <= 1 ? 'disabled' : ''; ?>
                                >

                                    <i class="fas fa-minus"></i>

                                </button>

                            </form>


                            <!-- JUMLAH BARANG -->

                            <span class="jumlah-barang">

                                <?= $jumlahKeranjang; ?>

                            </span>


                            <!-- TOMBOL TAMBAH -->

                            <form method="POST" action="keranjang.php">

                                <input
                                    type="hidden"
                                    name="id_barang"
                                    value="<?= $idBarang; ?>"
                                >

                                <input
                                    type="hidden"
                                    name="aksi"
                                    value="tambah"
                                >

                                <button
                                    type="submit"
                                    name="ubah_jumlah"
                                    class="btn-jumlah"
                                    <?= $jumlahKeranjang >= $stokMaks ? 'disabled' : ''; ?>
                                >

                                    <i class="fas fa-plus"></i>

                                </button>

                            </form>


                            <!-- TOMBOL HAPUS -->

                            <form
                                method="POST"
                                action="keranjang.php"
                                class="form-hapus"
                            >

                                <button
                                    type="submit"
                                    name="hapus_keranjang"
                                    value="<?= $idBarang; ?>"
                                    class="btn-hapus"
                                >

                                    <i class="fas fa-trash"></i>

                                    Hapus

                                </button>

                            </form>


                        </div>

                    </div>

                </div>


            <?php endforeach; ?>


        </div>


        <!-- FOOTER KERANJANG -->

        <div class="keranjang-footer">


            <div class="keranjang-footer-info">

                <span>Barang dipilih</span>

                <strong id="jumlahDipilih">
                    0 barang
                </strong>

            </div>


            <div class="keranjang-footer-tombol">


                <a
                    href="kamera&tripod.php"
                    class="btn-kembali-keranjang"
                >

                    <i class="fas fa-arrow-left me-2"></i>

                    Kembali

                </a>


                <button
                    type="button"
                    id="btnAjukan"
                    class="btn-ajukan-keranjang"
                    disabled
                >

                    Ajukan Peminjaman

                    <i class="fas fa-arrow-right ms-2"></i>

                </button>


            </div>

        </div>


    <?php else: ?>


        <!-- KERANJANG KOSONG -->

        <div class="keranjang-kosong">

            <i class="fas fa-shopping-cart"></i>

            <h4>Keranjang Anda Masih Kosong</h4>

            <p>
                Silakan pilih kamera atau tripod yang ingin Anda pinjam.
            </p>

            <a
                href="kamera&tripod.php"
                class="btn-kembali-belanja"
            >

                Lihat Kamera & Tripod

            </a>

        </div>


    <?php endif; ?>


</div>


<script>

// ==========================================
// PILIH BARANG
// ==========================================

const pilihSemua = document.getElementById('pilihSemua');

const checkboxBarang = document.querySelectorAll('.checkbox-barang');

const jumlahDipilih = document.getElementById('jumlahDipilih');

const btnAjukan = document.getElementById('btnAjukan');


function perbaruiPilihan() {

    const checkboxAktif = Array.from(checkboxBarang).filter(
        checkbox => !checkbox.disabled
    );

    const totalDipilih = document.querySelectorAll(
        '.checkbox-barang:checked'
    ).length;


    if (jumlahDipilih) {

        jumlahDipilih.textContent = totalDipilih + ' barang';

    }


    if (btnAjukan) {

        btnAjukan.disabled = totalDipilih === 0;

    }


    if (pilihSemua) {

        pilihSemua.checked =

            checkboxAktif.length > 0 &&

            checkboxAktif.every(checkbox => checkbox.checked);


        pilihSemua.indeterminate =

            totalDipilih > 0 &&

            !pilihSemua.checked;

        pilihSemua.disabled = checkboxAktif.length === 0;

    }

}


// ==========================================
// PILIH SEMUA BARANG
// ==========================================

if (pilihSemua) {

    pilihSemua.addEventListener('change', function() {

        checkboxBarang.forEach(function(checkbox) {

            if (!checkbox.disabled) {

                checkbox.checked = pilihSemua.checked;

            }

        });

        perbaruiPilihan();

    });

}


// ==========================================
// PILIH BARANG SATU PER SATU
// ==========================================

checkboxBarang.forEach(function(checkbox) {

    checkbox.addEventListener('change', perbaruiPilihan);

});


// ==========================================
// AJUKAN PEMINJAMAN
// ==========================================

if (btnAjukan) {
    btnAjukan.addEventListener('click', function() {
        const barangDipilih = document.querySelectorAll(
            '.checkbox-barang:checked:not(:disabled)'
        );

        if (barangDipilih.length === 0) {
            return;
        }

        if (barangDipilih.length > 1) {
            alert('Silakan pilih satu barang terlebih dahulu.');
            return;
        }

        const barang = barangDipilih[0];
        const idBarang = barang.value;
        const kondisiBarang = barang.dataset.kondisi.trim().toLowerCase();

        if (kondisiBarang === 'rusak') {
            const hubungiAdmin = confirm(
                'Barang yang dipilih sedang dalam kondisi rusak dan belum dapat dipinjam.\n\n' +
                'Silakan hubungi admin untuk informasi lebih lanjut.\n\n' +
                'Klik OK untuk menuju halaman kontak.'
            );

            if (hubungiAdmin) {
                window.location.href = 'kontak.php';
            }

            return;
        }

        if (kondisiBarang !== 'baik') {
            alert('Barang belum dapat dipinjam karena kondisinya tidak memenuhi persyaratan.');
            return;
        }

        window.location.href =
            '../Transaksi/Peminjaman/peminjaman.php?id=' + idBarang;
    });
}


// ==========================================
// INISIALISASI
// ==========================================

perbaruiPilihan();

</script>


</body>
</html>
