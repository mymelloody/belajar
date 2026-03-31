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
include 'koneksi.php';
header("content-type:appliication/vnd.ms-excel");
header("content-Dispotion: attachment; filename=data_mahasiswa.xls");?>
<h3>Daftar Mahasiswa</h3>
<table border>
    <tr><th>No</th><th>Nim</th><th>nama</th><th>Jurusan</th></tr>
    <?php
    $no = 1;
    $query = mysqli_query($conn,"SELECT * FROM php_crud ORDER BY nim ASC");
    while ($row = mysqli_fetch_assoc($query)) {
        echo "<tr>
                <td>".$no++."</td>
                <td>".$row['nim']."</td>
                <td>".$row['nama']."</td>
                <td>".$row['jurusan']."</td>
            </tr>";
    }
    ?>
</table>