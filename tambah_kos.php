<?php
session_start();

if(!isset($_SESSION['role'])){
    header("Location: login_pemilik.php");
    exit;
}

if($_SESSION['role'] != 'pemilik'){
    header("Location: login_pemilik.php");
    exit;
}

require "koneksi.php";

$user_id = $_SESSION['id'];
$error   = "";
$success = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $nama_kos        = $_POST['nama_kos'];
    $alamat          = $_POST['alamat'];
    $kampus_terdekat = $_POST['kampus_terdekat'];
    $harga           = $_POST['harga'];
    $gender          = $_POST['gender'];
    $fasilitas       = $_POST['fasilitas'];
    $jam_malam       = $_POST['jam_malam'];
    $status          = $_POST['status'];

    $lat = !empty($_POST['lat']) ? (float)$_POST['lat'] : null;
    $lng = !empty($_POST['lng']) ? (float)$_POST['lng'] : null;

    $lat_val = $lat !== null ? "'$lat'" : "NULL";
    $lng_val = $lng !== null ? "'$lng'" : "NULL";

    $query = "INSERT INTO kos (user_id, nama_kos, alamat, kampus_terdekat, harga, gender, fasilitas, jam_malam, status, terverifikasi, rating, lat, lng) VALUES ('$user_id', '$nama_kos', '$alamat', '$kampus_terdekat', '$harga', '$gender', '$fasilitas', '$jam_malam', '$status', 0, 0, $lat_val, $lng_val)";

    if(mysqli_query($conn, $query)){
        $success = "Kos berhasil ditambahkan! Menunggu verifikasi admin.";
    } else {
        $error = "Gagal menambahkan kos. Coba lagi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kos - CariKos.Ku</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="navbar">
        <h1>CariKos.Ku</h1>
        <div class="menu">
            <a href="index_pemilik.php">Dashboard</a>
            <a href="tambah_kos.php">Tambah Kos</a>
            <a href="profil_pemilik.php">Profil</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="form-page">

        <div class="form-card">

            <h2 class="form-title" style="text-align:left; margin-bottom:8px;">Tambah Kos Baru</h2>
            <p style="color:#6B7280; margin-bottom:30px; font-size:14px;">Isi informasi kos Anda. Listing akan ditinjau admin sebelum aktif.</p>

            <?php if($error): ?>
                <p style="color:red; margin-bottom:15px;"><?= $error ?></p>
            <?php endif; ?>

            <?php if($success): ?>
                <p style="color:green; margin-bottom:15px;"><?= $success ?></p>
            <?php endif; ?>

            <form action="" method="POST">

                <div class="form-section-title">Informasi Dasar</div>

                <div class="input-group">
                    <label>Nama Kos</label>
                    <input type="text" name="nama_kos" placeholder="Contoh: Kos Putri Melati" required>
                </div>

                <div class="input-group">
                    <label>Alamat Lengkap</label>
                    <input type="text" name="alamat" placeholder="Jl. Contoh No. 1, Kelurahan, Kota" required>
                </div>

                <div class="input-group">
                    <label>Kampus Terdekat</label>
                    <select name="kampus_terdekat" required>
                        <option value="">-- Pilih Kampus --</option>
                        <option value="Universitas Mataram (UNRAM)">Universitas Mataram (UNRAM)</option>
                        <option value="UIN Mataram">UIN Mataram</option>
                        <option value="UNDIKMA">UNDIKMA</option>
                        <option value="Universitas Nahdlatul Wathan">Universitas Nahdlatul Wathan</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="form-section-title">Harga & Tipe</div>

                <div class="input-group">
                    <label>Harga per Bulan (Rp)</label>
                    <input type="number" name="harga" placeholder="Contoh: 800000" required>
                </div>

                <div class="input-group">
                    <label>Gender Kos</label>
                    <select name="gender" required>
                        <option value="">-- Pilih Gender --</option>
                        <option value="putra">Putra</option>
                        <option value="putri">Putri</option>
                        <option value="campur">Campur</option>
                    </select>
                </div>

                <div class="form-section-title">Fasilitas & Aturan</div>

                <div class="input-group">
                    <label>Fasilitas</label>
                    <input type="text" name="fasilitas" placeholder="Contoh: WiFi, AC, Kamar Mandi Dalam, Parkir" required>
                </div>

                <div class="input-group">
                    <label>Jam Malam <span style="color:#9CA3AF; font-weight:400;">(kosongkan jika bebas)</span></label>
                    <input type="text" name="jam_malam" placeholder="Contoh: 22.00">
                </div>

                <div class="input-group">
                    <label>Status Ketersediaan</label>
                    <select name="status" required>
                        <option value="tersedia">Tersedia</option>
                        <option value="hampir_penuh">Hampir Penuh</option>
                        <option value="penuh">Penuh</option>
                    </select>
                </div>

                <div class="form-section-title">Lokasi di Peta (Opsional)</div>

                <div class="input-group">
                    <label>Latitude <span style="color:#9CA3AF; font-weight:400;">(contoh: -8.5833)</span></label>
                    <input type="text" name="lat" placeholder="-8.5833">
                </div>

                <div class="input-group">
                    <label>Longitude <span style="color:#9CA3AF; font-weight:400;">(contoh: 116.1167)</span></label>
                    <input type="text" name="lng" placeholder="116.1167">
                </div>

                <div style="display:flex; gap:15px; margin-top:10px;">
                    <button type="submit" class="btn" style="flex:1;">Tambah Kos</button>
                    <a href="index_pemilik.php" style="flex:1;">
                        <button type="button" class="btn" style="background:#9CA3AF; width:100%;">Batal</button>
                    </a>
                </div>

            </form>

        </div>

    </div>

</body>
</html>