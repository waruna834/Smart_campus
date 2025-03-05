<?php
$servername = "localhost"; // Change if necessary
$username = "root"; // Change if necessary
$password = ""; // Change if necessary
$dbname = "uniemls";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch announcements
$result = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Homepage</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-image: url('hm3.jpg'); /* Replace with your image path */
                background-size: cover; /* Cover the entire viewport */
                background-position: center; /* Center the image */
                background-repeat: no-repeat;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                color: white; /* Change text color for better contrast */
            }
            
            .container {
                text-align: center;
                background: rgba(129, 125, 125, 0.9); /* Semi-transparent white background */
                padding: 40px;
                border-radius: 8px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                width: 80%; /* Set a width for the container */
            }

            h1 {
                margin-bottom: 20px;
            }

            .button {
                display: inline-block;
                padding: 15px 30px;
                margin: 10px;
                font-size: 16px;
                color: white;
                background-color: #007bff; /* Blue color */
                border: none;
                border-radius: 5px;
                text-decoration: none;
                cursor: pointer;
                transition: background-color 0.3s;
            }

            .button:hover {
                background-color: #0056b3; /* Darker blue on hover */
            }

            .announcement-cards {
                display: flex;
                flex-wrap: wrap;
                justify-content: center; /* Center the cards */
                margin-top: 20px;
            }

            .card {
                background: rgba(51, 148, 226, 0.8); /* Semi-transparent white background for cards */
                border-radius: 8px;
                padding: 20px;
                margin: 10px;
                width: 300px; /* Set a fixed width for cards */
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            }

            .button-container {
                text-align: right; /* Align buttons to the right */
                margin-top: 20px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Welcome to Smart Campus Management System</h1>
            <!--Super admin added announcement showing cards-->
            <div class="announcement-cards">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="card">
                            <h3>Title: <?php echo htmlspecialchars($row['title']); ?></h3>
                            <h4>Announcement: <?php echo htmlspecialchars($row['content']); ?></h4>
                            <small>Published Time: <?php echo $row['created_at']; ?></small>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No announcements found.</p>
                <?php endif; ?>
            </div>
            <!--New user register button and current user login button-->
            <div class="button-container">
                <a href="user_register_form.php" class="button">New User Login</a>
                <a href="users_login.php" class="button">Current User Login</a>
            </div>
        </div>

        <?php
        $conn->close();
        ?>
    </body>
</html>