<?php
require __DIR__ . '/config.php';

// ইউজার লগইন না থাকলে লগইন পেজে পাঠিয়ে দেবে
if (!isset($_SESSION['username'])) {
    header("Location: login.php?msg=Please+login+to+edit+reports&type=error");
    exit;
}

$id = $_GET['id'] ?? '';
$error = '';
$current_username = $_SESSION['username'];

// Fetch existing weather report
// 🚨 SQL INJECTION VULNERABILITY: $id সরাসরি কুয়েরিতে বসানো হয়েছে
$sql = "SELECT * FROM weather_reports WHERE id = '$id'";
$result = mysqli_query($conn, $sql);
$report = mysqli_fetch_assoc($result);

if (!$report) {
    header("Location: index.php?msg=" . urlencode('Report not found') . "&type=error");
    exit;
}

// চেক করা হচ্ছে টেবিলে user_id কলাম আছে কিনা (ব্যাকওয়ার্ড কম্প্যাটিবিলিটি ঠিক রাখার জন্য)
$checkCol = mysqli_query($conn, "SHOW COLUMNS FROM weather_reports LIKE 'user_id'");
$has_user_id = mysqli_num_rows($checkCol) > 0;

// 🚨 AUTHORIZATION BYPASS (CSRF TARGET): 
// 'shifat' নামের ইউজার অন্য অপারেটরের রিপোর্ট এডিট করতে পারবে না সরাসরি, কিন্তু CSRF ঘটিয়ে ভিকটিমকে দিয়ে করাতে পারবে।
if ($has_user_id && $current_username === 'shifat' && isset($report['user_id']) && $report['user_id'] != $_SESSION['user_id']) {
    $error = "Access Denied: You cannot edit weather reports that belong to other stations. (Hint: Exploit CSRF to bypass this!)";
}

$city = $report['city_name'];
$temp = $report['temperature'];
$cond = $report['condition_text'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $city = $_POST['city'] ?? '';
    $temp = $_POST['temp'] ?? '';
    $cond = $_POST['cond'] ?? '';

    if ($city === '' || $temp === '' || $cond === '') {
        $error = 'Please fill in all fields.';
    } else {
        // 🚨 VULNERABLE: No CSRF token protection & Vulnerable to SQLi
        $sql = "UPDATE weather_reports SET city_name = '$city', temperature = '$temp', condition_text = '$cond' WHERE id = '$id'";
        if (mysqli_query($conn, $sql)) {
            header("Location: index.php?msg=" . urlencode('Weather report updated successfully') . "&type=success");
            exit;
        } else {
            $error = 'Failed to update report: ' . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Weather Report - Weather Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1><a href="index.php">綫️ Vulnerable Weather Portal</a></h1>
            <nav>
                <a href="index.php">Home</a>
                <a href="logout.php">Logout</a>
            </nav>
        </div>
    </header>
    <div class="container">
        <div class="card">
            <h2>Edit Weather Report (ID: <?php echo htmlspecialchars($id); ?>)</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger" style="color: red; background: #fde8e8; padding: 10px; margin-bottom: 15px; border-radius: 4px; border: 1px solid #f5c6cb;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" id="edit-form">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="city" style="display:block; font-weight:bold; margin-bottom:5px;">City Name</label>
                    <input type="text" name="city" id="city" required value="<?php echo $city; ?>" style="width:100%; padding:8px; box-sizing:border-box;">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="temp" style="display:block; font-weight:bold; margin-bottom:5px;">Temperature</label>
                    <input type="text" name="temp" id="id" required value="<?php echo $temp; ?>" style="width:100%; padding:8px; box-sizing:border-box;">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="cond" style="display:block; font-weight:bold; margin-bottom:5px;">Weather Condition</label>
                    <textarea name="cond" id="cond" rows="5" required style="width:100%; padding:8px; box-sizing:border-box;"><?php echo $cond; ?></textarea>
                </div>
                <div style="display:flex; gap:10px;">
                    <button type="submit" class="btn btn-success" style="padding: 10px 20px; background: #2ecc71; color: white; border: none; cursor: pointer; border-radius: 4px;">Update Report</button>
                    <a href="index.php" class="btn btn-primary" style="padding: 10px 20px; background: #0984e3; color: white; text-decoration: none; border-radius: 4px;">Cancel</a>
                </div>
            </form>
        </div>
        
        <div class="card" style="border-left:4px solid #e74c3c; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-top: 20px;">
            <h3 style="color:#e74c3c; margin-top:0;">Danger Zone</h3>
            <p style="margin-bottom:15px; color:#555;">Once you delete a weather report, there is no going back.</p>
            <a href="delete_report.php?id=<?php echo $id; ?>" class="btn btn-sm btn-danger" style="padding: 8px 15px; background: #e74c3c; color: white; text-decoration: none; border-radius: 4px;" onclick="return confirm('Are you sure you want to delete this report permanently?')">Delete this report</a>
        </div>
    </div>
</body>
</html>