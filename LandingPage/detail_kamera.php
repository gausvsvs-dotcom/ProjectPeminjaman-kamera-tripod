<?php

$id = $_GET['id'] ?? 1;

$kamera = [
    1 => [
        'nama' => 'Canon EOS 700D',
        'gambar' => 'assets/images/kamera/canon700d.png',
        'deskripsi' => 'Canon EOS 700D merupakan kamera DSLR yang cocok digunakan untuk kebutuhan fotografi maupun dokumentasi.',
        'spesifikasi' => ['18 MP', 'APS-C', 'Full HD'],
        'kondisi' => 'Baik',
        'status' => 'Dipinjam'
    ],

    2 => [
        'nama' => 'Nikon D7500',
        'gambar' => 'assets/images/kamera/NikonD7500.jpg',
        'deskripsi' => 'Nikon D7500 merupakan kamera yang cocok digunakan untuk kebutuhan fotografi maupun dokumentasi.',
        'spesifikasi' => ['24.2 MP', 'APS-C', 'Full HD'],
        'kondisi' => 'Baik',
        'status' => 'Tersedia'
    ],

    3 => [
        'nama' => 'Nikon D5600',
        'gambar' => 'assets/images/kamera/NikonD5600.jpg',
        'deskripsi' => 'Nikon D5600 merupakan kamera yang cocok digunakan untuk kebutuhan fotografi maupun dokumentasi.',
        'spesifikasi' => ['20.9 MP', 'APS-C', '4K UHD'],
        'kondisi' => 'Baik',
        'status' => 'Tersedia'
    ],

    4 => [
        'nama' => 'Fujifilm X-S10',
        'gambar' => 'assets/images/kamera/FujifilmX-S10.jpg',
        'deskripsi' => 'Fujifilm X-S10 merupakan kamera mirrorless yang cocok digunakan untuk fotografi dan video.',
        'spesifikasi' => ['24.3 MP', 'APS-C', 'Full HD'],
        'kondisi' => 'Baik',
        'status' => 'Tersedia'
    ],

    5 => [
        'nama' => 'Sony Alpha A6400',
        'gambar' => 'assets/images/kamera/SonnyAlphaa6400.jpg',
        'deskripsi' => 'Sony Alpha A6400 merupakan kamera mirrorless yang cocok digunakan untuk kebutuhan fotografi dan video.',
        'spesifikasi' => ['24.2 MP', 'APS-C', '4K UHD'],
        'kondisi' => 'Baik',
        'status' => 'Perawatan'
    ],

    6 => [
        'nama' => 'Fujifilm X-T30',
        'gambar' => 'assets/images/kamera/FujifilmX-T30.jpg',
        'deskripsi' => 'Fujifilm X-T30 merupakan kamera mirrorless yang cocok digunakan untuk fotografi maupun dokumentasi.',
        'spesifikasi' => ['26.1 MP', 'APS-C', '4K UHD'],
        'kondisi' => 'Baik',
        'status' => 'Dipinjam'
    ],

    7 => [
        'nama' => 'Canon EOS 90D',
        'gambar' => 'assets/images/kamera/Canon90d.jpg',
        'deskripsi' => 'Canon EOS 90D merupakan kamera DSLR yang cocok digunakan untuk kebutuhan fotografi maupun video.',
        'spesifikasi' => ['16 MP', 'Micro 4/3', '4K UHD'],
        'kondisi' => 'Baik',
        'status' => 'Dipinjam'
    ],

    8 => [
        'nama' => 'Canon EOS 80D',
        'gambar' => 'assets/images/kamera/Canon80d.jpg',
        'deskripsi' => 'Canon EOS 80D merupakan kamera DSLR yang cocok digunakan untuk kebutuhan fotografi maupun dokumentasi.',
        'spesifikasi' => ['24.1 MP', 'APS-C', '4K UHD'],
        'kondisi' => 'Baik',
        'status' => 'Tersedia'
    ]
];

$data = $kamera[$id] ?? $kamera[1];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detail Kamera - Sistem Peminjaman Kamera dan Tripod</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">

    <link rel="stylesheet"
          href="assets/css/style.css">
</head>

<body>

<?php include 'includes/navbar.php'; ?>


<section class="detail-produk-section py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <!-- FOTO KAMERA -->
            <div class="col-lg-6">
                <div class="detail-produk-image">
                    <img src="<?= $data['gambar'] ?>"
                    alt="<?= $data['nama'] ?>"
                    class="img-fluid">
                </div>

            </div>


            <!-- INFORMASI KAMERA -->
            <div class="col-lg-6">

                <span class="badge bg-primary mb-3">
                    Kamera
                </span>

                <h1 class="detail-produk-title">
                    <?= $data['nama'] ?>
                </h1>

                <p class="detail-produk-description">
                    <?= $data['deskripsi'] ?>
                </p>


                <h5 class="detail-produk-heading">
                    Spesifikasi
                </h5>

                <div class="detail-produk-spesifikasi">

                    <?php foreach ($data['spesifikasi'] as $spesifikasi): ?>
                        <span><?= $spesifikasi ?></span>
                    <?php endforeach; ?>

                </div>


                <div class="detail-produk-info">

                    <p><strong>Kondisi:</strong> <?= $data['kondisi'] ?></p>
                    <p><strong>Status:</strong> <?= $data['status'] ?></p>

                </div>


                <a href="kamera&tripod.php"
                   class="btn btn-detail">
                   ~ Kembali ~
                </a>
            </div>
        </div>
    </div>
</section>


<?php include 'includes/footer.php'; ?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js">
</script>

</body>
</html>