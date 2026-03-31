<?php
include 'koneksi.php';

$sql_rekap = "SELECT u.nama, u.nim, u.divisi,
             SUM(CASE WHEN p.status = 'Hadir' THEN 1 ELSE 0 END) as jml_hadir,
             SUM(CASE WHEN p.status = 'Izin' THEN 1 ELSE 0 END) as jml_izin,
             (20 - SUM(CASE WHEN p.status = 'Hadir' THEN 1 ELSE 0 END) - SUM(CASE WHEN p.status = 'Izin' THEN 1 ELSE 0 END)) as jml_alpa
             FROM user u
             LEFT JOIN presensi_hima p ON u.nim = p.nim
             WHERE u.role != 'admin'
             GROUP BY u.nim 
             ORDER BY jml_hadir DESC";
$res_rekap = mysqli_query($conn, $sql_rekap);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Export PDF - Rekap Kehadiran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Times New Roman', serif; background: white; }
        .kop { border-bottom: 3px double black; padding-bottom: 10px; margin-bottom: 30px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="no-print alert alert-warning text-center">
            <h5 class="mb-2">Pratinjau Laporan</h5>
            <button onclick="window.print()" class="btn btn-primary">Download / Simpan sebagai PDF</button>
            <button onclick="window.close()" class="btn btn-secondary">Tutup</button>
        </div>

        <div class="text-center kop">
            <h3 class="mb-0">HIMPUNAN MAHASISWA SISTEM INFORMASI</h3>
            <h4 class="mb-0">UIN SULTAN SYARIF KASIM RIAU</h4>
            <p class="mb-0 mt-2">LAPORAN REKAPITULASI PRESENSI ANGGOTA</p>
            <small>Dicetak pada: <?= date('d/m/Y H:i') ?></small>
        </div>

        <table class="table table-bordered border-dark">
            <thead class="table-light text-center align-middle">
                <tr>
                    <th>No</th>
                    <th>NIM</th>
                    <th>Nama Lengkap</th>
                    <th>Divisi</th>
                    <th>Hadir</th>
                    <th>Izin</th>
                    <th>Alpa</th>
                    <th>Persen (%)</th>
                </tr>
            </thead>
            <tbody>
                <?php $n=1; while($r = mysqli_fetch_assoc($res_rekap)): 
                    $persen = ($r['jml_hadir'] > 0) ? round(($r['jml_hadir'] / 20) * 100) : 0;
                ?>
                <tr>
                    <td class="text-center"><?= $n++ ?></td>
                    <td><?= $r['nim'] ?></td>
                    <td><?= $r['nama'] ?></td>
                    <td><?= $r['divisi'] ?></td>
                    <td class="text-center text-success"><?= $r['jml_hadir'] ?></td>
                    <td class="text-center text-warning"><?= $r['jml_izin'] ?></td>
                    <td class="text-center text-danger"><?= $r['jml_alpa'] ?></td>
                    <td class="text-center fw-bold"><?= $persen ?>%</td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="row mt-5">
            <div class="col-8"></div>
            <div class="col-4 text-center">
                <p>Pekanbaru, <?= date('d F Y') ?></p>
                <br><br><br>
                <p><b>Ketua Himpunan</b></p>
            </div>
        </div>
    </div>
</body>
</html>