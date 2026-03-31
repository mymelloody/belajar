<?php include 'koneksi.php'; 
$id_nim = $_GET['id'] ?? '';
$q = mysqli_query($conn, "SELECT u.nama, u.divisi, p.* FROM user u JOIN presensi_hima p ON u.nim = p.nim WHERE p.nim = '$id_nim' AND p.status IN ('Izin', 'Sakit') ORDER BY p.id DESC LIMIT 1");
$d = mysqli_fetch_assoc($q);
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
    <h2 class="fw-bold">Konfirmasi Izin</h2>
    <p class="text-muted">Daftar pengajuan izin dan sakit anggota HIMA</p>

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

    <div class="card border-0 shadow-sm overflow-hidden">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">NIM</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Ambil data yang statusnya BUKAN 'Hadir'
                $sql = "SELECT * FROM presensi_hima WHERE status IN ('Izin', 'Sakit') ORDER BY waktu_hadir DESC";
                $res = mysqli_query($conn, $sql);

                if (mysqli_num_rows($res) > 0) {
                    while ($d = mysqli_fetch_assoc($res)) { ?>
                        <tr>
                            <td class="ps-4 fw-bold"><?= $d['nim'] ?></td>
                            <td>
                                <span class="badge bg-warning text-dark px-3"><?= $d['status'] ?></span>
                            </td>
                            <td><?= $d['keterangan'] ?></td>
                            <td class="text-center">
                                <?php if ($d['foto_bukti']) : ?>
                                    <a href="uploads/<?= $d['foto_bukti'] ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Lihat Bukti
                                    </a>
                                <?php else : ?>
                                    <span class="text-muted small">Tidak ada bukti</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                <?php }
                } else {
                    echo "<tr><td colspan='4' class='text-center py-4 text-muted'>Tidak ada data pengajuan izin.</td></tr>";
                } ?>
            </tbody>
        </table>
    </div>
</div>