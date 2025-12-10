<?php
require '../includes/session.php';

try {
    // Pagination settings
    $limit = 5; // Number of rows per page
    $page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int) $_GET['page'] : 1; // Current page
    $offset = ($page - 1) * $limit;

    // Search query handling
    $searchTerm = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%'; // Default to all records if no search term

    // Fetch dropdown values for filters
    $courseQuery = $pdo->query("SELECT DISTINCT course FROM dropdown_course_major ORDER BY course");
    $courses = $courseQuery->fetchAll(PDO::FETCH_COLUMN);

    $grantQuery = $pdo->query("SELECT DISTINCT grant_name FROM dropdown_scholarship_grant ORDER BY grant_name");
    $grants = $grantQuery->fetchAll(PDO::FETCH_COLUMN);

    // Fetch unique year and sections from applications
    $yrSecQuery = $pdo->query("SELECT DISTINCT yr_sec FROM scholarship_applications WHERE yr_sec IS NOT NULL AND yr_sec != '' ORDER BY yr_sec");
    $yrSecs = $yrSecQuery->fetchAll(PDO::FETCH_COLUMN);

    // Fetch unique semester and school year combinations from applications
    // Using the actual data from scholarship_applications table
    $semSyQuery = $pdo->query("SELECT DISTINCT semester, school_year FROM scholarship_applications WHERE semester IS NOT NULL AND semester != '' AND school_year IS NOT NULL AND school_year != '' ORDER BY school_year DESC, FIELD(semester, '1st sem', '2nd sem', 'Summer')");
    $semSysRaw = $semSyQuery->fetchAll(PDO::FETCH_ASSOC);
    $semSys = [];
    foreach ($semSysRaw as $row) {
        $semSys[] = $row['semester'] . ' ' . $row['school_year'];
    }

    // Fetch unique names for the header filter
    $nameQuery = $pdo->query("SELECT DISTINCT full_name FROM scholarship_applications WHERE full_name IS NOT NULL AND full_name != '' ORDER BY full_name");
    $names = $nameQuery->fetchAll(PDO::FETCH_COLUMN);

    // Fetch total number of rows with search and filters
    $whereConditions = ["1=1"]; // Start with always true condition
    $params = [];

    // Search filter
    if (isset($_GET['search']) && $_GET['search'] !== '') {
        $whereConditions[] = "(application_id LIKE :search OR full_name LIKE :search)";
        $params[':search'] = '%' . $_GET['search'] . '%';
    }

    // Name filter (from table header)
    $selectedName = isset($_GET['name_filter']) && $_GET['name_filter'] !== '' ? $_GET['name_filter'] : null;
    if ($selectedName) {
        $whereConditions[] = "full_name = :name";
        $params[':name'] = $selectedName;
    }

    // Course filter
    $selectedCourse = isset($_GET['course_filter']) && $_GET['course_filter'] !== '' ? $_GET['course_filter'] : null;
    if ($selectedCourse) {
        $whereConditions[] = "course = :course";
        $params[':course'] = $selectedCourse;
    }

    // Year & Section filter
    $selectedYrSec = isset($_GET['yr_sec_filter']) && $_GET['yr_sec_filter'] !== '' ? $_GET['yr_sec_filter'] : null;
    if ($selectedYrSec) {
        $whereConditions[] = "yr_sec = :yr_sec";
        $params[':yr_sec'] = $selectedYrSec;
    }

    // Scholarship Grant filter
    $selectedGrant = isset($_GET['grant_filter']) && $_GET['grant_filter'] !== '' ? $_GET['grant_filter'] : null;
    if ($selectedGrant) {
        $whereConditions[] = "scholarship_grant = :grant";
        $params[':grant'] = $selectedGrant;
    }

    // Status filter
    $selectedStatus = isset($_GET['sort_status']) && in_array($_GET['sort_status'], ['approved', 'pending', 'not qualified']) ? $_GET['sort_status'] : null;
    if ($selectedStatus) {
        $whereConditions[] = "status = :status";
        $params[':status'] = $selectedStatus;
    }

    // Semester & School Year filter - FIXED VERSION
    $selectedSemSy = isset($_GET['sem_sy_filter']) && $_GET['sem_sy_filter'] !== '' ? $_GET['sem_sy_filter'] : null;
    if ($selectedSemSy) {
        // Split the combined value correctly - "1st sem 2025-2026"
        // Find the last space to separate semester from school_year
        $lastSpacePos = strrpos($selectedSemSy, ' ');
        if ($lastSpacePos !== false) {
            $semester = substr($selectedSemSy, 0, $lastSpacePos); // "1st sem"
            $schoolYear = substr($selectedSemSy, $lastSpacePos + 1); // "2025-2026"
            $whereConditions[] = "semester = :semester AND school_year = :school_year";
            $params[':semester'] = $semester;
            $params[':school_year'] = $schoolYear;
        }
    }

    $whereClause = implode(' AND ', $whereConditions);
    
    // Debug: Uncomment to see the SQL query
    // echo "WHERE Clause: $whereClause<br>";
    // echo "Params: ";
    // print_r($params);
    // echo "<br>";
    
    // Count total rows
    $totalQuery = $pdo->prepare("SELECT COUNT(*) AS total FROM scholarship_applications WHERE $whereClause");
    foreach ($params as $key => $value) {
        $totalQuery->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $totalQuery->execute();
    $totalRows = $totalQuery->fetchColumn();

    // Calculate total pages
    $totalPages = ceil($totalRows / $limit);

    // Ensure page doesn't exceed total pages
    if ($page > $totalPages && $totalPages > 0) {
        $page = $totalPages;
        $offset = ($page - 1) * $limit;
    }

    // Sorting logic (default to application_id)
    $sortBy = isset($_GET['sort_by']) && in_array($_GET['sort_by'], ['application_id', 'full_name', 'course', 'yr_sec', 'date', 'status', 'sem_sy']) ? $_GET['sort_by'] : 'application_id';
    $sortOrder = isset($_GET['sort']) && $_GET['sort'] == 'asc' ? 'ASC' : 'DESC';

    // Prepare the query with pagination, sorting, search, and filters
    $query = "
        SELECT 
            sa.application_id, 
            CONCAT(sa.semester, ' ', sa.school_year) as sem_sy,
            sa.full_name, 
            sa.course, 
            sa.yr_sec, 
            sa.cell_no, 
            sa.scholarship_grant, 
            sa.date, 
            sa.status, 
            sa.notified
        FROM scholarship_applications sa
        WHERE $whereClause
        ORDER BY $sortBy $sortOrder, application_id $sortOrder
        LIMIT :limit OFFSET :offset
    ";

    // Debug: Uncomment to see the final query
    // echo "Query: $query<br>";

    $stmt = $pdo->prepare($query);

    // Bind all parameters
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Debug: Uncomment to see fetched rows
    // echo "Rows fetched: " . count($rows) . "<br>";
    // print_r($rows);
} catch (PDOException $e) {
    // Handle database errors
    die("Error fetching data: " . $e->getMessage());
}
?>

