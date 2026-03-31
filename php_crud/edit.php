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
$nim = $_GET['nim'];

$query = mysqli_query($conn, "SELECT * FROM php_crud WHERE nim ='$nim'");
$row = mysqli_fetch_assoc($query);
?>

<h2>Edit Data Mahasiswa</h2>

<form method="POST" enctype="multipart/form-data">
    NIM (tidak bisa diubah): <br>
    <input type="text" name="nim" value="<?= $row['nim']; ?>" readonly><br><br>

    Nama: <br>
    <input type="text" name="nama" value="<?= $row['nama']; ?>"><br><br>

    Jurusan: <br>
    <select name="jurusan">
        <?php
        $pil_jurusan = ["Sistem Informasi", "Teknik Informatika",   "Teknik Industri", "Teknik Elektro", "Matematika Terapan"];
        foreach ($pil_jurusan as $jur) {
            $selected = ($row['jurusan'] == $jur) ? 'selected' : '';
            echo "<option value='$jur' $selected>$jur</option>";
        }
        ?>
    </select>
    <br><br>

    Foto Lama: <br>
    <?php if ($row['foto']) { ?>
        <img src="uploads/<?= $row['foto']; ?>" width="80"><br>
    <?php } ?>

    Ganti Foto Baru: <br>
    <input type="file" name="foto"><br><br>

    <input type="submit" name="update" value="Ubah Data">  [ <a href="index.php">Kembali</a> ]
</form>
<?php
    if (isset($_POST['update'])) {      
        $nama    = $_POST['nama'];      
        $jurusan = $_POST['jurusan'];     
        $fotoBaru = $_FILES['foto']['name'];        
        $tmpBaru   = $_FILES['foto']['tmp_name'];    

        if ($fotoBaru != "") {      
            $ext = pathinfo($fotoBaru, PATHINFO_EXTENSION); 
            $allowed = array("jpg","jpeg","png","gif"); 
            if (in_array(strtolower($ext), $allowed)) {  
                $newName = "foto_" . time() . "." . $ext; 
                move_uploaded_file($tmpBaru, "uploads/" . $newName); 
                if ($row['foto'] && file_exists("uploads/" . $row['foto'])) { 
                    unlink("uploads/" . $row['foto']); }
            } else {
                echo "<script>alert('File harus berupa gambar');</script>"; 
                exit;
            }
        } else { $newName = $row['foto']; } 
        $sql = "UPDATE php_crud SET nama='$nama', jurusan='$jurusan', foto='$newName' WHERE nim='$nim'";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Data berhasil diupdate'); window.location='index.php';</script>";
        } else { echo "Error: " . mysqli_error($conn); } 
    }
    ?>