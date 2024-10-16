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
    <title>FAQs - Office of Scholarship Programs - ZPPSU</title>

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
                <a href="./about.php">
                    <i class="fas fa-solid fa-circle-info"></i>
                    <span class="nav-item-2">About</span>
                </a>
            </li>
            <li>
                <a href="./faqs.php" class="active">
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
        <h1>Frequently Asked Questions (FAQs)</h1>

        <div class="faq-item">
            <h2>1. What types of scholarships are available?</h2>
            <p>We offer a variety of scholarships based on academic merit, financial need, and specific criteria such as field of study, community service, and more.</p>
        </div>

        <div class="faq-item">
            <h2>2. How do I apply for a scholarship?</h2>
            <p>To apply for a scholarship, you must fill out our scholarship application form available on our website. Ensure that you provide all necessary documents and information.</p>
        </div>

        <div class="faq-item">
            <h2>3. When is the scholarship application deadline?</h2>
            <p>Deadlines for scholarship applications vary. Please check the specific scholarship details on our website for the most accurate information.</p>
        </div>

        <div class="faq-item">
            <h2>4. Can I apply for multiple scholarships?</h2>
            <p>Yes, you are encouraged to apply for multiple scholarships that you are eligible for. Each scholarship will have its own criteria and requirements.</p>
        </div>

        <div class="faq-item">
            <h2>5. Who can I contact for more information?</h2>
            <p>If you have any further questions, please contact us at scholarships@zppsu.edu or call us at (123) 456-7890.</p>
        </div>
    </div>
</body>

</html>