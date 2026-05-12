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
$nama    = $_SESSION['nama'];

$query_kos  = mysqli_query($conn, "SELECT * FROM kos WHERE user_id = '$user_id'");
$total_kos  = mysqli_num_rows($query_kos);

$query_tersedia   = mysqli_query($conn, "SELECT * FROM kos WHERE user_id='$user_id' AND status='tersedia'");
$total_tersedia   = mysqli_num_rows($query_tersedia);

$query_penuh      = mysqli_query($conn, "SELECT * FROM kos WHERE user_id='$user_id' AND status='penuh'");
$total_penuh      = mysqli_num_rows($query_penuh);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pemilik - CariKos.Ku</title>
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

    <div class="dashboard-hero">
        <div class="dashboard-welcome">
            <p class="welcome-label">Selamat datang kembali,</p>
            <h2><?= $nama ?></h2>
            <p class="welcome-sub">Kelola listing kos Anda dengan mudah di sini.</p>
        </div>
        <a href="tambah_kos.php">
            <button class="btn-tambah">+ Tambah Kos Baru</button>
        </a>
    </div>

    <div class="stats-container">

        <div class="stat-card">
            <div class="stat-icon stat-icon-blue">🏠</div>
            <div class="stat-info">
                <div class="stat-number"><?= $total_kos ?></div>
                <div class="stat-label">Total Kos</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-green">✅</div>
            <div class="stat-info">
                <div class="stat-number"><?= $total_tersedia ?></div>
                <div class="stat-label">Kos Tersedia</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-red">🔴</div>
            <div class="stat-info">
                <div class="stat-number"><?= $total_penuh ?></div>
                <div class="stat-label">Kos Penuh</div>
            </div>
        </div>

    </div>

    <h2 class="section-title">Daftar Kos Saya</h2>

    <div class="listing-container">

        <?php
        $query_kos = mysqli_query($conn, "SELECT * FROM kos WHERE user_id = '$user_id'");

        if(mysqli_num_rows($query_kos) == 0): ?>

            <div class="empty-state">
                <div class="empty-icon">🏠</div>
                <h3>Belum Ada Kos</h3>
                <p>Anda belum menambahkan kos apapun.</p>
                <a href="tambah_kos.php">
                    <button class="btn-tambah">+ Tambah Kos Sekarang</button>
                </a>
            </div>

        <?php else:
            while($kos = mysqli_fetch_assoc($query_kos)): ?>

            <div class="card">

                <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?q=80&w=1200&auto=format&fit=crop">

                <div class="card-content">

                    <div class="badge-group">
                        <?php if($kos['terverifikasi'] == 1): ?>
                            <div class="badge verified">Terverifikasi</div>
                        <?php else: ?>
                            <div class="badge" style="background:#9CA3AF;">Belum Verifikasi</div>
                        <?php endif; ?>

                        <?php if($kos['status'] == 'tersedia'): ?>
                            <div class="badge verified">Tersedia</div>
                        <?php elseif($kos['status'] == 'hampir_penuh'): ?>
                            <div class="badge cheap">Hampir Penuh</div>
                        <?php else: ?>
                            <div class="badge full">Penuh</div>
                        <?php endif; ?>
                    </div>

                    <h3><?= $kos['nama_kos'] ?></h3>

                    <div class="price">
                        Rp<?= number_format($kos['harga'], 0, ',', '.') ?> / bulan
                    </div>

                    <div class="info">📍 <?= $kos['alamat'] ?></div>
                    <div class="info">🏫 Dekat <?= $kos['kampus_terdekat'] ?></div>
                    <div class="info">🛏 <?= $kos['fasilitas'] ?></div>

                    <?php if($kos['jam_malam']): ?>
                        <div class="info">🕒 Jam malam <?= $kos['jam_malam'] ?></div>
                    <?php else: ?>
                        <div class="info">🕒 Bebas jam malam</div>
                    <?php endif; ?>

                    <div class="info">
                        <?= $kos['gender'] == 'putra' ? '👨' : ($kos['gender'] == 'putri' ? '👩' : '👥') ?>
                        Kos <?= ucfirst($kos['gender']) ?>
                    </div>

                    <div class="rating">⭐ <?= $kos['rating'] ?> rating</div>

                    <div class="button-group">
                        <a href="edit_kos.php?id=<?= $kos['id'] ?>" style="flex:1;">
                            <button class="btn-detail" style="width:100%;">Edit</button>
                        </a>
                        <a href="hapus_kos.php?id=<?= $kos['id'] ?>" onclick="return confirm('Yakin ingin menghapus kos ini?')" style="flex:1;">
                            <button class="btn-report" style="width:100%;">Hapus</button>
                        </a>
                    </div>

                </div>
            </div>

        <?php endwhile;
        endif; ?>

    </div>

</body>
</html>