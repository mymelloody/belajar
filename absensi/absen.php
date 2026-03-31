<?php
// 1. Inisialisasi Variabel Dasar
$role = $_SESSION['role'];
$nim_login = $_SESSION['nim'];
$cari = isset($_GET['cari']) ? mysqli_real_escape_string($conn, $_GET['cari']) : '';

// 2. LOGIKA UNTUK ADMIN
    $limit = 6;
    $p_num  = isset($_GET['p_num']) ? (int)$_GET['p_num'] : 1;
    if ($p_num < 1) $p_num = 1;
    $start = ($p_num - 1) * $limit;

    // --- QUERY STATISTIK (Hanya Anggota, Bukan Admin) ---
$total_agt = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) t FROM user WHERE role!='admin'")
)['t'];

$stat_hadir = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(DISTINCT nim) h FROM presensi_hima WHERE status='Hadir' AND DATE(waktu_hadir)=CURDATE()")
)['h'];

$total_izin = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(DISTINCT nim) i FROM presensi_hima WHERE status='Izin' AND DATE(waktu_hadir)=CURDATE()")
)['i'];

$stat_tidak_hadir = $total_agt - ($stat_hadir + $total_izin);


    // --- QUERY UTAMA (Data Anggota + Status Absen + Search + Pagination) ---
    $sql = "SELECT u.nama, u.nim, u.divisi, p.waktu_hadir 
            FROM user u 
            LEFT JOIN presensi_hima p ON u.nim = p.nim AND DATE(p.waktu_hadir) = CURDATE()
            WHERE u.role != 'admin' AND (u.nama LIKE '%$cari%' OR u.nim LIKE '%$cari%' OR u.divisi LIKE '%$cari%')
            ORDER BY u.nama ASC 
            LIMIT $start, $limit";
    $result = mysqli_query($conn, $sql);

    // --- HITUNG TOTAL HALAMAN ---
    $t_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM user WHERE role != 'admin' AND (nama LIKE '%$cari%' OR nim LIKE '%$cari%')");
    $total_data = mysqli_fetch_assoc($t_res)['total'];
    $total_p = ceil($total_data / $limit);   
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
<h4 class="fw-bold mb-3 text-center">Presensi HIMA</h4>
        <div class="container-fluid">
            <p class="text-muted text-center">Lakukan presensi kehadiran anggota HIMA sekarang</p>
        </div>
