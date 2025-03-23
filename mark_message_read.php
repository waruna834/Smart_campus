//Developed by group leader Waruna Munasinghe (K2463495)

//This PHP script is designed to update the status of a message in a MySQL database, marking it as read. 
It begins by starting a session and establishing a connection to the database using the provided credentials. 
When a POST request is received with a message_id, the script prepares an SQL UPDATE statement to set the is_read field to 1 for the specified message. 
It uses a prepared statement to safely execute the query, ensuring that the message ID is properly handled to prevent SQL injection. 
Finally, the script closes the database connection after executing the update operation.

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
