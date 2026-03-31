<?php
session_start();
include 'koneksi.php';

if ($_POST) {
    $nim = $_POST['nim'];
    $nama = $_POST['nama'];
    $divisi = $_POST['divisi'];
    $kegiatan = $_POST['kegiatan'];
    $status = $_POST['status'];
    $lokasi = $_POST['lokasi'];
    $foto_data = $_POST['foto'];

    // Proses Simpan Gambar
    $foto_data = str_replace('data:image/jpeg;base64,', '', $foto_data);
    $foto_data = base64_decode($foto_data);
    $nama_file = "bukti_" . $nim . "_" . time() . ".jpg";
    file_put_contents("gmbr/" . $nama_file, $foto_data);

    // Simpan ke Database
    $query = "INSERT INTO presensi_hima (nim, waktu_hadir, lokasi_lat_long, foto_bukti) 
              VALUES ('$nim', NOW(), '$lokasi', '$nama_file')";
    
    mysqli_query($conn, $query);
    // Kita tidak redirect, tapi lanjut ke HTML di bawah
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Surat Kehadiran</title>
    <style>
        /* Reset & Base Style */
        body {
            font-family: "Times New Roman", Times, serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            color: #000;
            line-height: 1.5;
        }

        /* Container Surat (Ukuran A4) */
        .container-print {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background-color: #fff;
            padding: 20mm;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            box-sizing: border-box;
        }

        /* Kop Surat */
        .kop-surat {
            display: flex;
            align-items: center;
            border-bottom: 4px double #000;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }
        .logo-kiri, .logo-kanan { 
            width: 90px; 
            height: auto;
        }
        .text-kop { 
            flex-grow: 1; 
            text-align: center; 
        }
        .text-kop h5 { margin: 0; font-size: 16px; font-weight: normal; }
        .text-kop h4 { margin: 2px 0; font-size: 18px; font-weight: bold; text-transform: uppercase; }
        .text-kop p { margin: 0; font-size: 11px; }

        /* Judul Surat */
        .judul-surat {
            text-align: center;
            text-decoration: underline;
            font-weight: bold;
            font-size: 18px;
            text-transform: uppercase;
            margin-bottom: 30px;
        }

        /* Isi Detail */
        .isi-surat {
            margin: 0 auto;
            width: 90%;
        }
        .table-detail {
            width: 100%;
            border: none;
            margin-bottom: 30px;
        }
        .table-detail td {
            padding: 8px 5px;
            vertical-align: top;
            font-size: 14px;
        }
        .label-cell {
            width: 120px;
            font-weight: bold;
        }
        .separator-cell {
            width: 10px;
        }

        /* Bukti Foto */
        .foto-section {
            margin-top: 20px;
            text-align: left;
        }
        .foto-section p {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .foto-frame {
            border: 1px solid #000;
            width: 150px;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .foto-frame img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }

        /* Tombol Navigasi */
        .no-print-area {
            text-align: center;
            margin-top: 30px;
            width: 210mm;
            margin-left: auto;
            margin-right: auto;
        }
        .btn-custom {
            padding: 10px 25px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            margin: 5px;
            cursor: pointer;
            border: none;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #5eb5ee, #b8e1ff);
            color: #000;
            transition: 0.3s;
        }
        .btn-custom:hover { opacity: 0.8; }

        /* Pengaturan Print */
        @media print {
            body { background: none; padding: 0; }
            .container-print { 
                box-shadow: none; 
                margin: 0; 
                width: 100%; 
            }
            .no-print-area { display: none !important; }
            @page { size: A4; margin: 1.5cm; }
        }
    </style>
</head>
<body>

<div class="container-print">
    <div class="kop-surat">
        <img src="gmbr/LOGO-uin.png" class="logo-kiri" alt="Logo UIN">
        <div class="text-kop">
            <h5>HIMPUNAN MAHASISWA SISTEM INFORMASI</h5>
            <h4>FAKULTAS SAINS DAN TEKNOLOGI</h4>
            <h4>UNIVERSITAS ISLAM NEGERI SULTAN SYARIF KASIM RIAU</h4>
            <p>Sekretariat: Kampus UIN Suska Riau Jl. H.R. Soebrantas No. 155 KM. 15 Pekanbaru 28293</p>
            <p>Hp: 0823-8577-8592 | Email: himasi@uin-suska.ac.id</p>
        </div>
        <img src="gmbr/logosi.png" class="logo-kanan" alt="Logo HIMASI">
    </div>

    <div class="judul-surat">
        Surat Keterangan Kehadiran
    </div>

    <div class="isi-surat">
        <table class="table-detail">
            <tr>
                <td class="label-cell">Nama</td>
                <td class="separator-cell">:</td>
                <td><?= htmlspecialchars($nama) ?></td>
            </tr>
            <tr>
                <td class="label-cell">NIM</td>
                <td class="separator-cell">:</td>
                <td><?= htmlspecialchars($nim) ?></td>
            </tr>
            <tr>
                <td class="label-cell">Divisi</td>
                <td class="separator-cell">:</td>
                <td><?= htmlspecialchars($divisi) ?></td>
            </tr>
            <tr>
                <td class="label-cell">Kegiatan</td>
                <td class="separator-cell">:</td>
                <td><?= htmlspecialchars($kegiatan) ?></td>
            </tr>
            <tr>
                <td class="label-cell">Status</td>
                <td class="separator-cell">:</td>
                <td style="color: green; font-weight: bold;">HADIR</td>
            </tr>
            <tr>
                <td class="label-cell">Waktu</td>
                <td class="separator-cell">:</td>
                <td><?= date('d-m-Y H:i') ?> WIB</td>
            </tr>
        </table>

        <div class="foto-section">
            <p>Bukti Foto:</p>
            <div class="foto-frame">
                <img src="gmbr/<?= $nama_file ?>" alt="Foto Bukti">
            </div>
        </div>
        
        <div style="margin-top: 50px; text-align: right;">
            <p>Pekanbaru, <?= date('d F Y') ?></p>
            <div style="height: 80px;"></div>
            <p><strong>( ____________________ )</strong></p>
            <p>Panitia Pelaksana</p>
        </div>
    </div>
</div>

<div class="no-print-area">
    <button onclick="window.print()" class="btn-custom">DOWNLOAD / CETAK PDF</button>
    <a href="index.php" class="btn-custom">KEMBALI KE BERANDA</a>
</div>

</body>
</html>