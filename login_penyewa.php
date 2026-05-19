<?php
session_start();
require "koneksi.php";

$error = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $email    = $_POST['email'];
    $password = md5($_POST['password']);
    $query    = "SELECT * FROM users WHERE email='$email' AND password='$password' AND role='penyewa'";
    $result   = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) == 1){
        $user = mysqli_fetch_assoc($result);
        $_SESSION['id']   = $user['id'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];
        header("Location: index_penyewa.php");
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
    <title>Login Penyewa - CariKos.Ku</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">

<div class="container">

    <div class="logo">
        <h1>CariKos.Ku</h1>
        <p>Temukan kos terbaik untukmu</p>
    </div>

    <center>
        <div class="role-badge">Login Penyewa</div>
    </center>

    <h2 class="form-title">Masuk ke Akun</h2>

    <?php if($error): ?>
        <p style="color:red; text-align:center; margin-bottom:15px;"><?= $error ?></p>
    <?php endif; ?>

    <form action="" method="POST" id="formLogin">

        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" id="email" placeholder="Masukkan email" required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" id="password" placeholder="Masukkan password" required>
        </div>

        <button type="submit" class="btn">Login</button>

    </form>

    <div class="link">
        Belum punya akun? <a href="register.php">Daftar Sekarang</a>
    </div>

</div>

<script>
document.getElementById('formLogin').addEventListener('submit', function(e){
    var email    = document.getElementById('email').value.trim();
    var password = document.getElementById('password').value;

    if(email === ''){
        e.preventDefault();
        alert('Email tidak boleh kosong.');
        return;
    }

    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if(!emailRegex.test(email)){
        e.preventDefault();
        alert('Format email tidak valid.');
        return;
    }

    if(password === ''){
        e.preventDefault();
        alert('Password tidak boleh kosong.');
        return;
    }
});
</script>

</body>
</html>