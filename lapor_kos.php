<?php
session_start();

if(!isset($_SESSION['role'])){
    header("Location: login_penyewa.php");
    exit;
}

if($_SESSION['role'] != 'penyewa'){
    header("Location: login_penyewa.php");
    exit;
}

require "koneksi.php";

$user_id = $_SESSION['id'];
$kos_id  = $_GET['id'];
$error   = "";
$success = "";

$kos = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM kos WHERE id='$kos_id'"));

if(!$kos){
    header("Location: index_penyewa.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $alasan = $_POST['alasan'];
    mysqli_query($conn, "INSERT INTO reports (reporter_id, kos_id, alasan, created_at) VALUES ('$user_id', '$kos_id', '$alasan', NOW())");
    $success = "Laporan berhasil dikirim. Tim kami akan meninjau dalam 1x24 jam.";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporkan Kos - CariKos.Ku</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="navbar">
        <h1>CariKos.Ku</h1>
        <div class="menu">
            <a href="index_penyewa.php">Beranda</a>
            <a href="favorit.php">Favorit</a>
            <a href="profil_penyewa.php">Profil</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="form-page">

        <div class="form-card">

            <h2 class="form-title" style="text-align:left; margin-bottom:8px;">Laporkan Kos</h2>
            <p style="color:#6B7280; margin-bottom:5px; font-size:14px;">Kos yang dilaporkan:</p>
            <p style="color:#157A6E; font-weight:600; margin-bottom:25px;"><?= $kos['nama_kos'] ?></p>

            <?php if($error): ?>
                <p style="color:red; margin-bottom:15px;"><?= $error ?></p>
            <?php endif; ?>

            <?php if($success): ?>
                <div style="background:#F0FDF4; border:1px solid #22C55E; border-radius:12px; padding:20px; margin-bottom:15px;">
                    <p style="color:#15803D; font-weight:500;"><?= $success ?></p>
                </div>
                <a href="index_penyewa.php"><button class="btn">Kembali ke Beranda</button></a>
            <?php else: ?>

                <form action="" method="POST">

                    <div class="input-group">
                        <label>Alasan Laporan</label>
                        <select name="alasan" required>
                            <option value="">-- Pilih Alasan --</option>
                            <option value="Informasi tidak sesuai kenyataan">Informasi tidak sesuai kenyataan</option>
                            <option value="Foto tidak asli / menyesatkan">Foto tidak asli / menyesatkan</option>
                            <option value="Pemilik tidak responsif">Pemilik tidak responsif</option>
                            <option value="Dugaan penipuan">Dugaan penipuan</option>
                            <option value="Harga tidak sesuai">Harga tidak sesuai</option>
                            <option value="Konten tidak pantas">Konten tidak pantas</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label>Keterangan Tambahan <span style="color:#9CA3AF; font-weight:400;">(opsional)</span></label>
                        <textarea name="keterangan" rows="4" placeholder="Jelaskan lebih detail masalah yang Anda temukan..." style="width:100%; padding:15px; border:1px solid #D1D5DB; border-radius:12px; outline:none; font-size:14px; resize:vertical;"></textarea>
                    </div>

                    <div style="display:flex; gap:15px;">
                        <button type="submit" class="btn-report" style="flex:1; padding:15px; border:none; border-radius:12px; cursor:pointer; font-size:15px; font-weight:600;">Kirim Laporan</button>
                        <a href="detail_kos.php?id=<?= $kos_id ?>" style="flex:1;">
                            <button type="button" class="btn" style="width:100%; background:#9CA3AF; padding:15px;">Batal</button>
                        </a>
                    </div>

                </form>

            <?php endif; ?>

        </div>

    </div>

</body>
</html>