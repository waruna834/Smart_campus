//Developed by group member Tharushi Dissanayake (K2462662)

//This PHP script allows a super admin to update the email and username of registered users in a MySQL database. 
It starts by checking if the user is logged in and has the role of super admin; 
if not, it redirects them to the login page. Upon receiving a POST request, 
the script checks for the presence of a user ID and then processes updates for either the email or 
username based on the submitted data. It uses prepared statements to securely execute the SQL update queries, 
ensuring that user input is properly handled to prevent SQL injection. 
Finally, the script returns a success message if the update is successful or an error message if it fails, 
and it closes the database connection afterward.

<?php
//for registered users email and user name changes (only super admin can accessed)
session_start();

// Check if the user is logged in and is a super admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'super_admin') {
    header("Location: all_admin_login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "uniemls";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['user_id'])) {
        $userId = $_POST['user_id'];

        // Update Email
        if (isset($_POST['email'])) {
            $newEmail = trim($_POST['email']);
            $updateEmailQuery = "UPDATE users SET email = ? WHERE id = ?";
            $stmt = $conn->prepare($updateEmailQuery);
            $stmt->bind_param("si", $newEmail, $userId);

            if ($stmt->execute()) {
                echo "success";
            } else {
                echo "error: " . $conn->error;
            }
            $stmt->close();
        }

        // Update Username
        if (isset($_POST['username'])) {
            $newUsername = trim($_POST['username']);
            $updateUsernameQuery = "UPDATE users SET username = ? WHERE id = ?";
            $stmt = $conn->prepare($updateUsernameQuery);
            $stmt->bind_param("si", $newUsername, $userId);

            if ($stmt->execute()) {
                echo "success";
            } else {
                echo "error: " . $conn->error;
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>
