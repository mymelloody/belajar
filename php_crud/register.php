<?php
session_start();
require 'koneksi.php';
if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
if ($username == "" || $password == "") {
        $error = "Username dan password wajib diisi!";
    } else {
        $cek = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $cek->bind_param("s", $username);
        $cek->execute();
        $cek->store_result();
        if ($cek->num_rows > 0) {
            $error = "Username sudah digunakan!";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed);
            $stmt->execute();
            $_SESSION['register_success'] = "Akun berhasil dibuat, silakan login.";
            header("location: login.php");
            exit;
        }
    }
}
?>
<h2>Form Register</h2>
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST">
    <label>Username:</label><br>
    <input type="text" name="username" required><br><br>
    <label>Password:</label><br>
    <input type="password" name="password" required minlength="6"><br><br>
    <button type="submit" name="register">Daftar</button>
</form>
<p>Sudah punya akun? <a href="login.php">Login</a></p>