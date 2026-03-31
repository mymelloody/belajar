<?php
include 'koneksi.php'; // Pastikan file koneksi sudah benar

if(isset($_POST['simpan'])){
    $nama = $_POST['nama_kegiatan'];
    $tgl  = $_POST['tgl_kegiatan'];
    $mulai = $_POST['jam_mulai'];
    $akhir = $_POST['jam_selesai'];
    $ket   = $_POST['keterangan'];

    mysqli_query($conn, "INSERT INTO kegiatan VALUES('', '$nama', '$tgl', '$mulai', '$akhir', '$ket')");
    echo "<script>alert('Kegiatan Berhasil Ditambahkan!'); window.location='index.php?page=input_kegiatan';</script>";
}
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
<div class="card border-0 shadow-sm p-3 mx-auto" style="max-width: 500px; border-radius: 15px;">
    <h5 class="text-muted fw-bold mb-0">Input Jadwal Kegiatan</h5>
    <br>
    <form method="POST">
        <input type="text" name="nama_kegiatan" class="form-control mb-2" placeholder="Nama Kegiatan (Contoh: Rapat Pleno)" required>
        <input type="date" name="tgl_kegiatan" class="form-control mb-2" required>
        <div class="row">
            <div class="col"><label class="small">Mulai</label><input type="time" name="jam_mulai" class="form-control mb-2" required></div>
            <div class="col"><label class="small">Selesai</label><input type="time" name="jam_selesai" class="form-control mb-2" required></div>
        </div>
        <textarea name="keterangan" class="form-control mb-3" placeholder="Deskripsi tambahan..."></textarea>
        <button type="submit" name="simpan" class="btn btn-primary w-100 fw-bold">SIMPAN KEGIATAN</button>
    </form>
</div>