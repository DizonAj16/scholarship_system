<?php
require '../includes/session.php';

try {
    // Pagination settings
    $limit = 5;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int) $_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Ensure the logged-in user is identified
    if (!isset($_SESSION['id'])) {
        throw new Exception("User not logged in.");
    }

    $userId = $_SESSION['id']; // Fetch `user_id` from the session

    // Debugging: Ensure `user_id` is being set correctly

    // Search query handling
    $searchTerm = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';

    // Fetch total number of rows with search and user filter
    $totalQuery = $pdo->prepare("
        SELECT COUNT(*) AS total
        FROM scholarship_applications
        WHERE user_id = :user_id
        AND (application_id LIKE :search OR full_name LIKE :search)
    ");
    $totalQuery->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $totalQuery->bindValue(':search', $searchTerm, PDO::PARAM_STR);
    $totalQuery->execute();
    $totalRows = $totalQuery->fetchColumn();

    // Calculate total pages
    $totalPages = ceil($totalRows / $limit);
    if ($page > $totalPages && $totalPages > 0) {
        $page = $totalPages;
        $offset = ($page - 1) * $limit;
    }

    // Sorting logic
    $sortBy = 'application_id';
    $sortOrder = 'DESC';
    if (isset($_GET['sort']) && $_GET['sort'] === 'asc') {
        $sortOrder = 'ASC';
    }
    if (isset($_GET['sort_status']) && in_array($_GET['sort_status'], ['approved', 'pending', 'not qualified'])) {
        $sortBy = 'status';
    }

    // Status filter logic
    $statusFilter = '';
    if (isset($_GET['sort_status']) && in_array($_GET['sort_status'], ['approved', 'pending', 'not qualified'])) {
        $statusFilter = "AND status = :status";
    }

    // Prepare the query with pagination, sorting, and search
    $query = "
        SELECT application_id, full_name, course, yr_sec, cell_no, scholarship_grant, date, status
        FROM scholarship_applications
        WHERE user_id = :user_id
        AND (application_id LIKE :search OR full_name LIKE :search)
        $statusFilter
        ORDER BY $sortBy $sortOrder
        LIMIT :limit OFFSET :offset
    ";
    $stmt = $pdo->prepare($query);

    // Bind parameters
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':search', $searchTerm, PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    if (!empty($statusFilter)) {
        $stmt->bindValue(':status', $_GET['sort_status'], PDO::PARAM_STR);
    }

    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($rows)) {
    }
} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

?>



