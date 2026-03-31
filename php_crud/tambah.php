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
include 'koneksi.php';?>
<h2>Tambah Data Mahasiswa</h2>

<form action="" method="POST" enctype="multipart/form-data">
    NIM: <input type="number" name="nim"><br>
    Nama: <input type="text" name="nama"><br>
    Jurusan: <br>
    <select name="jurusan">
        <?php
        $pil_jurusan = ["Sistem Informasi", "Teknik Informatika", "Teknik Industri", "Teknik Elektro", "Matematika Terapan"];
        ?>
        <option value="">-- Pilih Jurusan --</option>
        <?php foreach ($pil_jurusan as $jur) { ?>
            <option value="<?=$jur?>"><?=$jur?></option>
        <?php } ?>
    </select><br><br>

    Foto: <input type="file" name="foto"><br><br>
    <input type="submit" name="simpan" value="Simpan"> [<a href="index.php"> Kembali </a>]
</form>

<?php

if (isset($_POST['simpan'])) { // Jika tombol simpan diklik
    $nim     = $_POST['nim'];     // Menyimpan nilai nim dari form input ke variabel baru ($nim)
    $nama    = $_POST['nama'];    // Menyimpan nilai nama dari form input ke variabel baru ($nama)
    $jurusan = $_POST['jurusan']; // Menyimpan nilai jurusan dari form input ke variabel baru ($jurusan)
    $foto    = $_FILES['foto']['name'];    // Menyimpan nama asli foto yang diupload ke variabel $foto
    $tmp     = $_FILES['foto']['tmp_name']; // Menyimpan lokasi foto sementara ke $tmp

    if ($foto != "") { // Cek apakah foto tidak kosong?
        $ext = pathinfo($foto, PATHINFO_EXTENSION); // Mengambil ekstensi dari nama file
        $allowed = array("jpg", "jpeg", "png", "gif"); // Format yang diperbolehkan

        if (in_array(strtolower($ext), $allowed)) { // Cek apakah ekstensi sudah sesuai
            $newName = "foto_" . date("dmY_His") . "." . $ext; // Memberikan nama baru foto yang diupload
            move_uploaded_file($tmp, "uploads/" . $newName); // Memindahkan foto dari tmp ke folder upload
        } else {
            echo "<script>alert('File harus berupa gambar');</script>"; // Pesan kesalahan format gambar
            exit;
        }
    } else {
        $newName = null; // Jika $foto tidak ada
    }
}

$sql = "INSERT INTO php_crud (nim, nama, jurusan, foto) 
        VALUES ('$nim', '$nama', '$jurusan', '$newName')";

if (mysqli_query($conn, $sql)) {
    echo "<script>alert('Data Berhasil disimpan'); window.location='index.php';</script>";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>