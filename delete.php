<?php
require __DIR__ . '/config.php';

// ইউজার লগইন না থাকলে ডিলিট করতে দেবে না
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=Please+login+to+delete+reports&type=error");
    exit;
}

$id = $_GET['id'] ?? '';

if ($id === '') {
    header("Location: index.php?msg=" . urlencode('Invalid report ID') . "&type=error");
    exit;
}

// 🚨 SQL INJECTION VULNERABILITY: $id সরাসরি কুয়েরিতে কনক্যাটিনেট করা হয়েছে
$sql = "SELECT id FROM weather_reports WHERE id = '$id'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) === 0) {
    header("Location: index.php?msg=" . urlencode('Weather report not found') . "&type=error");
    exit;
}

// 🚨 VULNERABLE TO SQLi & GET-based CSRF: সরাসরি GET রিকোয়েস্টেই ডিলিট কুয়েরি এক্সিকিউট হচ্ছে
$sql = "DELETE FROM weather_reports WHERE id = '$id'";
$deleted = mysqli_query($conn, $sql);

if ($deleted) {
    header("Location: index.php?msg=" . urlencode('Weather report deleted successfully') . "&type=success");
    exit;
} else {
    header("Location: index.php?msg=" . urlencode('Failed to delete report: ' . mysqli_error($conn)) . "&type=error");
    exit;
}