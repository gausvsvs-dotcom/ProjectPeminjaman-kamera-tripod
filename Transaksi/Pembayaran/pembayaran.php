<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Form Peminjaman & Pembayaran Barang</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background-color: #f4f5f7;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
    }

    .form-card {
      background-color: #ffffff;
      width: 100%;
      max-width: 480px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
      overflow: hidden;
    }

    .form-header {
      background-color: #8174A0;
      color: #ffffff;
      padding: 18px 24px;
    }

    .form-header h2 {
      font-size: 1.25rem;
      font-weight: 600;
    }

    .form-body {
      padding: 24px;
      display: flex;
      flex-direction: column;
      gap: 18px;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .form-group label {
      font-size: 0.9rem;
      font-weight: 700;
      color: #2d3748;
    }

    .form-control {
      width: 100%;
      padding: 12px 14px;
      background-color: #F2EEF7;
      border: 1px solid #E2D9F3;
      border-radius: 10px;
      font-size: 0.95rem;
      color: #4a5568;
      outline: none;
      transition: all 0.2s ease;
    }

    .form-control:focus {
      background-color: #ffffff;
      border-color: #A888B5;
      box-shadow: 0 0 0 3px rgba(168, 136, 181, 0.2);
    }

    .form-control::placeholder {
      color: #a0aec0;
    }

    textarea.form-control {
      resize: vertical;
      min-height: 80px;
    }

    .section-divider {
      border: none;
      border-top: 1px dashed #E2D9F3;
      margin: 5px 0;
    }

    .section-title {
      font-size: 0.95rem;
      font-weight: 700;
      color: #8174A0;
      margin-bottom: -5px;
    }

    .btn-submit {
      margin-top: 10px;
      padding: 12px;
      background-color: #8174A0;
      color: #ffffff;
      border: none;
      border-radius: 10px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.2s ease;
    }

    .btn-submit:hover {
      background-color: #6e618d;
    }
  </style>
</head>
<body>

  <div class="form-card">
    <!-- Header Form -->
    <div class="form-header">
      <h2>Form Peminjaman & Pembayaran</h2>
    </div>

    <!-- Body Form -->
    <form class="form-body" action="#" method="POST">
      
      <!-- Nama Peminjam -->
      <div class="form-group">
        <label for="nama_peminjam">Nama Peminjam</label>
        <input 
          type="text" 
          id="nama_peminjam" 
          name="nama_peminjam" 
          class="form-control" 
          placeholder="Ketik nama peminjam..." 
          required 
        />
      </div>

      <!-- No HP -->
      <div class="form-group">
        <label for="no_hp">No HP</label>
        <input 
          type="tel" 
          id="no_hp" 
          name="no_hp" 
          class="form-control" 
          placeholder="Masukkan nomor HP..." 
          required 
        />
      </div>

      <!-- Tanggal Peminjaman -->
      <div class="form-group">
        <label for="tgl_peminjaman">Tanggal Peminjaman</label>
        <input 
          type="date" 
          id="tgl_peminjaman" 
          name="tgl_peminjaman" 
          class="form-control" 
          required 
        />
      </div>

      <!-- Tanggal Jatuh Tempo -->
      <div class="form-group">
        <label for="tgl_kembali">Tanggal Jatuh Tempo (Rencana Kembali)</label>
        <input 
          type="date" 
          id="tgl_kembali" 
          name="tgl_kembali" 
          class="form-control" 
          required 
        />
      </div>

      <!-- Alamat -->
      <div class="form-group">
        <label for="alamat">Alamat</label>
        <textarea 
          id="alamat" 
          name="alamat" 
          class="form-control" 
          placeholder="Masukkan alamat peminjam..." 
          rows="2"
          required
        ></textarea>
      </div>

      <hr class="section-divider">
      <p class="section-title">Informasi Pembayaran / Jaminan</p>

      <!-- Metode Pembayaran / Jaminan -->
      <div class="form-group">
        <label for="metode_pembayaran">Metode Pembayaran / Jaminan</label>
        <select id="metode_pembayaran" name="metode_pembayaran" class="form-control" required>
          <option value="" disabled selected>Pilih metode...</option>
          <option value="Tunai">Tunai / Cash</option>
          <option value="Transfer">Transfer Bank</option>
          <option value="QRIS">QRIS</option>
          <option value="Jaminan Kartu Identitas">Jaminan KTP / Kartu Pelajar</option>
        </select>
      </div>

      <!-- Jumlah Biaya / Disposisi -->
      <div class="form-group">
        <label for="biaya">Total Biaya / Bayar (Rp)</label>
        <input 
          type="number" 
          id="biaya" 
          name="biaya" 
          class="form-control" 
          placeholder="Masukkan nominal angka (cth: 50000)..." 
        />
      </div>

      <!-- Tombol Simpan -->
      <button type="submit" class="btn-submit">Simpan Transaksi</button>

    </form>
  </div>

</body>
</html>