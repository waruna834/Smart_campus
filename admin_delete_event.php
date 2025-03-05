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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];

    // Delete event from database
    $query = "DELETE FROM events WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $event_id);

    if ($stmt->execute()) {
        echo "<script>alert('Event deleted successfully!'); window.location.href='admin_event_add.php';</script>";
    } else {
        echo "<script>alert('Error deleting event!'); window.location.href='admin_event_add.php';</script>";
    }
}
?>
