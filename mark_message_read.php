<?php
//for message status update
session_start();

$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "uniemls"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['message_id'])) {
    $messageId = intval($_POST['message_id']);

    $updateQuery = "UPDATE messages SET is_read = 1 WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("i", $messageId);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
?>
