<?php
include '../includes/session.php';

// Validate application ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: my_applications.php");
    exit;
}

$application_id = (int) $_GET['id'];
$is_admin = $_SESSION["role"] === 'admin';

try {
    // Build query based on user role
    if ($is_admin) {
        // Admin can view any application
        $query = "
            SELECT 
                sa.*, 
                st.elementary, st.elementary_year_grad, st.elementary_honors, 
                st.secondary, st.secondary_year_grad, st.secondary_honors, 
                st.college, st.college_year_grad, st.college_honors, 
                pi.father_lastname, pi.father_givenname, pi.father_middlename, pi.father_cellphone, 
                pi.father_education, pi.father_occupation, pi.father_income, 
                pi.mother_lastname, pi.mother_givenname, pi.mother_middlename, pi.mother_cellphone, 
                pi.mother_education, pi.mother_occupation, pi.mother_income, 
                hi.house_status, 
                u.username,
                sa.reason_scholarship,
                sa.date_of_birth,
                sa.age,
                sa.place_of_birth,
                sa.civil_status,
                sa.religion,
                sa.scholarship_grant,
                sa.disability,
                sa.indigenous_group,
                sa.cell_no,
                sa.email,
                sa.sex,
                sa.permanent_address,
                sa.present_address,
                sa.zip_code,
                sa.course,
                sa.major,
                sa.yr_sec,
                sa.semester,
                sa.school_year,
                sa.date,
                sa.status
            FROM scholarship_applications sa
            LEFT JOIN schools_attended st ON sa.application_id = st.application_id
            LEFT JOIN parents_info pi ON sa.application_id = pi.application_id
            LEFT JOIN house_info hi ON sa.application_id = hi.application_id
            LEFT JOIN users u ON sa.user_id = u.id
            WHERE sa.application_id = :application_id
        ";
        $params = [':application_id' => $application_id];
    } else {
        // Student can only view their own applications
        $query = "
            SELECT 
                sa.*, 
                st.elementary, st.elementary_year_grad, st.elementary_honors, 
                st.secondary, st.secondary_year_grad, st.secondary_honors, 
                st.college, st.college_year_grad, st.college_honors, 
                pi.father_lastname, pi.father_givenname, pi.father_middlename, pi.father_cellphone, 
                pi.father_education, pi.father_occupation, pi.father_income, 
                pi.mother_lastname, pi.mother_givenname, pi.mother_middlename, pi.mother_cellphone, 
                pi.mother_education, pi.mother_occupation, pi.mother_income, 
                hi.house_status, 
                u.username,
                sa.reason_scholarship,
                sa.date_of_birth,
                sa.age,
                sa.place_of_birth,
                sa.civil_status,
                sa.religion,
                sa.scholarship_grant,
                sa.disability,
                sa.indigenous_group,
                sa.cell_no,
                sa.email,
                sa.sex,
                sa.permanent_address,
                sa.present_address,
                sa.zip_code,
                sa.course,
                sa.major,
                sa.yr_sec,
                sa.semester,
                sa.school_year,
                sa.date,
                sa.status
            FROM scholarship_applications sa
            LEFT JOIN schools_attended st ON sa.application_id = st.application_id
            LEFT JOIN parents_info pi ON sa.application_id = pi.application_id
            LEFT JOIN house_info hi ON sa.application_id = hi.application_id
            LEFT JOIN users u ON sa.user_id = u.id
            WHERE sa.application_id = :application_id AND sa.user_id = :user_id
        ";
        $params = [':application_id' => $application_id, ':user_id' => $_SESSION['id']];
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $application = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$application) {
        header("Location: " . ($is_admin ? "../views/applications.php" : "../views/my_applications.php") . "?error=Application not found");
        exit;
    }

    // Fetch uploaded files
    $stmtFiles = $pdo->prepare("SELECT files FROM scholarship_files WHERE application_id = :application_id");
    $stmtFiles->bindParam(':application_id', $application_id, PDO::PARAM_INT);
    $stmtFiles->execute();
    $fileData = $stmtFiles->fetch(PDO::FETCH_ASSOC);

    $uploadedFiles = [];
    if ($fileData && !empty($fileData['files'])) {
        $filesArray = json_decode($fileData['files'], true);

        if (isset($filesArray[0]) && is_array($filesArray[0]) && isset($filesArray[0]['requirement_name'])) {
            $uploadedFiles = $filesArray;
        } else {
            foreach ($filesArray as $filePath) {
                $uploadedFiles[] = [
                    'requirement_name' => 'Document',
                    'file_path' => $filePath
                ];
            }
        }
    }

} catch (PDOException $e) {
    die("Error fetching application details: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Application</title>

    <link rel="stylesheet" href="../css/view_applications.css?v=<?php echo time(); ?>">
    <script src="../js/fontawesome.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/preloader.css?v=<?php echo time(); ?>">
    <script src="../js/preloader.js?v=<?php echo time(); ?>"></script>
    <style>

    </style>
</head>

<body>

    <div class="preloader">
        <img src="../assets/images/icons/scholarship_seal.png" alt="Scholarship Seal"
            style="height: 70px; width: 70px;">
        <div class="lds-facebook">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>

    <!-- Tracking Modal -->
    <div id="trackingModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <div class="tracking-container">
                <h1>Scholarship Application Tracking</h1>

                <p class="info">
                    Application ID: <strong><?= $application_id ?></strong><br>
                    Applicant: <strong><?= htmlspecialchars($application['full_name']) ?></strong><br>
                    Submitted by: <strong><?= htmlspecialchars($application['username']) ?></strong><br>
                    Date: <strong><?= htmlspecialchars($application['date']) ?></strong><br>
                    Current Status: <strong><?= htmlspecialchars(ucfirst($application['status'])) ?></strong>
                </p>

                <div class="tracker">
                    <!-- STEP 1: SUBMITTED -->
                    <div
                        class="step 
                        <?= in_array($application['status'], ['submitted', 'pending', 'approved', 'not qualified']) ? 'active' : '' ?>">
                        <div class="circle">1</div>
                        <p>Submitted</p>
                    </div>

                    <div class="line <?= $application['status'] != 'submitted' ? 'active' : '' ?>"></div>

                    <!-- STEP 2: REVIEWING -->
                    <div
                        class="step 
                        <?= in_array($application['status'], ['pending', 'approved', 'not qualified']) ? 'active' : '' ?>">
                        <div class="circle">2</div>
                        <p>Reviewing</p>
                    </div>

                    <div
                        class="line <?= in_array($application['status'], ['approved', 'not qualified']) ? 'active' : '' ?>">
                    </div>

                    <!-- STEP 3: DECISION -->
                    <div
                        class="step 
                        <?= $application['status'] == 'approved' ? 'approved' : ($application['status'] == 'not qualified' ? 'not-qualified' : '') ?>">
                        <div class="circle">3</div>
                        <p>
                            <?php
                            if ($application['status'] == 'approved')
                                echo "Approved";
                            elseif ($application['status'] == 'not qualified')
                                echo "Not Qualified";
                            else
                                echo "Decision Pending";
                            ?>
                        </p>
                    </div>
                </div>

                <div class="modal-actions">
                    <button onclick="closeModal()" class="action-button back-btn">
                        Close Tracking
                    </button>
                    <a href="view_application.php?id=<?= $application_id ?>&print=true" target="_blank"
                        class="action-button print-button">
                        <i class="fas fa-print"></i> Print Status
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Header with Back Button at Top -->
        <div class="header-actions">
            <div class="back-nav">
                <a href="<?= $is_admin ? '../views/applications.php' : '../views/my_applications.php' ?>"
                    class="back-btn">
                    <i class="fas fa-chevron-left"></i> Back to Applications
                </a>
            </div>
            <div class="top-buttons">
                <button onclick="window.print()" class="action-button print-button">
                    <i class="fas fa-print"></i> Print Page
                </button>

                <button onclick="openTrackingModal()" class="action-button track-button">
                    <i class="fas fa-truck"></i> Track Application Status
                </button>

                <?php if ($is_admin): ?>
                    <a href="../application_process/edit_application_admin.php?id=<?= $application_id ?>"
                        class="action-button">
                        <i class="fas fa-edit"></i> Edit Application
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <h1>Scholarship Application Details</h1>

        <div class="submitted-info">
            <p><strong>Application ID:</strong> <?= htmlspecialchars($application['application_id']) ?></p>
            <p><strong>Submitted by:</strong> <?= htmlspecialchars($application['username']) ?></p>
            <p><strong>Application Date:</strong> <?= htmlspecialchars($application['date']) ?></p>

        </div>

        <!-- Scholarship Application Information -->
        <div class="section">
            <h2>Scholarship Application Information</h2>
            <div class="details">
                <p><i class="fas fa-calendar-alt"></i><strong>Semester:</strong>
                    <?= htmlspecialchars($application['semester']) ?></p>
                <p><i class="fas fa-calendar"></i><strong>School Year:</strong>
                    <?= htmlspecialchars($application['school_year']) ?></p>
                <p><i class="fas fa-graduation-cap"></i><strong>Scholarship Grant:</strong>
                    <?= htmlspecialchars($application['scholarship_grant']) ?></p>
                <p><i class="fas fa-book"></i><strong>Course:</strong>
                    <?= htmlspecialchars($application['course']) ?>
                </p>
                <p><i class="fas fa-graduation-cap"></i><strong>Major:</strong>
                    <?= htmlspecialchars($application['major']) ?></p>
                <p><i class="fas fa-users"></i><strong>Year/Section:</strong>
                    <?= htmlspecialchars($application['yr_sec']) ?></p>
                <p><i class="fas fa-pray"></i><strong>Reason for Scholarship:</strong><br>
                    <?= nl2br(htmlspecialchars($application['reason_scholarship'])) ?>
                </p>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="section">
            <h2>Personal Information</h2>
            <div class="details">
                <p><i class="fas fa-user"></i><strong>Full Name:</strong>
                    <?= htmlspecialchars($application['full_name']) ?></p>
                <p><i class="fas fa-envelope"></i><strong>Email Address:</strong>
                    <?= htmlspecialchars($application['email']) ?></p>
                <p><i class="fas fa-phone"></i><strong>Phone Number:</strong>
                    <?= htmlspecialchars($application['cell_no']) ?></p>
                <p><i class="fas fa-venus-mars"></i><strong>Sex:</strong>
                    <?= htmlspecialchars(ucfirst($application['sex'])) ?></p>
                <p><i class="fas fa-calendar-alt"></i><strong>Date of Birth:</strong>
                    <?= htmlspecialchars($application['date_of_birth']) ?></p>
                <p><i class="fas fa-birthday-cake"></i><strong>Age:</strong>
                    <?= htmlspecialchars($application['age']) ?></p>
                <p><i class="fas fa-map-marker-alt"></i><strong>Place of Birth:</strong>
                    <?= htmlspecialchars($application['place_of_birth']) ?></p>
                <p><i class="fas fa-heart"></i><strong>Civil Status:</strong>
                    <?= htmlspecialchars(ucfirst($application['civil_status'])) ?></p>
                <p><i class="fas fa-church"></i><strong>Religion:</strong>
                    <?= htmlspecialchars($application['religion']) ?></p>
                <p><i class="fas fa-wheelchair"></i><strong>Disability:</strong>
                    <?= htmlspecialchars($application['disability']) ?></p>
                <p><i class="fas fa-users"></i><strong>Indigenous Group:</strong>
                    <?= htmlspecialchars($application['indigenous_group']) ?></p>
                <p><i class="fas fa-home"></i><strong>Permanent Address:</strong>
                    <?= htmlspecialchars($application['permanent_address']) ?></p>
                <p><i class="fas fa-house-user"></i><strong>Present Address:</strong>
                    <?= htmlspecialchars($application['present_address']) ?></p>
                <p><i class="fas fa-map-pin"></i><strong>ZIP Code:</strong>
                    <?= htmlspecialchars($application['zip_code']) ?></p>
            </div>
        </div>

        <!-- Educational Background -->
        <div class="section">
            <h2>Educational Background</h2>
            <div class="details">
                <h3 style="color: #666; margin-top: 10px; margin-bottom: 10px;">Elementary School</h3>
                <p><i class="fas fa-school"></i><strong>School Name:</strong>
                    <?= htmlspecialchars($application['elementary']) ?></p>
                <p><i class="fas fa-calendar"></i><strong>Year Graduated:</strong>
                    <?= htmlspecialchars($application['elementary_year_grad']) ?></p>
                <p><i class="fas fa-award"></i><strong>Honors Received:</strong>
                    <?= htmlspecialchars($application['elementary_honors']) ?></p>

                <h3 style="color: #666; margin-top: 15px; margin-bottom: 10px;">Secondary School</h3>
                <p><i class="fas fa-school"></i><strong>School Name:</strong>
                    <?= htmlspecialchars($application['secondary']) ?></p>
                <p><i class="fas fa-calendar"></i><strong>Year Graduated:</strong>
                    <?= htmlspecialchars($application['secondary_year_grad']) ?></p>
                <p><i class="fas fa-award"></i><strong>Honors Received:</strong>
                    <?= htmlspecialchars($application['secondary_honors']) ?></p>

                <h3 style="color: #666; margin-top: 15px; margin-bottom: 10px;">College/University</h3>
                <p><i class="fas fa-university"></i><strong>School Name:</strong>
                    <?= htmlspecialchars($application['college'] ?: 'Not Applicable') ?></p>
                <p><i class="fas fa-calendar"></i><strong>Year Graduated:</strong>
                    <?= htmlspecialchars($application['college_year_grad'] ?: 'Not Applicable') ?></p>
                <p><i class="fas fa-award"></i><strong>Honors Received:</strong>
                    <?= htmlspecialchars($application['college_honors'] ?: 'Not Applicable') ?></p>
            </div>
        </div>

        <!-- Family Information -->
        <div class="section">
            <h2>Family Information</h2>
            <div class="details">
                <h3 style="color: #666; margin-top: 10px; margin-bottom: 10px;">Father's Information</h3>
                <p><i class="fas fa-male"></i><strong>Full Name:</strong>
                    <?= htmlspecialchars(trim($application['father_givenname'] . ' ' . ($application['father_middlename'] ? $application['father_middlename'] . ' ' : '') . $application['father_lastname'])) ?>
                </p>
                <p><i class="fas fa-phone"></i><strong>Phone Number:</strong>
                    <?= htmlspecialchars($application['father_cellphone']) ?></p>
                <p><i class="fas fa-graduation-cap"></i><strong>Educational Attainment:</strong>
                    <?= htmlspecialchars($application['father_education']) ?></p>
                <p><i class="fas fa-briefcase"></i><strong>Occupation:</strong>
                    <?= htmlspecialchars($application['father_occupation']) ?></p>
                <p><i class="fas fa-money-bill-wave"></i><strong>Monthly Income:</strong>
                    <?php
                    $father_income_display = $application['father_income'];
                    // If it's a range (contains -) or has peso sign, display as is
                    if (strpos($father_income_display, '-') !== false) {
                        // It's a range like "20000-25000" or "₱20000-25000"
                        if (strpos($father_income_display, '₱') === false) {
                            // Add peso sign if not present
                            $father_income_display = '₱' . $father_income_display;
                        }
                        echo htmlspecialchars($father_income_display);
                    } elseif (is_numeric($father_income_display)) {
                        // It's a specific number
                        echo '₱' . number_format(floatval($father_income_display), 2);
                    } else {
                        // It's text like "Not Applicable" or "Prefer not to say"
                        echo htmlspecialchars($father_income_display);
                    }
                    ?>
                </p>

                <h3 style="color: #666; margin-top: 15px; margin-bottom: 10px;">Mother's Information</h3>
                <p><i class="fas fa-female"></i><strong>Full Name:</strong>
                    <?= htmlspecialchars(trim($application['mother_givenname'] . ' ' . ($application['mother_middlename'] ? $application['mother_middlename'] . ' ' : '') . $application['mother_lastname'])) ?>
                </p>
                <p><i class="fas fa-phone"></i><strong>Phone Number:</strong>
                    <?= htmlspecialchars($application['mother_cellphone']) ?></p>
                <p><i class="fas fa-graduation-cap"></i><strong>Educational Attainment:</strong>
                    <?= htmlspecialchars($application['mother_education']) ?></p>
                <p><i class="fas fa-briefcase"></i><strong>Occupation:</strong>
                    <?= htmlspecialchars($application['mother_occupation']) ?></p>
                <p><i class="fas fa-money-bill-wave"></i><strong>Monthly Income:</strong>
                    <?php
                    $mother_income_display = $application['mother_income'];
                    // If it's a range (contains -) or has peso sign, display as is
                    if (strpos($mother_income_display, '-') !== false) {
                        // It's a range like "30000-35000" or "₱30000-35000"
                        if (strpos($mother_income_display, '₱') === false) {
                            // Add peso sign if not present
                            $mother_income_display = '₱' . $mother_income_display;
                        }
                        echo htmlspecialchars($mother_income_display);
                    } elseif (is_numeric($mother_income_display)) {
                        // It's a specific number
                        echo '₱' . number_format(floatval($mother_income_display), 2);
                    } else {
                        // It's text like "Not Applicable" or "Prefer not to say"
                        echo htmlspecialchars($mother_income_display);
                    }
                    ?>
                </p>
            </div>
        </div>

        <!-- Housing Information -->
        <div class="section">
            <h2>Housing Information</h2>
            <div class="details">
                <p><i class="fas fa-home"></i><strong>House Status:</strong>
                    <?= htmlspecialchars(ucfirst($application['house_status'])) ?></p>
            </div>
        </div>

        <!-- Attached Files -->
        <div class="section">
            <h2>Attached Documents</h2>
            <?php if (!empty($uploadedFiles)): ?>
                <div class="attachments">
                    <?php foreach ($uploadedFiles as $file):
                        if (is_array($file) && isset($file['file_path'])) {
                            $filePath = $file['file_path'];
                            $requirementName = isset($file['requirement_name']) ? $file['requirement_name'] : 'Document';
                            $displayFileName = basename($filePath);
                        } else {
                            $filePath = $file;
                            $requirementName = 'Document';
                            $displayFileName = basename($file);
                        }

                        $fullFilePath = "../uploads/" . $filePath;

                        if (!file_exists($fullFilePath)) {
                            continue;
                        }

                        $ext = strtolower(pathinfo($displayFileName, PATHINFO_EXTENSION));
                        ?>
                        <div class="file-card">
                            <?php if ($ext === "pdf"): ?>
                                <embed src="<?= htmlspecialchars($fullFilePath) ?>" type="application/pdf" width="250"
                                    height="200" />
                            <?php elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                <img src="<?= htmlspecialchars($fullFilePath) ?>" width="250" height="200"
                                    alt="<?= htmlspecialchars($requirementName) ?>"
                                    style="border:1px solid #ccc; border-radius:5px; object-fit: contain;">
                            <?php else: ?>
                                <div
                                    style="width: 250px; height: 200px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 5px;">
                                    <i class="fas fa-file" style="font-size: 48px; color: #666;"></i>
                                </div>
                            <?php endif; ?>
                            <div class="file-info">
                                <div class="requirement-name"><?= htmlspecialchars($requirementName) ?></div>
                                <p style="text-align: center; margin-top: 10px;">
                                    <a href="<?= htmlspecialchars($fullFilePath) ?>" download style="text-decoration: none;">
                                        <i class="fas fa-download"></i> Download
                                    </a><br>
                                    <small class="file-name"><?= htmlspecialchars($displayFileName) ?></small>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-file"></i>
                    <p>No files attached to this application.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Hide preloader when page loads
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                const preloader = document.querySelector('.preloader');
                if (preloader) {
                    preloader.style.display = 'none';
                }
            }, 1000);

            // Initialize mobile-friendly features
            initMobileFeatures();
        });

        function initMobileFeatures() {
            // Better touch handling for modals
            const closeBtn = document.querySelector('.close-modal');
            if (closeBtn) {
                closeBtn.addEventListener('touchend', function (e) {
                    e.preventDefault();
                    closeModal();
                });
            }

            // Prevent zoom on input focus for iOS
            const inputs = document.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('focus', function () {
                    this.style.fontSize = '16px'; // Prevents iOS zoom
                });
            });
        }

        // Modal Functions
        function openTrackingModal() {
            const modal = document.getElementById('trackingModal');
            if (modal) {
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
            }
        }

        function closeModal() {
            const modal = document.getElementById('trackingModal');
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto'; // Restore scrolling
            }
        }

        // Close modal when clicking outside
        window.addEventListener('click', function (event) {
            const modal = document.getElementById('trackingModal');
            if (event.target == modal) {
                closeModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });

        // Handle PDF embed errors
        document.addEventListener('DOMContentLoaded', function () {
            const embeds = document.querySelectorAll('embed[type="application/pdf"]');
            embeds.forEach(embed => {
                embed.addEventListener('error', function () {
                    const parent = this.parentElement;
                    if (parent) {
                        parent.innerHTML = `
                        <div style="width: 100%; height: 180px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; flex-direction: column; border-radius: 5px;">
                            <i class="fas fa-file-pdf" style="font-size: 36px; color: #d32f2f;"></i>
                            <p style="margin-top: 8px; font-size: 12px; color: #666;">PDF cannot be displayed</p>
                        </div>
                        ${parent.innerHTML}
                    `;
                    }
                });
            });

            // Make file cards more touch-friendly
            const fileCards = document.querySelectorAll('.file-card');
            fileCards.forEach(card => {
                card.style.cursor = 'pointer';
                card.addEventListener('touchend', function (e) {
                    const link = this.querySelector('a');
                    if (link && !e.target.closest('a')) {
                        e.preventDefault();
                        link.click();
                    }
                });
            });
        });

        // Auto-open modal if URL has tracking parameter
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('tracking')) {
                setTimeout(openTrackingModal, 500);
            }
        });

        // Handle orientation change
        window.addEventListener('orientationchange', function () {
            // Close modal on orientation change for better UX
            closeModal();

            // Force repaint for iOS
            setTimeout(function () {
                window.scrollTo(0, 0);
            }, 100);
        });
    </script>
</body>

</html>