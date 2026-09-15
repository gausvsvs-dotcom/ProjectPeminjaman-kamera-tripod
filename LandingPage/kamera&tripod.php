<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Peminjaman Kamera dan Tripod</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

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
                        <option value=""> Semua Kondisi </option>
                        <option value="Tersedia"> Tersedia </option>
                        <option value="Dipinjam"> Dipinjam </option>
                        <option value="Perawatan"> Perawatan</option>
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
        </div>
        </div>
    </section>

<section class="produk-section py-4" id="sectionKamera">
    <div class="container">
        <div class="mb-4">
            <h2 class="section-title">Kamera</h2>
             <p class="section-text">Pilihan kamera yang tersedia untuk anda dipinjam. </p>
        </div>
        <div class="row g-4">

            <div class="col-lg-3 col-md-6 produk-item" data-status="Dipinjam" data-kategori="Kamera">

                <div class="card produk-card h-100">

                    <img src="assets/images/kamera/canon700d.png"
                         class="card-img-top produk-img"
                         alt="Canon EOS 700D">

                    <div class="card-body">

                        <span class="badge bg-primary mb-2">
                            Kamera
                        </span>

                        <h5 class="produk-title">
                            Canon EOS 700D
                        </h5>

                        <div class="produk-spesifikasi">
                            <span>18 MP</span>
                            <span>APS-C</span>
                            <span>Full HD</span>
                        </div>

                        <div class="produk-status">
                            Dipinjam
                        </div>

                        <div class="mt-3">
                            <a href="detail_kamera.php?id=1" class="btn btn-detail btn-sm">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 produk-item" data-status="Tersedia" data-kategori="Kamera">

                <div class="card produk-card h-100">

                    <img src="assets/images/kamera/NikonD7500.jpg"
                         class="card-img-top produk-img"
                         alt="Nikon D5600">

                    <div class="card-body">

                        <span class="badge bg-primary mb-2">
                            Kamera
                        </span>

                        <h5 class="produk-title">
                            Nikon D7500
                        </h5>

                        <div class="produk-spesifikasi">
                            <span>24.2 MP</span>
                            <span>APS-C</span>
                            <span>Full HD</span>
                        </div>

                        <div class="produk-status">
                            Tersedia
                        </div>

                        <div class="mt-3">
                            <a href="detail_kamera.php?id=2" class="btn btn-detail btn-sm">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 produk-item" data-status="Tersedia" data-kategori="Kamera">

                <div class="card produk-card h-100">

                    <img src="assets/images/kamera/NikonD5600.jpg"
                         class="card-img-top produk-img"
                         alt="Nikon D7500">

                    <div class="card-body">

                        <span class="badge bg-primary mb-2">
                            Kamera
                        </span>

                        <h5 class="produk-title">
                            Nikon D5600
                        </h5>

                        <div class="produk-spesifikasi">
                            <span>20.9 MP</span>
                            <span>APS-C</span>
                            <span>4K UHD</span>
                        </div>

                        <div class="produk-status">
                            Tersedia
                        </div>

                        <div class="mt-3">
                            <a href="detail_kamera.php?id=3" class="btn btn-detail btn-sm">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 produk-item" data-status="Tersedia" data-kategori="Kamera">

                <div class="card produk-card h-100">

                    <img src="assets/images/kamera/FujifilmX-S10.jpg"
                         class="card-img-top produk-img"
                         alt="Sony Alpha A6000">

                    <div class="card-body">

                        <span class="badge bg-primary mb-2">
                            Kamera
                        </span>

                        <h5 class="produk-title">
                            Fujifilm X-S10
                        </h5>

                        <div class="produk-spesifikasi">
                            <span>24.3 MP</span>
                            <span>APS-C</span>
                            <span>Full HD</span>
                        </div>

                        <div class="produk-status">
                            Tersedia
                        </div>

                        <div class="mt-3">
                            <a href="detail_kamera.php?id=4" class="btn btn-detail btn-sm">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 produk-item" data-status="Perawatan" data-kategori="Kamera">

                <div class="card produk-card h-100">

                    <img src="assets/images/kamera/SonnyAlphaa6400.jpg"
                         class="card-img-top produk-img"
                         alt="Sony Alpha A6400">

                    <div class="card-body">

                        <span class="badge bg-primary mb-2">
                            Kamera
                        </span>

                        <h5 class="produk-title">
                            Sonny Alpha A6400
                        </h5>

                        <div class="produk-spesifikasi">
                            <span>24.2 MP</span>
                            <span>APS-C</span>
                            <span>4K UHD</span>
                        </div>

                        <div class="produk-status">
                            Perawatan
                        </div>

                        <div class="mt-3">
                            <a href="detail_kamera.php?id=5" class="btn btn-detail btn-sm">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 produk-item" data-status="Dipinjam" data-kategori="Kamera">

                <div class="card produk-card h-100">

                    <img src="assets/images/kamera/FujifilmX-T30.jpg"
                         class="card-img-top produk-img"
                         alt="Fujifilm X-T30">

                    <div class="card-body">

                        <span class="badge bg-primary mb-2">
                            Kamera
                        </span>

                        <h5 class="produk-title">
                            Fujifilm X-T30
                        </h5>

                        <div class="produk-spesifikasi">
                            <span>26.1 MP</span>
                            <span>APS-C</span>
                            <span>4K UHD</span>
                        </div>

                        <div class="produk-status">
                            Dipinjam
                        </div>

                        <div class="mt-3">
                            <a href="detail_kamera.php?id=6" class="btn btn-detail btn-sm">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 produk-item" data-status="Dipinjam" data-kategori="Kamera">

                <div class="card produk-card h-100">

                    <img src="assets/images/kamera/Canon90d.jpg"
                         class="card-img-top produk-img"
                         alt="Panasonic Lumix G7">

                    <div class="card-body">

                        <span class="badge bg-primary mb-2">
                            Kamera
                        </span>

                        <h5 class="produk-title">
                            Canon EOS 90D
                        </h5>

                        <div class="produk-spesifikasi">
                            <span>16 MP</span>
                            <span>Micro 4/3</span>
                            <span>4K UHD</span>
                        </div>

                        <div class="produk-status">
                            Dipinjam
                        </div>

                        <div class="mt-3">
                            <a href="detail_kamera.php?id=7" class="btn btn-detail btn-sm">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 produk-item" data-status="Tersedia" data-kategori="Kamera">

                <div class="card produk-card h-100">

                    <img src="assets/images/kamera/Canon80d.jpg"
                         class="card-img-top produk-img"
                         alt="Canon EOS 80D">

                    <div class="card-body">

                        <span class="badge bg-primary mb-2">
                            Kamera
                        </span>

                        <h5 class="produk-title">
                            Canon EOS 80D
                        </h5>

                        <div class="produk-spesifikasi">
                            <span>24.1 MP</span>
                            <span>APS-C</span>
                            <span>4K UHD</span>
                        </div>

                        <div class="produk-status">
                            Tersedia
                        </div>

                        <div class="mt-3">
                            <a href="detail_kamera.php?id=8" class="btn btn-detail btn-sm">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="produk-section py-4" id="sectionTripod">

    <div class="container">
       <div class="mb-4">
            <h2 class="section-title">Tripod</h2>
             <p class="section-text">Pilihan tripod yang tersedia untuk anda dipinjam.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6 produk-item" data-status="Perawatan" data-kategori="Tripod">

                <div class="card produk-card h-100">

                    <img src="assets/images/tripod/eco-196A.jpg"
                         class="card-img-top produk-img"
                         alt="Takara WT-3110">

                    <div class="card-body">

                        <span class="badge bg-primary mb-2">
                            Tripod
                        </span>

                        <h5 class="produk-title">
                            ECO 196A
                        </h5>

                        <div class="produk-spesifikasi">
                            <span>125 cm</span>
                            <span>Aluminium</span>
                            <span>Ringan</span>
                        </div>

                        <div class="produk-status">
                            Perawatan
                        </div>

                        <div class="mt-3">
                            <a href="detail_tripod.php?id=1" class="btn btn-detail btn-sm">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 produk-item" data-status="Dipinjam" data-kategori="Tripod">
                <div class="card produk-card h-100">
                    <img src="assets/images/tripod/W-3110A.jpg"
                         class="card-img-top produk-img"
                         alt="Takara Eco-173A">

                    <div class="card-body">

                        <span class="badge bg-primary mb-2">
                            Tripod
                        </span>

                        <h5 class="produk-title">
                            Weifeng WT-3110A
                        </h5>

                        <div class="produk-spesifikasi">
                            <span>130 cm</span>
                            <span>Aluminium</span>
                            <span>Ringan</span>
                        </div>

                        <div class="produk-status">
                            Dipinjam
                        </div>

                        <div class="mt-3">
                            <a href="detail_tripod.php?id=2" class="btn btn-detail btn-sm">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 produk-item" data-status="Tersedia" data-kategori="Tripod">
                <div class="card produk-card h-100">
                    <img src="assets/images/tripod/W-3520.jpg"
                         class="card-img-top produk-img"
                         alt="Takara Eco-193A">

                    <div class="card-body">

                        <span class="badge bg-primary mb-2">
                            Tripod
                        </span>

                        <h5 class="produk-title">
                            Weifeng WT-3520
                        </h5>

                        <div class="produk-spesifikasi">
                            <span>145 cm</span>
                            <span>Aluminium</span>
                            <span>Stabil</span>
                        </div>

                        <div class="produk-status">
                            Tersedia
                        </div>

                        <div class="mt-3">
                            <a href="detail_tripod.php?id=3" class="btn btn-detail btn-sm">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
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

    let kategoriDipilih = 'Semua';

    function filterProduk() {

        const kataKunci = searchProduk.value.toLowerCase();
        const statusDipilih = filterStatus.value;

        sectionKamera.style.display =
        kategoriDipilih === 'Tripod' ? 'none' : '';
        
        sectionTripod.style.display =
        kategoriDipilih === 'Kamera' ? 'none' : '';

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
            } else {
                produk.style.display = 'none';
            }

        });
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
</script>
</body>
</html>