<?php if ($_SESSION['role'] == 'admin') : ?>
            <div class="row g-3 mb-4 text-center">
                <div class="col-md-3">
                    <div class="card card-stat text-white p-3 shadow-sm border-0" style="background: linear-gradient(135deg, #5eb5ee, #b8e1ff);">
                        <h5 class="small opacity-75">Total Anggota</h5>
                        <h2 class="fw-bold"><?= $total ?></h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-stat bg-success text-white p-3 shadow-sm border-0" style="background: linear-gradient(135deg, #5eb5ee, #b8e1ff);">
                        <h5 class="small opacity-75 text-muted">Hadir</h5>
                        <h2 class="fw-bold"><?= $sudah_absen ?></h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-stat bg-white text-white p-3 shadow-sm border-0" style="background: linear-gradient(135deg, #5eb5ee, #b8e1ff);">
                        <h5 class="small opacity-75 text-muted">Tidak Hadir</h5>
                        <h2 class="fw-bold"><?= $tidak_hadir ?></h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-stat text-white p-3 shadow-sm border-0" style="background: linear-gradient(135deg, #5eb5ee, #b8e1ff);">
                        <h5 class="small opacity-75">Izin</h5>
                        <h2 class="fw-bold"><?= $total_izin ?></h2>
                    </div>
                </div>
    <form method="GET" action="index.php" class="mb-3 d-flex">
        <input type="hidden" name="page" value="absen"> 
        <input type="text" name="cari" class="form-control me-2" placeholder="Cari nama/divisi..." value="<?= htmlspecialchars($cari) ?>">
        <button type="submit" class="btn btn-primary btn-sm">Cari</button>
    </form>

    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">Nama</th>
                            <th class="text-center">NIM</th>
                            <th class="text-center">Jabatan/Divisi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($result) > 0) { 
                            while($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold;">
                                        <?= strtoupper(substr($row['nama'], 0, 1)); ?>
                                    </div>
                                    <div class="fw-bold"><?= $row['nama']; ?></div>
                                </div>
                            </td>
                            <td class="text-center"><?= $row['nim']; ?></td>
                            <td class="text-center small"><?= $row['divisi']; ?></td>
                            <td class="text-center">
                            
                            <a href="index.php?page=konfirmasi_hadir" class="btn btn-sm shadow-sm fw-bold mb-1 w-100" style="background: linear-gradient(135deg, #5eb5ee, #b8e1ff);">Hadir</a>
                            
                            <a href="index.php?page=konfirmasi_izin" class="btn btn-sm shadow-sm fw-bold mb-1 w-100" style="background: linear-gradient(135deg, #5eb5ee, #b8e1ff);">Izin</a>
                            
                            <a href="index.php?page=edit&id=<?= $row['nim']?>" class="btn btn-sm shadow-sm fw-bold mb-1 w-100" style="background: linear-gradient(135deg, #5eb5ee, #b8e1ff); color:white;">Edit</a>

                            <a href="hapus.php?nim=<?= $row['nim'] ?>"
   onclick="return confirm('Yakin hapus anggota ini?')"
   class="btn btn-sm shadow-sm fw-bold mb-1 w-100"
   style="background: linear-gradient(135deg, #5eb5ee, #b8e1ff); color:white;">
   Hapus
</a>

                        </tr>
                        <?php } 
                        } else { ?>
                            <tr><td colspan="4" class="text-center">Data tidak ditemukan</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if ($_SESSION['role'] == 'anggota') : ?>
    <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px;">
        <div class="text-center mb-4">
            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem; font-weight: 800;">
                <?= strtoupper(substr($_SESSION['nama'], 0, 1)); ?>
            </div>
            <h4 class="fw-bold mb-1"><?= $_SESSION['nama']; ?></h4>
            <p class="text-muted small"><?= $_SESSION['divisi']; ?></p>
        </div>

        <div class="row g-3">
            <div class="col-6">
                <a href="index.php?page=konfirmasi_hadir" 
                   class="btn btn-lg  w-100 py-3 shadow-sm fw-bold d-flex flex-column align-items-center justify-content-center" 
                   style="border-radius: 15px; border: none; background: linear-gradient(135deg, #5eb5ee, #b8e1ff);">
                    <i class="bi bi-check-circle fs-3 mb-1"></i>
                    <span>HADIR</span>
                </a>
            </div>
            
            <div class="col-6">
                <a href="index.php?page=izin" 
                   class="btn btn-lg btn-warning text-white w-100 py-3 shadow-sm fw-bold d-flex flex-column align-items-center justify-content-center" 
                   style="border-radius: 15px; border: none; background: linear-gradient(135deg, #5eb5ee, #b8e1ff);">
                    <i class="bi bi-envelope fs-3 mb-1"></i>
                    <span>IZIN</span>
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>


<script>
function tampilJam() {
    const n = new Date();
    document.getElementById('jam').innerText = ` ${n.getHours().toString().padStart(2,'0')}:${n.getMinutes().toString().padStart(2,'0')}:${n.getSeconds().toString().padStart(2,'0')}`;
}
setInterval(tampilJam, 1000); tampilJam();
</script>
        <?php
        $t_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM user WHERE role != 'admin' AND (nama LIKE '%$cari%' OR nim LIKE '%$cari%')");
        $total = mysqli_fetch_assoc($t_res)['total'];
        $total_p = ceil($total / $limit);?>
        <div class="mt-2 small text-muted">
        
        <?php
        $t_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM user WHERE role != 'admin' AND (nama LIKE '%$cari%' OR nim LIKE '%$cari%')");
        $total = mysqli_fetch_assoc($t_res)['total'];
        $total_p = ceil($total / $limit);?>

        <?php if($page > 1): ?>
        <a href="index.php?page=absen&p_num=<?= ($page-1) ?>&cari=<?= $cari ?>" class="me-2 text-decoration-none">&laquo; Prev</a>
        <?php endif; ?>
        Halaman <?= $page ?> dari <?= ($total_p > 0 ? $total_p : 1) ?>

        <?php if($page < $total_p): ?>
        <a href="index.php?page=absen&p_num=<?= ($page+1) ?>&cari=<?= $cari ?>" class="ms-2 text-decoration-none">Next &raquo;</a>
        <?php endif; ?>
    </div>
</div>
</div>
