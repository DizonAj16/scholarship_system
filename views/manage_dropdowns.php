<?php
include '../includes/session.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard.php");
    exit;
}

$message = '';

// Handle Add / Edit / Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type']; // 'sem_sy', 'course_major', 'scholarship_grant', or 'grant_requirements'
    $action = $_POST['action'];

    if ($type === 'sem_sy') {
        $semester = $_POST['semester'];
        $school_year = $_POST['school_year'];
        $is_default = isset($_POST['is_default']) ? 1 : 0;

        if ($action === 'add') {
            // If setting as default, unset all other defaults first
            if ($is_default) {
                $pdo->prepare("UPDATE dropdown_sem_sy SET is_default = 0")->execute();
            }

            $stmt = $pdo->prepare("INSERT INTO dropdown_sem_sy (semester, school_year, is_default) VALUES (?, ?, ?)");
            $stmt->execute([$semester, $school_year, $is_default]);
        }
    } elseif ($type === 'course_major') {
        $course = $_POST['course'];
        $major = $_POST['major'];
        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO dropdown_course_major (course, major) VALUES (?, ?)");
            $stmt->execute([$course, $major]);
        }
    } elseif ($type === 'scholarship_grant') {
        $grant_name = $_POST['grant_name'];
        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO dropdown_scholarship_grant (grant_name) VALUES (?)");
            $stmt->execute([$grant_name]);
        }
    } elseif ($type === 'grant_requirements') {
        $grant_name = $_POST['grant_name'];
        $requirement_name = $_POST['requirement_name'];
        $requirement_type = !empty($_POST['requirement_type']) ? $_POST['requirement_type'] : null;

        if ($action === 'add') {
            // Get the next display order for this grant
            $orderStmt = $pdo->prepare("SELECT MAX(display_order) as max_order FROM grant_requirements WHERE grant_name = ?");
            $orderStmt->execute([$grant_name]);
            $orderRow = $orderStmt->fetch(PDO::FETCH_ASSOC);
            $display_order = ($orderRow['max_order'] ?? 0) + 1;

            $stmt = $pdo->prepare("INSERT INTO grant_requirements (grant_name, requirement_name, requirement_type, display_order) VALUES (?, ?, ?, ?)");
            $stmt->execute([$grant_name, $requirement_name, $requirement_type, $display_order]);
        }
    } elseif ($type === 'set_default_sem_sy') {
        // Handle setting default semester/school year
        $id = $_POST['id'];
        $pdo->prepare("UPDATE dropdown_sem_sy SET is_default = 0")->execute();
        $pdo->prepare("UPDATE dropdown_sem_sy SET is_default = 1 WHERE id = ?")->execute([$id]);
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=1&tab=sem_sy");
        exit;
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "?success=1&tab=" . $type);
    exit;
}

// Handle deletion
if (isset($_GET['delete']) && isset($_GET['type'])) {
    $id = intval($_GET['delete']);
    $type = $_GET['type'];

    if ($type === 'scholarship_grant') {
        $pdo->prepare("DELETE FROM dropdown_scholarship_grant WHERE id=?")->execute([$id]);
    } elseif ($type === 'sem_sy') {
        // Check if deleting default, if so, set another as default
        $checkStmt = $pdo->prepare("SELECT is_default FROM dropdown_sem_sy WHERE id=?");
        $checkStmt->execute([$id]);
        $row = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($row && $row['is_default']) {
            // Find another record to set as default
            $newDefaultStmt = $pdo->prepare("SELECT id FROM dropdown_sem_sy WHERE id != ? ORDER BY id LIMIT 1");
            $newDefaultStmt->execute([$id]);
            $newDefault = $newDefaultStmt->fetch(PDO::FETCH_ASSOC);

            if ($newDefault) {
                $pdo->prepare("UPDATE dropdown_sem_sy SET is_default = 1 WHERE id = ?")->execute([$newDefault['id']]);
            }
        }

        $pdo->prepare("DELETE FROM dropdown_sem_sy WHERE id=?")->execute([$id]);
    } elseif ($type === 'course_major') {
        $pdo->prepare("DELETE FROM dropdown_course_major WHERE id=?")->execute([$id]);
    } elseif ($type === 'grant_requirements') {
        $pdo->prepare("DELETE FROM grant_requirements WHERE id=?")->execute([$id]);
    }

    header("Location: " . $_SERVER['PHP_SELF'] . "?deleted=1&tab=" . $type);
    exit;
}

