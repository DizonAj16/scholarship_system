<?php
// Include session and database connection
include '../includes/session.php';

// Query to get total applicants
$query = "SELECT COUNT(*) AS total_applicants FROM scholarship_applications";
$stmt = $pdo->prepare($query);
$stmt->execute();
$total_applicants = $stmt->fetch(PDO::FETCH_ASSOC)['total_applicants'] ?? 0;

// Query to get approved applications
$query_approved = "SELECT COUNT(*) AS approved_applications FROM scholarship_applications WHERE status = 'approved'";
$stmt_approved = $pdo->prepare($query_approved);
$stmt_approved->execute();
$approved_applications = $stmt_approved->fetch(PDO::FETCH_ASSOC)['approved_applications'] ?? 0;

// Query to get pending applications
$query_pending = "SELECT COUNT(*) AS pending_applications FROM scholarship_applications WHERE status = 'pending'";
$stmt_pending = $pdo->prepare($query_pending);
$stmt_pending->execute();
$pending_applications = $stmt_pending->fetch(PDO::FETCH_ASSOC)['pending_applications'] ?? 0;

// Query to get rejected applications
$query_rejected = "SELECT COUNT(*) AS rejected_applications FROM scholarship_applications WHERE status = 'not qualified'";
$stmt_rejected = $pdo->prepare($query_rejected);
$stmt_rejected->execute();
$rejected_applications = $stmt_rejected->fetch(PDO::FETCH_ASSOC)['rejected_applications'] ?? 0;

// Query for scholarship grants distribution
$grant_query = "SELECT scholarship_grant, COUNT(*) as count FROM scholarship_applications WHERE scholarship_grant IS NOT NULL AND scholarship_grant != '' GROUP BY scholarship_grant";
$grant_stmt = $pdo->prepare($grant_query);
$grant_stmt->execute();
$grant_data = $grant_stmt->fetchAll(PDO::FETCH_ASSOC);
$grant_labels = [];
$grant_counts = [];
foreach ($grant_data as $grant) {
    $grant_labels[] = !empty($grant['scholarship_grant']) ? $grant['scholarship_grant'] : 'Not Specified';
    $grant_counts[] = (int) $grant['count'];
}
if (empty($grant_labels)) {
    $grant_labels = ['No Data Available'];
    $grant_counts = [0];
}

// Query for gender distribution
$gender_query = "SELECT sex, COUNT(*) as count FROM scholarship_applications WHERE sex IS NOT NULL AND sex != '' GROUP BY sex";
$gender_stmt = $pdo->prepare($gender_query);
$gender_stmt->execute();
$gender_data = $gender_stmt->fetchAll(PDO::FETCH_ASSOC);
$gender_labels = [];
$gender_counts = [];
foreach ($gender_data as $gender) {
    $gender_labels[] = !empty($gender['sex']) ? ucfirst($gender['sex']) : 'Not Specified';
    $gender_counts[] = (int) $gender['count'];
}
if (empty($gender_labels)) {
    $gender_labels = ['No Data Available'];
    $gender_counts = [0];
}

// Query for course distribution (top 10) - FIXED with proper null check
$course_query = "SELECT course, COUNT(*) as count FROM scholarship_applications WHERE course IS NOT NULL AND course != '' GROUP BY course ORDER BY count DESC LIMIT 10";
$course_stmt = $pdo->prepare($course_query);
$course_stmt->execute();
$course_data = $course_stmt->fetchAll(PDO::FETCH_ASSOC);
$course_labels = [];
$course_counts = [];
foreach ($course_data as $course) {
    $course_labels[] = !empty($course['course']) ? $course['course'] : 'Not Specified';
    $course_counts[] = (int) $course['count'];
}
if (empty($course_labels)) {
    $course_labels = ['No Data Available'];
    $course_counts = [0];
}

// Query for applications by month (last 6 months)
$month_query = "
    SELECT 
        DATE_FORMAT(date, '%Y-%m') as month,
        COUNT(*) as count
    FROM scholarship_applications 
    WHERE date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(date, '%Y-%m')
    ORDER BY month ASC
