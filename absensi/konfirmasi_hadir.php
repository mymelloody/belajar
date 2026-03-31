<?php
$role = $_SESSION['role'] ?? '';
$nim  = $_SESSION['nim'] ?? '';

/*
|--------------------------------------------------------------------------
| AMBIL DATA USER (BUAT FORM ABSEN)
| AMBIL DARI TABEL USER, BUKAN presensi_hima
|--------------------------------------------------------------------------
*/
$q_user = mysqli_query($conn, "SELECT nim,nama,divisi FROM user WHERE nim='$nim'");
$u = mysqli_fetch_assoc($q_user);

// keamanan kalau session rusak
if (!$u) {
    echo "<div class='alert alert-danger'>Data user tidak ditemukan</div>";
    exit;
}

/*
|--------------------------------------------------------------------------
| FITUR HAPUS (ADMIN SAJA)
|--------------------------------------------------------------------------
*/
if ($role == 'admin' && isset($_GET['h'], $_GET['f'])) { 
    if (file_exists("gmbr/".$_GET['f'])) {
        unlink("gmbr/".$_GET['f']);
    }
    mysqli_query($conn, "DELETE FROM presensi_hima WHERE id='".$_GET['h']."'");
    echo "<script>location.href='index.php?page=konfirmasi_hadir'</script>";
    exit;
}
// Ambil kegiatan yang jadwalnya HARI INI
$tgl_sekarang = date('Y-m-d');
$q_kegiatan = mysqli_query($conn, "SELECT * FROM kegiatan WHERE tgl_kegiatan = '$tgl_sekarang' LIMIT 1");
$k = mysqli_fetch_assoc($q_kegiatan);

// Jika tidak ada kegiatan hari ini
$nama_acara = ($k) ? $k['nama_kegiatan'] : "Tidak Ada Kegiatan Hari Ini";
$waktu_acara = ($k) ? $k['jam_mulai'] . " - " . $k['jam_selesai'] . " WIB" : "-";
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
<div class="container mt-3">
    <!-- ================= KAMERA ================= -->
    <div id="div-kamera" class="card p-3 shadow-sm border-0 mx-auto mb-3" style="max-width:320px;">
        <video id="v" width="100%" class="rounded bg-dark mb-2" autoplay></video>
        <canvas id="c" style="display:none;"></canvas>
        <div id="g" class="badge bg-danger mb-2">GPS...</div>
        <button type="button" id="b" class="btn btn-primary w-100 fw-bold" disabled onclick="ambilFoto()">
            AMBIL FOTO ABSEN
        </button>
    </div>

    <!-- ================= FORM ABSEN ================= -->
    <div id="div-form" class="card p-3 shadow-sm border-0 mx-auto mb-3" style="max-width:360px; display:none;">
        <div class="text-center mb-2">
            <img id="prev" src="" class="rounded border" width="120">
            <h6 class="mt-2 fw-bold text-primary">KONFIRMASI ABSEN</h6>
        </div>

        <form action="simpan_kehadiran.php" method="POST">
            <!-- DATA PENTING -->
            <input type="hidden" name="lokasi" id="l">
            <input type="hidden" name="foto" id="p">
            <input type="hidden" name="nim" value="<?= $u['nim'] ?>">
            <input type="hidden" name="nama" value="<?= $u['nama'] ?>">
            <input type="hidden" name="divisi" value="<?= $u['divisi'] ?>">

            <!-- TAMPILAN SAJA -->
            <label class="small fw-bold">NIM</label>
            <input type="text" class="form-control form-control-sm mb-2 bg-light" value="<?= $u['nim'] ?>" readonly>

            <label class="small fw-bold">Nama</label>
            <input type="text" class="form-control form-control-sm mb-2 bg-light" value="<?= $u['nama'] ?>" readonly>

            <label class="small fw-bold">Divisi</label>
            <input type="text" class="form-control form-control-sm mb-2 bg-light" value="<?= $u['divisi'] ?>" readonly>

            <label class="small fw-bold">Nama Kegiatan</label>
            <input type="text" name="kegiatan" class="form-control form-control-sm mb-2" placeholder="Contoh: Rapat HIMASI" required>

            <label class="small fw-bold">Status Kehadiran</label>
            <select name="status" class="form-select form-select-sm mb-3" required>
                <option value="">-- Pilih Status --</option>
                <option value="Hadir">Hadir</option>
                <option value="Izin">Izin (Surat Resmi)</option>
            </select>

            <button type="submit" class="btn btn-success w-100 fw-bold">
                SIMPAN ABSEN
            </button>
            <button type="button" class="btn btn-link btn-sm w-100 text-muted" onclick="location.reload()">
                Batal / Foto Ulang
            </button>
        </form>
    </div>

    <!-- ================= ADMIN VIEW ================= -->
    <?php if ($role == 'admin'): ?>
    <div class="table-responsive bg-white shadow-sm rounded mt-3">
        <table class="table table-sm small text-center">
            <tr class="table-light">
                <th>NIM</th>
                <th>Foto</th>
                <th>Jam</th>
                <th>Aksi</th>
            </tr>
            <?php $res = mysqli_query($conn, "SELECT * FROM presensi_hima WHERE nim = '$nim'ORDER BY id DESC");
            while($r = mysqli_fetch_assoc($res)): ?>
            <tr>
                <td><?= $r['nim'] ?></td>
                <td><img src="gmbr/<?= $r['foto_bukti'] ?>" width="40" class="rounded"></td>
                <td><?= date('H:i', strtotime($r['waktu_hadir'])) ?></td>
                <td>
                    <a href="index.php?page=konfirmasi_hadir&h=<?= $r['id'] ?>&f=<?= $r['foto_bukti'] ?>"
                       onclick="return confirm('Hapus data absen?')"
                       class="text-danger fw-bold">
                       Hapus
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
    <?php endif; ?>
</div>
<div class="alert alert-info border-0 shadow-sm mb-3">
    <h6 class="fw-bold mb-1">Informasi Kegiatan Hari Ini:</h6>
    <p class="mb-0 small"><strong><?= $nama_acara ?></strong></p>
    <p class="mb-0 small text-muted">Waktu: <?= $waktu_acara ?></p>
</div>

<input type="hidden" name="kegiatan" value="<?= $nama_acara ?>">

<script>
const v=document.getElementById('v'),
      l=document.getElementById('l'),
      b=document.getElementById('b'),
      g=document.getElementById('g'),
      c=document.getElementById('c'),
      p=document.getElementById('p'),
      prev=document.getElementById('prev'),
      divKamera=document.getElementById('div-kamera'),
      divForm=document.getElementById('div-form');

// AKTIFKAN KAMERA
navigator.mediaDevices.getUserMedia({video:true}).then(s=>v.srcObject=s);

// AMBIL GPS
navigator.geolocation.getCurrentPosition(pos=>{ 
    l.value = pos.coords.latitude+','+pos.coords.longitude; 
    b.disabled=false;
    g.innerText="GPS OK";
    g.className="badge bg-success mb-2";
});

// AMBIL FOTO
function ambilFoto(){
    c.width=v.videoWidth;
    c.height=v.videoHeight;
    c.getContext('2d').drawImage(v,0,0);
    const data = c.toDataURL('image/jpeg');

    p.value = data;
    prev.src = data;

    divKamera.style.display='none';
    divForm.style.display='block';
}
</script>