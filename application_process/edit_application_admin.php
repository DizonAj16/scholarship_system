<?php
include '../includes/session.php';

// Validate application ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: applications.php");
    exit;
}

$application_id = (int) $_GET['id'];

// Fetch dropdown data
$sem_sy_list = $pdo->query("SELECT * FROM dropdown_sem_sy ORDER BY school_year DESC, semester ASC")->fetchAll(PDO::FETCH_ASSOC);
$course_major_list = $pdo->query("SELECT * FROM dropdown_course_major ORDER BY course ASC, major ASC")->fetchAll(PDO::FETCH_ASSOC);
$scholarship_grant_list = $pdo->query("SELECT * FROM dropdown_scholarship_grant ORDER BY grant_name  ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch application data
try {
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
            u.username
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
        $uploadedFiles = json_decode($fileData['files'], true);
    }

} catch (PDOException $e) {
    die("Error fetching application details: " . $e->getMessage());
}

// Handle form submission for update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // Update scholarship_applications table
        $stmt = $pdo->prepare("
            UPDATE scholarship_applications SET
                semester = :semester,
                school_year = :school_year,
                full_name = :full_name,
                course = :course,
                major = :major,
                yr_sec = :yr_sec,
                cell_no = :cell_no,
                permanent_address = :permanent_address,
                present_address = :present_address,
                zip_code = :zip_code,
                email = :email,
                sex = :sex,
                date_of_birth = :date_of_birth,
                age = :age,
                place_of_birth = :place_of_birth,
                civil_status = :civil_status,
                religion = :religion,
                scholarship_grant = :scholarship_grant,
                disability = :disability,
                indigenous_group = :indigenous_group,
                reason_scholarship = :reason_scholarship,
                status = :status
            WHERE application_id = :application_id
        ");

        $stmt->execute([
            ':semester' => $_POST['semester'],
            ':school_year' => $_POST['school_year'],
            ':full_name' => $_POST['full_name'],
            ':course' => $_POST['course'],
            ':major' => $_POST['major'],
            ':yr_sec' => $_POST['yr_sec'],
            ':cell_no' => $_POST['cell_no'],
            ':permanent_address' => $_POST['permanent_address'],
            ':present_address' => $_POST['present_address'],
            ':zip_code' => $_POST['zip_code'],
            ':email' => $_POST['email'],
            ':sex' => $_POST['sex'],
            ':date_of_birth' => $_POST['date_of_birth'],
            ':age' => $_POST['age'],
            ':place_of_birth' => $_POST['place_of_birth'],
            ':civil_status' => $_POST['civil_status'],
            ':religion' => $_POST['religion'],
            ':scholarship_grant' => $_POST['scholarship_grant'],
            ':disability' => $_POST['disability'],
            ':indigenous_group' => $_POST['indigenous_group'],
            ':reason_scholarship' => $_POST['reason_scholarship'],
            ':status' => $_POST['status'],
            ':application_id' => $application_id
        ]);

        // Update schools_attended table
        $stmt = $pdo->prepare("
            UPDATE schools_attended SET
                elementary = :elementary,
                elementary_year_grad = :elementary_year_grad,
                elementary_honors = :elementary_honors,
                secondary = :secondary,
                secondary_year_grad = :secondary_year_grad,
                secondary_honors = :secondary_honors,
                college = :college,
                college_year_grad = :college_year_grad,
                college_honors = :college_honors
            WHERE application_id = :application_id
        ");

        $stmt->execute([
            ':elementary' => $_POST['elementary'],
            ':elementary_year_grad' => $_POST['elementary_year_grad'],
            ':elementary_honors' => $_POST['elementary_honors'],
            ':secondary' => $_POST['secondary'],
            ':secondary_year_grad' => $_POST['secondary_year_grad'],
            ':secondary_honors' => $_POST['secondary_honors'],
            ':college' => $_POST['college'],
            ':college_year_grad' => $_POST['college_year_grad'],
            ':college_honors' => $_POST['college_honors'],
            ':application_id' => $application_id
        ]);

        // Update parents_info table
        $stmt = $pdo->prepare("
            UPDATE parents_info SET
                father_lastname = :father_lastname,
                father_givenname = :father_givenname,
                father_middlename = :father_middlename,
                father_cellphone = :father_cellphone,
                father_education = :father_education,
                father_occupation = :father_occupation,
                father_income = :father_income,
                mother_lastname = :mother_lastname,
                mother_givenname = :mother_givenname,
                mother_middlename = :mother_middlename,
                mother_cellphone = :mother_cellphone,
                mother_education = :mother_education,
                mother_occupation = :mother_occupation,
                mother_income = :mother_income
            WHERE application_id = :application_id
        ");

        $stmt->execute([
            ':father_lastname' => $_POST['father_lastname'],
            ':father_givenname' => $_POST['father_givenname'],
            ':father_middlename' => $_POST['father_middlename'],
            ':father_cellphone' => $_POST['father_cellphone'],
            ':father_education' => $_POST['father_education'],
            ':father_occupation' => $_POST['father_occupation'],
            ':father_income' => $_POST['father_income'],
            ':mother_lastname' => $_POST['mother_lastname'],
            ':mother_givenname' => $_POST['mother_givenname'],
            ':mother_middlename' => $_POST['mother_middlename'],
            ':mother_cellphone' => $_POST['mother_cellphone'],
            ':mother_education' => $_POST['mother_education'],
            ':mother_occupation' => $_POST['mother_occupation'],
            ':mother_income' => $_POST['mother_income'],
            ':application_id' => $application_id
        ]);

        // Update house_info table
        $stmt = $pdo->prepare("
            UPDATE house_info SET
                house_status = :house_status
            WHERE application_id = :application_id
        ");

        $stmt->execute([
            ':house_status' => $_POST['house_status'],
            ':application_id' => $application_id
        ]);

        // Handle file uploads
        if (!empty($_FILES['attachments']['name'][0])) {
            $uploadDir = "../uploads/";
            $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
            $newFiles = [];

            for ($i = 0; $i < count($_FILES['attachments']['name']); $i++) {
                $fileName = $_FILES['attachments']['name'][$i];
                $fileTmp = $_FILES['attachments']['tmp_name'][$i];
                $fileSize = $_FILES['attachments']['size'][$i];
                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if (!in_array($ext, $allowed)) continue;
                if ($fileSize > 5 * 1024 * 1024) continue;

                $uniqueName = time() . "_" . uniqid() . "." . $ext;
                $uploadPath = $uploadDir . $uniqueName;

                if (move_uploaded_file($fileTmp, $uploadPath)) {
                    $newFiles[] = $uniqueName;
                }
            }

            // Merge existing files with new files
            $allFiles = array_merge($uploadedFiles, $newFiles);
            $fileListJSON = json_encode($allFiles);

            // Update or insert files
            $stmt = $pdo->prepare("
                INSERT INTO scholarship_files (application_id, files) 
                VALUES (:application_id, :files)
                ON DUPLICATE KEY UPDATE files = :files
            ");
            $stmt->execute([
                ':application_id' => $application_id,
                ':files' => $fileListJSON
            ]);
        }

        $pdo->commit();
        
        // Log the activity
        $action = "Application updated by admin";
        $details = "Application ID $application_id updated by " . $_SESSION['username'];
        logActivity($pdo, $_SESSION['id'], $action, $details);

        $_SESSION['success_message'] = 'Application updated successfully.';
        header("Location: view_application_admin.php?id=" . $application_id);
        exit();

    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error_message'] = 'Error updating application: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Application</title>

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

        h1 {
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .form-section {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .form-section h2 {
            color: #555;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }

        .form-row {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .form-row label {
            width: 200px;
            font-weight: bold;
            color: #333;
        }

        .form-row input[type="text"],
        .form-row input[type="email"],
        .form-row input[type="tel"],
        .form-row input[type="number"],
        .form-row input[type="date"],
        .form-row select,
        .form-row textarea {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-row textarea {
            min-height: 100px;
            resize: vertical;
        }

        .form-row input[readonly] {
            background-color: #f5f5f5;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-save, .btn-cancel, .btn-delete {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-save {
            background: #4CAF50;
            color: white;
        }

        .btn-save:hover {
            background: #45a049;
        }

        .btn-cancel {
            background: #6c757d;
            color: white;
        }

        .btn-cancel:hover {
            background: #5a6268;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background: #c82333;
        }

        .file-list {
            margin-top: 10px;
        }

        .file-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 5px;
        }

        .file-item button {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 4px 8px;
            cursor: pointer;
            font-size: 12px;
        }

        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
        <h1>Edit Application #<?= htmlspecialchars($application['application_id']) ?></h1>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success_message']) ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($_SESSION['error_message']) ?></div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <!-- Application Status -->
            <div class="form-section">
                <h2>Application Status</h2>
                <div class="form-row">
                    <label for="status">Status:</label>
                    <select name="status" id="status" required>
                        <option value="pending" <?= $application['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="approved" <?= $application['status'] == 'approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="not qualified" <?= $application['status'] == 'not qualified' ? 'selected' : '' ?>>Not Qualified</option>
                    </select>
                </div>
            </div>

            <!-- Scholarship Information -->
            <div class="form-section">
                <h2>Scholarship Information</h2>
                <div class="form-row">
                    <label for="semester">Semester:</label>
                    <select name="semester" id="semester" required>
                        <?php foreach ($sem_sy_list as $sem): ?>
                            <option value="<?= htmlspecialchars($sem['semester']) ?>" 
                                <?= $application['semester'] == $sem['semester'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sem['semester']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-row">
                    <label for="school_year">School Year:</label>
                    <select name="school_year" id="school_year" required>
                        <?php foreach ($sem_sy_list as $sy): ?>
                            <option value="<?= htmlspecialchars($sy['school_year']) ?>" 
                                <?= $application['school_year'] == $sy['school_year'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sy['school_year']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-row">
                    <label for="scholarship_grant">Scholarship Grant:</label>
                    <select name="scholarship_grant" id="scholarship_grant" required>
                        <?php foreach ($scholarship_grant_list as $grant): ?>
                            <option value="<?= htmlspecialchars($grant['grant_name']) ?>" 
                                <?= $application['scholarship_grant'] == $grant['grant_name'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($grant['grant_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-row">
                    <label for="course">Course:</label>
                    <select name="course" id="course" required>
                        <?php foreach ($course_major_list as $cm): ?>
                            <option value="<?= htmlspecialchars($cm['course']) ?>" 
                                <?= $application['course'] == $cm['course'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cm['course']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-row">
                    <label for="major">Major:</label>
                    <select name="major" id="major" required>
                        <?php foreach ($course_major_list as $cm): ?>
                            <option value="<?= htmlspecialchars($cm['major']) ?>" 
                                <?= $application['major'] == $cm['major'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cm['major']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-row">
                    <label for="yr_sec">Year/Section:</label>
                    <input type="text" name="yr_sec" id="yr_sec" value="<?= htmlspecialchars($application['yr_sec']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="reason_scholarship">Reason for Scholarship:</label>
                    <textarea name="reason_scholarship" id="reason_scholarship" required><?= htmlspecialchars($application['reason_scholarship']) ?></textarea>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="form-section">
                <h2>Personal Information</h2>
                <div class="form-row">
                    <label for="full_name">Full Name:</label>
                    <input type="text" name="full_name" id="full_name" value="<?= htmlspecialchars($application['full_name']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" value="<?= htmlspecialchars($application['email']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="cell_no">Phone:</label>
                    <input type="tel" name="cell_no" id="cell_no" value="<?= htmlspecialchars($application['cell_no']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="sex">Sex:</label>
                    <select name="sex" id="sex" required>
                        <option value="male" <?= $application['sex'] == 'male' ? 'selected' : '' ?>>Male</option>
                        <option value="female" <?= $application['sex'] == 'female' ? 'selected' : '' ?>>Female</option>
                        <option value="others" <?= $application['sex'] == 'others' ? 'selected' : '' ?>>Others</option>
                    </select>
                </div>
                <div class="form-row">
                    <label for="date_of_birth">Date of Birth:</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" value="<?= htmlspecialchars($application['date_of_birth']) ?>" onchange="calculateAge()" required>
                </div>
                <div class="form-row">
                    <label for="age">Age:</label>
                    <input type="number" name="age" id="age" value="<?= htmlspecialchars($application['age']) ?>" readonly>
                </div>
                <div class="form-row">
                    <label for="place_of_birth">Place of Birth:</label>
                    <input type="text" name="place_of_birth" id="place_of_birth" value="<?= htmlspecialchars($application['place_of_birth']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="civil_status">Civil Status:</label>
                    <select name="civil_status" id="civil_status" required>
                        <option value="single" <?= $application['civil_status'] == 'single' ? 'selected' : '' ?>>Single</option>
                        <option value="married" <?= $application['civil_status'] == 'married' ? 'selected' : '' ?>>Married</option>
                        <option value="widowed" <?= $application['civil_status'] == 'widowed' ? 'selected' : '' ?>>Widowed</option>
                        <option value="divorced" <?= $application['civil_status'] == 'divorced' ? 'selected' : '' ?>>Divorced</option>
                        <option value="separated" <?= $application['civil_status'] == 'separated' ? 'selected' : '' ?>>Separated</option>
                    </select>
                </div>
                <div class="form-row">
                    <label for="religion">Religion:</label>
                    <select name="religion" id="religion" required>
                        <option value="roman catholic" <?= $application['religion'] == 'roman catholic' ? 'selected' : '' ?>>Roman Catholic</option>
                        <option value="islam" <?= $application['religion'] == 'islam' ? 'selected' : '' ?>>Islam</option>
                        <option value="iglesia ni cristo" <?= $application['religion'] == 'iglesia ni cristo' ? 'selected' : '' ?>>Iglesia ni Cristo</option>
                        <option value="evangelical christian" <?= $application['religion'] == 'evangelical christian' ? 'selected' : '' ?>>Evangelical Christian</option>
                        <option value="a biblical church" <?= $application['religion'] == 'a biblical church' ? 'selected' : '' ?>>Aglipayan / Philippine Independent Church</option>
                        <option value="others" <?= $application['religion'] == 'others' ? 'selected' : '' ?>>Others</option>
                    </select>
                </div>
                <div class="form-row">
                    <label for="disability">Disability:</label>
                    <input type="text" name="disability" id="disability" value="<?= htmlspecialchars($application['disability']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="indigenous_group">Indigenous Group:</label>
                    <select name="indigenous_group" id="indigenous_group" required>
                        <option value="igorot" <?= $application['indigenous_group'] == 'igorot' ? 'selected' : '' ?>>Igorot</option>
                        <option value="lumad" <?= $application['indigenous_group'] == 'lumad' ? 'selected' : '' ?>>Lumad</option>
                        <option value="moro" <?= $application['indigenous_group'] == 'moro' ? 'selected' : '' ?>>Moro</option>
                        <option value="aeta" <?= $application['indigenous_group'] == 'aeta' ? 'selected' : '' ?>>Aeta</option>
                        <option value="badjao" <?= $application['indigenous_group'] == 'badjao' ? 'selected' : '' ?>>Badjao</option>
                        <option value="others" <?= $application['indigenous_group'] == 'others' ? 'selected' : '' ?>>Others</option>
                        <option value="N/A" <?= $application['indigenous_group'] == 'N/A' ? 'selected' : '' ?>>Not Applicable</option>
                    </select>
                </div>
                <div class="form-row">
                    <label for="permanent_address">Permanent Address:</label>
                    <textarea name="permanent_address" id="permanent_address" required><?= htmlspecialchars($application['permanent_address']) ?></textarea>
                </div>
                <div class="form-row">
                    <label for="present_address">Present Address:</label>
                    <textarea name="present_address" id="present_address" required><?= htmlspecialchars($application['present_address']) ?></textarea>
                </div>
                <div class="form-row">
                    <label for="zip_code">ZIP Code:</label>
                    <input type="number" name="zip_code" id="zip_code" value="<?= htmlspecialchars($application['zip_code']) ?>" required>
                </div>
            </div>

            <!-- Educational Background -->
            <div class="form-section">
                <h2>Educational Background</h2>
                <div class="form-row">
                    <label for="elementary">Elementary School:</label>
                    <input type="text" name="elementary" id="elementary" value="<?= htmlspecialchars($application['elementary']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="elementary_year_grad">Elementary Year Graduated:</label>
                    <input type="number" name="elementary_year_grad" id="elementary_year_grad" min="1900" max="<?= date('Y') ?>" value="<?= htmlspecialchars($application['elementary_year_grad']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="elementary_honors">Elementary Honors:</label>
                    <input type="text" name="elementary_honors" id="elementary_honors" value="<?= htmlspecialchars($application['elementary_honors']) ?>">
                </div>
                <div class="form-row">
                    <label for="secondary">Secondary School:</label>
                    <input type="text" name="secondary" id="secondary" value="<?= htmlspecialchars($application['secondary']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="secondary_year_grad">Secondary Year Graduated:</label>
                    <input type="number" name="secondary_year_grad" id="secondary_year_grad" min="1900" max="<?= date('Y') ?>" value="<?= htmlspecialchars($application['secondary_year_grad']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="secondary_honors">Secondary Honors:</label>
                    <input type="text" name="secondary_honors" id="secondary_honors" value="<?= htmlspecialchars($application['secondary_honors']) ?>">
                </div>
                <div class="form-row">
                    <label for="college">College:</label>
                    <input type="text" name="college" id="college" value="<?= htmlspecialchars($application['college']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="college_year_grad">College Year Graduated:</label>
                    <input type="number" name="college_year_grad" id="college_year_grad" min="1900" max="<?= date('Y') ?>" value="<?= htmlspecialchars($application['college_year_grad']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="college_honors">College Honors:</label>
                    <input type="text" name="college_honors" id="college_honors" value="<?= htmlspecialchars($application['college_honors']) ?>">
                </div>
            </div>

            <!-- Family Information -->
            <div class="form-section">
                <h2>Family Information</h2>
                <h3>Father's Information</h3>
                <div class="form-row">
                    <label for="father_lastname">Last Name:</label>
                    <input type="text" name="father_lastname" id="father_lastname" value="<?= htmlspecialchars($application['father_lastname']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="father_givenname">Given Name:</label>
                    <input type="text" name="father_givenname" id="father_givenname" value="<?= htmlspecialchars($application['father_givenname']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="father_middlename">Middle Name:</label>
                    <input type="text" name="father_middlename" id="father_middlename" value="<?= htmlspecialchars($application['father_middlename']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="father_cellphone">Phone:</label>
                    <input type="tel" name="father_cellphone" id="father_cellphone" value="<?= htmlspecialchars($application['father_cellphone']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="father_education">Education:</label>
                    <select name="father_education" id="father_education" required>
                        <?php
                        $educationLevels = ['No Formal Education', 'Elementary Undergraduate', 'Elementary Graduate', 
                                           'High School Undergraduate', 'High School Graduate', 'Vocational Course',
                                           'College Undergraduate', 'College Graduate', 'Postgraduate'];
                        foreach ($educationLevels as $level): ?>
                            <option value="<?= $level ?>" <?= $application['father_education'] == $level ? 'selected' : '' ?>>
                                <?= $level ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-row">
                    <label for="father_occupation">Occupation:</label>
                    <select name="father_occupation" id="father_occupation" required>
                        <?php
                        $occupations = ['Government', 'Private Sector', 'Self-Employed', 'Laborer', 'Freelancer',
                                       'NGO/Non-Profit', 'Overseas Employment', 'Casual', 'Contractual', 'Intern'];
                        foreach ($occupations as $occupation): ?>
                            <option value="<?= $occupation ?>" <?= $application['father_occupation'] == $occupation ? 'selected' : '' ?>>
                                <?= $occupation ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-row">
                    <label for="father_income">Monthly Income:</label>
                    <input type="number" name="father_income" id="father_income" step="0.01" value="<?= htmlspecialchars($application['father_income']) ?>" required>
                </div>

                <h3>Mother's Information</h3>
                <div class="form-row">
                    <label for="mother_lastname">Last Name:</label>
                    <input type="text" name="mother_lastname" id="mother_lastname" value="<?= htmlspecialchars($application['mother_lastname']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="mother_givenname">Given Name:</label>
                    <input type="text" name="mother_givenname" id="mother_givenname" value="<?= htmlspecialchars($application['mother_givenname']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="mother_middlename">Middle Name:</label>
                    <input type="text" name="mother_middlename" id="mother_middlename" value="<?= htmlspecialchars($application['mother_middlename']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="mother_cellphone">Phone:</label>
                    <input type="tel" name="mother_cellphone" id="mother_cellphone" value="<?= htmlspecialchars($application['mother_cellphone']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="mother_education">Education:</label>
                    <select name="mother_education" id="mother_education" required>
                        <?php foreach ($educationLevels as $level): ?>
                            <option value="<?= $level ?>" <?= $application['mother_education'] == $level ? 'selected' : '' ?>>
                                <?= $level ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-row">
                    <label for="mother_occupation">Occupation:</label>
                    <select name="mother_occupation" id="mother_occupation" required>
                        <?php foreach ($occupations as $occupation): ?>
                            <option value="<?= $occupation ?>" <?= $application['mother_occupation'] == $occupation ? 'selected' : '' ?>>
                                <?= $occupation ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-row">
                    <label for="mother_income">Monthly Income:</label>
                    <input type="number" name="mother_income" id="mother_income" step="0.01" value="<?= htmlspecialchars($application['mother_income']) ?>" required>
                </div>
            </div>

            <!-- Housing Information -->
            <div class="form-section">
                <h2>Housing Information</h2>
                <div class="form-row">
                    <label for="house_status">House Status:</label>
                    <select name="house_status" id="house_status" required>
                        <option value="Owned" <?= $application['house_status'] == 'Owned' ? 'selected' : '' ?>>Owned</option>
                        <option value="Rented" <?= $application['house_status'] == 'Rented' ? 'selected' : '' ?>>Rented</option>
                        <option value="Living with relatives" <?= $application['house_status'] == 'Living with relatives' ? 'selected' : '' ?>>Living with relatives</option>
                        <option value="Others" <?= $application['house_status'] == 'Others' ? 'selected' : '' ?>>Others</option>
                    </select>
                </div>
            </div>

            <!-- Attached Files -->
            <div class="form-section">
                <h2>Attached Documents</h2>
                <?php if (!empty($uploadedFiles)): ?>
                    <div class="file-list">
                        <p>Existing Files:</p>
                        <?php foreach ($uploadedFiles as $index => $file): ?>
                            <div class="file-item">
                                <span><?= htmlspecialchars($file) ?></span>
                                <a href="../uploads/<?= htmlspecialchars($file) ?>" download>Download</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <div class="form-row">
                    <label for="attachments">Add More Files:</label>
                    <input type="file" name="attachments[]" id="attachments" multiple accept=".pdf,.jpg,.jpeg,.png">
                    <small>Max 5MB per file. Allowed: PDF, JPG, PNG</small>
                </div>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="../views/applications.php?id=<?= $application_id ?>" class="btn-cancel">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <a href="delete_application.php?id=<?= $application_id ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this application? This action cannot be undone.')">
                    <i class="fas fa-trash"></i> Delete Application
                </a>
            </div>
        </form>
    </div>

    <script>
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

        // Hide preloader when page loads
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.querySelector('.preloader').style.display = 'none';
            }, 1000);
        });
    </script>
</body>
</html>