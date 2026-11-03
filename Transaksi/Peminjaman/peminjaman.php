
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Peminjaman Barang</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- CSS Form -->
    <link rel="stylesheet" href="style_peminjaman.css">
</head>

<body>

    <div class="container-form">

        <!-- Header -->
        <div class="form-header">
            <div class="header-icon">
                <i class="bi bi-camera-fill"></i>
            </div>

            <div>
                <h2>Form Peminjaman Barang</h2>
                <p>Silakan lengkapi data untuk mengajukan peminjaman.</p>
            </div>
        </div>

        <!-- Form -->
        <form action="proses_pinjam.php" method="POST" class="form-body">

            <!-- Nama Peminjam -->
            <div class="form-group">
                <label for="nama_peminjam">Nama Lengkap</label>
                <input
                    type="text"
                    name="nama_peminjam"
                    id="nama_peminjam"
                    placeholder="Masukkan nama lengkap"
                    required
                >
            </div>

            <!-- Nomor HP -->
            <div class="form-group">
                <label for="no_hp">Nomor HP / WhatsApp</label>
                <input
                    type="tel"
                    name="no_hp"
                    id="no_hp"
                    placeholder="Contoh: 08xxxxxxxxxx"
                    required
                >
            </div>

            <!-- Tanggal Peminjaman -->
            <div class="form-group">
                <label for="tgl_peminjaman">Tanggal Peminjaman</label>
                <input
                    type="date"
                name="tgl_peminjaman"
                id="tgl_peminjaman"
                required
                >
            </div>

            <!-- Tanggal Pengembalian -->
            <div class="form-group">
                <label for="tgl_jatuh_tempo">Tanggal Rencana Pengembalian</label>
                <input
                    type="date"
                    name="tgl_jatuh_tempo"
                    id="tgl_jatuh_tempo"
                    required
                >
            </div>

            <!-- Alamat -->
            <div class="form-group">
                <label for="alamat">Alamat Lengkap</label>
                <textarea
                    name="alamat"
                    id="alamat"
                    rows="3"
                    placeholder="Masukkan alamat lengkap"
                    required
                ></textarea>
            </div>

            <!-- Tombol -->
            <button type="submit" name="simpan" class="btn-simpan">
                <i class="bi bi-send-fill"></i>
                Ajukan Peminjaman
            </button>

        </form>

    </div>

</body>
</html>
