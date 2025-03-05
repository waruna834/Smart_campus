<?php
//for resource availablity form
session_start();

// Check if the user is logged in and is a student
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
