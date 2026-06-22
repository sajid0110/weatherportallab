<?php
require 'config.php';

// নতুন ওয়েডার রিপোর্ট অ্যাড করার লজিক (CSRF টার্গেট ফর্ম)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 🚨 2. CSRF Vulnerability: কোনো টোকেন চেক করা হচ্ছে না!
    $city = $_POST['city'] ?? '';
    $temp = $_POST['temp'] ?? '';
    $cond = $_POST['cond'] ?? '';
    
    mysqli_query($conn, "INSERT INTO weather_reports (city_name, temperature, condition_text) VALUES ('$city', '$temp', '$cond')");
    header("Location: index.php?msg=Report Added!");
    exit;
}

$search = $_GET['search'] ?? '';
// 🚨 3. SQL Injection in Search
$sql = $search ? "SELECT * FROM weather_reports WHERE city_name LIKE '%$search%'" : "SELECT * FROM weather_reports";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head><title>Dashboard - Weather Portal</title></head>
<body style="font-family: Arial; margin: 30px;">
    <h1>🌦️ Weather Portal Dashboard</h1>
    
    <?php if($search): ?>
        <h3>Search results for: <?php echo $search; ?></h3>
    <?php endif; ?>

    <form method="GET">
        <input type="text" name="search" placeholder="Search city...">
        <button type="submit">Search</button>
    </form>

    <hr>

    <h3>Current Weather Reports</h3>
    <table border="1" cellpadding="10">
        <tr><th>City</th><th>Temp</th><th>Condition</th></tr>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['city_name']; ?></td>
                <td><?php echo $row['temperature']; ?></td>
                <td><?php echo $row['condition_text']; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <hr>

    <h3>Add New Weather Report (Staff Only)</h3>
    <form method="POST" action="index.php">
        City: <input type="text" name="city" required><br><br>
        Temp: <input type="text" name="temp" required><br><br>
        Condition: <input type="text" name="cond" required><br><br>
        <button type="submit">Submit Report</button>
    </form>

    <p style="background:#fff3cd; padding:10px;">
        <b>🎯 XSS Attack:</b> Search for <code>&lt;script&gt;alert(document.cookie)&lt;/script&gt;</code><br>
        <b>🎯 CSRF Attack:</b> অন্য একটি লোকাল ফাইলে একটি ফেক ফর্ম বানিয়ে এই অ্যাকশনে (index.php) পোস্ট রিকোয়েস্ট পাঠান। সেশন অন থাকলে ব্যাকগ্রাউন্ডে ডাটা সাবমিট হয়ে যাবে।
    </p>
</body>
</html>