<?php
session_start();

// Check if the user is logged in and is a student
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'student') {
    header("Location: users_login.php");
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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['attendance_id'])) {
    $attendance_id = $_POST['attendance_id'];

    // Get event_id before deleting attendance
    $query = "SELECT event_id FROM event_attendance WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $attendance_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $event_id = $row['event_id'];

    // Delete attendance record
    $deleteQuery = "DELETE FROM event_attendance WHERE id = ?";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bind_param("i", $attendance_id);

    if ($deleteStmt->execute()) {
        // Update event's current attendee count (reduce by 1)
        $updateCount = $conn->prepare("UPDATE events SET current_attendees = current_attendees - 1 WHERE id = ?");
        $updateCount->bind_param("i", $event_id);
        $updateCount->execute();

        echo "<script>alert('Attendance removed successfully!'); window.location.href='student_event.php';</script>";
    } else {
        echo "<script>alert('Error deleting attendance!'); window.location.href='student_event.php';</script>";
    }
}
?>
