<?php
include '../includes/session.php'
    ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About</title>
    <script src="../js/fontawesome.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/preloader.css?v=<?php echo time(); ?>">
    <script src="../js/preloader.js?v=<?php echo time(); ?>"></script>
    <link rel="stylesheet" href="../css/about.css?v=<?php echo time(); ?>">
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
                <li><a href="./logs.php"><i class="fas fa-clipboard-list"></i>
                        <span class="nav-item-2">Logs</span></a></li>
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
        <h1>About Us</h1>
        <p>Welcome to the Office of Scholarship Programs at ZPPSU. Our mission is to provide students with the resources
            and support necessary to navigate the scholarship application process successfully.</p>

        <h2>Meet Our Team</h2>
        <div class="profile-container">
            <div class="profile-card">
                <img src="../assets/images/member1.jpg" alt="Profile Picture 1">
                <div class="profile-info">
                    <p class="profile-name">Dr. John Smith</p>
                    <p class="profile-title">Director</p>
                    <p class="profile-description">With over 20 years of experience, Dr. Smith leads the office to guide
                        students in their scholarship journey.</p>
                </div>
            </div>
            <div class="profile-card">
                <img src="../assets/images/member2.jpg" alt="Profile Picture 2">
                <div class="profile-info">
                    <p class="profile-name">Ms. Lisa Brown</p>
                    <p class="profile-title">Scholarship Officer</p>
                    <p class="profile-description">Dedicated to assisting students with application forms and processing
                        scholarship requests.</p>
                </div>
            </div>
            <div class="profile-card">
                <img src="../assets/images/member3.jpg" alt="Profile Picture 3">
                <div class="profile-info">
                    <p class="profile-name">Mr. Mark Johnson</p>
                    <p class="profile-title">Administrative Assistant</p>
                    <p class="profile-description">Handles scheduling and assists in managing the day-to-day operations
                        of the office.</p>
                </div>
            </div>
        </div>

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
        <p>&copy; <?= date('Y') ?> Scholarship System</p>
    </div>


<<<<<<< HEAD
=======


>>>>>>> 5fe40da477c39321fb291661c5bfa63abd4656db
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

    </script>
</body>

</html>