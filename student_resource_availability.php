<?php
session_start();

// Check if the user is logged in and is a student
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'student') {
    header("Location: users_login.php");
    exit;
}

// Database connection
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "uniemls"; 

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch resource availability data
$query = "SELECT * FROM resource_availability ORDER BY floor, room_name";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Resource Availability</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-image: url('op.jpg');
                background-size: cover; /* Ensures the image covers the entire viewport */
                background-position: center; /* Centers the image */
                background-repeat: no-repeat; /* Prevents the image from repeating */
                background-attachment: fixed; /* Keeps the background image fixed during scroll */
                margin: 0; /* Remove default margin */
                padding: 0; /* Remove default padding */
                height: 100vh; /* Ensures the body takes the full height of the viewport */
            }

            .navbar {
                background-color: #333;
                padding: 10px;
                color: white;
                text-align: center;
            }
            .navbar a {
                color: white;
                margin: 0 10px;
                text-decoration: none;
            }

            .container {
                max-width: 600px; /* Set a max width for the container */
                margin: auto;
                background: white;
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                margin-top: 20px; /* Space between navbar and container */
            }

            h2 {
                text-align: center;
            }

            form {
                background: white;
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }

            label {
                display: block;
                margin-bottom: 5px;
            }
            
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px; /* Space between table and content above */
            }

            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }

            th {
                background-color: #d9d9d9; /* Grey background for table header */
                color: black; /* Black text for table header */
            }
        </style>
    </head>
    <body>
        <!-- Navigation Bar -->
        <div class="navbar">
            <a href="student_homepage.php" class="back">Back</a>
        </div>
        <!--Resouce table-->
        <div class="container">
            <h2 class="text-center mb-4">Resource Availability</h2>
            
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Floor</th>
                        <th>Room Name</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['floor']) ?></td>
                            <td><?= htmlspecialchars($row['room_name']) ?></td>
                            <td>
                                <span class="badge bg-<?= ($row['status'] == 'Available') ? 'success' : 'danger' ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </body>
</html>
