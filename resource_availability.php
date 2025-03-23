//Developed by group leader Waruna Munasinghe (K2463495)

//This PHP script allows an admin user to view and manage the availability of resources, such as rooms, in a MySQL database. 
It starts by checking if the user is logged in and has the role of "admin," 
then establishes a connection to the database to fetch resource availability data, which is displayed in a table format. 
Each row in the table shows the floor, room name, and current status (available or unavailable) of the resources, 
along with a button to toggle the status. 
When the toggle button is clicked, 
an AJAX request is sent to a separate PHP script (update_resource_status.php) to update the status in the database without refreshing the page. 
The page is styled with Bootstrap and custom CSS for a clean and user-friendly interface, enhancing the overall experience for the admin user.

<?php
session_start();

// Check if the user is logged in and is a admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: all_admin_login.php");
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
            <a href="admin_homepage.php" class="back">Back</a>
        </div>
        <!--Resource form for all admins-->
        <div class="container mt-4">
            <h2 class="text-center mb-4">Resource Availability (Admin)</h2>
            
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Floor</th>
                        <th>Room Name</th>
                        <th>Status</th>
                        <th>Action</th>
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
                            <td>
                                <button class="btn btn-sm btn-primary update-status-btn"
                                        data-id="<?= $row['id'] ?>" 
                                        data-status="<?= $row['status'] ?>">
                                    Toggle Status
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <script>
        $(document).ready(function () {
            $(".update-status-btn").click(function () {
                let roomId = $(this).data("id");
                let currentStatus = $(this).data("status");
                let newStatus = (currentStatus === "Available") ? "Unavailable" : "Available";

                $.ajax({
                    url: "update_resource_status.php",
                    type: "POST",
                    data: { id: roomId, status: newStatus },
                    success: function (response) {
                        if (response.trim() === "success") {
                            location.reload(); // Refresh page after update
                        } else {
                            alert("Error updating status: " + response);
                        }
                    }
                });
            });
        });
        </script>
    </body>
</html>
