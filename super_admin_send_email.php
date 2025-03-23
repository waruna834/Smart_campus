//Developed by group member Tharushi Dissanayake (K2462662)

//This PHP script allows a super admin to manage user accounts by sending login details via email and updating user information in a MySQL database. 
It utilizes the PHPMailer library to send emails securely through SMTP, 
including user-specific login information and a generated password. 
The script checks if the user already exists in the database, 
updating their details if they do, or inserting a new record if they do not, 
while also marking their registration status as "Approved." Additionally, 
the script provides a user interface for searching registered users, 
updating their usernames and emails via AJAX, and deleting users while preventing the deletion of super admin accounts. 
The page is styled with Bootstrap for a responsive design, and JavaScript is used for dynamic interactions, such as generating passwords and filtering user data.

<?php
session_start();
require 'vendor/autoload.php'; // Include PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if super admin is logged in
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
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch registered users
$sql = "SELECT full_name, email, user_type FROM registration";
$result = $conn->query($sql);

// Send email
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['delete_user'])) {
    // Check if the required fields are set
    if (isset($_POST['email'], $_POST['full_name'], $_POST['password'], $_POST['user_type'])) {
        $email = trim($_POST['email']);
        $fullName = trim($_POST['full_name']);
        $password = $_POST['password']; // Will be hashed before storing
        $userType = trim($_POST['user_type']);

        $mail = new PHPMailer(true);
        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'need to add email here'; // Your email
            $mail->Password = 'need to add password here'; // Your app password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Email Settings
            $mail->setFrom('neeed to add email here', 'Super Admin');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = "Your Login Details for UNIEMLS";
            
            // Determine login link based on user type
            $loginLink = ($userType === 'admin') 
                ? "http://localhost/UniELMS/all_admin_login.php" 
                : "http://localhost/UniELMS/users_login.php";

            $mail->Body = "
            <h3>Dear $fullName,</h3>
            <p>Your login details are:</p>
            <p><b>Email:</b> $email</p>
            <p><b>Password:</b> $password</p>
            <p><b>User Type:</b> $userType</p>
            <p><a href='$loginLink'>Click here to login</a></p>
            <p>Best regards,<br>Super Admin</p>
            ";

            $mail->send();

            // Hash the password for security
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $created_at = date("Y-m-d H:i:s"); // Get current timestamp

            // Check if the user already exists
            $checkUserQuery = "SELECT id FROM users WHERE email = ?";
            $checkUserStmt = $conn->prepare($checkUserQuery);
            $checkUserStmt->bind_param("s", $email);
            $checkUserStmt->execute();
            $checkUserStmt->store_result();

            if ($checkUserStmt->num_rows > 0) {
                // User exists, update their details
                $updateQuery = "UPDATE users 
                                SET username = ?, password_hash = ?, role = ?, created_at = ? 
                                WHERE email = ?";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bind_param("sssss", $fullName, $password_hash, $userType, $created_at, $email);
                $updateStmt->execute();
                $updateStmt->close();
            } else {
                // User does not exist, insert a new record
                $insertQuery = "INSERT INTO users (username, password_hash, email, role, created_at) 
                                VALUES (?, ?, ?, ?, ?)";
                $insertStmt = $conn->prepare($insertQuery);
                $insertStmt->bind_param("sssss", $fullName, $password_hash, $email, $userType, $created_at);
                $insertStmt->execute();
                $insertStmt->close();
            }

            // Update registration table status to 'approved'
            $updateRegistrationQuery = "UPDATE registration SET status = 'Approved' WHERE email = ?";
            $updateRegistrationStmt = $conn->prepare($updateRegistrationQuery);
            $updateRegistrationStmt->bind_param("s", $email);
            $updateRegistrationStmt->execute();
            $updateRegistrationStmt->close();

            $checkUserStmt->close();

            echo "<script>alert('Login details sent successfully! User data saved.');</script>";
        } catch (Exception $e) {
            echo "<script>alert('Email could not be sent. Error: {$mail->ErrorInfo}');</script>";
        }
    } else {
        echo "<script>alert('Please fill in all required fields.');</script>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) {
    if (isset($_POST['user_id'])) {
        $userId = $_POST['user_id'];

        // Fetch user role to prevent deleting super_admin
        $checkRoleQuery = "SELECT role FROM users WHERE id = ?";
        $checkRoleStmt = $conn->prepare($checkRoleQuery);
        $checkRoleStmt->bind_param("i", $userId);
        $checkRoleStmt->execute();
        $result = $checkRoleStmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && $user['role'] !== 'super_admin') {
            $deleteQuery = "DELETE FROM users WHERE id = ?";
            $deleteStmt = $conn->prepare($deleteQuery);
            $deleteStmt->bind_param("i", $userId);

            if ($deleteStmt->execute()) {
                echo "<script>alert('User deleted successfully!'); window.location.reload();</script>";
            } else {
                echo "<script>alert('Error deleting user.');</script>";
            }
            $deleteStmt->close();
        } 
        $checkRoleStmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Super Admin - Send Login Details</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            .container {
                margin-top: 50px;
                background: white;
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
            .search-bar {
                width: 25%;
                padding: 8px;
                margin-bottom: 20px;
            }
        </style>
    </head>
    <body>
        <!--navibar-->
        <div class="navbar">
            <a href="super_admin_homepage.php" class="back">Back</a>
        </div>

        <div class="container">
            <h2 class="text-center">Send Login Details</h2>
            <!--search bar-->
            <div class="mb-3">
                <?php
                // Fetch all users first to avoid closing the result set prematurely
                $usersArray = [];
                while ($row = $result->fetch_assoc()) {
                    $usersArray[] = $row;
                }
                ?>

                <label for="searchUser" class="form-label">Search by Email or Name</label>
                <input type="text" id="searchUser" class="form-control" placeholder="Start typing...">

                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script>
                    $(document).ready(function () {
                        let users = <?php echo json_encode($usersArray); ?>; 

                        $("#searchUser").on("keyup", function () {
                            let value = $(this).val().trim().toLowerCase();

                            // Find the first matching user
                            let foundUser = users.find(user => 
                                user.email.toLowerCase().includes(value) || 
                                (user.full_name && user.full_name.toLowerCase().includes(value)) // Ensure full_name exists
                            );

                            if (foundUser) {
                                $("#full_name").val(foundUser.full_name || ""); // Prevent undefined errors
                                $("#email").val(foundUser.email || "");
                                $("#user_type").val(foundUser.user_type || "");
                            } else {
                                $("#full_name, #email, #user_type").val(""); // Clear fields if no match
                            }
                        });
                    });
                </script>
            </div>
            <!--login details sending form-->
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">User Type</label>
                    <input type="text" id="user_type" name="user_type" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input type="text" id="password" name="password" class="form-control" placeholder="Enter or generate password" required>
                        <button type="button" class="btn btn-secondary" onclick="generatePassword()">Generate</button>
                    </div>
                </div>
                <script>
                    function generatePassword() {
                        const length = 12;
                        const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()";
                        let password = "";
                        for (let i = 0; i < length; i++) {
                            password += charset.charAt(Math.floor(Math.random() * charset.length));
                        }
                        document.getElementById("password").value = password;
                    }
                </script>
                <button type="submit" class="btn btn-primary">Approved & Send Login Details</button>
            </form><br>

            <h3 class="mt-4">Registered Users</h3><br>
            <input type="text" id="search" class="search-bar" placeholder="Search by ID, Role,..." onkeyup="searchData()">

            <!-- Updated Approved User Details Table -->
            <table id="" class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="user-table">
                    <?php
                    $userQuery = "SELECT id, username, email, role, created_at FROM users";
                    $userResult = $conn->query($userQuery);

                    if ($userResult->num_rows > 0):
                        while ($row = $userResult->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <input type="text" class="form-control username-input"
                                            id="username_<?= htmlspecialchars($row['id']) ?>" 
                                            value="<?= htmlspecialchars($row['username']) ?>">
                                        <button class="btn btn-sm btn-primary update-username-btn ms-2"
                                                data-id="<?= htmlspecialchars($row['id']) ?>">
                                            Update
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <input type="email" class="form-control email-input"
                                            id="email_<?= htmlspecialchars($row['id']) ?>" 
                                            value="<?= htmlspecialchars($row['email']) ?>">
                                        <button class="btn btn-sm btn-primary update-email-btn ms-2"
                                                data-id="<?= htmlspecialchars($row['id']) ?>">
                                            Update
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-<?= ($row['role'] === 'super_admin') ? 'danger' : 
                                                            (($row['role'] === 'admin') ? 'primary' : 
                                                            (($row['role'] === 'lecturer') ? 'info' : 'success')) ?>">
                                        <?= htmlspecialchars($row['role']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($row['created_at']) ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?= htmlspecialchars($row['id']) ?>">
                                        <button type="submit" name="delete_user" class="btn btn-danger btn-sm" 
                                                <?= ($row['role'] === 'super_admin') ? 'disabled' : '' ?>
                                                onclick="return confirm('Are you sure you want to delete this user?');">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile;
                    else: ?>
                        <tr><td colspan="6" class="text-center">No users found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <script>
                $(document).ready(function () {
                    // Update Email
                    $(".update-email-btn").click(function () {
                        let userId = $(this).data("id");
                        let emailField = $("#email_" + userId);
                        let newEmail = emailField.val().trim();

                        if (newEmail === "") {
                            alert("Email cannot be empty!");
                            return;
                        }

                        $.ajax({
                            url: "update_user.php",
                            type: "POST",
                            data: { user_id: userId, email: newEmail },
                            success: function (response) {
                                if (response.trim() === "success") {
                                    alert("Email updated successfully!");
                                } else {
                                    alert("Error updating email: " + response);
                                }
                            }
                        });
                    });

                    // Update Username
                    $(".update-username-btn").click(function () {
                        let userId = $(this).data("id");
                        let usernameField = $("#username_" + userId);
                        let newUsername = usernameField.val().trim();

                        if (newUsername === "") {
                            alert("Username cannot be empty!");
                            return;
                        }

                        $.ajax({
                            url: "update_user.php",
                            type: "POST",
                            data: { user_id: userId, username: newUsername },
                            success: function (response) {
                                if (response.trim() === "success") {
                                    alert("Username updated successfully!");
                                } else {
                                    alert("Error updating username: " + response);
                                }
                            }
                        });
                    });
                });
            </script>

            <script>
                // JavaScript function to filter the data based on search input
                function searchData() {
                    let value = document.getElementById("search").value.toLowerCase();
                    let rows = document.querySelectorAll("#user-table tr");

                    rows.forEach(row => {
                        let id = row.cells[0].textContent.toLowerCase(); // ID Column
                        let username = row.cells[1].querySelector("input").value.toLowerCase(); // Username Column (input field)
                        let email = row.cells[2].querySelector("input").value.toLowerCase(); // Email Column (input field)
                        let role = row.cells[3].textContent.toLowerCase(); // Role Column
                        let createdAt = row.cells[4].textContent.toLowerCase(); // Created At Column

                        let rowText = id + " " + username + " " + email + " " + role + " " + createdAt;
                        row.style.display = rowText.indexOf(value) > -1 ? "" : "none";
                    });
                }
            </script>
        </div>
    </body>
</html>

<?php
$conn->close();
?>