// Handle requirement reordering
if (isset($_POST['reorder_requirements'])) {
    $requirementIds = $_POST['requirement_order'];
    foreach ($requirementIds as $order => $id) {
        $stmt = $pdo->prepare("UPDATE grant_requirements SET display_order = ? WHERE id = ?");
        $stmt->execute([$order + 1, $id]);
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "?success=1&tab=grant_requirements");
    exit;
}

// Fetch dropdown data
$sem_sy_list = $pdo->query("SELECT * FROM dropdown_sem_sy ORDER BY is_default DESC, semester, school_year")->fetchAll(PDO::FETCH_ASSOC);
$course_major_list = $pdo->query("SELECT * FROM dropdown_course_major ORDER BY course, major")->fetchAll(PDO::FETCH_ASSOC);
$scholarship_grant_list = $pdo->query("SELECT * FROM dropdown_scholarship_grant ORDER BY grant_name")->fetchAll(PDO::FETCH_ASSOC);

// Fetch grant requirements grouped by grant
$grant_requirements = [];
$requirements_stmt = $pdo->query("SELECT gr.*, dsg.grant_name as grant_display_name 
    FROM grant_requirements gr 
    JOIN dropdown_scholarship_grant dsg ON gr.grant_name = dsg.grant_name 
    ORDER BY gr.grant_name, gr.display_order, gr.requirement_name");
while ($row = $requirements_stmt->fetch(PDO::FETCH_ASSOC)) {
    if (!isset($grant_requirements[$row['grant_name']])) {
        $grant_requirements[$row['grant_name']] = [];
    }
    $grant_requirements[$row['grant_name']][] = $row;
}

// Determine active tab
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'sem_sy';
$valid_tabs = ['sem_sy', 'course_major', 'scholarship_grant', 'grant_requirements'];
if (!in_array($active_tab, $valid_tabs)) {
    $active_tab = 'sem_sy';
}

$message = '';
if (isset($_GET['success']))
    $message = '<i class="fas fa-check-circle"></i> Changes saved successfully!';
if (isset($_GET['deleted']))
    $message = '<i class="fas fa-trash-alt"></i> Deleted successfully!';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Settings</title>
    <link rel="stylesheet" href="../css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/manage_dropdowns.css?v=<?php echo time(); ?>">
    
    <!-- ADD JQUERY UI CSS -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    
    <script src="../js/fontawesome.js"></script>
    <script src="../js/toggle_nav.js?v=<?php echo time(); ?>"></script>
    
    <!-- LOAD JQUERY FIRST -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- THEN LOAD JQUERY UI -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    
    <style>
        /* Additional styles for requirements management */
        .requirements-container {
            display: grid;
            gap: 20px;
        }

        .requirements-by-grant {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid #eaeaea;
        }

        .grant-requirements-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #007bff;
        }

        .grant-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
        }

        .requirements-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .requirement-item {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            margin-bottom: 8px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid #007bff;
            transition: all 0.2s ease;
            cursor: move; /* IMPORTANT: Add cursor style */
        }

        .requirement-item:hover {
            background: #e9ecef;
        }

        .requirement-details {
            flex: 1;
            margin-left: 10px;
        }

        .requirement-name {
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 3px;
        }

        .requirement-type {
            font-size: 12px;
            color: #6c757d;
            background: #e9ecef;
            padding: 2px 8px;
            border-radius: 12px;
            display: inline-block;
        }

        .requirement-actions {
            display: flex;
            gap: 10px;
        }

        .reorder-handle {
            cursor: move;
            color: #6c757d;
            padding: 8px 10px;
            margin-right: 5px;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .reorder-handle:hover {
            color: #007bff;
            background: #e3f2fd;
        }

        .empty-requirements {
            text-align: center;
            padding: 30px;
            color: #6c757d;
            background: #f8f9fa;
            border-radius: 8px;
            border: 2px dashed #dee2e6;
        }

        .empty-requirements i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #adb5bd;
        }

        .requirement-form-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        @media (max-width: 768px) {
            .requirement-form-group {
                grid-template-columns: 1fr;
            }
        }

        .sortable-requirements {
            min-height: 50px;
        }

        /* JQUERY UI SORTABLE STYLES */
        .ui-sortable-placeholder {
            background: #e3f2fd !important;
            border: 2px dashed #007bff !important;
            visibility: visible !important;
            height: 60px !important;
            border-radius: 6px !important;
        }

        .ui-sortable-helper {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1) !important;
            transform: rotate(2deg) !important;
            opacity: 0.9 !important;
            z-index: 1000 !important;
        }

        /* Default semester badge */
        .default-badge {
            background: #28a745;
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            margin-left: 10px;
        }

        .default-checkbox {
            margin-top: 10px;
        }

        .default-checkbox label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .set-default-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .set-default-btn:hover {
            background: #218838;
        }

        .set-default-btn.active {
            background: #dc3545;
        }

        .set-default-btn.active:hover {
            background: #c82333;
        }

        .default-indicator {
            color: #28a745;
            margin-left: 5px;
            font-size: 14px;
        }

        /* Tab styles */
        .tab-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .tab-header {
            display: flex;
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .tab-item {
            padding: 15px 25px;
            cursor: pointer;
            border-right: 1px solid #dee2e6;
            transition: all 0.3s ease;
            font-weight: 500;
            color: #6c757d;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tab-item:hover {
            background: #e9ecef;
            color: #495057;
        }

        .tab-item.active {
            background: #007bff;
            color: white;
            border-bottom: 3px solid #0056b3;
        }

        .tab-content {
            padding: 30px;
        }

        .tab-pane {
            display: none;
        }

        .tab-pane.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .dashboard-card {
            margin-bottom: 20px;
        }

        .full-width {
            width: 100%;
        }
    </style>
</head>

<body>
    <!-- SIDEBAR -->
    <nav class="stroke" id="sideNav">
        <ul>
            <li>
                <a href="#" class="logo">
                    <div style="display: flex; align-items: center;">
                        <img src="../assets/images/icons/scholarship_seal.png" alt="Scholarship Seal">
                        <span class="username"><?= htmlspecialchars($_SESSION['username']); ?></span>
                    </div>
                    <span class="nav-item" style="margin-left: 10px;">OSP</span>
                </a>
            </li>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <li><a href="./dashboard.php"><i class="fas fa-solid fa-gauge"></i><span
                            class="nav-item-2">Dashboard</span></a></li>
                <li><a href="./announcement.php"><i class="fas fa-bullhorn"></i><span
                            class="nav-item-2">Announcements</span></a></li>
                <li><a href="./scholarship_form.php"><i class="fas fa-solid fa-file"></i><span
                            class="nav-item-2">Scholarship Form</span></a></li>
                <li><a href="./manage_dropdowns.php" class="activea"><i class="fas fa-list"></i><span
                            class="nav-item-2">Scholarship Settings</span></a></li>
                <li><a href="./applications.php"><i class="fas fa-solid fa-folder"></i><span
                            class="nav-item-2">Applications</span></a></li>
                <li><a href="./logs.php"><i class="fas fa-clipboard-list"></i><span class="nav-item-2">Logs</span></a></li>
            <?php endif; ?>
            <li><a href="../auth/logout.php" class="logout"><i class="fas fa-solid fa-right-from-bracket"></i><span
                        class="nav-item-2">Logout</span></a></li>
        </ul>
        <button class="toggle-btn" onclick="toggleNav()">
            <i class="fas fa-bars" id="toggle-icon"></i>
        </button>
    </nav>

    <!-- PAGE CONTENT -->
    <div class="content">
        <h1 class="page-title">Manage Dropdown Options</h1>

        <?php if ($message): ?>
            <div class="alert alert-success" id="successMessage">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <div class="tab-container">
            <ul class="tab-header">
                <li class="tab-item <?= $active_tab === 'sem_sy' ? 'active' : '' ?>" 
                    data-tab="sem_sy">
                    <i class="fas fa-calendar-alt"></i> Semester & School Year
                </li>
                <li class="tab-item <?= $active_tab === 'course_major' ? 'active' : '' ?>" 
                    data-tab="course_major">
                    <i class="fas fa-graduation-cap"></i> Course & Major
                </li>
                <li class="tab-item <?= $active_tab === 'scholarship_grant' ? 'active' : '' ?>" 
                    data-tab="scholarship_grant">
                    <i class="fas fa-award"></i> Scholarship Grants
                </li>
                <li class="tab-item <?= $active_tab === 'grant_requirements' ? 'active' : '' ?>" 
                    data-tab="grant_requirements">
                    <i class="fas fa-file-alt"></i> Grant Requirements
                </li>
            </ul>

            <div class="tab-content">
                <!-- SEMESTER & SCHOOL YEAR TAB -->
                <div class="tab-pane <?= $active_tab === 'sem_sy' ? 'active' : '' ?>" id="sem_sy">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="fas fa-calendar-alt"></i> Semester & School Year</h3>
                        </div>
                        <div class="card-body">
                            <h4 class="section-title"><i class="fas fa-plus-circle"></i> Add New Entry</h4>
                            <form method="POST" class="add-form">
                                <input type="hidden" name="type" value="sem_sy">
                                <input type="hidden" name="action" value="add">

                                <div class="form-group">
                                    <label for="semester">Semester</label>
                                    <select name="semester" id="semester" class="form-control" required>
                                        <option value="">Select Semester</option>
                                        <option value="1st sem">1st Semester</option>
                                        <option value="2nd sem">2nd Semester</option>
                                        <option value="Summer">Summer</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="school_year">School Year</label>
                                    <input type="text" name="school_year" id="school_year" class="form-control"
                                        placeholder="Ex: 2025-2026" pattern="\d{4}-\d{4}" title="Format: YYYY-YYYY" required>
                                </div>

                                <div class="default-checkbox">
                                    <label>
                                        <input type="checkbox" name="is_default" value="1">
                                        <span>Set as default semester/school year</span>
                                    </label>
                                    <small class="text-muted">This will be pre-selected in forms</small>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Entry
                                </button>
                            </form>

                            <h4 class="section-title" style="margin-top: 30px;">
                                <i class="fas fa-list-ul"></i> Existing Entries
                            </h4>

                            <?php if (empty($sem_sy_list)): ?>
                                <div class="empty-state">
                                    <i class="fas fa-calendar-times"></i>
                                    <p>No semester entries found</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Semester</th>
                                                <th>School Year</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($sem_sy_list as $s): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($s['semester']) ?></td>
                                                    <td><?= htmlspecialchars($s['school_year']) ?></td>
                                                    <td>
                                                        <?php if ($s['is_default']): ?>
                                                            <span class="default-badge">Default</span>
                                                        <?php else: ?>
                                                            <form method="POST" style="display: inline;">
                                                                <input type="hidden" name="type" value="set_default_sem_sy">
                                                                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                                                <button type="submit" class="set-default-btn"
                                                                    onclick="return confirm('Set this as the default semester/school year?')">
                                                                    Set as Default
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="?delete=<?= $s['id'] ?>&type=sem_sy&tab=sem_sy" class="btn-delete-icon"
                                                            onclick="return confirm('Are you sure you want to delete this entry?')"
                                                            title="Delete">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- COURSE & MAJOR TAB -->
                <div class="tab-pane <?= $active_tab === 'course_major' ? 'active' : '' ?>" id="course_major">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="fas fa-graduation-cap"></i> Course & Major</h3>
                        </div>
                        <div class="card-body">
                            <h4 class="section-title"><i class="fas fa-plus-circle"></i> Add New Entry</h4>
                            <form method="POST" class="add-form">
                                <input type="hidden" name="type" value="course_major">
                                <input type="hidden" name="action" value="add">

                                <div class="form-group">
                                    <label for="course">Course</label>
                                    <input type="text" name="course" id="course" class="form-control"
                                        placeholder="Ex: BS Information Technology" required>
                                </div>

                                <div class="form-group">
                                    <label for="major">Major</label>
                                    <input type="text" name="major" id="major" class="form-control"
                                        placeholder="Ex: Web Development" required>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Entry
                                </button>
                            </form>

                            <h4 class="section-title" style="margin-top: 30px;">
                                <i class="fas fa-list-ul"></i> Existing Entries
                            </h4>

                            <?php if (empty($course_major_list)): ?>
                                <div class="empty-state">
                                    <i class="fas fa-book"></i>
                                    <p>No course entries found</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Course</th>
                                                <th>Major</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($course_major_list as $c): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($c['course']) ?></td>
                                                    <td><?= htmlspecialchars($c['major']) ?></td>
                                                    <td>
                                                        <a href="?delete=<?= $c['id'] ?>&type=course_major&tab=course_major" class="btn-delete-icon"
                                                            onclick="return confirm('Are you sure you want to delete this entry?')"
                                                            title="Delete">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- SCHOLARSHIP GRANT TAB -->
                <div class="tab-pane <?= $active_tab === 'scholarship_grant' ? 'active' : '' ?>" id="scholarship_grant">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="fas fa-award"></i> Scholarship Grants</h3>
                        </div>
                        <div class="card-body">
                            <h4 class="section-title"><i class="fas fa-plus-circle"></i> Add New Entry</h4>
                            <form method="POST" class="add-form">
                                <input type="hidden" name="type" value="scholarship_grant">
                                <input type="hidden" name="action" value="add">

                                <div class="form-group">
                                    <label for="grant_name">Grant Name</label>
                                    <input type="text" name="grant_name" id="grant_name" class="form-control"
                                        placeholder="Ex: Academic Scholarship" required>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Grant
                                </button>
                            </form>

                            <h4 class="section-title" style="margin-top: 30px;">
                                <i class="fas fa-list-ul"></i> Existing Grants
                            </h4>

                            <?php if (empty($scholarship_grant_list)): ?>
                                <div class="empty-state">
                                    <i class="fas fa-award"></i>
                                    <p>No scholarship grants found</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Grant Name</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($scholarship_grant_list as $g): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($g['grant_name']) ?></td>
                                                    <td>
                                                        <a href="?delete=<?= $g['id'] ?>&type=scholarship_grant&tab=scholarship_grant" class="btn-delete-icon"
                                                            onclick="return confirm('Are you sure you want to delete this scholarship grant?')"
                                                            title="Delete">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- GRANT REQUIREMENTS TAB -->
                <div class="tab-pane <?= $active_tab === 'grant_requirements' ? 'active' : '' ?>" id="grant_requirements">
                    <div class="dashboard-card full-width">
                        <div class="card-header">
                            <h3><i class="fas fa-file-alt"></i> Manage Grant Requirements</h3>
                        </div>
                        <div class="card-body">
                            <h4 class="section-title"><i class="fas fa-plus-circle"></i> Add New Requirement</h4>
                            <form method="POST" class="add-form">
                                <input type="hidden" name="type" value="grant_requirements">
                                <input type="hidden" name="action" value="add">

                                <div class="requirement-form-group">
                                    <div class="form-group">
                                        <label for="grant_name_select">Scholarship Grant</label>
                                        <select name="grant_name" id="grant_name_select" class="form-control" required>
                                            <option value="">Select Scholarship Grant</option>
                                            <?php foreach ($scholarship_grant_list as $grant): ?>
                                                <option value="<?= htmlspecialchars($grant['grant_name']) ?>">
                                                    <?= htmlspecialchars($grant['grant_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="requirement_name">Requirement Name</label>
                                        <input type="text" name="requirement_name" id="requirement_name" class="form-control"
                                            placeholder="Ex: Recent 2x2 ID Picture" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="requirement_type">Requirement Type (Optional)</label>
                                    <input type="text" name="requirement_type" id="requirement_type" class="form-control"
                                        placeholder="Ex: Photo, Certificate, Form, etc.">
                                    <small class="text-muted">Examples: Photo, Certificate, Form, ID, Card, Proof, etc.</small>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Requirement
                                </button>
                            </form>

                            <h4 class="section-title" style="margin-top: 30px;">
                                <i class="fas fa-list-ul"></i> Existing Requirements by Grant
                            </h4>

                            <?php if (empty($grant_requirements)): ?>
                                <div class="empty-requirements">
                                    <i class="fas fa-file-alt"></i>
                                    <h5>No requirements found</h5>
                                    <p>Add requirements for each scholarship grant using the form above.</p>
                                </div>
                            <?php else: ?>
                                <div class="requirements-container">
                                    <?php foreach ($grant_requirements as $grant_name => $requirements): ?>
                                        <div class="requirements-by-grant">
                                            <div class="grant-requirements-header">
                                                <h5 class="grant-title">
                                                    <i class="fas fa-award"></i> <?= htmlspecialchars($grant_name) ?>
                                                    <span class="badge"
                                                        style="background: #007bff; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px; margin-left: 10px;">
                                                        <?= count($requirements) ?> requirement(s)
                                                    </span>
                                                </h5>
                                            </div>

                                            <?php if (empty($requirements)): ?>
                                                <div class="text-center text-muted py-3">
                                                    No requirements for this grant
                                                </div>
                                            <?php else: ?>
                                                <form method="POST" class="reorder-form" data-grant="<?= htmlspecialchars($grant_name) ?>">
                                                    <input type="hidden" name="reorder_requirements" value="1">
                                                    <ul class="requirements-list sortable-requirements"
                                                        data-grant="<?= htmlspecialchars($grant_name) ?>">
                                                        <?php foreach ($requirements as $req): ?>
                                                            <li class="requirement-item" data-id="<?= $req['id'] ?>">
                                                                <div class="reorder-handle">
                                                                    <i class="fas fa-grip-vertical"></i>
                                                                </div>
                                                                <div class="requirement-details">
                                                                    <div class="requirement-name">
                                                                        <?= htmlspecialchars($req['requirement_name']) ?>
                                                                    </div>
                                                                    <?php if ($req['requirement_type']): ?>
                                                                        <span class="requirement-type">
                                                                            <?= htmlspecialchars($req['requirement_type']) ?>
                                                                        </span>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div class="requirement-actions">
                                                                    <a href="?delete=<?= $req['id'] ?>&type=grant_requirements&tab=grant_requirements"
                                                                        class="btn-delete-icon"
                                                                        onclick="return confirm('Are you sure you want to delete this requirement?')"
                                                                        title="Delete">
                                                                        <i class="fas fa-trash-alt"></i>
                                                                    </a>
                                                                </div>
                                                                <input type="hidden" name="requirement_order[]" value="<?= $req['id'] ?>">
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                    <div class="text-right mt-3">
                                                        <button type="submit" class="btn btn-sm btn-outline-primary save-order-btn">
                                                            <i class="fas fa-save"></i> Save Order
                                                        </button>
                                                    </div>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto hide success message after 5 seconds
        setTimeout(() => {
            const msg = document.getElementById('successMessage');
            if (msg) {
                msg.style.opacity = '0';
                msg.style.transition = 'opacity 0.5s ease';
                setTimeout(() => {
                    if (msg.parentNode) {
                        msg.parentNode.removeChild(msg);
                    }
                }, 500);
            }
        }, 5000);

        // Tab switching functionality
        document.addEventListener('DOMContentLoaded', function () {
            // Tab switching
            const tabItems = document.querySelectorAll('.tab-item');
            const tabPanes = document.querySelectorAll('.tab-pane');

            tabItems.forEach(tab => {
                tab.addEventListener('click', function () {
                    const tabId = this.getAttribute('data-tab');
                    
                    // Update URL without reloading page
                    const url = new URL(window.location);
                    url.searchParams.set('tab', tabId);
                    window.history.pushState({}, '', url);

                    // Update active tab
                    tabItems.forEach(item => item.classList.remove('active'));
                    this.classList.add('active');

                    // Show corresponding tab pane
                    tabPanes.forEach(pane => {
                        pane.classList.remove('active');
                        if (pane.id === tabId) {
                            pane.classList.add('active');
                        }
                    });

                    // Reinitialize sortable for requirements tab if needed
                    if (tabId === 'grant_requirements') {
                        setTimeout(initializeSortable, 100);
                    }
                });
            });

            // Handle browser back/forward buttons
            window.addEventListener('popstate', function () {
                const urlParams = new URLSearchParams(window.location.search);
                const tab = urlParams.get('tab') || 'sem_sy';
                
                // Update active tab
                tabItems.forEach(item => {
                    if (item.getAttribute('data-tab') === tab) {
                        item.classList.add('active');
                    } else {
                        item.classList.remove('active');
                    }
                });
                
                // Show corresponding tab pane
                tabPanes.forEach(pane => {
                    if (pane.id === tab) {
                        pane.classList.add('active');
                    } else {
                        pane.classList.remove('active');
                    }
                });
            });

            // Form validation for school year pattern
            const schoolYearInput = document.getElementById('school_year');
            if (schoolYearInput) {
                schoolYearInput.addEventListener('input', function (e) {
                    const value = e.target.value;
                    if (!/^\d{0,4}(-\d{0,4})?$/.test(value)) {
                        e.target.value = value.slice(0, -1);
                    }
                });
            }

            // Focus management for better UX
            const forms = document.querySelectorAll('.add-form');
            forms.forEach(form => {
                const firstInput = form.querySelector('input, select, textarea');
                if (firstInput) {
                    firstInput.focus();
                }
            });

            // Confirm delete with custom message
            const deleteButtons = document.querySelectorAll('.btn-delete-icon');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    if (!confirm('Are you sure you want to delete this item?')) {
                        e.preventDefault();
                    }
                });
            });

            // Initialize sortable if we're on the requirements tab
            if (document.getElementById('grant_requirements').classList.contains('active')) {
                setTimeout(initializeSortable, 100);
            }
        });

        // Function to initialize sortable
        function initializeSortable() {
            console.log("Initializing sortable...");
            
            $('.sortable-requirements').each(function(index) {
                console.log("Initializing sortable for list #" + index);
                
                $(this).sortable({
                    handle: '.reorder-handle',
                    placeholder: 'ui-sortable-placeholder',
                    forcePlaceholderSize: true,
                    start: function(e, ui) {
                        console.log("Drag started");
                        ui.placeholder.height(ui.item.height());
                    },
                    update: function(event, ui) {
                        console.log("Order changed, updating hidden inputs...");
                        
                        // Update hidden inputs when order changes
                        const $list = $(this);
                        let requirementIds = [];
                        
                        $list.find('li').each(function(index) {
                            const $li = $(this);
                            const id = $li.data('id');
                            requirementIds.push(id);
                            
                            // Update the hidden input value
                            $li.find('input[name="requirement_order[]"]').val(id);
                        });
                        
                        console.log("Updated IDs:", requirementIds);
                        
                        // Show save button for this specific form
                        const $form = $list.closest('.reorder-form');
                        $form.find('.save-order-btn').show();
                    }
                }).disableSelection();
                
                console.log("Sortable initialized for list #" + index);
            });

            // Hide all save buttons initially
            $('.save-order-btn').hide();

            // Handle form submission for reordering
            $('.reorder-form').on('submit', function (e) {
                const $form = $(this);
                const grantName = $form.data('grant');

                console.log("Saving order for grant:", grantName);
                
                // Validate that all requirement IDs are present
                const requirementIds = [];
                $form.find('input[name="requirement_order[]"]').each(function () {
                    const val = $(this).val();
                    if (val) {
                        requirementIds.push(val);
                    }
                });

                console.log("Requirement IDs to save:", requirementIds);
                
                if (requirementIds.length === 0) {
                    e.preventDefault();
                    alert('No requirements to save!');
                    return false;
                }

                // Show loading state
                const $submitBtn = $form.find('.save-order-btn');
                const originalText = $submitBtn.html();
                $submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...');
                $submitBtn.prop('disabled', true);

                // Re-enable after a short delay (in case of error)
                setTimeout(() => {
                    $submitBtn.html(originalText);
                    $submitBtn.prop('disabled', false);
                }, 3000);

                return true;
            });
            
            console.log("Sortable test: Try dragging any requirement item");
        }
    </script>
</body>

</html>