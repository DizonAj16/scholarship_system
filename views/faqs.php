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
    <script src="../js/toggle_nav.js?v=<?php echo time(); ?>"></script>
    <link rel="stylesheet" href="../css/faqs.css?v=<?php echo time(); ?>">
</head>

<body>

    <!-- <div class="preloader">
        <img src="../assets/images/icons/scholarship_seal.png" alt="" style="height: 70px; width: 70px;">
        <div class="lds-facebook">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div> -->

    <!-- <button class="toggle-btn" onclick="toggleNav()">
        <i class="fas fa-times" id="toggle-icon"></i>
    </button> -->

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

                <li><a href="./announcement.php"><i class="fas fa-bullhorn"></i>
                        <span class="nav-item-2">Announcements</span></a>
                </li>

                <li>
                    <a href="./scholarship_form.php">
                        <i class="fas fa-solid fa-file"></i>
                        <span class="nav-item-2">Scholarship Form</span>
                    </a>
                </li>
                <li><a href="./manage_dropdowns.php"><i class="fas fa-list"></i><span class="nav-item-2">Manage
                            Dropdowns</span></a></li>
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
                    <a href="./faqs.php" class="activea">
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
                    <a href="./faqs.php" class="activea">
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
        <button class="toggle-btn" onclick="toggleNav()">
            <i class="fas fa-bars" id="toggle-icon"></i>
        </button>

    </nav>


    <div class="content">
        <h1>Frequently Asked Questions (FAQs)</h1>

        <div class="faq-item">
            <h2 onclick="toggleFAQ(this)">1. What types of scholarships are available? <i
                    class="fas fa-chevron-down"></i></h2>
            <p style="display: none;">We offer a variety of scholarships based on academic merit, financial need, and
                specific criteria such as field of study, community service, and more.</p>
        </div>

        <div class="faq-item">
            <h2 onclick="toggleFAQ(this)">2. How do I apply for a scholarship? <i class="fas fa-chevron-down"></i></h2>
            <p style="display: none;">To apply for a scholarship, you must fill out our scholarship application form
                available on our website. Ensure that you provide all necessary documents and information.</p>
        </div>

        <div class="faq-item">
            <h2 onclick="toggleFAQ(this)">3. When is the scholarship application deadline? <i
                    class="fas fa-chevron-down"></i></h2>
            <p style="display: none;">Deadlines for scholarship applications vary. Please check the specific scholarship
                details on our website for the most accurate information.</p>
        </div>

        <div class="faq-item">
            <h2 onclick="toggleFAQ(this)">4. Can I apply for multiple scholarships? <i class="fas fa-chevron-down"></i>
            </h2>
            <p style="display: none;">Yes, you are encouraged to apply for multiple scholarships that you are eligible
                for. Each scholarship will have its own criteria and requirements.</p>
        </div>

        <div class="faq-item">
            <h2 onclick="toggleFAQ(this)">5. Who can I contact for more information? <i class="fas fa-chevron-down"></i>
            </h2>
            <p style="display: none;">If you have any further questions, please contact us at scholarships@zppsu.edu or
                call us at (123) 456-7890.</p>
        </div>

        <div class="faq-item">
            <h2 onclick="toggleFAQ(this)">6. What documents are needed for the application? <i
                    class="fas fa-chevron-down"></i></h2>
            <p style="display: none;">You will need to submit a valid ID, proof of income, academic records, and any
                other documents as specified in the scholarship guidelines.</p>
        </div>

        <div class="faq-item">
            <h2 onclick="toggleFAQ(this)">7. How are the scholarship recipients selected? <i
                    class="fas fa-chevron-down"></i></h2>
            <p style="display: none;">Selection is based on a comprehensive review of your application, including
                academic performance, financial need, and adherence to scholarship criteria.</p>
        </div>

        <div class="faq-item">
            <h2 onclick="toggleFAQ(this)">8. What types of scholarships are available in the Philippines? <i
                    class="fas fa-chevron-down"></i></h2>
            <p style="display: none;">
                We offer various types of scholarships, including: <br>
                <strong>CHED Scholarships:</strong> Scholarships offered by the Commission on Higher Education, such as
                the Full Merit Scholarship, Half Merit Scholarship, and Tulong Dunong Program.
                <br>
                <strong>DOST-SEI Scholarship:</strong> Scholarships for students pursuing science, technology,
                engineering, and mathematics (STEM) fields provided by the Department of Science and Technology.
                <br>
                <strong>LGU Scholarships:</strong> Scholarships provided by local government units for deserving
                residents of their community.
                <br>
                <strong>Academic Scholarships:</strong> Offered by universities for students with outstanding academic
                achievements.
                <br>
                <strong>Private Sector Scholarships:</strong> Scholarships funded by private companies, foundations, or
                organizations to support specific groups of students.
                <br>
                <strong>Athletic Scholarships:</strong> Financial aid given to student-athletes who excel in sports and
                academics.
                <br>
                <strong>TESDA Scholarships:</strong> Vocational and technical education scholarships provided by the
                Technical Education and Skills Development Authority.
                <br>
                <strong>Specialized Scholarships:</strong> Scholarships based on criteria such as leadership, community
                service, or specific fields of study.
                <br>
        </div>

    </div>

    <script>


        function toggleFAQ(element) {
            const answer = element.nextElementSibling;
            const icon = element.querySelector('i');

            if (answer.style.display === "none") {
                answer.style.display = "block";
                icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
            } else {
                answer.style.display = "none";
                icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
            }
        }
    </script>
</body>

</html>