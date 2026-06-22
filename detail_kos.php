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

$id  = (int)$_GET['id'];
$kos = mysqli_fetch_assoc(mysqli_query($conn, "SELECT k.*, u.nama AS nama_pemilik, u.no_hp AS hp_pemilik FROM kos k JOIN users u ON k.user_id = u.id WHERE k.id='$id' AND k.terverifikasi=1"));

if(!$kos){
    header("Location: index_penyewa.php");
    exit;
}

$user_id  = $_SESSION['id'];
$is_fav   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM favorites WHERE user_id='$user_id' AND kos_id='$id'"))[0];
$foto_list = mysqli_query($conn, "SELECT * FROM kos_foto WHERE kos_id='$id' ORDER BY is_primary DESC");
$ulasan_list = mysqli_query($conn, "SELECT r.*, u.nama AS nama_reviewer FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.kos_id='$id' ORDER BY r.created_at DESC");

$error   = "";
$success = "";

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['kirim_ulasan'])){
    $rating   = (int)$_POST['rating'];
    $komentar = mysqli_real_escape_string($conn, $_POST['komentar']);
    $cek      = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM reviews WHERE kos_id='$id' AND user_id='$user_id'"));

    if($cek[0] > 0){
        $error = "Anda sudah pernah memberikan ulasan untuk kos ini.";
    } elseif(strlen($komentar) < 20){
        $error = "Komentar minimal 20 karakter.";
    } else {
        $foto_ulasan = null;
        if(!empty($_FILES['foto_ulasan']['name']) && $_FILES['foto_ulasan']['error'] == 0){
            $allowed = ['image/jpeg', 'image/png'];
            if(in_array($_FILES['foto_ulasan']['type'], $allowed)){
                $ext      = pathinfo($_FILES['foto_ulasan']['name'], PATHINFO_EXTENSION);
                $filename = 'ulasan_' . $user_id . '_' . time() . '.' . $ext;
                move_uploaded_file($_FILES['foto_ulasan']['tmp_name'], 'uploads/' . $filename);
                $foto_ulasan = $filename;
            }
        }
        $foto_val = $foto_ulasan ? "'$foto_ulasan'" : "NULL";
        mysqli_query($conn, "INSERT INTO reviews (kos_id, user_id, rating, komentar, foto, created_at) VALUES ('$id', '$user_id', '$rating', '$komentar', $foto_val, NOW())");
        mysqli_query($conn, "UPDATE kos SET rating = (SELECT ROUND(AVG(r.rating), 2) FROM reviews r WHERE r.kos_id = '$id') WHERE id = '$id'");
        $avg = mysqli_fetch_row(mysqli_query($conn, "SELECT rating FROM kos WHERE id='$id'"))[0];
        $success     = "Ulasan berhasil dikirim!";
        $ulasan_list = mysqli_query($conn, "SELECT r.*, u.nama AS nama_reviewer FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.kos_id='$id' ORDER BY r.created_at DESC");
    }
}

$foto_arr = [];
while($f = mysqli_fetch_assoc($foto_list)) $foto_arr[] = $f;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($kos['nama_kos']) ?> - CariKos.Ku</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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

