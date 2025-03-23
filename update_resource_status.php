//Developed by group leader Waruna Munasinghe (K2463495)

//This PHP script is designed to update the availability status of resources in a MySQL database, specifically for admin users. 
It begins by checking if the user is logged in and has the role of "admin"; 
if not, it redirects them to the login page. Upon receiving a POST request, 
the script retrieves the resource ID and the new status from the submitted data, 
determining whether the status should be set to "Available" or "Unavailable." 
It then prepares and executes an SQL UPDATE statement to change the status of the specified resource in the resource_availability table. 
Finally, the script returns a success or error message based on the outcome of the update operation and closes the database connection.

<?php
//for resource availablity form (for admin only)
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $new_status = ($_POST['status'] == "Available") ? "Available" : "Unavailable";

    $query = "UPDATE resource_availability SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $new_status, $id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
}
?>
