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
$scholarship_grant_list = $pdo->query("SELECT * FROM dropdown_scholarship_grant ORDER BY grant_name ASC")->fetchAll(PDO::FETCH_ASSOC);

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

    // Parse income values to determine if they're custom amounts or ranges
    $father_income_data = parseIncomeValue($application['father_income']);
    $mother_income_data = parseIncomeValue($application['mother_income']);

} catch (PDOException $e) {
    die("Error fetching application details: " . $e->getMessage());
}

// Function to parse income value from database
function parseIncomeValue($income)
{
    if (empty($income)) {
        return ['type' => 'range', 'value' => '', 'custom' => ''];
    }

    // Check if it's a range or "Not Applicable"/"Prefer not to say"
    if (strpos($income, '₱') === 0) {
        // Check if it's a range (e.g., "₱0-5000") or custom amount (e.g., "₱32500.50")
        if (strpos($income, '-') !== false || strpos($income, '+') !== false) {
            // It's a range, remove the ₱ symbol
            $range = str_replace('₱', '', $income);
            return ['type' => 'range', 'value' => $range, 'custom' => ''];
        } else {
            // It's a custom amount, extract the number
            $amount = str_replace('₱', '', $income);
            return ['type' => 'custom', 'value' => 'custom', 'custom' => $amount];
        }
    } else if (in_array($income, ['Not Applicable', 'Prefer not to say'])) {
        return ['type' => 'range', 'value' => $income, 'custom' => ''];
    }

    // Default to range
    return ['type' => 'range', 'value' => $income, 'custom' => ''];
}

