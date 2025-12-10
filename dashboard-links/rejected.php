<?php

require '../includes/session.php';

// Pagination logic
$limit = 5; // Records per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $limit; // Calculate offset

try {
    // Fetch total records count
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM scholarship_applications WHERE status = 'not qualiied'");
    $countStmt->execute();
    $totalRecords = $countStmt->fetchColumn();

    // Fetch records for the current page
    $stmt = $pdo->prepare("SELECT * FROM scholarship_applications WHERE status = 'not qualified' LIMIT :limit OFFSET :offset");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rejectedApplications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate total pages
    $totalPages = ceil($totalRecords / $limit);
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
    <title>Not Qualified Applications</title>
    <link rel="stylesheet" href="../css/approved.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/pagination.css?v=<?php echo time(); ?>">
</head>

<body>

    <header>
        <a href="../views/dashboard.php">
            <button>
                <i class="fas fa-arrow-left"></i>
            </button>
        </a>
        <h1>Not Qualified Applications</h1>
    </header>

    <main>
        <h2>List of Not Qualified Applications</h2>
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

            <!-- Pagination Links -->
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>" class="prev">Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>" class="next">Next</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p class="no-data">No not qualified applications found.</p>
        <?php endif; ?>
        <button onclick="window.print()" class="print-button">
            <i class="fas fa-print"></i> Print Page
        </button>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Scholarship System</p>
    </footer>

</body>

</html>