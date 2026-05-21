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
$id      = (int)$_GET['id'];
$error   = "";
$success = "";

$kos = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM kos WHERE id='$id' AND user_id='$user_id'"));

if(!$kos){
    header("Location: index_pemilik.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $nama_kos        = mysqli_real_escape_string($conn, $_POST['nama_kos']);
    $alamat          = mysqli_real_escape_string($conn, $_POST['alamat']);
    $kampus_terdekat = mysqli_real_escape_string($conn, $_POST['kampus_terdekat']);
    $harga           = (int)$_POST['harga'];
    $gender          = $_POST['gender'];
    $fasilitas       = mysqli_real_escape_string($conn, $_POST['fasilitas']);
    $jam_malam       = mysqli_real_escape_string($conn, $_POST['jam_malam']);
    $status          = $_POST['status'];

    $lat = !empty($_POST['lat']) ? (float)$_POST['lat'] : null;
    $lng = !empty($_POST['lng']) ? (float)$_POST['lng'] : null;
    $lat_val = $lat !== null ? "'$lat'" : "NULL";
    $lng_val = $lng !== null ? "'$lng'" : "NULL";

    $query = "UPDATE kos SET nama_kos='$nama_kos', alamat='$alamat', kampus_terdekat='$kampus_terdekat', harga='$harga', gender='$gender', fasilitas='$fasilitas', jam_malam='$jam_malam', status='$status', lat=$lat_val, lng=$lng_val WHERE id='$id' AND user_id='$user_id'";

    if(mysqli_query($conn, $query)){
        $success = "Kos berhasil diperbarui!";
        $kos = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM kos WHERE id='$id'"));
    } else {
        $error = "Gagal memperbarui kos. Coba lagi.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kos - CariKos.Ku</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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

        <h2 class="form-card-title">Edit Kos</h2>
        <p class="form-card-subtitle">Perbarui informasi kos Anda.</p>

        <?php if($error): ?><div class="alert-error"><?= $error ?></div><?php endif; ?>
        <?php if($success): ?><div class="alert-success"><?= $success ?></div><?php endif; ?>

        <form action="" method="POST" id="formEditKos">

            <div class="form-section-title">Informasi Dasar</div>

            <div class="input-group">
                <label>Nama Kos</label>
                <input type="text" name="nama_kos" id="nama_kos" value="<?= htmlspecialchars($kos['nama_kos']) ?>" required>
            </div>

            <div class="input-group">
                <label>Alamat Lengkap</label>
                <input type="text" name="alamat" id="alamat" value="<?= htmlspecialchars($kos['alamat']) ?>" required>
            </div>

            <div class="input-group">
                <label>Dekat Lokasi / Kampus</label>
                <input type="text" name="kampus_terdekat" id="kampus_terdekat"
                    value="<?= htmlspecialchars($kos['kampus_terdekat']) ?>"
                    placeholder="Contoh: Universitas Mataram, UIN Mataram, Epicentrum..."
                    list="lokasi_list" required>
                <datalist id="lokasi_list">
                    <option value="Universitas Mataram (UNRAM)">
                    <option value="UIN Mataram">
                    <option value="UNDIKMA">
                    <option value="Universitas Nahdlatul Wathan (UNW)">
                    <option value="Epicentrum Mall">
                    <option value="Mataram Mall">
                    <option value="RS Provinsi NTB">
                    <option value="Lombok Plaza">
                </datalist>
            </div>

            <div class="form-section-title">Harga & Tipe</div>

            <div class="input-group">
                <label>Harga per Bulan (Rp)</label>
                <input type="number" name="harga" id="harga" value="<?= $kos['harga'] ?>" required>
            </div>

            <div class="input-group">
                <label>Gender Kos</label>
                <select name="gender" id="gender" required>
                    <option value="putra"  <?= $kos['gender'] == 'putra'  ? 'selected' : '' ?>>Putra</option>
                    <option value="putri"  <?= $kos['gender'] == 'putri'  ? 'selected' : '' ?>>Putri</option>
                    <option value="campur" <?= $kos['gender'] == 'campur' ? 'selected' : '' ?>>Campur</option>
                </select>
            </div>

            <div class="form-section-title">Fasilitas & Aturan</div>

            <div class="input-group">
                <label>Fasilitas</label>
                <input type="text" name="fasilitas" id="fasilitas" value="<?= htmlspecialchars($kos['fasilitas']) ?>" required>
            </div>

            <div class="input-group">
                <label>Jam Malam <span class="input-hint">(kosongkan jika bebas)</span></label>
                <input type="text" name="jam_malam" value="<?= htmlspecialchars($kos['jam_malam']) ?>">
            </div>

            <div class="input-group">
                <label>Status Ketersediaan</label>
                <select name="status" required>
                    <option value="tersedia"     <?= $kos['status'] == 'tersedia'     ? 'selected' : '' ?>>Tersedia</option>
                    <option value="hampir_penuh" <?= $kos['status'] == 'hampir_penuh' ? 'selected' : '' ?>>Hampir Penuh</option>
                    <option value="penuh"        <?= $kos['status'] == 'penuh'        ? 'selected' : '' ?>>Penuh</option>
                </select>
            </div>

            <div class="form-section-title">Lokasi di Peta (Opsional)</div>

            <p class="map-hint">Klik pada peta untuk menentukan lokasi kos. Geser marker untuk menyesuaikan posisi.</p>
            <div id="map-picker" class="map-picker"></div>

            <div class="map-coords">
                <div class="input-group">
                    <label>Latitude <span class="input-hint">(otomatis terisi)</span></label>
                    <input type="text" name="lat" id="lat" value="<?= $kos['lat'] ?>" placeholder="Klik peta di atas" class="input-readonly" readonly>
                </div>
                <div class="input-group">
                    <label>Longitude <span class="input-hint">(otomatis terisi)</span></label>
                    <input type="text" name="lng" id="lng" value="<?= $kos['lng'] ?>" placeholder="Klik peta di atas" class="input-readonly" readonly>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">Simpan Perubahan</button>
                <a href="index_pemilik.php">
                    <button type="button" class="btn btn-gray">Batal</button>
                </a>
            </div>

        </form>

    </div>
</div>

<script>
var defaultLat = <?= $kos['lat'] ? $kos['lat'] : -8.5833 ?>;
var defaultLng = <?= $kos['lng'] ? $kos['lng'] : 116.1167 ?>;
var hasCoord   = <?= ($kos['lat'] && $kos['lng']) ? 'true' : 'false' ?>;

var map = L.map('map-picker').setView([defaultLat, defaultLng], 15);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);

var marker = hasCoord ? L.marker([defaultLat, defaultLng], {draggable: true}).addTo(map) : null;

if(marker){
    marker.on('dragend', function(){
        var pos = marker.getLatLng();
        document.getElementById('lat').value = pos.lat.toFixed(7);
        document.getElementById('lng').value = pos.lng.toFixed(7);
    });
}

map.on('click', function(e){
    document.getElementById('lat').value = e.latlng.lat.toFixed(7);
    document.getElementById('lng').value = e.latlng.lng.toFixed(7);
    if(marker){ marker.setLatLng(e.latlng); }
    else {
        marker = L.marker(e.latlng, {draggable: true}).addTo(map);
        marker.on('dragend', function(){
            var pos = marker.getLatLng();
            document.getElementById('lat').value = pos.lat.toFixed(7);
            document.getElementById('lng').value = pos.lng.toFixed(7);
        });
    }
});

document.getElementById('formEditKos').addEventListener('submit', function(e){
    var nama      = document.getElementById('nama_kos').value.trim();
    var alamat    = document.getElementById('alamat').value.trim();
    var kampus    = document.getElementById('kampus_terdekat').value.trim();
    var harga     = parseInt(document.getElementById('harga').value);
    var gender    = document.getElementById('gender').value;
    var fasilitas = document.getElementById('fasilitas').value.trim();

    if(nama.length < 3){ e.preventDefault(); alert('Nama kos minimal 3 karakter.'); return; }
    if(alamat.length < 10){ e.preventDefault(); alert('Alamat terlalu pendek.'); return; }
    if(kampus === ''){ e.preventDefault(); alert('Isi lokasi/kampus terdekat.'); return; }
    if(isNaN(harga) || harga <= 0){ e.preventDefault(); alert('Harga harus lebih dari 0.'); return; }
    if(gender === ''){ e.preventDefault(); alert('Pilih gender kos.'); return; }
    if(fasilitas.length < 3){ e.preventDefault(); alert('Fasilitas tidak boleh kosong.'); return; }
});
</script>

</body>
</html>