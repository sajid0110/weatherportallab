<?php
require __DIR__ . '/config.php';

// ইউজার লগইন না থাকলে লগইন পেজে পাঠিয়ে দেবে
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=Please+login+to+access+your+profile&type=error");
    exit;
}

$user_id = $_SESSION['user_id'];
$msg = '';
$msgType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['password'] ?? '';
    
    // 🚨 VULNERABLE: No CSRF tokens, and SQL Injection in the update query
    $sql = "UPDATE users SET password = '$new_password' WHERE id = '$user_id'";
    
    if (mysqli_query($conn, $sql)) {
        $msg = "Password updated successfully!";
    } else {
        $msg = "Error updating password: " . mysqli_error($conn);
        $msgType = 'error';
    }
}

// কারেন্ট ইউজারের ডিটেইলস নিয়ে আসা (SQLi Vulnerable)
$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Weather Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1><a href="index.php">🌦️ Vulnerable Weather Portal</a></h1>
            <nav>
                <a href="index.php">Home</a>
                <a href="create_report.php">New Report</a>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>
    <div class="container">
        <div class="card" style="max-width: 500px; margin: 20px auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
            <h2>Station Operator Profile</h2>
            <?php if ($msg): ?>
                <div class="alert alert-<?php echo $msgType === 'error' ? 'danger' : 'success'; ?>">
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>
            
            <p><strong>Operator Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Account ID:</strong> Station-#<?php echo $user['id']; ?></p>
            
            <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
            
            <h3>Change Access Password</h3>
            <p style="font-size: 0.85em; color: #e74c3c; margin-bottom: 10px; font-weight: bold;">
                ⚠️ Lab Warning: This form lacks CSRF Protection!
            </p>
            <form method="POST">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="password" style="display: block; margin-bottom: 5px; font-weight: bold;">New Station Password</label>
                    <input type="password" name="password" id="password" required placeholder="Enter new station password" style="width: 100%; padding: 8px; box-sizing: border-box;">
                </div>
                <button type="submit" class="btn btn-warning" style="padding: 10px 20px; background: #e67e22; color: white; border: none; border-radius: 4px; cursor: pointer;">Update Password</button>
            </form>
        </div>
    </div>
</body>
</html>