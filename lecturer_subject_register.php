<?php
session_start(); // Start the session to access user login details

// Check if the user is logged in and is a super admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: users_login.php");
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

// Fetch the logged-in user's username and role
$user_id = $_SESSION['user_id']; // Assuming user ID is stored in session
$user_result = $conn->query("SELECT username, role FROM users WHERE id = $user_id");
$user_row = $user_result->fetch_assoc();
$lecturer_name = $user_row['username'];
$lecturer_role = $user_row['role']; // Fetch the user's role

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $faculty = $_POST['faculty'];
    $program = $_POST['program'];
    $subject = $_POST['subject'];

    $stmt = $conn->prepare("INSERT INTO subjects (user_id, faculty, program, subject, name, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $user_id, $faculty, $program, $subject, $lecturer_name, $lecturer_role);
    $stmt->execute();
    $stmt->close();
}

// Fetch saved subjects for the logged-in user
$saved_subjects = $conn->query("SELECT * FROM subjects WHERE user_id = $user_id ORDER BY created_at DESC");

// Handle delete request
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM subjects WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: lecturer_subject_register.php"); // Redirect to avoid resubmission
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Lecture Subject Assign</title>
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
                max-width: 1200px;
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

            select, textarea {
                width: 100%;
                padding: 8px;
                box-sizing: border-box;
                margin-bottom: 15px;
                border: 1px solid #ccc;
                border-radius: 4px;
            }

            textarea {
                resize: vertical;
            }

            button {
                width: 100%;
                padding: 10px;
                background-color: #007bff;
                border: none;
                color: white;
                font-size: 16px;
                cursor: pointer;
                border-radius: 4px;
            }

            button:hover {
                background-color: #0056b3;
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
                margin-top: 20px;
            }

            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }

            th {
                background-color: #d9d9d9;
                color: black;
            }

            .unread {
                background-color: yellow !important;
                font-weight: bold;
                cursor: pointer;
            }

            .unread:hover {
                background-color:rgb(237, 238, 154) !important;
            }
        </style>
    </head>
    <body>
    <!-- Navigation Bar -->
        <div class="navbar">
            <a href="lecturer_homepage.php" class="back">Back</a>
        </div>
        <!--Lecturer assigned form-->
        <div class="container">
            <h2>Lecturer Subject Assign Form</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="lecturer">Full Name</label>
                    <input type="text" class="form-control" id="lecturer" name="lecturer" value="<?php echo htmlspecialchars($lecturer_name); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <input type="text" class="form-control" id="role" name="role" value="<?php echo htmlspecialchars($lecturer_role); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="faculty">Faculty</label>
                    <select class="form-control" id="faculty" name="faculty" required>
                        <option value="">Select Faculty</option>
                        <option value="IT">IT</option>
                        <option value="Business">Business</option>
                        <option value="Art">Art</option>
                        <option value="Engineering">Engineering</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="program">Program</label>
                    <select class="form-control" id="program" name="program" required>
                        <option value="">Select Program</option>
                        <option value="Certificate">Certificate</option>
                        <option value="HND">HND</option>
                        <option value="Top-up Degre">Top-up Degree</option>
                        <option value="4 Year Degree">4 Year Degree</option>
                        <option value="Master's Degree">Master's Degree</option>
                        <option value="PhD">PhD</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" class="form-control" id="subject" name="subject" required>
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
            </form>
            <!--Currently assigned subject form-->
            <h2 class="mt-5">Currently Assigned Subjects</h2>
            <table class=" table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Faculty</th>
                        <th>Program</th>
                        <th>Subject</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($saved_subjects->num_rows > 0): ?>
                        <?php while($row = $saved_subjects->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['faculty']); ?></td>
                                <td><?php echo htmlspecialchars($row['program']); ?></td>
                                <td><?php echo htmlspecialchars($row['subject']); ?></td>
                                <td><?php echo $row['created_at']; ?></td>
                                <td>
                                    <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No subjects registered yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </body>
</html>

<?php
$conn->close();
?>