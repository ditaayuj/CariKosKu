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

$query_laporan = mysqli_query($conn, "
    SELECT r.*, k.nama_kos
    FROM reports r
    JOIN kos k ON r.kos_id = k.id
    WHERE r.reporter_id = '$user_id'
    ORDER BY r.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Saya - CariKos.Ku</title>
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

<div class="profil-page">

    <div class="profil-header" style="border-radius:24px; margin-bottom:24px;">
        <div class="profil-avatar">🚩</div>
        <div class="profil-header-info">
            <h2>Laporan Saya</h2>
            <p>Pantau perkembangan laporan yang pernah Anda kirim</p>
        </div>
    </div>

    <div class="profil-body">

        <div class="profil-card full-width">
            <h3>Riwayat Laporan</h3>

            <?php if(mysqli_num_rows($query_laporan) == 0): ?>
                <p style="color:#9CA3AF;font-size:14px;">
                    Anda belum pernah mengirim laporan.
                </p>
            <?php else: ?>

                <?php while($lap = mysqli_fetch_assoc($query_laporan)): ?>

                <div style="border:1px solid #E5E7EB;border-radius:16px;padding:20px;margin-bottom:16px;">

                    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:10px;margin-bottom:12px;">

                        <div>
                            <p style="font-weight:600;color:#1F2937;margin-bottom:4px;">
                                <?= htmlspecialchars($lap['nama_kos']) ?>
                            </p>

                            <p style="font-size:13px;color:#6B7280;">
                                Alasan: <?= htmlspecialchars($lap['alasan']) ?>
                            </p>

                            <p style="font-size:13px;color:#9CA3AF;">
                                <?= date('d M Y H:i', strtotime($lap['created_at'])) ?>
                            </p>
                        </div>

                        <div>
                            <span style="background:#F3F4F6;padding:6px 10px;border-radius:8px;font-size:12px;margin-right:5px;">
                                Level <?= (int)$lap['level_peringatan'] ?>
                            </span>

                            <span style="background:#DBEAFE;padding:6px 10px;border-radius:8px;font-size:12px;">
                                <?= ucfirst($lap['status']) ?>
                            </span>
                        </div>

                    </div>

                    <?php if(!empty($lap['keterangan'])): ?>
                    <div style="background:#F9FAFB;padding:12px;border-radius:10px;margin-bottom:12px;">
                        <strong>Keterangan Laporan</strong>
                        <p>
                            <?= nl2br(htmlspecialchars($lap['keterangan'])) ?>
                        </p>
                    </div>
                    <?php endif; ?>

                    <?php if(!empty($lap['pembelaan_pemilik'])): ?>
                    <div style="background:#EFF6FF;padding:12px;border-radius:10px;margin-bottom:12px;">
                        <strong>Pembelaan Pemilik</strong>
                        <p>
                            <?= nl2br(htmlspecialchars($lap['pembelaan_pemilik'])) ?>
                        </p>

                        <?php if(!empty($lap['foto_pembelaan'])): ?>
                            <img
                                src="uploads/<?= htmlspecialchars($lap['foto_pembelaan']) ?>"
                                style="max-width:250px;margin-top:10px;border-radius:10px;"
                            >
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php if(!empty($lap['tanggapan_admin'])): ?>
                    <div style="background:#F0FDF4;padding:12px;border-radius:10px;margin-bottom:12px;">
                        <strong>Tanggapan Admin</strong>
                        <p>
                            <?= nl2br(htmlspecialchars($lap['tanggapan_admin'])) ?>
                        </p>
                    </div>
                    <?php endif; ?>

                    <?php if($lap['status'] == 'pending'): ?>
                        <div style="background:#FEF3C7;padding:10px;border-radius:10px;">
                            ⏳ Menunggu peninjauan admin.
                        </div>
                    <?php elseif($lap['status'] == 'ditinjau'): ?>
                        <div style="background:#DBEAFE;padding:10px;border-radius:10px;">
                            🔎 Sedang ditinjau admin.
                        </div>
                    <?php else: ?>
                        <div style="background:#DCFCE7;padding:10px;border-radius:10px;">
                            ✅ Laporan telah selesai diproses.
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