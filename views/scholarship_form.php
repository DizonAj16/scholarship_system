<?php
include '../includes/session.php';

// Fetch DEFAULT Semester + School Year only
$sem_sy_list = $pdo->query("SELECT * FROM dropdown_sem_sy WHERE is_default = 1 ORDER BY school_year DESC, semester ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch Course + Major
$course_major_list = $pdo->query("SELECT * FROM dropdown_course_major ORDER BY course ASC, major ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch Scholarship Grants
$scholarship_grant_list = $pdo->query("SELECT * FROM dropdown_scholarship_grant ORDER BY grant_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Initialize variables
$current_sem = '';
$current_sy = '';
$current_course = '';
$current_major = '';
$current_scholarship_grant = '';

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

// ============================================
// PRE-FILL USER PROFILE DATA FOR STUDENTS
// ============================================
$user_profile_data = [];
$user_schools_data = [];
$user_parents_data = [];
$user_house_data = [];

if ($user_role === 'student') {
    // Fetch user profile data
    $stmt = $pdo->prepare("SELECT * FROM user_profile WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $user_profile_data = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch user schools attended data
    $stmt = $pdo->prepare("SELECT * FROM user_schools_attended WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $user_schools_data = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch user parents info data
    $stmt = $pdo->prepare("SELECT * FROM user_parents_info WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $user_parents_data = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch user house info data
    $stmt = $pdo->prepare("SELECT * FROM user_house_info WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $user_house_data = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Process form submission
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

    // Handle college year graduation - set to NULL if empty
    if (empty($data['college_year_grad'])) {
        $data['college_year_grad'] = null;
    }

    $user_id = $_SESSION['id'];
    $username = $_SESSION['username']; // Retrieve username from session

    try {
        // Start transaction
        $pdo->beginTransaction();

        $application_id = date("YmdHis"); // Format: YYYYMMDDHHMMSS
        // Optionally add milliseconds
        $application_id .= substr((string) microtime(), 2, 3); // Adds 3-digit milliseconds

        // Sanitize full name for folder creation
        $sanitized_full_name = preg_replace('/[^a-zA-Z0-9\s\-]/', '', $data['full_name']);
        $sanitized_full_name = str_replace(' ', '_', $sanitized_full_name);
        $sanitized_full_name = substr($sanitized_full_name, 0, 50); // Limit length

        // Create folder path: uploads/(full_name - application_id)/
        $upload_folder_name = $sanitized_full_name . '_' . $application_id;
        $uploadDir = "../uploads/" . $upload_folder_name . "/";

        // Create the directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

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
        $schools_sql = "INSERT INTO schools_attended (
            application_id, elementary, elementary_year_grad, elementary_honors,
            secondary, secondary_year_grad, secondary_honors,
            college, college_year_grad, college_honors
        ) VALUES (
            :application_id, :elementary, :elementary_year_grad, :elementary_honors,
            :secondary, :secondary_year_grad, :secondary_honors,
            :college, :college_year_grad, :college_honors
        )";
        
        $schools_stmt = $pdo->prepare($schools_sql);
        $schools_stmt->bindParam(':application_id', $application_id);
        $schools_stmt->bindParam(':elementary', $data['elementary']);
        $schools_stmt->bindParam(':elementary_year_grad', $data['elementary_year_grad']);
        $schools_stmt->bindParam(':elementary_honors', $data['elementary_honors']);
        $schools_stmt->bindParam(':secondary', $data['secondary']);
        $schools_stmt->bindParam(':secondary_year_grad', $data['secondary_year_grad']);
        $schools_stmt->bindParam(':secondary_honors', $data['secondary_honors']);
        $schools_stmt->bindParam(':college', $data['college']);
        $schools_stmt->bindParam(':college_year_grad', $data['college_year_grad']);
        $schools_stmt->bindParam(':college_honors', $data['college_honors']);
        
        // Handle NULL value for college_year_grad
        if ($data['college_year_grad'] === null) {
            $schools_stmt->bindValue(':college_year_grad', null, PDO::PARAM_NULL);
        } else {
            $schools_stmt->bindParam(':college_year_grad', $data['college_year_grad']);
        }
        
        if (!$schools_stmt->execute()) {
            throw new Exception("Failed to save schools attended information.");
        }

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

        // Handle file uploads to the organized folder
        // Process individual requirement uploads
        if (isset($_POST['requirement_names']) && is_array($_POST['requirement_names'])) {
            foreach ($_POST['requirement_names'] as $index => $requirementName) {
                $fileInputName = 'requirement_' . $index;

                if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] === UPLOAD_ERR_OK) {
                    $fileName = $_FILES[$fileInputName]['name'];
                    $fileTmp = $_FILES[$fileInputName]['tmp_name'];
                    $fileSize = $_FILES[$fileInputName]['size'];
                    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    $allowed = ['pdf', 'jpg', 'jpeg', 'png'];

                    if (!in_array($ext, $allowed)) {
                        continue;
                    }
                    if ($fileSize > 5 * 1024 * 1024) {
                        continue;
                    }

                    // Sanitize requirement name for filename
                    $sanitizedReqName = preg_replace('/[^a-zA-Z0-9\s\-]/', '', $requirementName);
                    $sanitizedReqName = str_replace(' ', '_', $sanitizedReqName);
                    $sanitizedReqName = substr($sanitizedReqName, 0, 30);

                    // Generate unique filename with requirement name
                    $uniqueName = time() . "_" . $sanitizedReqName . "_" . uniqid() . "." . $ext;
                    $uploadPath = $uploadDir . $uniqueName;

                    if (move_uploaded_file($fileTmp, $uploadPath)) {
                        // Store relative path in database with requirement name
                        $relativePath = $upload_folder_name . "/" . $uniqueName;
                        $uploadedFiles[] = [
                            'requirement_name' => $requirementName,
                            'file_path' => $relativePath
                        ];
                    }
                }
            }
        }

        // Also handle additional files from the general upload area
        if (!empty($_FILES['attachments']['name'][0])) {
            $allowed = ['pdf', 'jpg', 'jpeg', 'png'];

            for ($i = 0; $i < count($_FILES['attachments']['name']); $i++) {
                if ($_FILES['attachments']['error'][$i] !== UPLOAD_ERR_OK) {
                    continue;
                }
                
                $fileName = $_FILES['attachments']['name'][$i];
                $fileTmp = $_FILES['attachments']['tmp_name'][$i];
                $fileSize = $_FILES['attachments']['size'][$i];
                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if (!in_array($ext, $allowed))
                    continue;
                if ($fileSize > 5 * 1024 * 1024)
                    continue;

                // Generate unique filename with timestamp and original name
                $uniqueName = time() . "_" . uniqid() . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);
                $uploadPath = $uploadDir . $uniqueName;

                if (move_uploaded_file($fileTmp, $uploadPath)) {
                    // Store relative path in database
                    $relativePath = $upload_folder_name . "/" . $uniqueName;
                    $uploadedFiles[] = [
                        'requirement_name' => 'Additional Document',
                        'file_path' => $relativePath
                    ];
                }
            }
        }

        // Save file paths in database as JSON
        $fileListJSON = json_encode($uploadedFiles);

        $stmt = $pdo->prepare("INSERT INTO scholarship_files (application_id, files, upload_folder) VALUES (:application_id, :files, :upload_folder)");
        $stmt->execute([
            ':application_id' => $application_id,
            ':files' => $fileListJSON,
            ':upload_folder' => $upload_folder_name
        ]);

        // Commit transaction
        $pdo->commit();

        // Update existing applications list for the current request
        $existing_applications[] = $selected_grant;

        // Log the activity
        $action = "Scholarship application submitted";
        $details = "Application ID $application_id submitted by $username. Files uploaded to: $upload_folder_name";
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
    } catch (Exception $e) {
        // Rollback transaction on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $_SESSION['error_message'] = 'There was an error processing your application: ' . $e->getMessage();
        error_log("Scholarship Form Exception: " . $e->getMessage());
    }
}

// Check if there's a default semester/school year set
if (empty($sem_sy_list)) {
    // If no default is set, show an error message
    $error_message = '<div class="alert alert-danger" style="margin: 20px auto; max-width: 800px;">
            <h4><i class="fas fa-exclamation-triangle"></i> No Active Semester/School Year</h4>
            <p>There is currently no active semester/school year set for applications. Please contact the administrator to set a default semester and school year.</p>';

    // If user is admin, show additional message
    if ($user_role === 'admin') {
        $error_message .= '<p><strong>Admin Note:</strong> You can set a default semester/school year in the <a href="./manage_dropdowns.php">Scholarship Settings</a> page.</p>';
    }
    $error_message .= '</div>';

    echo $error_message;

    // Don't show the form if there's no default semester
    die();
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
    <script src="../js/toggle_nav.js?v=<?php echo time(); ?>"></script>

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

        /* Semester Display Styles */
        .semester-display {
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            border: 2px solid #28a745;
            border-radius: 10px;
            padding: 15px 20px;
            margin: 20px 0;
            text-align: center;
            box-shadow: 0 4px 6px rgba(40, 167, 69, 0.1);
            position: relative;
            overflow: hidden;
        }

        .semester-display::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: #28a745;
        }

        .semester-display h4 {
            color: #155724;
            margin: 0 0 10px 0;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .semester-display h4 i {
            font-size: 20px;
        }

        .semester-value {
            font-size: 22px;
            font-weight: 700;
            color: #28a745;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .semester-note {
            font-size: 14px;
            color: #666;
            margin-top: 10px;
            font-style: italic;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        /* Pre-filled Data Indicator */
        .prefilled-indicator {
            background: #e8f5e9;
            border: 1px solid #c8e6c9;
            border-radius: 5px;
            padding: 10px;
            margin: 10px 0;
            font-size: 14px;
            color: #2e7d32;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .prefilled-indicator i {
            color: #28a745;
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
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
        }

        .file-item:hover {
            border-color: #007bff;
            transform: translateX(5px);
            box-shadow: 0 6px 12px rgba(0, 123, 255, 0.1);
        }

        .file-info {
            display: flex;
            align-items: center;
            flex: 1;
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
            margin-right: 15px;
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

        .file-details {
            flex: 1;
        }

        .file-name {
            font-weight: 600;
            color: #2c3e50;
            display: block;
            margin-bottom: 5px;
            word-break: break-word;
        }

        .file-meta {
            display: flex;
            gap: 15px;
            font-size: 13px;
            color: #666;
        }

        .remove-file {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            width: 36px;
            height: 36px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s;
        }

        .remove-file:hover {
            background: #c82333;
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



            .buttonNav,
            .submitBtn {
                min-width: 120px;
                padding: 12px 25px;
            }

            .semester-display {
                padding: 12px 15px;
                margin: 15px 0;
            }

            .semester-value {
                font-size: 18px;
            }

            .file-meta {
                flex-direction: column;
                gap: 5px;
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

            .semester-display {
                border: 1px solid #000 !important;
                background: none !important;
                box-shadow: none !important;
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

        /* Upload folder info */
        .folder-info {
            background: #e8f5e9;
            border: 1px solid #c8e6c9;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
            font-size: 14px;
        }

        .folder-info strong {
            color: #2e7d32;
        }

        /* Profile Info Section */
        .profile-info-section {
            background: #f8f9fa;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .profile-info-section h3 {
            color: #155724;
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .profile-info-section h3 i {
            color: #28a745;
        }

        .profile-info-section p {
            margin: 5px 0;
            color: #666;
        }

        .profile-info-section strong {
            color: #333;
        }

        /* Individual Requirement Upload Styles */
        .requirement-upload-container {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .requirement-upload-container:hover {
            border-color: #007bff;
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.1);
        }

        .requirement-upload-container.completed {
            border-color: #28a745;
            background-color: #f8fff8;
        }

        .requirement-upload-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .requirement-number {
            background: linear-gradient(135deg, #007bff, #00bcd4);
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
            font-size: 18px;
        }

        .requirement-details {
            flex: 1;
        }

        .requirement-title {
            font-weight: 600;
            color: #2c3e50;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .requirement-description {
            font-size: 14px;
            color: #666;
        }

        .requirement-upload-area {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 10px;
        }

        .requirement-upload-area:hover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }

        .requirement-upload-area.highlight {
            border-color: #28a745;
            background-color: #e8f5e9;
        }

        .requirement-upload-preview {
            margin-top: 15px;
        }

        .requirement-file-info {
            display: flex;
            align-items: center;
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
        }

        .requirement-file-icon {
            font-size: 24px;
            color: #007bff;
            margin-right: 15px;
        }

        .requirement-file-details {
            flex: 1;
        }

        .requirement-file-name {
            font-weight: 500;
            color: #2c3e50;
            word-break: break-word;
        }

        .requirement-file-remove {
            color: #dc3545;
            cursor: pointer;
            padding: 5px;
        }

        .requirement-file-remove:hover {
            color: #c82333;
        }

        .requirement-status {
            display: flex;
            align-items: center;
            margin-top: 10px;
            font-size: 14px;
        }

        .requirement-status.completed {
            color: #28a745;
        }

        .requirement-status.pending {
            color: #ff9800;
        }

        /* Additional Files Section */
        .additional-files-section {
            background: #f8f9fa;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
        }

        .additional-files-section h5 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .additional-files-note {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }

        /* Validation styles */
        .requirement-upload-container.error {
            border-color: #dc3545;
            animation: shake 0.5s;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            10%,
            30%,
            50%,
            70%,
            90% {
                transform: translateX(-5px);
            }

            20%,
            40%,
            60%,
            80% {
                transform: translateX(5px);
            }
        }

        .requirement-error {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }
        
        /* Validation Summary Styles */
        .validation-summary {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: none;
            border-left: 4px solid #dc3545;
        }
        
        .validation-summary h3 {
            margin-top: 0;
            font-size: 18px;
            color: #721c24;
        }
        
        .validation-summary ul {
            margin: 10px 0 0 20px;
            padding: 0;
        }
        
        .validation-summary li {
            margin-bottom: 5px;
        }
        
        /* Error message styles */
        .section-error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: none;
            border-left: 4px solid #dc3545;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }
        
        .form-row.error .error-message {
            display: block;
        }
        
        .form-row.error input,
        .form-row.error select,
        .form-row.error textarea {
            border-color: #dc3545 !important;
        }
        
        .form-row.error .requirement-upload-area {
            border-color: #dc3545 !important;
        }
    </style>
</head>

<body>


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
                    <a href="./scholarship_form.php" class="activea">
                        <i class="fas fa-solid fa-file"></i>
                        <span class="nav-item-2">Scholarship Form</span>
                    </a>
                </li>
                <li><a href="./manage_dropdowns.php"><i class="fas fa-list"></i><span class="nav-item-2">Scholarship Settings</span></a></li>
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
                    <a href="./scholarship_form.php" class="activea">
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
        <button class="toggle-btn" onclick="toggleNav()">
            <i class="fas fa-bars" id="toggle-icon"></i>
        </button>
    </nav>

    <div class="phppot-container">
        <h1 class="text-center">Scholarship Application Form</h1>

        <?php if (!empty($sem_sy_list)): ?>
            <!-- Display Current Semester/School Year -->
            <div class="semester-display">
                <h4><i class="fas fa-calendar-check"></i> Current Application Period</h4>
                <div class="semester-value">
                    <?= htmlspecialchars($sem_sy_list[0]['semester']) ?> /
                    <?= htmlspecialchars($sem_sy_list[0]['school_year']) ?>
                </div>
                <div class="semester-note">
                    <i class="fas fa-info-circle"></i> This is the active semester/school year for applications
                </div>
            </div>
        <?php endif; ?>

        <?php if ($user_role === 'student' && !empty($user_profile_data)): ?>
            <!-- Profile Info Section (Only for students with profile data) -->
            <div class="profile-info-section">
                <h3><i class="fas fa-user-check"></i> Profile Information Loaded</h3>
                <p><strong>Note:</strong> Your personal information has been pre-filled from your profile.
                    You can review and update the information as needed for this specific scholarship application.</p>
                <p><i class="fas fa-info-circle"></i> Required fields marked with * must be completed for this application.
                </p>
            </div>
        <?php endif; ?>

        <div id="validationSummary" class="validation-summary">
            <h3>Please fill up the following required fields:</h3>
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

                    <!-- SEMESTER / SCHOOL YEAR (Hidden since we're using the default) -->
                    <input type="hidden" name="semester_sy"
                        value="<?= htmlspecialchars($sem_sy_list[0]['semester']) ?>|<?= htmlspecialchars($sem_sy_list[0]['school_year']) ?>">

                    <!-- Display instead of select for semester/school year -->
                    <div class="form-row">
                        <label class="label-width required-field">Semester / School Year</label>
                        <div
                            style="background: #f8f9fa; border: 1px solid #ddd; padding: 10px 15px; border-radius: 4px; font-weight: 600; color: #28a745;">
                            <i class="fas fa-calendar-alt" style="margin-right: 8px;"></i>
                            <?= htmlspecialchars($sem_sy_list[0]['semester']) ?> /
                            <?= htmlspecialchars($sem_sy_list[0]['school_year']) ?>
                            <span style="font-size: 12px; color: #666; margin-left: 10px;">
                                <i class="fas fa-check-circle"></i> Active Period
                            </span>
                        </div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Full Name</label>
                        <input name="fullName" type="text" required id="fullNameField"
                            value="<?= $user_role === 'student' && !empty($user_profile_data['full_name']) ? htmlspecialchars($user_profile_data['full_name']) : '' ?>">
                        <div class="error-message">Please enter your full name</div>
                    </div>

                    <!-- COURSE / MAJOR -->
                    <div class="form-row">
                        <label class="label-width required-field">Course / Major</label>
                        <select name="course_major" required>
                            <option value="" disabled selected>Select Course / Major</option>
                            <?php foreach ($course_major_list as $c):
                                $isSelected = false;
                                if ($user_role === 'student' && !empty($user_profile_data)) {
                                    $isSelected = ($c['course'] === $user_profile_data['course'] && $c['major'] === $user_profile_data['major']);
                                }
                                ?>
                                <option value="<?= htmlspecialchars($c['course']) ?>|<?= htmlspecialchars($c['major']) ?>"
                                    <?= $isSelected ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['course']) ?> / <?= htmlspecialchars($c['major']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="error-message">Please select course and major</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Year/Section</label>
                        <input name="yr_sec" type="text" required
                            value="<?= $user_role === 'student' && !empty($user_profile_data['yr_sec']) ? htmlspecialchars($user_profile_data['yr_sec']) : '' ?>">
                        <div class="error-message">Please enter year/section</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Cellphone #</label>
                        <input name="cellNo" type="text" required
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="11"
                            value="<?= $user_role === 'student' && !empty($user_profile_data['cell_no']) ? htmlspecialchars($user_profile_data['cell_no']) : '' ?>">
                        <div class="error-message">Please enter a valid 11-digit cellphone number</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Complete Present Address</label>
                        <input name="pres_address" id="pres_address" type="text" required
                            value="<?= $user_role === 'student' && !empty($user_profile_data['present_address']) ? htmlspecialchars($user_profile_data['present_address']) : '' ?>">
                        <div class="error-message">Please enter complete present address</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Complete Permanent Address</label>
                        <input name="perma_address" id="perma_address" type="text" required
                            value="<?= $user_role === 'student' && !empty($user_profile_data['permanent_address']) ? htmlspecialchars($user_profile_data['permanent_address']) : '' ?>">
                        <div class="error-message">Please enter complete permanent address</div>
                    </div>

                    <div class="address-checkbox">
                        <input type="checkbox" id="same_address" onchange="copyAddress()">
                        <label for="same_address">Same as Present Address</label>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">ZIP code</label>
                        <input name="zip_code" type="number" required
                            value="<?= $user_role === 'student' && !empty($user_profile_data['zip_code']) ? htmlspecialchars($user_profile_data['zip_code']) : '' ?>">
                        <div class="error-message">Please enter ZIP code</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Email Address</label>
                        <input name="email" type="email" required
                            value="<?= $user_role === 'student' && !empty($user_profile_data['email']) ? htmlspecialchars($user_profile_data['email']) : '' ?>">
                        <div class="error-message">Please enter a valid email address</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Sex</label>
                        <select name="sex" id="sex" required onchange="toggleOtherField(this, 'sex_other')">
                            <option value="" disabled selected>Select Sex</option>
                            <?php
                            $user_sex = $user_role === 'student' && !empty($user_profile_data['sex']) ? $user_profile_data['sex'] : '';
                            $isSexOther = !in_array($user_sex, ['male', 'female']) && !empty($user_sex);
                            ?>
                            <option value="male" <?= ($user_sex === 'male') ? 'selected' : '' ?>>Male</option>
                            <option value="female" <?= ($user_sex === 'female') ? 'selected' : '' ?>>Female</option>
                        </select>
                        <div class="specify-field" id="sex_other_field"
                            style="display: <?= $isSexOther ? 'block' : 'none' ?>;">
                            <label for="sex_other">Please specify:</label>
                            <input type="text" name="sex_other" id="sex_other" placeholder="Please specify your gender"
                                value="<?= $isSexOther ? htmlspecialchars($user_sex) : '' ?>">
                        </div>
                        <div class="error-message">Please select your sex</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Date of Birth</label>
                        <input name="date_of_birth" id="date_of_birth" type="date" onchange="calculateAge()" required
                            value="<?= $user_role === 'student' && !empty($user_profile_data['date_of_birth']) ? htmlspecialchars($user_profile_data['date_of_birth']) : '' ?>">
                        <div class="error-message">Please enter your date of birth</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Age</label>
                        <input name="age" id="age" type="number" readonly
                            value="<?= $user_role === 'student' && !empty($user_profile_data['age']) ? htmlspecialchars($user_profile_data['age']) : '' ?>">
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Place of Birth</label>
                        <input name="place_of_birth" type="text" required
                            value="<?= $user_role === 'student' && !empty($user_profile_data['place_of_birth']) ? htmlspecialchars($user_profile_data['place_of_birth']) : '' ?>">
                        <div class="error-message">Please enter your place of birth</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Civil Status</label>
                        <select name="civil_status" required>
                            <option value="" disabled selected>Select Civil Status</option>
                            <?php
                            $user_civil_status = $user_role === 'student' && !empty($user_profile_data['civil_status']) ? $user_profile_data['civil_status'] : '';
                            ?>
                            <option value="single" <?= ($user_civil_status === 'single') ? 'selected' : '' ?>>Single
                            </option>
                            <option value="married" <?= ($user_civil_status === 'married') ? 'selected' : '' ?>>Married
                            </option>
                            <option value="widowed" <?= ($user_civil_status === 'widowed') ? 'selected' : '' ?>>Widowed
                            </option>
                            <option value="divorced" <?= ($user_civil_status === 'divorced') ? 'selected' : '' ?>>Divorced
                            </option>
                            <option value="separated" <?= ($user_civil_status === 'separated') ? 'selected' : '' ?>>
                                Separated</option>
                        </select>
                        <div class="error-message">Please select your civil status</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Religion</label>
                        <select name="religion" id="religion" required
                            onchange="toggleOtherField(this, 'religion_other')">
                            <option value="" disabled selected>Select Religion</option>
                            <?php
                            $user_religion = $user_role === 'student' && !empty($user_profile_data['religion']) ? $user_profile_data['religion'] : '';
                            $isReligionOther = !in_array($user_religion, ['roman catholic', 'islam', 'iglesia ni cristo', 'evangelical christian', 'a biblical church']) && !empty($user_religion);
                            ?>
                            <option value="roman catholic" <?= ($user_religion === 'roman catholic') ? 'selected' : '' ?>>
                                Roman Catholic</option>
                            <option value="islam" <?= ($user_religion === 'islam') ? 'selected' : '' ?>>Islam</option>
                            <option value="iglesia ni cristo" <?= ($user_religion === 'iglesia ni cristo') ? 'selected' : '' ?>>Iglesia ni Cristo</option>
                            <option value="evangelical christian" <?= ($user_religion === 'evangelical christian') ? 'selected' : '' ?>>Evangelical Christian</option>
                            <option value="a biblical church" <?= ($user_religion === 'a biblical church') ? 'selected' : '' ?>>Aglipayan / Philippine Independent Church</option>
                            <option value="others" <?= $isReligionOther ? 'selected' : '' ?>>Others</option>
                        </select>
                        <div class="specify-field" id="religion_other_field"
                            style="display: <?= $isReligionOther ? 'block' : 'none' ?>;">
                            <label for="religion_other">Please specify:</label>
                            <input type="text" name="religion_other" id="religion_other"
                                placeholder="Please specify your religion"
                                value="<?= $isReligionOther ? htmlspecialchars($user_religion) : '' ?>">
                        </div>
                        <div class="error-message">Please select your religion</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Scholarship Grant</label>
                        <select name="scholarship_grant" id="scholarship_grant" required>
                            <option value="" disabled selected>Select Scholarship Grant</option>
                            <?php foreach ($scholarship_grant_list as $grant): ?>
                                <option value="<?= htmlspecialchars($grant['grant_name']) ?>">
                                    <?= htmlspecialchars($grant['grant_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="error-message">Please select a scholarship grant</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Type of Disability</label>
                        <input name="disability" type="text" required placeholder="Enter 'None' if not applicable"
                            value="<?= $user_role === 'student' && !empty($user_profile_data['disability']) ? htmlspecialchars($user_profile_data['disability']) : '' ?>">
                        <div class="error-message">Please enter type of disability</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Indigenous People Group</label>
                        <select name="indigenous_group" id="indigenous_group" required
                            onchange="toggleOtherField(this, 'indigenous_group_other')">
                            <option value="" disabled selected>Select Indigenous People Group</option>
                            <?php
                            $user_indigenous = $user_role === 'student' && !empty($user_profile_data['indigenous_group']) ? $user_profile_data['indigenous_group'] : '';
                            $isIndigenousOther = !in_array($user_indigenous, ['igorot', 'lumad', 'moro', 'aeta', 'badjao', 'N/A']) && !empty($user_indigenous);
                            ?>
                            <option value="igorot" <?= ($user_indigenous === 'igorot') ? 'selected' : '' ?>>Igorot</option>
                            <option value="lumad" <?= ($user_indigenous === 'lumad') ? 'selected' : '' ?>>Lumad</option>
                            <option value="moro" <?= ($user_indigenous === 'moro') ? 'selected' : '' ?>>Moro</option>
                            <option value="aeta" <?= ($user_indigenous === 'aeta') ? 'selected' : '' ?>>Aeta</option>
                            <option value="badjao" <?= ($user_indigenous === 'badjao') ? 'selected' : '' ?>>Badjao</option>
                            <option value="others" <?= $isIndigenousOther ? 'selected' : '' ?>>Others</option>
                            <option value="N/A" <?= ($user_indigenous === 'N/A') ? 'selected' : '' ?>>Not Applicable
                            </option>
                        </select>
                        <div class="specify-field" id="indigenous_group_other_field"
                            style="display: <?= $isIndigenousOther ? 'block' : 'none' ?>;">
                            <label for="indigenous_group_other">Please specify:</label>
                            <input type="text" name="indigenous_group_other" id="indigenous_group_other"
                                placeholder="Please specify your indigenous group"
                                value="<?= $isIndigenousOther ? htmlspecialchars($user_indigenous) : '' ?>">
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
                        <input type="text" name="elementary" required
                            value="<?= $user_role === 'student' && !empty($user_schools_data['elementary']) ? htmlspecialchars($user_schools_data['elementary']) : '' ?>">
                        <div class="error-message">Please enter elementary school name</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Year Graduated</label>
                        <input type="number" name="elementary_yr_grad" required min="1900"
                            max="<?php echo date('Y'); ?>"
                            value="<?= $user_role === 'student' && !empty($user_schools_data['elementary_year_grad']) ? htmlspecialchars($user_schools_data['elementary_year_grad']) : '' ?>">
                        <div class="error-message">Please enter a valid graduation year</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width">Honors Received</label>
                        <input type="text" name="elementary_honors_rec" placeholder="Enter 'None' if not applicable"
                            value="<?= $user_role === 'student' && !empty($user_schools_data['elementary_honors']) ? htmlspecialchars($user_schools_data['elementary_honors']) : '' ?>">
                    </div>

                    <br>
                    <h3>Secondary School</h3>
                    <div class="form-row">
                        <label class="float-left label-width required-field">Secondary</label>
                        <input type="text" name="secondary" required
                            value="<?= $user_role === 'student' && !empty($user_schools_data['secondary']) ? htmlspecialchars($user_schools_data['secondary']) : '' ?>">
                        <div class="error-message">Please enter secondary school name</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width required-field">Year Graduated</label>
                        <input type="number" name="secondary_yr_grad" required min="1900" max="<?php echo date('Y'); ?>"
                            value="<?= $user_role === 'student' && !empty($user_schools_data['secondary_year_grad']) ? htmlspecialchars($user_schools_data['secondary_year_grad']) : '' ?>">
                        <div class="error-message">Please enter a valid graduation year</div>
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width">Honors Received</label>
                        <input type="text" name="secondary_honors_rec" placeholder="Enter 'None' if not applicable"
                            value="<?= $user_role === 'student' && !empty($user_schools_data['secondary_honors']) ? htmlspecialchars($user_schools_data['secondary_honors']) : '' ?>">
                    </div>

                    <br>
                    <h3>College/University (If applicable)</h3>
                    <div class="form-row">
                        <label class="float-left label-width">College</label>
                        <input type="text" name="college" placeholder="Leave blank if not applicable"
                            value="<?= $user_role === 'student' && !empty($user_schools_data['college']) ? htmlspecialchars($user_schools_data['college']) : '' ?>">
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width">Year Graduated</label>
                        <input type="number" name="college_yr_grad" min="1900" max="<?php echo date('Y'); ?>" 
                               placeholder="Leave blank if not applicable"
                               value="<?= $user_role === 'student' && !empty($user_schools_data['college_year_grad']) ? htmlspecialchars($user_schools_data['college_year_grad']) : '' ?>">
                    </div>

                    <div class="form-row">
                        <label class="float-left label-width">Honors Received</label>
                        <input type="text" name="college_honors_rec" placeholder="Enter 'None' if not applicable"
                            value="<?= $user_role === 'student' && !empty($user_schools_data['college_honors']) ? htmlspecialchars($user_schools_data['college_honors']) : '' ?>">
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
                <div class="form-container">
                    <div class="form-row">
                        <label class="float-left label-width required-field">Reason for Scholarship</label>
                        <textarea name="reason_scholarship" id="reason_scholarship" required
                            placeholder="Please explain why you need a scholarship (minimum 50 characters)"
                            oninput="validateReasonTextarea()" rows="6"></textarea>
                        <div class="error-message">Please explain why you need a scholarship (minimum 50 characters)
                        </div>
                        <div class="character-counter"
                            style="font-size: 12px; color: #666; margin-top: 5px; text-align: right;">
                            Character count: <span id="charCount">0</span> / <span id="minChars">50</span>
                        </div>
                    </div>
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
                        <input id="father_lastname" name="father_lastname" type="text" required
                            value="<?= $user_role === 'student' && !empty($user_parents_data['father_lastname']) ? htmlspecialchars($user_parents_data['father_lastname']) : '' ?>">
                        <div class="error-message">Please enter father's last name</div>
                    </div>

                    <div class="form-row">
                        <label for="father_givenname" class="float-left label-width required-field">Given Name:</label>
                        <input id="father_givenname" name="father_givenname" type="text" required
                            value="<?= $user_role === 'student' && !empty($user_parents_data['father_givenname']) ? htmlspecialchars($user_parents_data['father_givenname']) : '' ?>">
                        <div class="error-message">Please enter father's given name</div>
                    </div>

                    <div class="form-row">
                        <label for="father_middlename" class="float-left label-width">Middle Name:</label>
                        <input id="father_middlename" name="father_middlename" type="text"
                            value="<?= $user_role === 'student' && !empty($user_parents_data['father_middlename']) ? htmlspecialchars($user_parents_data['father_middlename']) : '' ?>">
                    </div>

                    <div class="form-row">
                        <label for="father_cellphone" class="float-left label-width required-field">Cellphone
                            Number:</label>
                        <input id="father_cellphone" name="father_cellphone" type="text" class="" required
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="11"
                            placeholder="e.g., 09171234567"
                            value="<?= $user_role === 'student' && !empty($user_parents_data['father_cellphone']) ? htmlspecialchars($user_parents_data['father_cellphone']) : '' ?>">
                        <div class="error-message">Please enter a valid 11-digit cellphone number</div>
                    </div>

                    <div class="form-row">
                        <label for="father_education" class="float-left label-width required-field">Educational
                            Attainment:</label>
                        <select name="father_education" id="father_education" required>
                            <option value="" disabled selected>Select Educational Attainment</option>
                            <?php
                            $father_education = $user_role === 'student' && !empty($user_parents_data['father_education']) ? $user_parents_data['father_education'] : '';
                            ?>
                            <option value="No Formal Education" <?= ($father_education == 'No Formal Education') ? 'selected' : '' ?>>No Formal Education</option>
                            <option value="Elementary Undergraduate" <?= ($father_education == 'Elementary Undergraduate') ? 'selected' : '' ?>>Elementary Undergraduate</option>
                            <option value="Elementary Graduate" <?= ($father_education == 'Elementary Graduate') ? 'selected' : '' ?>>Elementary Graduate</option>
                            <option value="High School Undergraduate" <?= ($father_education == 'High School Undergraduate') ? 'selected' : '' ?>>High School Undergraduate</option>
                            <option value="High School Graduate" <?= ($father_education == 'High School Graduate') ? 'selected' : '' ?>>High School Graduate</option>
                            <option value="Vocational Course" <?= ($father_education == 'Vocational Course') ? 'selected' : '' ?>>Vocational Course</option>
                            <option value="College Undergraduate" <?= ($father_education == 'College Undergraduate') ? 'selected' : '' ?>>College Undergraduate</option>
                            <option value="College Graduate" <?= ($father_education == 'College Graduate') ? 'selected' : '' ?>>College Graduate</option>
                            <option value="Postgraduate" <?= ($father_education == 'Postgraduate') ? 'selected' : '' ?>>
                                Postgraduate (Master's/PhD)</option>
                        </select>
                        <div class="error-message">Please select father's educational attainment</div>
                    </div>

                    <div class="form-row">
                        <label for="father_occupation" class="float-left label-width required-field">Occupation:</label>
                        <select name="father_occupation" id="father_occupation" required>
                            <option value="" disabled selected>Select Occupation</option>
                            <?php
                            $father_occupation = $user_role === 'student' && !empty($user_parents_data['father_occupation']) ? $user_parents_data['father_occupation'] : '';
                            ?>
                            <option value="Government" <?= ($father_occupation == 'Government') ? 'selected' : '' ?>>
                                Government</option>
                            <option value="Private Sector" <?= ($father_occupation == 'Private Sector') ? 'selected' : '' ?>>Private Sector</option>
                            <option value="Self-Employed" <?= ($father_occupation == 'Self-Employed') ? 'selected' : '' ?>>
                                Self-Employed</option>
                            <option value="Laborer" <?= ($father_occupation == 'Laborer') ? 'selected' : '' ?>>Laborer
                            </option>
                            <option value="Freelancer" <?= ($father_occupation == 'Freelancer') ? 'selected' : '' ?>>
                                Freelancer</option>
                            <option value="NGO/Non-Profit" <?= ($father_occupation == 'NGO/Non-Profit') ? 'selected' : '' ?>>NGO/Non-Profit</option>
                            <option value="Overseas Employment" <?= ($father_occupation == 'Overseas Employment') ? 'selected' : '' ?>>Overseas Employment</option>
                            <option value="Casual" <?= ($father_occupation == 'Casual') ? 'selected' : '' ?>>Casual
                            </option>
                            <option value="Contractual" <?= ($father_occupation == 'Contractual') ? 'selected' : '' ?>>
                                Contractual</option>
                            <option value="Intern" <?= ($father_occupation == 'Intern') ? 'selected' : '' ?>>Intern
                            </option>
                        </select>
                        <div class="error-message">Please select father's occupation</div>
                    </div>

                    <div class="form-row">
                        <label for="father_income" class="float-left label-width required-field">Monthly Income:</label>
                        <input id="father_income" name="father_income" type="number" required min="0" step="0.01"
                            placeholder="0.00"
                            value="<?= $user_role === 'student' && !empty($user_parents_data['father_income']) ? htmlspecialchars($user_parents_data['father_income']) : '' ?>">
                        <div class="error-message">Please enter father's monthly income</div>
                    </div>
                </div>

                <!-- Mother's Information Section -->
                <div class="form-container">
                    <h4>Mother's Information</h4>
                    <div class="form-row">
                        <label for="mother_lastname" class="float-left label-width required-field">Maiden Name:</label>
                        <input id="mother_lastname" name="mother_lastname" type="text" required
                            value="<?= $user_role === 'student' && !empty($user_parents_data['mother_lastname']) ? htmlspecialchars($user_parents_data['mother_lastname']) : '' ?>">
                        <div class="error-message">Please enter mother's maiden name</div>
                    </div>

                    <div class="form-row">
                        <label for="mother_givenname" class="float-left label-width required-field">Given Name:</label>
                        <input id="mother_givenname" name="mother_givenname" type="text" required
                            value="<?= $user_role === 'student' && !empty($user_parents_data['mother_givenname']) ? htmlspecialchars($user_parents_data['mother_givenname']) : '' ?>">
                        <div class="error-message">Please enter mother's given name</div>
                    </div>

                    <div class="form-row">
                        <label for="mother_middlename" class="float-left label-width">Middle Name:</label>
                        <input id="mother_middlename" name="mother_middlename" type="text"
                            value="<?= $user_role === 'student' && !empty($user_parents_data['mother_middlename']) ? htmlspecialchars($user_parents_data['mother_middlename']) : '' ?>">
                    </div>

                    <div class="form-row">
                        <label for="mother_cellphone" class="float-left label-width required-field">Cellphone
                            Number:</label>
                        <input id="mother_cellphone" name="mother_cellphone" type="text" class="" required
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="11"
                            placeholder="e.g., 09171234567"
                            value="<?= $user_role === 'student' && !empty($user_parents_data['mother_cellphone']) ? htmlspecialchars($user_parents_data['mother_cellphone']) : '' ?>">
                        <div class="error-message">Please enter a valid 11-digit cellphone number</div>
                    </div>

                    <div class="form-row">
                        <label for="mother_education" class="float-left label-width required-field">Educational
                            Attainment:</label>
                        <select name="mother_education" id="mother_education" required>
                            <option value="" disabled selected>Select Educational Attainment</option>
                            <?php
                            $mother_education = $user_role === 'student' && !empty($user_parents_data['mother_education']) ? $user_parents_data['mother_education'] : '';
                            ?>
                            <option value="No Formal Education" <?= ($mother_education == 'No Formal Education') ? 'selected' : '' ?>>No Formal Education</option>
                            <option value="Elementary Undergraduate" <?= ($mother_education == 'Elementary Undergraduate') ? 'selected' : '' ?>>Elementary Undergraduate</option>
                            <option value="Elementary Graduate" <?= ($mother_education == 'Elementary Graduate') ? 'selected' : '' ?>>Elementary Graduate</option>
                            <option value="High School Undergraduate" <?= ($mother_education == 'High School Undergraduate') ? 'selected' : '' ?>>High School Undergraduate</option>
                            <option value="High School Graduate" <?= ($mother_education == 'High School Graduate') ? 'selected' : '' ?>>High School Graduate</option>
                            <option value="Vocational Course" <?= ($mother_education == 'Vocational Course') ? 'selected' : '' ?>>Vocational Course</option>
                            <option value="College Undergraduate" <?= ($mother_education == 'College Undergraduate') ? 'selected' : '' ?>>College Undergraduate</option>
                            <option value="College Graduate" <?= ($mother_education == 'College Graduate') ? 'selected' : '' ?>>College Graduate</option>
                            <option value="Postgraduate" <?= ($mother_education == 'Postgraduate') ? 'selected' : '' ?>>
                                Postgraduate (Master's/PhD)</option>
                        </select>
                        <div class="error-message">Please select mother's educational attainment</div>
                    </div>

                    <div class="form-row">
                        <label for="mother_occupation" class="float-left label-width required-field">Occupation:</label>
                        <select name="mother_occupation" id="mother_occupation" required>
                            <option value="" disabled selected>Select Occupation</option>
                            <?php
                            $mother_occupation = $user_role === 'student' && !empty($user_parents_data['mother_occupation']) ? $user_parents_data['mother_occupation'] : '';
                            ?>
                            <option value="Government" <?= ($mother_occupation == 'Government') ? 'selected' : '' ?>>
                                Government</option>
                            <option value="Private Sector" <?= ($mother_occupation == 'Private Sector') ? 'selected' : '' ?>>Private Sector</option>
                            <option value="Self-Employed" <?= ($mother_occupation == 'Self-Employed') ? 'selected' : '' ?>>
                                Self-Employed</option>
                            <option value="Laborer" <?= ($mother_occupation == 'Laborer') ? 'selected' : '' ?>>Laborer
                            </option>
                            <option value="Freelancer" <?= ($mother_occupation == 'Freelancer') ? 'selected' : '' ?>>
                                Freelancer</option>
                            <option value="NGO/Non-Profit" <?= ($mother_occupation == 'NGO/Non-Profit') ? 'selected' : '' ?>>NGO/Non-Profit</option>
                            <option value="Overseas Employment" <?= ($mother_occupation == 'Overseas Employment') ? 'selected' : '' ?>>Overseas Employment</option>
                            <option value="Casual" <?= ($mother_occupation == 'Casual') ? 'selected' : '' ?>>Casual
                            </option>
                            <option value="Contractual" <?= ($mother_occupation == 'Contractual') ? 'selected' : '' ?>>
                                Contractual</option>
                            <option value="Intern" <?= ($mother_occupation == 'Intern') ? 'selected' : '' ?>>Intern
                            </option>
                        </select>
                        <div class="error-message">Please select mother's occupation</div>
                    </div>

                    <div class="form-row mb-5">
                        <label for="mother_income" class="float-left label-width required-field">Monthly Income:</label>
                        <input id="mother_income" name="mother_income" type="number" required min="0" step="0.01"
                            placeholder="0.00"
                            value="<?= $user_role === 'student' && !empty($user_parents_data['mother_income']) ? htmlspecialchars($user_parents_data['mother_income']) : '' ?>">
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
                    <?php
                    $user_house_status = $user_role === 'student' && !empty($user_house_data['house_status']) ? $user_house_data['house_status'] : '';
                    ?>
                    <label class="house-option">
                        <input type="radio" name="house_status" value="owned" required <?= ($user_house_status === 'owned') ? 'checked' : '' ?>>
                        <span class="option-label">House Owned</span>
                    </label>

                    <label class="house-option">
                        <input type="radio" name="house_status" value="rented" <?= ($user_house_status === 'rented') ? 'checked' : '' ?>>
                        <span class="option-label">Rented</span>
                    </label>

                    <label class="house-option">
                        <input type="radio" name="house_status" value="living with relatives"
                            <?= ($user_house_status === 'living with relatives') ? 'checked' : '' ?>>
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

                <!-- Folder Organization Info -->
                <div class="folder-info">
                    <i class="fas fa-folder-open" style="color: #2e7d32; margin-right: 8px;"></i>
                    <strong>File Organization:</strong>
                    Your files will be organized in a folder named:
                    <span id="folderNamePreview"
                        style="background: #fff; padding: 3px 8px; border-radius: 4px; border: 1px dashed #4caf50; font-family: monospace;">
                        (Your Name)_(Application ID)/
                    </span>
                </div>

                <div class="upload-section mt-4" aria-labelledby="attach-label">
                    <label id="attach-label" class="section-label required-field">Attach Supporting Documents (PDF / JPG
                        / PNG)</label>

                    <!-- Document Requirements List with Upload Areas -->
                    <div class="requirements-box"
                        style="background: #f8f9fa; border-left: 4px solid #007bff; padding: 20px; margin-bottom: 30px; border-radius: 8px;">
                        <h4 style="margin-top: 0; color: #2c3e50; font-size: 18px; margin-bottom: 20px;">
                            <i class="fas fa-info-circle" style="color: #007bff;"></i> Required Documents for
                            <span id="selectedGrantName">[Selected Grant]</span>:
                        </h4>

                        <!-- Requirements upload containers will be dynamically loaded here -->
                        <div id="requirementsUploadContainer">
                            <div style="text-align: center; padding: 40px; color: #666;">
                                <i class="fas fa-file-upload"
                                    style="font-size: 48px; margin-bottom: 15px; color: #ddd;"></i>
                                <p>Select a scholarship grant to view and upload required documents</p>
                            </div>
                        </div>

                        <div
                            style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 5px; border-left: 4px solid #ffc107;">
                            <p style="margin: 0; font-size: 14px; color: #856404;">
                                <i class="fas fa-exclamation-triangle" style="color: #ffc107; margin-right: 8px;"></i>
                                <strong>Note:</strong> All documents must be clear, readable, and in PDF, JPG, or PNG
                                format. Maximum file size: 5MB per file.
                            </p>
                        </div>
                    </div>

                    <!-- Additional Files Section -->
                    <div class="additional-files-section">
                        <h5><i class="fas fa-plus-circle" style="color: #28a745; margin-right: 8px;"></i> Additional
                            Supporting Documents</h5>
                        <p class="additional-files-note">
                            Upload any additional documents that support your scholarship application (optional).
                        </p>

                        <div id="drop-area" class="drop-area" tabindex="0">
                            <p class="drop-text" aria-hidden="true">
                                <i class="fas fa-cloud-upload-alt"></i><br>
                                Drag & drop additional files here<br>or
                            </p>

                            <button type="button" class="browse-btn"
                                onclick="document.getElementById('attachments').click();">
                                Browse Files
                            </button>

                            <input type="file" name="attachments[]" id="attachments" multiple
                                accept=".pdf,.jpg,.jpeg,.png" style="display: none;">
                        </div>

                        <!-- Additional Files Preview -->
                        <div id="additional-files-preview" class="preview-container" style="margin-top: 20px;">
                            <div class="preview-header"
                                style="margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #ddd;">
                                <h4 style="margin: 0; color: #2c3e50; font-size: 16px;">Additional Files</h4>
                            </div>
                            <div id="additional-files-list"></div>
                        </div>
                    </div>
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
        // Global variables
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

        // Store for requirement uploads
        let requirementUploads = {};
        let additionalFiles = [];
        let additionalFileInput = null;

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

        // Function to update folder name preview
        function updateFolderNamePreview() {
            const fullNameInput = document.getElementById('fullNameField');
            const folderPreview = document.getElementById('folderNamePreview');

            if (fullNameInput && folderPreview) {
                let fullName = fullNameInput.value.trim();
                if (fullName) {
                    // Sanitize the name for folder creation
                    let sanitizedName = fullName.replace(/[^a-zA-Z0-9\s\-]/g, '');
                    sanitizedName = sanitizedName.replace(/\s+/g, '_');
                    sanitizedName = sanitizedName.substring(0, 50); // Limit length

                    // Generate application ID preview (using current timestamp as example)
                    const timestamp = new Date().toISOString().replace(/[-:.]/g, '').substring(2, 15);
                    const folderName = sanitizedName + '_' + timestamp;

                    folderPreview.textContent = folderName + '/';
                } else {
                    folderPreview.textContent = '(Your Name)_(Application ID)/';
                }
            }
        }

        // Load grant requirements function with upload areas
        function loadGrantRequirements(grantName) {
            console.log('Loading requirements for grant:', grantName);

            const requirementsContainer = document.getElementById('requirementsUploadContainer');
            const selectedGrantName = document.getElementById('selectedGrantName');

            if (!grantName || grantName === "") {
                console.log('No grant name provided or empty');
                requirementsContainer.innerHTML = `
                        <div style="text-align: center; padding: 40px; color: #666;">
                            <i class="fas fa-file-upload" style="font-size: 48px; margin-bottom: 15px; color: #ddd;"></i>
                            <p>Select a scholarship grant to view and upload required documents</p>
                        </div>
                    `;
                selectedGrantName.textContent = '[Selected Grant]';
                return;
            }

            selectedGrantName.textContent = grantName;

            // Show loading message
            requirementsContainer.innerHTML = `
                    <div style="text-align: center; padding: 40px;">
                        <i class="fas fa-spinner fa-spin" style="font-size: 48px; color: #007bff; margin-bottom: 15px;"></i>
                        <p>Loading requirements for "${grantName}"...</p>
                    </div>
                `;

            // Clear previous requirement uploads
            requirementUploads = {};

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
                            const requirementId = 'req_' + index;
                            requirementUploads[requirementId] = {
                                requirementName: req.requirement_name,
                                file: null
                            };

                            html += `
                                    <div class="requirement-upload-container" id="req_container_${index}">
                                        <div class="requirement-upload-header">
                                            <div class="requirement-number">${index + 1}</div>
                                            <div class="requirement-details">
                                                <div class="requirement-title">${req.requirement_name}</div>
                                                ${req.requirement_type ? `<div class="requirement-description">Type: ${req.requirement_type}</div>` : ''}
                                            </div>
                                        </div>
                                        
                                        <div class="requirement-upload-area" id="upload_area_${index}" onclick="openFileInput('${requirementId}')">
                                            <i class="fas fa-cloud-upload-alt" style="font-size: 32px; color: #007bff; margin-bottom: 10px;"></i>
                                            <p style="margin: 10px 0; color: #666;">
                                                Click to upload file for this requirement<br>
                                                <small>PDF, JPG, PNG up to 5MB</small>
                                            </p>
                                        </div>
                                        
                                        <input type="hidden" name="requirement_names[]" value="${req.requirement_name}">
                                        <input type="file" name="requirement_${index}" id="file_input_${index}" 
                                            style="display: none;" accept=".pdf,.jpg,.jpeg,.png"
                                            onchange="handleRequirementFileUpload(${index}, this)">
                                        
                                        <div id="requirement_preview_${index}" class="requirement-upload-preview" style="display: none;">
                                            <!-- File preview will be inserted here -->
                                        </div>
                                        
                                        <div class="requirement-error" id="requirement_error_${index}"></div>
                                    </div>
                                `;
                        });
                        requirementsContainer.innerHTML = html;
                    } else {
                        requirementsContainer.innerHTML = `
                                <div class="requirement-upload-container">
                                    <div class="requirement-upload-header">
                                        <div class="requirement-number">1</div>
                                        <div class="requirement-details">
                                            <div class="requirement-title">General Supporting Documents</div>
                                            <div class="requirement-description">Upload all required documents for this grant</div>
                                        </div>
                                    </div>
                                    
                                    <div class="requirement-upload-area" onclick="openFileInput('req_0')">
                                        <i class="fas fa-cloud-upload-alt" style="font-size: 32px; color: #007bff; margin-bottom: 10px;"></i>
                                        <p style="margin: 10px 0; color: #666;">
                                            Click to upload supporting documents<br>
                                            <small>PDF, JPG, PNG up to 5MB</small>
                                        </p>
                                    </div>
                                    
                                    <input type="hidden" name="requirement_names[]" value="General Documents">
                                    <input type="file" name="requirement_0" id="file_input_0" 
                                        style="display: none;" accept=".pdf,.jpg,.jpeg,.png"
                                        onchange="handleRequirementFileUpload(0, this)">
                                </div>
                            `;
                    }
                })
                .catch(error => {
                    console.error('Error loading requirements:', error);
                    requirementsContainer.innerHTML = `
                            <div style="text-align: center; padding: 40px; color: #dc3545;">
                                <i class="fas fa-exclamation-triangle" style="font-size: 48px; margin-bottom: 15px;"></i>
                                <p>Unable to load requirements. Please check your connection and try again.</p>
                            </div>
                        `;
                });
        }

        // Open file input for requirement
        function openFileInput(requirementId) {
            const index = requirementId.split('_')[1];
            document.getElementById('file_input_' + index).click();
        }

        // Handle requirement file upload
        function handleRequirementFileUpload(index, fileInput) {
            const file = fileInput.files[0];
            const uploadArea = document.getElementById('upload_area_' + index);
            const previewContainer = document.getElementById('requirement_preview_' + index);
            const errorContainer = document.getElementById('requirement_error_' + index);
            const requirementContainer = document.getElementById('req_container_' + index);

            // Reset states
            errorContainer.style.display = 'none';
            requirementContainer.classList.remove('error', 'completed');

            if (!file) {
                return;
            }

            // Validate file type
            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                errorContainer.textContent = 'Invalid file type. Only PDF, JPG, and PNG files are allowed.';
                errorContainer.style.display = 'block';
                requirementContainer.classList.add('error');
                fileInput.value = '';
                return;
            }

            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                errorContainer.textContent = 'File is too large. Maximum size is 5MB.';
                errorContainer.style.display = 'block';
                requirementContainer.classList.add('error');
                fileInput.value = '';
                return;
            }

            // Store file in requirementUploads
            const requirementId = 'req_' + index;
            requirementUploads[requirementId] = {
                requirementName: fileInput.previousElementSibling.value,
                file: file
            };

            // Show preview
            uploadArea.style.display = 'none';
            previewContainer.style.display = 'block';
            previewContainer.innerHTML = `
                    <div class="requirement-file-info">
                        <div class="requirement-file-icon">
                            ${getFileIcon(file.type)}
                        </div>
                        <div class="requirement-file-details">
                            <div class="requirement-file-name">${escapeHtml(file.name)}</div>
                            <div style="font-size: 12px; color: #666;">
                                ${formatFileSize(file.size)} • ${getFileType(file.type)}
                            </div>
                        </div>
                        <div class="requirement-file-remove" onclick="removeRequirementFile(${index})">
                            <i class="fas fa-times"></i>
                        </div>
                    </div>
                    <div class="requirement-status completed">
                        <i class="fas fa-check-circle"></i> File uploaded successfully
                    </div>
                `;

            // Mark as completed
            requirementContainer.classList.add('completed');
        }

        // Remove requirement file
        function removeRequirementFile(index) {
            const requirementId = 'req_' + index;
            const fileInput = document.getElementById('file_input_' + index);
            const uploadArea = document.getElementById('upload_area_' + index);
            const previewContainer = document.getElementById('requirement_preview_' + index);
            const requirementContainer = document.getElementById('req_container_' + index);

            // Clear file
            requirementUploads[requirementId] = {
                requirementName: fileInput.previousElementSibling.value,
                file: null
            };
            fileInput.value = '';

            // Reset UI
            uploadArea.style.display = 'block';
            previewContainer.style.display = 'none';
            previewContainer.innerHTML = '';
            requirementContainer.classList.remove('completed', 'error');
        }

        // Helper functions for file handling
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

        // Initialize additional files upload
        function initializeAdditionalFilesUpload() {
            const dropArea = document.getElementById('drop-area');
            const fileInput = document.getElementById('attachments');
            additionalFileInput = fileInput; // Store reference
            const allowed = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
            const maxSize = 5 * 1024 * 1024;

            // Store original event listener
            const originalOnChange = fileInput.onchange;

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
                handleAdditionalFiles(files);
            }

            // Handle file input change
            fileInput.addEventListener('change', function (e) {
                handleAdditionalFiles(e.target.files);
            });

            function handleAdditionalFiles(files) {
                const newFiles = [...files];
                newFiles.forEach(file => {
                    // Validate file type
                    if (!allowed.includes(file.type)) {
                        alert(`File "${file.name}" is not a valid file type. Only PDF, JPG, and PNG files are allowed.`);
                        return;
                    }

                    // Validate file size
                    if (file.size > maxSize) {
                        alert(`File "${file.name}" is too large. Maximum size is 5MB.`);
                        return;
                    }

                    // Check if file already exists
                    const fileExists = additionalFiles.some(existingFile =>
                        existingFile.file.name === file.name && existingFile.file.size === file.size
                    );

                    if (!fileExists) {
                        additionalFiles.push({
                            id: Date.now() + Math.random(),
                            file: file
                        });
                    }
                });

                updateAdditionalFilesPreview();
                updateAdditionalFilesInput();
            }

            function updateAdditionalFilesPreview() {
                const fileList = document.getElementById('additional-files-list');
                const previewContainer = document.getElementById('additional-files-preview');

                if (additionalFiles.length === 0) {
                    fileList.innerHTML = '<p class="no-files" style="text-align: center; color: #666; padding: 20px;">No additional files uploaded</p>';
                    previewContainer.style.display = 'none';
                    return;
                }

                previewContainer.style.display = 'block';
                fileList.innerHTML = '';

                additionalFiles.forEach((fileObj, index) => {
                    const file = fileObj.file;

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
                                </div>
                            </div>
                        </div>
                        <button type="button" class="remove-file" onclick="removeAdditionalFile(${index})" title="Remove file">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    fileList.appendChild(fileItem);
                });
            }

            window.removeAdditionalFile = function (index) {
                additionalFiles.splice(index, 1);
                updateAdditionalFilesPreview();
                updateAdditionalFilesInput();
            }

            function updateAdditionalFilesInput() {
                // Create a new DataTransfer object
                const dataTransfer = new DataTransfer();
                
                // Add all files from additionalFiles array
                additionalFiles.forEach(fileObj => {
                    dataTransfer.items.add(fileObj.file);
                });
                
                // Update the file input
                fileInput.files = dataTransfer.files;
                
                // Trigger change event if there are files
                if (additionalFiles.length > 0) {
                    const event = new Event('change', { bubbles: true });
                    fileInput.dispatchEvent(event);
                }
            }

            // Initial preview update
            updateAdditionalFilesPreview();
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

                // Check graduation years (skip for college year grad as it's optional)
                if (input.name.includes('yr_grad') && !input.name.includes('college')) {
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
                    if (input.name.includes('cellNo') || input.name.includes('cellphone')) {
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
                // Check if all requirements have files
                let hasMissingRequirements = false;
                Object.keys(requirementUploads).forEach(reqId => {
                    if (!requirementUploads[reqId] || !requirementUploads[reqId].file) {
                        const index = reqId.split('_')[1];
                        const errorContainer = document.getElementById('requirement_error_' + index);
                        if (errorContainer) {
                            errorContainer.textContent = 'This file is required';
                            errorContainer.style.display = 'block';
                        }
                        const requirementContainer = document.getElementById('req_container_' + index);
                        if (requirementContainer) {
                            requirementContainer.classList.add('error');
                        }
                        hasMissingRequirements = true;
                    }
                });

                if (hasMissingRequirements) {
                    const errorMessage = currentSection.querySelector('.error-message');
                    if (errorMessage) errorMessage.style.display = 'block';
                    isValid = false;
                    errorMessages.push('Please upload all required documents');
                }
            }

            if (!isValid) {
                // Show section error message
                if (errorDiv && errorMessages.length > 0) {
                    errorDiv.innerHTML = '<strong>Please fill up the following required fields:</strong><ul>' +
                        errorMessages.map(error => `<li>${error}</li>`).join('') + '</ul>';
                    errorDiv.style.display = 'block';

                    // Scroll to error message
                    errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return false;
            }

            return true;
        }
        // Function to validate reason textarea and update character count
        function validateReasonTextarea() {
            const textarea = document.getElementById('reason_scholarship');
            const charCount = document.getElementById('charCount');
            const minChars = 50;

            if (textarea && charCount) {
                const currentLength = textarea.value.trim().length;
                charCount.textContent = currentLength;

                // Update color based on length
                if (currentLength >= minChars) {
                    charCount.style.color = '#28a745';
                } else if (currentLength > 0) {
                    charCount.style.color = '#ff9800';
                } else {
                    charCount.style.color = '#666';
                }

                // Also add/remove error class on the parent row
                const parentRow = textarea.closest('.form-row');
                if (parentRow) {
                    const errorMessage = parentRow.querySelector('.error-message');
                    if (currentLength < minChars) {
                        parentRow.classList.add('error');
                        if (errorMessage) errorMessage.style.display = 'block';
                        if (currentLength > 0) {
                            errorMessage.textContent = `Please enter at least ${minChars - currentLength} more characters`;
                        } else {
                            errorMessage.textContent = 'Please explain why you need a scholarship (minimum 50 characters)';
                        }
                    } else {
                        parentRow.classList.remove('error');
                        if (errorMessage) errorMessage.style.display = 'none';
                    }
                }
            }
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

                    // If moving to attachments section, load grant requirements and update folder preview
                    if (currentStep === 6) {
                        const grantSelect = document.querySelector('select[name="scholarship_grant"]');
                        if (grantSelect && grantSelect.value) {
                            loadGrantRequirements(grantSelect.value);
                        }
                        updateFolderNamePreview();
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
                        if ((input.name.includes('cellNo') || input.name.includes('cellphone')) && input.value.trim()) {
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

            // Validate requirement uploads
            let hasMissingRequirements = false;
            Object.keys(requirementUploads).forEach(reqId => {
                if (!requirementUploads[reqId] || !requirementUploads[reqId].file) {
                    const index = reqId.split('_')[1];
                    const errorContainer = document.getElementById('requirement_error_' + index);
                    if (errorContainer) {
                        errorContainer.textContent = 'This file is required';
                        errorContainer.style.display = 'block';
                    }
                    const requirementContainer = document.getElementById('req_container_' + index);
                    if (requirementContainer) {
                        requirementContainer.classList.add('error');
                    }
                    hasMissingRequirements = true;
                    allErrors.push(`Please upload file for: ${requirementUploads[reqId]?.requirementName || 'required document'}`);
                }
            });

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
            fileCountInput.value = Object.keys(requirementUploads).length + additionalFiles.length;

            // Remove existing file_count input if it exists
            const existingFileCount = document.querySelector('input[name="file_count"]');
            if (existingFileCount) {
                existingFileCount.remove();
            }

            document.getElementById('checkout-form').appendChild(fileCountInput);

            // If all validations pass, submit the form
            return true;
        }

        // Initialize everything when DOM is loaded
        document.addEventListener('DOMContentLoaded', function () {
            // Check all dropdowns that have "others" option
            const religionSelect = document.getElementById('religion');
            if (religionSelect) {
                toggleOtherField(religionSelect, 'religion_other');
            }
            // Initialize character counter for scholarship reason
            validateReasonTextarea();
            const indigenousSelect = document.getElementById('indigenous_group');
            if (indigenousSelect) {
                toggleOtherField(indigenousSelect, 'indigenous_group_other');
            }

            const sexSelect = document.getElementById('sex');
            if (sexSelect) {
                toggleOtherField(sexSelect, 'sex_other');
            }

            // Update folder name when full name changes
            const fullNameInput = document.getElementById('fullNameField');
            if (fullNameInput) {
                fullNameInput.addEventListener('input', updateFolderNamePreview);
            }

            // Load requirements when scholarship grant is selected
            const grantSelect = document.querySelector('select[name="scholarship_grant"]');
            if (grantSelect) {
                grantSelect.addEventListener('change', function () {
                    loadGrantRequirements(this.value);
                });

                // Load requirements for initially selected grant (if any)
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

            // Initialize additional files upload system
            initializeAdditionalFilesUpload();

            // Update folder name preview initially
            updateFolderNamePreview();

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

            // Calculate age if date of birth is pre-filled
            const dobInput = document.getElementById('date_of_birth');
            if (dobInput && dobInput.value) {
                calculateAge();
            }
        });
    </script>
</body>

</html>