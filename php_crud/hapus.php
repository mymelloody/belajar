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
$q = mysqli_query($conn,"SELECT foto FROM php_crud WHERE nim='$nim'");
$row = mysqli_fetch_assoc($q);
if ($row['foto'] && file_exists("uploads/".$row['foto']));{
unlink("uploads/".$row['foto']);}
$sql = "DELETE FROM php_crud WHERE nim='$nim'";
if (mysqli_query($conn, $sql)) {
   echo"<script>alert('Data Berhasil dihapus');window.location='index.php'</script>";
}else{echo "Eror:".mysqli_error($conn);}
?>