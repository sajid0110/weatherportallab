<?php
require 'config.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // 🚨 1. SQL Injection Vulnerability (Direct concatenation)
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit;
    } else {
        $error = "Login Failed! Query: " . htmlspecialchars($sql);
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Login - Weather Portal</title></head>
<body style="font-family: Arial; margin: 40px;">
    <h2>🌦️ Weather Portal - Staff Login</h2>
    <?php if($error) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        Username: <input type="text" name="username" required><br><br>
        Password: <input type="password" name="password"><br><br>
        <button type="submit">Login</button>
    </form>
    <p style="background:#eee; padding:10px;"><b>🎯 SQLi Attack:</b> Enter <code>admin' OR '1'='1</code> as username to bypass.</p>
</body>
</html>