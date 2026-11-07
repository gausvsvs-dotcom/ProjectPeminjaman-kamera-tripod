
<?php
session_start();

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// Mengubah jumlah barang
if (isset($_POST['ubah_jumlah'])) {

    $id_barang = $_POST['id_barang'];
    $aksi = $_POST['aksi'];

    if (isset($_SESSION['keranjang'][$id_barang])) {

        if ($aksi == 'tambah') {
            $_SESSION['keranjang'][$id_barang]['jumlah']++;
        } elseif ($aksi == 'kurang') {

            if ($_SESSION['keranjang'][$id_barang]['jumlah'] > 1) {
                $_SESSION['keranjang'][$id_barang]['jumlah']--;
            }
        }
    }

    header("Location: keranjang.php");
    exit;
}

// Menghapus barang
if (isset($_POST['hapus_keranjang'])) {

    $id_barang = $_POST['hapus_keranjang'];

    unset($_SESSION['keranjang'][$id_barang]);

    header("Location: keranjang.php");
    exit;
}

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
            <p>Periksa kembali kamera dan tripod yang ingin Anda pinjam.</p>
        </div>

        <?php if ($jumlah_barang > 0): ?>

            <div class="keranjang-pilih-semua">
                <label>
                    <input type="checkbox" id="pilihSemua">
                    <span>Pilih Semua (<?= $jumlah_barang; ?> barang)</span>
                </label>
            </div>

            <div class="keranjang-daftar">

                <?php foreach ($_SESSION['keranjang'] as $barang): ?>

                    <div class="keranjang-item">

                        <input type="checkbox"
                               class="checkbox-barang"
                               value="<?= htmlspecialchars($barang['id']); ?>">

                        <img src="<?= htmlspecialchars($barang['gambar']); ?>"
                             alt="<?= htmlspecialchars($barang['nama']); ?>"
                             class="keranjang-gambar">

                        <div class="keranjang-info">

                            <h5><?= htmlspecialchars($barang['nama']); ?></h5>

                            <span class="keranjang-kategori">
                                <?= htmlspecialchars($barang['kategori']); ?>
                            </span>

                            <span class="keranjang-status">
                                <i class="fas fa-check-circle"></i>
                                Tersedia
                            </span>

                            <div class="keranjang-aksi">

                                <form method="POST" action="keranjang.php">
                                    <input type="hidden" name="id_barang"
                                           value="<?= htmlspecialchars($barang['id']); ?>">

                                    <input type="hidden" name="aksi" value="kurang">

                                    <button type="submit" name="ubah_jumlah"
                                            class="btn-jumlah">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </form>

                                <span class="jumlah-barang">
                                    <?= htmlspecialchars($barang['jumlah']); ?>
                                </span>

                                <form method="POST" action="keranjang.php">
                                    <input type="hidden" name="id_barang"
                                           value="<?= htmlspecialchars($barang['id']); ?>">

                                    <input type="hidden" name="aksi" value="tambah">

                                    <button type="submit" name="ubah_jumlah"
                                            class="btn-jumlah">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </form>

                                <form method="POST" action="keranjang.php"
                                      class="form-hapus">

                                    <button type="submit"
                                            name="hapus_keranjang"
                                            value="<?= htmlspecialchars($barang['id']); ?>"
                                            class="btn-hapus">

                                        <i class="fas fa-trash"></i>
                                        Hapus

                                    </button>

                                </form>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

           <div class="keranjang-footer">

                <div class="keranjang-footer-info">
                    <span>Barang dipilih</span>
                    <strong id="jumlahDipilih">0 barang</strong>
                </div>

                <div class="keranjang-footer-tombol">

                    <a href="kamera&tripod.php" class="btn-kembali-keranjang">
                        <i class="fas fa-arrow-left me-2"></i>
                        Kembali
                    </a>

                    <button type="button" id="btnAjukan" class="btn-ajukan-keranjang" disabled>
                        Ajukan Peminjaman
                        <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>

        <?php else: ?>

            <div class="keranjang-kosong">

                <i class="fas fa-shopping-cart"></i>

                <h4>Keranjang Anda Masih Kosong</h4>

                <p>Silakan pilih kamera atau tripod yang ingin Anda pinjam.</p>

                <a href="kamera&tripod.php" class="btn-kembali-belanja">
                    Lihat Kamera & Tripod
                </a>

            </div>

        <?php endif; ?>

    </div>

    <script>
        const pilihSemua = document.getElementById('pilihSemua');
        const checkboxBarang = document.querySelectorAll('.checkbox-barang');
        const jumlahDipilih = document.getElementById('jumlahDipilih');
        const btnAjukan = document.getElementById('btnAjukan');

        function perbaruiPilihan() {

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
                    checkboxBarang.length > 0 &&
                    totalDipilih === checkboxBarang.length;

                pilihSemua.indeterminate =
                    totalDipilih > 0 &&
                    totalDipilih < checkboxBarang.length;
            }
        }

        if (pilihSemua) {
            pilihSemua.addEventListener('change', function () {

                checkboxBarang.forEach(function (checkbox) {
                    checkbox.checked = pilihSemua.checked;
                });

                perbaruiPilihan();
            });
        }

        checkboxBarang.forEach(function (checkbox) {
            checkbox.addEventListener('change', perbaruiPilihan);
        });

       if (btnAjukan) {
            btnAjukan.addEventListener('click', function () {

        const barangDipilih = document.querySelectorAll(
            '.checkbox-barang:checked'
        );

        if (barangDipilih.length === 0) {
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '../Transaksi/peminjaman/peminjaman.php';

        barangDipilih.forEach(function (checkbox) {

            const input = document.createElement('input');

            input.type = 'hidden';
            input.name = 'barang_dipilih[]';
            input.value = checkbox.value;

            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    });
}

        perbaruiPilihan();
    </script>

</body>
</html>