";
$month_stmt = $pdo->prepare($month_query);
$month_stmt->execute();
$month_data = $month_stmt->fetchAll(PDO::FETCH_ASSOC);
$month_labels = [];
$month_counts = [];
foreach ($month_data as $month) {
    $month_labels[] = date('M Y', strtotime($month['month'] . '-01'));
    $month_counts[] = (int) $month['count'];
}
if (empty($month_labels)) {
    // Create empty labels for last 6 months
    $month_labels = [];
    $month_counts = [];
    for ($i = 5; $i >= 0; $i--) {
        $month_labels[] = date('M Y', strtotime("-$i months"));
        $month_counts[] = 0;
    }
}

// NEW QUERIES FOR ADDITIONAL STATISTICS

// Query for civil status distribution - FIXED
$civil_status_query = "SELECT civil_status, COUNT(*) as count FROM scholarship_applications WHERE civil_status IS NOT NULL AND civil_status != '' GROUP BY civil_status";
$civil_status_stmt = $pdo->prepare($civil_status_query);
$civil_status_stmt->execute();
$civil_status_data = $civil_status_stmt->fetchAll(PDO::FETCH_ASSOC);
$civil_status_labels = [];
$civil_status_counts = [];
foreach ($civil_status_data as $status) {
    $civil_status_labels[] = !empty($status['civil_status']) ? ucfirst($status['civil_status']) : 'Not Specified';
    $civil_status_counts[] = (int) $status['count'];
}
if (empty($civil_status_labels)) {
    $civil_status_labels = ['No Data Available'];
    $civil_status_counts = [0];
}

// Query for religion distribution - FIXED
$religion_query = "SELECT religion, COUNT(*) as count FROM scholarship_applications WHERE religion IS NOT NULL AND religion != '' GROUP BY religion";
$religion_stmt = $pdo->prepare($religion_query);
$religion_stmt->execute();
$religion_data = $religion_stmt->fetchAll(PDO::FETCH_ASSOC);
$religion_labels = [];
$religion_counts = [];
foreach ($religion_data as $religion) {
    $religion_labels[] = !empty($religion['religion']) ? ucfirst($religion['religion']) : 'Not Specified';
    $religion_counts[] = (int) $religion['count'];
}
if (empty($religion_labels)) {
    $religion_labels = ['No Data Available'];
    $religion_counts = [0];
}

// Query for indigenous group distribution - FIXED
$indigenous_query = "SELECT indigenous_group, COUNT(*) as count FROM scholarship_applications WHERE indigenous_group IS NOT NULL AND indigenous_group != '' AND indigenous_group != 'N/A' GROUP BY indigenous_group";
$indigenous_stmt = $pdo->prepare($indigenous_query);
$indigenous_stmt->execute();
$indigenous_data = $indigenous_stmt->fetchAll(PDO::FETCH_ASSOC);
$indigenous_labels = [];
$indigenous_counts = [];
foreach ($indigenous_data as $group) {
    $indigenous_labels[] = !empty($group['indigenous_group']) ? ucfirst($group['indigenous_group']) : 'Not Specified';
    $indigenous_counts[] = (int) $group['count'];
}
if (empty($indigenous_labels)) {
    $indigenous_labels = ['No Data Available'];
    $indigenous_counts = [0];
}

// Query for semester distribution - FIXED
$semester_query = "SELECT semester, COUNT(*) as count FROM scholarship_applications WHERE semester IS NOT NULL AND semester != '' GROUP BY semester";
$semester_stmt = $pdo->prepare($semester_query);
$semester_stmt->execute();
$semester_data = $semester_stmt->fetchAll(PDO::FETCH_ASSOC);
$semester_labels = [];
$semester_counts = [];
foreach ($semester_data as $semester) {
    $semester_labels[] = !empty($semester['semester']) ? ucfirst($semester['semester']) : 'Not Specified';
    $semester_counts[] = (int) $semester['count'];
}
if (empty($semester_labels)) {
    $semester_labels = ['No Data Available'];
    $semester_counts = [0];
}

