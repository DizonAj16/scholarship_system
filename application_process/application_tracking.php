<?php
include '../includes/session.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: applications.php");
    exit;
}

$application_id = (int) $_GET['id'];

$stmt = $pdo->prepare("
    SELECT sa.status, sa.date, sa.full_name, u.username
    FROM scholarship_applications sa
    LEFT JOIN users u ON sa.user_id = u.id
    WHERE sa.application_id = :id
");
$stmt->execute(['id' => $application_id]);
$app = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$app) {
    die("Application not found.");
}

$status = strtolower($app['status']); // submitted, pending, approved, not qualified
?>
<!DOCTYPE html>
<html>
<head>
    <title>Application Tracking</title>
    <link rel="stylesheet" href="../css/tracking.css?v=<?php echo time(); ?>">
</head>

<body>

<div class="tracking-container">

    <h1>Scholarship Application Tracking</h1>

    <p class="info">
        Application ID: <strong><?= $application_id ?></strong><br>
        Submitted by: <strong><?= htmlspecialchars($app['username']) ?></strong><br>
        Date: <strong><?= htmlspecialchars($app['date']) ?></strong>
    </p>

    <div class="tracker">

        <!-- STEP 1: SUBMITTED -->
        <div class="step 
            <?= in_array($status, ['submitted','pending','approved','not qualified']) ? 'active' : '' ?>">
            <div class="circle">1</div>
            <p>Submitted</p>
        </div>

        <div class="line <?= $status != 'submitted' ? 'active' : '' ?>"></div>

        <!-- STEP 2: REVIEWING -->
        <div class="step 
            <?= in_array($status, ['pending','approved','not qualified']) ? 'active' : '' ?>">
            <div class="circle">2</div>
            <p>Reviewing</p>
        </div>

        <div class="line <?= in_array($status, ['approved','not qualified']) ? 'active' : '' ?>"></div>

        <!-- STEP 3: DECISION -->
        <div class="step 
            <?= $status == 'approved' ? 'approved' : ($status == 'not qualified' ? 'not-qualified' : '') ?>">
            
            <div class="circle">3</div>

            <p>
                <?php
                    if ($status == 'approved') echo "Approved";
                    elseif ($status == 'not qualified') echo "Not Qualified";
                    else echo "Decision Pending";
                ?>
            </p>
        </div>

    </div>

    <a href="view_application.php?id=<?= $application_id ?>" class="back-btn">Back</a>

</div>

</body>
</html>
