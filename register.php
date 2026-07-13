<?php
include 'config.php';

if(isset($_POST['register'])){

    // password_hash বাদ দিয়ে সরাসরি ফর্ম থেকে আসা পাসওয়ার্ডটি ভ্যারিয়েবলে নেওয়া হলো
    $password = $_POST['password']; 
    $username = $_POST['username'];
    $email = $_POST['email'];

    // SQL কোয়েরি (সরাসরি প্লেইন টেক্সট পাসওয়ার্ড ইনসার্ট হবে)
    $conn->query("
        INSERT INTO users (username, email, password)
        VALUES (
            '$username',
            '$email',
            '$password'
        )
    ");

    echo "<p style='color: green;'>Registration successful! Plain-text password added to database.</p>";
}
?>

<form method="POST">
    <div>
        <label>Username:</label><br>
        <input name="username" type="text" placeholder="Enter Username" required>
    </div><br>

    <div>
        <label>Email:</label><br>
        <input name="email" type="email" placeholder="Enter Email" required>
    </div><br>

    <div>
        <label>Password:</label><br>
        <input name="password" type="password" placeholder="Enter Password" required>
    </div><br>

    <button name="register" type="submit">Register</button>
</form>
