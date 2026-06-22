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

$user_id  = $_SESSION['id'];
$aksi     = isset($_GET['aksi'])     ? $_GET['aksi']           : '';
$kos_id   = isset($_GET['kos_id'])   ? (int)$_GET['kos_id']   : 0;
$redirect = isset($_GET['redirect']) ? $_GET['redirect']       : 'favorit.php';

if($aksi == 'tambah' && $kos_id){
    mysqli_query($conn, "INSERT IGNORE INTO favorites (user_id, kos_id) VALUES ('$user_id', '$kos_id')");
    header("Location: $redirect");
    exit;
}

if($aksi == 'hapus' && $kos_id){
    mysqli_query($conn, "DELETE FROM favorites WHERE user_id='$user_id' AND kos_id='$kos_id'");
    header("Location: $redirect");
    exit;
}

$query_fav = mysqli_query($conn, "SELECT k.*, u.nama AS nama_pemilik FROM favorites f JOIN kos k ON f.kos_id = k.id JOIN users u ON k.user_id = u.id WHERE f.user_id = '$user_id' ORDER BY f.created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorit - CariKos.Ku</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="navbar">
    <h1>CariKos.Ku</h1>
    <div class="menu">
        <a href="index_penyewa.php">Beranda</a>
        <a href="peta.php">Peta Kos</a>
        <a href="favorit.php">Favorit</a>
        <a href="profil_penyewa.php">Profil</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="favorit-header">
    <h2>Kos Favorit Saya</h2>
    <p>Kos yang sudah kamu simpan untuk dibandingkan.</p>
</div>

<div class="listing-container">
<?php if(mysqli_num_rows($query_fav) == 0): ?>
    <div class="empty-state">
        <h3>Belum Ada Favorit</h3>
        <p>Simpan kos yang kamu suka dari halaman pencarian.</p>
        <a href="index_penyewa.php"><button class="btn-tambah">Cari Kos</button></a>
    </div>
<?php else: ?>
    <?php while($kos = mysqli_fetch_assoc($query_fav)):
        $foto_utama = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_file FROM kos_foto WHERE kos_id='{$kos['id']}' AND is_primary=1 LIMIT 1"));
        $img_src = $foto_utama ? 'uploads/' . $foto_utama['nama_file'] : 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?q=80&w=1200&auto=format&fit=crop';
    ?>
    <div class="card">
        <img src="<?= $img_src ?>" alt="<?= htmlspecialchars($kos['nama_kos']) ?>">
        <div class="card-content">
            <div class="badge-group">
                <?php if($kos['terverifikasi']): ?>
                    <div class="badge verified">Terverifikasi</div>
                <?php endif; ?>
                <?php if($kos['status'] == 'tersedia'): ?>
                    <div class="badge verified">Tersedia</div>
                <?php elseif($kos['status'] == 'hampir_penuh'): ?>
                    <div class="badge cheap">Hampir Penuh</div>
                <?php else: ?>
                    <div class="badge full">Penuh</div>
                <?php endif; ?>
            </div>
            <h3><?= htmlspecialchars($kos['nama_kos']) ?></h3>
            <div class="price">Rp<?= number_format($kos['harga'], 0, ',', '.') ?> / bulan</div>
            <div class="info">📍 <?= htmlspecialchars($kos['alamat']) ?></div>
            <div class="info">🏫 Dekat <?= htmlspecialchars($kos['kampus_terdekat']) ?></div>
            <div class="info">🛏 <?= htmlspecialchars($kos['fasilitas']) ?></div>
            <div class="info"><?= $kos['gender'] == 'putra' ? '👨' : ($kos['gender'] == 'putri' ? '👩' : '👥') ?> Kos <?= ucfirst($kos['gender']) ?></div>
            <div class="rating">⭐ <?= $kos['rating'] > 0 ? $kos['rating'] : 'Belum ada rating' ?></div>
            <div class="button-group">
                <a href="detail_kos.php?id=<?= $kos['id'] ?>">
                    <button class="btn-detail">Lihat Detail</button>
                </a>
                <a href="favorit.php?aksi=hapus&kos_id=<?= $kos['id'] ?>&redirect=favorit.php">
                    <button class="btn-report">💔 Hapus</button>
                </a>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
<?php endif; ?>
</div>

</body>
</html>