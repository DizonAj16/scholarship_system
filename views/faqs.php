<?php
include '../includes/session.php'
    ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQs - Frequently Asked Questions</title>
    <script src="../js/fontawesome.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/preloader.css?v=<?php echo time(); ?>">
    <script src="../js/preloader.js?v=<?php echo time(); ?>"></script>
    <link rel="stylesheet" href="../css/faqs.css?v=<?php echo time(); ?>">
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

    <button class="toggle-btn" onclick="toggleNav()">
        <i class="fas fa-times" id="toggle-icon"></i>
    </button>

    <nav class="stroke" id="sideNav">
        <ul>
            <li>
                <a href="#" class="logo">
                    <div style="display: flex; align-items: center;">
                        <img src="../assets/images/icons/scholarship_seal.png" alt="Scholarship Seal">
                        <span class="username"><?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                    </div>
                    <span class="nav-item" style="margin-left: 10px;">OSP</span>
                </a>
            </li>
            <?php if ($_SESSION["role"] === 'admin'): ?>
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
                    <a href="./applications.php">
                        <i class="fas fa-solid fa-folder"></i>
                        <span class="nav-item-2">Applications</span>
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
                <li><a href="./logs.php"><i class="fas fa-clipboard-list"></i><span class="nav-item-2">Logs</span></a></li>
            <?php elseif ($_SESSION["role"] === 'student'): ?>
                <li>
                    <a href="../index.php">
                        <i class="fas fa-solid fa-house"></i>
                        <span class="nav-item-2">Home</span>
                    </a>
                </li>
                <li>
                    <a href="./my_applications.php">
                        <i class="fas fa-solid fa-folder-open"></i>
                        <span class="nav-item-2">My Applications</span>
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
            <?php endif; ?>
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
            <h2 onclick="toggleFAQ(this)">1. What types of scholarships are available? <i
                    class="fas fa-chevron-down"></i></h2>
            <p>We offer a variety of scholarships based on academic merit, financial need, and specific criteria such as
                field of study, community service, and more.</p>
        </div>

        <div class="faq-item">
            <h2 onclick="toggleFAQ(this)">2. How do I apply for a scholarship? <i class="fas fa-chevron-down"></i></h2>
            <p>To apply for a scholarship, you must fill out our scholarship application form available on our website.
                Ensure that you provide all necessary documents and information.</p>
        </div>

        <div class="faq-item">
            <h2 onclick="toggleFAQ(this)">3. When is the scholarship application deadline? <i
                    class="fas fa-chevron-down"></i></h2>
            <p>Deadlines for scholarship applications vary. Please check the specific scholarship details on our website
                for the most accurate information.</p>
        </div>

        <div class="faq-item">
            <h2 onclick="toggleFAQ(this)">4. Can I apply for multiple scholarships? <i class="fas fa-chevron-down"></i>
            </h2>
            <p>Yes, you are encouraged to apply for multiple scholarships that you are eligible for. Each scholarship
                will have its own criteria and requirements.</p>
        </div>

        <div class="faq-item">
            <h2 onclick="toggleFAQ(this)">5. Who can I contact for more information? <i class="fas fa-chevron-down"></i>
            </h2>
            <p>If you have any further questions, please contact us at scholarships@zppsu.edu or call us at (123)
                456-7890.</p>
        </div>

        <div class="faq-item">
            <h2 onclick="toggleFAQ(this)">6. What documents are needed for the application? <i
                    class="fas fa-chevron-down"></i></h2>
            <p>You will need to submit a valid ID, proof of income, academic records, and any other documents as
                specified in the scholarship guidelines.</p>
        </div>

        <div class="faq-item">
            <h2 onclick="toggleFAQ(this)">7. How are the scholarship recipients selected? <i
                    class="fas fa-chevron-down"></i></h2>
            <p>Selection is based on a comprehensive review of your application, including academic performance,
                financial need, and adherence to scholarship criteria.</p>
        </div>
    </div>

    <script>
        function toggleNav() {
            const sideNav = document.getElementById('sideNav');
            const toggleIcon = document.getElementById('toggle-icon');

            // Check if the navigation is currently open (visible)
            if (sideNav.style.transform === 'translateX(0px)' || sideNav.style.transform === '') {
                // Close the navigation
                sideNav.style.transform = 'translateX(-250px)';
                toggleIcon.classList.remove('fa-times');
                toggleIcon.classList.add('fa-bars');
            } else {
                // Open the navigation
                sideNav.style.transform = 'translateX(0px)';
                toggleIcon.classList.remove('fa-bars');
                toggleIcon.classList.add('fa-times');
            }
        }

        function toggleFAQ(element) {
            const faqItem = element.parentElement;
            faqItem.classList.toggle('active');
        }
    </script>
</body>

</html>