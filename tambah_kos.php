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
$error   = "";
$success = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $nama_kos        = mysqli_real_escape_string($conn, $_POST['nama_kos']);
    $alamat          = mysqli_real_escape_string($conn, $_POST['alamat']);
    $kampus_terdekat = mysqli_real_escape_string($conn, $_POST['kampus_terdekat']);
    $harga           = (int)$_POST['harga'];
    $gender          = $_POST['gender'];
    $fasilitas       = mysqli_real_escape_string($conn, $_POST['fasilitas']);
    $jam_malam       = mysqli_real_escape_string($conn, $_POST['jam_malam']);
    $status          = $_POST['status'];
    $dokumen_kepemilikan = "";

    if(isset($_FILES['dokumen_kepemilikan']) && $_FILES['dokumen_kepemilikan']['error'] == 0){

        $ext = strtolower(pathinfo(
            $_FILES['dokumen_kepemilikan']['name'],
            PATHINFO_EXTENSION
        ));

        $dokumen_kepemilikan =
            'dokumen_' .
            time() .
            '_' .
            rand(1000,9999) .
            '.' .
            $ext;

        move_uploaded_file(
            $_FILES['dokumen_kepemilikan']['tmp_name'],
            'uploads/' . $dokumen_kepemilikan
        );
}

    $lat = !empty($_POST['lat']) ? (float)$_POST['lat'] : null;
    $lng = !empty($_POST['lng']) ? (float)$_POST['lng'] : null;
    $lat_val = $lat !== null ? "'$lat'" : "NULL";
    $lng_val = $lng !== null ? "'$lng'" : "NULL";

    $query = "INSERT INTO kos (user_id, nama_kos, alamat, kampus_terdekat, harga, gender, fasilitas, jam_malam, status, dokumen_kepemilikan, terverifikasi, rating, lat, lng) VALUES ('$user_id', '$nama_kos', '$alamat', '$kampus_terdekat', '$harga', '$gender', '$fasilitas', '$jam_malam', '$status', '$dokumen_kepemilikan', 0, 0, $lat_val, $lng_val)";

    if(mysqli_query($conn, $query)){
        $kos_id = mysqli_insert_id($conn);

        if(!empty($_FILES['foto']['name'][0])){
            $allowed = ['image/jpeg', 'image/png'];
            $count   = min(count($_FILES['foto']['name']), 5);
            $first   = true;
            for($i = 0; $i < $count; $i++){
                if($_FILES['foto']['error'][$i] == 0 && in_array($_FILES['foto']['type'][$i], $allowed)){
                    $ext      = pathinfo($_FILES['foto']['name'][$i], PATHINFO_EXTENSION);
                    $filename = 'kos_' . $kos_id . '_' . time() . '_' . $i . '.' . $ext;
                    if(move_uploaded_file($_FILES['foto']['tmp_name'][$i], 'uploads/' . $filename)){
                        $is_primary = $first ? 1 : 0;
                        mysqli_query($conn, "INSERT INTO kos_foto (kos_id, nama_file, is_primary) VALUES ('$kos_id', '$filename', '$is_primary')");
                        $first = false;
                    }
                }
            }
        }
        $success = "Kos berhasil ditambahkan! Menunggu verifikasi admin.";
    } else {
        $error = "Gagal menambahkan kos. Coba lagi.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kos - CariKos.Ku</title>
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

        <h2 class="form-card-title">Tambah Kos Baru</h2>
        <p class="form-card-subtitle">Isi informasi kos Anda. Listing akan ditinjau admin sebelum aktif.</p>

        <?php if($error): ?><div class="alert-error"><?= $error ?></div><?php endif; ?>
        <?php if($success): ?><div class="alert-success"><?= $success ?></div><?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data" id="formTambahKos">

            <div class="form-section-title">Informasi Dasar</div>

            <div class="input-group">
                <label>Nama Kos</label>
                <input type="text" name="nama_kos" id="nama_kos" placeholder="Contoh: Kos Putri Melati" required>
            </div>

            <div class="input-group">
                <label>Alamat Lengkap</label>
                <input type="text" name="alamat" id="alamat" placeholder="Jl. Contoh No. 1, Kelurahan, Kota" required>
            </div>

            <div class="input-group">
                <label>Dekat Lokasi / Kampus</label>
                <input type="text" name="kampus_terdekat" id="kampus_terdekat"
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
                <input type="number" name="harga" id="harga" placeholder="Contoh: 800000" required>
            </div>

            <div class="input-group">
                <label>Gender Kos</label>
                <select name="gender" id="gender" required>
                    <option value="">-- Pilih Gender --</option>
                    <option value="putra">Putra</option>
                    <option value="putri">Putri</option>
                    <option value="campur">Campur</option>
                </select>
            </div>

            <div class="form-section-title">Fasilitas & Aturan</div>

            <div class="input-group">
                <label>Fasilitas</label>
                <input type="text" name="fasilitas" id="fasilitas" placeholder="Contoh: WiFi, AC, Kamar Mandi Dalam, Parkir" required>
            </div>

            <div class="input-group">
                <label>Jam Malam <span class="input-hint">(kosongkan jika bebas)</span></label>
                <input type="text" name="jam_malam" placeholder="Contoh: 22.00">
            </div>

            <div class="input-group">
                <label>Status Ketersediaan</label>
                <select name="status" required>
                    <option value="tersedia">Tersedia</option>
                    <option value="hampir_penuh">Hampir Penuh</option>
                    <option value="penuh">Penuh</option>
                </select>
            </div>

            <div class="form-section-title">Verifikasi Kepemilikan</div>

            <div class="input-group">
                <label>Dokumen Kepemilikan Kos / Identitas Pemilik</label>
                <input
                    type="file"
                    name="dokumen_kepemilikan"
                    accept=".jpg,.jpeg,.png,.pdf"
                    required
                >
                <small style="color:#6B7280;">
                    Dokumen ini hanya dapat dilihat admin untuk proses verifikasi dan tidak ditampilkan kepada penyewa.
                </small>
            </div>

            <div class="form-section-title">Foto Kos</div>

            <div class="input-group">
                <label>Upload Foto Kos <span class="input-hint">(maks. 5 foto, format JPG/PNG)</span></label>
                <input type="file" name="foto[]" id="foto" multiple accept="image/jpeg,image/png" class="upload-input">
                <div id="preview-foto" class="foto-preview"></div>
            </div>

            <div class="form-section-title">Lokasi di Peta (Opsional)</div>

            <p class="map-hint">Klik pada peta untuk menentukan lokasi kos. Geser marker untuk menyesuaikan posisi.</p>
            <div id="map-picker" class="map-picker"></div>

            <div class="map-coords">
                <div class="input-group">
                    <label>Latitude <span class="input-hint">(otomatis terisi)</span></label>
                    <input type="text" name="lat" id="lat" placeholder="Klik peta di atas" class="input-readonly" readonly>
                </div>
                <div class="input-group">
                    <label>Longitude <span class="input-hint">(otomatis terisi)</span></label>
                    <input type="text" name="lng" id="lng" placeholder="Klik peta di atas" class="input-readonly" readonly>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">Tambah Kos</button>
                <a href="index_pemilik.php">
                    <button type="button" class="btn btn-gray">Batal</button>
                </a>
            </div>

        </form>

    </div>
</div>

<script>
var map = L.map('map-picker').setView([-8.5833, 116.1167], 14);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);
var marker = null;

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

document.getElementById('foto').addEventListener('change', function(){
    var preview = document.getElementById('preview-foto');
    preview.innerHTML = '';
    if(this.files.length > 5){ alert('Maksimal 5 foto.'); this.value = ''; return; }
    for(var i = 0; i < this.files.length; i++){
        var reader = new FileReader();
        reader.onload = (function(){ return function(e){
            var img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'foto-preview-item';
            preview.appendChild(img);
        }; })();
        reader.readAsDataURL(this.files[i]);
    }
});

document.getElementById('formTambahKos').addEventListener('submit', function(e){
    var nama     = document.getElementById('nama_kos').value.trim();
    var alamat   = document.getElementById('alamat').value.trim();
    var kampus   = document.getElementById('kampus_terdekat').value.trim();
    var harga    = parseInt(document.getElementById('harga').value);
    var gender   = document.getElementById('gender').value;
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