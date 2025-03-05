<?php
session_start();

// Database connection
$servername = "localhost"; // Change if necessary
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "uniemls"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usernameOrEmail = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT id, password_hash, role FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
    $stmt->execute();
    $stmt->store_result();

    // Check if user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userId, $hashedPassword, $role);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashedPassword)) {
            // Set session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $userId; // Set user ID from the fetched result
            $_SESSION['username'] = $usernameOrEmail; // You might want to store the actual username instead
            $_SESSION['role'] = $role;

            // Redirect based on role
            if ($role === 'lecturer') {
                header("Location: lecturer_homepage.php");
                exit;
            } elseif ($role === 'student') {
                header("Location: student_homepage.php");
                exit;
            } else {
                $error = "You do not have permission to access this page.";
            }
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No user found with that username or email.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>User Login (Students & Lecturers)</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-image: url('op.jpg');
                display: flex;
                background-size: cover;
                justify-content: center;
                align-items: center;
                height: 80vh;
                margin: 0;
            }
            .login-container {
                background: white;
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                width: 400px;
            }
            .form-group {
                margin-bottom: 15px;
            }
            label {
                display: block;
                margin-bottom: 5px;
            }
            input[type="text"],
            input[type="password"] {
                width: 100%;
                padding: 8px;
                box-sizing: border-box;
            }
            button {
                width: 100%;
                padding: 10px;
                background-color: rgb(45, 131, 230);
                border: none;
                color: white;
                font-size: 16px;
                cursor: pointer;
            }
            button:hover {
                background-color: rgb(45, 131, 230);
            }
            .error {
                color: red;
                margin-top: 10px;
            }
        </style>
    </head>
    <body>
        <!--Current user login form for lecturer & student-->
        <div class="login-container">
            <h2>User Login (Students & Lecturers)</h2>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="username">Enter Email:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Enter Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">Login</button>
                <div class="text-center mt-3">
                        <p class="mb-1">
                            If you don't have login credentials, Plaese go to register page! <a href="user_register_form.php" class="text-decoration-none">New user register</a>
                        </p>
                        <p class="mb-0">
                            <a href="homepage.php" class="text-decoration-none">Go to home</a>
                        </p>
                </div>
                <?php if (isset($error)): ?>
                    <div class="error"><?php echo $error; ?></div>
                <?php endif; ?>
            </form>
        </div>
    </body>
</html>