<?php
include '../includes/session.php';

// Fetch announcements (latest 10)
$annQuery = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 10");
$announcements = $annQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Office of Scholarship Programs - ZPPSU</title>

    <script src="../js/fontawesome.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/preloader.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/home.css?v=<?php echo time(); ?>">
    <script src="../js/toggle_nav.js?v=<?php echo time(); ?>"></script>
    <script src="../js/preloader.js?v=<?php echo time(); ?>"></script>
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
        <button class="toggle-btn" onclick="toggleNav()">
            <i class="fas fa-bars" id="toggle-icon"></i>
        </button>
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
            <li>
                <a href="../index.php" class="activea">
                    <i class="fas fa-solid fa-house"></i>
                    <span class="nav-item-2">Home</span>
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
                    <a href="./faqs.php">
                        <i class="fas fa-solid fa-circle-question"></i>
                        <span class="nav-item-2">FAQs</span>
                    </a>
                </li>
            <?php elseif ($_SESSION["role"] === 'student'): ?>
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
        <div class="content-1">
            <h1>Welcome to the Office of Scholarship Programs</h1>
            <p>This section contains information and resources related to scholarships available for ZPPSU students.
                Please
                explore the sections below to learn more about the programs we offer, recent updates, and how to apply.
            </p>

            <!-- Announcements Section -->
            <section>
                <h2>Latest Announcements</h2>

                <?php if (count($announcements) === 0): ?>
                    <p>No announcements available at the moment.</p>
                <?php else: ?>
                    <div class="announcement-cards">
                        <?php foreach ($announcements as $a): ?>
                            <div class="announcement-card">
                                <h3><?= htmlspecialchars($a['title']); ?></h3>
                                <p><?= nl2br(htmlspecialchars($a['message'])); ?></p>
                                <span class="announcement-date">Posted: <?= $a['created_at']; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
            <!-- <section>
            <h2>Recent Announcements</h2>
            <ul class="ul1">
                <li><strong>Deadline for Fall Semester Applications:</strong> October 15, 2024</li>
                <li><strong>New Scholarship Opportunities:</strong> Applications are now open for the 2024-2025 academic
                    year.</li>
                <li><strong>Information Session:</strong> Join our virtual scholarship info session on September 30,
                    2024.</li>
            </ul>
         </section> -->

            <!-- Scholarship Opportunities Section
                <   section>
            <h2>Available Scholarships</h2>
            <ul class="ul1">
                <li><strong>Academic Excellence Scholarship:</strong> Full tuition for students with outstanding
                    academic achievements.</li>
                <li><strong>Need-Based Financial Aid:</strong> Grants for students who demonstrate financial need.</li>
                <li><strong>Community Leadership Scholarship:</strong> Scholarships for students who show leadership in
                    their communities.</li>
            </ul>
            </section> -->

            <!-- Application Information Section -->
            <section>
                <h2>How to Apply</h2>
                <p>To apply for a scholarship, follow these steps:</p>
                <ol class="ol1">
                    <li class="form-link">Fill out the <a href="./scholarship_form.php"><i class="fa-solid fa-file"></i>
                            Scholarship
                            Application Form</a></li>
                    <li>Submit all required documents, including transcripts and recommendation letters.</li>
                    <li>Check your email for application status updates.</li>
                </ol>
            </section>





            <!-- Upcoming Events Section
            <section>
            <h2>Upcoming Events</h2>
            <ul class="ul1">
                <li><strong>Scholarship Info Session:</strong> September 30, 2024, via Zoom</li>
                <li><strong>Scholarship Award Ceremony:</strong> November 15, 2024, ZPPSU Auditorium</li>
            </ul>
            </section> -->
        </div>
    </div>

</body>

</html>