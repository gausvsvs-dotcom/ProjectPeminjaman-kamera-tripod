<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Peminjaman Barang</title>
    <!-- Google Fonts Inter & FontAwesome Icon -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background-color: #f5f3f8 !important; margin: 0; padding: 30px 20px; font-family: 'Inter', sans-serif !important; display: flex; justify-content: center; align-items: center; min-height: 100vh;">

    <!-- Card Container Form -->
    <div class="card-form" style="width: 100% !important; max-width: 500px !important; background-color: #ffffff !important; border-radius: 16px !important; border: 1px solid #e6e1ed !important; overflow: hidden !important; box-shadow: 0 10px 25px rgba(129, 116, 160, 0.08) !important;">
        
        <!-- Header Form -->
        <div class="card-header" style="background-color: #8174a0 !important; padding: 20px 24px !important; color: #ffffff !important;">
            <h2 style="font-size: 1.25rem !important; font-weight: 600 !important; color: #ffffff !important; margin: 0 !important; letter-spacing: -0.02em !important;">Form Peminjaman Barang</h2>
        </div>

        <!-- Body Form -->
        <form class="card-body" action="proses_pinjam.php" method="POST" style="padding: 24px !important; display: flex !important; flex-direction: column !important; gap: 18px !important;">
            
            <!-- Nama Peminjam -->
            <div class="form-group" style="display: flex !important; flex-direction: column !important; gap: 8px !important;">
                <label for="nama_peminjam" style="font-size: 0.875rem !important; font-weight: 600 !important; color: #2d2338 !important;">Nama Peminjam</label>
                <input type="text" name="nama_peminjam" id="nama_peminjam" value="Rian adi" placeholder="Ketik nama peminjam..." style="width: 100% !important; padding: 12px 16px !important; background-color: #f0ebf5 !important; border: 1px solid #d8d0e3 !important; border-radius: 10px !important; font-size: 0.925rem !important; color: #2d2338 !important; outline: none !important; box-sizing: border-box !important;">
            </div>

            <!-- No HP -->
            <div class="form-group" style="display: flex !important; flex-direction: column !important; gap: 8px !important;">
                <label for="no_hp" style="font-size: 0.875rem !important; font-weight: 600 !important; color: #2d2338 !important;">No HP</label>
                <input type="text" name="no_hp" id="no_hp" placeholder="Masukkan nomor HP..." style="width: 100% !important; padding: 12px 16px !important; background-color: #f0ebf5 !important; border: 1px solid #d8d0e3 !important; border-radius: 10px !important; font-size: 0.925rem !important; color: #2d2338 !important; outline: none !important; box-sizing: border-box !important;">
            </div>

            <!-- Tanggal Peminjaman -->
            <div class="form-group" style="display: flex !important; flex-direction: column !important; gap: 8px !important;">
                <label for="tgl_peminjaman" style="font-size: 0.875rem !important; font-weight: 600 !important; color: #2d2338 !important;">Tanggal Peminjaman</label>
                <input type="date" name="tgl_peminjaman" id="tgl_peminjaman" style="width: 100% !important; padding: 12px 16px !important; background-color: #f0ebf5 !important; border: 1px solid #d8d0e3 !important; border-radius: 10px !important; font-size: 0.925rem !important; color: #2d2338 !important; outline: none !important; box-sizing: border-box !important;">
            </div>

            <!-- Tanggal Jatuh Tempo -->
            <div class="form-group" style="display: flex !important; flex-direction: column !important; gap: 8px !important;">
                <label for="tgl_jatuh_tempo" style="font-size: 0.875rem !important; font-weight: 600 !important; color: #2d2338 !important;">Tanggal Jatuh Tempo (Rencana Kembali)</label>
                <input type="date" name="tgl_jatuh_tempo" id="tgl_jatuh_tempo" style="width: 100% !important; padding: 12px 16px !important; background-color: #f0ebf5 !important; border: 1px solid #d8d0e3 !important; border-radius: 10px !important; font-size: 0.925rem !important; color: #2d2338 !important; outline: none !important; box-sizing: border-box !important;">
            </div>

            <!-- Jenis Peminjaman -->
            <div class="form-group" style="display: flex !important; flex-direction: column !important; gap: 8px !important;">
                <label for="jenis_peminjaman" style="font-size: 0.875rem !important; font-weight: 600 !important; color: #2d2338 !important;">Jenis Peminjaman</label>
                <select name="jenis_peminjaman" id="jenis_peminjaman" style="width: 100% !important; padding: 12px 16px !important; background-color: #f0ebf5 !important; border: 1px solid #d8d0e3 !important; border-radius: 10px !important; font-size: 0.925rem !important; color: #2d2338 !important; outline: none !important; box-sizing: border-box !important;">
                    <option value="offline">Offline</option>
                    <option value="online">Online</option>
                </select>
            </div>

            <!-- Alamat -->
            <div class="form-group" style="display: flex !important; flex-direction: column !important; gap: 8px !important;">
                <label for="Alamat" style="font-size: 0.875rem !important; font-weight: 600 !important; color: #2d2338 !important;">Alamat</label>
                <textarea name="Alamat" id="Alamat" placeholder="Masukkan alamat peminjam..." rows="2" style="width: 100% !important; padding: 12px 16px !important; background-color: #f0ebf5 !important; border: 1px solid #d8d0e3 !important; border-radius: 10px !important; font-size: 0.925rem !important; color: #2d2338 !important; outline: none !important; box-sizing: border-box !important; resize: none;"></textarea>
            </div>

            <!-- Tombol Simpan -->
            <button type="submit" name="simpan" style="margin-top: 8px !important; padding: 12px !important; background-color: #8174a0 !important; color: #ffffff !important; border: none !important; border-radius: 10px !important; font-size: 0.95rem !important; font-weight: 600 !important; cursor: pointer !important; width: 100% !important;">
                Simpan Peminjaman
            </button>

        </form>
    </div>

</body>
</html>