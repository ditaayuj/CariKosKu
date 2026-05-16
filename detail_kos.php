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

$id  = $_GET['id'];
$kos = mysqli_fetch_assoc(mysqli_query($conn, "SELECT k.*, u.nama AS nama_pemilik, u.no_hp AS hp_pemilik FROM kos k JOIN users u ON k.user_id = u.id WHERE k.id='$id' AND k.terverifikasi=1"));

if(!$kos){
    header("Location: index_penyewa.php");
    exit;
}

$ulasan_list = mysqli_query($conn, "SELECT r.*, u.nama AS nama_reviewer FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.kos_id='$id' ORDER BY r.created_at DESC");

$error   = "";
$success = "";

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['kirim_ulasan'])){
    $user_id = $_SESSION['id'];
    $rating  = $_POST['rating'];
    $komentar = $_POST['komentar'];

    $cek = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM reviews WHERE kos_id='$id' AND user_id='$user_id'"));

    if($cek[0] > 0){
        $error = "Anda sudah pernah memberikan ulasan untuk kos ini.";
    } elseif(strlen($komentar) < 20){
        $error = "Komentar minimal 20 karakter.";
    } else {
        mysqli_query($conn, "INSERT INTO reviews (kos_id, user_id, rating, komentar, created_at) VALUES ('$id', '$user_id', '$rating', '$komentar', NOW())");
        $success = "Ulasan berhasil dikirim!";
        $ulasan_list = mysqli_query($conn, "SELECT r.*, u.nama AS nama_reviewer FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.kos_id='$id' ORDER BY r.created_at DESC");
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $kos['nama_kos'] ?> - CariKos.Ku</title>
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

    <div class="detail-container">

        <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?q=80&w=1200&auto=format&fit=crop" class="detail-img">

        <div class="detail-body">

            <div class="detail-left">

                <div class="badge-group" style="margin-bottom:15px;">
                    <div class="badge verified">Terverifikasi</div>
                    <?php if($kos['status'] == 'tersedia'): ?>
                        <div class="badge verified">Tersedia</div>
                    <?php elseif($kos['status'] == 'hampir_penuh'): ?>
                        <div class="badge cheap">Hampir Penuh</div>
                    <?php else: ?>
                        <div class="badge full">Penuh</div>
                    <?php endif; ?>
                </div>

                <h2 style="color:#1F2937; font-size:28px; margin-bottom:10px;"><?= $kos['nama_kos'] ?></h2>

                <div class="price" style="font-size:28px; margin-bottom:20px;">
                    Rp<?= number_format($kos['harga'], 0, ',', '.') ?> / bulan
                </div>

                <div class="detail-info-grid">
                    <div class="detail-info-item">
                        <div class="detail-info-label">📍 Alamat</div>
                        <div class="detail-info-value"><?= $kos['alamat'] ?></div>
                    </div>
                    <div class="detail-info-item">
                        <div class="detail-info-label">🏫 Kampus Terdekat</div>
                        <div class="detail-info-value"><?= $kos['kampus_terdekat'] ?></div>
                    </div>
                    <div class="detail-info-item">
                        <div class="detail-info-label">🛏 Fasilitas</div>
                        <div class="detail-info-value"><?= $kos['fasilitas'] ?></div>
                    </div>
                    <div class="detail-info-item">
                        <div class="detail-info-label">🕒 Jam Malam</div>
                        <div class="detail-info-value"><?= $kos['jam_malam'] ? $kos['jam_malam'] : 'Bebas jam malam' ?></div>
                    </div>
                    <div class="detail-info-item">
                        <div class="detail-info-label">
                            <?= $kos['gender'] == 'putra' ? '👨' : ($kos['gender'] == 'putri' ? '👩' : '👥') ?> Tipe Kos
                        </div>
                        <div class="detail-info-value">Kos <?= ucfirst($kos['gender']) ?></div>
                    </div>
                    <div class="detail-info-item">
                        <div class="detail-info-label">⭐ Rating</div>
                        <div class="detail-info-value"><?= $kos['rating'] > 0 ? $kos['rating'] : 'Belum ada rating' ?></div>
                    </div>
                </div>

                <div class="ulasan-section">

                    <h3 style="color:#1F2937; margin-bottom:20px;">Ulasan Penghuni</h3>

                    <?php if($error): ?>
                        <p style="color:red; margin-bottom:15px;"><?= $error ?></p>
                    <?php endif; ?>

                    <?php if($success): ?>
                        <p style="color:green; margin-bottom:15px;"><?= $success ?></p>
                    <?php endif; ?>

                    <form action="" method="POST" style="background:#F9FAFB; padding:20px; border-radius:16px; margin-bottom:25px;">
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
                            <label>Komentar <span style="color:#9CA3AF; font-weight:400;">(min. 20 karakter)</span></label>
                            <textarea name="komentar" rows="3" placeholder="Bagikan pengalaman Anda..." style="width:100%; padding:15px; border:1px solid #D1D5DB; border-radius:12px; outline:none; font-size:14px; resize:vertical;" required></textarea>
                        </div>
                        <button type="submit" name="kirim_ulasan" class="btn" style="padding:12px;">Kirim Ulasan</button>
                    </form>

                    <?php if(mysqli_num_rows($ulasan_list) == 0): ?>
                        <p style="color:#9CA3AF; text-align:center; padding:30px 0;">Belum ada ulasan untuk kos ini.</p>
                    <?php else: ?>
                        <?php while($ulasan = mysqli_fetch_assoc($ulasan_list)): ?>
                        <div class="ulasan-card">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                                <strong style="color:#1F2937;"><?= $ulasan['nama_reviewer'] ?></strong>
                                <span style="color:#F0A629;"><?= str_repeat('⭐', $ulasan['rating']) ?></span>
                            </div>
                            <p style="color:#6B7280; font-size:14px;"><?= $ulasan['komentar'] ?></p>
                            <p style="color:#9CA3AF; font-size:12px; margin-top:8px;"><?= date('d M Y', strtotime($ulasan['created_at'])) ?></p>
                        </div>
                        <?php endwhile; ?>
                    <?php endif; ?>

                </div>

            </div>

            <div class="detail-right">

                <div class="kontak-card">
                    <h3 style="color:#1F2937; margin-bottom:15px;">Kontak Pemilik</h3>
                    <div style="font-size:32px; text-align:center; margin-bottom:10px;">🏠</div>
                    <p style="color:#1F2937; font-weight:600; text-align:center; margin-bottom:5px;"><?= $kos['nama_pemilik'] ?></p>
                    <p style="color:#6B7280; text-align:center; font-size:14px; margin-bottom:20px;">Pemilik Kos</p>
                    <?php if($kos['hp_pemilik']): ?>
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $kos['hp_pemilik']) ?>" target="_blank">
                            <button class="btn" style="width:100%; padding:14px;">💬 Hubungi via WhatsApp</button>
                        </a>
                        <p style="color:#6B7280; text-align:center; font-size:13px; margin-top:10px;">📞 <?= $kos['hp_pemilik'] ?></p>
                    <?php else: ?>
                        <p style="color:#9CA3AF; text-align:center; font-size:14px;">Kontak tidak tersedia</p>
                    <?php endif; ?>
                </div>

                <a href="lapor_kos.php?id=<?= $kos['id'] ?>">
                    <button class="btn-report" style="width:100%; padding:12px; margin-top:15px; border-radius:12px;">🚩 Laporkan Kos Ini</button>
                </a>

                <a href="index_penyewa.php">
                    <button class="btn-detail" style="width:100%; padding:12px; margin-top:10px; border-radius:12px; background:#9CA3AF;">← Kembali</button>
                </a>

            </div>

        </div>

    </div>

</body>
</html>