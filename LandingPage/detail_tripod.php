<?php

$id = $_GET['id'] ?? 1;

$tripod = [
    1 => [
        'nama' => 'ECO 196A',
        'gambar' => 'assets/images/tripod/eco-196A.jpg',
        'deskripsi' => 'ECO 196A merupakan tripod ringan yang dapat digunakan untuk membantu menjaga kamera tetap stabil saat digunakan untuk mengambil foto maupun video.',
        'spesifikasi' => ['125 cm', 'Aluminium', 'Ringan'],
        'kondisi' => 'Baik',
        'status' => 'Perawatan'
    ],

    2 => [
        'nama' => 'Weifeng WT-3110A',
        'gambar' => 'assets/images/tripod/W-3110A.jpg',
        'deskripsi' => 'Weifeng WT-3110A merupakan tripod yang dapat digunakan untuk membantu menjaga kamera tetap stabil saat digunakan untuk mengambil foto maupun video.',
        'spesifikasi' => ['130 cm', 'Aluminium', 'Ringan'],
        'kondisi' => 'Baik',
        'status' => 'Dipinjam'
    ],

    3 => [
        'nama' => 'Weifeng WT-3520',
        'gambar' => 'assets/images/tripod/W-3520.jpg',
        'deskripsi' => 'Weifeng WT-3520 merupakan tripod yang dapat digunakan untuk membantu menjaga kamera tetap stabil saat digunakan untuk mengambil foto maupun video.',
        'spesifikasi' => ['145 cm', 'Aluminium', 'Stabil'],
        'kondisi' => 'Baik',
        'status' => 'Tersedia'
    ]
];

$data = $tripod[$id] ?? $tripod[1];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detail Tripod - Sistem Peminjaman Kamera dan Tripod</title>

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

            <!-- FOTO TRIPOD -->
            <div class="col-lg-6">
                <div class="detail-produk-image">
                    <img src="<?= $data['gambar'] ?>"
                    alt="<?= $data['nama'] ?>"
                    class="img-fluid">

                </div>

            </div>


            <!-- INFORMASI TRIPOD -->
            <div class="col-lg-6">

                <span class="badge bg-primary mb-3">
                    Tripod
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
                    <p>
                        <strong>Kondisi:</strong>
                        <?= $data['kondisi'] ?>
                    </p>
                    
                    <p>
                        <strong>Status:</strong>
                        <?= $data['status'] ?>
                    </p>
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