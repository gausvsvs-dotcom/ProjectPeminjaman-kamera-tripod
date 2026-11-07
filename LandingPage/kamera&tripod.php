<?php
session_start();

require_once __DIR__ . '/includes/koneksi.php';

// Inisialisasi keranjang
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// Proses menambahkan barang ke keranjang
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

        $_SESSION['keranjang'][$id_barang]['jumlah']++;

        $pesan = $nama_barang . ' berhasil ditambahkan lagi!';
    }

    // Respons untuk AJAX
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

// Menghitung jumlah barang
$total_keranjang = 0;

foreach ($_SESSION['keranjang'] as $barang) {
    $total_keranjang += $barang['jumlah'];
}

// Mengambil data kamera
$queryKamera = mysqli_query($conn, "
    SELECT 
        b.*,
        k.nama_kategori,
        kb.nama_kondisi,
        m.nama_merk
    FROM barang b
    LEFT JOIN kategori k ON b.id_kategori = k.id_kategori
    LEFT JOIN kondisi_barang kb ON b.id_kondisi = kb.id_kondisi
    LEFT JOIN merk m ON b.id_merk = m.id_merk
    WHERE b.jenis_barang = 'kamera'
    ORDER BY b.id_barang ASC
");

// Mengambil data tripod
$queryTripod = mysqli_query($conn, "
    SELECT 
        b.*,
        k.nama_kategori,
        kb.nama_kondisi,
        m.nama_merk
    FROM barang b
    LEFT JOIN kategori k ON b.id_kategori = k.id_kategori
    LEFT JOIN kondisi_barang kb ON b.id_kondisi = kb.id_kondisi
    LEFT JOIN merk m ON b.id_merk = m.id_merk
    WHERE b.jenis_barang = 'tripod'
    ORDER BY b.id_barang ASC
");

// Mengambil data kondisi barang
$queryKondisi = mysqli_query($conn, "
    SELECT * FROM kondisi_barang
    ORDER BY id_kondisi ASC
");

?>


<!DOCTYPE html>
<html lang="en">
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

    <?php if (isset($_SESSION['pesan_keranjang'])): ?>
    <div class="notifikasi-keranjang" id="notifikasiKeranjang">
        <i class="fas fa-check-circle"></i>
        <span><?= htmlspecialchars($_SESSION['pesan_keranjang']); ?></span>
    </div>

    <?php unset($_SESSION['pesan_keranjang']); ?>
    <?php endif; ?>

    <section class="hero-section py-5">

    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="hero-title">
                    Kamera & Tripod
                </h1>

                <p class="hero-text">
                    Daftar Kamera dan Tripod yang tersedia untuk anda pinjam.
                </p>

            </div>

            <div class="col-lg-6">
                <div class="row">
                    <div class="col-md-9">
                        <input type="text" id="searchProduk" class="form-control search-box"
                        placeholder="Cari Kamera atau Tripod...">
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


<!-- SECTION KAMERA -->
<section class="produk-section py-4" id="sectionKamera">
    <div class="container">

        <div class="mb-4">
            <h2 class="section-title">Kamera</h2>
            <p class="section-text">Pilihan kamera yang tersedia untuk anda dipinjam.</p>
        </div>

        <div class="row g-4">

            <?php while ($barang = mysqli_fetch_assoc($queryKamera)): ?>

                <div class="col-lg-3 col-md-6 produk-item"
                     data-status="<?= htmlspecialchars($barang['nama_kondisi']); ?>"
                     data-kategori="<?= htmlspecialchars($barang['nama_kategori']); ?>">

                    <div class="card produk-card">

                        <img src="<?= htmlspecialchars($barang['foto']); ?>"
                             class="card-img-top produk-img"
                             alt="<?= htmlspecialchars($barang['nama_barang']); ?>">

                        <div class="card-body">

                            <span class="badge bg-primary mb-2">
                                <?= htmlspecialchars($barang['nama_kategori']); ?>
                            </span>

                            <h5 class="produk-title">
                                <?= htmlspecialchars($barang['nama_barang']); ?>
                            </h5>

                            <p class="produk-deskripsi">
                                <?= htmlspecialchars($barang['deskripsi']); ?>
                            </p>

                            <div class="produk-status">
                                <?= htmlspecialchars($barang['nama_kondisi']); ?>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-3">

                                <a href="detail_kamera.php?id=<?= (int)$barang['id_barang']; ?>"
                                   class="btn btn-detail btn-sm">
                                    Lihat Detail
                                </a>

                                <form method="POST" action="" class="form-keranjang d-inline-flex mb-0 flex-shrink-0">

                                    <input type="hidden" name="id_barang"
                                           value="<?= (int)$barang['id_barang']; ?>">

                                    <input type="hidden" name="nama_barang"
                                           value="<?= htmlspecialchars($barang['nama_barang']); ?>">

                                    <input type="hidden" name="kategori"
                                           value="<?= htmlspecialchars($barang['nama_kategori']); ?>">

                                    <input type="hidden" name="gambar"
                                           value="<?= htmlspecialchars($barang['foto']); ?>">

                                    <button type="submit"
                                            name="tambah_keranjang"
                                            value="1"
                                            class="btn btn-keranjang"
                                            title="Tambah ke Keranjang">
                                        
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

            <?php endwhile; ?>

        </div>

        <div id="pesanKameraTidakDitemukan" class="text-center py-4" style="display: none;">
            <p class="section-text">Data kamera tidak ditemukan.</p>
        </div>

    </div>
</section>


<!-- SECTION TRIPOD -->
<section class="produk-section py-4" id="sectionTripod">
    <div class="container">

        <div class="mb-4">
            <h2 class="section-title">Tripod</h2>
            <p class="section-text">Pilihan tripod yang tersedia untuk anda dipinjam.</p>
        </div>

        <div class="row g-4">

            <?php while ($barang = mysqli_fetch_assoc($queryTripod)): ?>

                <div class="col-lg-3 col-md-6 produk-item"
                     data-status="<?= htmlspecialchars($barang['nama_kondisi']); ?>"
                     data-kategori="<?= htmlspecialchars($barang['nama_kategori']); ?>">

                    <div class="card produk-card">

                        <img src="<?= htmlspecialchars($barang['foto']); ?>"
                             class="card-img-top produk-img"
                             alt="<?= htmlspecialchars($barang['nama_barang']); ?>">

                        <div class="card-body">

                            <span class="badge bg-primary mb-2">
                                <?= htmlspecialchars($barang['nama_kategori']); ?>
                            </span>

                            <h5 class="produk-title">
                                <?= htmlspecialchars($barang['nama_barang']); ?>
                            </h5>

                            <p class="produk-deskripsi">
                                <?= htmlspecialchars($barang['deskripsi']); ?>
                            </p>

                            <div class="produk-status">
                                <?= htmlspecialchars($barang['nama_kondisi']); ?>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-3">

                                <a href="detail_tripod.php?id=<?= (int)$barang['id_barang']; ?>"
                                   class="btn btn-detail btn-sm">
                                    Lihat Detail
                                </a>

                                <form method="POST" action="" class="form-keranjang d-inline-flex mb-0 flex-shrink-0">

                                    <input type="hidden" name="id_barang"
                                           value="<?= (int)$barang['id_barang']; ?>">

                                    <input type="hidden" name="nama_barang"
                                           value="<?= htmlspecialchars($barang['nama_barang']); ?>">

                                    <input type="hidden" name="kategori"
                                           value="<?= htmlspecialchars($barang['nama_kategori']); ?>">

                                    <input type="hidden" name="gambar"
                                           value="<?= htmlspecialchars($barang['foto']); ?>">

                                    <button type="submit"
                                            name="tambah_keranjang"
                                            value="1"
                                            class="btn btn-keranjang"
                                            title="Tambah ke Keranjang">
                                        
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

            <?php endwhile; ?>

        </div>

        <div id="pesanTripodTidakDitemukan" class="text-center py-4" style="display: none;">
            <p class="section-text">Data tripod tidak ditemukan.</p>
        </div>

    </div>
</section>



    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

    <script>
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

        sectionKamera.style.display =
        kategoriDipilih === 'Tripod' ? 'none' : '';
        
        sectionTripod.style.display =
        kategoriDipilih === 'Kamera' ? 'none' : '';

        let jumlahKamera = 0;
        let jumlahTripod = 0;

        produkItems.forEach(function (produk) {

            const namaProduk = produk.textContent.toLowerCase();
            const statusProduk = produk.getAttribute('data-status');
            const kategoriProduk = produk.getAttribute('data-kategori');

            const cocokSearch = namaProduk.includes(kataKunci);
            const cocokStatus =
                statusDipilih === '' || statusProduk === statusDipilih;
            const cocokKategori =
                kategoriDipilih === 'Semua' ||
                kategoriProduk === kategoriDipilih;

            if (cocokSearch && cocokStatus && cocokKategori) {
                produk.style.display = '';
                
                if (kategoriProduk === 'Kamera') {
                    jumlahKamera++;
                }
                if (kategoriProduk === 'Tripod') {
                    jumlahTripod++;
                }
            } else {
                produk.style.display = 'none';
            }
        });

        if (kategoriDipilih === 'Kamera') {
            pesanKameraTidakDitemukan.style.display =
            jumlahKamera === 0 ? '' : 'none';
            
            pesanTripodTidakDitemukan.style.display = 'none';
        
        } else if (kategoriDipilih === 'Tripod') {
            pesanTripodTidakDitemukan.style.display =
            jumlahTripod === 0 ? '' : 'none';
            
            pesanKameraTidakDitemukan.style.display = 'none';
        
        } else {
            pesanKameraTidakDitemukan.style.display =
            jumlahKamera === 0 ? '' : 'none';
            
            pesanTripodTidakDitemukan.style.display =
            jumlahTripod === 0 ? '' : 'none';
        }
    }

    searchProduk.addEventListener('input', filterProduk);

    filterStatus.addEventListener('change', filterProduk);

    kategoriButtons.forEach(function (button) {

        button.addEventListener('click', function () {

            kategoriDipilih = this.getAttribute('data-kategori');

            kategoriButtons.forEach(function (btn) {
                btn.classList.remove('active');
            });

            this.classList.add('active');

            filterProduk();
        });

    });

// Tambah Keranjang tanpa Refresh Halaman
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
        .then(response => response.json())
        .then(hasil => {

            // Memperbarui jumlah keranjang
            let badge = document.querySelector('.badge-jumlah-keranjang');

            if (!badge) {
                badge = document.createElement('span');
                badge.className = 'badge-jumlah-keranjang';

                document.querySelector('.btn-ikon-keranjang')
                    .appendChild(badge);
            }

            badge.textContent = hasil.total;

            // Menampilkan notifikasi
            let notifikasi = document.getElementById('notifikasiKeranjang');

            if (notifikasi) {
                notifikasi.remove();
            }

            notifikasi = document.createElement('div');
            notifikasi.id = 'notifikasiKeranjang';
            notifikasi.className = 'notifikasi-keranjang';

            notifikasi.innerHTML = `
                <i class="fas fa-check-circle"></i>
                <span></span>
            `;

            notifikasi.querySelector('span').textContent = hasil.pesan;

            document.body.appendChild(notifikasi);

            // Menghilangkan notifikasi setelah 3,5 detik
            setTimeout(() => {
                notifikasi.classList.add('hilang');

                setTimeout(() => {
                    notifikasi.remove();
                }, 500);

            }, 3500);

        })
        .catch(error => {
            console.error('Gagal menambahkan barang:', error);
        });
    });

});
    
