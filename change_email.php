<?php
require __DIR__ . '/config.php';

// ল্যাব টেস্টের জন্য একটি ডেমো সেশন ইমেইল বা ইউজার চেক (যদি সেশন না থাকে)
session_start();
if (!isset($_SESSION['user_email'])) {
    $_SESSION['user_email'] = "test@anyxel.com"; // প্রথম স্ক্রিনশটের ডিফল্ট ইমেইল
}

// ইমেইল আপডেট করার লজিক (🚨 CSRF Vulnerable)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_email = $_POST['email'] ?? '';
    
    if (!empty($new_email)) {
        // সেশন আপডেট
        $_SESSION['user_email'] = $new_email;
        
        // ডাটাবেজ আপডেট (আপনার আগের কোডের ইউজার আইডি ২ অনুযায়ী)
        // email = '$new_email' এর বদলে username = '$new_email' হবে
$sql = "UPDATE users SET username = '$new_email' WHERE id = 2";
        mysqli_query($conn, $sql);
        
        header("Location: change_email.php?msg=Email+Updated+Successfully");
        exit;
    }
}

$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anyxel Bank Ltd - CSRF Attack</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f8f9fa; color: #333; margin: 0; padding: 40px; }
        .container { max-width: 700px; margin: 0 auto; background: #fff; padding: 40px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        h2 { text-align: center; font-size: 24px; margin-bottom: 30px; color: #111; }
        h3 { font-size: 16px; margin-bottom: 5px; color: #444; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 14px; color: #666; margin-bottom: 8px; }
        .form-control { width: 100%; padding: 10px; font-size: 14px; border: 1px solid #dee2e6; border-radius: 4px; box-sizing: border-box; background-color: #fff; }
        .form-control[readonly] { background-color: #e9ecef; color: #495057; }
        .btn-change { background-color: #007bff; color: white; padding: 10px 24px; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; font-weight: bold; }
        .btn-change:hover { background-color: #0056b3; }
        .alert { padding: 12px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 4px; margin-bottom: 20px; }
        .back-link { display: block; margin-top: 20px; text-align: center; color: #007bff; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>

    <div class="container">
        <h2>weather portal</h2>
        <?php if ($msg): ?>
            <div class="alert"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>

        <div class="form-group">
            <h3>Change Email:</h3>
        </div>

        <form method="POST" action="change_email.php">
            <div class="form-group">
                <label>Current email:</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($_SESSION['user_email']); ?>" readonly>
            </div>

            <div class="form-group">
                <label>Enter new email</label>
                <input type="email" name="email" class="form-control" placeholder="email@exampl.com" required>
            </div>

            <button type="submit" class="btn-change">Change</button>
        </form>

        <a href="index.php" class="back-link">← Go Back to Weather Dashboard</a>
    </div>

</body>
</html>
