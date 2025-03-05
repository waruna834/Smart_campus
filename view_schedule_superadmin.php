<?php
session_start();

// Check if the user is logged in and is a super admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'super_admin') {
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

// Fetch all data initially
$sql = "SELECT * FROM program_schedule ORDER BY start_date";
$result = $conn->query($sql);

// Store the data into an array
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>View Program Schedule - For super admin</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-image: url('hm3.jpg');
                background-size: cover; /* Ensures the image covers the entire viewport */
                background-position: center; /* Centers the image */
                background-repeat: no-repeat; /* Prevents the image from repeating */
                background-attachment: fixed; /* Keeps the background image fixed during scroll */
                margin: 0; /* Remove default margin */
                padding: 0; /* Remove default padding */
                height: 100vh; /* Ensures the body takes the full height of the viewport */
            }
            h1 {
                text-align: center;
            }
            /* Navigation Bar Styles */
            .navbar {
                background-color: #5cb85c;
                overflow: hidden;
                padding: 10px;
            }
            .navbar a {
                float: left;
                display: block;
                color: white;
                text-align: center;
                padding: 14px 16px;
                text-decoration: none;
            }
            .navbar a:hover {
                background-color: #4cae4c;
            }
            .navbar .logout {
                float: right; /* Align logout button to the right */
            }
            .table-container {
                max-width: 1400px;
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
                margin-top: 20px; /* Space between table and content above */
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #5cb85c;
                color: white;
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
            <a href="super_admin_homepage.php" class="back">Back</a>
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
                            <td><?php echo $row['faculty']; ?></td>
                            <td><?php echo $row['program_type']; ?></td>
                            <td><?php echo $row['subject']; ?></td>
                            <td><?php echo $row['start_date']; ?></td>
                            <td><?php echo $row['end_date']; ?></td>
                            <td><?php echo $row['duration']; ?></td>
                            <td><?php echo $row['batch']; ?></td>
                            <td><?php echo $row['classroom']; ?></td>
                            <td><?php echo $row['days_of_week']; ?></td>
                            <td><?php echo $row['start_time']; ?></td>
                            <td><?php echo $row['end_time']; ?></td>
                            <td class="status-text"><?php echo $row['status']; ?></td>
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
