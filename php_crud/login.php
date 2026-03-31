<?php
session_start();
require 'koneksi.php';

// jika sudah login, langsung ke index
if (isset ($_SESSION ['user_login'])) {
    header("Location: index.php");
    exit;}

if (isset ($_POST ['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);
    $stmt = $conn -> prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt -> bind_param("s", $username);
    $stmt -> execute();
    $stmt -> store_result();

    // cek username
    if ($stmt-> num_rows == 1) {
        $stmt->bind_result($id, $hash);
        $stmt->fetch();
        if (password_verify($password, $hash)) {
            session_regenerate_id(true); //anti session fixation
            $_SESSION ['user_login'] = $id;
            $_SESSION['username'] = $username;

            // Remember me -> simpan cookie 7 hari
            if ($remember) {
                setcookie("rememberUser", $username, time() + (86400 * 7), "/", "", false, true);}
            header("Location: index.php");
            exit;
        } else { $error = "Password salah!";}
    } else { $error = "Username tidak ditemukan!";} 
}
?>

<h2>Login</h2>

<?php
if (isset($_SESSION['register_success'])) {
    echo "<p style='color:green;'>" . $_SESSION['register_success'] . "</p>";
    unset($_SESSION['register_success']);}
if (isset($error)) 
    echo "<p style='color:red;'>$error</p>";
?>

<form method="POST">
    <label>Username:</label><br>
    <input type="text" name="username" required><br><br>
    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>
    <label><input type="checkbox" name="remember"> Remember Me </label><br><br>
    <button type="submit" name="login">Login</button>
</form>
<p>Belum punya akun? <a href="register.php">Daftar</a></p>
