<?php
session_start(); // Start the session to access user login details

// Check if the user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: all_admin_login.php");
    exit;
}

// Database connection
$servername = "localhost"; // Change if necessary
$username = "root"; // Change if necessary
$password = ""; // Change if necessary
$dbname = "uniemls"; // Change to your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all subjects for students and lecturers
$student_subjects = $conn->query("SELECT * FROM subjects WHERE role = 'student' ORDER BY created_at DESC");
$lecturer_subjects = $conn->query("SELECT * FROM subjects WHERE role = 'lecturer' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Subject Registration Details</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
                max-width: 1500px;
                margin: auto;
                background: white;
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                margin-top: 20px;
            }

            h2 {
                text-align: center;
            }

            .table-container {
                display: flex;
                justify-content: space-between;
            }

            .student-table {
                width: 49%; /* Adjust width for student table */
            }

            .lecturer-table {
                width: 49%; /* Adjust width for lecturer table */
            }

            .search-bar {
                margin-bottom: 20px;
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
    <div class="container mt-5">
        <h2><strong>Subject Registration Details</strong></h2><br>

        <div class="table-container">
            <!-- Student Subjects Table -->
            <div class="student-table">
                <h3>Student Subjects Registration</h3>
                <input type="text" id="studentSearch" class="form-control search-bar" placeholder="Search Student Subjects...">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Faculty</th>
                            <th>Program</th>
                            <th>Subject</th>
                            <th>Registered At</th>
                        </tr>
                    </thead>
                    <tbody id="studentTableBody">
                        <?php if ($student_subjects->num_rows > 0): ?>
                            <?php while($row = $student_subjects->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['faculty']); ?></td>
                                    <td><?php echo htmlspecialchars($row['program']); ?></td>
                                    <td><?php echo htmlspecialchars($row['subject']); ?></td>
                                    <td><?php echo $row['created_at']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No subjects registered yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Lecturer Subjects Table -->
            <div class="lecturer-table">
                <h3>Lecturer Subjects Assign</h3>
                <input type="text" id="lecturerSearch" class="form-control search-bar" placeholder="Search Lecturer Subjects...">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Faculty</th>
                            <th>Program</th>
                            <th>Subject</th>
                            <th>Assigned At</th>
                        </tr>
                    </thead>
                    <tbody id="lecturerTableBody">
                        <?php if ($lecturer_subjects->num_rows > 0): ?>
                            <?php while($row = $lecturer_subjects->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['faculty']); ?></td>
                                    <td><?php echo htmlspecialchars($row['program']); ?></td>
                                    <td><?php echo htmlspecialchars($row['subject']); ?></td>
                                    <td><?php echo $row['created_at']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No subjects registered yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Search functionality for student subjects
        document.getElementById('studentSearch').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#studentTableBody tr');
            rows.forEach(row => {
                let cells = row.getElementsByTagName('td');
                let match = Array.from(cells).some(cell => cell.textContent.toLowerCase().includes(filter));
                row.style.display = match ? '' : 'none';
            });
        });

        // Search functionality for lecturer subjects
        document.getElementById('lecturerSearch').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#lecturerTableBody tr');
            rows.forEach(row => {
                let cells = row.getElementsByTagName('td');
                let match = Array.from(cells).some(cell => cell.textContent.toLowerCase().includes(filter));
                row.style.display = match ? '' : 'none';
            });
        });
    </script>
    </body>
</html>

<?php
$conn->close();
?>