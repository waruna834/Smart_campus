//Developed by group leader Waruna Munasinghe (K2463495)

//This PHP script allows an admin user to register new events and view a list of registered events in a MySQL database. 
It starts by checking if the user is logged in and has the role of "admin"; 
if not, it redirects them to the login page. The script processes form submissions to add new events, 
capturing details such as event name, date, time, location, and maximum attendees, 
and then inserts this data into the database. Additionally, it retrieves and displays all registered events in a table format, 
providing an option to delete events through a separate form submission. 
The page is styled with Bootstrap and custom CSS for a user-friendly interface, 
and it includes JavaScript to set the minimum date for the event date input to today's date.

<?php
session_start();

// Check if the user is logged in and is a super admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: all_admin_login.php");
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
    $name = $_POST['event_name'];
    $date = $_POST['event_date'];
    $time = $_POST['event_time'];
    $location = $_POST['event_location'];
    $max_attendees = $_POST['max_attendees'];
    $admin_id = $_SESSION['user_id']; // Assuming admin is logged in

    $query = "INSERT INTO events (event_name, event_date, event_time, event_location, max_attendees, created_by) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssii", $name, $date, $time, $location, $max_attendees, $admin_id);
    $stmt->execute();
    echo "<script>alert('Event Added!'); window.location.href='admin_event_add.php';</script>";
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

            input[type="text"],
            input[type="date"],
            input[type="time"],
            input[type="number"] {
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
            <a href="admin_homepage.php" class="back">Back</a>
        </div>
        <!--New user registration form-->
        <div class="container">
            <h2>New Event Registration Form</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <label for="event">Event Name:</label>
                <input type="text" name="event_name" placeholder="Event Name" required>
                <br>
                <label for="date">Event Held Date:</label>
                <input type="date" id="event_date" name="event_date" required>
                <br>
                <label for="time">Event Held Time:</label>
                <input type="time" name="event_time" required>
                <br>
                <label for="location">Event Held Location:</label>
                <input type="text" name="event_location" placeholder="Location">
                <br>
                <label for="attendees">Max Attendees:</label>
                <input type="number" name="max_attendees" placeholder="Max Attendees" required>
                <br>
                <button type="submit">Add Event</button>
            </form>
        </div>
        <div class="container1">
            <!-- Registered Events Table -->
            <h2>Registered Events</h2>
            <table>
                <tr>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Max Attendees</th>
                    <th>Current Attendees</th>
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
                $result = $conn->query("SELECT * FROM events");

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['event_name']}</td>
                            <td>{$row['event_date']}</td>
                            <td>{$row['event_time']}</td>
                            <td>{$row['event_location']}</td>
                            <td>{$row['max_attendees']}</td>
                            <td>{$row['current_attendees']}</td>
                            <td>
                                <form action='admin_delete_event.php' method='POST'>
                                    <input type='hidden' name='event_id' value='{$row['id']}'>
                                    <button type='submit' onclick='return confirm(\"Are you sure?\")' class='btn btn-danger btn-sm'>Delete</button>
                                </form>
                            </td>
                        </tr>";
                }
                ?>
            </table>
        </div>
        <script>
            // Set the min attribute to today's date
            document.addEventListener("DOMContentLoaded", function() {
                const today = new Date();
                const dd = String(today.getDate()).padStart(2, '0');
                const mm = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
                const yyyy = today.getFullYear();

                // Format the date as YYYY-MM-DD
                const formattedDate = `${yyyy}-${mm}-${dd}`;
                document.getElementById("event_date").setAttribute("min", formattedDate);
            });
        </script>

    </body>
</html>

