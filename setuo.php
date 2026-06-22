<?php
require __DIR__ . '/config.php';

echo "<h2>🌦️ Weather Portal - Database & Lab Setup</h2>";

// ১. users টেবিল তৈরি করা হচ্ছে
$sqlUsers = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $sqlUsers)) {
    echo "✅ Table 'users' created or already exists.<br>";
} else {
    echo "❌ Error creating table 'users': " . mysqli_error($conn) . "<br>";
}

// ২. weather_reports টেবিল তৈরি করা হচ্ছে (posts টেবিলের পরিবর্তে)
$sqlReports = "CREATE TABLE IF NOT EXISTS weather_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,          /* ব্যাকওয়ার্ড কম্প্যাটিবিলিটি ও কোড ম্যাচিং এর জন্য রাখা হলো */
    city_name VARCHAR(100) NOT NULL,
    temperature VARCHAR(50) NOT NULL,
    condition_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $sqlReports)) {
    echo "✅ Table 'weather_reports' created or already exists.<br>";
} else {
    echo "❌ Error creating table 'weather_reports': " . mysqli_error($conn) . "<br>";
}

// ৩. weather_reports টেবিলে user_id কলাম চেক ও অ্যাড করা (CSRF ল্যাব ও ব্যাকওয়ার্ড কম্প্যাটিবিলিটির জন্য)
$checkCol = mysqli_query($conn, "SHOW COLUMNS FROM weather_reports LIKE 'user_id'");
if (mysqli_num_rows($checkCol) === 0) {
    $alterSql = "ALTER TABLE weather_reports ADD COLUMN user_id INT NOT NULL DEFAULT 1";
    if (mysqli_query($conn, $alterSql)) {
        echo "🔹 Column 'user_id' added to weather_reports table.<br>";
    } else {
        echo "❌ Error adding user_id column: " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "🔹 Column 'user_id' already exists in weather_reports.<br>";
}

// ৪. ডিফল্ট 'admin' ইউজার তৈরি করা (যদি না থাকে)
$checkAdmin = mysqli_query($conn, "SELECT id FROM users WHERE username = 'admin'");
if (mysqli_num_rows($checkAdmin) === 0) {
    $insertAdmin = "INSERT INTO users (username, password) VALUES ('admin', 'password')";
    if (mysqli_query($conn, $insertAdmin)) {
        echo "👤 Default admin user created (admin/password).<br>";
    }
} else {
    echo "👤 Admin user already exists.<br>";
}

// ৫. ডিফল্ট 'victim' ইউজার তৈরি করা (যদি না থাকে)
$checkVictim = mysqli_query($conn, "SELECT id FROM users WHERE username = 'victim'");
if (mysqli_num_rows($checkVictim) === 0) {
    $insertVictim = "INSERT INTO users (username, password) VALUES ('victim', 'victim123');";
    if (mysqli_query($conn, $insertVictim)) {
        echo "👤 Default victim user created (victim/victim123).<br>";
    } else {
        echo "❌ Error creating victim user: " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "👤 Victim user already exists.<br>";
}

// ৬. অ্যাটাকার ইউজার 'shifat' তৈরি করা (যদি না থাকে)
$checkShifat = mysqli_query($conn, "SELECT id FROM users WHERE username = 'shifat'");
if (mysqli_num_rows($checkShifat) === 0) {
    $insertShifat = "INSERT INTO users (username, password) VALUES ('shifat', '1234')";
    if (mysqli_query($conn, $insertShifat)) {
        echo "👤 Attacker user 'shifat' created (shifat/1234).<br>";
    } else {
        echo "❌ Error creating shifat user: " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "👤 Shifat user already exists.<br>";
}

// ৭. ডেমো ওয়েডার রিপোর্ট ইনসার্ট করা (যদি টেবিল খালি থাকে)
$checkReports = mysqli_query($conn, "SELECT COUNT(*) as count FROM weather_reports");
$row = mysqli_fetch_assoc($checkReports);
if ($row['count'] == 0) {
    $insertReport = "INSERT INTO weather_reports (user_id, title, city_name, temperature, condition_text) 
                     VALUES (2, 'Dhaka Weather Station', 'Dhaka', '32°C', 'Sunny and clear sky. No sign of rain today.')";
    if (mysqli_query($conn, $insertReport)) {
        echo "🌦️ Default weather report created by victim.<br>";
    }
} else {
    echo "🌦️ Weather reports already exist.<br>";
}

echo "<br>🚀 <b>Lab Setup Complete!</b> <a href='index.php'>Go to Weather Dashboard</a>";