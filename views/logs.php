<?php
// Include session and database connection
include '../includes/session.php';

// Restrict access to admin users only
if ($_SESSION["role"] !== 'admin') {
    header("location: ../views/home.php");
    exit;
}

// Fetch logs from the database
$logs = [];
$sql = "SELECT l.id, l.user_id, u.username, l.action, l.details, l.timestamp 
        FROM logs l 
        INNER JOIN users u ON l.user_id = u.id 
        ORDER BY l.timestamp DESC";
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error fetching logs: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs</title>
    <script src="../js/fontawesome.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/preloader.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/dashboard.css?v=<?php echo time(); ?>">
    <script src="../js/preloader.js?v=<?php echo time(); ?>"></script>
    <script src="../js/toggle_nav.js?v=<?php echo time(); ?>"></script>

    <link rel="stylesheet" href="../css/logs.css?v=<?php echo time(); ?>">

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

    <!-- 
    <button class="toggle-btn" onclick="toggleNav()">
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
                <li><a href="./dashboard.php"><i class="fas fa-solid fa-gauge"></i><span
                            class="nav-item-2">Dashboard</span></a></li>
                <li><a href="./announcement.php"><i class="fas fa-bullhorn"></i>
                        <span class="nav-item-2">Announcements</span></a>
                </li>

                <li><a href="./scholarship_form.php"><i class="fas fa-solid fa-file"></i><span
                            class="nav-item-2">Scholarship Form</span></a></li>
                <li><a href="./manage_dropdowns.php"><i class="fas fa-list"></i><span class="nav-item-2">Scholarship Settings</span></a></li>
                <li><a href="./applications.php"><i class="fas fa-solid fa-folder"></i><span
                            class="nav-item-2">Applications</span></a></li>
                <li><a href="./logs.php" class="activea"><i class="fas fa-clipboard-list"></i><span
                            class="nav-item-2">Logs</span></a></li>
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
        <button class="toggle-btn" onclick="toggleNav()">
            <i class="fas fa-bars" id="toggle-icon"></i>
        </button>
    </nav>

    <div class="content">
        <h1>Activity Logs</h1>
        <table class="log-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($logs)): ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($log['id']); ?></td>
                            <td><?php echo htmlspecialchars($log['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($log['username']); ?></td>
                            <td><?php echo htmlspecialchars($log['action']); ?></td>
                            <td><?php echo htmlspecialchars($log['details']); ?></td>
                            <td><?php try {
                                // Convert the timestamp from the database into a DateTime object
                                $date = DateTime::createFromFormat('Y-m-d H:i:s', $log['timestamp']);
                                if ($date) {
                                    // Format the date to the desired format
                                    echo $date->format('h:i:s a, F j, Y');
                                } else {
                                    // Handle the case where the timestamp couldn't be parsed
                                    echo "Invalid date format";
                                }
                            } catch (Exception $e) {
                                echo "Error: " . $e->getMessage();
                            } ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No logs found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>

</html>