// Query for school year distribution - FIXED
$sy_query = "SELECT school_year, COUNT(*) as count FROM scholarship_applications WHERE school_year IS NOT NULL AND school_year != '' GROUP BY school_year ORDER BY school_year DESC";
$sy_stmt = $pdo->prepare($sy_query);
$sy_stmt->execute();
$sy_data = $sy_stmt->fetchAll(PDO::FETCH_ASSOC);
$sy_labels = [];
$sy_counts = [];
foreach ($sy_data as $sy) {
    $sy_labels[] = !empty($sy['school_year']) ? $sy['school_year'] : 'Not Specified';
    $sy_counts[] = (int) $sy['count'];
}
if (empty($sy_labels)) {
    $sy_labels = ['No Data Available'];
    $sy_counts = [0];
}

// Query for age distribution - FIXED
$age_query = "
    SELECT 
        CASE 
            WHEN age < 18 THEN 'Under 18'
            WHEN age BETWEEN 18 AND 21 THEN '18-21'
            WHEN age BETWEEN 22 AND 25 THEN '22-25'
            WHEN age BETWEEN 26 AND 30 THEN '26-30'
            ELSE 'Over 30'
        END as age_group,
        COUNT(*) as count
    FROM scholarship_applications 
    WHERE age IS NOT NULL
    GROUP BY age_group
    ORDER BY 
        CASE age_group
            WHEN 'Under 18' THEN 1
            WHEN '18-21' THEN 2
            WHEN '22-25' THEN 3
            WHEN '26-30' THEN 4
            ELSE 5
        END
";
$age_stmt = $pdo->prepare($age_query);
$age_stmt->execute();
$age_data = $age_stmt->fetchAll(PDO::FETCH_ASSOC);
$age_labels = [];
$age_counts = [];
foreach ($age_data as $age) {
    $age_labels[] = $age['age_group'];
    $age_counts[] = (int) $age['count'];
}
// If no age data, create placeholders for all age groups
if (empty($age_labels)) {
    $age_labels = ['Under 18', '18-21', '22-25', '26-30', 'Over 30'];
    $age_counts = [0, 0, 0, 0, 0];
}

// Query for house status distribution - FIXED with LEFT JOIN for better results
$house_query = "SELECT h.house_status, COUNT(*) as count 
                FROM scholarship_applications s 
                LEFT JOIN house_info h ON h.application_id = s.application_id 
                WHERE h.house_status IS NOT NULL AND h.house_status != ''
                GROUP BY h.house_status";
$house_stmt = $pdo->prepare($house_query);
$house_stmt->execute();
$house_data = $house_stmt->fetchAll(PDO::FETCH_ASSOC);
$house_labels = [];
$house_counts = [];
foreach ($house_data as $house) {
    $house_labels[] = !empty($house['house_status']) ? ucfirst($house['house_status']) : 'Not Specified';
    $house_counts[] = (int) $house['count'];
}
if (empty($house_labels)) {
    $house_labels = ['No Data Available'];
    $house_counts = [0];
}

// Query for average income - FIXED with proper null check
$income_query = "SELECT 
                    COALESCE(AVG(father_income), 0) as avg_father_income, 
                    COALESCE(AVG(mother_income), 0) as avg_mother_income 
                 FROM parents_info 
                 WHERE father_income IS NOT NULL OR mother_income IS NOT NULL";
$income_stmt = $pdo->prepare($income_query);
$income_stmt->execute();
$income_data = $income_stmt->fetch(PDO::FETCH_ASSOC);
$avg_father_income = number_format($income_data['avg_father_income'] ?? 0, 2);
$avg_mother_income = number_format($income_data['avg_mother_income'] ?? 0, 2);

// Additional statistics queries
$age_avg_query = "SELECT COALESCE(AVG(age), 0) as avg_age FROM scholarship_applications WHERE age IS NOT NULL";
$age_avg_stmt = $pdo->prepare($age_avg_query);
$age_avg_stmt->execute();
$avg_age_data = $age_avg_stmt->fetch(PDO::FETCH_ASSOC);
$avg_age = number_format($avg_age_data['avg_age'] ?? 0, 1);

