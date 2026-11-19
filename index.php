<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Peminjaman Kamera & Tripod</title>
    <style>
        body { background-color: #FFFBEB; font-family: 'Segoe UI', Tahoma, sans-serif; margin: 0; padding: 50px 20px; display: flex; justify-content: center; align-items: center; min-height: 80vh; }
        .container { max-width: 650px; background: #fff; padding: 40px; border-radius: 14px; box-shadow: 0 6px 20px rgba(0,0,0,0.06); border-top: 6px solid #8174A0; text-align: center; }
        h1 { color: #8174A0; margin-top: 0; font-size: 24px; margin-bottom: 10px; }
        p { color: #666; font-size: 14px; margin-bottom: 30px; }
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-bottom: 25px; }
        .menu-btn { display: block; background-color: #fcf9f2; border: 2px solid #A888B5; color: #8174A0; padding: 15px; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 14px; transition: all 0.2s; }
        .menu-btn:hover { background-color: #8174A0; color: white; border-color: #8174A0; transform: translateY(-2px); }
        .btn-primary { background-color: #8174A0; color: white; border-color: #8174A0; grid-column: span 2; }
        .btn-primary:hover { background-color: #6a5d88; }
        .footer { font-size: 12px; color: #888; margin-top: 25px; border-top: 1px solid #eee; padding-top: 15px; }
    </style>
</head>
<body>

<div class="container">
    <h1>Sistem Peminjaman Kamera & Tripod</h1>
    <p>Selamat datang di panel utama pengelolaan sistem penyewaan alat fotografi berbasis web.</p>
    
    <div class="menu-grid">
        <a href="LandingPage/" class="menu-btn">🏠 Landing Page</a>
        <a href="DataMaster/" class="menu-btn">⚙️ Data Master</a>
        <a href="Transaksi/Peminjaman/peminjaman.php" class="menu-btn btn-primary">🚀 Mulai Transaksi Peminjaman</a>
    </div>

    <div class="footer">
        Project PKL / Tugas Kejuruan RPL • SMK • 2026
    </div>
</div>

</body>
</html>