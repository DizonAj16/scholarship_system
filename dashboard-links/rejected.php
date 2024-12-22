<?php

require '../includes/session.php';

// Fetch all rejected applications
try {
    $stmt = $pdo->prepare("SELECT * FROM scholarship_applications WHERE status = 'Rejected'");
    $stmt->execute();
    $rejectedApplications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Error fetching data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../js/fontawesome.js" crossorigin="anonymous"></script>
    <title>Rejected Applications</title>
    <link rel="stylesheet" href="../css/approved.css?v=<?php echo time(); ?>">
</head>

<body>

    <header>
        <button onclick="window.history.back()">
            <i class="fas fa-arrow-left"></i>
        </button>
        <h1>Rejected Applications</h1>
    </header>

    <main>
        <h2>List of Rejected Applications</h2>
        <?php if (count($rejectedApplications) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Application ID</th>
                        <th>Full Name</th>
                        <th>Course</th>
                        <th>Scholarship Grant</th>
                        <th>Year & Section</th>
                        <th>Phone</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rejectedApplications as $application): ?>
                        <tr>
                            <td><?= htmlspecialchars($application['application_id']) ?></td>
                            <td><?= htmlspecialchars($application['full_name']) ?></td>
                            <td><?= htmlspecialchars($application['course']) ?></td>
                            <td><?= htmlspecialchars($application['scholarship_grant']) ?></td>
                            <td><?= htmlspecialchars($application['yr_sec']) ?></td>
                            <td><?= htmlspecialchars($application['cell_no']) ?></td>
                            <td><?= htmlspecialchars($application['email']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No rejected applications found.</p>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Scholarship System</p>
    </footer>
</body>

</html>
