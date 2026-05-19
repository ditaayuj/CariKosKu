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
$error   = "";
$success = "";

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'"));

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $nama  = mysqli_real_escape_string($conn, $_POST['nama']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);

    if(!empty($_POST['password'])){
        $password = md5($_POST['password']);
        mysqli_query($conn, "UPDATE users SET nama='$nama', no_hp='$no_hp', password='$password' WHERE id='$user_id'");
    } else {
        mysqli_query($conn, "UPDATE users SET nama='$nama', no_hp='$no_hp' WHERE id='$user_id'");
    }

    $_SESSION['nama'] = $nama;
    $success = "Profil berhasil diperbarui!";
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'"));
}

$total_fav    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM favorites WHERE user_id='$user_id'"))[0];
$total_ulasan = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM reviews WHERE user_id='$user_id'"))[0];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - CariKos.Ku</title>
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

<div class="stats-container" style="max-width:700px; margin:40px auto 0;">
    <div class="stat-card">
        <div class="stat-icon stat-icon-red">❤️</div>
        <div class="stat-info">
            <div class="stat-number"><?= $total_fav ?></div>
            <div class="stat-label">Kos Favorit</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-green">⭐</div>
        <div class="stat-info">
            <div class="stat-number"><?= $total_ulasan ?></div>
            <div class="stat-label">Ulasan Diberikan</div>
        </div>
    </div>
</div>

<div class="form-page" style="padding-top:20px;">
    <div class="form-card">

        <div style="text-align:center; margin-bottom:30px;">
            <div style="width:80px; height:80px; background:#157A6E; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 15px; font-size:32px;">🧑‍🎓</div>
            <h2 style="color:#1F2937; font-size:22px;"><?= htmlspecialchars($user['nama']) ?></h2>
            <p style="color:#6B7280; font-size:14px;">Penyewa Kos</p>
        </div>

        <?php if($error): ?>
            <p style="color:red; margin-bottom:15px;"><?= $error ?></p>
        <?php endif; ?>

        <?php if($success): ?>
            <p style="color:green; margin-bottom:15px;"><?= $success ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="input-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>
            </div>
            <div class="input-group">
                <label>Email</label>
                <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled style="background:#F3F4F6; color:#9CA3AF;">
            </div>
            <div class="input-group">
                <label>Nomor HP / WhatsApp</label>
                <input type="text" name="no_hp" value="<?= htmlspecialchars($user['no_hp'] ?? '') ?>" placeholder="Contoh: 08123456789">
            </div>
            <div class="input-group">
                <label>Password Baru <span style="color:#9CA3AF; font-weight:400;">(kosongkan jika tidak ingin ganti)</span></label>
                <input type="password" name="password" placeholder="Masukkan password baru">
            </div>
            <button type="submit" class="btn">Simpan Perubahan</button>
        </form>

    </div>
</div>

</body>
</html>