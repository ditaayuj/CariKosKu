<?php
session_start();

if(!isset($_SESSION['role'])){
    header("Location: login_admin.php");
    exit;
}

if($_SESSION['role'] != 'admin'){
    header("Location: login_admin.php");
    exit;
}

require "koneksi.php";

$id = $_GET['id'];

mysqli_query($conn, "UPDATE kos SET terverifikasi=1 WHERE id='$id'");

header("Location: index_admin.php");
exit;
?>