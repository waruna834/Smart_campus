<?php
session_start();

// Check if the user is logged in and is a super admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'super_admin') {
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

// Fetch user information
$userId = $_SESSION['user_id']; // Assuming user_id is stored in session
$userQuery = "SELECT username, role FROM users WHERE id = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $idToDelete = $_POST['id'];

    // Check the status of the user before deleting
    $statusQuery = "SELECT status FROM registration WHERE id = ?";
    $statusStmt = $conn->prepare($statusQuery);
    $statusStmt->bind_param("i", $idToDelete);
    $statusStmt->execute();
    $statusResult = $statusStmt->get_result();
    $statusRow = $statusResult->fetch_assoc();

    if ($statusRow && $statusRow['status'] === 'Approved') {
        echo "<script>alert('Cannot delete record with status Approved.');</script>";
    } else {
        // Proceed with deletion if status is not Approved
        $deleteQuery = "DELETE FROM registration WHERE id = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("i", $idToDelete);
        $deleteStmt->execute();
        $deleteStmt->close();
        echo "<script>alert('Record deleted successfully.');</script>";
    }

    $statusStmt->close();
}

// Fetch user details
$sql = "SELECT id, full_name, email, phone, user_type, faculty, program, subject, id_image, payment_receipt, created_at, status FROM registration";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <title>Super Admin Homepage</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-image: url('hm3.jpg');
                background-size: cover; /* Ensures the image covers the entire viewport */
                background-position: center; /* Centers the image */
                background-repeat: no-repeat; /* Prevents the image from repeating */
                background-attachment: fixed; /* Keeps the background image fixed during scroll */
                margin: 0; /* Remove default margin */
                padding: 0; /* Remove default padding */
                height: 100vh; /* Ensures the body takes the full height of the viewport */
            }
            h1 {
                text-align: center;
            }
            .navbar {
                background-color: #5cb85c;
                overflow: hidden;
                padding: 10px;
            }
            .navbar a {
                float: left;
                display: block;
                color: white;
                text-align: center;
                padding: 14px 16px;
                text-decoration: none;
            }
            .navbar a:hover {
                background-color: #4cae4c;
            }
            .navbar .logout {
                float: right;
            }
            .container {
                max-width: 1400px;
                margin: auto;
                background: white;
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                margin-top: 20px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #5cb85c;
                color: white;
            }
            img {
                max-width: 100px;
                height: auto;
                cursor: pointer;
            }
            .modal {
                display: none;
                position: fixed;
                z-index: 1000;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                overflow: auto;
                background-color: rgba(0,0,0,0.9);
                justify-content: center;
                align-items: center;
            }
            .modal-content {
                margin: auto;
                display: block;
                max-width: 90%;
                max-height: 90%;
            }
            .close {
                position: absolute;
                top: 15px;
                right: 35px;
                color: #fff;
                font-size: 40px;
                font-weight: bold;
                transition: 0.3s;
            }
            .close:hover,
            .close:focus {
                color: #bbb;
                text-decoration: none;
                cursor: pointer;
            }
            .search-bar {
                width: 50%;
                padding: 8px;
                margin-bottom: 20px;
            }
            /* Delete Button Styles */
            .btn-danger {
                background-color: #d9534f; /* Bootstrap danger color */
                color: white;
                border: none;
                padding: 8px 12px;
                border-radius: 4px;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }

            .btn-danger:hover {
                background-color: #c9302c; /* Darker shade on hover */
            }

            .btn-danger:focus {
                outline: none; /* Remove outline on focus */
            }
        </style>
    </head>
    <body>
        <!--navibar-->
        <div class="navbar">
            <a href="view_schedule_superadmin.php">View Programs Schedule</a>
            <a href="super_admin_announcement.php">Announcemets</a>
            <a href="super_admin_send_email.php">Send login credentials & Registered users details</a>
            <a href="super_admin_messages.php">Messages</a>
            <a href="all_admin_login.php" class="logout">Logout</a>
        </div>
        <!--New user registation details table-->
        <div class="container">
            <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?>! - New users registration details</h1>
            <div class="mb-3">
                <input type="text" id="searchInput" class="search-bar" placeholder="Search Submissions...">
            </div>

            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Send At</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>User Type</th>
                        <th>Faculty</th>
                        <th>Program</th>
                        <th>Subject</th>
                        <th>ID Image</th>
                        <th>Payment Receipt</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="submissionsTable">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['created_at']) ?></td>
                                <td style="background-color: <?= empty($row['full_name']) ? '#ffff99' : 'transparent' ?>;">
                                    <?= htmlspecialchars($row['full_name']) ?>
                                </td>
                                <td style="background-color: <?= empty($row['email']) ? '#ffff99' : 'transparent' ?>;">
                                    <?= htmlspecialchars($row['email']) ?>
                                </td>
                                <td style="background-color: <?= empty($row['phone']) ? '#ffff99' : 'transparent' ?>;">
                                    <?= htmlspecialchars($row['phone']) ?>
                                </td>
                                <td style="background-color: <?= empty($row['user_type']) ? '#ffff99' : 'transparent' ?>;">
                                    <?= htmlspecialchars($row['user_type']) ?>
                                </td>
                                <td style="background-color: <?= empty($row['faculty']) ? '#ffff99' : 'transparent' ?>;">
                                    <?= htmlspecialchars($row['faculty']) ?>
                                </td>
                                <td style="background-color: <?= empty($row['program']) ? '#ffff99' : 'transparent' ?>;">
                                    <?= htmlspecialchars($row['program']) ?>
                                </td>
                                <td style="background-color: <?= empty($row['subject']) ? '#ffff99' : 'transparent' ?>;">
                                    <?= htmlspecialchars($row['subject']) ?>
                                </td>
                                <td style="background-color: <?= empty($row['id_image']) ? '#ffff99' : 'transparent' ?>;">
                                    <?php if (!empty($row['id_image'])): ?>
                                        <img src="uploads/<?= htmlspecialchars($row['id_image']) ?>" 
                                            alt="ID Image" class="img-thumbnail" 
                                            style="width: 80px; height: 80px; cursor: pointer;" 
                                            onclick="openModal(this.src)">
                                    <?php endif; ?>
                                </td>
                                <td style="background-color: <?= empty($row['payment_receipt']) ? '#ffff99' : 'transparent' ?>;">
                                    <?php if (!empty($row['payment_receipt'])): ?>
                                        <img src="uploads/<?= htmlspecialchars($row['payment_receipt']) ?>" 
                                            alt="Payment Receipt" class="img-thumbnail" 
                                            style="width: 80px; height: 80px; cursor: pointer;" 
                                            onclick="openModal(this.src)">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="<?= ($row['status'] === 'Approved') ? 'badge bg-success' : 'badge bg-warning' ?>">
                                        <?= htmlspecialchars($row['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <form action="" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="text-center">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div id="myModal" class="modal">
            <span class="close" onclick="closeModal()">&times;</span>
            <img class="modal-content" id="img01" src="">
        </div>

        <script>
            $(document).ready(function () {
                $("#searchInput").on("keyup", function () {
                    let value = $(this).val().toLowerCase();
                    $("#submissionsTable tr").filter(function () {
                        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                    });
                });
            });

            function openModal(src) {
                var modal = document.getElementById("myModal");
                var img = document.getElementById("img01");
                img.src = src;
                modal.style.display = "block";
            }

            function closeModal() {
                var modal = document.getElementById("myModal");
                modal.style.display = "none";
            }

            window.onclick = function(event) {
                var modal = document.getElementById("myModal");
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        </script>
    </body>
</html>

<?php
$conn->close();
?>