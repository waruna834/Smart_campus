<?php
session_start();

// Check if the user is logged in and is a student
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'student') {
    header("Location: users_login.php");
    exit;
}

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_id = $_POST['event_id'];
    $student_id = $_SESSION['user_id'];
    $status = $_POST['attendance_status'];

    // Check if the student is already registered for this event
    $checkQuery = "SELECT id FROM event_attendance WHERE event_id = ? AND student_id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("ii", $event_id, $student_id);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        echo "<script>alert('You have already registered for this event!'); window.location.href='student_event.php';</script>";
        exit();
    }

    // Check if seats are available
    $checkSeats = $conn->prepare("SELECT max_attendees, current_attendees FROM events WHERE id = ?");
    $checkSeats->bind_param("i", $event_id);
    $checkSeats->execute();
    $result = $checkSeats->get_result();
    $row = $result->fetch_assoc();

    if ($row['current_attendees'] < $row['max_attendees']) {
        // Insert attendance record
        $query = "INSERT INTO event_attendance (event_id, student_id, attendance_status) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iis", $event_id, $student_id, $status);
        $stmt->execute();

        // Update event's current attendee count
        $updateCount = $conn->prepare("UPDATE events SET current_attendees = current_attendees + 1 WHERE id = ?");
        $updateCount->bind_param("i", $event_id);
        $updateCount->execute();

        echo "<script>alert('Attendance Recorded Successfully!'); window.location.href='student_event.php';</script>";
    } else {
        echo "<script>alert('Sorry, this event is already full!'); window.location.href='student_event.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <title>Admin Event Registration</title>
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
                margin: 0 15px; /* Increased margin for better spacing */
                text-decoration: none;
                font-weight: bold; /* Make the links bold */
            }

            .container {
                max-width: 600px; /* Set a max width for the container */
                margin: auto;
                background: white;
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                margin-top: 20px; /* Enhanced shadow */
            }

            .container1 {
                max-width: 700px; /* Set a max width for the container */
                margin: auto;
                background: white;
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                margin-top: 20px; /* Enhanced shadow */
            }

            .container1 {
                max-width: 700px; /* Set a max width for the container */
                margin: auto;
                background: white;
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                margin-top: 20px; /* Enhanced shadow */
            }

            h2 {
                text-align: center;
                margin-bottom: 20px; /* Space below the heading */
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
                font-weight: bold; /* Make labels bold */
            }

            select[name="event_id"],
            select[name="attendance_status"] {
                width: 100%; /* Full width for inputs */
                padding: 10px; /* Padding for inputs */
                margin-bottom: 15px; /* Space below inputs */
                border: 1px solid #ccc; /* Light border */
                border-radius: 4px; /* Rounded corners */
            }

            button {
                width: 100%;
                padding: 10px;
                background-color: #007bff; /* Blue color */
                border: none;
                color: white;
                font-size: 16px;
                cursor: pointer;
                border-radius: 4px;
                transition: background-color 0.3s; /* Smooth transition */
            }

            button:hover {
                background-color: #0056b3; /* Darker blue on hover */
            }

            .error {
                color: red;
                margin-top: 10px;
            }

            .success {
                color: green;
                margin-top: 10px;
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
        <div class="container">
            <h2>New Event Registration Form</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <label for="name">Select Event Name:</label>
                <select name="event_id" required>
                    <?php
                    $result = $conn->query("SELECT *, (max_attendees - current_attendees) AS remaining FROM events ORDER BY event_date");
                    while ($row = $result->fetch_assoc()) {
                        $remainingSeats = $row['remaining'];
                        if ($remainingSeats > 0) {
                            echo "<option value='".$row['id']."'>".$row['event_name']." (Seats Left: ".$remainingSeats.")</option>";
                        } else {
                            echo "<option disabled>".$row['event_name']." (Full)</option>";
                        }
                    }
                    ?>
                </select>
                <br>
                <label for="status">Select Attendance Status:</label>
                <select name="attendance_status">
                    <option value="Present">Present</option>
                </select>
                <br>
                <button type="submit">Submit Attendance</button>
            </form>
        </div>
        <!-- Attended Events Table -->
        <div class="container1">
            <h2>My Event Attendance</h2>
            <table>
                <tr>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Attendance Status</th>
                    <th>Action</th>
                </tr>
                <?php
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
                
                $student_id = $_SESSION['user_id'];

                $query = "SELECT e.event_name, e.event_date, e.event_time, e.event_location, a.attendance_status, a.id 
                        FROM event_attendance a
                        JOIN events e ON a.event_id = e.id
                        WHERE a.student_id = ?";
                
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $student_id);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['event_name']}</td>
                            <td>{$row['event_date']}</td>
                            <td>{$row['event_time']}</td>
                            <td>{$row['event_location']}</td>
                            <td>{$row['attendance_status']}</td>
                            <td>
                                <form action='delete_attendance.php' method='POST'>
                                    <input type='hidden' name='attendance_id' value='{$row['id']}'>
                                    <button type='submit' onclick='return confirm(\"Are you sure?\")' class='btn btn-danger btn-sm'>Delete</button>
                                </form>
                            </td>
                        </tr>";
                }
                ?>
            </table>
        </div>

    </body>
</html>
