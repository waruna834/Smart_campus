//Developed by group leader Waruna Munasinghe (K2463495)

//This PHP script enables an admin user to send messages to other users (super admins, lecturers, and students) and 
view received messages from the database. It starts by checking if the user is logged in and has the role of "admin," 
then establishes a connection to a MySQL database to handle message storage and retrieval. 
The script processes form submissions to send messages, including optional file attachments, 
and validates the file type before uploading it to a designated directory. 
It also fetches and displays the messages sent to the admin in a table format, 
allowing for real-time searching through the messages based on sender or role. 
Additionally, JavaScript is used to mark messages as read when clicked, enhancing user interaction and experience.

<?php
session_start();

// Check if the user is logged in and is a admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: users_login.php");
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

// Handle message sending
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['message'])) {
    $receiverId = $_POST['receiver_id'];
    $message = trim($_POST['message']);
    $senderId = $_SESSION['user_id'];
    $filePath = NULL; // Default value if no file is uploaded

    if (!empty($message)) {
        // Check if a file is uploaded
        if (!empty($_FILES['attachment']['name'])) {
            $uploadDir = "uploads/"; // Ensure this directory exists and is writable
            $fileName = basename($_FILES["attachment"]["name"]);
            $targetFilePath = $uploadDir . time() . "_" . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

            // Allow only certain file formats
            $allowedTypes = ["jpg", "jpeg", "png", "gif", "pdf", "doc", "docx"];
            if (in_array(strtolower($fileType), $allowedTypes)) {
                if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $targetFilePath)) {
                    $filePath = $targetFilePath; // Store the file path in the database
                } else {
                    $error = "Error uploading file.";
                }
            } else {
                $error = "Invalid file type. Only JPG, PNG, GIF, PDF, DOC, and DOCX files are allowed.";
            }
        }

        // Insert message into database
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, file_path, is_read) VALUES (?, ?, ?, ?, 0)");
        $stmt->bind_param("iiss", $senderId, $receiverId, $message, $filePath);

        if ($stmt->execute()) {
            $success = "Message sent successfully!";
        } else {
            $error = "Error sending message: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Message cannot be empty!";
    }
}

// Fetch users (Super admin, Lecturer, Student)
$userQuery = "SELECT id, username, role FROM users WHERE role IN ('super_admin','lecturer', 'student')";
$userResult = $conn->query($userQuery);

// Fetch messages for Admin
$messageQuery = "SELECT m.id, u.username AS sender, u.role, m.message, m.file_path, m.created_at, m.is_read
                 FROM messages m 
                 JOIN users u ON m.sender_id = u.id 
                 WHERE m.receiver_id = ?
                 ORDER BY m.created_at DESC";