$top_course_query = "SELECT course, COUNT(*) as count FROM scholarship_applications WHERE course IS NOT NULL AND course != '' GROUP BY course ORDER BY count DESC LIMIT 1";
$top_course_stmt = $pdo->prepare($top_course_query);
$top_course_stmt->execute();
$top_course_data = $top_course_stmt->fetch(PDO::FETCH_ASSOC);
$top_course = $top_course_data['course'] ?? 'N/A';
$top_course_count = $top_course_data['count'] ?? 0;

$top_grant_query = "SELECT scholarship_grant, COUNT(*) as count FROM scholarship_applications WHERE scholarship_grant IS NOT NULL AND scholarship_grant != '' GROUP BY scholarship_grant ORDER BY count DESC LIMIT 1";
$top_grant_stmt = $pdo->prepare($top_grant_query);
$top_grant_stmt->execute();
$top_grant_data = $top_grant_stmt->fetch(PDO::FETCH_ASSOC);
$top_grant = $top_grant_data['scholarship_grant'] ?? 'N/A';
$top_grant_count = $top_grant_data['count'] ?? 0;

$monthly_query = "SELECT COUNT(*) as count FROM scholarship_applications WHERE MONTH(date) = MONTH(CURRENT_DATE()) AND YEAR(date) = YEAR(CURRENT_DATE())";
$monthly_stmt = $pdo->prepare($monthly_query);
$monthly_stmt->execute();
$monthly_count_data = $monthly_stmt->fetch(PDO::FETCH_ASSOC);
$monthly_count = $monthly_count_data['count'] ?? 0;

$process_query = "SELECT COALESCE(AVG(DATEDIFF(NOW(), date)), 0) as avg_days FROM scholarship_applications WHERE status != 'pending' AND date IS NOT NULL";
$process_stmt = $pdo->prepare($process_query);
$process_stmt->execute();
$process_data = $process_stmt->fetch(PDO::FETCH_ASSOC);
$avg_days = number_format($process_data['avg_days'] ?? 0, 1);

// Calculate rates
$approval_rate = ($total_applicants > 0) ? ($approved_applications / $total_applicants) * 100 : 0;
$pending_rate = ($total_applicants > 0) ? ($pending_applications / $total_applicants) * 100 : 0;
$rejection_rate = ($total_applicants > 0) ? ($rejected_applications / $total_applicants) * 100 : 0;

$annQuery = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5");
$announcements = $annQuery->fetchAll(PDO::FETCH_ASSOC);

