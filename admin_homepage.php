//Developed by group leader Waruna Munasinghe (K2463495)

//This PHP script allows an admin user to schedule a new program by filling out a form with details such as faculty, 
program type, subject, start date, duration, batch, classroom, and days of the week. 
It begins by checking if the user is logged in and has the role of "admin," and 
then establishes a connection to a MySQL database to store the program details. 
Upon form submission, the script calculates the end date based on the start date and duration, 
and inserts the program information into the program_schedule table in the database. 
The HTML structure includes a navigation bar displaying the logged-in user's information and links to other admin functionalities, 
as well as a form styled with CSS for a user-friendly interface. 
Additionally, JavaScript is used to dynamically calculate and display the end date based on the input duration, enhancing the user experience.

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

// Fetch user information
$userId = $_SESSION['user_id']; // Assuming user_id is stored in session
$userQuery = "SELECT username, role FROM users WHERE id = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $faculty = $_POST['faculty'];
    $program_type = $_POST['program_type'];
    $subject = $_POST['subject'];
    $start_date = $_POST['start_date'];
    $duration = $_POST['duration'];
    $batch = $_POST['batch'];
    $classroom = $_POST['classroom'];
    $days_of_week = implode(", ", $_POST['days_of_week']); // Multiple days selection
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Calculate the end date by adding duration (in days) to the start date
    $startDateObj = new DateTime($start_date);
    $startDateObj->modify("+$duration days");
    $end_date = $startDateObj->format('Y-m-d');

    // Insert the data into the database
    $stmt = $conn->prepare("INSERT INTO program_schedule (faculty, program_type, subject, start_date, end_date, duration, batch, classroom, days_of_week, start_time, end_time) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssss", $faculty, $program_type, $subject, $start_date, $end_date, $duration, $batch, $classroom, $days_of_week, $start_time, $end_time);

    if ($stmt->execute()) {
        echo "<script>alert('Program scheduled successfully!'); window.location.href='admin_homepage.php';</script>";
    } else {
        echo "<script>alert('Error: ' . $stmt->error;); window.location.href='admin_homepage.php';</script>";
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
        <title>Admin Homepage - Program Scheduling</title>
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
            .form-container {
                max-width: 700px; /* Set a max width for the container */
                margin: auto;
                background: white;
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                margin-top: 20px; /* Space between navbar and container */
            }
            .form-group {
                margin-bottom: 15px;
            }
            label {
                display: block;
                margin-bottom: 5px;
            }
            input[type="text"],
            input[type="date"],
            input[type="time"],
            select,
            input[type="number"] {
                width: 100%;
                padding: 8px;
                box-sizing: border-box;
            }
            button {
                width: 100%;
                padding: 10px;
                background-color:rgb(45, 131, 230);
                border: none;
                color: white;
                font-size: 16px;
                cursor: pointer;
            }
            button:hover {
                background-color:rgb(45, 131, 230);
            }
        </style>
    </head>
    <body>
        <!-- Navigation Bar -->
        <div class="navbar">
            <span>Welcome <?php echo htmlspecialchars($user['username']); ?> (<?php echo htmlspecialchars($user['role']); ?>)  </span>
            <a href="view_schedule.php">View Programs Schedule</a>
            <a href="admin_subject_register_view.php">Subject Registration Details</a>
            <a href="resource_availability.php">Resource Availability</a>
            <a href="admin_event_add.php">Events Registeration</a>
            <a href="admin_messages.php">Messages</a>
            <a href="all_admin_login.php">Logout</a>
        </div>
        <!--Programming scheduling form-->
        <div class="form-container">
            <h2>Program Scheduling</h2>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="faculty">Faculty</label>
                    <select name="faculty" id="faculty" required>
                        <option value="">Select Faculty</option>
                        <option value="IT">IT</option>
                        <option value="Business">Business</option>
                        <option value="Art">Art</option>
                        <option value="Engineering">Engineering</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="program_type">Program Type</label>
                    <select name="program_type" id="program_type" required>
                        <option value="">Select Program</option>
                        <option value="Certificate">Certificate</option>
                        <option value="HND">HND</option>
                        <option value="Top-up Degree">Top-up Degree</option>
                        <option value="4 Year Degree">4 Year Degree</option>
                        <option value="Master's Degree">Master's Degree</option>
                        <option value="PhD">PhD</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" name="subject" id="subject" required>
                </div>
                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" name="start_date" id="start_date" required>
                </div>
                <div class="form-group">
                    <label for="duration">Duration (in days)</label>
                    <input type="number" name="duration" id="duration" required>
                </div>
                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input type="date" name="end_date" id="end_date" readonly>
                </div>
                <div class="form-group">
                    <label for="batch">Batch</label>
                    <input type="text" name="batch" id="batch" required>
                </div>
                <div class="form-group">
                    <label for="classroom">Classroom</label>
                    <input type="text" name="classroom" id="classroom" required>
                </div>
                <div class="form-group">
                    <label for="days_of_week">Days of the Week</label><br>
                    <input type="checkbox" name="days_of_week[]" value="Monday"> Monday
                    <input type="checkbox" name="days_of_week[]" value="Tuesday"> Tuesday
                    <input type="checkbox" name="days_of_week[]" value="Wednesday"> Wednesday
                    <input type="checkbox" name="days_of_week[]" value="Thursday"> Thursday
                    <input type="checkbox" name="days_of_week[]" value="Friday"> Friday
                    <input type="checkbox" name="days_of_week[]" value="Saturday"> Saturday
                    <input type="checkbox" name="days_of_week[]" value="Sunday"> Sunday
                </div>
                <div class="form-group">
                    <label for="start_time">Start Time</label>
                    <input type="time" name="start_time" id="start_time" required>
                </div>
                <div class="form-group">
                    <label for="end_time">End Time</label>
                    <input type="time" name="end_time" id="end_time" required>
                </div>
                <button type="submit">Schedule Program</button>
            </form>
        </div>

        <script>
            // Function to calculate the end date based on the start date and duration
            document.getElementById('duration').addEventListener('input', function() {
                const startDate = document.getElementById('start_date').value;
                const duration = parseInt(this.value);

                if (startDate && duration) {
                    const startDateObj = new Date(startDate);
                    startDateObj.setDate(startDateObj.getDate() + duration);

                    // Format the end date as yyyy-mm-dd
                    const endDate = startDateObj.toISOString().split('T')[0];

                    document.getElementById('end_date').value = endDate;
                }
            });
        </script>
    </body>
</html>
