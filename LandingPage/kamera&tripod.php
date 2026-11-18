
<?php

session_start();
require_once __DIR__ . '/includes/koneksi.php';

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// PROSES KERANJANG
if (isset($_POST['tambah_keranjang'])) {

    $id_barang = $_POST['id_barang'];
    $nama_barang = $_POST['nama_barang'];
    $kategori = $_POST['kategori'];
    $gambar = $_POST['gambar'];

    if (!isset($_SESSION['keranjang'][$id_barang])) {

        $_SESSION['keranjang'][$id_barang] = [
            'id' => $id_barang,
            'nama' => $nama_barang,
            'kategori' => $kategori,
            'gambar' => $gambar,
            'jumlah' => 1
        ];

        $pesan = $nama_barang . ' berhasil ditambahkan ke keranjang!';

    } else {

        $_SESSION['keranjang'][$id_barang]['gambar'] = $gambar;
        $_SESSION['keranjang'][$id_barang]['jumlah']++;

        $pesan = $nama_barang . ' berhasil ditambahkan lagi!';
    }

    if (isset($_POST['ajax'])) {

        header('Content-Type: application/json');

        $total = 0;

        foreach ($_SESSION['keranjang'] as $barang) {
            $total += $barang['jumlah'];
        }

        echo json_encode([
            'pesan' => $pesan,
            'total' => $total
        ]);

        exit;
    }

    $_SESSION['pesan_keranjang'] = $pesan;

    header("Location: kamera&tripod.php");
    exit;
}


// TOTAL KERANJANG
$total_keranjang = 0;

foreach ($_SESSION['keranjang'] as $barang) {
    $total_keranjang += $barang['jumlah'];
}