<?php
if (isset($_SESSION['success_message'])) {
    echo "<div id='successMessage' class='alert success'>{$_SESSION['success_message']}</div>";
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    echo "<div id='errorMessage' class='alert error'>{$_SESSION['error_message']}</div>";
    unset($_SESSION['error_message']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../js/fontawesome.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/preloader.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/applications.css?v=<?php echo time(); ?>">
    <script src="../js/preloader.js?v=<?php echo time(); ?>"></script>
    <script src="../js/applications.js?v=<?php echo time(); ?>"></script>
    <title>Application Management</title>
    <style>
        /* Additional CSS for table header filters */
        .table-header-filter {
            display: block;
            margin-top: 5px;
            padding: 4px 8px;
            width: 90%;
            font-size: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f9f9f9;
        }
        
        .table-header-filter:focus {
            outline: none;
            border-color: #4a90e2;
            background-color: #fff;
        }
        
        th {
            vertical-align: top;
            padding-top: 12px;
        }
        
        .filter-label {
            display: block;
            margin-bottom: 5px;
            font-size: 12px;
            color: #666;
        }
        
        /* Active filter indicator */
        .table-header-filter:not([value=""]) {
            background-color: #e3f2fd !important;
            border-color: #4a90e2 !important;
        }
        
        .active-filters {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 10px;
            margin: 10px 0;
            font-size: 14px;
        }
        
        .active-filters strong {
            color: #495057;
        }
        
        .clear-filters {
            margin-left: 10px;
            color: #dc3545;
            text-decoration: none;
        }
        
        .clear-filters:hover {
            text-decoration: underline;
        }
    </style>
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
                <li>
                    <a href="./dashboard.php">
                        <i class="fas fa-solid fa-gauge"></i>
                        <span class="nav-item-2">Dashboard</span>
                    </a>
                </li>

                <li><a href="./announcement.php"><i class="fas fa-bullhorn"></i>
                        <span class="nav-item-2">Announcements</span></a>
                </li>

                <li>
                    <a href="./scholarship_form.php">
                        <i class="fas fa-solid fa-file"></i>
                        <span class="nav-item-2">Scholarship Form</span>
                    </a>
                </li>
                <li><a href="./manage_dropdowns.php"><i class="fas fa-list"></i><span class="nav-item-2">Manage
                            Dropdowns</span></a></li>
                <li>
                    <a href="./applications.php" class="active">
                        <i class="fas fa-solid fa-folder"></i>
                        <span class="nav-item-2">Applications</span>
                    </a>
                </li>
                <li><a href="./logs.php"><i class="fas fa-clipboard-list"></i>
                        <span class="nav-item-2">Logs</span></a></li>
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
                    <a href="./resources.php">
                        <i class="fas fa-solid fa-book"></i>
                        <span class="nav-item-2">Resources</span>
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
                <li>
                    <a href="./contact.php">
                        <i class="fas fa-solid fa-envelope"></i>
                        <span class="nav-item-2">Contact Us</span>
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

    <div class="container">
        <h1>Applications Management</h1>
        <div class="table-container">
            <div class="search-container">
                <form action="" method="GET" id="filterForm">
                    <input type="text" name="search" id="searchInput" placeholder="Search by Application ID or Name"
                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit" class="btn-search"><i class="fas fa-search"></i> Search</button>
                    <button type="button" class="btn-reset" id="resetButton" onclick="resetFilters()">
                        <i class="fas fa-redo"></i> Reset All Filters
                    </button>
                </form>
            </div>
            
            <!-- Active Filters Display -->
            <?php 
            $activeFilters = [];
            if (isset($_GET['search']) && $_GET['search'] !== '') {
                $activeFilters[] = "Search: " . htmlspecialchars($_GET['search']);
            }
            if (isset($_GET['name_filter']) && $_GET['name_filter'] !== '') {
                $activeFilters[] = "Name: " . htmlspecialchars($_GET['name_filter']);
            }
            if (isset($_GET['course_filter']) && $_GET['course_filter'] !== '') {
                $activeFilters[] = "Course: " . htmlspecialchars($_GET['course_filter']);
            }
            if (isset($_GET['yr_sec_filter']) && $_GET['yr_sec_filter'] !== '') {
                $activeFilters[] = "Year & Section: " . htmlspecialchars($_GET['yr_sec_filter']);
            }
            if (isset($_GET['grant_filter']) && $_GET['grant_filter'] !== '') {
                $activeFilters[] = "Grant: " . htmlspecialchars($_GET['grant_filter']);
            }
            if (isset($_GET['sort_status']) && $_GET['sort_status'] !== '') {
                $activeFilters[] = "Status: " . htmlspecialchars(ucfirst($_GET['sort_status']));
            }
            if (isset($_GET['sem_sy_filter']) && $_GET['sem_sy_filter'] !== '') {
                $activeFilters[] = "Semester & SY: " . htmlspecialchars($_GET['sem_sy_filter']);
            }
            
            if (!empty($activeFilters)): ?>
            <div class="active-filters">
                <strong>Active Filters:</strong>
                <?php echo implode(' • ', $activeFilters); ?>
                <a href="applications.php" class="clear-filters">Clear All</a>
            </div>
            <?php endif; ?>
        </div>

        <table>
            <thead>
                <tr>
                    <th>
                        Application ID
                        <select id="sortDropdown" onchange="updateSort('application_id')" class="table-header-filter">
                            <option value="">Sort Order</option>
                            <option value="desc" <?php echo isset($_GET['sort']) && $_GET['sort'] == 'desc' ? 'selected' : ''; ?>>Newest to Oldest</option>
                            <option value="asc" <?php echo isset($_GET['sort']) && $_GET['sort'] == 'asc' ? 'selected' : ''; ?>>Oldest to Newest</option>
                        </select>
                    </th>
                    <th>
                        Semester & School Year
                        <select name="sem_sy_filter" onchange="applyTableFilter(this)" class="table-header-filter">
                            <option value="">All Semesters</option>
                            <?php foreach ($semSys as $semSy): ?>
                                <option value="<?php echo htmlspecialchars($semSy); ?>" 
                                    <?php echo isset($_GET['sem_sy_filter']) && $_GET['sem_sy_filter'] == $semSy ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($semSy); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </th>
                    <th>
                        Full Name
                        <select name="name_filter" onchange="applyTableFilter(this)" class="table-header-filter">
                            <option value="">All Names</option>
                            <?php foreach ($names as $name): ?>
                                <option value="<?php echo htmlspecialchars($name); ?>" 
                                    <?php echo isset($_GET['name_filter']) && $_GET['name_filter'] == $name ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </th>
                    <th>
                        Course
                        <select name="course_filter" onchange="applyTableFilter(this)" class="table-header-filter">
                            <option value="">All Courses</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?php echo htmlspecialchars($course); ?>" 
                                    <?php echo isset($_GET['course_filter']) && $_GET['course_filter'] == $course ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($course); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </th>
                    <th>
                        Year & Section
                        <select name="yr_sec_filter" onchange="applyTableFilter(this)" class="table-header-filter">
                            <option value="">All Year & Sections</option>
                            <?php foreach ($yrSecs as $yrSec): ?>
                                <option value="<?php echo htmlspecialchars($yrSec); ?>" 
                                    <?php echo isset($_GET['yr_sec_filter']) && $_GET['yr_sec_filter'] == $yrSec ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($yrSec); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </th>
                    <th>
                        Phone
                    </th>
                    <th>
                        Scholarship Grant
                        <select name="grant_filter" onchange="applyTableFilter(this)" class="table-header-filter">
                            <option value="">All Grants</option>
                            <?php foreach ($grants as $grant): ?>
                                <option value="<?php echo htmlspecialchars($grant); ?>" 
                                    <?php echo isset($_GET['grant_filter']) && $_GET['grant_filter'] == $grant ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($grant); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </th>
                    <th>
                        Application Date
                    </th>
                    <th>
                        Status
                        <select name="sort_status" onchange="applyTableFilter(this)" class="table-header-filter">
                            <option value="">All Status</option>
                            <option value="approved" <?php echo isset($_GET['sort_status']) && $_GET['sort_status'] == 'approved' ? 'selected' : ''; ?>>Approved</option>
                            <option value="not qualified" <?php echo isset($_GET['sort_status']) && $_GET['sort_status'] == 'not qualified' ? 'selected' : ''; ?>>Not Qualified</option>
                            <option value="pending" <?php echo isset($_GET['sort_status']) && $_GET['sort_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        </select>
                    </th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($rows) > 0): ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['application_id']) ?></td>
                            <td><?= htmlspecialchars($row['sem_sy'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['full_name']) ?></td>
                            <td><?= htmlspecialchars($row['course']) ?></td>
                            <td><?= htmlspecialchars($row['yr_sec']) ?></td>
                            <td><?= htmlspecialchars($row['cell_no']) ?></td>
                            <td><?= htmlspecialchars($row['scholarship_grant']) ?></td>
                            <td>
                                <?php
                                $formattedDate = date('F j, Y, g:ia', strtotime($row['date']));
                                echo htmlspecialchars($formattedDate);
                                ?>
                            </td>
                            <td>
                                <span class="status <?php echo htmlspecialchars($row['status']); ?>">
                                    <?php echo htmlspecialchars(ucfirst($row['status'])); ?>
                                </span>
                            </td>

                            <td>
                                <div class="actions">
                                    <a href="../application_process/view_application_admin.php?id=<?= $row['application_id'] ?>"
                                        class="btn-view" data-tooltip="View Application">
                                        <i class="fas fa-solid fa-eye"></i>
                                    </a>
                                    <a href="../application_process/edit_application_admin.php?id=<?= $row['application_id'] ?>"
                                        class="btn-view" data-tooltip="Edit Application">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <a href="../application_process/delete_application.php?id=<?= $row['application_id'] ?>"
                                        class="btn-delete"
                                        onclick="return confirm('This action cannot be undone. Proceed with deletion?');"
                                        data-tooltip="Delete Application">
                                        <i class="fas fa-solid fa-trash"></i>
                                    </a>
                                    <?php if ($row['status'] === 'approved' && $row['notified'] === 'no'): ?>
                                        <!-- Show Notify Applicant button if approved but not notified -->
                                        <a href="../application_process/approve_application.php?id=<?= $row['application_id'] ?>&action=notify"
                                            class="btn-notify"
                                            onclick="return confirm('Are you sure you want to notify the applicant?');"
                                            data-tooltip="Notify Applicant">
                                            <i class="fas fa-solid fa-envelope"></i>
                                        </a>
                                    <?php elseif ($row['status'] !== 'approved'): ?>
                                        <!-- Show Approve and Notify and Approve Only buttons if not approved -->
                                        <a href="../application_process/approve_application.php?id=<?= $row['application_id'] ?>&action=notify"
                                            class="btn-approve"
                                            onclick="return confirm('Are you sure you want to approve this application and notify the applicant?');"
                                            data-tooltip="Approve Application and Notify">
                                            <i class="fas fa-solid fa-envelope"></i>
                                        </a>
                                        <a href="../application_process/approve_application.php?id=<?= $row['application_id'] ?>&notify=no"
                                            class="btn-approve"
                                            onclick="return confirm('Are you sure you want to approve this application without notifying?');"
                                            data-tooltip="Approve Application Only">
                                            <i class="fas fa-solid fa-circle-check"></i>
                                        </a>
                                        <!-- Show Reject and Pending buttons only if not approved -->
                                        <a href="../application_process/reject_application.php?id=<?= $row['application_id'] ?>"
                                            class="btn-reject"
                                            onclick="return confirm('Are you sure you want to reject this application?');"
                                            data-tooltip="Reject Application">
                                            <i class="fas fa-solid fa-circle-xmark"></i>
                                        </a>
                                        <a href="../application_process/pending_application.php?id=<?= $row['application_id'] ?>"
                                            class="btn-pending"
                                            onclick="return confirm('Are you sure you want to change the status of this application back to pending?');"
                                            data-tooltip="Change to Pending">
                                            <i class="fas fa-solid fa-circle-notch"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 20px;">
                            No data available
                            <?php if (!empty($activeFilters)): ?>
                                <br><small>Try changing your filters</small>
                                <?php 
                                // Debug output - uncomment to see what's happening
                                // echo "<br><small>Debug WHERE: " . $whereClause . "</small>";
                                // echo "<br><small>Debug Params: ";
                                // print_r($params);
                                // echo "</small>";
                                ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="export-button-container">
            <a href="../application_process/export_application.php" class="btn-export">
                <i class="fas fa-solid fa-download"></i> Export to CSV
            </a>
        </div>

        <ul class="pagination">
            <?php if ($page > 1): ?>
                <li><a href="?<?php echo buildQueryString(['page' => 1]); ?>" class="prev-next">&laquo; First</a></li>
                <li><a href="?<?php echo buildQueryString(['page' => $page - 1]); ?>" class="prev-next">&laquo; Prev</a></li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li><a href="?<?php echo buildQueryString(['page' => $i]); ?>" class="<?php echo $i == $page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a></li>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <li><a href="?<?php echo buildQueryString(['page' => $page + 1]); ?>" class="prev-next">Next &raquo;</a></li>
                <li><a href="?<?php echo buildQueryString(['page' => $totalPages]); ?>" class="prev-next">Last &raquo;</a></li>
            <?php endif; ?>
        </ul>

        <script>
            // Display the success or error message
            var successMessage = document.getElementById("successMessage");
            if (successMessage) {
                successMessage.style.display = "inline-block";
                setTimeout(function () {
                    successMessage.style.display = "none";
                }, 8000);
            }

            var errorMessage = document.getElementById("errorMessage");
            if (errorMessage) {
                errorMessage.style.display = "inline-block";
                setTimeout(function () {
                    errorMessage.style.display = "none";
                }, 8000);
            }

            function buildQueryString(params) {
                const urlParams = new URLSearchParams();
                
                // Add existing parameters
                <?php 
                $currentParams = $_GET;
                unset($currentParams['page']); // Remove page as we'll add it separately
                foreach ($currentParams as $key => $value): 
                ?>
                    urlParams.set('<?php echo htmlspecialchars($key); ?>', '<?php echo htmlspecialchars($value); ?>');
                <?php endforeach; ?>
                
                // Add new parameters
                for (const key in params) {
                    urlParams.set(key, params[key]);
                }
                
                return urlParams.toString();
            }

            function resetFilters() {
                // Redirect to the page with default parameters (page 1, no filters)
                window.location.href = 'applications.php';
            }

            function updateSort(field) {
                var sortValue = document.getElementById("sortDropdown").value;
                if (sortValue === "") return; // Don't do anything if "Sort Order" is selected
                
                var url = new URL(window.location.href);
                url.searchParams.set('sort', sortValue);
                url.searchParams.set('sort_by', field);
                url.searchParams.set('page', 1); // Reset to page 1
                window.location.href = url.toString();
            }

            function applyTableFilter(selectElement) {
                const name = selectElement.name;
                const value = selectElement.value;
                
                var url = new URL(window.location.href);
                
                if (value) {
                    url.searchParams.set(name, value);
                } else {
                    url.searchParams.delete(name);
                }
                
                // Reset to page 1 when filtering
                url.searchParams.set('page', 1);
                
                // Remove sort parameters if not sorting by application_id
                if (name !== 'sort') {
                    url.searchParams.delete('sort');
                    url.searchParams.delete('sort_by');
                }
                
                window.location.href = url.toString();
            }

            function toggleNav() {
                const sideNav = document.getElementById('sideNav');
                const toggleIcon = document.getElementById('toggle-icon');

                // Check if the navigation is currently open (visible)
                if (sideNav.style.transform === 'translateX(0px)' || sideNav.style.transform === '') {
                    // Close the navigation
                    sideNav.style.transform = 'translateX(-250px)';
                    toggleIcon.classList.remove('fa-times');
                    toggleIcon.classList.add('fa-bars');
                } else {
                    // Open the navigation
                    sideNav.style.transform = 'translateX(0px)';
                    toggleIcon.classList.remove('fa-bars');
                    toggleIcon.classList.add('fa-times');
                }
            }
            
            // Add visual feedback for active filters
            document.addEventListener('DOMContentLoaded', function() {
                const filters = document.querySelectorAll('.table-header-filter');
                filters.forEach(filter => {
                    if (filter.value !== '') {
                        filter.style.backgroundColor = '#e3f2fd';
                        filter.style.borderColor = '#4a90e2';
                    }
                });
            });
        </script>
</body>

</html>

<?php
// Helper function to build query strings for pagination
function buildQueryString($newParams = []) {
    $params = $_GET;
    foreach ($newParams as $key => $value) {
        $params[$key] = $value;
    }
    
    // Remove empty parameters
    foreach ($params as $key => $value) {
        if ($value === '' || $value === null) {
            unset($params[$key]);
        }
    }
    
    return http_build_query($params);
}