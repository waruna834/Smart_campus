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
