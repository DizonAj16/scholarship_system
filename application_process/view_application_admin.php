<?php
include '../includes/session.php';

// Validate application ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: applications.php");
    exit;
}

$application_id = (int) $_GET['id'];

try {
    // Fetch applicant information from all related tables
    $stmt = $pdo->prepare("
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
    ");
    $stmt->bindParam(':application_id', $application_id, PDO::PARAM_INT);
    $stmt->execute();
    $application = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$application) {
        header("Location: ../views/applications.php?error=Application not found");
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
        
        // Check if this is the new format (array of objects) or old format (array of strings)
        if (isset($filesArray[0]) && is_array($filesArray[0]) && isset($filesArray[0]['requirement_name'])) {
            // New format: array of objects with 'requirement_name' and 'file_path'
            $uploadedFiles = $filesArray;
        } else {
            // Old format: array of file paths
            // Convert to new format for consistency
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
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            font-family: Arial, sans-serif;
        }

        .top-buttons {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .action-button {
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .action-button:hover {
            background: #45a049;
        }

        .print-button {
            background: #2196F3;
        }

        .print-button:hover {
            background: #1976D2;
        }

        .track-button {
            background: #FF9800;
        }

        .track-button:hover {
            background: #F57C00;
        }

        h1 {
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        h2 {
            color: #555;
            margin-top: 30px;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }

        .submitted-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #4CAF50;
        }

        .section {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .details p {
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .details p:last-child {
            border-bottom: none;
        }

        .details strong {
            color: #333;
            min-width: 180px;
            display: inline-block;
        }

        .details i {
            width: 20px;
            margin-right: 10px;
            color: #4CAF50;
        }

        .attachments {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-top: 15px;
        }

        .file-card {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            max-width: 300px;
            background: #f9f9f9;
            transition: transform 0.3s ease;
        }

        .file-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .file-card embed,
        .file-card img {
            display: block;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            max-width: 100%;
            height: 200px;
            object-fit: contain;
        }

        .file-info {
            margin-top: 10px;
        }

        .requirement-name {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .file-name {
            font-size: 12px;
            color: #666;
            word-break: break-all;
        }

        .btn-back {
            display: inline-block;
            padding: 10px 20px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            transition: background 0.3s ease;
        }

        .btn-back:hover {
            background: #5a6268;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }

        .status-pending {
            background: #ffc107;
            color: #212529;
        }

        .status-approved {
            background: #28a745;
            color: white;
        }

        .status-not-qualified {
            background: #dc3545;
            color: white;
        }

        .status-under-review {
            background: #17a2b8;
            color: white;
        }

        .status-cancelled {
            background: #6c757d;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #ddd;
        }

        @media print {
            .top-buttons,
            .btn-back {
                display: none;
            }
            
            .section {
                box-shadow: none;
                border: 1px solid #ddd;
                page-break-inside: avoid;
            }
            
            .file-card {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>

    <div class="preloader">
        <img src="../assets/images/icons/scholarship_seal.png" alt="Scholarship Seal" style="height: 70px; width: 70px;">
        <div class="lds-facebook">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>

    <div class="container">
        <div class="top-buttons">
            <button onclick="window.print()" class="action-button print-button">
                <i class="fas fa-print"></i> Print Page
            </button>
            
            <?php if ($application['status'] === 'pending' || $application['status'] === 'under review'): ?>
            <button onclick="location.href='application_tracking.php?id=<?= $application_id ?>'"
                class="action-button track-button">
                <i class="fas fa-truck"></i> Track Application Status
            </button>
            <?php endif; ?>
        </div>

        <h1>Scholarship Application Details</h1>

        <div class="submitted-info">
            <p><strong>Application ID:</strong> <?= htmlspecialchars($application['application_id']) ?></p>
            <p><strong>Submitted by:</strong> <?= htmlspecialchars($application['username']) ?></p>
            <p><strong>Application Date:</strong> <?= htmlspecialchars($application['date']) ?></p>
            <p><strong>Status:</strong> 
                <span class="status-badge status-<?= htmlspecialchars($application['status']) ?>">
                    <?= htmlspecialchars(strtoupper($application['status'])) ?>
                </span>
            </p>
        </div>

        <!-- Scholarship Application Information -->
        <div class="section">
            <h2>Scholarship Application Information</h2>
            <div class="details">
                <p><i class="fas fa-calendar-alt"></i><strong>Semester:</strong> <?= htmlspecialchars($application['semester']) ?></p>
                <p><i class="fas fa-calendar"></i><strong>School Year:</strong> <?= htmlspecialchars($application['school_year']) ?></p>
                <p><i class="fas fa-graduation-cap"></i><strong>Scholarship Grant:</strong> <?= htmlspecialchars($application['scholarship_grant']) ?></p>
                <p><i class="fas fa-book"></i><strong>Course:</strong> <?= htmlspecialchars($application['course']) ?></p>
                <p><i class="fas fa-graduation-cap"></i><strong>Major:</strong> <?= htmlspecialchars($application['major']) ?></p>
                <p><i class="fas fa-users"></i><strong>Year/Section:</strong> <?= htmlspecialchars($application['yr_sec']) ?></p>
                <p><i class="fas fa-pray"></i><strong>Reason for Scholarship:</strong><br>
                    <?= nl2br(htmlspecialchars($application['reason_scholarship'])) ?>
                </p>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="section">
            <h2>Personal Information</h2>
            <div class="details">
                <p><i class="fas fa-user"></i><strong>Full Name:</strong> <?= htmlspecialchars($application['full_name']) ?></p>
                <p><i class="fas fa-envelope"></i><strong>Email:</strong> <?= htmlspecialchars($application['email']) ?></p>
                <p><i class="fas fa-phone"></i><strong>Phone:</strong> <?= htmlspecialchars($application['cell_no']) ?></p>
                <p><i class="fas fa-venus-mars"></i><strong>Sex:</strong> <?= htmlspecialchars(ucfirst($application['sex'])) ?></p>
                <p><i class="fas fa-calendar-alt"></i><strong>Date of Birth:</strong> <?= htmlspecialchars($application['date_of_birth']) ?></p>
                <p><i class="fas fa-birthday-cake"></i><strong>Age:</strong> <?= htmlspecialchars($application['age']) ?></p>
                <p><i class="fas fa-map-marker-alt"></i><strong>Place of Birth:</strong> <?= htmlspecialchars($application['place_of_birth']) ?></p>
                <p><i class="fas fa-heart"></i><strong>Civil Status:</strong> <?= htmlspecialchars(ucfirst($application['civil_status'])) ?></p>
                <p><i class="fas fa-church"></i><strong>Religion:</strong> <?= htmlspecialchars($application['religion']) ?></p>
                <p><i class="fas fa-wheelchair"></i><strong>Disability:</strong> <?= htmlspecialchars($application['disability']) ?></p>
                <p><i class="fas fa-users"></i><strong>Indigenous Group:</strong> <?= htmlspecialchars($application['indigenous_group']) ?></p>
                <p><i class="fas fa-home"></i><strong>Permanent Address:</strong> <?= htmlspecialchars($application['permanent_address']) ?></p>
                <p><i class="fas fa-house-user"></i><strong>Present Address:</strong> <?= htmlspecialchars($application['present_address']) ?></p>
                <p><i class="fas fa-map-pin"></i><strong>ZIP Code:</strong> <?= htmlspecialchars($application['zip_code']) ?></p>
            </div>
        </div>

        <!-- Educational Background (from schools_attended) -->
        <div class="section">
            <h2>Educational Background</h2>
            <div class="details">
                <h3 style="color: #666; margin-top: 10px; margin-bottom: 10px;">Elementary School</h3>
                <p><i class="fas fa-school"></i><strong>School Name:</strong> <?= htmlspecialchars($application['elementary']) ?></p>
                <p><i class="fas fa-calendar"></i><strong>Year Graduated:</strong> <?= htmlspecialchars($application['elementary_year_grad']) ?></p>
                <p><i class="fas fa-award"></i><strong>Honors Received:</strong> <?= htmlspecialchars($application['elementary_honors']) ?></p>
                
                <h3 style="color: #666; margin-top: 15px; margin-bottom: 10px;">Secondary School</h3>
                <p><i class="fas fa-school"></i><strong>School Name:</strong> <?= htmlspecialchars($application['secondary']) ?></p>
                <p><i class="fas fa-calendar"></i><strong>Year Graduated:</strong> <?= htmlspecialchars($application['secondary_year_grad']) ?></p>
                <p><i class="fas fa-award"></i><strong>Honors Received:</strong> <?= htmlspecialchars($application['secondary_honors']) ?></p>
                
                <h3 style="color: #666; margin-top: 15px; margin-bottom: 10px;">College/University</h3>
                <p><i class="fas fa-university"></i><strong>School Name:</strong> <?= htmlspecialchars($application['college'] ?: 'Not Applicable') ?></p>
                <p><i class="fas fa-calendar"></i><strong>Year Graduated:</strong> <?= htmlspecialchars($application['college_year_grad'] ?: 'Not Applicable') ?></p>
                <p><i class="fas fa-award"></i><strong>Honors Received:</strong> <?= htmlspecialchars($application['college_honors'] ?: 'Not Applicable') ?></p>
            </div>
        </div>

        <!-- Family Information (from parents_info) -->
        <div class="section">
            <h2>Family Information</h2>
            <div class="details">
                <h3 style="color: #666; margin-top: 10px; margin-bottom: 10px;">Father's Information</h3>
                <p><i class="fas fa-male"></i><strong>Full Name:</strong> 
                    <?= htmlspecialchars(trim($application['father_givenname'] . ' ' . ($application['father_middlename'] ? $application['father_middlename'] . ' ' : '') . $application['father_lastname'])) ?>
                </p>
                <p><i class="fas fa-phone"></i><strong>Phone:</strong> <?= htmlspecialchars($application['father_cellphone']) ?></p>
                <p><i class="fas fa-graduation-cap"></i><strong>Education:</strong> <?= htmlspecialchars($application['father_education']) ?></p>
                <p><i class="fas fa-briefcase"></i><strong>Occupation:</strong> <?= htmlspecialchars($application['father_occupation']) ?></p>
                <p><i class="fas fa-money-bill-wave"></i><strong>Monthly Income:</strong> ₱<?= number_format(floatval($application['father_income']), 2) ?></p>
                
                <h3 style="color: #666; margin-top: 15px; margin-bottom: 10px;">Mother's Information</h3>
                <p><i class="fas fa-female"></i><strong>Full Name:</strong> 
                    <?= htmlspecialchars(trim($application['mother_givenname'] . ' ' . ($application['mother_middlename'] ? $application['mother_middlename'] . ' ' : '') . $application['mother_lastname'])) ?>
                </p>
                <p><i class="fas fa-phone"></i><strong>Phone:</strong> <?= htmlspecialchars($application['mother_cellphone']) ?></p>
                <p><i class="fas fa-graduation-cap"></i><strong>Education:</strong> <?= htmlspecialchars($application['mother_education']) ?></p>
                <p><i class="fas fa-briefcase"></i><strong>Occupation:</strong> <?= htmlspecialchars($application['mother_occupation']) ?></p>
                <p><i class="fas fa-money-bill-wave"></i><strong>Monthly Income:</strong> ₱<?= number_format(floatval($application['mother_income']), 2) ?></p>
            </div>
        </div>

        <!-- Housing Information (from house_info) -->
        <div class="section">
            <h2>Housing Information</h2>
            <div class="details">
                <p><i class="fas fa-home"></i><strong>House Status:</strong> <?= htmlspecialchars(ucfirst($application['house_status'])) ?></p>
            </div>
        </div>

        <!-- Attached Files -->
        <div class="section">
            <h2>Attached Documents</h2>
            <?php if (!empty($uploadedFiles)): ?>
                <div class="attachments">
                    <?php foreach ($uploadedFiles as $file):
                        // Handle both new and old formats
                        if (is_array($file) && isset($file['file_path'])) {
                            $filePath = $file['file_path'];
                            $requirementName = isset($file['requirement_name']) ? $file['requirement_name'] : 'Document';
                            $displayFileName = basename($filePath);
                        } else {
                            // Handle old format (string)
                            $filePath = $file;
                            $requirementName = 'Document';
                            $displayFileName = basename($file);
                        }
                        
                        $fullFilePath = "../uploads/" . $filePath;
                        
                        // Check if file exists
                        if (!file_exists($fullFilePath)) {
                            continue;
                        }
                        
                        $ext = strtolower(pathinfo($displayFileName, PATHINFO_EXTENSION));
                        ?>
                        <div class="file-card">
                            <div class="file-info">
                                <div class="requirement-name"><?= htmlspecialchars($requirementName) ?></div>
                            </div>
                            <?php if ($ext === "pdf"): ?>
                                <embed src="<?= htmlspecialchars($fullFilePath) ?>" type="application/pdf" width="250" height="200" />
                            <?php elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                <img src="<?= htmlspecialchars($fullFilePath) ?>" width="250" height="200" alt="<?= htmlspecialchars($requirementName) ?>" 
                                     style="border:1px solid #ccc; border-radius:5px; object-fit: contain;">
                            <?php else: ?>
                                <div style="width: 250px; height: 200px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 5px;">
                                    <i class="fas fa-file" style="font-size: 48px; color: #666;"></i>
                                </div>
                            <?php endif; ?>
                            <p style="text-align: center; margin-top: 10px;">
                                <a href="<?= htmlspecialchars($fullFilePath) ?>" download style="text-decoration: none; color: #2196F3;">
                                    <i class="fas fa-download"></i> Download
                                </a><br>
                                <small class="file-name"><?= htmlspecialchars($displayFileName) ?></small>
                            </p>
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

        <a href="../views/applications.php" class="btn-back">
            <i class="fas fa-chevron-left"></i> Back to Applications
        </a>
    </div>

    <script>
        // Hide preloader when page loads
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.querySelector('.preloader').style.display = 'none';
            }, 1000);
        });
        
        // Handle PDF embed errors
        document.addEventListener('DOMContentLoaded', function() {
            const embeds = document.querySelectorAll('embed[type="application/pdf"]');
            embeds.forEach(embed => {
                embed.addEventListener('error', function() {
                    const parent = this.parentElement;
                    if (parent) {
                        parent.innerHTML = `
                            <div class="file-info">
                                <div class="requirement-name">${parent.querySelector('.requirement-name')?.textContent || 'Document'}</div>
                            </div>
                            <div style="width: 250px; height: 200px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; flex-direction: column; border-radius: 5px;">
                                <i class="fas fa-file-pdf" style="font-size: 48px; color: #d32f2f;"></i>
                                <p style="margin-top: 10px; font-size: 12px; color: #666;">PDF cannot be displayed</p>
                            </div>
                            ${parent.querySelector('p') ? parent.querySelector('p').outerHTML : ''}
                        `;
                    }
                });
            });
        });
    </script>
</body>
</html>