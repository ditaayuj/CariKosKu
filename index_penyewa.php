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

$user_id   = $_SESSION['id'];
$keyword   = isset($_GET['keyword'])   ? mysqli_real_escape_string($conn, $_GET['keyword'])   : '';
$lokasi    = isset($_GET['lokasi'])    ? mysqli_real_escape_string($conn, $_GET['lokasi'])    : '';
$harga_min = isset($_GET['harga_min']) && $_GET['harga_min'] !== '' ? (int)$_GET['harga_min'] : '';
$harga_max = isset($_GET['harga_max']) && $_GET['harga_max'] !== '' ? (int)$_GET['harga_max'] : '';
$gender    = isset($_GET['gender'])    ? mysqli_real_escape_string($conn, $_GET['gender'])    : '';
$fasilitas = isset($_GET['fasilitas']) ? mysqli_real_escape_string($conn, $_GET['fasilitas']) : '';

$where = "WHERE k.terverifikasi = 1";
if($keyword)   $where .= " AND (k.nama_kos LIKE '%$keyword%' OR k.alamat LIKE '%$keyword%')";
if($lokasi)    $where .= " AND k.kampus_terdekat LIKE '%$lokasi%'";
if($gender)    $where .= " AND k.gender = '$gender'";
if($fasilitas) $where .= " AND k.fasilitas LIKE '%$fasilitas%'";
if($harga_min !== '') $where .= " AND k.harga >= $harga_min";
if($harga_max !== '') $where .= " AND k.harga <= $harga_max";

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

    <form action="" method="GET" class="search-box" id="formCari">
        <div class="search-row-main">
            <input type="text" name="keyword" placeholder="Cari nama kos atau alamat..." value="<?= htmlspecialchars($keyword) ?>">
        </div>
        <div class="search-row-filters">
            <div class="filter-group">
                <label class="filter-label">📍 Dekat Lokasi</label>
                <input type="text" name="lokasi" placeholder="Contoh: Unram, UIN, Epicentrum..." value="<?= htmlspecialchars($lokasi) ?>">
            </div>
            <div class="filter-group">
                <label class="filter-label">💰 Harga Min (Rp)</label>
                <input type="number" name="harga_min" placeholder="Contoh: 500000" value="<?= $harga_min ?>">
            </div>
            <div class="filter-group">
                <label class="filter-label">💰 Harga Maks (Rp)</label>
                <input type="number" name="harga_max" placeholder="Contoh: 1000000" value="<?= $harga_max ?>">
            </div>
            <div class="filter-group">
                <label class="filter-label">🚻 Gender Kos</label>
                <select name="gender">
                    <option value="">Semua Gender</option>
                    <option value="putra"  <?= $gender == 'putra'  ? 'selected' : '' ?>>Putra</option>
                    <option value="putri"  <?= $gender == 'putri'  ? 'selected' : '' ?>>Putri</option>
                    <option value="campur" <?= $gender == 'campur' ? 'selected' : '' ?>>Campur</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">🛏 Fasilitas</label>
                <input type="text" name="fasilitas" placeholder="Contoh: WiFi, AC, KM Dalam..." value="<?= htmlspecialchars($fasilitas) ?>">
            </div>
        </div>
        <div class="search-actions">
            <button type="submit" class="search-btn search-btn-main">🔍 Cari Sekarang</button>
            <a href="index_penyewa.php">
                <button type="button" class="search-btn search-btn-reset">Reset</button>
            </a>
            <a href="peta.php<?= $lokasi ? '?kampus='.urlencode($lokasi) : '' ?>">
                <button type="button" class="search-btn search-btn-peta">🗺️ Peta</button>
            </a>
        </div>
    </form>
</div>

<h2 class="section-title">
    <?php if($keyword || $lokasi || $harga_min !== '' || $harga_max !== '' || $gender || $fasilitas): ?>
        Hasil Pencarian (<?= mysqli_num_rows($query_kos) ?> kos ditemukan)
    <?php else: ?>
        Rekomendasi Kos
    <?php endif; ?>
</h2>

<div class="listing-container">
<?php if(mysqli_num_rows($query_kos) == 0): ?>
    <div class="empty-state">
        <div class="empty-icon">🔍</div>
        <h3>Tidak Ada Kos Ditemukan</h3>
        <p>Coba ubah kata kunci atau filter pencarian kamu.</p>
        <a href="index_penyewa.php"><button class="btn-tambah">Reset Pencarian</button></a>
    </div>
<?php else: ?>
    <?php while($kos = mysqli_fetch_assoc($query_kos)):
        $foto_utama = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_file FROM kos_foto WHERE kos_id='{$kos['id']}' AND is_primary=1 LIMIT 1"));
        $img_src = $foto_utama ? 'uploads/' . $foto_utama['nama_file'] : 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?q=80&w=1200&auto=format&fit=crop';
    ?>
    <div class="card">
        <img src="<?= $img_src ?>" alt="<?= htmlspecialchars($kos['nama_kos']) ?>">
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
                <a href="detail_kos.php?id=<?= $kos['id'] ?>">
                    <button class="btn-detail">Lihat Detail</button>
                </a>
                <a href="favorit.php?aksi=tambah&kos_id=<?= $kos['id'] ?>&redirect=index_penyewa.php">
                    <button class="btn-fav">❤️</button>
                </a>
                <a href="lapor_kos.php?id=<?= $kos['id'] ?>">
                    <button class="btn-report">Laporkan</button>
                </a>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
<?php endif; ?>
</div>

</body>
</html>