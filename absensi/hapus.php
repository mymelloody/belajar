<?php
session_start();
include 'koneksi.php';

/* ================== CEK LOGIN ================== */
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

/* ================== CEK ROLE ================== */
if ($_SESSION['role'] != 'admin') {
    echo "<h3>Akses ditolak</h3>";
    exit;
}

/* ================== CEK PARAMETER ================== */
if (!isset($_GET['nim'])) {
    header("Location: index.php?page=absen");
    exit;
}

$nim = mysqli_real_escape_string($conn, $_GET['nim']);

/* ================== HAPUS DATA ================== */
/* Hapus presensi dulu (kalau ada relasi) */
mysqli_query($conn, "DELETE FROM presensi_hima WHERE nim = '$nim'");

/* Hapus user */
$hapus = mysqli_query($conn, "DELETE FROM user WHERE nim = '$nim'");

if ($hapus) {
    header("Location: index.php?page=absen&msg=hapus_sukses");
} else {
    header("Location: index.php?page=absen&msg=hapus_gagal");
}
exit;
