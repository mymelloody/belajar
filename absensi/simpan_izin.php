<?php
session_start();
include 'koneksi.php';

if ($_POST) {
    // Ambil data dari form dan session
    $nim = $_SESSION['nim']; // Mengambil NIM dari session login agar aman
    $status = $_POST['status'];
    $keterangan = $_POST['keterangan'];

    $nama_file = ""; // Default jika tidak ada file

    // Logika simpan file (Gambar/PDF) dari input type="file"
    if (!empty($_FILES['bukti']['name'])) {
        $ekstensi = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
        $nama_file = "izin_" . $nim . "_" . time() . "." . $ekstensi;
        
        // Pastikan folder "uploads" sudah ada
        move_uploaded_file($_FILES['bukti']['tmp_name'], "uploads/" . $nama_file);
    }

    // Query simpan ke database
    // Sesuaikan nama kolom: status, keterangan, foto_bukti
    $query = "INSERT INTO presensi_hima (nim, waktu_hadir, status, keterangan, foto_bukti) 
              VALUES ('$nim', NOW(), '$status', '$keterangan', '$nama_file')";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['pesan'] = "Permohonan Izin Berhasil Dikirim!";
        // Redirect kembali ke halaman absen atau dashboard
        header("Location: index.php?page=absen");
        exit();
    } else {
        echo "Gagal menyimpan data: " . mysqli_error($conn);
    }
}
?>