<!-- Display Success and Error Messages -->
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
            <li>
                <a href="../index.php">
                    <i class="fas fa-solid fa-house"></i>
                    <span class="nav-item-2">Home</span>
                </a>
            </li>
            <?php if ($_SESSION["role"] === 'admin'): ?>
                <li>
                    <a href="./dashboard.php">
                        <i class="fas fa-solid fa-gauge"></i>
                        <span class="nav-item-2">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="./scholarship_form.php">
                        <i class="fas fa-solid fa-file"></i>
                        <span class="nav-item-2">Scholarship Form</span>
                    </a>
                </li>
                <li>
                    <a href="./applications.php">
                        <i class="fas fa-solid fa-folder"></i>
                        <span class="nav-item-2">Applications</span>
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
            <?php elseif ($_SESSION["role"] === 'student'): ?>
                <li>
                    <a href="./my_applications.php" class="active">
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
        <h1>My Applications</h1>
        <div class="table-container">
            <div class="search-container">
                <form action="" method="GET">
                    <input type="text" name="search" id="searchInput" placeholder="Search by Application ID or Name"
                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit" class="btn-search"><i class="fas fa-search"></i> Search</button>
                    <button type="button" class="btn-reset" id="resetButton" onclick="resetSearch()">
                        <i class="fas fa-redo"></i> Reset
                    </button>
                </form>
            </div>
        </div>


        <table>
            <thead>
                <tr>
                    <th>Application ID
                        <select id="sortDropdown" onchange="sortApplications()">
                            <option value="desc" <?php echo isset($_GET['sort']) && $_GET['sort'] == 'desc' ? 'selected' : ''; ?>>Newest to Oldest</option>
                            <option value="asc" <?php echo isset($_GET['sort']) && $_GET['sort'] == 'asc' ? 'selected' : ''; ?>>Oldest to Newest</option>
                        </select>
                    </th>
                    <th>Full Name</th>
                    <th>Course</th>
                    <th>Year & Section</th>
                    <th>Phone</th>
                    <th>Scholarship Grant</th>
                    <th>Application Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($rows) > 0): ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['application_id']) ?></td>
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
                                <div class="actions">
                                    <a href="../application_process/view_application.php?id=<?= $row['application_id'] ?>"
                                        class="btn-view" data-tooltip="View Application">
                                        <i class="fas fa-solid fa-eye"></i>
                                    </a>
                                    <a href="../application_process/delete_application.php?id=<?= $row['application_id'] ?>"
                                        class="btn-delete"
                                        onclick="return confirm('This action cannot be undone. Proceed with deletion?');"
                                        data-tooltip="Delete Application">
                                        <i class="fas fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>


                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 20px;">No data available</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>



        <ul class="pagination">
            <?php if ($page > 1): ?>
                <li><a href="?page=1&search=<?php echo htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES); ?>&sort_status=<?php echo htmlspecialchars($_GET['sort_status'] ?? '', ENT_QUOTES); ?>&sort=<?php echo htmlspecialchars($_GET['sort'] ?? '', ENT_QUOTES); ?>"
                        class="prev-next">
                        &laquo; First</a>
                </li>
                <li><a href="?page=<?php echo $page - 1; ?>&search=<?php echo htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES); ?>&sort_status=<?php echo htmlspecialchars($_GET['sort_status'] ?? '', ENT_QUOTES); ?>&sort=<?php echo htmlspecialchars($_GET['sort'] ?? '', ENT_QUOTES); ?>"
                        class="prev-next">
                        &laquo; Prev</a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li><a href="?page=<?php echo $i; ?>&search=<?php echo htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES); ?>&sort_status=<?php echo htmlspecialchars($_GET['sort_status'] ?? '', ENT_QUOTES); ?>&sort=<?php echo htmlspecialchars($_GET['sort'] ?? '', ENT_QUOTES); ?>"
                        class="<?php echo $i == $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a></li>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <li><a href="?page=<?php echo $page + 1; ?>&search=<?php echo htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES); ?>&sort_status=<?php echo htmlspecialchars($_GET['sort_status'] ?? '', ENT_QUOTES); ?>&sort=<?php echo htmlspecialchars($_GET['sort'] ?? '', ENT_QUOTES); ?>"
                        class="prev-next">
                        Next &raquo;</a>
                </li>
                <li><a href="?page=<?php echo $totalPages; ?>&search=<?php echo htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES); ?>&sort_status=<?php echo htmlspecialchars($_GET['sort_status'] ?? '', ENT_QUOTES); ?>&sort=<?php echo htmlspecialchars($_GET['sort'] ?? '', ENT_QUOTES); ?>"
                        class="prev-next">
                        Last &raquo;</a>
                </li>
            <?php endif; ?>
        </ul>






        <script>
            // Display the success or error message
            var successMessage = document.getElementById("successMessage");
            if (successMessage) {
                successMessage.style.display = "inline-block";
                setTimeout(function () {
                    successMessage.style.display = "none";
                }, 5000);
            }

            var errorMessage = document.getElementById("errorMessage");
            if (errorMessage) {
                errorMessage.style.display = "inline-block";
                setTimeout(function () {
                    errorMessage.style.display = "none";
                }, 5000);
            }
            function resetSearch() {
                // Clear the search input field
                document.getElementById("searchInput").value = "";

                // Redirect to the page with default parameters (page 1, no search or sort)
                var url = new URL(window.location.href);

                // Remove search, sort, and page parameters
                url.searchParams.delete('search');
                url.searchParams.delete('sort');
                url.searchParams.delete('sort_status');
                url.searchParams.delete('page'); // Optionally, reset to page 1 if you want to go back to the first page

                // Reload the page with default parameters
                window.location.href = url.toString();
            }

            function sortApplications() {
                var sortValue = document.getElementById("sortDropdown").value;
                var url = new URL(window.location.href);
                url.searchParams.set('sort', sortValue); // Set the sort parameter
                window.location.href = url.toString(); // Reload the page with the new sort parameter
            }


            function sortApplicationsByStatus() {
                const sortStatusDropdown = document.getElementById('sortStatusDropdown');
                const selectedStatus = sortStatusDropdown.value;
                const url = new URL(window.location.href);
                if (selectedStatus) {
                    url.searchParams.set('sort_status', selectedStatus); // Set the selected status
                } else {
                    url.searchParams.delete('sort_status'); // Remove the status filter if 'All' is selected
                }
                url.searchParams.set('page', 1); // Reset to the first page
                window.location.href = url.toString(); // Reload the page with the new parameters
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



        </script>
</body>

</html>