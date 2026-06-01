<?php
session_start();

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'pemilik'){
    header("Location: login_pemilik.php");
    exit;
}

require "koneksi.php";

$user_id = $_SESSION['id'];
$success = "";
$error   = "";

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['laporan_id'])){
    $laporan_id  = (int)$_POST['laporan_id'];
    $pembelaan   = mysqli_real_escape_string($conn, $_POST['pembelaan']);
    $foto_bela   = null;

    if(!empty($_FILES['foto_pembelaan']['name']) && $_FILES['foto_pembelaan']['error'] == 0){
        $allowed = ['image/jpeg','image/png'];
        if(in_array($_FILES['foto_pembelaan']['type'], $allowed)){
            $ext       = pathinfo($_FILES['foto_pembelaan']['name'], PATHINFO_EXTENSION);
            $filename  = 'bela_'.$user_id.'_'.time().'.'.$ext;
            move_uploaded_file($_FILES['foto_pembelaan']['tmp_name'], 'uploads/'.$filename);
            $foto_bela = $filename;
        }
    }

    if($foto_bela){
        mysqli_query($conn, "UPDATE reports SET pembelaan_pemilik='$pembelaan', foto_pembelaan='$foto_bela' WHERE id='$laporan_id'");
    } else {
        mysqli_query($conn, "UPDATE reports SET pembelaan_pemilik='$pembelaan' WHERE id='$laporan_id'");
    }

    $success = "Tanggapan berhasil dikirim ke admin.";
}

