<?php
session_start();
require "koneksi.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email    = $_POST['email'];
    $password = md5($_POST['password']);

    $query  = "SELECT * FROM users WHERE email='$email' AND password='$password' AND role='pemilik'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        $_SESSION['id']    = $user['id'];
        $_SESSION['nama']  = $user['nama'];
        $_SESSION['role']  = $user['role'];

        header("Location: index_pemilik.php");
        exit;

    } else {
        $error = "Email atau password salah!";
    }

}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pemilik Kos - CariKos.Ku</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="auth-page">

<div class="container">

    <div class="logo">
        <h1>CariKos.Ku</h1>
        <p>Kelola listing kos anda</p>
    </div>

    <center>
        <div class="role-badge">Login Pemilik Kos</div>
    </center>

    <h2 class="form-title">Dashboard Pemilik</h2>

    <?php if ($error): ?>
        <p style="color:red; text-align:center; margin-bottom:15px;">
            <?= $error ?>
        </p>
    <?php endif; ?>

    <form action="" method="POST">

        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="Masukkan email" required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan password" required>
        </div>

        <button type="submit" class="btn">Login</button>

    </form>

    <div class="link">
        Belum punya akun? <a href="register.php">Daftar Sekarang</a>
    </div>

</div>

</body>
</html>