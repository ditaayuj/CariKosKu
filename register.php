<?php
session_start();
require "koneksi.php";

$error   = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nama     = $_POST['nama'];
    $email    = $_POST['email'];
    $password = md5($_POST['password']);
    $role     = $_POST['role'];

    // Cek apakah email sudah terdaftar
    $cek = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if (mysqli_num_rows($cek) > 0) {
        $error = "Email sudah terdaftar!";
    } else {
        $query = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password', '$role')";
        mysqli_query($conn, $query);
        $success = "Akun berhasil dibuat! Silakan login.";
    }

}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CariKos.Ku</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="auth-page">

<div class="container">

    <div class="logo">
        <h1>CariKos.Ku</h1>
        <p>Buat akun baru</p>
    </div>

    <center>
        <div class="role-badge">Register Account</div>
    </center>

    <h2 class="form-title">Daftar Sekarang</h2>

    <?php if ($error): ?>
        <p style="color:red; text-align:center; margin-bottom:15px;">
            <?= $error ?>
        </p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color:green; text-align:center; margin-bottom:15px;">
            <?= $success ?>
        </p>
    <?php endif; ?>

    <form action="" method="POST">

        <div class="input-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" placeholder="Masukkan nama lengkap" required>
        </div>

        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="Masukkan email" required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan password" required>
        </div>

        <div class="input-group">
            <label>Daftar Sebagai</label>
            <select name="role" required>
                <option value="">-- Pilih Role --</option>
                <option value="penyewa">Penyewa</option>
                <option value="pemilik">Pemilik Kos</option>
            </select>
        </div>

        <button type="submit" class="btn">Daftar</button>

    </form>

    <div class="link">
        Sudah punya akun? <a href="login_penyewa.php">Login</a>
    </div>

</div>

</body>
</html>