// MENGAMBIL DATA BARANG DARI DATABASE
$queryBarang = mysqli_query($conn, "

    SELECT
        b.*,
        k.nama_kategori,
        m.nama_merk,

        COALESCE(
            unit.daftar_kondisi,
            kb_barang.nama_kondisi,
            'Belum ada kondisi'
        ) AS daftar_kondisi,

        COALESCE(unit.total_unit, b.stok) AS total_unit,

        COALESCE(unit.jumlah_dipinjam, 0) AS jumlah_dipinjam,

        COALESCE(unit.jumlah_tersedia, b.stok) AS jumlah_tersedia

    FROM barang b

    LEFT JOIN kategori k
        ON b.id_kategori = k.id_kategori

    LEFT JOIN kondisi_barang kb_barang
        ON b.id_kondisi = kb_barang.id_kondisi

    LEFT JOIN merk m
        ON b.id_merk = m.id_merk

    LEFT JOIN (

        SELECT
            bu.id_barang,

            GROUP_CONCAT(
                DISTINCT kb.nama_kondisi
                ORDER BY kb.id_kondisi
                SEPARATOR '|'
            ) AS daftar_kondisi,

            COUNT(*) AS total_unit,

            SUM(
                CASE
                    WHEN LOWER(TRIM(bu.status_unit)) = 'dipinjam'
                    THEN 1
                    ELSE 0
                END
            ) AS jumlah_dipinjam,

            SUM(
                CASE
                    WHEN LOWER(TRIM(bu.status_unit)) = 'tersedia'
                    THEN 1
                    ELSE 0
                END
            ) AS jumlah_tersedia

        FROM barang_unit bu

        LEFT JOIN kondisi_barang kb
            ON bu.id_kondisi = kb.id_kondisi

        GROUP BY bu.id_barang

    ) unit ON b.id_barang = unit.id_barang

    ORDER BY b.id_barang ASC

");

if (!$queryBarang) {
    die("Gagal mengambil data barang: " . mysqli_error($conn));
}


// MEMISAHKAN KAMERA DAN TRIPOD
$dataKamera = [];
$dataTripod = [];

while ($barang = mysqli_fetch_assoc($queryBarang)) {

    if ($barang['jenis_barang'] == 'kamera') {

        $dataKamera[] = $barang;

    } else if ($barang['jenis_barang'] == 'tripod') {

        $dataTripod[] = $barang;
    }
}


// MENGAMBIL DATA KONDISI
$queryKondisi = mysqli_query($conn, "
    SELECT *
    FROM kondisi_barang
    ORDER BY id_kondisi ASC
");

if (!$queryKondisi) {
    die("Gagal mengambil data kondisi: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sistem Peminjaman Kamera dan Tripod</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="assets/css/style.css?v=103">

</head>

<body>

<?php include 'includes/navbar.php'; ?>


<!-- NOTIFIKASI KERANJANG -->
<?php if (isset($_SESSION['pesan_keranjang'])): ?>

    <div class="notifikasi-keranjang" id="notifikasiKeranjang">

        <i class="fas fa-check-circle"></i>

        <span>
            <?= htmlspecialchars($_SESSION['pesan_keranjang']); ?>
        </span>

    </div>

    <?php unset($_SESSION['pesan_keranjang']); ?>

<?php endif; ?>


<!-- HERO SECTION -->
<section class="hero-section py-5">

    <div class="container">

        <div class="row align-items-center">

            <div class="col-lg-6">

                <h1 class="hero-title">Kamera & Tripod</h1>

                <p class="hero-text">
                    Daftar Kamera dan Tripod yang tersedia untuk anda pinjam.
                </p>

            </div>

            <div class="col-lg-6">

                <div class="row">

                    <div class="col-md-9">

                        <input
                            type="text"
                            id="searchProduk"
                            class="form-control search-box"
                            placeholder="Cari Kamera atau Tripod..."
                        >

                    </div>

                    <div class="col-md-3">

                        <select class="form-select search-box" id="filterStatus">

                            <option value="">Semua Kondisi</option>

                            <?php while ($kondisi = mysqli_fetch_assoc($queryKondisi)): ?>

                                <option value="<?= htmlspecialchars($kondisi['nama_kondisi']); ?>">

                                    <?= htmlspecialchars($kondisi['nama_kondisi']); ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- KATEGORI MENU -->
<section class="kategori-section">

    <div class="container">

        <div class="kategori-menu">

            <button class="kategori-btn active" data-kategori="Semua">
                Semua
            </button>

            <button class="kategori-btn" data-kategori="Kamera">
                Kamera
            </button>

            <button class="kategori-btn" data-kategori="Tripod">
                Tripod
            </button>

            <a href="keranjang.php" class="kategori-btn btn btn-ikon-keranjang">

                <span class="ikon-keranjang-plus">

                    <span class="tanda-plus">+</span>

                    <i class="fas fa-shopping-cart"></i>

                </span>

                <?php if ($total_keranjang > 0): ?>

                    <span class="badge-jumlah-keranjang">
                        <?= $total_keranjang; ?>
                    </span>

                <?php endif; ?>

            </a>

        </div>

    </div>

</section>


<!-- ================================================== -->
<!-- DAFTAR KAMERA -->
<!-- ================================================== -->

<section class="produk-section py-4" id="sectionKamera">

    <div class="container">

        <div class="mb-4">

            <h2 class="section-title">Kamera</h2>

            <p class="section-text">
                Pilihan kamera yang tersedia untuk anda dipinjam.
            </p>

        </div>

        <div class="row g-4">

            <?php foreach ($dataKamera as $barang): ?>

                <?php

                // KONDISI BARANG
                $daftarKondisi = $barang['daftar_kondisi'];

                $kondisiTampil = str_replace('|', ', ', $daftarKondisi);


                // INFORMASI STOK
                $totalUnit = (int) $barang['total_unit'];

                $jumlahDipinjam = (int) $barang['jumlah_dipinjam'];

                $jumlahTersedia = (int) $barang['jumlah_tersedia'];


                // MENENTUKAN STATUS BARANG
                if ($jumlahTersedia > 0) {

                    $statusBarang = 'Tersedia';
                    $warnaStatus = 'success';

                } elseif ($jumlahDipinjam > 0) {

                    $statusBarang = 'Dipinjam';
                    $warnaStatus = 'warning';

                } else {

                    $statusBarang = 'Tidak Tersedia';
                    $warnaStatus = 'danger';

                }

                ?>

                <div
                    class="col-lg-3 col-md-6 produk-item"
                    data-status="<?= htmlspecialchars($daftarKondisi); ?>"
                    data-kategori="<?= htmlspecialchars($barang['nama_kategori'] ?? ''); ?>"
                    data-jenis="<?= htmlspecialchars($barang['jenis_barang']); ?>"
                >

                    <div class="card produk-card">

                        <img
                            src="../uploads/barang/<?= htmlspecialchars($barang['foto']); ?>"
                            class="card-img-top produk-img"
                            alt="<?= htmlspecialchars($barang['nama_barang']); ?>"
                        >

                        <div class="card-body">

                            <span class="badge bg-primary mb-2">

                                <?= htmlspecialchars($barang['nama_kategori'] ?? 'Tanpa Kategori'); ?>

                            </span>

                            <h5 class="produk-title">

                                <?= htmlspecialchars($barang['nama_barang']); ?>

                            </h5>

                            <p class="produk-deskripsi">

                                <?= htmlspecialchars($barang['deskripsi'] ?? ''); ?>

                            </p>

                            <p class="produk-harga">

                                Rp<?= number_format($barang['harga_sewa'], 0, ',', '.'); ?>

                            </p>


                            <!-- INFORMASI KONDISI DAN STOK -->
                            <div class="produk-info">

                                <div class="produk-status">

                                    <strong>Kondisi:</strong>

                                    <?= htmlspecialchars($kondisiTampil); ?>

                                </div>


                                <div class="mt-2">

                                    <strong>Status:</strong>

                                    <span class="badge bg-<?= $warnaStatus; ?>">

                                        <?= htmlspecialchars($statusBarang); ?>

                                    </span>

                                </div>


                                <p class="produk-stok mt-2 mb-0">

                                    <i class="fas fa-box"></i>

                                    <?= $jumlahDipinjam; ?> barang dipinjam,
                                    tersisa <?= $jumlahTersedia; ?> barang.

                                </p>

                            </div>


                            <!-- TOMBOL DETAIL DAN KERANJANG -->
                            <div class="d-flex justify-content-between align-items-center mt-3">

                                <a
                                    href="detail_kamera.php?id=<?= (int)$barang['id_barang']; ?>"
                                    class="btn btn-detail btn-sm"
                                >

                                    Lihat Detail

                                </a>


                                <form
                                    method="POST"
                                    action=""
                                    class="form-keranjang d-inline-flex mb-0 flex-shrink-0"
                                >

                                    <input
                                        type="hidden"
                                        name="id_barang"
                                        value="<?= (int)$barang['id_barang']; ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="nama_barang"
                                        value="<?= htmlspecialchars($barang['nama_barang']); ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="kategori"
                                        value="<?= htmlspecialchars($barang['nama_kategori'] ?? ''); ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="gambar"
                                        value="<?= htmlspecialchars($barang['foto']); ?>"
                                    >

                                    <button
                                        type="submit"
                                        name="tambah_keranjang"
                                        value="1"
                                        class="btn btn-keranjang"
                                        title="Tambah ke Keranjang"
                                    >

                                        <span class="ikon-keranjang-plus">

                                            <span class="tanda-plus">+</span>

                                            <i class="fas fa-shopping-cart"></i>

                                        </span>

                                    </button>

                                </form>

                            </div>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>


        <div
            id="pesanKameraTidakDitemukan"
            class="text-center py-4"
            style="display: none;"
        >

            <p class="section-text">
                Data kamera tidak ditemukan.
            </p>

        </div>

    </div>

</section>



<!-- ================================================== -->
<!-- DAFTAR TRIPOD -->
<!-- ================================================== -->

<section class="produk-section py-4" id="sectionTripod">

    <div class="container">

        <div class="mb-4">

            <h2 class="section-title">Tripod</h2>

            <p class="section-text">
                Pilihan tripod yang tersedia untuk anda dipinjam.
            </p>

        </div>

        <div class="row g-4">

            <?php foreach ($dataTripod as $barang): ?>

                <?php

                // KONDISI BARANG
                $daftarKondisi = $barang['daftar_kondisi'];

                $kondisiTampil = str_replace('|', ', ', $daftarKondisi);


                // INFORMASI STOK
                $totalUnit = (int) $barang['total_unit'];

                $jumlahDipinjam = (int) $barang['jumlah_dipinjam'];

                $jumlahTersedia = (int) $barang['jumlah_tersedia'];


                // MENENTUKAN STATUS BARANG
                if ($jumlahTersedia > 0) {

                    $statusBarang = 'Tersedia';
                    $warnaStatus = 'success';

                } elseif ($jumlahDipinjam > 0) {

                    $statusBarang = 'Dipinjam';
                    $warnaStatus = 'warning';

                } else {

                    $statusBarang = 'Tidak Tersedia';
                    $warnaStatus = 'danger';

                }

                ?>

                <div
                    class="col-lg-3 col-md-6 produk-item"
                    data-status="<?= htmlspecialchars($daftarKondisi); ?>"
                    data-kategori="<?= htmlspecialchars($barang['nama_kategori'] ?? ''); ?>"
                    data-jenis="<?= htmlspecialchars($barang['jenis_barang']); ?>"
                >

                    <div class="card produk-card">

                        <img
                            src="../uploads/barang/<?= htmlspecialchars($barang['foto']); ?>"
                            class="card-img-top produk-img"
                            alt="<?= htmlspecialchars($barang['nama_barang']); ?>"
                        >

                        <div class="card-body">

                            <span class="badge bg-primary mb-2">

                                <?= htmlspecialchars($barang['nama_kategori'] ?? 'Tanpa Kategori'); ?>

                            </span>

                            <h5 class="produk-title">

                                <?= htmlspecialchars($barang['nama_barang']); ?>

                            </h5>

                            <p class="produk-deskripsi">

                                <?= htmlspecialchars($barang['deskripsi'] ?? ''); ?>

                            </p>

                            <p class="produk-harga">

                                Rp<?= number_format($barang['harga_sewa'], 0, ',', '.'); ?>

                            </p>


                            <!-- INFORMASI KONDISI DAN STOK -->
                            <div class="produk-info">

                                <div class="produk-status">

                                    <strong>Kondisi:</strong>

                                    <?= htmlspecialchars($kondisiTampil); ?>

                                </div>


                                <div class="mt-2">

                                    <strong>Status:</strong>

                                    <span class="badge bg-<?= $warnaStatus; ?>">

                                        <?= htmlspecialchars($statusBarang); ?>

                                    </span>

                                </div>


                                <p class="produk-stok mt-2 mb-0">

                                    <i class="fas fa-box"></i>

                                    <?= $jumlahDipinjam; ?> barang dipinjam,
                                    tersisa <?= $jumlahTersedia; ?> barang.

                                </p>

                            </div>


                            <!-- TOMBOL DETAIL DAN KERANJANG -->
                            <div class="d-flex justify-content-between align-items-center mt-3">

                                <a
                                    href="detail_tripod.php?id=<?= (int)$barang['id_barang']; ?>"
                                    class="btn btn-detail btn-sm"
                                >

                                    Lihat Detail

                                </a>


                                <form
                                    method="POST"
                                    action=""
                                    class="form-keranjang d-inline-flex mb-0 flex-shrink-0"
                                >

                                    <input
                                        type="hidden"
                                        name="id_barang"
                                        value="<?= (int)$barang['id_barang']; ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="nama_barang"
                                        value="<?= htmlspecialchars($barang['nama_barang']); ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="kategori"
                                        value="<?= htmlspecialchars($barang['nama_kategori'] ?? ''); ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="gambar"
                                        value="<?= htmlspecialchars($barang['foto']); ?>"
                                    >

                                    <button
                                        type="submit"
                                        name="tambah_keranjang"
                                        value="1"
                                        class="btn btn-keranjang"
                                        title="Tambah ke Keranjang"
                                    >

                                        <span class="ikon-keranjang-plus">

                                            <span class="tanda-plus">+</span>

                                            <i class="fas fa-shopping-cart"></i>

                                        </span>

                                    </button>

                                </form>

                            </div>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>


        <div
            id="pesanTripodTidakDitemukan"
            class="text-center py-4"
            style="display: none;"
        >

            <p class="section-text">
                Data tripod tidak ditemukan.
            </p>

        </div>

    </div>

</section>


<?php include 'includes/footer.php'; ?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>


<script>

// ==================================================
// FILTER PRODUK
// ==================================================

const searchProduk = document.getElementById('searchProduk');

const filterStatus = document.getElementById('filterStatus');

const produkItems = document.querySelectorAll('.produk-item');

const kategoriButtons = document.querySelectorAll('.kategori-btn');

const sectionKamera = document.getElementById('sectionKamera');

const sectionTripod = document.getElementById('sectionTripod');

const pesanKameraTidakDitemukan = document.getElementById('pesanKameraTidakDitemukan');

const pesanTripodTidakDitemukan = document.getElementById('pesanTripodTidakDitemukan');

let kategoriDipilih = 'Semua';


function filterProduk() {

    const kataKunci = searchProduk.value.toLowerCase();

    const statusDipilih = filterStatus.value;

    sectionKamera.style.display = kategoriDipilih === 'Tripod' ? 'none' : '';

    sectionTripod.style.display = kategoriDipilih === 'Kamera' ? 'none' : '';

    let jumlahKamera = 0;

    let jumlahTripod = 0;


    produkItems.forEach(function(produk) {

        const namaProduk = produk.textContent.toLowerCase();

        const statusProduk = (produk.getAttribute('data-status') || '').split('|');

        const jenisProduk = produk.getAttribute('data-jenis');


        const cocokSearch = namaProduk.includes(kataKunci);

        const cocokStatus = statusDipilih === '' || statusProduk.includes(statusDipilih);

        const cocokKategori = kategoriDipilih === 'Semua' || jenisProduk === kategoriDipilih.toLowerCase();


        if (cocokSearch && cocokStatus && cocokKategori) {

            produk.style.display = '';

            if (jenisProduk === 'kamera') {
                jumlahKamera++;
            }

            if (jenisProduk === 'tripod') {
                jumlahTripod++;
            }

        } else {

            produk.style.display = 'none';

        }

    });


    if (kategoriDipilih === 'Kamera') {

        pesanKameraTidakDitemukan.style.display = jumlahKamera === 0 ? '' : 'none';

        pesanTripodTidakDitemukan.style.display = 'none';

    } else if (kategoriDipilih === 'Tripod') {

        pesanTripodTidakDitemukan.style.display = jumlahTripod === 0 ? '' : 'none';

        pesanKameraTidakDitemukan.style.display = 'none';

    } else {

        pesanKameraTidakDitemukan.style.display = jumlahKamera === 0 ? '' : 'none';

        pesanTripodTidakDitemukan.style.display = jumlahTripod === 0 ? '' : 'none';

    }

}


searchProduk.addEventListener('input', filterProduk);

filterStatus.addEventListener('change', filterProduk);


kategoriButtons.forEach(function(button) {

    button.addEventListener('click', function() {

        const kategori = this.getAttribute('data-kategori');

        if (!kategori) {
            return;
        }

        kategoriDipilih = kategori;

        kategoriButtons.forEach(function(btn) {
            btn.classList.remove('active');
        });

        this.classList.add('active');

        filterProduk();

    });

});



// ==================================================
// TAMBAH KERANJANG TANPA REFRESH
// ==================================================

document.querySelectorAll('.form-keranjang').forEach(form => {

    form.addEventListener('submit', function(e) {

        e.preventDefault();

        const data = new FormData(form);

        data.append('tambah_keranjang', '1');

        data.append('ajax', '1');


        fetch(window.location.href, {

            method: 'POST',

            body: data

        })

        .then(response => {

            if (!response.ok) {
                throw new Error('Respons server tidak berhasil.');
            }

            return response.json();

        })

        .then(hasil => {

            let badge = document.querySelector('.badge-jumlah-keranjang');


            if (!badge) {

                badge = document.createElement('span');

                badge.className = 'badge-jumlah-keranjang';

                document.querySelector('.btn-ikon-keranjang').appendChild(badge);

            }


            badge.textContent = hasil.total;


            let notifikasi = document.getElementById('notifikasiKeranjang');


            if (notifikasi) {
                notifikasi.remove();
            }


            notifikasi = document.createElement('div');

            notifikasi.id = 'notifikasiKeranjang';

            notifikasi.className = 'notifikasi-keranjang';

            notifikasi.innerHTML = '<i class="fas fa-check-circle"></i><span></span>';

            notifikasi.querySelector('span').textContent = hasil.pesan;

            document.body.appendChild(notifikasi);


            setTimeout(() => {

                notifikasi.classList.add('hilang');

                setTimeout(() => notifikasi.remove(), 500);

            }, 3500);

        })

        .catch(error => {

            console.error('Gagal menambahkan barang:', error);

        });

    });

});



// ==================================================
// ANIMASI SAAT SCROLL
// ==================================================

if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {

    const teksBagianProduk = document.querySelectorAll(

        '.produk-section .container > .mb-4 .section-title, ' +

        '.produk-section .container > .mb-4 .section-text'

    );


    teksBagianProduk.forEach(function(teks) {

        teks.classList.add('scroll-hidden');

        teks.classList.remove('show-text');

    });


    const pengamatTeks = new IntersectionObserver(function(entries) {

        entries.forEach(function(entry) {

            if (entry.isIntersecting) {

                entry.target.classList.remove('scroll-hidden');

                entry.target.classList.add('show-text');

            } else if (entry.boundingClientRect.top > 0) {

                entry.target.classList.add('scroll-hidden');

                entry.target.classList.remove('show-text');

            }

        });

    }, {

        threshold: 0.1

    });


    teksBagianProduk.forEach(function(teks) {

        pengamatTeks.observe(teks);

    });


    const kartuProduk = document.querySelectorAll('.produk-item');


    kartuProduk.forEach(function(kartu, index) {

        kartu.classList.add('reveal-card');

        kartu.classList.remove('show-card');

        kartu.style.transitionDelay = (index % 4) * 100 + 'ms';

    });


    const pengamatKartu = new IntersectionObserver(function(entries) {

        entries.forEach(function(entry) {

            if (entry.isIntersecting) {

                entry.target.classList.add('show-card');

            } else if (entry.boundingClientRect.top > 0) {

                entry.target.classList.remove('show-card');

            }

        });

    }, {

        threshold: 0.1

    });


    kartuProduk.forEach(function(kartu) {

        pengamatKartu.observe(kartu);

    });

}



// ==================================================
// NOTIFIKASI KERANJANG
// ==================================================

const notifikasi = document.getElementById('notifikasiKeranjang');

if (notifikasi) {

    setTimeout(() => {

        notifikasi.classList.add('hilang');

        setTimeout(() => {

            notifikasi.remove();

        }, 500);

    }, 2500);

}

</script>

</body>
</html>
