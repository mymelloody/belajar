<?php
include 'koneksi.php'; 

// --- 1. LOGIKA DATABASE ---
// Hitung total mahasiswa (bukan admin)
$res_mhs = mysqli_query($conn, "SELECT COUNT(*) as total FROM user WHERE role != 'admin'");
$total_mhs = mysqli_fetch_assoc($res_mhs)['total'];

// Hitung total kehadiran (Hadir)
$res_hadir = mysqli_query($conn, "SELECT COUNT(*) as total FROM presensi_hima WHERE status = 'Hadir'");
$total_presensi = mysqli_fetch_assoc($res_hadir)['total'];

// Hitung total izin
$res_izin_global = mysqli_query($conn, "SELECT COUNT(*) as total FROM presensi_hima WHERE status = 'Izin'");
$total_izin_global = mysqli_fetch_assoc($res_izin_global)['total'];

// Hitung hadir tepat waktu (sebelum jam 08:00)
$res_tepat = mysqli_query($conn, "SELECT COUNT(*) as total FROM presensi_hima WHERE status = 'Hadir' AND TIME(waktu_hadir) <= '08:00:00'");
$total_tepat_waktu = mysqli_fetch_assoc($res_tepat)['total'];

// Hitung Total Alpa Global (Asumsi 20 kali pertemuan)
$total_alpa_global = ($total_mhs * 20) - $total_presensi - $total_izin_global;

// Rata-rata kehadiran seluruhnya
$avg_kehadiran = ($total_mhs > 0) ? round(($total_presensi / ($total_mhs * 20)) * 100, 1) : 0;

// Data Grafik Mingguan (7 hari terakhir)
$grafik_labels = [];
$grafik_data = [];
for ($i = 6; $i >= 0; $i--) {
    $tgl = date('Y-m-d', strtotime("-$i days"));
    $grafik_labels[] = date('D', strtotime($tgl));
    $q = mysqli_query($conn, "SELECT COUNT(*) as jml FROM presensi_hima WHERE DATE(waktu_hadir) = '$tgl' AND status = 'Hadir'");
    $grafik_data[] = mysqli_fetch_assoc($q)['jml'] ?? 0;
}

// Rekapitulasi Seluruh Anggota
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

// 1. PENGATURAN PAGINATION
$limit = 7; // Tampilkan 10 data per halaman
$halaman = isset($_GET['hal']) ? (int)$_GET['hal'] : 1;
$mulai = ($halaman > 1) ? ($halaman * $limit) - $limit : 0;

// 2. HITUNG TOTAL DATA UNTUK NAVIGASI (Sesuaikan query ini dengan query rekapmu)
$total_data = mysqli_num_rows($res_rekap); // Ambil total dari hasil query rekap aslimu
$total_halaman = ceil($total_data / $limit);
?>