$stmt = $conn->prepare($messageQuery);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$messageResult = $stmt->get_result();

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <title>Admin Messages</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-image: url('op.jpg');
                background-size: cover; /* Ensures the image covers the entire viewport */
                background-position: center; /* Centers the image */
                background-repeat: no-repeat; /* Prevents the image from repeating */
                background-attachment: fixed; /* Keeps the background image fixed during scroll */
                margin: 0; /* Remove default margin */
                padding: 0; /* Remove default padding */
                height: 100vh; /* Ensures the body takes the full height of the viewport */
            }

            .navbar {
                background-color: #333;
                padding: 10px;
                color: white;
                text-align: center;
            }
            .navbar a {
                color: white;
                margin: 0 10px;
                text-decoration: none;
            }

            .container {
                max-width: 1200px; /* Set a max width for the container */
                margin: auto;
                background: white;
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                margin-top: 20px; /* Space between navbar and container */
            }

            h2 {
                text-align: center;
            }

            form {
                background: white;
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }

            label {
                display: block;
                margin-bottom: 5px;
            }

            select,
            textarea {
                width: 100%;
                padding: 8px;
                box-sizing: border-box;
                margin-bottom: 15px;
                border: 1px solid #ccc;
                border-radius: 4px;
            }

            textarea {
                resize: vertical; /* Allow vertical resizing only */
            }

            button {
                width: 100%;
                padding: 10px;
                background-color: #007bff; /* Blue color */
                border: none;
                color: white;
                font-size: 16px;
                cursor: pointer;
                border-radius: 4px;
            }

            button:hover {
                background-color: #0056b3; /* Darker blue on hover */
            }

            .error {
                color: red;
                margin-top: 10px;
            }

            .success {
                color: green;
                margin-top: 10px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px; /* Space between table and content above */
            }

            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }

            th {
                background-color: #d9d9d9; /* Grey background for table header */
                color: black; /* Black text for table header */
            }
            .unread {
                background-color: yellow !important;
                font-weight: bold;
                cursor: pointer;
            }

            .unread:hover {
                background-color:rgb(237, 238, 154) !important;
            }
            .search-bar {
                width: 25%;
                padding: 8px;
                margin-bottom: 20px;
            }
        </style>
    </head>
    <body>
        <!-- Navigation Bar -->
        <div class="navbar">
            <a href="admin_homepage.php" class="back">Back</a>
        </div>
        <!--Message form-->
        <div class="container">
            <h2>Send Message</h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <label for="receiver_id">Receiver:</label>
                <select name="receiver_id" required>
                    <?php while ($row = $userResult->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>">
                            <?php echo htmlspecialchars($row['username']) . " (" . htmlspecialchars($row['role']) . ")"; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <br>
                
                <label for="message">Message:</label>
                <textarea name="message" required></textarea>
                <br>

                <label for="attachment">Attach File (Image/PDF/Doc):</label>
                <input type="file" name="attachment" accept="image/*, .pdf, .doc, .docx">
                <br><br>

                <button type="submit">Send Message</button>
                <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
                <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            </form>
            <!--Recevied messages table-->
            <h2>Your Messages</h2>
            <input type="text" id="search" class="search-bar" placeholder="Search by Sender, Role, ..." onkeyup="searchData()">
            <table id="schedule-table" border="1">
                <tr>
                    <th>Sender</th>
                    <th>Role</th>
                    <th>Message</th>
                    <th>Attachment</th>
                    <th>Sent At</th>
                </tr>
                <?php while ($row = $messageResult->fetch_assoc()): ?>
                    <tr class="message-row <?php echo $row['is_read'] == 0 ? 'unread' : ''; ?>" 
                        data-message-id="<?php echo htmlspecialchars($row['id']); ?>">
                        <td><?php echo htmlspecialchars($row['sender']); ?></td>
                        <td><?php echo htmlspecialchars($row['role']); ?></td>
                        <td><?php echo htmlspecialchars($row['message']); ?></td>
                        <td>
                            <?php if (!empty($row['file_path'])): ?>
                                <a href="<?php echo htmlspecialchars($row['file_path']); ?>" target="_blank">Download</a>
                            <?php else: ?>
                                No Attachment
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
        
        <script>
            // JavaScript function to filter the data based on search input
            function searchData() {
                const searchValue = document.getElementById("search").value.toLowerCase();
                const rows = document.getElementById("schedule-table").getElementsByTagName("tr");

                for (let i = 1; i < rows.length; i++) { // Skip header row
                    let row = rows[i];
                    let columns = row.getElementsByTagName("td");
                    let match = false;

                    for (let j = 0; j < columns.length; j++) {
                        if (columns[j].innerText.toLowerCase().includes(searchValue)) {
                            match = true;
                            break;
                        }
                    }

                    row.style.display = match ? "" : "none";
                }
            }

            document.addEventListener("DOMContentLoaded", function () {
                let messageRows = document.querySelectorAll(".message-row");

                messageRows.forEach(row => {
                    row.addEventListener("click", function () {
                        let messageId = this.getAttribute("data-message-id");

                        // Remove highlight instantly
                        this.classList.remove("unread");

                        // Send AJAX request to mark as read in the database
                        let xhr = new XMLHttpRequest();
                        xhr.open("POST", "mark_message_read.php", true);
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        xhr.send("message_id=" + messageId);
                    });
                });
            });
        </script>
    </body>
</html>