<div class="detail-container">

    <?php if(count($foto_arr) > 0): ?>
    <div class="foto-galeri">
        <img src="uploads/<?= $foto_arr[0]['nama_file'] ?>" class="detail-img" id="foto-main" alt="Foto kos">
        <?php if(count($foto_arr) > 1): ?>
        <div class="foto-thumb-row">
            <?php foreach($foto_arr as $f): ?>
            <img src="uploads/<?= $f['nama_file'] ?>" class="foto-thumb" onclick="document.getElementById('foto-main').src=this.src" alt="Foto kos">
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?q=80&w=1200&auto=format&fit=crop" class="detail-img" alt="Foto kos">
    <?php endif; ?>

    <div class="detail-body">

        <div class="detail-left">

            <div class="detail-badge-wrap">
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
            </div>

            <h2 class="detail-nama"><?= htmlspecialchars($kos['nama_kos']) ?></h2>
            <div class="detail-price">Rp<?= number_format($kos['harga'], 0, ',', '.') ?> / bulan</div>

            <div class="detail-info-grid">
                <div class="detail-info-item">
                    <div class="detail-info-label">Alamat</div>
                    <div class="detail-info-value"><?= htmlspecialchars($kos['alamat']) ?></div>
                </div>
                <div class="detail-info-item">
                    <div class="detail-info-label">Dekat Lokasi</div>
                    <div class="detail-info-value"><?= htmlspecialchars($kos['kampus_terdekat']) ?></div>
                </div>
                <div class="detail-info-item">
                    <div class="detail-info-label">Fasilitas</div>
                    <div class="detail-info-value"><?= htmlspecialchars($kos['fasilitas']) ?></div>
                </div>
                <div class="detail-info-item">
                    <div class="detail-info-label">Jam Malam</div>
                    <div class="detail-info-value"><?= $kos['jam_malam'] ? htmlspecialchars($kos['jam_malam']) : 'Bebas jam malam' ?></div>
                </div>
                <div class="detail-info-item">
                    <div class="detail-info-label"><?= $kos['gender'] == 'putra' ? '👨' : ($kos['gender'] == 'putri' ? '👩' : '👥') ?> Tipe Kos</div>
                    <div class="detail-info-value">Kos <?= ucfirst($kos['gender']) ?></div>
                </div>
                <div class="detail-info-item">
                    <div class="detail-info-label">⭐ Rating</div>
                    <div class="detail-info-value"><?= $kos['rating'] > 0 ? $kos['rating'] : 'Belum ada rating' ?></div>
                </div>
            </div>

            <?php if($kos['lat'] && $kos['lng']): ?>
            <div class="map-container">
                <h3 class="map-title">Lokasi Kos</h3>
                <div id="map-detail" class="map-detail"></div>
            </div>
            <?php endif; ?>

            <div class="ulasan-section">
                <h3 class="ulasan-title">Ulasan Penghuni</h3>

                <?php if($error): ?>
                    <div class="alert-error"><?= $error ?></div>
                <?php endif; ?>
                <?php if($success): ?>
                    <div class="alert-success"><?= $success ?></div>
                <?php endif; ?>

                <form action="" method="POST" enctype="multipart/form-data" class="ulasan-form">
                    <div class="input-group">
                        <label>Rating</label>
                        <select name="rating" required>
                            <option value="5">⭐⭐⭐⭐⭐ - Sangat Bagus</option>
                            <option value="4">⭐⭐⭐⭐ - Bagus</option>
                            <option value="3">⭐⭐⭐ - Cukup</option>
                            <option value="2">⭐⭐ - Kurang</option>
                            <option value="1">⭐ - Buruk</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Komentar <span class="input-hint">(min. 20 karakter)</span></label>
                        <textarea name="komentar" class="ulasan-textarea" rows="3" placeholder="Bagikan pengalaman Anda..." required></textarea>
                    </div>
                    <div class="input-group">
                        <label>Foto Pendukung <span class="input-hint">(opsional, maks. 1 foto)</span></label>
                        <input type="file" name="foto_ulasan" class="upload-input" accept="image/jpeg,image/png">
                    </div>
                    <button type="submit" name="kirim_ulasan" class="btn">Kirim Ulasan</button>
                </form>

                <?php if(mysqli_num_rows($ulasan_list) == 0): ?>
                    <p class="ulasan-empty">Belum ada ulasan untuk kos ini.</p>
                <?php else: ?>
                    <?php while($ulasan = mysqli_fetch_assoc($ulasan_list)): ?>
                    <div class="ulasan-card">
                        <div class="ulasan-card-header">
                            <span class="ulasan-nama"><?= htmlspecialchars($ulasan['nama_reviewer']) ?></span>
                            <span class="ulasan-rating"><?= str_repeat('⭐', $ulasan['rating']) ?></span>
                        </div>
                        <p class="ulasan-komentar"><?= htmlspecialchars($ulasan['komentar']) ?></p>
                        <?php if(!empty($ulasan['foto'])): ?>
                            <img src="uploads/<?= $ulasan['foto'] ?>" class="ulasan-foto" onclick="window.open(this.src)" alt="Foto ulasan">
                        <?php endif; ?>
                        <p class="ulasan-tanggal"><?= date('d M Y', strtotime($ulasan['created_at'])) ?></p>
                    </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>

        </div>

        <div class="detail-right">

            <div class="kontak-card">
                <h3 class="kontak-title">Kontak Pemilik</h3>
                <p class="kontak-nama"><?= htmlspecialchars($kos['nama_pemilik']) ?></p>
                <p class="kontak-role">Pemilik Kos</p>
                <?php if($kos['hp_pemilik']): ?>
                    <div style="background:#F0FDF4;border:1px solid #22C55E;border-radius:12px;padding:14px;margin:12px 0;text-align:center;">
                        <p style="font-size:13px;color:#15803D;font-weight:600;margin-bottom:4px;">Nomor Kontak</p>
                        <p style="font-size:16px;font-weight:700;color:#1F2937;margin-bottom:12px;"><?= htmlspecialchars($kos['hp_pemilik']) ?></p>
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $kos['hp_pemilik']) ?>" target="_blank" style="display:block;">
                            <button class="btn" style="width:100%;background:#22C55E;">Chat via WhatsApp</button>
                        </a>
                        <a href="tel:<?= preg_replace('/[^0-9+]/', '', $kos['hp_pemilik']) ?>" style="display:block;margin-top:8px;">
                            <button class="btn btn-gray" style="width:100%;">Telepon Langsung</button>
                        </a>
                    </div>
                <?php else: ?>
                    <div style="background:#FEF2F2;border:1px solid #EF4444;border-radius:12px;padding:14px;text-align:center;margin:12px 0;">
                        <p style="color:#EF4444;font-size:13px;font-weight:600;margin-bottom:4px;">Kontak Belum Tersedia</p>
                        <p style="color:#6B7280;font-size:12px;">Pemilik belum mengisi nomor HP. Coba hubungi lewat laporan atau cari kos lain.</p>
                    </div>
                <?php endif; ?>
            </div>

            <a href="favorit.php?aksi=<?= $is_fav ? 'hapus' : 'tambah' ?>&kos_id=<?= $id ?>&redirect=detail_kos.php?id=<?= $id ?>">
                <button class="detail-right-btn <?= $is_fav ? 'detail-right-btn-fav-remove' : 'detail-right-btn-fav-add' ?>">
                    <?= $is_fav ? 'Hapus dari Favorit' : 'Simpan ke Favorit' ?>
                </button>
            </a>

            <a href="lapor_kos.php?id=<?= $kos['id'] ?>">
                <button class="detail-right-btn detail-right-btn-lapor">Laporkan Kos Ini</button>
            </a>

            <a href="index_penyewa.php">
                <button class="detail-right-btn detail-right-btn-back">← Kembali</button>
            </a>

        </div>

    </div>

</div>

<?php if($kos['lat'] && $kos['lng']): ?>
<script>
var map = L.map('map-detail').setView([<?= $kos['lat'] ?>, <?= $kos['lng'] ?>], 16);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);
L.marker([<?= $kos['lat'] ?>, <?= $kos['lng'] ?>])
    .addTo(map)
    .bindPopup('<b><?= addslashes(htmlspecialchars($kos['nama_kos'])) ?></b><br>Rp<?= number_format($kos['harga'],0,',','.') ?>/bln')
    .openPopup();
</script>
<?php endif; ?>

</body>
</html>