// Debug output (remove this in production)
error_log("Dashboard Data - Total: $total_applicants, Approved: $approved_applications, Pending: $pending_applications, Rejected: $rejected_applications");
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 30px;
        }

        .chart-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .chart-card.full-width {
            grid-column: 1 / -1;
        }

        .chart-card h3 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #333;
            font-size: 18px;
            border-bottom: 2px solid #4a6cf7;
            padding-bottom: 10px;
        }

        .chart-wrapper {
            position: relative;
            height: 300px;
        }

        @media (max-width: 1024px) {
            .chart-container {
                grid-template-columns: 1fr;
            }
        }

        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-decoration: none;
            display: block;
            color: inherit;
            cursor: pointer;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            text-decoration: none;
        }

        .stat-card i {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .stat-card.total {
            border-top: 4px solid #007bff;
        }

        .stat-card.approved {
            border-top: 4px solid #28a745;
        }

        .stat-card.pending {
            border-top: 4px solid #ffc107;
        }

        .stat-card.rejected {
            border-top: 4px solid #dc3545;
        }

        .stat-card.income {
            border-top: 4px solid #6f42c1;
            cursor: default;
        }

        .stat-card h3 {
            margin: 10px 0;
            color: #555;
            font-size: 16px;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
            margin: 10px 0;
        }

        .stat-subtext {
            font-size: 0.9rem;
            color: #777;
            margin-top: 5px;
        }

        .dashboard-header {
            margin-bottom: 30px;
        }

        .dashboard-title h1 {
            margin: 0;
            color: #333;
            font-size: 2rem;
        }

        .dashboard-title p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 1rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .info-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .info-card h4 {
            margin-top: 0;
            color: #4a6cf7;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #eee;
        }

        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .info-label {
            color: #666;
        }

        .info-value {
            font-weight: bold;
            color: #333;
        }

        .income-comparison {
            display: flex;
            justify-content: space-around;
            margin-top: 15px;
        }

        .income-item {
            text-align: center;
        }

        .income-amount {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }

        .income-label {
            font-size: 0.9rem;
            color: #666;
            margin-top: 5px;
        }

        .no-data-message {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>

<body>
    <!-- <div class="preloader">
        <img src="../assets/images/icons/scholarship_seal.png" alt="Scholarship Seal"
            style="height: 70px; width: 70px;">
        <div class="lds-facebook">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div> -->

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
                <li><a href="./announcement.php"><i class="fas fa-bullhorn"></i><span
                            class="nav-item-2">Announcements</span></a></li>
                <li><a href="./scholarship_form.php"><i class="fas fa-solid fa-file"></i><span
                            class="nav-item-2">Scholarship Form</span></a></li>
                <li><a href="./manage_dropdowns.php"><i class="fas fa-list"></i><span class="nav-item-2">Manage
                            Dropdowns</span></a></li>

                <li><a href="./applications.php"><i class="fas fa-solid fa-folder"></i><span
                            class="nav-item-2">Applications</span></a></li>
                <li><a href="./logs.php"><i class="fas fa-clipboard-list"></i><span class="nav-item-2">Logs</span></a></li>
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
        <div class="dashboard-header">
            <div class="dashboard-title">
                <h1>Dashboard</h1>
                <p>Welcome back! Here you can find various resources and updates related to the Office of Scholarship
                    Programs.</p>
                <?php if ($total_applicants == 0): ?>
                    <p style="color: #dc3545; font-weight: bold; margin-top: 10px;">
                        <i class="fas fa-exclamation-triangle"></i> No applications found in the database. Submit some
                        applications to see statistics.
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Statistics Cards (CLICKABLE) -->
        <div class="stats-summary">
            <a href="./applications.php" class="stat-card total">
                <i class="fas fa-users" style="color: #007bff;"></i>
                <h3>Total Applicants</h3>
                <p class="stat-value"><?php echo $total_applicants; ?></p>
                <p class="stat-subtext">View all applications</p>
            </a>

            <a href="../dashboard-links/approved.php" class="stat-card approved">
                <i class="fas fa-check-circle" style="color: #28a745;"></i>
                <h3>Approved</h3>
                <p class="stat-value"><?php echo $approved_applications; ?></p>
                <p class="stat-subtext">View approved applications</p>
            </a>

            <a href="../dashboard-links/pending.php" class="stat-card pending">
                <i class="fas fa-hourglass-half" style="color: #ffc107;"></i>
                <h3>Pending</h3>
                <p class="stat-value"><?php echo $pending_applications; ?></p>
                <p class="stat-subtext">View pending applications</p>
            </a>

            <a href="../dashboard-links/rejected.php" class="stat-card rejected">
                <i class="fas fa-times-circle" style="color: #dc3545;"></i>
                <h3>Not Qualified</h3>
                <p class="stat-value"><?php echo $rejected_applications; ?></p>
                <p class="stat-subtext">View not qualified applications</p>
            </a>

            <!-- <div class="stat-card income">
                <i class="fas fa-money-bill-wave" style="color: #6f42c1;"></i>
                <h3>Average Parent Income</h3>
                <div class="income-comparison">
                    <div class="income-item">
                        <div class="income-amount">₱<?php echo $avg_father_income; ?></div>
                        <div class="income-label">Father</div>
                    </div>
                    <div class="income-item">
                        <div class="income-amount">₱<?php echo $avg_mother_income; ?></div>
                        <div class="income-label">Mother</div>
                    </div>
                </div>
            </div> -->
        </div>

        <!-- Additional Statistics Grid -->
        <?php if ($total_applicants > 0): ?>
            <div class="stats-grid">
                <div class="info-card">
                    <h4>Demographic Information</h4>
                    <div class="info-item">
                        <span class="info-label">Average Age:</span>
                        <span class="info-value"><?php echo $avg_age; ?> years</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Most Common Course:</span>
                        <span class="info-value"><?php echo htmlspecialchars($top_course); ?>
                            (<?php echo $top_course_count; ?>)</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Most Popular Grant:</span>
                        <span class="info-value"><?php echo htmlspecialchars($top_grant); ?>
                            (<?php echo $top_grant_count; ?>)</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Applications This Month:</span>
                        <span class="info-value"><?php echo $monthly_count; ?></span>
                    </div>
                </div>

                <div class="info-card">
                    <h4>Status Overview</h4>
                    <div class="info-item">
                        <span class="info-label">Approval Rate:</span>
                        <span class="info-value"><?php echo number_format($approval_rate, 1); ?>%</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Pending Rate:</span>
                        <span class="info-value"><?php echo number_format($pending_rate, 1); ?>%</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Rejection Rate:</span>
                        <span class="info-value"><?php echo number_format($rejection_rate, 1); ?>%</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Processing Time:</span>
                        <span class="info-value"><?php echo $avg_days; ?> days</span>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Charts Section -->
        <div class="chart-container">
            <!-- Application Status Chart -->
            <div class="chart-card">
                <h3>Application Status Distribution</h3>
                <div class="chart-wrapper">
                    <canvas id="statusChart"></canvas>
                </div>
                <?php if ($total_applicants == 0): ?>
                    <div class="no-data-message">No application data available</div>
                <?php endif; ?>
            </div>

            <!-- Gender Distribution Chart -->
            <div class="chart-card">
                <h3>Gender Distribution</h3>
                <div class="chart-wrapper">
                    <canvas id="genderChart"></canvas>
                </div>
                <?php if (empty($gender_data) || $total_applicants == 0): ?>
                    <div class="no-data-message">No gender data available</div>
                <?php endif; ?>
            </div>

            <!-- Age Distribution Chart -->
            <div class="chart-card">
                <h3>Age Group Distribution</h3>
                <div class="chart-wrapper">
                    <canvas id="ageChart"></canvas>
                </div>
                <?php if (empty($age_data) || $total_applicants == 0): ?>
                    <div class="no-data-message">No age data available</div>
                <?php endif; ?>
            </div>

            <!-- Civil Status Chart -->
            <div class="chart-card">
                <h3>Civil Status Distribution</h3>
                <div class="chart-wrapper">
                    <canvas id="civilStatusChart"></canvas>
                </div>
                <?php if (empty($civil_status_data) || $total_applicants == 0): ?>
                    <div class="no-data-message">No civil status data available</div>
                <?php endif; ?>
            </div>

            <!-- Scholarship Grants Distribution -->
            <div class="chart-card full-width">
                <h3>Scholarship Grants Distribution</h3>
                <div class="chart-wrapper">
                    <canvas id="grantsChart"></canvas>
                </div>
                <?php if (empty($grant_data) || $total_applicants == 0): ?>
                    <div class="no-data-message">No scholarship grant data available</div>
                <?php endif; ?>
            </div>

            <!-- Applications Over Time -->
            <div class="chart-card full-width">
                <h3>Applications Over Time (Last 6 Months)</h3>
                <div class="chart-wrapper">
                    <canvas id="timelineChart"></canvas>
                </div>
                <?php if (empty($month_data) || $total_applicants == 0): ?>
                    <div class="no-data-message">No timeline data available</div>
                <?php endif; ?>
            </div>

            <!-- Top Courses -->
            <div class="chart-card full-width">
                <h3>Top Courses by Applications</h3>
                <div class="chart-wrapper">
                    <canvas id="coursesChart"></canvas>
                </div>
                <?php if (empty($course_data) || $total_applicants == 0): ?>
                    <div class="no-data-message">No course data available</div>
                <?php endif; ?>
            </div>

            <!-- Semester Distribution -->
            <div class="chart-card">
                <h3>Semester Distribution</h3>
                <div class="chart-wrapper">
                    <canvas id="semesterChart"></canvas>
                </div>
                <?php if (empty($semester_data) || $total_applicants == 0): ?>
                    <div class="no-data-message">No semester data available</div>
                <?php endif; ?>
            </div>

            <!-- School Year Distribution -->
            <div class="chart-card">
                <h3>School Year Distribution</h3>
                <div class="chart-wrapper">
                    <canvas id="schoolYearChart"></canvas>
                </div>
                <?php if (empty($sy_data) || $total_applicants == 0): ?>
                    <div class="no-data-message">No school year data available</div>
                <?php endif; ?>
            </div>

            <!-- Religion Distribution -->
            <div class="chart-card">
                <h3>Religion Distribution</h3>
                <div class="chart-wrapper">
                    <canvas id="religionChart"></canvas>
                </div>
                <?php if (empty($religion_data) || $total_applicants == 0): ?>
                    <div class="no-data-message">No religion data available</div>
                <?php endif; ?>
            </div>

            <!-- Indigenous Groups Distribution -->
            <div class="chart-card">
                <h3>Indigenous Groups Distribution</h3>
                <div class="chart-wrapper">
                    <canvas id="indigenousChart"></canvas>
                </div>
                <?php if (empty($indigenous_data) || $total_applicants == 0): ?>
                    <div class="no-data-message">No indigenous group data available</div>
                <?php endif; ?>
            </div>

            <!-- House Status Distribution -->
            <div class="chart-card">
                <h3>House Status Distribution</h3>
                <div class="chart-wrapper">
                    <canvas id="houseChart"></canvas>
                </div>
                <?php if (empty($house_data) || $total_applicants == 0): ?>
                    <div class="no-data-message">No house status data available</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Announcements Section -->
        <div class="announcement-section">
            <h2>Latest Announcements</h2>
            <?php if (count($announcements) === 0): ?>
                <p>No announcements available.</p>
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
        </div>
    </div>

    <script>
        // Wait for DOM to load
        document.addEventListener('DOMContentLoaded', function () {
            // Application Status Chart
            const statusCtx = document.getElementById('statusChart');
            if (statusCtx) {
                const statusChart = new Chart(statusCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Approved', 'Pending', 'Not Qualified'],
                        datasets: [{
                            data: [
                                <?php echo $approved_applications; ?>,
                                <?php echo $pending_applications; ?>,
                                <?php echo $rejected_applications; ?>
                            ],
                            backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true } }
                        }
                    }
                });
            }

            // Gender Distribution Chart
            const genderCtx = document.getElementById('genderChart');
            if (genderCtx) {
                const genderChart = new Chart(genderCtx.getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: <?php echo json_encode($gender_labels); ?>,
                        datasets: [{
                            data: <?php echo json_encode($gender_counts); ?>,
                            backgroundColor: ['#36a2eb', '#ff6384', '#4bc0c0', '#ff9f40', '#9966ff'],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true } }
                        }
                    }
                });
            }

            // Age Distribution Chart
            const ageCtx = document.getElementById('ageChart');
            if (ageCtx) {
                const ageChart = new Chart(ageCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($age_labels); ?>,
                        datasets: [{
                            label: 'Number of Applicants',
                            data: <?php echo json_encode($age_counts); ?>,
                            backgroundColor: 'rgba(255, 159, 64, 0.7)',
                            borderColor: 'rgba(255, 159, 64, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1 } }
                        }
                    }
                });
            }

            // Civil Status Chart
            const civilStatusCtx = document.getElementById('civilStatusChart');
            if (civilStatusCtx) {
                const civilStatusChart = new Chart(civilStatusCtx.getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: <?php echo json_encode($civil_status_labels); ?>,
                        datasets: [{
                            data: <?php echo json_encode($civil_status_counts); ?>,
                            backgroundColor: ['#4bc0c0', '#36a2eb', '#ff6384', '#ffcd56', '#c9cbcf'],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true } }
                        }
                    }
                });
            }

            // Scholarship Grants Chart
            const grantsCtx = document.getElementById('grantsChart');
            if (grantsCtx) {
                const grantsChart = new Chart(grantsCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($grant_labels); ?>,
                        datasets: [{
                            label: 'Number of Applications',
                            data: <?php echo json_encode($grant_counts); ?>,
                            backgroundColor: 'rgba(54, 162, 235, 0.7)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1 } },
                            x: { ticks: { autoSkip: false, maxRotation: 45, minRotation: 45 } }
                        },
                        plugins: { legend: { display: false } }
                    }
                });
            }

            // Timeline Chart
            const timelineCtx = document.getElementById('timelineChart');
            if (timelineCtx) {
                const timelineChart = new Chart(timelineCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: <?php echo json_encode($month_labels); ?>,
                        datasets: [{
                            label: 'Applications',
                            data: <?php echo json_encode($month_counts); ?>,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1 } }
                        }
                    }
                });
            }

            // Courses Chart
            const coursesCtx = document.getElementById('coursesChart');
            if (coursesCtx) {
                const coursesChart = new Chart(coursesCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($course_labels); ?>,
                        datasets: [{
                            label: 'Applications',
                            data: <?php echo json_encode($course_counts); ?>,
                            backgroundColor: 'rgba(153, 102, 255, 0.7)',
                            borderColor: 'rgba(153, 102, 255, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        return 'Applications: ' + context.raw;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Semester Chart
            const semesterCtx = document.getElementById('semesterChart');
            if (semesterCtx) {
                const semesterChart = new Chart(semesterCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: <?php echo json_encode($semester_labels); ?>,
                        datasets: [{
                            data: <?php echo json_encode($semester_counts); ?>,
                            backgroundColor: ['#ff6384', '#36a2eb', '#4bc0c0'],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true } }
                        }
                    }
                });
            }

            // School Year Chart
            const schoolYearCtx = document.getElementById('schoolYearChart');
            if (schoolYearCtx) {
                const schoolYearChart = new Chart(schoolYearCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($sy_labels); ?>,
                        datasets: [{
                            label: 'Applications',
                            data: <?php echo json_encode($sy_counts); ?>,
                            backgroundColor: 'rgba(255, 99, 132, 0.7)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1 } }
                        }
                    }
                });
            }

            // Religion Chart
            const religionCtx = document.getElementById('religionChart');
            if (religionCtx) {
                const religionChart = new Chart(religionCtx.getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: <?php echo json_encode($religion_labels); ?>,
                        datasets: [{
                            data: <?php echo json_encode($religion_counts); ?>,
                            backgroundColor: ['#ffcd56', '#4bc0c0', '#ff6384', '#36a2eb', '#9966ff', '#c9cbcf'],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                    font: { size: 11 }
                                }
                            }
                        }
                    }
                });
            }

            // Indigenous Chart
            const indigenousCtx = document.getElementById('indigenousChart');
            if (indigenousCtx) {
                const indigenousChart = new Chart(indigenousCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: <?php echo json_encode($indigenous_labels); ?>,
                        datasets: [{
                            data: <?php echo json_encode($indigenous_counts); ?>,
                            backgroundColor: ['#ff9f40', '#4bc0c0', '#36a2eb', '#ff6384', '#9966ff'],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true } }
                        }
                    }
                });
            }

            // House Chart
            const houseCtx = document.getElementById('houseChart');
            if (houseCtx) {
                const houseChart = new Chart(houseCtx.getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels: <?php echo json_encode($house_labels); ?>,
                        datasets: [{
                            data: <?php echo json_encode($house_counts); ?>,
                            backgroundColor: ['#28a745', '#007bff', '#ffc107', '#6c757d'],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true } }
                        }
                    }
                });
            }
        });
    </script>
</body>

</html>