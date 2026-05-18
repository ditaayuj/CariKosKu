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

$keyword  = isset($_GET['keyword'])  ? mysqli_real_escape_string($conn, $_GET['keyword'])  : '';
$kampus   = isset($_GET['kampus'])   ? mysqli_real_escape_string($conn, $_GET['kampus'])   : '';
$harga    = isset($_GET['harga'])    ? $_GET['harga']    : '';
$gender   = isset($_GET['gender'])   ? mysqli_real_escape_string($conn, $_GET['gender'])   : '';
$fasilitas = isset($_GET['fasilitas']) ? mysqli_real_escape_string($conn, $_GET['fasilitas']) : '';

$where = "WHERE k.terverifikasi = 1";

if($keyword)   $where .= " AND (k.nama_kos LIKE '%$keyword%' OR k.alamat LIKE '%$keyword%')";
if($kampus)    $where .= " AND k.kampus_terdekat = '$kampus'";
if($gender)    $where .= " AND k.gender = '$gender'";
if($fasilitas) $where .= " AND k.fasilitas LIKE '%$fasilitas%'";

if($harga == '1') $where .= " AND k.harga < 500000";
elseif($harga == '2') $where .= " AND k.harga BETWEEN 500000 AND 1000000";
elseif($harga == '3') $where .= " AND k.harga > 1000000";

$query_kos = mysqli_query($conn, "SELECT k.*, u.nama AS nama_pemilik FROM kos k JOIN users u ON k.user_id = u.id $where ORDER BY k.created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CariKos.Ku - Cari Kos</title>
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

<div class="hero">
    <h2>Temukan Kos Terbaik Untukmu</h2>
    <p>Cari kos nyaman, aman, dan dekat kampus dengan mudah.</p>

    <form action="" method="GET" class="search-box">
        <div class="search-row">
            <input type="text" name="keyword" placeholder="Cari lokasi atau nama kos..." value="<?= htmlspecialchars($keyword) ?>">
            <select name="kampus">
                <option value="">Semua Kampus</option>
                <option value="Universitas Mataram (UNRAM)"  <?= $kampus == 'Universitas Mataram (UNRAM)'  ? 'selected' : '' ?>>UNRAM</option>
                <option value="UIN Mataram"                  <?= $kampus == 'UIN Mataram'                  ? 'selected' : '' ?>>UIN Mataram</option>
                <option value="UNDIKMA"                      <?= $kampus == 'UNDIKMA'                      ? 'selected' : '' ?>>UNDIKMA</option>
                <option value="Universitas Nahdlatul Wathan" <?= $kampus == 'Universitas Nahdlatul Wathan' ? 'selected' : '' ?>>Univ. NW</option>
            </select>
            <select name="harga">
                <option value="">Semua Harga</option>
                <option value="1" <?= $harga == '1' ? 'selected' : '' ?>>&lt; 500rb</option>
                <option value="2" <?= $harga == '2' ? 'selected' : '' ?>>500rb - 1jt</option>
                <option value="3" <?= $harga == '3' ? 'selected' : '' ?>>&gt; 1jt</option>
            </select>
            <select name="gender">
                <option value="">Semua Gender</option>
                <option value="putra"  <?= $gender == 'putra'  ? 'selected' : '' ?>>Putra</option>
                <option value="putri"  <?= $gender == 'putri'  ? 'selected' : '' ?>>Putri</option>
                <option value="campur" <?= $gender == 'campur' ? 'selected' : '' ?>>Campur</option>
            </select>
            <select name="fasilitas">
                <option value="">Semua Fasilitas</option>
                <option value="WiFi"              <?= $fasilitas == 'WiFi'              ? 'selected' : '' ?>>WiFi</option>
                <option value="AC"                <?= $fasilitas == 'AC'                ? 'selected' : '' ?>>AC</option>
                <option value="Kamar Mandi Dalam" <?= $fasilitas == 'Kamar Mandi Dalam' ? 'selected' : '' ?>>KM Dalam</option>
            </select>
        </div>
        <div style="display:flex; gap:12px;">
            <button type="submit" class="search-btn" style="flex:1;">Cari Sekarang</button>
            <a href="peta.php<?= $kampus ? '?kampus='.urlencode($kampus) : '' ?>" style="flex:none;">
                <button type="button" class="search-btn" style="background:#157A6E; padding:15px 24px;">🗺️ Lihat Peta</button>
            </a>
        </div>
    </form>
</div>

<h2 class="section-title">Rekomendasi Kos</h2>

<div class="listing-container">
<?php if(mysqli_num_rows($query_kos) == 0): ?>
    <div class="empty-state" style="grid-column:1/-1;">
        <div class="empty-icon">🔍</div>
        <h3>Tidak Ada Kos Ditemukan</h3>
        <p>Coba ubah filter pencarian kamu.</p>
        <a href="index_penyewa.php"><button class="btn-tambah">Reset Pencarian</button></a>
    </div>
<?php else: ?>
    <?php while($kos = mysqli_fetch_assoc($query_kos)): ?>
    <div class="card">
        <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?q=80&w=1200&auto=format&fit=crop">
        <div class="card-content">
            <div class="badge-group">
                <div class="badge verified">Terverifikasi</div>
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
            <?php if($kos['jam_malam']): ?>
                <div class="info">🕒 Jam malam <?= htmlspecialchars($kos['jam_malam']) ?></div>
            <?php else: ?>
                <div class="info">🕒 Bebas jam malam</div>
            <?php endif; ?>
            <div class="info">
                <?= $kos['gender'] == 'putra' ? '👨' : ($kos['gender'] == 'putri' ? '👩' : '👥') ?>
                Kos <?= ucfirst($kos['gender']) ?>
            </div>
            <div class="rating">⭐ <?= $kos['rating'] > 0 ? $kos['rating'] : 'Belum ada rating' ?></div>
            <div class="button-group">
                <a href="detail_kos.php?id=<?= $kos['id'] ?>" style="flex:1;">
                    <button class="btn-detail" style="width:100%;">Lihat Detail</button>
                </a>
                <a href="favorit.php?aksi=tambah&kos_id=<?= $kos['id'] ?>&redirect=index_penyewa.php" style="flex:none;">
                    <button class="btn-fav">❤️</button>
                </a>
                <a href="lapor_kos.php?id=<?= $kos['id'] ?>" style="flex:1;">
                    <button class="btn-report" style="width:100%;">Laporkan</button>
                </a>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
<?php endif; ?>
</div>

</body>
</html>