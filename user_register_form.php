<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>User Registration Form</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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

            .container {
                background: rgba(129, 125, 125, 0.9); /* Semi-transparent white background for the form */
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }

            .hidden { display: none; }
        </style>
    </head>
    <body>
        <!-- Navigation Bar -->
        <div class="navbar">
            <a href="homepage.php" class="btn btn-primary">Back</a>
        </div>
        <!--New user registration form-->
        <div class="container mt-5">
            <h2>New User Registration Form</h2>

            <form id="registrationForm" action="register.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <input type="text" class="form-control" id="fullName" placeholder="Enter your full name"name="fullName" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" placeholder="Enter your email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" class="form-control" id="phone" placeholder="Enter your phone number" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="userType">User Type</label>
                    <select class="form-control" id="userType" name="userType" required>
                        <option value="">Select User Type</option>
                        <option value="admin">Admin</option>
                        <option value="lecturer">Lecturer</option>
                        <option value="student">Student</option>
                    </select>
                </div>

                <!-- Admin Section -->
                <div id="adminSection" class="hidden">
                    <h4>Admin Details</h4>
                    <div class="form-group">
                        <label for="faculty">Faculty</label>
                        <select class="form-control" id="faculty" name="faculty">
                            <option value="">Select Faculty</option>
                            <option value="IT">IT</option>
                            <option value="Business">Business</option>
                            <option value="Art">Art</option>
                            <option value="Engineering">Engineering</option>
                        </select>
                    </div>
                </div>

                <!-- Lecture Section -->
                <div id="lectureSection" class="hidden">
                    <h4>Lecturer Details</h4>
                    <div class="form-group">
                        <label for="facultyLecture">Faculty</label>
                        <select class="form-control" id="facultyLecture" name="facultyLecture">
                            <option value="">Select Faculty</option>
                            <option value="IT">IT</option>
                            <option value="Business">Business</option>
                            <option value="Art">Art</option>
                            <option value="Engineering">Engineering</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="programLecture">Program</label>
                        <select class="form-control" id="programLecture" name="programLecture">
                            <option value="">Select Program</option>
                            <option value="Certificate">Certificate</option>
                            <option value="HND">HND</option>
                            <option value="Top-up Degre">Top-up Degree</option>
                            <option value="4 Year Degree">4 Year Degree</option>
                            <option value="Master's Degree">Master's Degree</option>
                            <option value="PhD">PhD</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject">
                    </div>
                </div>

                <!-- Student Section -->
                <div id="studentSection" class="hidden">
                    <h4>Student Details</h4>
                    <div class="form-group">
                        <label for="facultyStudent">Faculty</label>
                        <select class="form-control" id="facultyStudent" name="facultyStudent">
                            <option value="">Select Faculty</option>
                            <option value="IT">IT</option>
                            <option value="Business">Business</option>
                            <option value="Art">Art</option>
                            <option value="Engineering">Engineering</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="programStudent">Program</label>
                        <select class="form-control" id="programStudent" name="programStudent">
                            <option value="">Select Program</option>
                            <option value="Certificate">Certificate</option>
                            <option value="HND">HND</option>
                            <option value="Top-up Degre">Top-up Degree</option>
                            <option value="4 Year Degree">4 Year Degree</option>
                            <option value="Master's Degree">Master's Degree</option>
                            <option value="PhD">PhD</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="idImage">ID Image Upload</label>
                    <input type="file" class="form-control-file" id="idImage" name="idImage" accept="image/*" required>
                </div>
                <div class="form-group">
                    <label for="paymentReceipt">Payment Receipt/Approval Letter Upload</label>
                    <input type="file" class="form-control-file" id="paymentReceipt" name="paymentReceipt" accept="image/*,application/pdf" required>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
            <!-- Success/Error Message -->
            <?php if (isset($_GET['message'])): ?>
                <div id="alertMessage" class="alert <?php echo ($_GET['status'] == 'success') ? 'alert-success' : 'alert-danger'; ?>">
                    <?php echo htmlspecialchars($_GET['message']); ?>
                </div>
            <?php endif; ?>
            <script>
                // Auto-dismiss the alert message after 5 seconds
                window.onload = function() {
                    var alertMessage = document.getElementById("alertMessage");
                    if (alertMessage) {
                        setTimeout(function() {
                            alertMessage.style.display = "none"; // Hide the alert
                        }, 2000); // 2000 milliseconds = 2 seconds
                    }
                };
            </script>
        </div><br>

        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#userType').change(function() {
                    var userType = $(this).val();
                    $('.hidden').hide();
                    if (userType === 'admin') $('#adminSection').show();
                    else if (userType === 'lecturer') $('#lectureSection').show();
                    else if (userType === 'student') $('#studentSection').show();
                });
            });
        </script>
    </body>
</html>
