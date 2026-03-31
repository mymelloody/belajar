<?php
include 'koneksi.php';

// Ambil NIM dari URL
$id = $_GET['id'] ?? '';

// Ambil data user lama
$q = mysqli_query($conn, "SELECT * FROM user WHERE nim = '$id'");
$d = mysqli_fetch_assoc($q);

// Proteksi kalau data kosong biar gak error null
if (!$d) {
    echo "<script>alert('Data NIM $id Tidak Ditemukan!'); location.href='index.php?page=absen';</script>";
    exit;
}

// LOGIKA UPDATE (KHUSUS EDIT PROFIL)
if (isset($_POST['update_profil'])) {
    $nama   = mysqli_real_escape_string($conn, $_POST['nama_baru']);
    $divisi = mysqli_real_escape_string($conn, $_POST['divisi_baru']);
    
    // Perintah SQL UPDATE (Bukan INSERT)
    $sql = "UPDATE user SET nama='$nama', divisi='$divisi' WHERE nim='$id'";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('BERHASIL: Data Profil $id Sudah Diganti!'); location.href='index.php?page=absen';</script>";
    } else {
        echo "<script>alert('GAGAL: " . mysqli_error($conn) . "');</script>";
    }
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
<div class="card shadow-sm border-0 mx-auto mt-3" style="max-width: 450px; border-radius: 15px;">
    <div class="card-body p-4">
        <h5 class="fw-bold text-primary mb-3"><i class="bi bi-pencil-square"></i> Menu Edit Profil</h5>
        <hr>
        <form method="POST">
            <div class="mb-3">
                <label class="small fw-bold text-muted">NIM Anggota (Tetap)</label>
                <input type="text" class="form-control bg-light" value="<?= $id ?>" readonly>
            </div>

            <div class="mb-3">
                <label class="small fw-bold">Nama Lengkap Baru</label>
                <input type="text" name="nama_baru" class="form-control" value="<?= $d['nama'] ?>" required>
            </div>

            <div class="mb-4">
                <label class="small fw-bold">Divisi/Jabatan Baru</label>
                <input type="text" name="divisi_baru" class="form-control" value="<?= $d['divisi'] ?>" required>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" name="update_profil" class="btn btn-primary fw-bold">
                    SIMPAN PERUBAHAN
                </button>
                <a href="index.php?page=absen" class="btn btn-outline-secondary">BATAL</a>
            </div>
        </form>
    </div>
</div>