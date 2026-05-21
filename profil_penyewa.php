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

<div class="profil-page">

    <div class="profil-header">
        <div class="profil-avatar">🧑‍🎓</div>
        <div class="profil-header-info">
            <h2><?= htmlspecialchars($user['nama']) ?></h2>
            <p>Penyewa Kos • <?= htmlspecialchars($user['email']) ?></p>
        </div>
    </div>

    <div class="profil-stat-grid">
        <div class="profil-stat-item">
            <div class="number">❤️ <?= $total_fav ?></div>
            <div class="label">Kos Favorit</div>
        </div>
        <div class="profil-stat-item">
            <div class="number">⭐ <?= $total_ulasan ?></div>
            <div class="label">Ulasan Diberikan</div>
        </div>
    </div>

    <?php if($error): ?><div class="alert-error"><?= $error ?></div><?php endif; ?>
    <?php if($success): ?><div class="alert-success"><?= $success ?></div><?php endif; ?>

    <form action="" method="POST" id="formProfil">
        <div class="profil-body">

            <div class="profil-card">
                <h3>Informasi Akun</h3>
                <div class="profil-input-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" id="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>
                </div>
                <div class="profil-input-group">
                    <label>Email</label>
                    <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                </div>
                <div class="profil-input-group">
                    <label>Nomor HP / WhatsApp</label>
                    <input type="text" name="no_hp" id="no_hp" value="<?= htmlspecialchars($user['no_hp'] ?? '') ?>" placeholder="Contoh: 08123456789">
                </div>
            </div>

            <div class="profil-card">
                <h3>Ganti Password</h3>
                <div class="profil-input-group">
                    <label>Password Baru <span class="input-hint">(kosongkan jika tidak ingin ganti)</span></label>
                    <input type="password" name="password" id="password" placeholder="Masukkan password baru">
                </div>
                <div class="profil-input-group">
                    <label>Konfirmasi Password Baru</label>
                    <input type="password" id="confirm_password" placeholder="Ulangi password baru">
                </div>
            </div>

            <div class="profil-card full-width">
                <button type="submit" class="profil-btn">Simpan Perubahan</button>
            </div>

        </div>
    </form>

</div>

<script>
document.getElementById('formProfil').addEventListener('submit', function(e){
    var nama     = document.getElementById('nama').value.trim();
    var password = document.getElementById('password').value;
    var confirm  = document.getElementById('confirm_password').value;
    if(nama.length < 3){ e.preventDefault(); alert('Nama minimal 3 karakter.'); return; }
    if(password !== '' && password.length < 6){ e.preventDefault(); alert('Password baru minimal 6 karakter.'); return; }
    if(password !== confirm){ e.preventDefault(); alert('Konfirmasi password tidak cocok.'); return; }
});
</script>

</body>
</html>