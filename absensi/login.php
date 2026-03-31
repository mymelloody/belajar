<?php
session_start();
require "koneksi.php";

if (isset($_POST['login'])) {
    $nim = $_POST['nim'];
    $password = $_POST['password'];

    $query = mysqli_query(
        $conn,
        "SELECT * FROM user WHERE nim='$nim' AND password='$password'"
    );
    $data = mysqli_fetch_assoc($query);

    if ($data) {
        $_SESSION['id']     = $data['id'];
        $_SESSION['nim']    = $data['nim'];
        $_SESSION['nama']   = $data['nama'];
        $_SESSION['divisi'] = $data['divisi'];
        $_SESSION['role']   = $data['role'];

        header("Location: index.php");
        exit;
    } else {
        echo "<div class='alert alert-danger text-center'>
                NIM atau Password salah
              </div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>PRESENSI HIMA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #5eb5ee, #b8e1ff);
            font-family: Arial, sans-serif;
        }

        .card {
            border-radius: 18px;
        }

        .form-control {
            border-radius: 10px;
        }

        .btn-primary {
            border-radius: 25px;
            font-weight: 600;
        }
    </style>
</head>

<body class="bg-light">
<div class="container">
    <div class="row min-vh-100 align-items-center justify-content-center">
        <div class="col-12 col-sm-8 col-md-6 col-lg-4">
            <div class="card shadow-lg border-0">
                <div class="card-body p-4">
                    <div class="text-center mb-3">
                        <img src="https://sif.uin-suska.ac.id/wp-content/uploads/2023/11/Logo-SIF.jpg"
                             alt="Logo Himpunan Mahasiswa Sistem Informasi"
                             width="90">
                    </div>
                    <div class="text-center mb-4">
                        <h3 class="fw-bold judul-login" style="color: #5eb5ee;" >
                            PHIMA-SI
                        </h3>
                        <p class="text-muted small">
                            Presensi Himpunan Mahasiswa Sistem Informasi
                        </p>
                    </div>

                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">NIM</label>
                            <input type="text"
                                   name="nim"
                                   class="form-control"
                                   placeholder="Masukkan NIM"
                                   required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Password</label>
                            <input type="password"
                                   name="password"
                                   class="form-control"
                                   placeholder="Masukkan Password"
                                   required>
                        </div>

                        <button type="submit"
                                name="login"
                                class="btn btn-primary w-100" 
                                style="background-color: #5eb5ee; color: white; font-weight: 600;">
                            Login
                        </button>
                    </form>

                </div>
            </div>

            <p class="text-center text-muted mt-3" style="font-size: 0.65rem; line-height: 1.2;">
                INFORMATION SYSTEM UIN SUSKA RIAU <br>
                &copy; 2025 | All Right Reserved
            </p>

        </div>
    </div>
</div>

</body>
</html>
