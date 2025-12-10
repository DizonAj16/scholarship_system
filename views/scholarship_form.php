<?php
include '../includes/session.php';

// Fetch Semester + School Year
$sem_sy_list = $pdo->query("SELECT * FROM dropdown_sem_sy ORDER BY school_year DESC, semester ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch Course + Major
$course_major_list = $pdo->query("SELECT * FROM dropdown_course_major ORDER BY course ASC, major ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch Scholarship Grants
$scholarship_grant_list = $pdo->query("SELECT * FROM dropdown_scholarship_grant ORDER BY grant_name ASC")->fetchAll(PDO::FETCH_ASSOC);

$current_sem = $application['semester'] ?? '';
$current_sy = $application['school_year'] ?? '';
$current_course = $application['course'] ?? '';
$current_major = $application['major'] ?? '';
$current_scholarship_grant = $application['scholarship_grant'] ?? '';

// Check if user already has an application for the selected grant (for non-admin users)
$user_id = $_SESSION['id'];
$user_role = $_SESSION['role'];

// Get user's existing applications
$existing_applications = [];
if ($user_role !== 'admin') {
    $stmt = $pdo->prepare("SELECT scholarship_grant FROM scholarship_applications WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $existing_applications = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
}

// process_form.php - Processing form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $semester_sy = explode('|', $_POST['semester_sy']);
    $course_major = explode('|', $_POST['course_major']);

    $selected_grant = $_POST['scholarship_grant'];

    // Check if non-admin user already has an application for this grant
    if ($user_role !== 'admin' && in_array($selected_grant, $existing_applications)) {
        $_SESSION['error_message'] = 'You have already submitted an application for the "' . htmlspecialchars($selected_grant) . '" scholarship grant. Each user can only submit one application per grant.';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Collect and sanitize form data
    $data = [
        'date' => $_POST['date'],
        'semester' => $semester_sy[0] ?? '', // First part before |
        'school_year' => $semester_sy[1] ?? '', // Second part after |
        'full_name' => $_POST['fullName'],
        'course' => $course_major[0] ?? '', // First part before |
        'major' => $course_major[1] ?? '', // Second part after |
        'yr_sec' => $_POST['yr_sec'],
        'cell_no' => preg_replace('/[^0-9]/', '', $_POST['cellNo']), // Remove non-numeric characters
        'present_address' => $_POST['pres_address'],
        'permanent_address' => $_POST['perma_address'],
        'zip_code' => $_POST['zip_code'],
        'email' => $_POST['email'],
        'sex' => $_POST['sex'],
        'date_of_birth' => $_POST['date_of_birth'],
        'age' => $_POST['age'],
        'place_of_birth' => $_POST['place_of_birth'],
        'civil_status' => $_POST['civil_status'],
        'religion' => $_POST['religion'],
        'scholarship_grant' => $selected_grant,
        'disability' => $_POST['disability'],
        'indigenous_group' => $_POST['indigenous_group'],
        'elementary' => $_POST['elementary'],
        'elementary_year_grad' => $_POST['elementary_yr_grad'],
        'elementary_honors' => $_POST['elementary_honors_rec'],
        'secondary' => $_POST['secondary'],
        'secondary_year_grad' => $_POST['secondary_yr_grad'],
        'secondary_honors' => $_POST['secondary_honors_rec'],
        'college' => $_POST['college'],
        'college_year_grad' => $_POST['college_yr_grad'],
        'college_honors' => $_POST['college_honors_rec'],
        'reason_scholarship' => $_POST['reason_scholarship'],
        'father_lastname' => $_POST['father_lastname'],
        'father_givenname' => $_POST['father_givenname'],
        'father_middlename' => $_POST['father_middlename'],
        'father_cellphone' => preg_replace('/[^0-9]/', '', $_POST['father_cellphone']), // Remove non-numeric characters
        'father_education' => $_POST['father_education'],
        'father_occupation' => $_POST['father_occupation'],
        'father_income' => $_POST['father_income'],
        'mother_lastname' => $_POST['mother_lastname'],
        'mother_givenname' => $_POST['mother_givenname'],
        'mother_middlename' => $_POST['mother_middlename'],
        'mother_cellphone' => preg_replace('/[^0-9]/', '', $_POST['mother_cellphone']), // Remove non-numeric characters
        'mother_education' => $_POST['mother_education'],
        'mother_occupation' => $_POST['mother_occupation'],
        'mother_income' => $_POST['mother_income'],
        'house_status' => $_POST['house_status'],
    ];

    // Handle "others" fields
    if ($_POST['sex'] === 'others' && isset($_POST['sex_other'])) {
        $data['sex'] = $_POST['sex_other'];
    }

    if ($_POST['religion'] === 'others' && isset($_POST['religion_other'])) {
        $data['religion'] = $_POST['religion_other'];
    }

    if ($_POST['indigenous_group'] === 'others' && isset($_POST['indigenous_group_other'])) {
        $data['indigenous_group'] = $_POST['indigenous_group_other'];
    }

    $user_id = $_SESSION['id'];
    $username = $_SESSION['username']; // Retrieve username from session

    try {
        // Start transaction
        $pdo->beginTransaction();

        $application_id = date("YmdHis"); // Format: YYYYMMDDHHMMSS
        // Optionally add milliseconds
        $application_id .= substr((string) microtime(), 2, 3); // Adds 3-digit milliseconds

        // Insert into scholarship_applications table
        $stmt = $pdo->prepare("INSERT INTO scholarship_applications (application_id, user_id, date, semester, school_year, full_name, course, yr_sec, major, cell_no, permanent_address, zip_code, present_address, email, sex, date_of_birth, age, place_of_birth, civil_status, religion, scholarship_grant, disability, indigenous_group, reason_scholarship) VALUES (:application_id, :user_id, :date, :semester, :school_year, :full_name, :course, :yr_sec, :major, :cell_no, :permanent_address, :zip_code, :present_address, :email, :sex, :date_of_birth, :age, :place_of_birth, :civil_status, :religion, :scholarship_grant, :disability, :indigenous_group, :reason_scholarship)");

        $stmt->execute([
            ':application_id' => $application_id,
            ':user_id' => $user_id,
            ':date' => $data['date'],
            ':semester' => $data['semester'],
            ':school_year' => $data['school_year'],
            ':full_name' => $data['full_name'],
            ':course' => $data['course'],
            ':yr_sec' => $data['yr_sec'],
            ':major' => $data['major'],
            ':cell_no' => $data['cell_no'],
            ':permanent_address' => $data['permanent_address'],
            ':zip_code' => $data['zip_code'],
            ':present_address' => $data['present_address'],
            ':email' => $data['email'],
            ':sex' => $data['sex'],
            ':date_of_birth' => $data['date_of_birth'],
            ':age' => $data['age'],
            ':place_of_birth' => $data['place_of_birth'],
            ':civil_status' => $data['civil_status'],
            ':religion' => $data['religion'],
            ':scholarship_grant' => $data['scholarship_grant'],
            ':disability' => $data['disability'],
            ':indigenous_group' => $data['indigenous_group'],
            ':reason_scholarship' => $data['reason_scholarship'],
        ]);

        // Insert into schools_attended table
        $stmt = $pdo->prepare("INSERT INTO schools_attended (application_id, elementary, elementary_year_grad, elementary_honors, secondary, secondary_year_grad, secondary_honors, college, college_year_grad, college_honors) VALUES (:application_id, :elementary, :elementary_year_grad, :elementary_honors, :secondary, :secondary_year_grad, :secondary_honors, :college, :college_year_grad, :college_honors)");

        $stmt->execute([
            ':application_id' => $application_id,
            ':elementary' => $data['elementary'],
            ':elementary_year_grad' => $data['elementary_year_grad'],
            ':elementary_honors' => $data['elementary_honors'],
            ':secondary' => $data['secondary'],
            ':secondary_year_grad' => $data['secondary_year_grad'],
            ':secondary_honors' => $data['secondary_honors'],
            ':college' => $data['college'],
            ':college_year_grad' => $data['college_year_grad'],
            ':college_honors' => $data['college_honors'],
        ]);

        // Insert into parents_info table
        $stmt = $pdo->prepare("INSERT INTO parents_info (application_id, father_lastname, father_givenname, father_middlename, father_cellphone, father_education, father_occupation, father_income, mother_lastname, mother_givenname, mother_middlename, mother_cellphone, mother_education, mother_occupation, mother_income) VALUES (:application_id, :father_lastname, :father_givenname, :father_middlename, :father_cellphone, :father_education, :father_occupation, :father_income, :mother_lastname, :mother_givenname, :mother_middlename, :mother_cellphone, :mother_education, :mother_occupation, :mother_income)");

        $stmt->execute([
            ':application_id' => $application_id,
            ':father_lastname' => $data['father_lastname'],
            ':father_givenname' => $data['father_givenname'],
            ':father_middlename' => $data['father_middlename'],
            ':father_cellphone' => $data['father_cellphone'],
            ':father_education' => $data['father_education'],
            ':father_occupation' => $data['father_occupation'],
            ':father_income' => $data['father_income'],
            ':mother_lastname' => $data['mother_lastname'],
            ':mother_givenname' => $data['mother_givenname'],
            ':mother_middlename' => $data['mother_middlename'],
            ':mother_cellphone' => $data['mother_cellphone'],
            ':mother_education' => $data['mother_education'],
            ':mother_occupation' => $data['mother_occupation'],
            ':mother_income' => $data['mother_income'],
        ]);

        // Insert into house_info table
        $stmt = $pdo->prepare("INSERT INTO house_info (application_id, house_status) VALUES (:application_id, :house_status)");

        $stmt->execute([
            ':application_id' => $application_id,
            ':house_status' => $data['house_status'],
        ]);

        $uploadedFiles = [];
        $uploadDir = "../uploads/"; // create this folder if not exists
        if (!is_dir($uploadDir))
            mkdir($uploadDir, 0777, true);

        if (!empty($_FILES['attachments']['name'][0])) {
            $allowed = ['pdf', 'jpg', 'jpeg', 'png'];

            for ($i = 0; $i < count($_FILES['attachments']['name']); $i++) {
                $fileName = $_FILES['attachments']['name'][$i];
                $fileTmp = $_FILES['attachments']['tmp_name'][$i];
                $fileSize = $_FILES['attachments']['size'][$i];
                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if (!in_array($ext, $allowed))
                    continue;
                if ($fileSize > 5 * 1024 * 1024)
                    continue;

                $uniqueName = time() . "_" . uniqid() . "." . $ext;
                $uploadPath = $uploadDir . $uniqueName;

                if (move_uploaded_file($fileTmp, $uploadPath)) {
                    $uploadedFiles[] = $uniqueName;
                }
            }
        }

        // Save file names in database as JSON
        $fileListJSON = json_encode($uploadedFiles);

        $stmt = $pdo->prepare("INSERT INTO scholarship_files (application_id, files) VALUES (:application_id, :files)");
        $stmt->execute([
            ':application_id' => $application_id,
            ':files' => $fileListJSON
        ]);

        // Commit transaction
        $pdo->commit();

        // Update existing applications list for the current request
        $existing_applications[] = $selected_grant;

        // Log the activity
        $action = "Scholarship application submitted";
        $details = "Application ID $application_id submitted by $username.";
        logActivity($pdo, $user_id, $action, $details);

        $_SESSION['success_message'] = 'Your scholarship application has been successfully submitted.';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (PDOException $e) {
        // Rollback transaction on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $_SESSION['error_message'] = 'There was an error processing your application. Please try again.';
        // For debugging: echo "Error: " . $e->getMessage();
        error_log("Scholarship Form Error: " . $e->getMessage());
    }
}

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
    <title>Application Form</title>

    <script src="../js/fontawesome.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/preloader.css?v=<?php echo time(); ?>">
    <script src="../js/preloader.js?v=<?php echo time(); ?>"></script>
    <script src="../js/wizard.js?v=<?php echo time(); ?>"></script>
    <title>Scholarship Application Form</title>
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" type="text/css" href="../css/form.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" type="text/css" href="../css/wizard.css?v=<?php echo time(); ?>" />
    <style>
        /* Enhanced Page Styles */
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Form Header Enhancement */
        h1.text-center {
            background: linear-gradient(135deg, #8b0000, #b22222);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            padding: 20px 0;
            position: relative;
            margin-bottom: 40px;
        }

        h1.text-center::before {
            content: '📝';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            font-size: 40px;
            opacity: 0.2;
        }

        h1.text-center::after {
            content: '📝';
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            font-size: 40px;
            opacity: 0.2;
        }

        /* File Upload Section Enhancement */
        #drop-area {
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            border: 3px dashed #007bff;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        #drop-area.highlight {
            border-color: #28a745;
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            transform: scale(1.02);
        }

        .drop-text i {
            background: linear-gradient(135deg, #007bff, #00bcd4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 70px;
            margin-bottom: 20px;
        }

        /* Requirements Box Enhancement */
        .requirements-box {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            border-left: 5px solid #1976d2;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(25, 118, 210, 0.1);
        }

        .requirements-box h4 {
            color: #0d47a1;
            font-size: 18px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .requirements-box h4 i {
            font-size: 22px;
        }

        /* File Preview Enhancement */
        .preview-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-top: 30px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e0e0e0;
        }

        .file-item {
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            margin-bottom: 12px;
            transition: all 0.3s ease;
        }

        .file-item:hover {
            border-color: #007bff;
            transform: translateX(5px);
            box-shadow: 0 6px 12px rgba(0, 123, 255, 0.1);
        }

        .file-icon {
            font-size: 32px;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
        }

        .file-icon i.fa-file-pdf {
            color: #d32f2f;
        }

        .file-icon i.fa-file-image {
            color: #388e3c;
        }

        .file-icon i.fa-file {
            color: #f57c00;
        }

        /* Success/Error Messages Animation */
        .alert {
            animation: slideInRight 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275),
                slideOutRight 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) 4.5s forwards;
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        /* Loading State for Requirements */
        .fa-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Form Field Focus Effects */
        input:focus,
        select:focus,
        textarea:focus {
            animation: pulseGlow 2s infinite;
        }

        @keyframes pulseGlow {

            0%,
            100% {
                box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.15);
            }

            50% {
                box-shadow: 0 0 0 8px rgba(0, 123, 255, 0.05);
            }
        }

        /* Progress Indicator Enhancement */
        .wizard-progress {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 30px 0;
            padding: 0 20px;
        }

        .wizard-progress .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        /* Responsive Improvements */
        @media (max-width: 768px) {
            .phppot-container {
                padding: 10px;
            }

            section {
                padding: 20px;
            }

            .wizard-flow-chart {
                margin: 30px 0;
            }

            .buttonNav,
            .submitBtn {
                min-width: 120px;
                padding: 12px 25px;
            }
        }

        /* Accessibility Improvements */
        input:focus-visible,
        select:focus-visible,
        textarea:focus-visible,
        button:focus-visible {
            outline: 3px solid #ff9800;
            outline-offset: 2px;
        }

        /* Print Styles */
        @media print {

            .toggle-btn,
            .wizard-flow-chart,
            .button-row {
                display: none !important;
            }

            section {
                box-shadow: none !important;
                border: 1px solid #ccc !important;
            }
        }

        /* Wizard Step States */
        .wizard-flow-chart .step {
            position: relative;
        }

        .wizard-flow-chart .step.current span {
            background: linear-gradient(135deg, #ffc72c, #ff9800);
            border-color: #ff9800;
            transform: scale(1.15);
            box-shadow: 0 8px 16px rgba(255, 152, 0, 0.4);
            animation: pulse 2s infinite;
        }

        .wizard-flow-chart .step.completed span {
            background: linear-gradient(135deg, #8b0000, #b22222);
            border-color: #8b0000;
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(139, 0, 0, 0.3);
        }

        .wizard-flow-chart .step.completed span::after {
            content: '✓';
            position: absolute;
            top: -5px;
            right: -5px;
            background: #28a745;
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 152, 0, 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(255, 152, 0, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(255, 152, 0, 0);
            }
        }

        /* Single page display */
        .form-section {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .form-section.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Progress counter */
        .progress-counter {
            text-align: center;
            margin: 15px 0 30px 0;
            font-size: 16px;
            color: #666;
            font-weight: 600;
        }
    </style>
</head>

<body>


    <nav class="stroke" id="sideNav">
        <!-- <button class="toggle-btn" onclick="toggleNav()">
            <i class="fas fa-times" id="toggle-icon"></i>
        </button> -->

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
                    <a href="./scholarship_form.php" class="active">
                        <i class="fas fa-solid fa-file"></i>
                        <span class="nav-item-2">Scholarship Form</span>
                    </a>
                </li>
                <li><a href="./manage_dropdowns.php"><i class="fas fa-list"></i><span class="nav-item-2">Manage
                            Dropdowns</span></a></li>
                <li>
                    <a href="./applications.php">
                        <i class="fas fa-solid fa-folder"></i>
                        <span class="nav-item-2">Applications</span>
                    </a>
                </li>

                <li><a href="./logs.php"><i class="fas fa-solid fa-circle-question"></i><span
                            class="nav-item-2">Logs</span></a></li>
            <?php elseif ($_SESSION["role"] === 'student'): ?>
                <li>
                    <a href="../index.php">
                        <i class="fas fa-solid fa-house"></i>
                        <span class="nav-item-2">Home</span>
                    </a>
                </li>
                <li>
                    <a href="./my_applications.php">
                        <i class="fas fa-solid fa-folder-open"></i>
                        <span class="nav-item-2">My Applications</span>
                    </a>
                </li>
                <li>
                    <a href="./scholarship_form.php" class="active">
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

    <div class="phppot-container">
        <h1 class="text-center">Scholarship Application Form</h1>

        <div id="validationSummary" class="validation-summary">
            <h3>Please fix the following errors:</h3>
            <ul id="errorList"></ul>
        </div>

        <?php if ($user_role !== 'admin' && !empty($existing_applications)): ?>
            <div class="info-box"
                style="margin-bottom: 20px; padding: 15px; background-color: #f8f9fa; border-left: 4px solid #007bff;">
                <p><strong>Note:</strong> You have already applied for the following scholarship grants. Each user is
                    limited to one application per grant.</p>
                <ul style="margin: 10px 0 0 20px;">
                    <?php foreach ($existing_applications as $grant): ?>
                        <li><?php echo htmlspecialchars($grant); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" id="checkout-form" onSubmit="return validateForm()" enctype="multipart/form-data">
            <div class="wizard-flow-chart">
                <div class="step current" id="step1">
                    <span>1</span>
                    <div class="step-label">Personal Info</div>
                </div>
                <div class="step" id="step2">
                    <span>2</span>
                    <div class="step-label">Schools Attended</div>
                </div>
                <div class="step" id="step3">
                    <span>3</span>
                    <div class="step-label">Reason</div>
                </div>
                <div class="step" id="step4">
                    <span>4</span>
                    <div class="step-label">Parent's Data</div>
                </div>
                <div class="step" id="step5">
                    <span>5</span>
                    <div class="step-label">House Status</div>
                </div>
                <div class="step" id="step6">
                    <span>6</span>
                    <div class="step-label">Attachments</div>
                </div>
            </div>

            <div class="progress-counter">Step <span id="currentStep">1</span> of 6</div>

            <!-- Wizard section 1 -->
            <section id="personal-info" class="form-section active">
                <h2 class="text-center" style="margin-bottom: 20px;">Personal Information</h2>
                <div class="section-error" id="personal-info-error"></div>
                <div class="form-container">
                    <input type="hidden" name="date" id="autoDateTime">

                    <!-- SEMESTER / SCHOOL YEAR -->
                    <div class="form-row">
                        <label class="required-field">Semester / School Year</label>
                        <select name="semester_sy" required>
                            <option value="" disabled selected>Select Semester / School Year</option>
                            <?php foreach ($sem_sy_list as $s): ?>
                                <option
                                    value="<?= htmlspecialchars($s['semester']) ?>|<?= htmlspecialchars($s['school_year']) ?>"
                                    <?= ($s['semester'] === $current_sem && $s['school_year'] === $current_sy) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($s['semester']) ?> / <?= htmlspecialchars($s['school_year']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="error-message">Please select semester and school year</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Full Name</label>
                        <input name="fullName" type="text" required>
                        <div class="error-message">Please enter your full name</div>
                    </div>

                    <!-- COURSE / MAJOR -->
                    <div class="form-row">
                        <label class="required-field">Course / Major</label>
                        <select name="course_major" required>
                            <option value="" disabled selected>Select Course / Major</option>
                            <?php foreach ($course_major_list as $c): ?>
                                <option value="<?= htmlspecialchars($c['course']) ?>|<?= htmlspecialchars($c['major']) ?>"
                                    <?= ($c['course'] === $current_course && $c['major'] === $current_major) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['course']) ?> / <?= htmlspecialchars($c['major']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="error-message">Please select course and major</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Year/Section</label>
                        <input name="yr_sec" type="text" required>
                        <div class="error-message">Please enter year/section</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Cellphone #</label>
                        <input name="cellNo" type="text" class="phone-input" required
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="11"
                            placeholder="e.g., 09171234567">
                        <div class="error-message">Please enter a valid 11-digit cellphone number</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Complete Present Address</label>
                        <input name="pres_address" id="pres_address" type="text" required>
                        <div class="error-message">Please enter complete present address</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Complete Permanent Address</label>
                        <input name="perma_address" id="perma_address" type="text" required>
                        <div class="error-message">Please enter complete permanent address</div>
                    </div>

                    <div class="address-checkbox">
                        <input type="checkbox" id="same_address" onchange="copyAddress()">
                        <label for="same_address">Same as Present Address</label>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">ZIP code</label>
                        <input name="zip_code" type="number" required>
                        <div class="error-message">Please enter ZIP code</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Email Address</label>
                        <input name="email" type="email" required>
                        <div class="error-message">Please enter a valid email address</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Sex</label>
                        <select name="sex" id="sex" required onchange="toggleOtherField(this, 'sex_other')">
                            <option value="" disabled selected>Select Sex</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="others">Others</option>
                        </select>
                        <div class="specify-field" id="sex_other_field">
                            <label for="sex_other">Please specify:</label>
                            <input type="text" name="sex_other" id="sex_other" placeholder="Please specify your gender">
                        </div>
                        <div class="error-message">Please select your sex</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Date of Birth</label>
                        <input name="date_of_birth" id="date_of_birth" type="date" onchange="calculateAge()" required>
                        <div class="error-message">Please enter your date of birth</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Age</label>
                        <input name="age" id="age" type="number" readonly>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Place of Birth</label>
                        <input name="place_of_birth" type="text" required>
                        <div class="error-message">Please enter your place of birth</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Civil Status</label>
                        <select name="civil_status" required>
                            <option value="" disabled selected>Select Civil Status</option>
                            <option value="single">Single</option>
                            <option value="married">Married</option>
                            <option value="widowed">Widowed</option>
                            <option value="divorced">Divorced</option>
                            <option value="separated">Separated</option>
                        </select>
                        <div class="error-message">Please select your civil status</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Religion</label>
                        <select name="religion" id="religion" required
                            onchange="toggleOtherField(this, 'religion_other')">
                            <option value="" disabled selected>Select Religion</option>
                            <option value="roman catholic">Roman Catholic</option>
                            <option value="islam">Islam</option>
                            <option value="iglesia ni cristo">Iglesia ni Cristo</option>
                            <option value="evangelical christian">Evangelical Christian</option>
                            <option value="a biblical church">Aglipayan / Philippine Independent Church</option>
                            <option value="others">Others</option>
                        </select>
                        <div class="specify-field" id="religion_other_field">
                            <label for="religion_other">Please specify:</label>
                            <input type="text" name="religion_other" id="religion_other"
                                placeholder="Please specify your religion">
                        </div>
                        <div class="error-message">Please select your religion</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Scholarship Grant</label>
                        <select name="scholarship_grant" id="scholarship_grant" required>
                            <option value="" disabled selected>Select Scholarship Grant</option>
                            <?php foreach ($scholarship_grant_list as $grant): ?>
                                <option value="<?= htmlspecialchars($grant['grant_name']) ?>"
                                    <?= ($grant['grant_name'] === $current_scholarship_grant) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($grant['grant_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="error-message">Please select a scholarship grant</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Type of Disability</label>
                        <input name="disability" type="text" required placeholder="Enter 'None' if not applicable">
                        <div class="error-message">Please enter type of disability</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Indigenous People Group</label>
                        <select name="indigenous_group" id="indigenous_group" required
                            onchange="toggleOtherField(this, 'indigenous_group_other')">
                            <option value="" disabled selected>Select Indigenous People Group</option>
                            <option value="igorot">Igorot</option>
                            <option value="lumad">Lumad</option>
                            <option value="moro">Moro</option>
                            <option value="aeta">Aeta</option>
                            <option value="badjao">Badjao</option>
                            <option value="others">Others</option>
                            <option value="N/A">Not Applicable</option>
                        </select>
                        <div class="specify-field" id="indigenous_group_other_field">
                            <label for="indigenous_group_other">Please specify:</label>
                            <input type="text" name="indigenous_group_other" id="indigenous_group_other"
                                placeholder="Please specify your indigenous group">
                        </div>
                        <div class="error-message">Please select indigenous group</div>
                    </div>

                    <div class="row button-row">
                        <button type="button" class="buttonNav" onClick="nextStep()">Next</button>
                    </div>
                </div>
            </section>

            <!-- Wizard section 2 -->
            <section id="schools-attended" class="form-section">
                <h2 class="text-center" style="margin-bottom: 20px;">Schools Attended</h2>
                <div class="section-error" id="schools-attended-error"></div>
                <div class="form-container">
                    <h3>Elementary School</h3>
                    <div class="form-row">
                        <label class="float-left label-width required-field">Elementary</label>
                        <input type="text" name="elementary" required>
                        <div class="error-message">Please enter elementary school name</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Year Graduated</label>
                        <input type="number" name="elementary_yr_grad" required min="1900"
                            max="<?php echo date('Y'); ?>">
                        <div class="error-message">Please enter a valid graduation year</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width">Honors Received</label>
                        <input type="text" name="elementary_honors_rec" placeholder="Enter 'None' if not applicable">
                    </div>

                    <br>
                    <h3>Secondary School</h3>
                    <div class="form-row">
                        <label class="float-left label-width required-field">Secondary</label>
                        <input type="text" name="secondary" required>
                        <div class="error-message">Please enter secondary school name</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Year Graduated</label>
                        <input type="number" name="secondary_yr_grad" required min="1900"
                            max="<?php echo date('Y'); ?>">
                        <div class="error-message">Please enter a valid graduation year</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width">Honors Received</label>
                        <input type="text" name="secondary_honors_rec" placeholder="Enter 'None' if not applicable">
                    </div>

                    <br>
                    <h3>College/University (If applicable)</h3>
                    <div class="form-row">
                        <label class="float-left label-width">College</label>
                        <input type="text" name="college" placeholder="Leave blank if not applicable">
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width">Year Graduated</label>
                        <input type="number" name="college_yr_grad" min="1900" max="<?php echo date('Y'); ?>">
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width">Honors Received</label>
                        <input type="text" name="college_honors_rec" placeholder="Enter 'None' if not applicable">
                    </div>

                    <div class="row button-row">
                        <button type="button" class="buttonNav" onClick="prevStep()">Previous</button>
                        <button type="button" class="buttonNav" onClick="nextStep()">Next</button>
                    </div>
                </div>
            </section>

            <!-- Wizard section 3 -->
            <section id="scholarship-reason" class="form-section">
                <h2 class="text-center" style="margin-bottom: 20px;">Why do you need a Scholarship?</h2>
                <div class="section-error" id="scholarship-reason-error"></div>
                <div class="row">
                    <textarea name="reason_scholarship" id="reason_scholarship" required
                        placeholder="Please explain why you need a scholarship (minimum 50 characters)"></textarea>
                    <div class="error-message">Please explain why you need a scholarship (minimum 50 characters)</div>
                </div>
                <div class="row button-row">
                    <button type="button" class="buttonNav" onClick="prevStep()">Previous</button>
                    <button type="button" class="buttonNav" onClick="nextStep()">Next</button>
                </div>
            </section>

            <!-- Wizard section 4 -->
            <section id="parents-data" class="form-section">
                <h2 class="text-center" style="margin-bottom: 20px;">Parent's Data</h2>
                <div class="section-error" id="parents-data-error"></div>

                <!-- Father's Information Section -->
                <div class="form-container">
                    <h4>Father's Information</h4>
                    <div class="form-row">
                        <label for="father_lastname" class="float-left label-width required-field">Last Name:</label>
                        <input id="father_lastname" name="father_lastname" type="text" required>
                        <div class="error-message">Please enter father's last name</div>
                    </div>

                    <div class="form-row">
                        <label for="father_givenname" class="float-left label-width required-field">Given Name:</label>
                        <input id="father_givenname" name="father_givenname" type="text" required>
                        <div class="error-message">Please enter father's given name</div>
                    </div>

                    <div class="form-row">
                        <label for="father_middlename" class="float-left label-width">Middle Name:</label>
                        <input id="father_middlename" name="father_middlename" type="text">
                    </div>

                    <div class="form-row">
                        <label for="father_cellphone" class="float-left label-width required-field">Cellphone
                            Number:</label>
                        <input id="father_cellphone" name="father_cellphone" type="text" class="phone-input" required
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="11"
                            placeholder="e.g., 09171234567">
                        <div class="error-message">Please enter a valid 11-digit cellphone number</div>
                    </div>

                    <div class="form-row">
                        <label for="father_education" class="float-left label-width required-field">Educational
                            Attainment:</label>
                        <select name="father_education" id="father_education" required>
                            <option value="" disabled selected>Select Educational Attainment</option>
                            <option value="No Formal Education">No Formal Education</option>
                            <option value="Elementary Undergraduate">Elementary Undergraduate</option>
                            <option value="Elementary Graduate">Elementary Graduate</option>
                            <option value="High School Undergraduate">High School Undergraduate</option>
                            <option value="High School Graduate">High School Graduate</option>
                            <option value="Vocational Course">Vocational Course</option>
                            <option value="College Undergraduate">College Undergraduate</option>
                            <option value="College Graduate">College Graduate</option>
                            <option value="Postgraduate">Postgraduate (Master's/PhD)</option>
                        </select>
                        <div class="error-message">Please select father's educational attainment</div>
                    </div>

                    <div class="form-row">
                        <label for="father_occupation" class="float-left label-width required-field">Occupation:</label>
                        <select name="father_occupation" id="father_occupation" required>
                            <option value="" disabled selected>Select Occupation</option>
                            <option value="Government">Government</option>
                            <option value="Private Sector">Private Sector</option>
                            <option value="Self-Employed">Self-Employed</option>
                            <option value="Laborer">Laborer</option>
                            <option value="Freelancer">Freelancer</option>
                            <option value="NGO/Non-Profit">NGO/Non-Profit</option>
                            <option value="Overseas Employment">Overseas Employment</option>
                            <option value="Casual">Casual</option>
                            <option value="Contractual">Contractual</option>
                            <option value="Intern">Intern</option>
                        </select>
                        <div class="error-message">Please select father's occupation</div>
                    </div>

                    <div class="form-row">
                        <label for="father_income" class="float-left label-width required-field">Monthly Income:</label>
                        <input id="father_income" name="father_income" type="number" required min="0" step="0.01"
                            placeholder="0.00">
                        <div class="error-message">Please enter father's monthly income</div>
                    </div>
                </div>

                <!-- Mother's Information Section -->
                <div class="form-container">
                    <h4>Mother's Information</h4>
                    <div class="form-row">
                        <label for="mother_lastname" class="float-left label-width required-field">Maiden Name:</label>
                        <input id="mother_lastname" name="mother_lastname" type="text" required>
                        <div class="error-message">Please enter mother's maiden name</div>
                    </div>

                    <div class="form-row">
                        <label for="mother_givenname" class="float-left label-width required-field">Given Name:</label>
                        <input id="mother_givenname" name="mother_givenname" type="text" required>
                        <div class="error-message">Please enter mother's given name</div>
                    </div>

                    <div class="form-row">
                        <label for="mother_middlename" class="float-left label-width">Middle Name:</label>
                        <input id="mother_middlename" name="mother_middlename" type="text">
                    </div>

                    <div class="form-row">
                        <label for="mother_cellphone" class="float-left label-width required-field">Cellphone
                            Number:</label>
                        <input id="mother_cellphone" name="mother_cellphone" type="text" class="phone-input" required
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="11"
                            placeholder="e.g., 09171234567">
                        <div class="error-message">Please enter a valid 11-digit cellphone number</div>
                    </div>

                    <div class="form-row">
                        <label for="mother_education" class="float-left label-width required-field">Educational
                            Attainment:</label>
                        <select name="mother_education" id="mother_education" required>
                            <option value="" disabled selected>Select Educational Attainment</option>
                            <option value="No Formal Education">No Formal Education</option>
                            <option value="Elementary Undergraduate">Elementary Undergraduate</option>
                            <option value="Elementary Graduate">Elementary Graduate</option>
                            <option value="High School Undergraduate">High School Undergraduate</option>
                            <option value="High School Graduate">High School Graduate</option>
                            <option value="Vocational Course">Vocational Course</option>
                            <option value="College Undergraduate">College Undergraduate</option>
                            <option value="College Graduate">College Graduate</option>
                            <option value="Postgraduate">Postgraduate (Master's/PhD)</option>
                        </select>
                        <div class="error-message">Please select mother's educational attainment</div>
                    </div>

                    <div class="form-row">
                        <label for="mother_occupation" class="float-left label-width required-field">Occupation:</label>
                        <select name="mother_occupation" id="mother_occupation" required>
                            <option value="" disabled selected>Select Occupation</option>
                            <option value="Government">Government</option>
                            <option value="Private Sector">Private Sector</option>
                            <option value="Self-Employed">Self-Employed</option>
                            <option value="Laborer">Laborer</option>
                            <option value="Freelancer">Freelancer</option>
                            <option value="NGO/Non-Profit">NGO/Non-Profit</option>
                            <option value="Overseas Employment">Overseas Employment</option>
                            <option value="Casual">Casual</option>
                            <option value="Contractual">Contractual</option>
                            <option value="Intern">Intern</option>
                        </select>
                        <div class="error-message">Please select mother's occupation</div>
                    </div>

                    <div class="form-row mb-5">
                        <label for="mother_income" class="float-left label-width required-field">Monthly Income:</label>
                        <input id="mother_income" name="mother_income" type="number" required min="0" step="0.01"
                            placeholder="0.00">
                        <div class="error-message">Please enter mother's monthly income</div>
                    </div>
                </div>

                <div class="button-row">
                    <button type="button" class="buttonNav" onClick="prevStep()">Previous</button>
                    <button type="button" class="buttonNav" onClick="nextStep()">Next</button>
                </div>
            </section>

            <!-- Wizard section 5 -->
            <section id="house-status" class="form-section">
                <h2 class="text-center" style="margin-bottom: 20px;">House Status</h2>
                <div class="section-error" id="house-status-error"></div>

                <div class="house-status-group" role="radiogroup" aria-label="House Status">
                    <label class="house-option">
                        <input type="radio" name="house_status" value="owned" required>
                        <span class="option-label">House Owned</span>
                    </label>

                    <label class="house-option">
                        <input type="radio" name="house_status" value="rented">
                        <span class="option-label">Rented</span>
                    </label>

                    <label class="house-option">
                        <input type="radio" name="house_status" value="living with relatives">
                        <span class="option-label">Living with Relatives</span>
                    </label>
                </div>
                <div class="error-message" style="text-align: center; margin-top: 10px;">Please select house status
                </div>

                <div class="row button-row" style="margin-top:18px;">
                    <button type="button" class="buttonNav" onClick="prevStep()">Previous</button>
                    <button type="button" class="buttonNav" onClick="nextStep()">Next</button>
                </div>
            </section>

            <!-- Section 6: Attachments -->
            <section id="attachments-section" class="form-section">
                <h2 class="text-center" style="margin-bottom: 20px;">Attach Supporting Documents</h2>
                <div class="section-error" id="attachments-error"></div>

                <div class="upload-section mt-4" aria-labelledby="attach-label">
                    <label id="attach-label" class="section-label required-field">Attach Supporting Documents (PDF / JPG
                        / PNG)</label>

                    <!-- Document Requirements List -->
                    <div class="requirements-box"
                        style="background: #f8f9fa; border-left: 4px solid #007bff; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                        <h4 style="margin-top: 0; color: #2c3e50; font-size: 16px;">
                            <i class="fas fa-info-circle" style="color: #007bff;"></i> Required Documents for
                            <span id="selectedGrantName">[Selected Grant]</span>:
                        </h4>
                        <div id="grantRequirementsList" class="requirements-grid"
                            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 10px; margin-top: 10px;">
                            <!-- Requirements will be dynamically loaded here -->
                            <div class="requirement-item"
                                style="display: flex; align-items: flex-start; margin-bottom: 8px;">
                                <i class="fas fa-check-circle"
                                    style="color: #28a745; margin-right: 10px; margin-top: 2px;"></i>
                                <span>Select a scholarship grant to see specific requirements</span>
                            </div>
                        </div>

                        <div
                            style="margin-top: 15px; padding: 10px; background: #fff3cd; border-radius: 5px; border-left: 4px solid #ffc107;">
                            <p style="margin: 0; font-size: 14px; color: #856404;">
                                <i class="fas fa-exclamation-triangle" style="color: #ffc107; margin-right: 8px;"></i>
                                <strong>Note:</strong> All documents must be clear, readable, and in PDF, JPG, or PNG
                                format.
                                Maximum file size: 5MB per file.
                            </p>
                        </div>
                    </div>
                </div>

                <div id="drop-area" class="drop-area" tabindex="0">
                    <p class="drop-text" aria-hidden="true">
                        <i class="fas fa-cloud-upload-alt"></i><br>
                        Drag & drop files here<br>or
                    </p>

                    <button type="button" class="browse-btn" onclick="document.getElementById('attachments').click();">
                        Browse Files
                    </button>

                    <input type="file" name="attachments[]" id="attachments" multiple accept=".pdf,.jpg,.jpeg,.png"
                        style="display: none;">
                </div>
                <div class="error-message" style="margin-top: 10px;">Please attach all required supporting documents
                </div>

                <!-- File Preview with Categories -->
                <div id="preview" class="preview-container" aria-live="polite">
                    <div class="preview-header"
                        style="margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #ddd;">
                        <h4 style="margin: 0; color: #2c3e50;">Uploaded Documents</h4>
                        <small style="color: #666;">Click on file name to edit document type</small>
                    </div>
                    <div id="file-list"></div>
                </div>
                <div class="row button-row" style="margin-top:18px;">
                    <button type="button" class="buttonNav" onClick="prevStep()">Previous</button>
                    <button type="submit" class="submitBtn">Submit Application</button>
                </div>
            </section>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script>
        // Global variable for selected files
        let selectedFiles = [];
        let currentStep = 1;
        const totalSteps = 6;
        const sections = [
            'personal-info',
            'schools-attended',
            'scholarship-reason',
            'parents-data',
            'house-status',
            'attachments-section'
        ];

        // Function to toggle "Please specify" fields
        function toggleOtherField(selectElement, fieldId) {
            const otherField = document.getElementById(fieldId + '_field');
            const otherInput = document.getElementById(fieldId);

            if (selectElement.value === 'others') {
                otherField.style.display = 'block';
                if (otherInput) otherInput.required = true;
            } else {
                otherField.style.display = 'none';
                if (otherInput) {
                    otherInput.required = false;
                    otherInput.value = '';
                }
            }
        }

        function copyAddress() {
            const presentAddress = document.getElementById('pres_address').value;
            const permanentAddressField = document.getElementById('perma_address');
            const sameAddressCheckbox = document.getElementById('same_address');

            if (sameAddressCheckbox.checked) {
                // Copy from Present Address to Permanent Address
                permanentAddressField.value = presentAddress;
                permanentAddressField.readOnly = true;
            } else {
                permanentAddressField.readOnly = false;
                permanentAddressField.value = '';
            }
        }

        function getDateTime() {
            const now = new Date();

            const year = now.getFullYear();
            const month = ("0" + (now.getMonth() + 1)).slice(-2);
            const day = ("0" + now.getDate()).slice(-2);
            const hours = ("0" + now.getHours()).slice(-2);
            const minutes = ("0" + now.getMinutes()).slice(-2);
            const seconds = ("0" + now.getSeconds()).slice(-2);

            return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
        }

        function calculateAge() {
            const dob = document.getElementById('date_of_birth').value;
            if (dob) {
                const dobDate = new Date(dob);
                const today = new Date();
                let age = today.getFullYear() - dobDate.getFullYear();
                const monthDiff = today.getMonth() - dobDate.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dobDate.getDate())) {
                    age--;
                }
                document.getElementById('age').value = age;
            } else {
                document.getElementById('age').value = '';
            }
        }

        // Load grant requirements function
        function loadGrantRequirements(grantName) {
            console.log('Loading requirements for grant:', grantName);

            const requirementsList = document.getElementById('grantRequirementsList');
            const selectedGrantName = document.getElementById('selectedGrantName');

            if (!grantName || grantName === "") {
                console.log('No grant name provided or empty');
                requirementsList.innerHTML = `
                <div class="requirement-item" style="display: flex; align-items: flex-start; margin-bottom: 8px;">
                    <i class="fas fa-check-circle" style="color: #28a745; margin-right: 10px; margin-top: 2px;"></i>
                    <span>Select a scholarship grant to see specific requirements</span>
                </div>
            `;
                selectedGrantName.textContent = '[Selected Grant]';
                return;
            }

            selectedGrantName.textContent = grantName;

            // Show loading message
            requirementsList.innerHTML = `
            <div class="requirement-item" style="display: flex; align-items: center; margin-bottom: 8px;">
                <i class="fas fa-spinner fa-spin" style="color: #007bff; margin-right: 10px;"></i>
                <span>Loading requirements for "${grantName}"...</span>
            </div>
        `;

            // Fetch requirements via AJAX
            const url = '../includes/get_grant_requirements.php?grant_name=' + encodeURIComponent(grantName);

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.requirements && data.requirements.length > 0) {
                        let html = '';
                        data.requirements.forEach((req, index) => {
                            html += `
                            <div class="requirement-item" style="display: flex; align-items: flex-start; margin-bottom: 8px;">
                                <i class="fas fa-check-circle" style="color: #28a745; margin-right: 10px; margin-top: 2px;"></i>
                                <span>
                                    <strong>${index + 1}. ${req.requirement_name}</strong>
                                    ${req.requirement_type ? `<br><small>Type: ${req.requirement_type}</small>` : ''}
                                </span>
                            </div>
                        `;
                        });
                        requirementsList.innerHTML = html;
                    } else {
                        requirementsList.innerHTML = `
                        <div class="requirement-item" style="display: flex; align-items: flex-start; margin-bottom: 8px;">
                            <i class="fas fa-info-circle" style="color: #007bff; margin-right: 10px; margin-top: 2px;"></i>
                            <span>No specific requirements listed for this grant. Please upload all standard supporting documents.</span>
                        </div>
                    `;
                    }
                })
                .catch(error => {
                    console.error('Error loading requirements:', error);
                    requirementsList.innerHTML = `
                    <div class="requirement-item" style="display: flex; align-items: flex-start; margin-bottom: 8px;">
                        <i class="fas fa-exclamation-triangle" style="color: #dc3545; margin-right: 10px; margin-top: 2px;"></i>
                        <span>Unable to load requirements. Please check your connection and try again.</span>
                    </div>
                `;
                });
        }

        // Simple validation for number fields
        function validateNumberInput(input) {
            if (input.type === 'number') {
                // Check if value is negative (for income fields)
                if (parseFloat(input.value) < 0) {
                    return false;
                }

                // Check if value is too large
                if (parseFloat(input.value) > 99999999) {
                    return false;
                }

                // Check graduation years
                if (input.name.includes('yr_grad')) {
                    const year = parseInt(input.value);
                    const currentYear = new Date().getFullYear();
                    if (year < 1900 || year > currentYear) {
                        return false;
                    }
                }
            }
            return true;
        }

        // Validate current section
        function validateCurrentSection() {
            const currentSectionId = sections[currentStep - 1];
            const currentSection = document.getElementById(currentSectionId);
            const errorDiv = document.getElementById(currentSectionId + '-error');
            const inputs = currentSection.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;
            let errorMessages = [];

            // Clear previous errors
            if (errorDiv) {
                errorDiv.style.display = 'none';
                errorDiv.innerHTML = '';
            }
            currentSection.querySelectorAll('.error-message').forEach(msg => {
                msg.style.display = 'none';
            });
            currentSection.querySelectorAll('.form-row').forEach(row => {
                row.classList.remove('error');
            });

            // Hide main validation summary
            const validationSummary = document.getElementById('validationSummary');
            if (validationSummary) {
                validationSummary.style.display = 'none';
            }

            // Validate each required field in CURRENT SECTION ONLY
            inputs.forEach(input => {
                // Skip validation for "other" fields if not displayed
                if (input.name.includes('_other') && input.closest('.specify-field') && input.closest('.specify-field').style.display === 'none') {
                    return;
                }

                const parentRow = input.closest('.form-row');
                if (parentRow) {
                    const errorMessage = parentRow.querySelector('.error-message');

                    // Check if field is empty
                    if (!input.value.trim()) {
                        parentRow.classList.add('error');
                        if (errorMessage) errorMessage.style.display = 'block';
                        isValid = false;
                        const label = input.labels[0]?.textContent?.replace('*', '').trim() || input.name;
                        errorMessages.push(`${label} is required`);
                        return;
                    }

                    // Validate number fields
                    if (input.type === 'number') {
                        if (!validateNumberInput(input)) {
                            parentRow.classList.add('error');
                            if (errorMessage) errorMessage.style.display = 'block';
                            isValid = false;
                            const label = input.labels[0]?.textContent?.replace('*', '').trim() || input.name;
                            if (input.name.includes('yr_grad')) {
                                errorMessages.push(`${label} must be a valid year between 1900 and ${new Date().getFullYear()}`);
                            } else if (input.name.includes('income')) {
                                errorMessages.push(`${label} must be a valid positive number`);
                            } else {
                                errorMessages.push(`${label} must be a valid number`);
                            }
                            return;
                        }

                        // Check for negative values in income fields
                        if ((input.name.includes('income') || input.name.includes('father_income') || input.name.includes('mother_income')) && parseFloat(input.value) < 0) {
                            parentRow.classList.add('error');
                            if (errorMessage) errorMessage.style.display = 'block';
                            isValid = false;
                            errorMessages.push(`${input.labels[0]?.textContent || input.name} cannot be negative`);
                            return;
                        }
                    }

                    // Check email format
                    if (input.type === 'email') {
                        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailPattern.test(input.value)) {
                            parentRow.classList.add('error');
                            if (errorMessage) errorMessage.style.display = 'block';
                            isValid = false;
                            errorMessages.push(`Please enter a valid email address`);
                            return;
                        }
                    }

                    // Check phone number format
                    if (input.classList.contains('phone-input')) {
                        const phoneNumber = input.value.replace(/[^0-9]/g, '');
                        if (phoneNumber.length !== 11) {
                            parentRow.classList.add('error');
                            if (errorMessage) errorMessage.style.display = 'block';
                            isValid = false;
                            errorMessages.push(`Please enter a valid 11-digit cellphone number (e.g., 09171234567)`);
                            return;
                        }
                        // Check if starts with 09 (Philippine mobile numbers)
                        if (!phoneNumber.startsWith('09')) {
                            parentRow.classList.add('error');
                            if (errorMessage) errorMessage.style.display = 'block';
                            isValid = false;
                            errorMessages.push(`Cellphone number must start with '09'`);
                            return;
                        }
                    }

                    // Check date is not in the future
                    if (input.type === 'date') {
                        const selectedDate = new Date(input.value);
                        const today = new Date();
                        if (selectedDate > today) {
                            parentRow.classList.add('error');
                            if (errorMessage) errorMessage.style.display = 'block';
                            isValid = false;
                            errorMessages.push(`Date cannot be in the future`);
                            return;
                        }
                    }

                    // Check textarea minimum length for scholarship reason
                    if (input.name === 'reason_scholarship' || input.id === 'reason_scholarship') {
                        if (input.value.trim().length < 50) {
                            parentRow.classList.add('error');
                            if (errorMessage) errorMessage.style.display = 'block';
                            isValid = false;
                            errorMessages.push(`Please provide a detailed reason (minimum 50 characters)`);
                            return;
                        }
                    }
                }
            });

            // Check radio buttons for house status
            if (currentSectionId === 'house-status') {
                const radioSelected = currentSection.querySelector('input[name="house_status"]:checked');
                if (!radioSelected) {
                    const houseStatusError = currentSection.querySelector('.error-message');
                    if (houseStatusError) houseStatusError.style.display = 'block';
                    isValid = false;
                    errorMessages.push('Please select house status');
                }
            }

            // Check file upload in attachments section
            if (currentSectionId === 'attachments-section') {
                if (selectedFiles.length === 0) {
                    const errorMessage = currentSection.querySelector('.error-message');
                    if (errorMessage) errorMessage.style.display = 'block';
                    isValid = false;
                    errorMessages.push('Please attach at least one supporting document');
                }
            }

            if (!isValid) {
                // Show section error message
                if (errorDiv && errorMessages.length > 0) {
                    errorDiv.innerHTML = '<strong>Please fix the following errors:</strong><ul>' +
                        errorMessages.map(error => `<li>${error}</li>`).join('') + '</ul>';
                    errorDiv.style.display = 'block';

                    // Scroll to error message
                    errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return false;
            }

            return true;
        }

        // Navigate to next step
        function nextStep() {
            if (validateCurrentSection()) {
                if (currentStep < totalSteps) {
                    // Mark current step as completed
                    document.getElementById('step' + currentStep).classList.remove('current');
                    document.getElementById('step' + currentStep).classList.add('completed');

                    // Hide current section
                    document.getElementById(sections[currentStep - 1]).classList.remove('active');

                    // Move to next step
                    currentStep++;

                    // Show next section
                    document.getElementById(sections[currentStep - 1]).classList.add('active');

                    // Mark next step as current
                    document.getElementById('step' + currentStep).classList.add('current');

                    // Update progress counter
                    document.getElementById('currentStep').textContent = currentStep;

                    // Scroll to top of section
                    window.scrollTo({ top: 0, behavior: 'smooth' });

                    // If moving to attachments section, load grant requirements
                    if (currentStep === 6) {
                        const grantSelect = document.querySelector('select[name="scholarship_grant"]');
                        if (grantSelect && grantSelect.value) {
                            loadGrantRequirements(grantSelect.value);
                        }
                    }
                }
            }
        }

        // Navigate to previous step
        function prevStep() {
            if (currentStep > 1) {
                // Remove current class from current step
                document.getElementById('step' + currentStep).classList.remove('current');

                // Hide current section
                document.getElementById(sections[currentStep - 1]).classList.remove('active');

                // Move to previous step
                currentStep--;

                // Show previous section
                document.getElementById(sections[currentStep - 1]).classList.add('active');

                // Remove completed class and add current class
                document.getElementById('step' + currentStep).classList.remove('completed');
                document.getElementById('step' + currentStep).classList.add('current');

                // Update progress counter
                document.getElementById('currentStep').textContent = currentStep;

                // Scroll to top of section
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        function showValidationSummary(errors) {
            const summary = document.getElementById('validationSummary');
            const errorList = document.getElementById('errorList');

            if (summary && errorList) {
                errorList.innerHTML = '';
                errors.forEach(error => {
                    const li = document.createElement('li');
                    li.textContent = error;
                    errorList.appendChild(li);
                });
                summary.style.display = 'block';

                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        function validateForm() {
            // Validate all sections before submission
            let allErrors = [];

            sections.forEach(sectionId => {
                const section = document.getElementById(sectionId);
                const inputs = section.querySelectorAll('input[required], select[required], textarea[required]');

                inputs.forEach(input => {
                    // Skip validation for "other" fields if not displayed
                    if (input.name.includes('_other') && input.closest('.specify-field')?.style.display === 'none') {
                        return;
                    }

                    const parentRow = input.closest('.form-row');
                    if (parentRow) {
                        const errorMessage = parentRow.querySelector('.error-message');

                        // Check if field is empty
                        if (!input.value.trim()) {
                            parentRow.classList.add('error');
                            if (errorMessage) errorMessage.style.display = 'block';
                            const label = input.labels[0]?.textContent?.replace('*', '').trim() || input.name;
                            allErrors.push(`${label} is required`);
                        }

                        // Additional validation for specific fields
                        if (input.type === 'email' && input.value.trim()) {
                            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                            if (!emailPattern.test(input.value)) {
                                parentRow.classList.add('error');
                                if (errorMessage) errorMessage.style.display = 'block';
                                allErrors.push(`Please enter a valid email address`);
                            }
                        }

                        // Check phone numbers
                        if (input.classList.contains('phone-input') && input.value.trim()) {
                            const phoneNumber = input.value.replace(/[^0-9]/g, '');
                            if (phoneNumber.length !== 11) {
                                parentRow.classList.add('error');
                                if (errorMessage) errorMessage.style.display = 'block';
                                allErrors.push(`Please enter a valid 11-digit cellphone number for ${input.labels[0]?.textContent?.replace('*', '').trim() || input.name}`);
                            } else if (!phoneNumber.startsWith('09')) {
                                parentRow.classList.add('error');
                                if (errorMessage) errorMessage.style.display = 'block';
                                allErrors.push(`${input.labels[0]?.textContent?.replace('*', '').trim() || input.name} must start with '09'`);
                            }
                        }

                        if (input.name === 'reason_scholarship' && input.value.trim().length < 50) {
                            parentRow.classList.add('error');
                            if (errorMessage) errorMessage.style.display = 'block';
                            allErrors.push(`Please provide a detailed reason for scholarship (minimum 50 characters)`);
                        }
                    }
                });

                // Special handling for house status radio buttons
                if (sectionId === 'house-status') {
                    const radioSelected = section.querySelector('input[name="house_status"]:checked');
                    if (!radioSelected) {
                        const houseStatusError = section.querySelector('.error-message');
                        if (houseStatusError) houseStatusError.style.display = 'block';
                        allErrors.push('Please select house status');
                    }
                }
            });

            // Validate file upload
            if (selectedFiles.length === 0) {
                allErrors.push('Please attach at least one supporting document');
                // Also show the error in the attachments section
                const attachmentsSection = document.getElementById('attachments-section');
                if (attachmentsSection) {
                    const errorMessage = attachmentsSection.querySelector('.error-message');
                    if (errorMessage) errorMessage.style.display = 'block';
                }
            }

            if (allErrors.length > 0) {
                // Go to first section with error
                for (let i = 0; i < sections.length; i++) {
                    const section = document.getElementById(sections[i]);
                    const errorsInSection = section.querySelectorAll('.error');
                    if (errorsInSection.length > 0) {
                        // Navigate to that section
                        while (currentStep > i + 1) {
                            prevStep();
                        }
                        while (currentStep < i + 1) {
                            nextStep();
                        }
                        break;
                    }
                }

                showValidationSummary(allErrors);
                return false;
            }

            // Add file count to form before submission
            const fileCountInput = document.createElement('input');
            fileCountInput.type = 'hidden';
            fileCountInput.name = 'file_count';
            fileCountInput.value = selectedFiles.length;

            // Remove existing file_count input if it exists
            const existingFileCount = document.querySelector('input[name="file_count"]');
            if (existingFileCount) {
                existingFileCount.remove();
            }

            document.getElementById('checkout-form').appendChild(fileCountInput);

            // If all validations pass, submit the form
            return true;
        }

        // ========== FILE UPLOAD FUNCTIONALITY ==========
        function initializeFileUpload() {
            const dropArea = document.getElementById('drop-area');
            const fileInput = document.getElementById('attachments');
            const previewContainer = document.getElementById('preview');
            const allowed = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
            const maxSize = 5 * 1024 * 1024; // 5MB

            function showError(msg) {
                const errorDiv = document.getElementById('attachments-error');
                if (errorDiv) {
                    errorDiv.textContent = msg;
                    errorDiv.style.display = 'block';
                    setTimeout(() => {
                        errorDiv.style.display = 'none';
                    }, 5000);
                }
            }

            // Drag and drop functionality
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, preventDefaults, false);
                document.body.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, unhighlight, false);
            });

            function highlight() {
                dropArea.classList.add('highlight');
            }

            function unhighlight() {
                dropArea.classList.remove('highlight');
            }

            // Handle dropped files
            dropArea.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                handleFiles(files);
            }

            // Handle file input change
            fileInput.addEventListener('change', function (e) {
                handleFiles(e.target.files);
            });

            function handleFiles(files) {
                const errorDiv = document.getElementById('attachments-error');
                if (errorDiv) {
                    errorDiv.style.display = 'none';
                }

                [...files].forEach(file => {
                    // Validate file type
                    if (!allowed.includes(file.type)) {
                        showError(`Invalid file type for ${file.name}. Only PDF, JPG, and PNG files are allowed.`);
                        return;
                    }

                    // Validate file size
                    if (file.size > maxSize) {
                        showError(`File "${file.name}" is too large. Maximum size is 5MB.`);
                        return;
                    }

                    // Check if file already exists
                    const fileExists = selectedFiles.some(existingFile =>
                        existingFile.file.name === file.name && existingFile.file.size === file.size
                    );

                    if (!fileExists) {
                        selectedFiles.push({
                            id: Date.now() + Math.random(),
                            file: file,
                            documentType: 'Other Document'
                        });
                    } else {
                        showError(`File "${file.name}" is already added.`);
                    }
                });

                updateFilePreview();
                updateInputFiles();
            }

            function updateFilePreview() {
                const fileList = document.getElementById('file-list');
                const previewContainer = document.getElementById('preview');

                if (selectedFiles.length === 0) {
                    fileList.innerHTML = '<p class="no-files">No files uploaded yet</p>';
                    previewContainer.classList.remove('visible');
                    previewContainer.style.display = 'none';
                    return;
                }

                previewContainer.classList.add('visible');
                previewContainer.style.display = 'block';
                fileList.innerHTML = '';

                selectedFiles.forEach((fileObj, index) => {
                    const file = fileObj.file;
                    const fileId = fileObj.id;

                    const fileItem = document.createElement('div');
                    fileItem.className = 'file-item';
                    fileItem.innerHTML = `
                    <div class="file-info">
                        <div class="file-icon">
                            ${getFileIcon(file.type)}
                        </div>
                        <div class="file-details">
                            <span class="file-name">${escapeHtml(file.name)}</span>
                            <div class="file-meta">
                                <span class="file-size">${formatFileSize(file.size)}</span>
                                <span class="file-type">${getFileType(file.type)}</span>
                                <span class="document-type">Document Type: <strong>${escapeHtml(fileObj.documentType)}</strong></span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="remove-file" onclick="removeFile(${index})" title="Remove file">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                    fileList.appendChild(fileItem);
                });
            }

            function getFileIcon(fileType) {
                if (fileType === 'application/pdf') {
                    return '<i class="fas fa-file-pdf"></i>';
                } else if (fileType.startsWith('image/')) {
                    return '<i class="fas fa-file-image"></i>';
                } else {
                    return '<i class="fas fa-file"></i>';
                }
            }

            function getFileType(fileType) {
                if (fileType === 'application/pdf') return 'PDF';
                if (fileType === 'image/jpeg' || fileType === 'image/jpg') return 'JPG';
                if (fileType === 'image/png') return 'PNG';
                return fileType;
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // Make removeFile function global
            window.removeFile = function (index) {
                selectedFiles.splice(index, 1);
                updateFilePreview();
                updateInputFiles();
            }

            function updateInputFiles() {
                const dataTransfer = new DataTransfer();
                selectedFiles.forEach(fileObj => dataTransfer.items.add(fileObj.file));
                fileInput.files = dataTransfer.files;

                // Clear any validation error
                const errorMessage = document.querySelector('#attachments-section .error-message');
                if (errorMessage && selectedFiles.length > 0) {
                    errorMessage.style.display = 'none';
                }
            }

            // Initial preview update
            updateFilePreview();
        }

        function toggleNav() {
            const sideNav = document.getElementById('sideNav');
            const toggleBtn = document.querySelector('.toggle-btn');
            const toggleIcon = document.getElementById('toggle-icon');

            if (sideNav.style.left === '0px' || sideNav.style.left === '') {
                sideNav.style.left = '-250px';
                toggleIcon.className = 'fas fa-bars';
            } else {
                sideNav.style.left = '0px';
                toggleIcon.className = 'fas fa-times';
            }
        }

        // Initialize everything when DOM is loaded
        document.addEventListener('DOMContentLoaded', function () {
            // Check all dropdowns that have "others" option
            const religionSelect = document.getElementById('religion');
            if (religionSelect) {
                toggleOtherField(religionSelect, 'religion_other');
            }

            const indigenousSelect = document.getElementById('indigenous_group');
            if (indigenousSelect) {
                toggleOtherField(indigenousSelect, 'indigenous_group_other');
            }

            const sexSelect = document.getElementById('sex');
            if (sexSelect) {
                toggleOtherField(sexSelect, 'sex_other');
            }

            // Load requirements when scholarship grant is selected
            const grantSelect = document.querySelector('select[name="scholarship_grant"]');
            if (grantSelect) {
                grantSelect.addEventListener('change', function () {
                    loadGrantRequirements(this.value);
                });

                // Load requirements for initially selected grant
                if (grantSelect.value && grantSelect.value !== '') {
                    loadGrantRequirements(grantSelect.value);
                }
            }

            // Disable already applied scholarship grants for non-admin users
            <?php if ($user_role !== 'admin' && !empty($existing_applications)): ?>
                var scholarshipSelect = document.querySelector('select[name="scholarship_grant"]');
                var options = scholarshipSelect.options;

                for (var i = 0; i < options.length; i++) {
                    var grantValue = options[i].value;
                    <?php foreach ($existing_applications as $grant): ?>
                        if (grantValue === "<?php echo htmlspecialchars($grant); ?>") {
                            options[i].disabled = true;
                            options[i].textContent += " (Already Applied)";
                        }
                    <?php endforeach; ?>
                }
            <?php endif; ?>

            // Set date time when page loads
            document.getElementById("autoDateTime").value = getDateTime();

            // Initialize file upload system
            initializeFileUpload();

            // Handle success/error messages
            var successMessage = document.getElementById("successMessage");
            var errorMessage = document.getElementById("errorMessage");

            if (successMessage) {
                setTimeout(function () {
                    successMessage.style.display = "none";
                }, 5000);
            }

            if (errorMessage) {
                setTimeout(function () {
                    errorMessage.style.display = "none";
                }, 5000);
            }
        });
    </script>
</body>

</html>