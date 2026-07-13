<?php
require __DIR__ . '/config.php';

// যদি সেশন অলরেডি স্টার্ট করা না থাকে, তবে স্টার্ট করা
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🔒 ইউজার লগইন না থাকলে তাকে লগইন পেজে পাঠিয়ে দেওয়া
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';
$msg = $_GET['msg'] ?? '';

// 🔍 ডেটাবেজ থেকে কারেন্ট লগইন করা ইউজারের লেটেস্ট তথ্য তুলে আনা
$user_query = mysqli_query($conn, "SELECT username, email FROM users WHERE id = '$user_id'");
$current_user = mysqli_fetch_assoc($user_query);

$current_email = $current_user['email'] ?? '';

// 🚨 প্রোফাইল আপডেট (Email & Password) করার লজিক (🚨 CSRF Vulnerable)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // ১. ইমেইল পরিবর্তনের রিকোয়েস্ট আসলে
    if (isset($_POST['change_email'])) {
        $new_email = $_POST['email'] ?? '';
        if (!empty($new_email)) {
            $sql = "UPDATE users SET email = '$new_email' WHERE id = '$user_id'";
            mysqli_query($conn, $sql);
            header("Location: change_email.php?msg=Email+Updated+Successfully");
            exit;
        }
    }
    
    // ২. পাসওয়ার্ড পরিবর্তনের রিকোয়েস্ট আসলে
    if (isset($_POST['change_password'])) {
        $new_password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if (!empty($new_password) && !empty($confirm_password)) {
            // চেক করা হচ্ছে দুটি পাসওয়ার্ড ম্যাচ করে কিনা
            if ($new_password === $confirm_password) {
                
                // ল্যাবের জন্য সরাসরি প্লেইন টেক্সট পাসওয়ার্ড আপডেট করা হচ্ছে
                $sql = "UPDATE users SET password = '$new_password' WHERE id = '$user_id'";
                mysqli_query($conn, $sql);
                
                header("Location: change_email.php?msg=Password+Changed+Successfully");
                exit;
            } else {
                $error = "Passwords do not match!";
            }
        } else {
            $error = "Please fill in both password fields!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Portal - Account Settings</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f8f9fa; color: #333; margin: 0; padding: 40px; }
        .container { max-width: 700px; margin: 0 auto; background: #fff; padding: 40px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        h2 { text-align: center; font-size: 24px; margin-bottom: 30px; color: #111; }
        h3 { font-size: 16px; margin-bottom: 5px; color: #444; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 14px; color: #666; margin-bottom: 8px; }
        .form-control { width: 100%; padding: 10px; font-size: 14px; border: 1px solid #dee2e6; border-radius: 4px; box-sizing: border-box; background-color: #fff; }
        .form-control[readonly] { background-color: #e9ecef; color: #495057; }
        .btn-change { background-color: #007bff; color: white; padding: 10px 24px; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; font-weight: bold; }
        .btn-change:hover { background-color: #0056b3; }
        .btn-danger { background-color: #dc3545; }
        .btn-danger:hover { background-color: #bd2130; }
        .alert { padding: 12px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 4px; margin-bottom: 20px; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .back-link { display: block; margin-top: 30px; text-align: center; color: #007bff; text-decoration: none; font-size: 14px; }
        .section-wrapper { margin-bottom: 40px; padding: 20px; border: 1px solid #f0f0f0; border-radius: 6px; background: #fafafa; }
    </style>
</head>
<body>

    <div class="container">
        <h2>命 Weather Portal - Account Settings</h2>
        
        <?php if ($msg): ?>
            <div class="alert"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="section-wrapper">
            <h3>✉️ Change Email</h3><br>
            <form method="POST" action="change_email.php">
                <div class="form-group">
                    <label>Current email:</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($current_email); ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Enter new email:</label>
                    <input type="email" name="email" class="form-control" placeholder="email@example.com" required>
                </div>

                <button type="submit" name="change_email" class="btn-change">Update Email</button>
            </form>
        </div>

        <div class="section-wrapper">
            <h3>🔒 Change Password</h3><br>
            <form method="POST" action="change_email.php">
                <div class="form-group">
                    <label>New Password:</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter new password" required>
                </div>

                <div class="form-group">
                    <label>Confirm New Password:</label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Confirm new password" required>
                </div>

                <button type="submit" name="change_password" class="btn-change btn-danger">Update Password</button>
            </form>
        </div>

        <a href="index.php" class="back-link">← Go Back to Weather Dashboard</a>
    </div>

</body>
</html>
