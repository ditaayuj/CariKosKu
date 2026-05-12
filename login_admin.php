<?php
session_start();
require "koneksi.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'];
    $password = md5($_POST['password']);

    // Admin login pakai nama, bukan email
    $query  = "SELECT * FROM users WHERE nama='$username' AND password='$password' AND role='admin'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        $_SESSION['id']    = $user['id'];
        $_SESSION['nama']  = $user['nama'];
        $_SESSION['role']  = $user['role'];

        header("Location: index_admin.php");
        exit;

    } else {
        $error = "Username atau password salah!";
    }

}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - CariKos.Ku</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="auth-page">

<div class="container">

    <div class="logo">
        <h1>CariKos.Ku</h1>
        <p>Administrator Panel</p>
    </div>

    <center>
        <div class="role-badge">Login Admin</div>
    </center>

    <h2 class="form-title">Admin Access</h2>

    <?php if ($error): ?>
        <p style="color:red; text-align:center; margin-bottom:15px;">
            <?= $error ?>
        </p>
    <?php endif; ?>

    <form action="" method="POST">

        <div class="input-group">
            <label>Username</label>
            <input type="text" name="username" placeholder="Masukkan username" required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan password" required>
        </div>

        <button type="submit" class="btn">Login Admin</button>

    </form>

</div>

</body>
</html>