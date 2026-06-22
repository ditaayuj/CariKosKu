<?php
session_start();

if(!isset($_SESSION['role'])){
    header("Location: index.php");
    exit;
}

require "koneksi.php";

$id   = (int)$_GET['id'];
$role = $_SESSION['role'];

if($role == 'pemilik'){
    $user_id = $_SESSION['id'];
    mysqli_query($conn, "DELETE FROM kos WHERE id='$id' AND user_id='$user_id'");
    header("Location: index_pemilik.php");
} elseif($role == 'admin'){
    mysqli_query($conn, "DELETE FROM kos WHERE id='$id'");
    header("Location: index_admin.php");
} else {
    header("Location: index.php");
}

exit;
?>