<?php

require __DIR__ . '/config.php';



$error = '';

$query_debug = '';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'] ?? '';

    $password = $_POST['password'] ?? '';



    

    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";

    $query_debug = $sql; 

    

    $result = mysqli_query($conn, $sql);



    if ($result && mysqli_num_rows($result) > 0) {

       

        $user = mysqli_fetch_assoc($result);

        $_SESSION['user_id'] = $user['id'];

        $_SESSION['username'] = $user['username'];



      

        header("Location: index.php?msg=Login+Successful!&type=success");

        exit;

    } else {

        $error = "Login Failed! Invalid username or password.";

    }

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Staff Login - Weather Portal</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

    <header>

        <div class="container">

            <h1><a href="index.php">🌦️ Weather Portal</a></h1>

            <nav>

                <a href="index.php">Home</a>

            </nav>

        </div>

    </header>



    <div class="container" style="max-width: 450px; margin-top: 50px;">

        <div class="card">

            <h2>Staff Login</h2>

            <p style="color: #718096; font-size: 0.9em; margin-bottom: 20px;">Please enter your credentials to access the station manager.</p>



            <?php if ($error): ?>

                <div class="alert alert-danger" style="font-size: 0.9em; padding: 10px 15px;">

                    <strong><?php echo $error; ?></strong>

                    <br><small style="font-family: monospace; display:block; margin-top:5px;">Query: <?php echo htmlspecialchars($query_debug); ?></small>

                </div>

            <?php endif; ?>



            <form method="POST" action="login.php">

                <div class="form-group">

                    <label>Username</label>

                    <input type="text" name="username" required placeholder="e.g., admin">

                </div>

                <div class="form-group">

                    <label>Password</label>

                    <input type="password" name="password" required placeholder="••••••••">

                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">Login to Station</button>

            </form>

        </div>

    </div>

</body>

</html>
