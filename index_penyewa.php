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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CariKos.Ku - Cari Kos</title>

    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">

        <h1>CariKos.Ku</h1>

        <div class="menu">
            <a href="">Beranda</a>
            <a href="">Favorit</a>
            <a href="">Riwayat</a>
            <a href="">Profil</a>
            <a href="logout.php">Logout</a>
        </div>

    </div>

    <!-- Hero -->
    <div class="hero">

        <h2>Temukan Kos Terbaik Untukmu</h2>

        <p>
            Cari kos nyaman, aman, dan dekat kampus dengan mudah.
        </p>

        <!-- Search -->
        <div class="search-box">

            <div class="search-row">

                <input 
                    type="text"
                    placeholder="Cari lokasi atau nama kos..."
                >

                <select>
                    <option>Kampus</option>
                    <option>UNRAM</option>
                    <option>UIN Mataram</option>
                    <option>UNDIKMA</option>
                </select>

                <select>
                    <option>Harga</option>
                    <option>< 500rb</option>
                    <option>500rb - 1jt</option>
                    <option>> 1jt</option>
                </select>

                <select>
                    <option>Gender Kos</option>
                    <option>Putra</option>
                    <option>Putri</option>
                    <option>Campur</option>
                </select>

                <select>
                    <option>Fasilitas</option>
                    <option>WiFi</option>
                    <option>AC</option>
                    <option>Kamar Mandi Dalam</option>
                </select>

            </div>

            <button class="search-btn">
                Cari Sekarang
            </button>

        </div>

    </div>

    <!-- Listing -->
    <h2 class="section-title">
        Rekomendasi Kos
    </h2>

    <div class="listing-container">

        <!-- CARD 1 -->
        <div class="card">

            <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?q=80&w=1200&auto=format&fit=crop">

            <div class="card-content">

                <div class="badge-group">

                    <div class="badge popular">
                        Terpopuler
                    </div>

                    <div class="badge verified">
                        Terverifikasi
                    </div>

                </div>

                <h3>Kos Putri Melati</h3>

                <div class="price">
                    Rp850.000 / bulan
                </div>

                <div class="status">
                    Tersedia
                </div>

                <div class="info">
                    📍 500m dari Universitas Mataram
                </div>

                <div class="info">
                    🛏 WiFi • AC • Kamar Mandi Dalam
                </div>

                <div class="info">
                    🕒 Jam malam 22.00
                </div>

                <div class="info">
                    🛒 Dekat minimarket & laundry
                </div>

                <div class="info">
                    👩 Kos Putri
                </div>

                <div class="info">
                    📞 0812-3456-7890
                </div>

                <div class="rating">
                    ⭐ 4.9 dari penghuni terverifikasi
                </div>

                <div class="button-group">

                    <button class="btn-detail">
                        Lihat Detail
                    </button>

                    <button class="btn-report">
                        Laporkan
                    </button>

                </div>

            </div>

        </div>

        <!-- CARD 2 -->
        <div class="card">

            <img src="https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?q=80&w=1200&auto=format&fit=crop">

            <div class="card-content">

                <div class="badge-group">

                    <div class="badge cheap">
                        Termurah
                    </div>

                    <div class="badge new">
                        Baru
                    </div>

                </div>

                <h3>Kos Putra Harmoni</h3>

                <div class="price">
                    Rp550.000 / bulan
                </div>

                <div class="status status-warning">
                    Hampir Penuh
                </div>

                <div class="info">
                    📍 Dekat UIN Mataram
                </div>

                <div class="info">
                    🛏 WiFi • Lemari • Parkiran
                </div>

                <div class="info">
                    🕒 Bebas jam malam
                </div>

                <div class="info">
                    🍜 Dekat warung makan
                </div>

                <div class="info">
                    👨 Kos Putra
                </div>

                <div class="info">
                    📞 0812-1111-2222
                </div>

                <div class="rating">
                    ⭐ 4.7 dari penghuni terverifikasi
                </div>

                <div class="button-group">

                    <button class="btn-detail">
                        Lihat Detail
                    </button>

                    <button class="btn-report">
                        Laporkan
                    </button>

                </div>

            </div>

        </div>

    </div>

</body>
</html>