/* =========================================
   ANIMASI BERULANG SAAT SCROLL KE BAWAH
========================================= */

if (
    'IntersectionObserver' in window &&
    !window.matchMedia('(prefers-reduced-motion: reduce)').matches
) {
    let posisiScroll = window.scrollY;

    // Catat arah scroll
    window.addEventListener('scroll', function () {
        posisiScroll = window.scrollY;
    }, { passive: true });

    // Animasi judul dan deskripsi
    const teksBagianProduk = document.querySelectorAll(
        '.produk-section .container > .mb-4 .section-title, ' +
        '.produk-section .container > .mb-4 .section-text'
    );

    teksBagianProduk.forEach(function (teks) {
        teks.classList.add('scroll-hidden');
        teks.classList.remove('show-text');
    });

    const pengamatTeks = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.remove('scroll-hidden');
                entry.target.classList.add('show-text');
            } else if (entry.boundingClientRect.top > 0) {
                // Siapkan animasi lagi ketika elemen berada
                // di bawah layar dan akan dimasuki saat scroll turun
                entry.target.classList.add('scroll-hidden');
                entry.target.classList.remove('show-text');
            }
        });
    }, {
        threshold: 0.1
    });

    teksBagianProduk.forEach(function (teks) {
        pengamatTeks.observe(teks);
    });

    // Animasi kartu kamera dan tripod
    const kartuProduk = document.querySelectorAll('.produk-item');

    kartuProduk.forEach(function (kartu, index) {
        kartu.classList.add('reveal-card');
        kartu.classList.remove('show-card');
        kartu.style.transitionDelay = (index % 4) * 100 + 'ms';
    });

    const pengamatKartu = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('show-card');
            } else if (entry.boundingClientRect.top > 0) {
                // Siapkan animasi kembali saat kartu masih
                // berada di bawah layar
                entry.target.classList.remove('show-card');
            }
        });
    }, {
        threshold: 0.1
    });

    kartuProduk.forEach(function (kartu) {
        pengamatKartu.observe(kartu);
    });
}

//keranjang
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