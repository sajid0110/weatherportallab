<?php
require __DIR__ . '/config.php';

// ১. টেবিলটি যদি ডাটাবেজে না থাকে তবে অটোমেটিক তৈরি হবে
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS weather_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// ২. কমেন্ট সাবমিট করার লজিক (🚨 STORED XSS VULNERABILITY)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    $comment_text = $_POST['comment'] ?? '';
    
    // কোনো স্যানিটাইজেশন বা ফিল্টারিং ছাড়া সরাসরি ইনসার্ট
    $commentSql = "INSERT INTO weather_comments (comment) VALUES ('$comment_text')";
    mysqli_query($conn, $commentSql);
    
    header("Location: edit.php?msg=Comment+Posted+Successfully!");
    exit;
}

// ৩. ডাটাবেজ থেকে সব কমেন্ট রিড করা
$commentsResult = mysqli_query($conn, "SELECT * FROM weather_comments ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public Discussion - Weather Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1><a href="index.php">🌦️ Weather Portal Lab</a></h1>
            <nav><a href="index.php">Back to Dashboard</a></nav>
        </div>
    </header>

    <div class="container" style="max-width: 650px; margin-top: 30px;">
        <div class="card">
            <h3>💬 Public Comments & Feedback Box</h3>
            <p style="font-size: 0.85em; color: #e74c3c; margin-bottom: 15px; font-weight: bold;">
                ⚠️ Lab Notice: This section blindly executes any JavaScript stored in the comments!
            </p>

            <form method="POST" action="edit.php" style="margin-bottom: 25px;">
                <div class="form-group">
                    <label>Leave your comment:</label>
                    <textarea name="comment" placeholder="Write a comment or paste your XSS payload here..." rows="4" required></textarea>
                </div>
                <button type="submit" name="submit_comment" class="btn btn-success">Post Comment</button>
            </form>

            <hr style="border:0; border-top:1px solid #eee; margin-bottom: 20px;">

            <h4>All Live Comments:</h4>
            <div class="comments-list" style="margin-top: 15px;">
                <?php if ($commentsResult && mysqli_num_rows($commentsResult) > 0): ?>
                    <?php while($com = mysqli_fetch_assoc($commentsResult)): ?>
                        <div class="post" style="background: #f8f9fa; border-left: 4px solid #cbd5e0; padding: 12px; margin-bottom: 12px; border-radius: 4px;">
                            <small style="color: #a0aec0; float: right; font-size: 0.8em;"><?php echo $com['created_at']; ?></small>
                            
                            <p style="margin: 5px 0 0 0; color: #2d3748; word-wrap: break-word;"><?php echo $com['comment']; ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="color: #a0aec0; font-style: italic; font-size: 0.9em;">No comments yet. Test your payload!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
