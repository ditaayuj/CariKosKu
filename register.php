<?php
require "koneksi.php";

$error = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $nama     = $_POST['nama'];
    $email    = $_POST['email'];
    $password = md5($_POST['password']);
    $role     = $_POST['role'];

    $cek = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if(mysqli_num_rows($cek) > 0){
        $error = "Email sudah digunakan!";
    } else {
        $query = "INSERT INTO users(nama,email,password,role) VALUES('$nama','$email','$password','$role')";
        if(mysqli_query($conn, $query)){
            if($role == 'pemilik'){
                header("Location: login_pemilik.php");
            } else {
                header("Location: login_penyewa.php");
            }
            exit;
        } else {
            $error = "Registrasi gagal!";
        }
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
        <div class="role-badge">Register</div>
    </center>

    <h2 class="form-title">Daftar Akun</h2>

    <?php if($error): ?>
        <p style="color:red; text-align:center; margin-bottom:15px;"><?= $error ?></p>
    <?php endif; ?>

    <form action="" method="POST" id="formRegister">

        <div class="input-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" id="nama" placeholder="Masukkan nama" required>
        </div>

        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" id="email" placeholder="Masukkan email" required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" id="password" placeholder="Masukkan password" required>
        </div>

        <div class="input-group">
            <label>Daftar Sebagai</label>
            <select name="role" id="roleSelect" required>
                <option value="penyewa">Penyewa</option>
                <option value="pemilik">Pemilik Kos</option>
            </select>
        </div>

        <button type="submit" class="btn">Daftar</button>

    </form>

    <div class="link">
        Sudah punya akun? <a id="loginLink" href="login_penyewa.php">Login</a>
    </div>

</div>

<script>
document.getElementById('formRegister').addEventListener('submit', function(e){
    var nama     = document.getElementById('nama').value.trim();
    var email    = document.getElementById('email').value.trim();
    var password = document.getElementById('password').value;
    var role     = document.getElementById('roleSelect').value;

    if(nama.length < 3){
        e.preventDefault();
        alert('Nama lengkap minimal 3 karakter.');
        return;
    }

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

    if(password.length < 6){
        e.preventDefault();
        alert('Password minimal 6 karakter.');
        return;
    }

    if(role === ''){
        e.preventDefault();
        alert('Pilih peran terlebih dahulu.');
        return;
    }
});

document.getElementById('roleSelect').addEventListener('change', function(){
    document.getElementById('loginLink').href = this.value === 'pemilik' ? 'login_pemilik.php' : 'login_penyewa.php';
});
</script>

</body>
</html>