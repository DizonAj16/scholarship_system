<?php
// Include session and database connection
include '../includes/session.php';

// Query to get total applicants
$query = "SELECT COUNT(*) AS total_applicants FROM scholarship_applications";
$stmt = $pdo->prepare($query); // Assuming $pdo is the PDO object
$stmt->execute();
$total_applicants = $stmt->fetch(PDO::FETCH_ASSOC)['total_applicants'];

// Query to get approved applications
$query_approved = "SELECT COUNT(*) AS approved_applications FROM scholarship_applications WHERE status = 'approved'";
$stmt_approved = $pdo->prepare($query_approved);
$stmt_approved->execute();
$approved_applications = $stmt_approved->fetch(PDO::FETCH_ASSOC)['approved_applications'];

// Query to get pending applications
$query_pending = "SELECT COUNT(*) AS pending_applications FROM scholarship_applications WHERE status = 'pending'";
$stmt_pending = $pdo->prepare($query_pending);
$stmt_pending->execute();
$pending_applications = $stmt_pending->fetch(PDO::FETCH_ASSOC)['pending_applications'];

// Query to get rejected applications
$query_rejected = "SELECT COUNT(*) AS rejected_applications FROM scholarship_applications WHERE status = 'rejected'";
$stmt_rejected = $pdo->prepare($query_rejected);
$stmt_rejected->execute();
$rejected_applications = $stmt_rejected->fetch(PDO::FETCH_ASSOC)['rejected_applications'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="../js/fontawesome.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/preloader.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/dashboard.css?v=<?php echo time(); ?>">
    <script src="../js/preloader.js?v=<?php echo time(); ?>"></script>
</head>

<body>
    <div class="preloader">
        <img src="../assets/images/icons/scholarship_seal.png" alt="Scholarship Seal"
            style="height: 70px; width: 70px;">
        <div class="lds-facebook">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>

    <nav class="stroke">
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
                <li><a href="./dashboard.php" class="active"><i class="fas fa-solid fa-gauge"></i><span
                            class="nav-item-2">Dashboard</span></a></li>
                <li><a href="./scholarship_form.php"><i class="fas fa-solid fa-file"></i><span
                            class="nav-item-2">Scholarship Form</span></a></li>
                <li><a href="./applications.php"><i class="fas fa-solid fa-folder"></i><span
                            class="nav-item-2">Applications</span></a></li>
                <li><a href="./about.php"><i class="fas fa-solid fa-circle-info"></i><span
                            class="nav-item-2">About</span></a></li>
                <li><a href="./faqs.php"><i class="fas fa-solid fa-circle-question"></i><span
                            class="nav-item-2">FAQs</span></a></li>
                <li><a href="./logs.php"><i class="fas fa-clipboard-list"></i>
<<<<<<< HEAD
                <span
                            class="nav-item-2">Logs</span></a></li>
=======
                        <span class="nav-item-2">Logs</span></a></li>
>>>>>>> 5fe40da477c39321fb291661c5bfa63abd4656db
            <?php elseif ($_SESSION["role"] === 'student'): ?>
                <li><a href="./my_applications.php"><i class="fas fa-solid fa-folder-open"></i><span class="nav-item-2">My
                            Applications</span></a></li>
                <li><a href="./scholarship_form.php"><i class="fas fa-solid fa-file"></i><span
                            class="nav-item-2">Scholarship Form</span></a></li>
                <li><a href="./resources.php"><i class="fas fa-solid fa-book"></i><span
                            class="nav-item-2">Resources</span></a></li>
                <li><a href="./about.php"><i class="fas fa-solid fa-circle-info"></i><span
                            class="nav-item-2">About</span></a></li>
                <li><a href="./faqs.php"><i class="fas fa-solid fa-circle-question"></i><span
                            class="nav-item-2">FAQs</span></a></li>
                <li><a href="./contact.php"><i class="fas fa-solid fa-envelope"></i><span class="nav-item-2">Contact
                            Us</span></a></li>
            <?php endif; ?>
            <li><a href="../auth/logout.php" class="logout"><i class="fas fa-solid fa-right-from-bracket"></i><span
                        class="nav-item-2">Logout</span></a></li>
        </ul>
    </nav>

    <div class="content">
        <h1>Dashboard</h1>
        <p>Welcome back! Here you can find various resources and updates related to the Office of Scholarship Programs.
        </p>

        <!-- Dashboard Cards -->
        <div class="card-container">
<<<<<<< HEAD
            <div class="card">
                <i class="fas fa-users"></i>
                <h3>Total Applicants</h3>
                <p class="card-value"><?php echo $total_applicants; ?></p>
            </div>
            <div class="card">
                <i class="fas fa-check-circle"></i>
                <h3>Approved Applications</h3>
                <p class="card-value"><?php echo $approved_applications; ?></p>
            </div>
            <div class="card">
                <i class="fas fa-hourglass-half"></i>
                <h3>Pending Applications</h3>
                <p class="card-value"><?php echo $pending_applications; ?></p>
            </div>
            <div class="card">
                <i class="fas fa-times-circle"></i>
                <h3>Rejected Applications</h3>
                <p class="card-value"><?php echo $rejected_applications; ?></p>
            </div>
=======
            <!-- Total Applicants -->
            <a href="./applications.php" class="card">
                <i class="fas fa-users"></i>
                <h3>Total Applicants</h3>
                <p class="card-value"><?php echo $total_applicants; ?></p>
            </a>

            <!-- Approved Applications -->
            <a href="../dashboard-links/approved.php" class="card">
                <i class="fas fa-check-circle"></i>
                <h3>Approved Applications</h3>
                <p class="card-value"><?php echo $approved_applications; ?></p>
            </a>

            <!-- Pending Applications -->
            <a href="../dashboard-links/pending.php" class="card">
                <i class="fas fa-hourglass-half"></i>
                <h3>Pending Applications</h3>
                <p class="card-value"><?php echo $pending_applications; ?></p>
            </a>

            <!-- Rejected Applications -->
            <a href="../dashboard-links/rejected.php" class="card">
                <i class="fas fa-times-circle"></i>
                <h3>Rejected Applications</h3>
                <p class="card-value"><?php echo $rejected_applications; ?></p>
            </a>
>>>>>>> 5fe40da477c39321fb291661c5bfa63abd4656db
            <div class="card">
                <i class="fas fa-calendar-alt"></i>
                <h3>Upcoming Events</h3>
                <p>No upcoming events at this time. Please check back later!</p>
            </div>
            <div class="card">
                <i class="fas fa-history"></i>
                <h3>Recent Activities</h3>
                <p>Stay tuned for updates on recent scholarship activities.</p>
            </div>
            <div class="card">
                <i class="fas fa-book"></i>
                <h3>Resources</h3>
                <p>We will provide links to useful resources soon!</p>
            </div>
        </div>
    </div>
</body>

</html>