<?php
include '../includes/session.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard.php");
    exit;
}

$message = '';

// Handle new announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $stmt = $pdo->prepare("INSERT INTO announcements (title, message) VALUES (?, ?)");
    $stmt->execute([$title, $content]);

    $message = "Announcement posted successfully!";

    // Prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
    exit;
}

// Handle deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $pdo->prepare("DELETE FROM announcements WHERE id = ?");
    $stmt->execute([$delete_id]);

    header("Location: " . $_SERVER['PHP_SELF'] . "?deleted=1");
    exit;
}

// Fetch all announcements
$annList = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Success messages
if (isset($_GET['success']))
    $message = "Announcement posted successfully!";
if (isset($_GET['deleted']))
    $message = "Announcement deleted successfully!";
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>

    <!-- CSS files -->
    <link rel="stylesheet" href="../css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/announcement.css?v=<?php echo time(); ?>">
    <script src="../js/fontawesome.js"></script>
</head>

<body>

    <!-- NAVIGATION SIDEBAR -->
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

            <li><a href="./dashboard.php"><i class="fas fa-gauge"></i><span class="nav-item-2">Dashboard</span></a></li>

            <li><a href="./announcement.php" class="active"><i class="fas fa-bullhorn"></i>
                    <span class="nav-item-2">Announcements</span></a>
            </li>

            <li><a href="./scholarship_form.php"><i class="fas fa-file"></i><span class="nav-item-2">Scholarship
                        Form</span></a></li>
            <li><a href="./manage_dropdowns.php"><i class="fas fa-list"></i><span class="nav-item-2">Manage
                        Dropdowns</span></a></li>
            <li><a href="./applications.php"><i class="fas fa-folder"></i><span
                        class="nav-item-2">Applications</span></a></li>

            <li><a href="./logs.php"><i class="fas fa-clipboard-list"></i><span class="nav-item-2">Logs</span></a></li>

            <li><a href="../auth/logout.php" class="logout"><i class="fas fa-right-from-bracket"></i>
                    <span class="nav-item-2">Logout</span></a></li>
        </ul>
    </nav>

    <!-- MAIN PAGE CONTENT -->
    <div class="content">
        <h1>Post Announcement</h1>
        <p>Create announcements that students will see on their dashboard.</p>

        <?php if ($message): ?>
            <div id="success-message" class="success-message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="form-box">
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <label for="title">Announcement Title</label>
                <input type="text" name="title" id="title" required>

                <label for="content">Announcement Message</label>
                <textarea name="content" id="content" rows="6" required></textarea>

                <button type="submit">Post Announcement</button>
            </form>
        </div>

        <div class="announcement-list">
            <h2>Existing Announcements</h2>

            <?php if (count($annList) === 0): ?>
                <p>No announcements yet.</p>
            <?php else: ?>
                <?php foreach ($annList as $a): ?>
                    <div class="announcement-item">
                        <h3><?= htmlspecialchars($a['title']); ?></h3>
                        <p><?= nl2br(htmlspecialchars($a['message'])); ?></p>
                        <small>Posted: <?= $a['created_at']; ?></small>
                        <br>
                        <a href="?delete_id=<?= $a['id']; ?>" class="delete-btn"
                            onclick="return confirm('Delete this announcement?');">Delete</a>
                    </div>
                    <hr>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <script>
        // Wait until the page loads
        document.addEventListener("DOMContentLoaded", function () {
            const msg = document.getElementById("success-message");
            if (msg) {
                // Hide after 3 seconds (3000ms)
                setTimeout(() => {
                    msg.style.transition = "opacity 0.5s ease";
                    msg.style.opacity = "0";
                    // Optional: remove from DOM after fade-out
                    setTimeout(() => msg.remove(), 500);
                }, 3000);
            }
        });
    </script>

</body>

</html>