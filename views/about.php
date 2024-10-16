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
    <title>About - Office of Scholarship Programs - ZPPSU</title>

    <script src="../js/fontawesome.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/preloader.css?v=<?php echo time(); ?>">
    <script src="../js/preloader.js"></script>
</head>

<body>

    <div class="preloader">
    <img src="../assets/images/icons/scholarship_seal.png" alt="" style="height: 70px; width: 70px;">
    <div class="lds-facebook">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>

    <nav>
        <ul>
            <li>
                <a href="#" class="logo">
                    <img src="../assets/images/icons/scholarship_seal.png" alt="Scholarship Seal">
                    <span class="nav-item">OSP</span>
                </a>
            </li>
            <li>
                <a href="../index.php">
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
                <a href="./about.php" class="active">
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
        <h1>About Us</h1>
        <p>Welcome to the Office of Scholarship Programs at ZPPSU. Our mission is to provide students with the resources and support necessary to navigate the scholarship application process successfully.</p>

        <h2>Our Goals</h2>
        <p>We aim to:</p>
        <ul>
            <li>Provide information on available scholarships.</li>
            <li>Assist students in completing scholarship applications.</li>
            <li>Promote scholarship opportunities to enhance educational access.</li>
        </ul>

        <h2>Contact Us</h2>
        <p>If you have any questions or need assistance, please reach out to us at:</p>
        <p>Email: scholarships@zppsu.edu</p>
        <p>Phone: (123) 456-7890</p>
    </div>
</body>

</html>