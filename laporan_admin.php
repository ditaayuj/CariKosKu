<?php
session_start();

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login_admin.php");
    exit;
}

require "koneksi.php";

$id     = (int)$_GET['id'];
$laporan = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT r.*, u.nama AS nama_pelapor, u.email AS email_pelapor,
           k.nama_kos, k.user_id AS pemilik_id, pk.nama AS nama_pemilik
    FROM reports r
    JOIN users u ON r.reporter_id = u.id
    JOIN kos k ON r.kos_id = k.id
    JOIN users pk ON k.user_id = pk.id
    WHERE r.id = '$id'
"));

if(!$laporan){
    header("Location: index_admin.php");
    exit;
}

$success = "";
$error   = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $aksi = $_POST['aksi'];

    if($aksi == 'beri_tanggapan'){
        $tanggapan = mysqli_real_escape_string($conn, $_POST['tanggapan']);
        $status    = $_POST['status'];
        mysqli_query($conn, "UPDATE reports SET tanggapan_admin='$tanggapan', status='$status' WHERE id='$id'");

        $pesan_notif = mysqli_real_escape_string($conn, "Laporan untuk kos \"{$laporan['nama_kos']}\" mendapat tanggapan admin: $tanggapan");
        mysqli_query($conn, "INSERT INTO notifikasi (user_id, kos_id, report_id, pesan, tipe) VALUES ('{$laporan['pemilik_id']}', '{$laporan['kos_id']}', '$id', '$pesan_notif', 'info')");
        mysqli_query($conn, "INSERT INTO notifikasi (user_id, kos_id, report_id, pesan, tipe) VALUES ('{$laporan['reporter_id']}', '{$laporan['kos_id']}', '$id', '$pesan_notif', 'info')");

        $success = "Tanggapan berhasil dikirim ke pemilik dan pelapor.";
        $laporan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT r.*, u.nama AS nama_pelapor, u.email AS email_pelapor, k.nama_kos, k.user_id AS pemilik_id, pk.nama AS nama_pemilik FROM reports r JOIN users u ON r.reporter_id=u.id JOIN kos k ON r.kos_id=k.id JOIN users pk ON k.user_id=pk.id WHERE r.id='$id'"));

    } elseif($aksi == 'naikkan_level'){
        $level_baru = min($laporan['level_peringatan'] + 1, 3);
        mysqli_query($conn, "UPDATE reports SET level_peringatan='$level_baru', status='ditinjau' WHERE id='$id'");

        $tipe  = $level_baru == 3 ? 'peringatan' : 'info';
        $pesan = mysqli_real_escape_string($conn, "⚠️ Peringatan Level $level_baru untuk kos \"{$laporan['nama_kos']}\". Harap segera tinjau laporan yang masuk.");
        mysqli_query($conn, "INSERT INTO notifikasi (user_id, kos_id, report_id, pesan, tipe) VALUES ('{$laporan['pemilik_id']}', '{$laporan['kos_id']}', '$id', '$pesan', '$tipe')");

        $pesan_pelapor = mysqli_real_escape_string($conn, "Laporan Anda sedang ditindaklanjuti admin. Level peringatan kos saat ini menjadi Level $level_baru.");

        mysqli_query($conn, "
        INSERT INTO notifikasi
        (user_id, kos_id, report_id, pesan, tipe)
        VALUES
            (
            '{$laporan['reporter_id']}',
            '{$laporan['kos_id']}',
            '$id',
            '$pesan_pelapor',
            'info'
            )
        ");
        
        $success = "Level peringatan dinaikkan ke Level $level_baru.";
        $laporan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT r.*, u.nama AS nama_pelapor, u.email AS email_pelapor, k.nama_kos, k.user_id AS pemilik_id, pk.nama AS nama_pemilik FROM reports r JOIN users u ON r.reporter_id=u.id JOIN kos k ON r.kos_id=k.id JOIN users pk ON k.user_id=pk.id WHERE r.id='$id'"));

    } elseif($aksi == 'hapus_listing'){
        $alasan_hapus = mysqli_real_escape_string(
        $conn,
        $_POST['alasan_hapus']
        );

    mysqli_query($conn, "
        UPDATE reports
        SET status='selesai'
        WHERE kos_id='{$laporan['kos_id']}'
    ");

    mysqli_query($conn, "
        DELETE FROM kos
        WHERE id='{$laporan['kos_id']}'
    ");

    $pesan_pemilik = mysqli_real_escape_string(
        $conn,
        "Listing kos \"{$laporan['nama_kos']}\" telah dihapus oleh admin. Alasan: $alasan_hapus"
    );

    mysqli_query($conn, "
        INSERT INTO notifikasi
        (user_id, kos_id, report_id, pesan, tipe)
        VALUES
        (
            '{$laporan['pemilik_id']}',
            '{$laporan['kos_id']}',
            '$id',
            '$pesan_pemilik',
            'peringatan'
        )
    ");

    $pesan_pelapor = mysqli_real_escape_string(
        $conn,
        "Laporan Anda terbukti valid. Listing kos \"{$laporan['nama_kos']}\" telah dihapus oleh admin."
    );

    mysqli_query($conn, "
        INSERT INTO notifikasi
        (user_id, kos_id, report_id, pesan, tipe)
        VALUES
        (
            '{$laporan['reporter_id']}',
            '{$laporan['kos_id']}',
            '$id',
            '$pesan_pelapor',
            'selesai'
        )
    ");

    header("Location: index_admin.php");
    exit;

    } elseif($aksi == 'tutup_laporan'){
        mysqli_query($conn, "UPDATE reports SET status='selesai' WHERE id='$id'");

        $pesan = mysqli_real_escape_string($conn, "Laporan untuk kos \"{$laporan['nama_kos']}\" telah ditutup. Terima kasih atas laporan Anda.");
        mysqli_query($conn, "INSERT INTO notifikasi (user_id, kos_id, report_id, pesan, tipe) VALUES ('{$laporan['reporter_id']}', '{$laporan['kos_id']}', '$id', '$pesan', 'selesai')");

        $success = "Laporan ditutup.";
        $laporan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT r.*, u.nama AS nama_pelapor, u.email AS email_pelapor, k.nama_kos, k.user_id AS pemilik_id, pk.nama AS nama_pemilik FROM reports r JOIN users u ON r.reporter_id=u.id JOIN kos k ON r.kos_id=k.id JOIN users pk ON k.user_id=pk.id WHERE r.id='$id'"));
    }
}

$pembelaan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT pembelaan_pemilik, foto_pembelaan FROM reports WHERE id='$id'"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tinjau Laporan - CariKos.Ku</title>
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

<div class="profil-page">

    <div class="profil-header admin-hero" style="border-radius:24px; margin-bottom:24px;">
        <div class="profil-avatar">🚩</div>
        <div class="profil-header-info">
            <h2>Tinjau Laporan</h2>
            <p>Kos: <?= htmlspecialchars($laporan['nama_kos']) ?> • Level Peringatan: <?= $laporan['level_peringatan'] ?>/3</p>
        </div>
    </div>

    <?php if($success): ?><div class="alert-success"><?= $success ?></div><?php endif; ?>
    <?php if($error): ?><div class="alert-error"><?= $error ?></div><?php endif; ?>

    <div class="profil-body">

        <div class="profil-card">
            <h3>Detail Laporan</h3>
            <div class="profil-input-group">
                <label>Kos Dilaporkan</label>
                <input type="text" value="<?= htmlspecialchars($laporan['nama_kos']) ?>" disabled>
            </div>
            <div class="profil-input-group">
                <label>Pemilik Kos</label>
                <input type="text" value="<?= htmlspecialchars($laporan['nama_pemilik']) ?>" disabled>
            </div>
            <div class="profil-input-group">
                <label>Pelapor</label>
                <input type="text" value="<?= htmlspecialchars($laporan['nama_pelapor']) ?> (<?= htmlspecialchars($laporan['email_pelapor']) ?>)" disabled>
            </div>
            <div class="profil-input-group">
                <label>Alasan Laporan</label>
                <input type="text" value="<?= htmlspecialchars($laporan['alasan']) ?>" disabled>
            </div>
            <?php if($laporan['keterangan']): ?>
            <div class="profil-input-group">
                <label>Keterangan Tambahan</label>
                <textarea class="ulasan-textarea" rows="3" disabled><?= htmlspecialchars($laporan['keterangan']) ?></textarea>
            </div>
            <?php endif; ?>
            <div class="profil-input-group">
                <label>Status</label>
                <input type="text" value="<?= ucfirst($laporan['status']) ?>" disabled>
            </div>
            <div class="profil-input-group">
                <label>Level Peringatan</label>
                <input type="text" value="Level <?= $laporan['level_peringatan'] ?> / 3" disabled>
            </div>
            <div class="profil-input-group">
                <label>Tanggal Laporan</label>
                <input type="text" value="<?= date('d M Y H:i', strtotime($laporan['created_at'])) ?>" disabled>
            </div>
        </div>

        <div class="profil-card">
            <h3>Tanggapan Pemilik</h3>
            <?php if($pembelaan && $pembelaan['pembelaan_pemilik']): ?>
                <p style="color:#374151; font-size:14px; line-height:1.7; margin-bottom:12px;"><?= htmlspecialchars($pembelaan['pembelaan_pemilik']) ?></p>
                <?php if($pembelaan['foto_pembelaan']): ?>
                    <img src="uploads/<?= $pembelaan['foto_pembelaan'] ?>" style="width:100%; border-radius:12px; cursor:pointer;" onclick="window.open(this.src)" alt="Foto pembelaan">
                <?php endif; ?>
            <?php else: ?>
                <p style="color:#9CA3AF; font-size:14px;">Belum ada tanggapan dari pemilik kos.</p>
            <?php endif; ?>

            <?php if($laporan['tanggapan_admin']): ?>
            <div style="margin-top:20px; padding:16px; background:#F0FDF4; border-radius:12px; border:1px solid #22C55E;">
                <p style="color:#15803D; font-size:13px; font-weight:600; margin-bottom:6px;">Tanggapan Admin:</p>
                <p style="color:#374151; font-size:14px;"><?= htmlspecialchars($laporan['tanggapan_admin']) ?></p>
            </div>
            <?php endif; ?>
        </div>

        <div class="profil-card full-width">
            <h3>Tindakan Admin</h3>

            <form action="" method="POST" style="margin-bottom:20px;">
                <input type="hidden" name="aksi" value="beri_tanggapan">
                <div class="profil-input-group">
                    <label>Tanggapan / Pesan ke Pemilik & Pelapor</label>
                    <textarea name="tanggapan" class="ulasan-textarea" rows="3" placeholder="Tulis tanggapan atau permintaan klarifikasi..." required></textarea>
                </div>
                <div class="profil-input-group">
                    <label>Update Status</label>
                    <select name="status" style="width:100%; padding:12px; border:1px solid #E5E7EB; border-radius:12px; font-family:'Poppins',sans-serif;">
                        <option value="ditinjau" <?= $laporan['status']=='ditinjau'?'selected':'' ?>>Sedang Ditinjau</option>
                        <option value="pending"  <?= $laporan['status']=='pending' ?'selected':'' ?>>Pending</option>
                        <option value="selesai"  <?= $laporan['status']=='selesai' ?'selected':'' ?>>Selesai</option>
                    </select>
                </div>
                <button type="submit" class="profil-btn">💬 Kirim Tanggapan</button>
            </form>

            <div style="display:flex; gap:12px; flex-wrap:wrap;">
                <?php if($laporan['level_peringatan'] < 3): ?>
                <form action="" method="POST">
                    <input type="hidden" name="aksi" value="naikkan_level">
                    <button type="submit" class="profil-btn" style="background:#F0A629;" onclick="return confirm('Naikkan level peringatan ke Level <?= $laporan['level_peringatan']+1 ?>?')">
                        ⚠️ Naikkan ke Level <?= $laporan['level_peringatan']+1 ?>
                    </button>
                </form>
                <?php endif; ?>

                <?php if($laporan['level_peringatan'] >= 3): ?>
                <form action="" method="POST">
                    <input type="hidden" name="aksi" value="hapus_listing">

                    <div class="profil-input-group" style="margin-bottom:10px;">
                        <textarea
                            name="alasan_hapus"
                            class="ulasan-textarea"
                            rows="3"
                            placeholder="Tuliskan alasan penghapusan listing..."
                            required
                        ></textarea>
                </div>

                    <button type="submit" class="profil-btn" style="background:#EF4444;" onclick="return confirm('Yakin hapus listing kos ini? Tindakan ini tidak bisa dibatalkan.')">
                        🗑️ Hapus Listing Kos
                    </button>
                </form>
                <?php endif; ?>

                <form action="" method="POST">
                    <input type="hidden" name="aksi" value="tutup_laporan">
                    <button type="submit" class="profil-btn" style="background:#22C55E;" onclick="return confirm('Tutup laporan ini? Berarti laporan dianggap tidak valid.')">
                        ✅ Tutup Laporan (Tidak Valid)
                    </button>
                </form>

                <a href="index_admin.php" class="profil-btn" style="background:#9CA3AF; text-decoration:none; display:inline-block;">← Kembali</a>
            </div>

        </div>

    </div>

</div>

</body>
</html>