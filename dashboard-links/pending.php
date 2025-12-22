<?php

require '../includes/session.php';

// Pagination logic - Get limit from request or default to 5
$defaultLimit = 5;
$limitOptions = [5, 10, 50, 100, 500, 'all'];
$limit = isset($_GET['limit']) && in_array($_GET['limit'], array_map('strval', $limitOptions)) ? $_GET['limit'] : $defaultLimit;

// Convert 'all' to a large number for SQL query
$sqlLimit = ($limit === 'all') ? 1000000 : (int)$limit;

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $sqlLimit; // Calculate offset

try {
    // Fetch total records count
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM scholarship_applications WHERE status = 'Pending'");
    $countStmt->execute();
    $totalRecords = $countStmt->fetchColumn();

    // Fetch records for the current page
    $stmt = $pdo->prepare("SELECT * FROM scholarship_applications WHERE status = 'Pending' LIMIT :limit OFFSET :offset");
    $stmt->bindParam(':limit', $sqlLimit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $pendingApplications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate total pages
    $totalPages = ($limit === 'all') ? 1 : ceil($totalRecords / $sqlLimit);
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
    <title>Pending Applications</title>
    <link rel="stylesheet" href="../css/approved.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/pagination.css?v=<?php echo time(); ?>">
    
    <!-- Print-specific styles -->
    <style>
        @media print {
            /* Hide unnecessary elements */
            header, footer, .pagination, .print-button,
            button, nav, .no-print, .pagination-controls {
                display: none !important;
            }
            
            /* Reset margins for printing */
            @page {
                margin: 1cm;
                size: landscape;
            }
            
            /* Main print layout */
            body {
                margin: 0;
                padding: 0;
                font-family: 'Times New Roman', Times, serif;
                font-size: 12pt;
                color: #000;
                background: #fff !important;
            }
            
            /* Print header */
            .print-header {
                display: block !important;
                text-align: center;
                margin-bottom: 20px;
                border-bottom: 2px solid #000;
                padding-bottom: 15px;
            }
            
            .print-header h1 {
                font-size: 18pt;
                margin: 10px 0 5px 0;
                color: #000;
            }
            
            .print-header .document-info {
                font-size: 10pt;
                margin: 5px 0;
                color: #666;
            }
            
            .print-header .print-date {
                font-size: 10pt;
                margin-top: 10px;
            }
            
            /* Table styling for print */
            table {
                width: 100% !important;
                border-collapse: collapse;
                margin: 20px 0;
                page-break-inside: avoid;
            }
            
            table thead {
                display: table-header-group;
            }
            
            table th {
                background-color: #f2f2f2 !important;
                color: #000 !important;
                border: 1px solid #000;
                padding: 8px;
                font-weight: bold;
                font-size: 10pt;
                text-align: left;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            table td {
                border: 1px solid #000;
                padding: 8px;
                font-size: 10pt;
                text-align: left;
            }
            
            /* Page breaks */
            tr {
                page-break-inside: avoid;
            }
            
            /* Summary information */
            .print-summary {
                margin: 15px 0;
                padding: 10px;
                background: #f9f9f9;
                border: 1px solid #000;
                font-size: 10pt;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            /* Footer for print */
            .print-footer {
                display: block !important;
                text-align: center;
                margin-top: 30px;
                padding-top: 10px;
                border-top: 1px solid #000;
                font-size: 9pt;
                color: #666;
            }
            
            /* Force black and white for print */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            /* Avoid page breaks inside important elements */
            main {
                page-break-inside: avoid;
            }
            
            h2 {
                page-break-after: avoid;
            }
        }
        
        /* Screen-only styles */
        @media screen {
            .print-header,
            .print-footer,
            .print-summary {
                display: none;
            }
        }
        
        /* Common styles for both screen and print */
        .print-header,
        .print-footer {
            font-family: 'Times New Roman', Times, serif;
        }
        
        /* Pagination Controls Styles */
        .pagination-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .records-per-page {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .records-per-page label {
            font-weight: 600;
            color: #333;
        }
        
        .records-dropdown {
            position: relative;
            display: inline-block;
        }
        
        .records-dropdown-btn {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 8px 15px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-width: 120px;
            transition: all 0.3s ease;
        }
        
        .records-dropdown-btn:hover {
            border-color: #007bff;
        }
        
        .records-dropdown-btn i {
            margin-left: 10px;
            transition: transform 0.3s ease;
        }
        
        .records-dropdown-btn.active i {
            transform: rotate(180deg);
        }
        
        .records-dropdown-content {
            display: none;
            position: absolute;
            background: #fff;
            min-width: 120px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            border-radius: 4px;
            z-index: 1000;
            top: 100%;
            left: 0;
            margin-top: 5px;
        }
        
        .records-dropdown-content.show {
            display: block;
        }
        
        .records-dropdown-content a {
            display: block;
            padding: 10px 15px;
            text-decoration: none;
            color: #333;
            transition: all 0.2s ease;
        }
        
        .records-dropdown-content a:hover {
            background: #f8f9fa;
            color: #007bff;
        }
        
        .records-dropdown-content a.active {
            background: #007bff;
            color: white;
        }
        
        .pagination-info {
            font-size: 14px;
            color: #666;
        }
        
        .pagination-info strong {
            color: #333;
        }
    </style>
</head>

<body>

    <header>
        <a href="../views/dashboard.php">
            <button>
                <i class="fas fa-arrow-left"></i>
            </button>
        </a>
        <h1>Pending Applications</h1>
    </header>

    <!-- Print Header (only shows when printing) -->
    <div class="print-header">
        <h1>PENDING SCHOLARSHIP APPLICATIONS</h1>
        <div class="document-info">
            Scholarship Management System<br>
            Official Report
        </div>
        <div class="print-date">
            Generated on: <?php echo date('F j, Y h:i A'); ?><br>
            Page: <span class="page-number"></span>
        </div>
    </div>

    <main>
        <h2 class="no-print">List of Pending Applications</h2>
        
        <!-- Pagination Controls -->
        <div class="pagination-controls no-print">
            <div class="records-per-page">
                <label for="records-per-page">Show:</label>
                <div class="records-dropdown">
                    <button class="records-dropdown-btn" id="recordsDropdownBtn">
                        <?php echo $limit === 'all' ? 'All' : $limit; ?> records
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="records-dropdown-content" id="recordsDropdown">
                        <?php foreach ($limitOptions as $option): ?>
                            <?php 
                                $currentLimit = isset($_GET['limit']) ? $_GET['limit'] : $defaultLimit;
                                $isActive = ($option == $currentLimit) || (!isset($_GET['limit']) && $option == $defaultLimit);
                            ?>
                            <a href="?page=1&limit=<?php echo $option; ?>" 
                               class="<?php echo $isActive ? 'active' : ''; ?>">
                                <?php echo $option === 'all' ? 'All' : $option; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="pagination-info">
                Showing 
                <strong><?php 
                    if ($limit === 'all') {
                        echo "all {$totalRecords}";
                    } else {
                        $start = ($page - 1) * $sqlLimit + 1;
                        $end = min($start + $sqlLimit - 1, $totalRecords);
                        echo "{$start}-{$end}";
                    }
                ?></strong> 
                of <strong><?php echo $totalRecords; ?></strong> records
            </div>
        </div>
        
        <!-- Summary for print -->
        <div class="print-summary">
            <strong>Report Summary:</strong><br>
            Total Pending Applications: <?php echo $totalRecords; ?><br>
            <?php if ($limit === 'all'): ?>
                Displaying: All <?php echo $totalRecords; ?> records
            <?php else: ?>
                Displaying: <?php echo count($pendingApplications); ?> records (Page <?php echo $page; ?> of <?php echo $totalPages; ?>)
            <?php endif; ?>
        </div>
        
        <?php if (count($pendingApplications) > 0): ?>
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
                    <?php foreach ($pendingApplications as $application): ?>
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
            <?php if ($limit !== 'all' && $totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>&limit=<?= $limit ?>" class="prev">Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>&limit=<?= $limit ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>&limit=<?= $limit ?>" class="next">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <p class="no-data">No pending applications found.</p>
        <?php endif; ?>

        <button onclick="window.print()" class="print-button">
            <i class="fas fa-print"></i> Print Page
        </button>
    </main>

    <!-- Print Footer (only shows when printing) -->
    <div class="print-footer">
        <strong>CONFIDENTIAL</strong><br>
        This document is generated from the Scholarship Management System<br>
        For official use only | &copy; <?php echo date('Y'); ?> All rights reserved
    </div>

    <footer class="no-print">
        <p>&copy; <?= date('Y') ?> Scholarship System</p>
    </footer>

    <!-- JavaScript -->
    <script>
        // Update page numbers before printing
        window.onbeforeprint = function() {
            const pageNumElements = document.querySelectorAll('.page-number');
            pageNumElements.forEach((el, index) => {
                el.textContent = (index + 1);
            });
        };

        // Better print experience
        document.querySelector('.print-button').addEventListener('click', function(e) {
            setTimeout(() => {
                window.print();
            }, 100);
        });

        // Records per page dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownBtn = document.getElementById('recordsDropdownBtn');
            const dropdownContent = document.getElementById('recordsDropdown');
            
            if (dropdownBtn && dropdownContent) {
                // Toggle dropdown
                dropdownBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropdownContent.classList.toggle('show');
                    dropdownBtn.classList.toggle('active');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!dropdownBtn.contains(e.target) && !dropdownContent.contains(e.target)) {
                        dropdownContent.classList.remove('show');
                        dropdownBtn.classList.remove('active');
                    }
                });
                
                // Handle dropdown item clicks
                dropdownContent.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', function(e) {
                        dropdownContent.classList.remove('show');
                        dropdownBtn.classList.remove('active');
                        
                        // Update button text
                        dropdownBtn.innerHTML = this.textContent + ' <i class="fas fa-chevron-down"></i>';
                    });
                });
            }
            
            // Update URL parameters for pagination links when limit is 'all'
            const currentUrl = new URL(window.location.href);
            const currentLimit = currentUrl.searchParams.get('limit');
            
            if (currentLimit === 'all') {
                // Update all pagination links to maintain the 'all' limit
                document.querySelectorAll('.pagination a').forEach(link => {
                    const linkUrl = new URL(link.href);
                    linkUrl.searchParams.set('limit', 'all');
                    link.href = linkUrl.toString();
                });
            }
        });
    </script>

</body>

</html>