$query_laporan = mysqli_query($conn, "
    SELECT r.*, u.nama AS nama_pelapor, k.nama_kos
    FROM reports r
    JOIN users u ON r.reporter_id = u.id
    JOIN kos k ON r.kos_id = k.id
    WHERE k.user_id = '$user_id'
    ORDER BY r.created_at DESC
");

$query_notif = mysqli_query($conn, "
    SELECT * FROM notifikasi
    WHERE user_id = '$user_id'
    ORDER BY created_at DESC
    LIMIT 10
");

mysqli_query($conn, "UPDATE notifikasi SET is_read=1 WHERE user_id='$user_id'");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kos Saya - CariKos.Ku</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="navbar">
    <h1>CariKos.Ku</h1>
    <div class="menu">
        <a href="index_pemilik.php">Dashboard</a>
        <a href="tambah_kos.php">Tambah Kos</a>
        <a href="laporan_pemilik.php">Laporan</a>
        <a href="profil_pemilik.php">Profil</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="profil-page">

    <div class="profil-header pemilik" style="border-radius:24px; margin-bottom:24px;">
        <div class="profil-avatar">🚩</div>
        <div class="profil-header-info">
            <h2>Laporan & Notifikasi</h2>
            <p>Pantau laporan yang masuk untuk kos Anda</p>
        </div>
    </div>

    <?php if($success): ?><div class="alert-success"><?= $success ?></div><?php endif; ?>

    <div class="profil-body">

        <div class="profil-card full-width">
            <h3>Notifikasi dari Admin</h3>
            <?php if(mysqli_num_rows($query_notif) == 0): ?>
                <p style="color:#9CA3AF; font-size:14px;">Belum ada notifikasi.</p>
            <?php else: ?>
                <?php while($notif = mysqli_fetch_assoc($query_notif)): ?>
                <div style="padding:14px; border-radius:12px; margin-bottom:10px; background:<?= $notif['tipe']=='peringatan' ? '#FEF2F2' : ($notif['tipe']=='selesai' ? '#F0FDF4' : '#EFF6FF') ?>; border:1px solid <?= $notif['tipe']=='peringatan' ? '#EF4444' : ($notif['tipe']=='selesai' ? '#22C55E' : '#3B82F6') ?>;">
                    <p style="font-size:14px; color:#374151; margin-bottom:4px;"><?= htmlspecialchars($notif['pesan']) ?></p>
                    <p style="font-size:12px; color:#9CA3AF;"><?= date('d M Y H:i', strtotime($notif['created_at'])) ?></p>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

        <div class="profil-card full-width">
            <h3>Daftar Laporan Masuk</h3>
            <?php if(mysqli_num_rows($query_laporan) == 0): ?>
                <p style="color:#9CA3AF; font-size:14px;">Belum ada laporan untuk kos Anda.</p>
            <?php else: ?>
                <?php while($lap = mysqli_fetch_assoc($query_laporan)): ?>
                <div style="border:1px solid #E5E7EB; border-radius:16px; padding:20px; margin-bottom:16px;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px; flex-wrap:wrap; gap:8px;">
                        <div>
                            <p style="font-weight:600; color:#1F2937; margin-bottom:4px;"><?= htmlspecialchars($lap['nama_kos']) ?></p>
                            <p style="font-size:13px; color:#6B7280;">Dilaporkan oleh: <?= htmlspecialchars($lap['nama_pelapor']) ?></p>
                            <p style="font-size:13px; color:#6B7280;">Alasan: <?= htmlspecialchars($lap['alasan']) ?></p>
                        </div>
                        <div style="display:flex; gap:8px; align-items:center;">
                            <span class="badge <?= $lap['level_peringatan'] == 0 ? 'new' : ($lap['level_peringatan'] == 1 ? 'cheap' : ($lap['level_peringatan'] == 2 ? 'popular' : 'full')) ?>">
                                Level <?= $lap['level_peringatan'] ?>
                            </span>
                            <span class="badge <?= $lap['status']=='pending' ? 'full' : ($lap['status']=='ditinjau' ? 'cheap' : 'verified') ?>">
                                <?= ucfirst($lap['status']) ?>
                            </span>
                        </div>
                    </div>

                    <?php if($lap['tanggapan_admin']): ?>
                    <div style="background:#F0FDF4; padding:12px; border-radius:10px; margin-bottom:12px; border:1px solid #22C55E;">
                        <p style="font-size:12px; font-weight:600; color:#15803D; margin-bottom:4px;">Tanggapan Admin:</p>
                        <p style="font-size:13px; color:#374151;"><?= htmlspecialchars($lap['tanggapan_admin']) ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if(!$lap['pembelaan_pemilik'] && $lap['status'] != 'selesai'): ?>
                    <details style="margin-top:10px;">
                        <summary style="cursor:pointer; color:#157A6E; font-weight:600; font-size:14px;">📝 Berikan tanggapan</summary>
                        <form action="" method="POST" enctype="multipart/form-data" style="margin-top:12px;">
                            <input type="hidden" name="laporan_id" value="<?= $lap['id'] ?>">
                            <div class="profil-input-group">
                                <label>Tanggapan pemilik</label>
                                <textarea name="pembelaan" class="ulasan-textarea" rows="3" placeholder="Berikan tanggapan terkait laporan yang diberikan" required></textarea>
                            </div>
                            <div class="profil-input-group">
                                <label>Foto Pendukung <span class="input-hint">(opsional)</span></label>
                                <input type="file" name="foto_pembelaan" class="upload-input" accept="image/jpeg,image/png">
                            </div>
                            <button type="submit" class="profil-btn" style="margin-top:8px;">Kirim Tanggapan</button>
                        </form>
                    </details>
                    <?php elseif($lap['pembelaan_pemilik']): ?>
                    <div style="background:#EFF6FF; padding:12px; border-radius:10px; border:1px solid #3B82F6;">
                        <p style="font-size:12px; font-weight:600; color:#1D4ED8; margin-bottom:4px;">Tanggapan Anda:</p>
                        <p style="font-size:13px; color:#374151;"><?= htmlspecialchars($lap['pembelaan_pemilik']) ?></p>
                    </div>
                    <?php endif; ?>

                </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

    </div>

</div>

</body>
</html>