// Handle form submission for update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // Process income values
        $father_income = '';
        $mother_income = '';

        // Process father's income
        if ($_POST['father_income'] === 'custom' && !empty($_POST['father_income_custom'])) {
            $father_income = '₱' . number_format(floatval($_POST['father_income_custom']), 2, '.', '');
        } else if (in_array($_POST['father_income'], ['Not Applicable', 'Prefer not to say'])) {
            $father_income = $_POST['father_income'];
        } else if (!empty($_POST['father_income'])) {
            $father_income = '₱' . $_POST['father_income'];
        }

        // Process mother's income
        if ($_POST['mother_income'] === 'custom' && !empty($_POST['mother_income_custom'])) {
            $mother_income = '₱' . number_format(floatval($_POST['mother_income_custom']), 2, '.', '');
        } else if (in_array($_POST['mother_income'], ['Not Applicable', 'Prefer not to say'])) {
            $mother_income = $_POST['mother_income'];
        } else if (!empty($_POST['mother_income'])) {
            $mother_income = '₱' . $_POST['mother_income'];
        }

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
        // Handle empty college fields
        $college = !empty($_POST['college']) ? trim($_POST['college']) : '';
        $college_year_grad = !empty($_POST['college_year_grad']) ? (int) $_POST['college_year_grad'] : NULL;
        $college_honors = !empty($_POST['college_honors']) ? trim($_POST['college_honors']) : '';


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
            ':elementary_honors' => $_POST['elementary_honors'] ?? '',
            ':secondary' => $_POST['secondary'],
            ':secondary_year_grad' => $_POST['secondary_year_grad'],
            ':secondary_honors' => $_POST['secondary_honors'] ?? '',
            ':college' => $college,
            ':college_year_grad' => $college_year_grad,
            ':college_honors' => $college_honors,
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
            ':father_income' => $father_income,
            ':mother_lastname' => $_POST['mother_lastname'],
            ':mother_givenname' => $_POST['mother_givenname'],
            ':mother_middlename' => $_POST['mother_middlename'],
            ':mother_cellphone' => $_POST['mother_cellphone'],
            ':mother_education' => $_POST['mother_education'],
            ':mother_occupation' => $_POST['mother_occupation'],
            ':mother_income' => $mother_income,
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
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .form-section h2 {
            color: #555;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }

        .form-section h3 {
            color: #666;
            margin: 15px 0 10px 0;
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

        .income-custom-field {
            margin-top: 10px;
            padding: 10px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            display: none;
        }

        .income-custom-field label {
            display: block;
            margin-bottom: 5px;
            font-weight: normal;
            color: #666;
            width: 100%;
        }

        .income-custom-field input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-save,
        .btn-cancel,
        .btn-delete {
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

        .optional-label {
            color: #666;
            font-weight: normal;
        }

        .optional-label::after {
            content: " (Optional)";
            font-weight: normal;
            color: #666;
            font-size: 12px;
        }

        .form-row.hidden {
            display: none;
        }

        .specify-field {
            margin-top: 10px;
            padding: 10px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            display: none;
        }

        .specify-field label {
            display: block;
            margin-bottom: 5px;
            font-weight: normal;
            color: #666;
            width: 100%;
        }

        .specify-field input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .house-status-group {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }

        .house-option {
            display: flex;
            align-items: center;
        }

        .house-option input[type="radio"] {
            margin-right: 8px;
        }
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

        <form method="POST">
            <!-- Application Status -->
            <div class="form-section">
                <h2>Application Status</h2>
                <div class="form-row">
                    <label for="status">Status:</label>
                    <select name="status" id="status" required>
                        <option value="pending" <?= $application['status'] == 'pending' ? 'selected' : '' ?>>Pending
                        </option>
                        <option value="approved" <?= $application['status'] == 'approved' ? 'selected' : '' ?>>Approved
                        </option>
                        <option value="not qualified" <?= $application['status'] == 'not qualified' ? 'selected' : '' ?>>
                            Not Qualified</option>
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
                            <option value="<?= htmlspecialchars($cm['course']) ?>" <?= $application['course'] == $cm['course'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cm['course']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-row">
                    <label for="major">Major:</label>
                    <select name="major" id="major" required>
                        <?php foreach ($course_major_list as $cm): ?>
                            <option value="<?= htmlspecialchars($cm['major']) ?>" <?= $application['major'] == $cm['major'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cm['major']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-row">
                    <label for="yr_sec">Year/Section:</label>
                    <input type="text" name="yr_sec" id="yr_sec" value="<?= htmlspecialchars($application['yr_sec']) ?>"
                        required>
                </div>
                <div class="form-row">
                    <label for="reason_scholarship">Reason for Scholarship:</label>
                    <textarea name="reason_scholarship" id="reason_scholarship"
                        required><?= htmlspecialchars($application['reason_scholarship']) ?></textarea>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="form-section">
                <h2>Personal Information</h2>
                <div class="form-row">
                    <label for="full_name">Full Name:</label>
                    <input type="text" name="full_name" id="full_name"
                        value="<?= htmlspecialchars($application['full_name']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" value="<?= htmlspecialchars($application['email']) ?>"
                        required>
                </div>
                <div class="form-row">
                    <label for="cell_no">Phone:</label>
                    <input type="tel" name="cell_no" id="cell_no"
                        value="<?= htmlspecialchars($application['cell_no']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="sex">Sex:</label>
                    <select name="sex" id="sex" required onchange="toggleOtherField(this, 'sex_other')">
                        <option value="" disabled>Select Sex</option>
                        <option value="male" <?= $application['sex'] == 'male' ? 'selected' : '' ?>>Male</option>
                        <option value="female" <?= $application['sex'] == 'female' ? 'selected' : '' ?>>Female</option>
                        <option value="others" <?= !in_array($application['sex'], ['male', 'female']) && !empty($application['sex']) ? 'selected' : '' ?>>Others</option>
                    </select>
                    <div class="specify-field" id="sex_other_field">
                        <label for="sex_other">Please specify:</label>
                        <input type="text" name="sex_other" id="sex_other"
                            value="<?= !in_array($application['sex'], ['male', 'female']) && !empty($application['sex']) ? htmlspecialchars($application['sex']) : '' ?>"
                            placeholder="Please specify your gender">
                    </div>
                </div>
                <div class="form-row">
                    <label for="date_of_birth">Date of Birth:</label>
                    <input type="date" name="date_of_birth" id="date_of_birth"
                        value="<?= htmlspecialchars($application['date_of_birth']) ?>" onchange="calculateAge()"
                        required>
                </div>
                <div class="form-row">
                    <label for="age">Age:</label>
                    <input type="number" name="age" id="age" value="<?= htmlspecialchars($application['age']) ?>"
                        readonly>
                </div>
                <div class="form-row">
                    <label for="place_of_birth">Place of Birth:</label>
                    <input type="text" name="place_of_birth" id="place_of_birth"
                        value="<?= htmlspecialchars($application['place_of_birth']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="civil_status">Civil Status:</label>
                    <select name="civil_status" id="civil_status" required>
                        <option value="" disabled>Select Civil Status</option>
                        <option value="single" <?= $application['civil_status'] == 'single' ? 'selected' : '' ?>>Single
                        </option>
                        <option value="married" <?= $application['civil_status'] == 'married' ? 'selected' : '' ?>>Married
                        </option>
                        <option value="widowed" <?= $application['civil_status'] == 'widowed' ? 'selected' : '' ?>>Widowed
                        </option>
                        <option value="divorced" <?= $application['civil_status'] == 'divorced' ? 'selected' : '' ?>>
                            Divorced</option>
                        <option value="separated" <?= $application['civil_status'] == 'separated' ? 'selected' : '' ?>>
                            Separated</option>
                    </select>
                </div>
                <div class="form-row">
                    <label for="religion">Religion:</label>
                    <select name="religion" id="religion" required onchange="toggleOtherField(this, 'religion_other')">
                        <option value="" disabled>Select Religion</option>
                        <option value="roman catholic" <?= $application['religion'] == 'roman catholic' ? 'selected' : '' ?>>Roman Catholic</option>
                        <option value="islam" <?= $application['religion'] == 'islam' ? 'selected' : '' ?>>Islam</option>
                        <option value="iglesia ni cristo" <?= $application['religion'] == 'iglesia ni cristo' ? 'selected' : '' ?>>Iglesia ni Cristo</option>
                        <option value="evangelical christian" <?= $application['religion'] == 'evangelical christian' ? 'selected' : '' ?>>Evangelical Christian</option>
                        <option value="a biblical church" <?= $application['religion'] == 'a biblical church' ? 'selected' : '' ?>>Aglipayan / Philippine Independent Church</option>
                        <option value="others" <?= !in_array($application['religion'], ['roman catholic', 'islam', 'iglesia ni cristo', 'evangelical christian', 'a biblical church']) && !empty($application['religion']) ? 'selected' : '' ?>>Others</option>
                    </select>
                    <div class="specify-field" id="religion_other_field">
                        <label for="religion_other">Please specify:</label>
                        <input type="text" name="religion_other" id="religion_other"
                            value="<?= !in_array($application['religion'], ['roman catholic', 'islam', 'iglesia ni cristo', 'evangelical christian', 'a biblical church']) && !empty($application['religion']) ? htmlspecialchars($application['religion']) : '' ?>"
                            placeholder="Please specify your religion">
                    </div>
                </div>
                <div class="form-row">
                    <label for="disability">Disability:</label>
                    <input type="text" name="disability" id="disability"
                        value="<?= htmlspecialchars($application['disability']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="indigenous_group">Indigenous Group:</label>
                    <select name="indigenous_group" id="indigenous_group" required
                        onchange="toggleOtherField(this, 'indigenous_group_other')">
                        <option value="" disabled>Select Indigenous Group</option>
                        <option value="igorot" <?= $application['indigenous_group'] == 'igorot' ? 'selected' : '' ?>>Igorot
                        </option>
                        <option value="lumad" <?= $application['indigenous_group'] == 'lumad' ? 'selected' : '' ?>>Lumad
                        </option>
                        <option value="moro" <?= $application['indigenous_group'] == 'moro' ? 'selected' : '' ?>>Moro
                        </option>
                        <option value="aeta" <?= $application['indigenous_group'] == 'aeta' ? 'selected' : '' ?>>Aeta
                        </option>
                        <option value="badjao" <?= $application['indigenous_group'] == 'badjao' ? 'selected' : '' ?>>Badjao
                        </option>
                        <option value="others" <?= !in_array($application['indigenous_group'], ['igorot', 'lumad', 'moro', 'aeta', 'badjao', 'N/A']) && !empty($application['indigenous_group']) ? 'selected' : '' ?>>Others</option>
                        <option value="N/A" <?= $application['indigenous_group'] == 'N/A' ? 'selected' : '' ?>>Not
                            Applicable</option>
                    </select>
                    <div class="specify-field" id="indigenous_group_other_field">
                        <label for="indigenous_group_other">Please specify:</label>
                        <input type="text" name="indigenous_group_other" id="indigenous_group_other"
                            value="<?= !in_array($application['indigenous_group'], ['igorot', 'lumad', 'moro', 'aeta', 'badjao', 'N/A']) && !empty($application['indigenous_group']) ? htmlspecialchars($application['indigenous_group']) : '' ?>"
                            placeholder="Please specify your indigenous group">
                    </div>
                </div>
                <div class="form-row">
                    <label for="permanent_address">Permanent Address:</label>
                    <textarea name="permanent_address" id="permanent_address"
                        required><?= htmlspecialchars($application['permanent_address']) ?></textarea>
                </div>
                <div class="form-row">
                    <label for="present_address">Present Address:</label>
                    <textarea name="present_address" id="present_address"
                        required><?= htmlspecialchars($application['present_address']) ?></textarea>
                </div>
                <div class="form-row">
                    <label for="zip_code">ZIP Code:</label>
                    <input type="number" name="zip_code" id="zip_code"
                        value="<?= htmlspecialchars($application['zip_code']) ?>" required>
                </div>
            </div>

            <!-- Educational Background -->
            <div class="form-section">
                <h2>Educational Background</h2>
                <div class="form-row">
                    <label for="elementary">Elementary School:</label>
                    <input type="text" name="elementary" id="elementary"
                        value="<?= htmlspecialchars($application['elementary']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="elementary_year_grad">Elementary Year Graduated:</label>
                    <input type="number" name="elementary_year_grad" id="elementary_year_grad" min="1900"
                        max="<?= date('Y') ?>" value="<?= htmlspecialchars($application['elementary_year_grad']) ?>"
                        required>
                </div>
                <div class="form-row">
                    <label for="elementary_honors" class="optional-label">Elementary Honors:</label>
                    <input type="text" name="elementary_honors" id="elementary_honors"
                        value="<?= htmlspecialchars($application['elementary_honors']) ?>">
                </div>
                <div class="form-row">
                    <label for="secondary">Secondary School:</label>
                    <input type="text" name="secondary" id="secondary"
                        value="<?= htmlspecialchars($application['secondary']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="secondary_year_grad">Secondary Year Graduated:</label>
                    <input type="number" name="secondary_year_grad" id="secondary_year_grad" min="1900"
                        max="<?= date('Y') ?>" value="<?= htmlspecialchars($application['secondary_year_grad']) ?>"
                        required>
                </div>
                <div class="form-row">
                    <label for="secondary_honors" class="optional-label">Secondary Honors:</label>
                    <input type="text" name="secondary_honors" id="secondary_honors"
                        value="<?= htmlspecialchars($application['secondary_honors']) ?>">
                </div>
                <div class="form-row">
                    <label for="college" class="optional-label">College:</label>
                    <input type="text" name="college" id="college"
                        value="<?= htmlspecialchars($application['college']) ?>"
                        placeholder="Leave blank if not applicable">
                </div>
                <div class="form-row">
                    <label for="college_year_grad" class="optional-label">College Year Graduated:</label>
                    <input type="number" name="college_year_grad" id="college_year_grad" min="1900"
                        max="<?= date('Y') ?>"
                        value="<?= !empty($application['college_year_grad']) && $application['college_year_grad'] != '0000' ? htmlspecialchars($application['college_year_grad']) : '' ?>"
                        placeholder="Leave blank if not applicable">
                </div>
                <div class="form-row">
                    <label for="college_honors" class="optional-label">College Honors:</label>
                    <input type="text" name="college_honors" id="college_honors"
                        value="<?= htmlspecialchars($application['college_honors']) ?>"
                        placeholder="Enter 'None' if not applicable">
                </div>
            </div>

            <!-- Family Information -->
            <div class="form-section">
                <h2>Family Information</h2>
                <h3>Father's Information</h3>
                <div class="form-row">
                    <label for="father_lastname">Last Name:</label>
                    <input type="text" name="father_lastname" id="father_lastname"
                        value="<?= htmlspecialchars($application['father_lastname']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="father_givenname">Given Name:</label>
                    <input type="text" name="father_givenname" id="father_givenname"
                        value="<?= htmlspecialchars($application['father_givenname']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="father_middlename">Middle Name:</label>
                    <input type="text" name="father_middlename" id="father_middlename"
                        value="<?= htmlspecialchars($application['father_middlename']) ?>">
                </div>
                <div class="form-row">
                    <label for="father_cellphone">Phone:</label>
                    <input type="tel" name="father_cellphone" id="father_cellphone"
                        value="<?= htmlspecialchars($application['father_cellphone']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="father_education">Education:</label>
                    <select name="father_education" id="father_education" required>
                        <option value="" disabled>Select Educational Attainment</option>
                        <option value="No Formal Education" <?= $application['father_education'] == 'No Formal Education' ? 'selected' : '' ?>>No Formal Education</option>
                        <option value="Elementary Undergraduate" <?= $application['father_education'] == 'Elementary Undergraduate' ? 'selected' : '' ?>>Elementary Undergraduate</option>
                        <option value="Elementary Graduate" <?= $application['father_education'] == 'Elementary Graduate' ? 'selected' : '' ?>>Elementary Graduate</option>
                        <option value="High School Undergraduate" <?= $application['father_education'] == 'High School Undergraduate' ? 'selected' : '' ?>>High School Undergraduate</option>
                        <option value="High School Graduate" <?= $application['father_education'] == 'High School Graduate' ? 'selected' : '' ?>>High School Graduate</option>
                        <option value="Vocational Course" <?= $application['father_education'] == 'Vocational Course' ? 'selected' : '' ?>>Vocational Course</option>
                        <option value="College Undergraduate" <?= $application['father_education'] == 'College Undergraduate' ? 'selected' : '' ?>>College Undergraduate</option>
                        <option value="College Graduate" <?= $application['father_education'] == 'College Graduate' ? 'selected' : '' ?>>College Graduate</option>
                        <option value="Postgraduate" <?= $application['father_education'] == 'Postgraduate' ? 'selected' : '' ?>>Postgraduate (Master's/PhD)</option>
                    </select>
                </div>
                <div class="form-row">
                    <label for="father_occupation">Occupation:</label>
                    <select name="father_occupation" id="father_occupation" required>
                        <option value="" disabled>Select Occupation</option>
                        <option value="Government" <?= $application['father_occupation'] == 'Government' ? 'selected' : '' ?>>Government</option>
                        <option value="Private Sector" <?= $application['father_occupation'] == 'Private Sector' ? 'selected' : '' ?>>Private Sector</option>
                        <option value="Self-Employed" <?= $application['father_occupation'] == 'Self-Employed' ? 'selected' : '' ?>>Self-Employed</option>
                        <option value="Laborer" <?= $application['father_occupation'] == 'Laborer' ? 'selected' : '' ?>>
                            Laborer</option>
                        <option value="Freelancer" <?= $application['father_occupation'] == 'Freelancer' ? 'selected' : '' ?>>Freelancer</option>
                        <option value="NGO/Non-Profit" <?= $application['father_occupation'] == 'NGO/Non-Profit' ? 'selected' : '' ?>>NGO/Non-Profit</option>
                        <option value="Overseas Employment" <?= $application['father_occupation'] == 'Overseas Employment' ? 'selected' : '' ?>>Overseas Employment</option>
                        <option value="Casual" <?= $application['father_occupation'] == 'Casual' ? 'selected' : '' ?>>
                            Casual</option>
                        <option value="Contractual" <?= $application['father_occupation'] == 'Contractual' ? 'selected' : '' ?>>Contractual</option>
                        <option value="Intern" <?= $application['father_occupation'] == 'Intern' ? 'selected' : '' ?>>
                            Intern</option>
                    </select>
                </div>
                <div class="form-row">
                    <label for="father_income">Monthly Income:</label>
                    <select name="father_income" id="father_income" required
                        onchange="toggleCustomIncome(this, 'father')">
                        <option value="" disabled>Select Monthly Income Range</option>
                        <option value="0-5000" <?= $father_income_data['value'] == '0-5000' ? 'selected' : '' ?>>₱0 -
                            ₱5,000</option>
                        <option value="5000-10000" <?= $father_income_data['value'] == '5000-10000' ? 'selected' : '' ?>>
                            ₱5,000 - ₱10,000</option>
                        <option value="10000-15000" <?= $father_income_data['value'] == '10000-15000' ? 'selected' : '' ?>>
                            ₱10,000 - ₱15,000</option>
                        <option value="15000-20000" <?= $father_income_data['value'] == '15000-20000' ? 'selected' : '' ?>>
                            ₱15,000 - ₱20,000</option>
                        <option value="20000-25000" <?= $father_income_data['value'] == '20000-25000' ? 'selected' : '' ?>>
                            ₱20,000 - ₱25,000</option>
                        <option value="25000-30000" <?= $father_income_data['value'] == '25000-30000' ? 'selected' : '' ?>>
                            ₱25,000 - ₱30,000</option>
                        <option value="30000-35000" <?= $father_income_data['value'] == '30000-35000' ? 'selected' : '' ?>>
                            ₱30,000 - ₱35,000</option>
                        <option value="35000-40000" <?= $father_income_data['value'] == '35000-40000' ? 'selected' : '' ?>>
                            ₱35,000 - ₱40,000</option>
                        <option value="40000-45000" <?= $father_income_data['value'] == '40000-45000' ? 'selected' : '' ?>>
                            ₱40,000 - ₱45,000</option>
                        <option value="45000-50000" <?= $father_income_data['value'] == '45000-50000' ? 'selected' : '' ?>>
                            ₱45,000 - ₱50,000</option>
                        <option value="50000+" <?= $father_income_data['value'] == '50000+' ? 'selected' : '' ?>>₱50,000+
                        </option>
                        <option value="Not Applicable" <?= $father_income_data['value'] == 'Not Applicable' ? 'selected' : '' ?>>Not Applicable (No Income)</option>
                        <option value="Prefer not to say" <?= $father_income_data['value'] == 'Prefer not to say' ? 'selected' : '' ?>>Prefer not to say</option>
                        <option value="custom" <?= $father_income_data['type'] == 'custom' ? 'selected' : '' ?>>Custom
                            Amount</option>
                    </select>
                    <div class="income-custom-field" id="father_custom_income_field">
                        <label for="father_income_custom">Enter Exact Monthly Income:</label>
                        <input type="number" name="father_income_custom" id="father_income_custom"
                            value="<?= htmlspecialchars($father_income_data['custom']) ?>" min="0" step="0.01"
                            placeholder="0.00">
                    </div>
                </div>

                <h3>Mother's Information</h3>
                <div class="form-row">
                    <label for="mother_lastname">Last Name:</label>
                    <input type="text" name="mother_lastname" id="mother_lastname"
                        value="<?= htmlspecialchars($application['mother_lastname']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="mother_givenname">Given Name:</label>
                    <input type="text" name="mother_givenname" id="mother_givenname"
                        value="<?= htmlspecialchars($application['mother_givenname']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="mother_middlename">Middle Name:</label>
                    <input type="text" name="mother_middlename" id="mother_middlename"
                        value="<?= htmlspecialchars($application['mother_middlename']) ?>">
                </div>
                <div class="form-row">
                    <label for="mother_cellphone">Phone:</label>
                    <input type="tel" name="mother_cellphone" id="mother_cellphone"
                        value="<?= htmlspecialchars($application['mother_cellphone']) ?>" required>
                </div>
                <div class="form-row">
                    <label for="mother_education">Education:</label>
                    <select name="mother_education" id="mother_education" required>
                        <option value="" disabled>Select Educational Attainment</option>
                        <option value="No Formal Education" <?= $application['mother_education'] == 'No Formal Education' ? 'selected' : '' ?>>No Formal Education</option>
                        <option value="Elementary Undergraduate" <?= $application['mother_education'] == 'Elementary Undergraduate' ? 'selected' : '' ?>>Elementary Undergraduate</option>
                        <option value="Elementary Graduate" <?= $application['mother_education'] == 'Elementary Graduate' ? 'selected' : '' ?>>Elementary Graduate</option>
                        <option value="High School Undergraduate" <?= $application['mother_education'] == 'High School Undergraduate' ? 'selected' : '' ?>>High School Undergraduate</option>
                        <option value="High School Graduate" <?= $application['mother_education'] == 'High School Graduate' ? 'selected' : '' ?>>High School Graduate</option>
                        <option value="Vocational Course" <?= $application['mother_education'] == 'Vocational Course' ? 'selected' : '' ?>>Vocational Course</option>
                        <option value="College Undergraduate" <?= $application['mother_education'] == 'College Undergraduate' ? 'selected' : '' ?>>College Undergraduate</option>
                        <option value="College Graduate" <?= $application['mother_education'] == 'College Graduate' ? 'selected' : '' ?>>College Graduate</option>
                        <option value="Postgraduate" <?= $application['mother_education'] == 'Postgraduate' ? 'selected' : '' ?>>Postgraduate (Master's/PhD)</option>
                    </select>
                </div>
                <div class="form-row">
                    <label for="mother_occupation">Occupation:</label>
                    <select name="mother_occupation" id="mother_occupation" required>
                        <option value="" disabled>Select Occupation</option>
                        <option value="Government" <?= $application['mother_occupation'] == 'Government' ? 'selected' : '' ?>>Government</option>
                        <option value="Private Sector" <?= $application['mother_occupation'] == 'Private Sector' ? 'selected' : '' ?>>Private Sector</option>
                        <option value="Self-Employed" <?= $application['mother_occupation'] == 'Self-Employed' ? 'selected' : '' ?>>Self-Employed</option>
                        <option value="Laborer" <?= $application['mother_occupation'] == 'Laborer' ? 'selected' : '' ?>>
                            Laborer</option>
                        <option value="Freelancer" <?= $application['mother_occupation'] == 'Freelancer' ? 'selected' : '' ?>>Freelancer</option>
                        <option value="NGO/Non-Profit" <?= $application['mother_occupation'] == 'NGO/Non-Profit' ? 'selected' : '' ?>>NGO/Non-Profit</option>
                        <option value="Overseas Employment" <?= $application['mother_occupation'] == 'Overseas Employment' ? 'selected' : '' ?>>Overseas Employment</option>
                        <option value="Casual" <?= $application['mother_occupation'] == 'Casual' ? 'selected' : '' ?>>
                            Casual</option>
                        <option value="Contractual" <?= $application['mother_occupation'] == 'Contractual' ? 'selected' : '' ?>>Contractual</option>
                        <option value="Intern" <?= $application['mother_occupation'] == 'Intern' ? 'selected' : '' ?>>
                            Intern</option>
                    </select>
                </div>
                <div class="form-row">
                    <label for="mother_income">Monthly Income:</label>
                    <select name="mother_income" id="mother_income" required
                        onchange="toggleCustomIncome(this, 'mother')">
                        <option value="" disabled>Select Monthly Income Range</option>
                        <option value="0-5000" <?= $mother_income_data['value'] == '0-5000' ? 'selected' : '' ?>>₱0 -
                            ₱5,000</option>
                        <option value="5000-10000" <?= $mother_income_data['value'] == '5000-10000' ? 'selected' : '' ?>>
                            ₱5,000 - ₱10,000</option>
                        <option value="10000-15000" <?= $mother_income_data['value'] == '10000-15000' ? 'selected' : '' ?>>
                            ₱10,000 - ₱15,000</option>
                        <option value="15000-20000" <?= $mother_income_data['value'] == '15000-20000' ? 'selected' : '' ?>>
                            ₱15,000 - ₱20,000</option>
                        <option value="20000-25000" <?= $mother_income_data['value'] == '20000-25000' ? 'selected' : '' ?>>
                            ₱20,000 - ₱25,000</option>
                        <option value="25000-30000" <?= $mother_income_data['value'] == '25000-30000' ? 'selected' : '' ?>>
                            ₱25,000 - ₱30,000</option>
                        <option value="30000-35000" <?= $mother_income_data['value'] == '30000-35000' ? 'selected' : '' ?>>
                            ₱30,000 - ₱35,000</option>
                        <option value="35000-40000" <?= $mother_income_data['value'] == '35000-40000' ? 'selected' : '' ?>>
                            ₱35,000 - ₱40,000</option>
                        <option value="40000-45000" <?= $mother_income_data['value'] == '40000-45000' ? 'selected' : '' ?>>
                            ₱40,000 - ₱45,000</option>
                        <option value="45000-50000" <?= $mother_income_data['value'] == '45000-50000' ? 'selected' : '' ?>>
                            ₱45,000 - ₱50,000</option>
                        <option value="50000+" <?= $mother_income_data['value'] == '50000+' ? 'selected' : '' ?>>₱50,000+
                        </option>
                        <option value="Not Applicable" <?= $mother_income_data['value'] == 'Not Applicable' ? 'selected' : '' ?>>Not Applicable (No Income)</option>
                        <option value="Prefer not to say" <?= $mother_income_data['value'] == 'Prefer not to say' ? 'selected' : '' ?>>Prefer not to say</option>
                        <option value="custom" <?= $mother_income_data['type'] == 'custom' ? 'selected' : '' ?>>Custom
                            Amount</option>
                    </select>
                    <div class="income-custom-field" id="mother_custom_income_field">
                        <label for="mother_income_custom">Enter Exact Monthly Income:</label>
                        <input type="number" name="mother_income_custom" id="mother_income_custom"
                            value="<?= htmlspecialchars($mother_income_data['custom']) ?>" min="0" step="0.01"
                            placeholder="0.00">
                    </div>
                </div>
            </div>

            <!-- Housing Information -->
            <div class="form-section">
                <h2>Housing Information</h2>
                <div class="house-status-group">
                    <label class="house-option">
                        <input type="radio" name="house_status" value="Owned" <?= $application['house_status'] == 'Owned' ? 'checked' : '' ?> required>
                        <span>House Owned</span>
                    </label>
                    <label class="house-option">
                        <input type="radio" name="house_status" value="Rented" <?= $application['house_status'] == 'Rented' ? 'checked' : '' ?>>
                        <span>Rented</span>
                    </label>
                    <label class="house-option">
                        <input type="radio" name="house_status" value="Living with relatives"
                            <?= $application['house_status'] == 'Living with relatives' ? 'checked' : '' ?>>
                        <span>Living with Relatives</span>
                    </label>
                </div>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="../views/applications.php?id=<?= $application_id ?>" class="btn-cancel">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <a href="delete_application.php?id=<?= $application_id ?>" class="btn-delete"
                    onclick="return confirm('Are you sure you want to delete this application? This action cannot be undone.')">
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

        function toggleOtherField(selectElement, fieldId) {
            const otherField = document.getElementById(fieldId + '_field');
            if (selectElement.value === 'others') {
                otherField.style.display = 'block';
            } else {
                otherField.style.display = 'none';
            }
        }

        function toggleCustomIncome(selectElement, parentType) {
            const customField = document.getElementById(parentType + '_custom_income_field');
            const customInput = document.getElementById(parentType + '_income_custom');

            if (selectElement.value === 'custom') {
                customField.style.display = 'block';
                if (customInput) customInput.required = true;
            } else {
                customField.style.display = 'none';
                if (customInput) {
                    customInput.required = false;
                }
            }
        }

        // Initialize fields on page load
        document.addEventListener('DOMContentLoaded', function () {
            // Check all dropdowns that have "others" option
            const sexSelect = document.getElementById('sex');
            if (sexSelect) {
                toggleOtherField(sexSelect, 'sex_other');
            }

            const religionSelect = document.getElementById('religion');
            if (religionSelect) {
                toggleOtherField(religionSelect, 'religion_other');
            }

            const indigenousSelect = document.getElementById('indigenous_group');
            if (indigenousSelect) {
                toggleOtherField(indigenousSelect, 'indigenous_group_other');
            }

            // Initialize custom income fields
            const fatherIncomeSelect = document.getElementById('father_income');
            if (fatherIncomeSelect) {
                toggleCustomIncome(fatherIncomeSelect, 'father');
            }

            const motherIncomeSelect = document.getElementById('mother_income');
            if (motherIncomeSelect) {
                toggleCustomIncome(motherIncomeSelect, 'mother');
            }

            // Set up date of birth age calculation
            const dobInput = document.getElementById('date_of_birth');
            if (dobInput && dobInput.value) {
                calculateAge();
            }
        });

        // Hide preloader when page loads
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                document.querySelector('.preloader').style.display = 'none';
            }, 1000);
        });
    </script>
</body>

</html>