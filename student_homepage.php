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

// Fetch user information
$userId = $_SESSION['user_id']; // Assuming user_id is stored in session
$userQuery = "SELECT username, role FROM users WHERE id = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();

// Fetch all data initially
$sql = "SELECT * FROM program_schedule ORDER BY start_date";
$result = $conn->query($sql);

// Store the data into an array
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Check if delete is requested
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Delete the row with the given id
    $delete_sql = "DELETE FROM registration WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $delete_id);
    
    if ($delete_stmt->execute()) {
        echo "Record deleted successfully!";
    } else {
        echo "Error deleting record: " . $delete_stmt->error;
    }

    $delete_stmt->close();
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>View Program Schedule - For student</title>
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
            .table-container {
                max-width: 1400px; /* Set a max width for the container */
                margin: auto;
                background: white;
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                margin-top: 20px; /* Space between navbar and container */
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            table, th, td {
                border: 1px solid #ddd;
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
            .search-bar {
                width: 25%;
                padding: 8px;
                margin-bottom: 20px;
            }
            .status-text {
                color: red;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <!-- Navigation Bar -->
        <div class="navbar">
            <span>Welcome <?php echo htmlspecialchars($user['username']); ?> (<?php echo htmlspecialchars($user['role']); ?>)</span>
            <a href="student_subject_register.php">New Subject Register</a>
            <a href="student_resource_availability.php">Resource Availability</a>
            <a href="student_event.php">Register For Event</a>
            <a href="student_messages.php">Messages</a>
            <a href="users_login.php">Logout</a>
        </div>

        <!-- Search Form -->
        <div class="table-container">
            <h2>View Program Schedule</h2>
            <input type="text" id="search" class="search-bar" placeholder="Search by Faculty, Program, Subject, Batch, Classroom..." onkeyup="searchData()">
            
            <!-- Displaying the Table -->
            <table id="schedule-table">
                <thead>
                    <tr>
                        <th>Faculty</th>
                        <th>Program Type</th>
                        <th>Subject</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Duration (In days)</th>
                        <th>Batch</th>
                        <th>Classroom</th>
                        <th>Days of the Week</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['faculty']); ?></td>
                            <td><?php echo htmlspecialchars($row['program_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['subject']); ?></td>
                            <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['end_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['duration']); ?></td>
                            <td><?php echo htmlspecialchars($row['batch']); ?></td>
                            <td><?php echo htmlspecialchars($row['classroom']); ?></td>
                            <td><?php echo htmlspecialchars($row['days_of_week']); ?></td>
                            <td><?php echo htmlspecialchars($row['start_time']); ?></td>
                            <td><?php echo htmlspecialchars($row['end_time']); ?></td>
                            <td class="status-text"><?php echo htmlspecialchars($row['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <script>
            // JavaScript function to filter the data based on search input
            function searchData() {
                const searchValue = document.getElementById("search").value.toLowerCase();
                const rows = document.getElementById("schedule-table").getElementsByTagName("tr");

                // Loop through the table rows and hide those that do not match the search value
                for (let i = 1; i < rows.length; i++) { // Starting from 1 to skip the header row
                    let row = rows[i];
                    let columns = row.getElementsByTagName("td");
                    let match = false;

                    // Loop through each cell in the row and check if it matches the search value
                    for (let j = 0; j < columns.length; j++) {
                        if (columns[j].innerText.toLowerCase().includes(searchValue)) {
                            match = true;
                            break;
                        }
                    }

                    // Show or hide the row based on the match
                    if (match) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }
                }
            }
        </script>
    </body>
</html>