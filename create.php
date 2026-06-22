<?php
require __DIR__ . '/config.php';

// ইউজার লগইন না থাকলে লগইন পেজে পাঠিয়ে দেবে
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=Please+login+to+create+reports&type=error");
    exit;
}

$city = '';
$temp = '';
$cond = '';
$error = '';

// চেক করা হচ্ছে weather_reports টেবিলে user_id কলাম আছে কিনা (ব্যাকওয়ার্ড কম্প্যাটিবিলিটি)
$checkCol = mysqli_query($conn, "SHOW COLUMNS FROM weather_reports LIKE 'user_id'");
$has_user_id = mysqli_num_rows($checkCol) > 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $city = trim($_POST['city'] ?? '');
    $temp = trim($_POST['temp'] ?? '');
    $cond = trim($_POST['cond'] ?? '');
    $user_id = $_SESSION['user_id'];

    if ($city === '' || $temp === '' || $cond === '') {
        $error = 'Please fill in all fields.';
    } else {
        // 🚨 VULNERABLE QUERY: সরাসরি ভ্যারিয়েবল কনক্যাটিনেট করায় SQL Injection সম্ভব। 
        // এছাড়াও কোনো স্যানিটাইজেশন না থাকায় এখানে Stored XSS-ও কাজ করবে!
        if ($has_user_id) {
            $sql = "INSERT INTO weather_reports (user_id, city_name, temperature, condition_text) VALUES ('$user_id', '$city', '$temp', '$cond')";
        } else {
            $sql = "INSERT INTO weather_reports (city_name, temperature, condition_text) VALUES ('$city', '$temp', '$cond')";
        }
        
        if (mysqli_query($conn, $sql)) {
            header("Location: index.php?msg=" . urlencode('Weather report published successfully') . "&type=success");
            exit;
        } else {
            $error = 'Failed to create report. Please try again: ' . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Report - Weather Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1><a href="index.php">🌦️ Vulnerable Weather Portal</a></h1>
            <nav>
                <a href="index.php">Home</a>
                <a href="create_report.php">New Report</a>
            </nav>
        </div>
    </header>
    <div class="container">
        <div class="card" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
            <h2>Create New Weather Report</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger" style="color: red; background: #fde8e8; padding: 10px; margin-bottom: 15px; border-radius: 4px; border: 1px solid #f5c6cb;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="city" style="display: block; margin-bottom: 5px; font-weight: bold;">City Name</label>
                    <input type="text" name="city" id="city" required value="<?php echo htmlspecialchars($city); ?>" placeholder="Enter city name (e.g. Dhaka)" style="width: 100%; padding: 8px; box-sizing: border-box;">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="temp" style="display: block; margin-bottom: 5px; font-weight: bold;">Temperature</label>
                    <input type="text" name="temp" id="temp" required value="<?php echo htmlspecialchars($temp); ?>" placeholder="Enter temperature (e.g. 32°C)" style="width: 100%; padding: 8px; box-sizing: border-box;">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="cond" style="display: block; margin-bottom: 5px; font-weight: bold;">Weather Condition</label>
                    <textarea name="cond" id="cond" rows="6" required placeholder="Write weather descriptions or warnings here..." style="width: 100%; padding: 8px; box-sizing: border-box;"><?php echo htmlspecialchars($cond); ?></textarea>
                </div>
                <div style="display:flex; gap:10px;">
                    <button type="submit" class="btn btn-success" style="padding: 10px 20px; background: #2ecc71; color: white; border: none; cursor: pointer; border-radius: 4px;">Publish Report</button>
                    <a href="index.php" class="btn btn-primary" style="padding: 10px 20px; background: #0984e3; color: white; text-decoration: none; border-radius: 4px;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>