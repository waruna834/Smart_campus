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
    $delete_sql = "DELETE FROM program_schedule WHERE id = ?";
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
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <title>View Program Schedule</title>
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
                max-width: 1500px; /* Set a max width for the container */
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
                width: 30%;
                padding: 8px;
                margin-bottom: 20px;
            }
            .delete-btn {
                background-color: #ff4747;
                color: white;
                padding: 5px 10px;
                border: none;
                cursor: pointer;
            }
            .delete-btn:hover {
                background-color: #ff1f1f;
            }
            .edit-btn {
                background-color: #007bff;
                color: white;
                padding: 5px 10px;
                border: none;
                cursor: pointer;
            }
            .edit-btn:hover {
                background-color: #0056b3;
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
            <a href="admin_homepage.php">Back</a>
        </div>

        <!-- Search Form -->
        <div class="table-container">
            <h2>View Program Schedule - For Admin</h2>
            <input type="text" id="search" class="search-bar" placeholder="Search by Faculty, Program, Classroom..." onkeyup="searchData()">
            
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
                        <th>Action</th>
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
                            <td class="status-text"><?php echo $row['status'] ?? ''; ?></td>
                            <td>
                                <!-- Edit Status Button -->
                                <button class="edit-btn" onclick="editStatus(<?php echo $row['id']; ?>)">Edit Status</button>
                                <!-- Delete Button -->
                                <a href="view_schedule.php?delete_id=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this schedule?')">Delete</a>
                            </td>
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

                for (let i = 1; i < rows.length; i++) { // Skip header row
                    let row = rows[i];
                    let columns = row.getElementsByTagName("td");
                    let match = false;

                    for (let j = 0; j < columns.length; j++) {
                        if (columns[j].innerText.toLowerCase().includes(searchValue)) {
                            match = true;
                            break;
                        }
                    }

                    row.style.display = match ? "" : "none";
                }
            }

            // Edit status function
            function editStatus(id) {
                let newStatus = prompt("Enter new status (e.g., Today lecture is canceled):");
                if (newStatus !== null) {
                    let xhr = new XMLHttpRequest();
                    xhr.open("POST", "update_status.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.onload = function () {
                        if (xhr.status == 200) {
                            location.reload(); // Refresh the page after successful update
                        }
                    };
                    xhr.send("id=" + id + "&status=" + encodeURIComponent(newStatus));
                }
            }
        </script>
    </body>
</html>
