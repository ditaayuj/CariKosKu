<?php
session_start();

if(!isset($_SESSION['role'])){
    header("Location: login_admin.php");
    exit;
}

if($_SESSION['role'] != 'admin'){
    header("Location: login_admin.php");
    exit;
}

require "koneksi.php";

$nama             = $_SESSION['nama'];
$total_user       = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role != 'admin'"))[0];
$total_penyewa    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role='penyewa'"))[0];
$total_pemilik    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role='pemilik'"))[0];
$total_kos        = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM kos"))[0];
$total_verified   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM kos WHERE terverifikasi=1"))[0];
$total_unverified = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM kos WHERE terverifikasi=0"))[0];
$total_laporan    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM reports WHERE status='pending'"))[0];

$query_users   = mysqli_query($conn, "SELECT * FROM users WHERE role != 'admin' ORDER BY created_at DESC");
$query_kos     = mysqli_query($conn, "SELECT k.*, u.nama AS nama_pemilik FROM kos k JOIN users u ON k.user_id=u.id ORDER BY k.created_at DESC");
$query_laporan = mysqli_query($conn, "SELECT r.*, u.nama AS nama_pelapor, k.nama_kos, k.user_id AS pemilik_id FROM reports r JOIN users u ON r.reporter_id=u.id JOIN kos k ON r.kos_id=k.id ORDER BY r.created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - CariKos.Ku</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="navbar">
    <h1>CariKos.Ku</h1>
    <div class="menu">
        <a href="index_admin.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="dashboard-hero admin-hero">
    <div class="dashboard-welcome">
        <p class="welcome-label">Administrator Panel</p>
        <h2><?= htmlspecialchars($nama) ?></h2>
        <p class="welcome-sub">Kelola seluruh data pengguna dan kos di sini.</p>
    </div>
</div>

<div class="stats-container">
    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">👥</div>
        <div class="stat-info"><div class="stat-number"><?= $total_user ?></div><div class="stat-label">Total Pengguna</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-green">🧑‍🎓</div>
        <div class="stat-info"><div class="stat-number"><?= $total_penyewa ?></div><div class="stat-label">Penyewa</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-yellow">🏠</div>
        <div class="stat-info"><div class="stat-number"><?= $total_pemilik ?></div><div class="stat-label">Pemilik Kos</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">🏘️</div>
        <div class="stat-info"><div class="stat-number"><?= $total_kos ?></div><div class="stat-label">Total Kos</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-green">✅</div>
        <div class="stat-info"><div class="stat-number"><?= $total_verified ?></div><div class="stat-label">Kos Terverifikasi</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-red">🚩</div>
        <div class="stat-info"><div class="stat-number"><?= $total_laporan ?></div><div class="stat-label">Laporan Pending</div></div>
    </div>
</div>

<div class="admin-section">
    <h2 class="section-title">Laporan Masuk</h2>
    <div class="table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kos Dilaporkan</th>
                    <th>Pelapor</th>
                    <th>Alasan</th>
                    <th>Level</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while($lap = mysqli_fetch_assoc($query_laporan)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($lap['nama_kos']) ?></td>
                    <td><?= htmlspecialchars($lap['nama_pelapor']) ?></td>
                    <td><?= htmlspecialchars($lap['alasan']) ?></td>
                    <td>
                        <span class="badge <?= $lap['level_peringatan'] == 0 ? 'new' : ($lap['level_peringatan'] == 1 ? 'cheap' : ($lap['level_peringatan'] == 2 ? 'popular' : 'full')) ?>">
                            Level <?= $lap['level_peringatan'] ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge <?= $lap['status'] == 'pending' ? 'full' : ($lap['status'] == 'ditinjau' ? 'cheap' : 'verified') ?>">
                            <?= ucfirst($lap['status']) ?>
                        </span>
                    </td>
                    <td><?= date('d M Y', strtotime($lap['created_at'])) ?></td>
                    <td>
                        <a href="laporan_admin.php?id=<?= $lap['id'] ?>">
                            <button class="table-btn table-btn-green">Tinjau</button>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="admin-section">
    <h2 class="section-title">Data Pengguna</h2>
    <div class="table-wrapper">
        <table class="admin-table">
            <thead>
                <tr><th>No</th><th>Nama</th><th>Email</th><th>Role</th><th>No HP</th><th>Terdaftar</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                <?php $no = 1; while($user = mysqli_fetch_assoc($query_users)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($user['nama']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><span class="badge <?= $user['role'] == 'penyewa' ? 'new' : 'popular' ?>"><?= ucfirst($user['role']) ?></span></td>
                    <td><?= $user['no_hp'] ?? '-' ?></td>
                    <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                    <td>
                        <a href="hapus_user.php?id=<?= $user['id'] ?>" onclick="return confirm('Yakin hapus user ini?')">
                            <button class="table-btn table-btn-red">Hapus</button>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="admin-section">
    <h2 class="section-title">Data Kos</h2>
    <div class="table-wrapper">
        <table class="admin-table">
            <thead>
                <tr><th>No</th><th>Nama Kos</th><th>Pemilik</th><th>Harga</th><th>Gender</th><th>Status</th><th>Dokumen</th><th>Verifikasi</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                <?php $no = 1; while($kos = mysqli_fetch_assoc($query_kos)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($kos['nama_kos']) ?></td>
                    <td><?= htmlspecialchars($kos['nama_pemilik']) ?></td>
                    <td>Rp<?= number_format($kos['harga'], 0, ',', '.') ?></td>
                    <td><?= ucfirst($kos['gender']) ?></td>
                    <td>
                        <span class="badge <?= $kos['status'] == 'tersedia' ? 'verified' : ($kos['status'] == 'hampir_penuh' ? 'cheap' : 'full') ?>">
                            <?= ucfirst(str_replace('_', ' ', $kos['status'])) ?>
                        </span>
                    </td>
                    <td>
                        <?php if (!empty($kos['dokumen_kepemilikan'])): ?>
                            <a href="uploads/<?= $kos['dokumen_kepemilikan'] ?>" target="_blank">
                                <button class="table-btn">Lihat Dokumen</button>
                            </a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($kos['terverifikasi'] == 1): ?>
                            <span class="badge verified">Verified</span>
                        <?php else: ?>
                            <a href="verifikasi_kos.php?id=<?= $kos['id'] ?>">
                                <button class="table-btn table-btn-green">Verifikasi</button>
                            </a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="hapus_kos.php?id=<?= $kos['id'] ?>" onclick="return confirm('Yakin hapus kos ini?')">
                            <button class="table-btn table-btn-red">Hapus</button>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>