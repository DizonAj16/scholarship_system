<?php
include '../includes/session.php';

// Check if admin role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard.php");
    exit;
}

// Initialize message variable
$message = '';
$annList = [];

// Check if PDO connection exists
if (!isset($pdo) || !($pdo instanceof PDO)) {
    die("Database connection not available");
}

// Handle new announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    try {
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $content = isset($_POST['content']) ? trim($_POST['content']) : '';

        // Validate inputs
        if (empty($title) || empty($content)) {
            $message = "Please fill in all fields!";
        } else {
            $stmt = $pdo->prepare("INSERT INTO announcements (title, message) VALUES (?, ?)");
            $result = $stmt->execute([$title, $content]);

            if ($result) {
                header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
                exit;
            } else {
                $message = "Failed to post announcement!";
            }
        }
    } catch (PDOException $e) {
        $message = "Database error: " . $e->getMessage();
    }
}

// Handle deletion
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    try {
        $delete_id = intval($_GET['delete_id']);
        $stmt = $pdo->prepare("DELETE FROM announcements WHERE id = ?");
        $result = $stmt->execute([$delete_id]);

        if ($result) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?deleted=1");
            exit;
        }
    } catch (PDOException $e) {
        $message = "Database error: " . $e->getMessage();
    }
}

// Fetch all announcements
try {
    $stmt = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC");
    $annList = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Failed to load announcements: " . $e->getMessage();
    $annList = [];
}

// Success messages
if (isset($_GET['success'])) {
    $message = "Announcement posted successfully!";
}
if (isset($_GET['deleted'])) {
    $message = "Announcement deleted successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements - Admin</title>

    <!-- CSS files -->
    <link rel="stylesheet" href="../css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/announcement.css?v=<?php echo time(); ?>">
    <script src="../js/toggle_nav.js?v=<?php echo time(); ?>"></script>

    <script src="../js/fontawesome.js"></script>
</head>

<body>

    <!-- NAVIGATION SIDEBAR -->
    <nav class="stroke" id="sideNav">
        <ul>
            <li>
                <a href="#" class="logo">
                    <div style="display: flex; align-items: center;">
                        <img src="../assets/images/icons/scholarship_seal.png" alt="Scholarship Seal">
                        <span class="username"><?php echo htmlspecialchars($_SESSION["username"] ?? 'Admin'); ?></span>
                    </div>
                    <span class="nav-item" style="margin-left: 10px;">OSP</span>
                </a>
            </li>

            <li><a href="./dashboard.php"><i class="fas fa-gauge"></i><span class="nav-item-2">Dashboard</span></a></li>

            <li><a href="./announcement.php" class="activea"><i class="fas fa-bullhorn"></i>
                    <span class="nav-item-2">Announcements</span></a>
            </li>

            <li><a href="./scholarship_form.php"><i class="fas fa-file"></i><span class="nav-item-2">Scholarship
                        Form</span></a></li>
            <li><a href="./manage_dropdowns.php"><i class="fas fa-list"></i><span class="nav-item-2">Scholarship Settings</span></a></li>
            <li><a href="./applications.php"><i class="fas fa-folder"></i><span
                        class="nav-item-2">Applications</span></a></li>

            <li><a href="./logs.php"><i class="fas fa-clipboard-list"></i><span class="nav-item-2">Logs</span></a></li>

            <li><a href="../auth/logout.php" class="logout"><i class="fas fa-right-from-bracket"></i>
                    <span class="nav-item-2">Logout</span></a></li>
        </ul>
        <button class="toggle-btn" onclick="toggleNav()">
            <i class="fas fa-bars" id="toggle-icon"></i>
        </button>
    </nav>

    <!-- MAIN PAGE CONTENT -->
    <div class="content">
        <h1>Post Announcement</h1>
        <p>Create announcements that students will see on their dashboard.</p>

        <?php if (!empty($message)): ?>
            <div id="success-message" class="success-message">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="form-box">
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <label for="title">Announcement Title</label>
                <input type="text" name="title" id="title" required placeholder="Enter announcement title"
                    maxlength="100">

                <label for="content">Announcement Message</label>
                <textarea name="content" id="content" rows="6" required
                    placeholder="Type your announcement message here" maxlength="1000"></textarea>

                <button type="submit">
                    <i class="fas fa-paper-plane"></i> Post Announcement
                </button>
            </form>
        </div>

        <div class="announcement-list">
            <h2>Existing Announcements <span class="count-badge"><?= count($annList) ?></span></h2>

            <?php if (count($annList) === 0): ?>
                <p class="no-announcements">No announcements yet. Create your first announcement above!</p>
            <?php else: ?>
                <?php foreach ($annList as $index => $a): ?>
                    <div class="announcement-item">
                        <h3><?= htmlspecialchars($a['title'] ?? 'Untitled'); ?></h3>
                        <p><?= nl2br(htmlspecialchars($a['message'] ?? '')); ?></p>
                        <small>
                            <i class="fas fa-calendar-alt"></i>
                            Posted:
                            <?= isset($a['created_at']) ? date('F j, Y \a\t g:i A', strtotime($a['created_at'])) : 'Unknown date'; ?>
                        </small>
                        <div class="announcement-actions">
                            <span class="announcement-id">ID: <?= $a['id'] ?? 'N/A'; ?></span>
                            <a href="?delete_id=<?= $a['id'] ?? ''; ?>" class="delete-btn"
                                onclick="return confirm('Are you sure you want to delete this announcement? This action cannot be undone.');">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                    <?php if ($index < count($annList) - 1): ?>
                        <hr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Fade out success message
            const msg = document.getElementById("success-message");
            if (msg) {
                setTimeout(() => {
                    msg.style.transition = "opacity 0.5s ease";
                    msg.style.opacity = "0";
                    setTimeout(() => msg.remove(), 500);
                }, 4000);
            }

            // Character counters
            const titleInput = document.getElementById('title');
            const contentTextarea = document.getElementById('content');

            function createCounter(element, maxLength) {
                const counter = document.createElement('div');
                counter.className = 'char-counter';
                counter.style.cssText = 'text-align: right; font-size: 0.85rem; color: #666; margin-top: -10px; margin-bottom: 15px;';
                element.parentNode.insertBefore(counter, element.nextSibling);

                element.addEventListener('input', function () {
                    const currentLength = this.value.length;
                    counter.textContent = `${currentLength}/${maxLength} characters`;
                    counter.style.color = currentLength > maxLength ? '#ff4444' : '#666';
                });

                // Trigger once on load
                element.dispatchEvent(new Event('input'));
            }

            if (titleInput) createCounter(titleInput, 100);
            if (contentTextarea) createCounter(contentTextarea, 1000);
        });
    </script>

</body>

</html>