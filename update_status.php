//Developed by group leader Waruna Munasinghe (K2463495)

//This PHP script is designed to update the status of a program schedule entry in a MySQL database, specifically for admin users. 
It starts by checking if the user is logged in and has the role of "admin"; if not, 
it redirects them to the login page. When a POST request is received containing an ID and a new status, 
the script prepares an SQL UPDATE statement to change the status of the specified program schedule entry in the program_schedule table. 
It uses a prepared statement to safely execute the query, allowing for the possibility of empty status values. 
Finally, the script provides feedback on whether the status update was successful or if an error occurred, and it closes the database connection afterward.

<?php
//for program sheduling (admin view program schedule - status change)
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

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if request contains an ID and a new status
if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $status = isset($_POST['status']) ? $_POST['status'] : "";

    // Update status in the database (allow empty values)
    $sql = "UPDATE program_schedule SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        echo "Status updated successfully!";
    } else {
        echo "Error updating status: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
