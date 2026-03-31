<?php
$nim_login = $_SESSION['nim'];

// 1. HITUNG STATISTIK PERSONAL (Akumulasi Selama Ini)
$stat = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT 
        SUM(CASE WHEN status = 'Hadir' THEN 1 ELSE 0 END) as total_hadir,
        SUM(CASE WHEN status = 'Izin' THEN 1 ELSE 0 END) as total_izin,
        SUM(CASE WHEN status = 'Sakit' THEN 1 ELSE 0 END) as total_sakit,
        SUM(CASE WHEN status = 'Alpa' THEN 1 ELSE 0 END) as total_alpa
    FROM presensi_hima WHERE nim = '$nim_login'
"));

// 2. AMBIL DAFTAR RIWAYAT KEGIATAN
$sql_riwayat = "SELECT * FROM presensi_hima WHERE nim = '$nim_login' ORDER BY waktu_hadir DESC";
$res_riwayat = mysqli_query($conn, $sql_riwayat);
?>
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 16px; background:linear-gradient(135deg, #5eb5ee, #b8e1ff); overflow: hidden;">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    
                    <div class="d-flex flex-column">
                        <h3 class="fw-bold mb-0" style="color: #1e293b; letter-spacing: -0.5px;">
                            <?= ($_SESSION['role']=='admin') ? 'Selamat Datang' : 'Hai, '.$_SESSION['nama'] ?>
                        </h3>
                        <div style="font-size: 0.8rem; color: #64748b; margin-top: 4px; line-height: 1.2;">
                            Presensi Himpunan Mahasiswa - Sistem Informasi <br>
                            <span style="font-weight: 500;">UIN Sultan Syarif Kasim Riau</span>
                        </div>
                    </div>

                    <div class="d-flex flex-column text-end">
                        <div class="fw-semibold text-muted" style="font-size: 0.85rem;">
                            <?= date('l, d F Y') ?>
                        </div>
                        <div id="jam" class="fw-bold" style="font-size: 1.4rem; color: #0ea5e9; margin-top: -2px;"></div>
                    </div>
                </div>
            </div>
        </div>
        <br>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold mb-0">Riwayat Presensi Saya</h4>
            <p class="text-muted small">Pantau record kehadiran kamu di setiap kegiatan HIMASI</p>
        </div>
    </div>
<!--card kehadiran-->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card card-stat bg-success text-white p-3 shadow-sm border-0" style="background: linear-gradient(135deg, #5eb5ee, #b8e1ff);">
                <h6 class="small opacity-75">Hadir</h6>
                <h3 class="fw-bold mb-0"><?= $stat['total_hadir'] ?? 0 ?> <span style="font-size: 1rem;">Kali</span></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat bg-success text-white p-3 shadow-sm border-0" style="background: linear-gradient(135deg, #5eb5ee, #b8e1ff);">
                <h6 class="small opacity-75">Izin/Sakit</h6>
                <h3 class="fw-bold mb-0"><?= ($stat['total_izin'] + $stat['total_sakit']) ?> <span style="font-size: 1rem;">Kali</span></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat bg-success text-white p-3 shadow-sm border-0" style="background: linear-gradient(135deg, #5eb5ee, #b8e1ff);">
                <h6 class="small opacity-75">Alpa</h6>
                <h3 class="fw-bold mb-0"><?= $stat['total_alpa'] ?? 0 ?> <span style="font-size: 1rem;">Kali</span></h3>
            </div>
        </div>
    </div>
<!--tabel-->
    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Nama Kegiatan</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($res_riwayat) > 0): 
                            while($row = mysqli_fetch_assoc($res_riwayat)): ?>
                            <tr>
                                <td class="small">
                                    <?= date('d/m/Y', strtotime($row['waktu_hadir'])) ?><br>
                                    <span class="text-muted"><?= date('H:i', strtotime($row['waktu_hadir'])) ?> WIB</span>
                                </td>
                                <td class="fw-bold"><?= $row['kegiatan'] ?? 'Kegiatan Rutin' ?></td>
                                <td>
                                    <?php 
                                    $bg = 'bg-secondary';
                                    if($row['status'] == 'Hadir') $bg = 'bg-success';
                                    if($row['status'] == 'Izin') $bg = 'bg-warning text-dark';
                                    if($row['status'] == 'Sakit') $bg = 'bg-info text-dark';
                                    if($row['status'] == 'Alpa') $bg = 'bg-danger';
                                    ?>
                                    <span class="badge <?= $bg ?>"><?= $row['status'] ?></span>
                                </td>

                                <td class="small text-muted"><?= $row['keterangan'] ?: '-' ?></td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="5" class="text-center py-4">Belum ada data absensi</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>