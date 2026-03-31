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
<h1>Selamat Datang, <?=htmlspecialchars($_SESSION['username']); ?>!</h1>
<a href="logout.php">Logout</a>
<?php
include 'koneksi.php'; 
?>
<h2>Data Mahasiswa</h2>
<button onclick="location.href='tambah.php'">Tambah Data</button>
<button onclick="location.href='export_excel.php'">Export XL</button>
<button onclick="window.open('export_pdf.php','_blank')">Print PDF</button>

<form method="GET" style="display: inline-block; margin-left: 5px;">
    <input type="text" name="cari" value="<?= isset($_GET['cari']) ? $_GET['cari'] : '' ?>">
    <button type="submit">Cari</button>
</form>

<?php
$limit = 5;
$page  = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

if (isset($_GET['cari'])) {
    $cari  = $_GET['cari'];
    $query = mysqli_query($conn, "SELECT * FROM php_crud 
        WHERE nim LIKE '%$cari%' OR nama LIKE '%$cari%' 
        ORDER BY nim DESC LIMIT $start, $limit");
} else {
    $query = mysqli_query($conn, "SELECT * FROM php_crud ORDER BY nim DESC LIMIT $start, $limit");
}

$no = $start + 1;
?>

<table border="1" cellspacing="0" cellpadding="8">
    <tr bgcolor="#dfcd82ff">
        <th>No</th>
        <th>foto</th>
        <th>nama</th>
        <th>nim</th>
        <th>jurusan</th>
        <th>aksi</th>
    </tr>

<?php while($row = mysqli_fetch_assoc($query)) { ?>
<tr>
    <td><?= $no++ ?></td>

    <td>
        <?php if ($row['foto']) { ?>
            <img src="uploads/<?= $row['foto']; ?>" width="60">
        <?php } else { ?>
            <span>-</span>
        <?php } ?>
    </td>

    <td><?= $row['nama']; ?></td>
    <td><?= $row['nim']; ?></td>
    <td><?= $row['jurusan']; ?></td>

    <td>
        <a href="edit.php?nim=<?= $row['nim']; ?>">Edit</a> |
        <a href="hapus.php?nim=<?= $row['nim']; ?>" onclick="return confirm('Yakin hapus data?')">Hapus</a>
    </td>
</tr>
<?php } ?> 
</table>

<?php
if (isset($_GET['cari'])) {
    $cari = $_GET['cari'];
    $result = mysqli_query($conn, 
        "SELECT COUNT(*) AS total FROM php_crud WHERE nim LIKE '%$cari%' OR nama LIKE '%$cari%' OR jurusan LIKE '%$cari%'"
    );
} else {
    $result = mysqli_query($conn, 
        "SELECT COUNT(*) AS total FROM php_crud"
    );
}

$rowtotal = mysqli_fetch_assoc($result);
$totaldata = $rowtotal['total'];
$totalpage = ceil($totaldata / $limit);
?>

<div style="margin-top:10px;">
    <b>Total</b>: <?= $totaldata?>Mahasiswa &nbsp; | &nbsp; (
        <?php if($page > 1) { ?>
            <a href="?page=<?=$page - 1 ?>&cari=<?=isset($_GET['cari']) ? $_GET['cari'] : '' ?>">prev</a>
        <?php } ?>
        &nbsp; Halaman : <?=$page ?> dari <?=$totalpage?> &nbsp;
        <?php if($page < $totalpage) { ?>
            <a href="?page=<?= $page + 1 ?>&cari=<?=isset($_GET['cari']) ? $_GET['cari'] : '' ?>">Next</a>
        <?php } ?>
    )
</div>