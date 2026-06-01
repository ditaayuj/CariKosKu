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
$kos_id  = (int)$_GET['id'];
$error   = "";
$success = "";

$kos = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM kos WHERE id='$kos_id'"));

if(!$kos){
    header("Location: index_penyewa.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $alasan     = mysqli_real_escape_string($conn, $_POST['alasan']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan'] ?? '');

    mysqli_query($conn, "
        INSERT INTO reports
        (reporter_id, kos_id, alasan, keterangan, created_at)
        VALUES
        ('$user_id', '$kos_id', '$alasan', '$keterangan', NOW())
    ");

    $report_id = mysqli_insert_id($conn);

    $pesan = mysqli_real_escape_string(
        $conn,
        "Ada laporan baru pada kos \"{$kos['nama_kos']}\" yang perlu ditinjau."
    );

    mysqli_query($conn, "
        INSERT INTO notifikasi
        (user_id, kos_id, report_id, pesan, tipe)
        VALUES
        (
            '{$kos['user_id']}',
            '$kos_id',
            '$report_id',
            '$pesan',
            'info'
        )
    ");

    $success = "Laporan berhasil dikirim. Tim kami akan meninjau dalam 1x24 jam.";
}
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
        <a href="peta.php">Peta Kos</a>
        <a href="favorit.php">Favorit</a>
        <a href="laporan_saya.php">Laporan Saya</a>
        <a href="profil_penyewa.php">Profil</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="form-page">
    <div class="form-card">

        <h2 class="form-card-title">Laporkan Kos</h2>
        <p class="lapor-subtitle">Kos yang dilaporkan:</p>
        <p class="lapor-kos-nama"><?= htmlspecialchars($kos['nama_kos']) ?></p>

        <?php if($success): ?>
            <div class="alert-success"><?= $success ?></div>
            <a href="index_penyewa.php"><button class="btn">Kembali ke Beranda</button></a>
        <?php else: ?>

            <form action="" method="POST" id="formLapor">

                <div class="input-group">
                    <label>Alasan Laporan</label>
                    <select name="alasan" id="alasan" required>
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
                    <label>Keterangan Tambahan <span class="input-hint">(opsional)</span></label>
                    <textarea name="keterangan" class="ulasan-textarea" rows="4" placeholder="Jelaskan lebih detail masalah yang Anda temukan..."></textarea>
                </div>

                <div class="lapor-actions">
                    <button type="submit" class="btn-report-submit">Kirim Laporan</button>
                    <a href="detail_kos.php?id=<?= $kos_id ?>">
                        <button type="button" class="btn btn-gray">Batal</button>
                    </a>
                </div>

            </form>

        <?php endif; ?>

    </div>
</div>

<script>
document.getElementById('formLapor') && document.getElementById('formLapor').addEventListener('submit', function(e){
    var alasan = document.getElementById('alasan').value;
    if(alasan === ''){ e.preventDefault(); alert('Pilih alasan laporan terlebih dahulu.'); return; }
});
</script>

</body>
</html>