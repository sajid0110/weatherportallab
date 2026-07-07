<?php
require __DIR__ . '/config.php';

// নতুন ওয়েদার রিপোর্ট অ্যাড করার লজিক (CSRF টার্গেট ফর্ম)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 🚨 1. CSRF Vulnerability: কোনো অ্যান্টি-CSRF টোকেন চেক করা হচ্ছে না!
    $city = $_POST['city'] ?? '';
    $temp = $_POST['temp'] ?? '';
    $cond = $_POST['cond'] ?? '';
    
    // কেটে যাওয়া কুয়েরিটি এখানে ফিক্স করা হলো
    $insertSql = "INSERT INTO weather_reports (city_name, temperature, condition_text, user_id) VALUES ('$city', '$temp', '$cond', 2)";
    mysqli_query($conn, $insertSql);
    
    header("Location: index.php?msg=Report+Added+Successfully&type=success");
    exit;
}

$search = $_GET['search'] ?? '';
$msg = $_GET['msg'] ?? '';
$msgType = $_GET['type'] ?? 'success';

// 🚨 2. SQL Injection in Search: ইনপুট স্যানিটাইজ না করেই সরাসরি LIKE কুয়েরি
if ($search !== '') {
    $sql = "SELECT * FROM weather_reports WHERE city_name LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM weather_reports ORDER BY id DESC";
}
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Weather Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1><a href="index.php">🌦️ Vulnerable Weather Portal</a></h1>
            <nav>
                <a href="index.php">Home</a>
                <a href="profil.php">Profile</a>
                <a href="login.php">Login</a>
<a href="edit.php">edit</a>
<a href="change_email.php" style="color: #e94560; font-weight: bold;"> Update Email (CSRF Test)</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <?php if ($msg): ?>
            <div class="alert alert-<?php echo $msgType === 'error' ? 'danger' : 'success'; ?>">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>

        <div class="toolbar card">
            <form method="GET" action="index.php" class="search-bar" style="width: 100%;">
                <input type="text" name="search" placeholder="Search city (e.g., Dhaka)..." value="<?php echo $search; ?>">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>

        <?php if ($search !== ''): ?>
            <div style="margin-bottom: 20px;">
                <h3>Search results for: <?php echo $search; ?></h3>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2>Current Weather Reports</h2>
            <hr style="margin-bottom: 20px; border:0; border-top:1px solid #eee;">
            
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <div class="post">
                        <h3>📍 <?php echo htmlspecialchars($row['city_name']); ?></h3>
                        <div class="content">
                            <strong>Temperature:</strong> <?php echo htmlspecialchars($row['temperature']); ?><br>
                            <strong>Condition:</strong> <?php echo $row['condition_text']; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">No reports found.</div>
            <?php endif; ?>
        </div>

        <div class="card" style="max-width: 600px; margin-top: 30px;">
            <h3>Add New Weather Report (Staff Only)</h3>
            <p style="font-size: 0.85em; color: #e74c3c; margin-bottom: 15px; font-weight: bold;">
                ⚠️ Lab Warning: This form lacks CSRF Tokens!
            </p>
            
            <form method="POST" action="index.php">
                <div class="form-group">
                    <label>City Name</label>
                    <input type="text" name="city" required placeholder="e.g., Sylhet">
                </div>
                <div class="form-group">
                    <label>Temperature</label>
                    <input type="text" name="temp" required placeholder="e.g., 28°C">
                </div>
                <div class="form-group">
                    <label>Condition</label>
                    <textarea name="cond" required placeholder="e.g., Heavy Rain" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-success">Submit Report</button>
            </form>
        </div>


            </ul>
        </div>
    </div>
</body>
</html>
