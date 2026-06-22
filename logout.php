<?php
require __DIR__ . '/config.php';

// সেশনের সব ডাটা খালি করা হচ্ছে
$_SESSION = array();

// ব্রাউজার থেকে সেশন কুকিটি সম্পূর্ণ মুছে ফেলার ব্যবস্থা
if (ini_get("session_use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// সেশন ডেস্ট্রয় করা হলো
session_destroy();

// লগআউট শেষে রিডাইরেক্ট করে মেসেজসহ হোমপেজে পাঠানো হচ্ছে
header("Location: index.php?msg=" . urlencode('Logged out successfully from Weather Station') . "&type=success");
exit;