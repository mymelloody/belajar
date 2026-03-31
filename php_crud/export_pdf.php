<?php
session_start();

//jika tidak login
if (!isset ($_SESSION ['user_login'])) {
    // cek remember me
    if (isset ($_COOKIE ['rememberUser'])) {
        $_SESSION['username'] = $_COOKIE['rememberUser'];
        $_SESSION['user_login'] = true;
    } else {
        header("Location: login.php");
        exit;
    }
}
?>
<?php
include 'koneksi.php'; ?>
<html>
<head><title>Cetak PDF Data Mahasiswa</title></head>
<body onload="window.print()">
    <h2 align="center">DAFTAR NAMA MAHASISWA</h2>
    <table border="1" cellspacing="0" cellpadding="8" align="center">
        <tr><th>No</th><th>NIM</th><th>Nama</th><th>Jurusan</th></tr>
        <?php
        $no = 1; // Nilai awal untuk nomor urut tabel
        // Perintah Query tampil data
        $query = mysqli_query($conn, "SELECT * FROM php_crud ORDER BY nim ASC");
        // Menjalankan query
        while ($row = mysqli_fetch_assoc($query)) {
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo $row['nim']; ?></td>
                <td><?php echo $row['nama']; ?></td>
                <td><?php echo $row['jurusan']; ?></td>
            </tr>
            <?php
        }
        ?>
    </table>
</body>
</html>