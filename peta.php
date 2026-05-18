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

$kampus = isset($_GET['kampus']) ? mysqli_real_escape_string($conn, $_GET['kampus']) : '';

$where = "WHERE k.terverifikasi = 1 AND k.lat IS NOT NULL AND k.lng IS NOT NULL";
if($kampus) $where .= " AND k.kampus_terdekat = '$kampus'";

$kos_list = mysqli_query($conn, "SELECT k.*, u.nama AS nama_pemilik FROM kos k JOIN users u ON k.user_id = u.id $where");

$kos_json = [];
while($k = mysqli_fetch_assoc($kos_list)){
    $kos_json[] = [
        'id'       => $k['id'],
        'nama'     => $k['nama_kos'],
        'alamat'   => $k['alamat'],
        'kampus'   => $k['kampus_terdekat'],
        'harga'    => number_format($k['harga'], 0, ',', '.'),
        'gender'   => ucfirst($k['gender']),
        'status'   => $k['status'],
        'rating'   => $k['rating'],
        'lat'      => (float)$k['lat'],
        'lng'      => (float)$k['lng'],
        'pemilik'  => $k['nama_pemilik'],
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Kos - CariKos.Ku</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        #map-full { height: calc(100vh - 70px); width: 100%; }
        .peta-filter {
            position: absolute;
            top: 86px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            background: white;
            border-radius: 16px;
            padding: 14px 20px;
            display: flex;
            gap: 12px;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        .peta-filter select {
            padding: 10px 14px;
            border: 1px solid #D1D5DB;
            border-radius: 10px;
            outline: none;
            font-size: 14px;
        }
        .peta-filter button {
            padding: 10px 20px;
            background: #F0A629;
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
        }
        .peta-filter a button {
            background: #157A6E;
        }
    </style>
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

<form action="" method="GET" class="peta-filter">
    <span style="font-weight:600; color:#157A6E;">Filter Kampus:</span>
    <select name="kampus">
        <option value="">Semua Kampus</option>
        <option value="Universitas Mataram (UNRAM)"  <?= $kampus == 'Universitas Mataram (UNRAM)'  ? 'selected' : '' ?>>UNRAM</option>
        <option value="UIN Mataram"                  <?= $kampus == 'UIN Mataram'                  ? 'selected' : '' ?>>UIN Mataram</option>
        <option value="UNDIKMA"                      <?= $kampus == 'UNDIKMA'                      ? 'selected' : '' ?>>UNDIKMA</option>
        <option value="Universitas Nahdlatul Wathan" <?= $kampus == 'Universitas Nahdlatul Wathan' ? 'selected' : '' ?>>Univ. NW</option>
    </select>
    <button type="submit">Tampilkan</button>
    <a href="index_penyewa.php"><button type="button">← Kembali</button></a>
</form>

<div id="map-full"></div>

<script>
var map = L.map('map-full').setView([-8.5833, 116.1167], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

var kosData = <?= json_encode($kos_json) ?>;

var statusColor = { tersedia: '#22C55E', hampir_penuh: '#F0A629', penuh: '#EF4444' };

kosData.forEach(function(k){
    var color = statusColor[k.status] || '#157A6E';
    var icon = L.divIcon({
        className: '',
        html: '<div style="background:'+color+'; color:white; padding:6px 10px; border-radius:999px; font-size:12px; font-weight:600; white-space:nowrap; box-shadow:0 2px 8px rgba(0,0,0,0.2);">🏠 '+k.nama+'</div>',
        iconAnchor: [0, 10]
    });

    L.marker([k.lat, k.lng], {icon: icon})
        .addTo(map)
        .bindPopup(
            '<div style="min-width:200px;">' +
            '<b style="font-size:15px;">'+k.nama+'</b><br>' +
            '<span style="color:#157A6E; font-weight:700;">Rp'+k.harga+'/bln</span><br>' +
            '<span style="color:#6B7280; font-size:13px;">📍 '+k.alamat+'</span><br>' +
            '<span style="color:#6B7280; font-size:13px;">🏫 '+k.kampus+'</span><br>' +
            '<span style="color:#6B7280; font-size:13px;">'+k.gender+' | ⭐ '+k.rating+'</span><br><br>' +
            '<a href="detail_kos.php?id='+k.id+'" style="background:#F0A629; color:white; padding:7px 14px; border-radius:8px; text-decoration:none; font-weight:600; font-size:13px;">Lihat Detail</a>' +
            '</div>'
        );
});

<?php if(count($kos_json) > 0): ?>
var bounds = L.latLngBounds(kosData.map(k => [k.lat, k.lng]));
map.fitBounds(bounds, {padding: [50, 50]});
<?php endif; ?>
</script>

</body>
</html>