<div class="col-12 mb-4">
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
                <div class="fw-semibold text-muted" style="font-size: 0.85rem;"><?= date('l, d F Y') ?></div>
                <div id="jam" class="fw-bold" style="font-size: 1.4rem; color: #0ea5e9;"></div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="text-muted fw-bold mb-0">Laporan dan Rekap kehadiran anggota</p>
        <a href="cetak_rekap.php" target="_blank" class="btn btn-primary shadow-sm px-4 py-2 rounded-3">
            <i class="bi bi-file-earmark-pdf-fill me-2"></i> Export PDF
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 rounded-4">
                <small class="text-muted">Rata-rata Kehadiran</small>
                <h3 class="fw-bold mb-0"><?= $avg_kehadiran ?>%</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 rounded-4">
                <small class="text-muted">jumlah Kehadiran</small>
                <h3 class="fw-bold mb-0"><?= $total_presensi ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 rounded-4">
                <small class="text-muted">Hadir Tepat Waktu</small>
                <h3 class="fw-bold mb-0"><?= $total_tepat_waktu ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 rounded-4">
                <small class="text-muted">Jumlah Alpa</small>
                <h3 class="fw-bold mb-0 text-danger"><?= $total_alpa_global ?></h3>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4 rounded-5">
                <h6 class="fw-bold mb-4">Grafik Kehadiran Mingguan</h6>
                <canvas id="weeklyChart" style="max-height: 280px;"></canvas>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 rounded-5">
                <h6 class="fw-bold mb-4">Distribusi Kehadiran</h6>
                <canvas id="distributionChart"></canvas>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm p-4 rounded-5">
    <h6 class="fw-bold mb-4">Rekapitulasi Kehadiran Seluruh Anggota</h6>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light text-secondary">
                <tr>
                    <th>NAMA ANGGOTA</th>
                    <th>DIVISI</th>
                    <th class="text-center">HADIR</th>
                    <th class="text-center">IZIN</th>
                    <th class="text-center">ALPA</th>
                    <th class="text-center">PERSENTASE</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Kita arahkan pointer data ke posisi 'mulai' sesuai halaman
                if($total_data > 0) mysqli_data_seek($res_rekap, $mulai); 
                
                $no = 0;
                while($r = mysqli_fetch_assoc($res_rekap)): 
                    if($no >= $limit) break; // Berhenti jika sudah mencapai limit per halaman
                    
                    $persen = ($r['jml_hadir'] > 0) ? round(($r['jml_hadir'] / 20) * 100) : 0;
                    $color = ($persen >= 75) ? "bg-success" : (($persen >= 50) ? "bg-warning" : "bg-danger");
                ?>
                <tr>
                    <td>
                        <div class="fw-bold"><?= $r['nama'] ?></div>
                        <small class="text-muted"><?= $r['nim'] ?></small>
                    </td>
                    <td><span class="badge bg-light text-dark"><?= $r['divisi'] ?></span></td>
                    <td class="text-center text-success fw-bold"><?= $r['jml_hadir'] ?></td>
                    <td class="text-center text-warning fw-bold"><?= $r['jml_izin'] ?></td>
                    <td class="text-center text-danger fw-bold"><?= $r['jml_alpa'] ?></td>
                    <td class="text-center">
                        <div class="progress mb-1" style="height: 6px; width: 80px; margin: 0 auto;">
                            <div class="progress-bar <?= $color ?>" style="width: <?= $persen ?>%"></div>
                        </div>
                        <small class="fw-bold"><?= $persen ?>%</small>
                    </td>
                </tr>
                <?php 
                    $no++;
                endwhile; 
                ?>
            </tbody>
        </table>
    </div>

    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= ($halaman <= 1) ? 'disabled' : '' ?>">
                <a class="page-link shadow-sm border-0 rounded-start" href="index.php?page=laporan&hal=<?= $halaman - 1 ?>" style="color: #5eb5ee;">
                    <i class="bi bi-chevron-left"></i> Prev
                </a>
            </li>

            <?php for($x=1; $x<=$total_halaman; $x++): ?>
                <li class="page-item <?= ($halaman == $x) ? 'active' : '' ?>">
                    <a class="page-link border-0 mx-1 rounded shadow-sm <?= ($halaman == $x) ? 'text-white' : '' ?>" 
                       style="<?= ($halaman == $x) ? 'background-color: #5eb5ee;' : 'color: #5eb5ee;' ?>"
                       href="index.php?page=laporan&hal=<?= $x ?>"><?= $x ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= ($halaman >= $total_halaman) ? 'disabled' : '' ?>">
                <a class="page-link shadow-sm border-0 rounded-end" href="index.php?page=laporan&hal=<?= $halaman + 1 ?>" style="color: #5eb5ee;">
                    Next <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        </ul>
    </nav>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Script jam digital
    function updateClock() {
        const now = new Date();
        document.getElementById('jam').textContent = now.toLocaleTimeString('en-GB');
    }
    setInterval(updateClock, 1000); updateClock();

    // Chart Mingguan
    const ctxWeekly = document.getElementById('weeklyChart').getContext('2d');
    new Chart(ctxWeekly, {
        type: 'bar',
        data: {
            labels: <?= json_encode($grafik_labels) ?>,
            datasets: [{
                label: 'Kehadiran',
                data: <?= json_encode($grafik_data) ?>,
                backgroundColor: '#10b981',
                borderRadius: 8
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // Chart Distribusi (Otomatis dari Database)
    const ctxDist = document.getElementById('distributionChart').getContext('2d');
    new Chart(ctxDist, {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Izin', 'Alpa'],
            datasets: [{
                data: [<?= $total_presensi ?>, <?= $total_izin_global ?>, <?= $total_alpa_global ?>],
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                borderWidth: 0
            }]
        },
        options: { responsive: true }
    });
</script>