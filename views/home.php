<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect them to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Office of Scholarship Programs - ZPPSU</title>

    <!-- Font Awesome Kit -->
    <script src="../js/fontawesome.js" crossorigin="anonymous"></script>

    <!-- CSS file with cache-busting -->
    <link rel="stylesheet" href="../css/styles.css?v=<?php echo time(); ?>">
</head>

<body>
    <nav>
        <ul>
            <li>
                <a href="#" class="logo">
                    <img src="../assets/images/icons/scholarship_seal.png" alt="Scholarship Seal">
                    <span class="nav-item">OFFICE OF SCHOLARSHIP PROGRAMS</span>
                </a>
            </li>
            <li>
                <a href="../index.php" class="active">
                    <i class="fas fa-solid fa-house"></i>
                    <span class="nav-item-2">Home</span>
                </a>
            </li>
            <li>
                <a href="./dashboard.php">
                    <i class="fas fa-solid fa-gauge"></i>
                    <span class="nav-item-2">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="./scholarship_form.php">
                    <i class="fas fa-solid fa-file"></i>
                    <span class="nav-item-2">Scholarship Form</span>
                </a>
            </li>
            <li>
                <a href="./about.php">
                    <i class="fas fa-solid fa-circle-info"></i>
                    <span class="nav-item-2">About</span>
                </a>
            </li>
            <li>
                <a href="./faqs.php">
                    <i class="fas fa-solid fa-circle-question"></i>
                    <span class="nav-item-2">FAQs</span>
                </a>
            </li>
            <li>
                <a href="../auth/logout.php" class="logout">
                    <i class="fas fa-solid fa-right-from-bracket"></i>
                    <span class="nav-item-2">Logout</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="content">
        <h1>Welcome to the Office of Scholarship Programs</h1>
        <p>This section contains information and resources related to scholarships available for ZPPSU students. Please explore the sections below to learn more about the programs we offer, recent updates, and how to apply.</p>

        <!-- Announcements Section -->
        <section>
            <h2>Recent Announcements</h2>
            <ul>
                <li><strong>Deadline for Fall Semester Applications:</strong> October 15, 2024</li>
                <li><strong>New Scholarship Opportunities:</strong> Applications are now open for the 2024-2025 academic year.</li>
                <li><strong>Information Session:</strong> Join our virtual scholarship info session on September 30, 2024.</li>
            </ul>
        </section>

        <!-- Scholarship Opportunities Section -->
        <section>
            <h2>Available Scholarships</h2>
            <ul>
                <li><strong>Academic Excellence Scholarship:</strong> Full tuition for students with outstanding academic achievements.</li>
                <li><strong>Need-Based Financial Aid:</strong> Grants for students who demonstrate financial need.</li>
                <li><strong>Community Leadership Scholarship:</strong> Scholarships for students who show leadership in their communities.</li>
            </ul>
        </section>

        <!-- Application Information Section -->
        <section>
            <h2>How to Apply</h2>
            <p>To apply for a scholarship, follow these steps:</p>
            <ol>
                <li>Fill out the <a href="./scholarship_form.php"><i class="fa-solid fa-file"></i> Scholarship Application Form</a></li>
                <li>Submit all required documents, including transcripts and recommendation letters.</li>
                <li>Check your email for application status updates.</li>
            </ol>
        </section>

        <!-- Upcoming Events Section -->
        <section>
            <h2>Upcoming Events</h2>
            <ul>
                <li><strong>Scholarship Info Session:</strong> September 30, 2024, via Zoom</li>
                <li><strong>Scholarship Award Ceremony:</strong> November 15, 2024, ZPPSU Auditorium</li>
            </ul>
        </section>
    </div>
</body>

</html>