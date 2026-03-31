
<?php
include 'koneksi.php';
// 1. KONEKSI DATABASE
$host = "localhost";
$user = "root";
$pass = "";
$db   = "absensi"; // Ganti dengan nama database kamu

$conn = mysqli_connect($host, $user, $pass, $db);

// 2. PROSES SIMPAN DATA JIKA TOMBOL DIKLIK
if (isset($_POST['submit'])) {
    $nama   = mysqli_real_escape_string($conn, $_POST['nama']);
    $alasan = mysqli_real_escape_string($conn, $_POST['alasan']);
    
    // Pengaturan File
    $nama_file = $_FILES['bukti_file']['name'];
    $tmp_name  = $_FILES['bukti_file']['tmp_name'];
    $dir_upload = "uploads/";

    // Buat folder uploads jika belum ada
    if (!is_dir($dir_upload)) {
        mkdir($dir_upload);
    }

    // Pindahkan file ke folder uploads
    if (move_uploaded_file($tmp_name, $dir_upload . $nama_file)) {
        $query = "INSERT INTO izin (nama, alasan, bukti_file) VALUES ('$nama', '$alasan', '$nama_file')";
        if (mysqli_query($conn, $query)) {
            $pesan = "<div class='alert alert-success'>Izin berhasil dikirim!</div>";
        } else {
            $pesan = "<div class='alert alert-danger'>Gagal menyimpan ke database.</div>";
        }
    } else {
        $pesan = "<div class='alert alert-danger'>Gagal mengunggah file.</div>";
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
<div class="container-fluid py-4">
    <div class="card shadow-sm p-4 mx-auto" style="max-width: 500px; border-radius: 15px;">
        <h5 class="fw-bold mb-3">Form Izin / Sakit</h5>
        <form action="simpan_izin.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Pilih Alasan</label>
                <select name="status" class="form-select" required>
                    <option value="Izin">Izin</option>
                    <option value="Sakit">Sakit</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" placeholder="Alasan detail..." required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Upload Bukti (PDF/Gambar)</label>
                <input type="file" name="bukti" class="form-control" accept="image/*,application/pdf">
            </div>
            <button type="submit" class="btn btn-warning w-100 fw-bold">Kirim Izin</button>
            <a href="index.php?page=absen" class="btn btn-link w-100 text-muted mt-2 btn-sm text-decoration-none">Kembali</a>
        </form>
    </div>
</div>