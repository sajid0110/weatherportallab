<?php
// Start session for auth and CSRF demos
// In this lab, we explicitly disable HttpOnly so that JavaScript can read the cookie for the XSS demo.
session_start([
    'cookie_httponly' => false,
]);

// ল্যাব এনভায়রনমেন্টে ডিবাগিং এর সুবিধার জন্য এরর রিপোর্টিং অন রাখা হলো
error_reporting(E_ALL);
ini_set('display_errors', 1);

// এনভায়রনমেন্ট ভ্যারিয়েবল অথবা ডিফল্ট লোকালহোস্ট ভ্যালু সেটআপ
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';             // ল্যাবের জন্য ডিফল্ট 'root' রাখা হলো
$pass = getenv('DB_PASS') ?: '';                 // XAMPP-এর ডিফল্ট পাসওয়ার্ড ফাঁকা থাকে
$name = getenv('DB_NAME') ?: 'weather_portal';   // ডাটাবেজের নাম পরিবর্তন করে weather_portal করা হলো

$conn = mysqli_connect($host, $user, $pass, $name);
if (!$conn) {
    error_log("DB connection failed: " . mysqli_connect_error());
    die("Cannot connect to the weather network database. Please try again later.");
}

// ক্যারেক্টার সেট utf8mb4 এ সেট করা হলো যেন ডিগ্রি বা অন্যান্য ওয়েদার সিম্বল ঠিকঠাক দেখায়
mysqli_set_charset($conn, 'utf8mb4');