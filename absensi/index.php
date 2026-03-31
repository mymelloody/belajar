<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['nim'])) { header("Location: login.php"); exit; }

$page = $_GET['page'] ?? 'dashboard';

// --- LOGIKA PHP ---
$res_kegiatan = mysqli_query($conn, "SELECT * FROM kegiatan ORDER BY tgl_kegiatan ASC");

$sql_total = "SELECT COUNT(*) as total FROM user WHERE role != 'admin'";
$total = mysqli_fetch_assoc(mysqli_query($conn, $sql_total))['total'];

$q_hadir = mysqli_query($conn, "SELECT COUNT(DISTINCT nim) as h FROM presensi_hima WHERE DATE(waktu_hadir) = CURDATE()");
$sudah_absen = mysqli_fetch_assoc($q_hadir)['h'];
$tidak_hadir = $total - $sudah_absen;
$persen_hadir = ($total > 0) ? round(($sudah_absen / $total) * 100) : 0;

$data_grafik = [];
for ($i = 6; $i >= 0; $i--) {
    $tgl = date('Y-m-d', strtotime("-$i days"));
    $hari = date('D', strtotime($tgl)); 
    $q = mysqli_query($conn, "SELECT COUNT(DISTINCT nim) as jml FROM presensi_hima WHERE DATE(waktu_hadir) = '$tgl'");
    $row = mysqli_fetch_assoc($q);
    $data_grafik[] = ['hari' => $hari, 'jumlah' => $row['jml']];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status'])) {
    $nim = $_SESSION['nim'];
    $status = $_POST['status'];
    $keterangan = $_POST['keterangan'];
    $nama_file = "";
    if (!empty($_FILES['bukti']['name'])) {
        $nama_file = time() . "_" . $_FILES['bukti']['name'];
        move_uploaded_file($_FILES['bukti']['tmp_name'], "uploads/" . $nama_file);
    }
    $query = "INSERT INTO presensi_hima (nim, waktu_hadir, status, keterangan, foto_bukti) VALUES ('$nim', NOW(), '$status', '$keterangan', '$nama_file')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Izin berhasil dikirim!'); window.location='index.php?page=absen';</script>";
    }
}

function hitungDivisi($conn, $nama_divisi) {
    $res = mysqli_query($conn, "SELECT COUNT(*) as t FROM user WHERE divisi = '$nama_divisi'");
    return mysqli_fetch_assoc($res)['t'];
}

$total_anggota = $total; 
$total_ADVOKESMA = hitungDivisi($conn, 'ADVOKESMA');
$total_Biro_Kesekretariatan = hitungDivisi($conn, 'Biro Kesekretariatan');
$total_Kaderisasi = hitungDivisi($conn, 'Kaderisasi');
$total_Minat_Bakat = hitungDivisi($conn, 'Minat Bakat');
$total_Keagamaan = hitungDivisi($conn, 'Keagamaan');
$total_Medkominfo = hitungDivisi($conn, 'Medkominfo');
$total_Kewirausahaan = hitungDivisi($conn, 'Kewirausahaan');
$total_HLK = hitungDivisi($conn, 'HLK');
$total_Sarana = hitungDivisi($conn, 'Sarana');
$total_Pristek = hitungDivisi($conn, 'Pristek');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Presensi HIMASI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        @media (max-width: 991px) {
            .sidebar { position: fixed !important; left: -100%; top: 0; width: 250px !important; height: 100vh !important; z-index: 9999; transition: 0.3s; }
            .sidebar.show { left: 0 !important; }
            .main-content { margin-left: 0 !important; width: 100% !important; padding-top: 60px; }
            .btn-toggle { display: block !important; }
        }
        .btn-toggle { display: none; position: fixed; top: 15px; right: 15px; z-index: 10000; background: #5eb5ee; color: white; border: none; padding: 10px; border-radius: 5px; }
        .card-stat { transition: transform 0.3s ease; border: none !important; }
        .card-stat:hover { transform: translateY(-5px); }
        .card-divisi { aspect-ratio: 16 / 9; display: flex; align-items: flex-end; background-size: cover; background-position: center; border: none; border-radius: 12px; overflow: hidden; position: relative; text-decoration: none !important; transition: 0.3s; }
        .card-divisi::before { content: ""; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); z-index: 1; }
        .card-divisi .card-body { position: relative; z-index: 2; color: white; padding: 10px; }
        .bg-semua { background-image: url('gmbr/inti.png'); }
        .bg-ADVOKESMA { background-image: url('gmbr/advokesma.png');}
        .bg-Biro-Kesekretariatan { background-image: url('gmbr/kesekre.png');}
        .bg-Kaderisasi { background-image: url('gmbr/kader.png');}
        .bg-Minat-Bakat { background-image: url('gmbr/minba.png');}
        .bg-Keagamaan { background-image: url('gmbr/agama.png');}
        .bg-Medkominfo { background-image: url('gmbr/medkom.png');}
        .bg-Kewirausahaan { background-image: url('gmbr/kwu.png');}
        .bg-HLK { background-image: url('gmbr/hlk.png');}
        .bg-Sarana { background-image: url('gmbr/sarana.png');}
        .bg-Pristek { background-image: url('gmbr/pristek.png');}
    </style>
