<?php
//for new user register - backend logic
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "uniemls";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST['fullName']) || empty($_POST['email']) || empty($_POST['phone']) || empty($_POST['userType'])) {
        header("Location: user_register_form.php?status=error&message=Missing required fields.");
        exit();
    }

    $fullName = $conn->real_escape_string($_POST['fullName']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $userType = $conn->real_escape_string($_POST['userType']);
    $faculty = $program = $subject = null;

    if ($userType === 'admin') {
        $faculty = $_POST['faculty'] ?? null;
    } elseif ($userType === 'lecturer') {
        $faculty = $_POST['facultyLecture'] ?? null;
        $program = $_POST['programLecture'] ?? null;
        $subject = $_POST['subject'] ?? null;
    } elseif ($userType === 'student') {
        $faculty = $_POST['facultyStudent'] ?? null;
        $program = $_POST['programStudent'] ?? null;
    }

    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $idImage = basename($_FILES['idImage']['name']);
    $paymentReceipt = basename($_FILES['paymentReceipt']['name']);

    if (!move_uploaded_file($_FILES['idImage']['tmp_name'], $uploadDir . $idImage)) {
        header("Location: user_register_form.php?status=error&message=Failed to upload ID image.");
        exit();
    }
    if (!move_uploaded_file($_FILES['paymentReceipt']['tmp_name'], $uploadDir . $paymentReceipt)) {
        header("Location: user_register_form.php?status=error&message=Failed to upload payment receipt.");
        exit();
    }

    $sql = "INSERT INTO registration (full_name, email, phone, user_type, faculty, program, subject, id_image, payment_receipt)
            VALUES ('$fullName', '$email', '$phone', '$userType', '$faculty', '$program', '$subject', '$idImage', '$paymentReceipt')";

    if ($conn->query($sql)) {
        header("Location: user_register_form.php?status=success&message=Registration successful! Login details will be sent via email.");
    } else {
        header("Location: user_register_form.php?status=error&message=Database Error: " . $conn->error);
    }
}

$conn->close();
?>
