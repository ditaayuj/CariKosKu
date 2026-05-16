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
$error   = "";
$success = "";

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'"));

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $nama  = $_POST['nama'];
    $no_hp = $_POST['no_hp'];

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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pemilik - CariKos.Ku</title>
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

    <div class="form-page">

        <div class="form-card">

            <div style="text-align:center; margin-bottom:30px;">
                <div style="width:80px; height:80px; background:#157A6E; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 15px; font-size:32px;">🏠</div>
                <h2 style="color:#1F2937; font-size:22px;"><?= $user['nama'] ?></h2>
                <p style="color:#6B7280; font-size:14px;">Pemilik Kos</p>
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
                    <input type="text" name="nama" value="<?= $user['nama'] ?>" required>
                </div>

                <div class="input-group">
                    <label>Email</label>
                    <input type="email" value="<?= $user['email'] ?>" disabled style="background:#F3F4F6; color:#9CA3AF;">
                </div>

                <div class="input-group">
                    <label>Nomor HP / WhatsApp</label>
                    <input type="text" name="no_hp" value="<?= $user['no_hp'] ?>" placeholder="Contoh: 08123456789">
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