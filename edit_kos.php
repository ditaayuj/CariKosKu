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
$id      = $_GET['id'];
$error   = "";
$success = "";

$kos = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM kos WHERE id='$id' AND user_id='$user_id'"));

if(!$kos){
    header("Location: index_pemilik.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $nama_kos        = $_POST['nama_kos'];
    $alamat          = $_POST['alamat'];
    $kampus_terdekat = $_POST['kampus_terdekat'];
    $harga           = $_POST['harga'];
    $gender          = $_POST['gender'];
    $fasilitas       = $_POST['fasilitas'];
    $jam_malam       = $_POST['jam_malam'];
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

        <h2 class="form-title" style="text-align:left; margin-bottom:8px;">Edit Kos</h2>
        <p style="color:#6B7280; margin-bottom:30px; font-size:14px;">Perbarui informasi kos Anda.</p>

        <?php if($error): ?>
            <p style="color:red; margin-bottom:15px;"><?= $error ?></p>
        <?php endif; ?>

        <?php if($success): ?>
            <p style="color:green; margin-bottom:15px;"><?= $success ?></p>
        <?php endif; ?>

        <form action="" method="POST" id="formEditKos">

            <div class="form-section-title">Informasi Dasar</div>

            <div class="input-group">
                <label>Nama Kos</label>
                <input type="text" name="nama_kos" id="nama_kos" value="<?= $kos['nama_kos'] ?>" required>
            </div>

            <div class="input-group">
                <label>Alamat Lengkap</label>
                <input type="text" name="alamat" id="alamat" value="<?= $kos['alamat'] ?>" required>
            </div>

            <div class="input-group">
                <label>Kampus Terdekat</label>
                <select name="kampus_terdekat" id="kampus_terdekat" required>
                    <option value="Universitas Mataram (UNRAM)" <?= $kos['kampus_terdekat'] == 'Universitas Mataram (UNRAM)' ? 'selected' : '' ?>>Universitas Mataram (UNRAM)</option>
                    <option value="UIN Mataram"                 <?= $kos['kampus_terdekat'] == 'UIN Mataram'                 ? 'selected' : '' ?>>UIN Mataram</option>
                    <option value="UNDIKMA"                     <?= $kos['kampus_terdekat'] == 'UNDIKMA'                     ? 'selected' : '' ?>>UNDIKMA</option>
                    <option value="Universitas Nahdlatul Wathan"<?= $kos['kampus_terdekat'] == 'Universitas Nahdlatul Wathan'? 'selected' : '' ?>>Universitas Nahdlatul Wathan</option>
                    <option value="Lainnya"                     <?= $kos['kampus_terdekat'] == 'Lainnya'                     ? 'selected' : '' ?>>Lainnya</option>
                </select>
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
                <input type="text" name="fasilitas" id="fasilitas" value="<?= $kos['fasilitas'] ?>" required>
            </div>

            <div class="input-group">
                <label>Jam Malam <span style="color:#9CA3AF; font-weight:400;">(kosongkan jika bebas)</span></label>
                <input type="text" name="jam_malam" value="<?= $kos['jam_malam'] ?>">
            </div>

            <div class="input-group">
                <label>Status Ketersediaan</label>
                <select name="status" required>
                    <option value="tersedia"    <?= $kos['status'] == 'tersedia'    ? 'selected' : '' ?>>Tersedia</option>
                    <option value="hampir_penuh"<?= $kos['status'] == 'hampir_penuh'? 'selected' : '' ?>>Hampir Penuh</option>
                    <option value="penuh"       <?= $kos['status'] == 'penuh'       ? 'selected' : '' ?>>Penuh</option>
                </select>
            </div>

            <div class="form-section-title">Lokasi di Peta (Opsional)</div>

            <div class="input-group">
                <label>Latitude <span style="color:#9CA3AF; font-weight:400;">(contoh: -8.5833)</span></label>
                <input type="text" name="lat" id="lat" value="<?= $kos['lat'] ?>" placeholder="-8.5833">
            </div>

            <div class="input-group">
                <label>Longitude <span style="color:#9CA3AF; font-weight:400;">(contoh: 116.1167)</span></label>
                <input type="text" name="lng" id="lng" value="<?= $kos['lng'] ?>" placeholder="116.1167">
            </div>

            <div style="display:flex; gap:15px; margin-top:10px;">
                <button type="submit" class="btn" style="flex:1;">Simpan Perubahan</button>
                <a href="index_pemilik.php" style="flex:1;">
                    <button type="button" class="btn" style="background:#9CA3AF; width:100%;">Batal</button>
                </a>
            </div>

        </form>

    </div>
</div>

<script>
document.getElementById('formEditKos').addEventListener('submit', function(e){
    var nama     = document.getElementById('nama_kos').value.trim();
    var alamat   = document.getElementById('alamat').value.trim();
    var kampus   = document.getElementById('kampus_terdekat').value;
    var harga    = parseInt(document.getElementById('harga').value);
    var gender   = document.getElementById('gender').value;
    var fasilitas = document.getElementById('fasilitas').value.trim();
    var lat      = document.getElementById('lat').value.trim();
    var lng      = document.getElementById('lng').value.trim();

    if(nama.length < 3){
        e.preventDefault();
        alert('Nama kos minimal 3 karakter.');
        return;
    }

    if(alamat.length < 10){
        e.preventDefault();
        alert('Alamat terlalu pendek, isi alamat lengkap.');
        return;
    }

    if(kampus === ''){
        e.preventDefault();
        alert('Pilih kampus terdekat.');
        return;
    }

    if(isNaN(harga) || harga <= 0){
        e.preventDefault();
        alert('Harga harus lebih dari 0.');
        return;
    }

    if(gender === ''){
        e.preventDefault();
        alert('Pilih gender kos.');
        return;
    }

    if(fasilitas.length < 3){
        e.preventDefault();
        alert('Fasilitas tidak boleh kosong.');
        return;
    }

    if(lat !== '' && isNaN(parseFloat(lat))){
        e.preventDefault();
        alert('Format latitude tidak valid. Contoh: -8.5833');
        return;
    }

    if(lng !== '' && isNaN(parseFloat(lng))){
        e.preventDefault();
        alert('Format longitude tidak valid. Contoh: 116.1167');
        return;
    }
});
</script>

</body>
</html>