</head>
<body> 

<button class="btn-toggle shadow" onclick="document.querySelector('.sidebar').classList.toggle('show')">
    <i class="bi bi-list"></i>
</button>

<div class="sidebar d-flex flex-column">
    <img src="gmbr/logosi.png" alt="Logo" width="130" class="mb-3 mx-auto d-block">
    <h3 class="fw-bold mx-auto d-block">PHIMA-SI</h3>
    <div class="nav flex-column mt-4 mx-auto d-block">
        <a href="index.php?page=dashboard" class="nav-link <?= $page=='dashboard'?'active':'' ?>"><i class="bi bi-house"></i> Dashboard</a>
        <?php if ($_SESSION['role'] == 'anggota'): ?>
            <a href="index.php?page=absen" class="nav-link <?= $page=='absen'?'active':'' ?>"><i class="bi bi-pencil"></i> Presensi</a>
            <a href="index.php?page=riwayat" class="nav-link <?= $page=='riwayat'?'active':'' ?>"><i class="bi bi-calendar-check"></i> Riwayat Saya</a>
        <?php endif; ?>
        <?php if ($_SESSION['role'] == 'admin'): ?>
            <a href="index.php?page=absen" class="nav-link <?= $page=='absen'?'active':'' ?>"><i class="bi bi-pencil"></i> Kelola Presensi</a>
            <a href="index.php?page=input_kegiatan" class="nav-link <?= $page=='input_kegiatan'?'active':'' ?>"><i class="bi bi-plus"></i> Input Kegiatan</a>
            <a href="index.php?page=laporan" class="nav-link <?= $page=='laporan'?'active':'' ?>"><i class="bi bi-clipboard-data"></i> Laporan</a>
        <?php endif; ?>
    </div>
    <a href="logout.php" class="nav-link text-danger mt-auto"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<div class="main-content">
    <div class="container-fluid pt-4">
        
        <?php if ($page == 'dashboard'): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm" style="border-radius: 16px; background:linear-gradient(135deg, #5eb5ee, #b8e1ff); overflow: hidden;">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-12 col-md-8">
                                    <h3 class="fw-bold mb-0" style="color: #1e293b; letter-spacing: -0.5px;">
                                        <?= ($_SESSION['role']=='admin') ? 'Selamat Datang Admin' : 'Hai, '.$_SESSION['nama'] ?>
                                    </h3>
                                    <div style="font-size: 0.8rem; color: #64748b; margin-top: 4px; line-height: 1.2;">
                                        Sistem Informasi Presensi HIMASI <br>
                                        <span style="font-weight: 500;">UIN Sultan Syarif Kasim Riau</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 text-md-end mt-3 mt-md-0">
                                    <div class="fw-semibold text-muted" style="font-size: 0.85rem;"><?= date('l, d F Y') ?></div>
                                    <div id="jam" class="fw-bold" style="font-size: 1.4rem; color: #0ea5e9;"></div>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div> 
            </div> 

<div style="display: flex; flex-wrap: wrap; gap: 20px;" class="mb-4">
    <?php while($k = mysqli_fetch_assoc($res_kegiatan)): 
        $tgl_acara = $k['tgl_kegiatan'];
        $tgl_skrg  = date('Y-m-d');
        $status    = ($tgl_acara > $tgl_skrg) ? 'Akan Datang' : (($tgl_acara == $tgl_skrg) ? 'Hari Ini' : 'Selesai');
        $warna     = ($status == 'Akan Datang') ? '#0d6efd' : (($status == 'Hari Ini') ? '#198754' : '#b8e1ff');
    ?>
        <div class="card-timbul" style="flex: 1 1 300px; background: white; border-radius: 20px; padding: 20px; transition: 0.3s; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-left: 6px solid <?= $warna ?>;">
            <span style="background: <?= $warna ?>20; color: <?= $warna ?>; padding: 4px 10px; border-radius: 50px; font-size: 10px; font-weight: 800;"><?= $status ?></span>
            <h6 style="margin: 15px 0 5px; font-weight: 800;"><?= $k['nama_kegiatan'] ?></h6>
            <div style="display: flex; justify-content: space-between; font-size: 11px; color: #666; margin-top: 15px; border-top: 1px solid #eee; pt-2;">
                <span><i class="bi bi-calendar"></i> <?= date('d/m/y', strtotime($k['tgl_kegiatan'])) ?></span>
                <span><i class="bi bi-clock"></i> <?= substr($k['jam_mulai'], 0, 5) ?> WIB</span>
            </div>
        </div>
    <?php endwhile; ?>
</div>

            <div class="row g-2 mb-4">
                <div class="col-6 col-md-4 col-lg-2"><a href="index.php?page=anggota" class="card card-divisi bg-semua shadow-sm"><div class="card-body"><h6>Semua</h6><span><?= $total_anggota; ?> Anggota</span></div></a></div>
                <div class="col-6 col-md-4 col-lg-2"><a href="#" class="card card-divisi bg-ADVOKESMA shadow-sm"><div class="card-body"><h6>ADVOKESMA</h6><span><?= $total_ADVOKESMA; ?></span></div></a></div>
                <div class="col-6 col-md-4 col-lg-2"><a href="#" class="card card-divisi bg-Biro-Kesekretariatan shadow-sm"><div class="card-body"><h6>Biro Kesekre</h6><span><?= $total_Biro_Kesekretariatan; ?></span></div></a></div>
                <div class="col-6 col-md-4 col-lg-2"><a href="#" class="card card-divisi bg-Kaderisasi shadow-sm"><div class="card-body"><h6>Kaderisasi</h6><span><?= $total_Kaderisasi; ?></span></div></a></div>
                <div class="col-6 col-md-4 col-lg-2"><a href="#" class="card card-divisi bg-Minat-Bakat shadow-sm"><div class="card-body"><h6>Minat Bakat</h6><span><?= $total_Minat_Bakat; ?></span></div></a></div>
                <div class="col-6 col-md-4 col-lg-2"><a href="#" class="card card-divisi bg-Pristek shadow-sm"><div class="card-body"><h6>Pristek</h6><span><?= $total_Pristek; ?></span></div></a></div>
            </div>

            <div class="row g-3 mb-4 text-center">
                <div class="col-6 col-md-3"><div class="card card-stat text-white p-3 shadow-sm border-0" style="background: linear-gradient(135deg, #5eb5ee, #b8e1ff);"><h5 class="small opacity-75">Total</h5><h2 class="fw-bold mb-0"><?= $total_anggota ?></h2></div></div>
                <div class="col-6 col-md-3"><div class="card card-stat text-white p-3 shadow-sm border-0" style="background: linear-gradient(135deg, #5eb5ee, #b8e1ff);"><h5 class="small opacity-75">Hadir</h5><h2 class="fw-bold mb-0"><?= $sudah_absen ?></h2></div></div>
                <div class="col-6 col-md-3"><div class="card card-stat text-white p-3 shadow-sm border-0" style="background: linear-gradient(135deg, #5eb5ee, #b8e1ff);"><h5 class="small opacity-75">Absen</h5><h2 class="fw-bold mb-0"><?= $tidak_hadir ?></h2></div></div>
                <div class="col-6 col-md-3"><div class="card card-stat text-white p-3 shadow-sm border-0" style="background: linear-gradient(135deg, #5eb5ee, #b8e1ff);"><h5 class="small opacity-75">Persentase</h5><h2 class="fw-bold mb-0"><?= $persen_hadir ?>%</h2></div></div>
            </div>

            <div class="card shadow-sm border-0 p-4 mb-4" style="border-radius: 16px;">
                <h5 class="fw-bold mb-4"><i class="bi bi-bar-chart-fill text-primary"></i> Statistik Kehadiran</h5>
                <div class="d-flex align-items-end justify-content-between text-center" style="height: 150px; border-bottom: 2px solid #eee;">
                    <?php foreach ($data_grafik as $d): $tinggi = ($total > 0) ? ($d['jumlah'] / $total) * 100 : 0; ?>
                        <div class="flex-grow-1 mx-1">
                            <div class="bg-primary rounded-top" style="height: <?= $tinggi ?>%; min-height: 2px;"></div>
                            <div class="mt-2 small fw-bold text-uppercase" style="font-size: 9px;"><?= $d['hari'] ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php 
        elseif ($page == 'absen'): include 'absen.php'; 
        elseif ($page == 'edit'): include 'edit.php';
        elseif ($page == 'izin'): include 'izin.php';
        elseif ($page == 'konfirmasi_hadir'): include 'konfirmasi_hadir.php';
        elseif ($page == 'konfirmasi_izin'): include 'konfirmasi_izin.php';
        elseif ($page == 'riwayat'): include 'riwayat.php';
        elseif ($page == 'laporan'): include 'laporan.php';
        elseif ($page == 'anggota'): include 'anggota.php';
        elseif ($page == 'input_kegiatan'): include 'input_kegiatan.php';
        endif; 
        ?>
    </div> 
</div>

<script>
function tampilJam() {
    const n = new Date();
    document.getElementById('jam').innerText = ` ${n.getHours().toString().padStart(2,'0')}:${n.getMinutes().toString().padStart(2,'0')}:${n.getSeconds().toString().padStart(2,'0')}`;
}
setInterval(tampilJam, 1000); tampilJam();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>