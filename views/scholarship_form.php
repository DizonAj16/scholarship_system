<?php
include '../includes/session.php';
// process_form.php - Processing form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize form data
    $data = [
        'date' => $_POST['date'],
        'semester' => $_POST['sem'],
        'school_year' => $_POST['sy'],
        'full_name' => $_POST['fullName'],
        'course' => $_POST['course'],
        'yr_sec' => $_POST['yr_sec'],
        'major' => $_POST['major'],
        'cell_no' => $_POST['cellNo'],
        'permanent_address' => $_POST['perma_address'],
        'zip_code' => $_POST['zip_code'],
        'present_address' => $_POST['pres_address'],
        'email' => $_POST['email'],
        'sex' => $_POST['sex'],
        'date_of_birth' => $_POST['date_of_birth'],
        'age' => $_POST['age'],
        'place_of_birth' => $_POST['place_of_birth'],
        'civil_status' => $_POST['civil_status'],
        'religion' => $_POST['religion'],
        'scholarship_grant' => $_POST['scholarship_grant'],
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
        'father_cellphone' => $_POST['father_cellphone'],
        'father_education' => $_POST['father_education'],
        'father_occupation' => $_POST['father_occupation'],
        'father_income' => $_POST['father_income'],
        'mother_lastname' => $_POST['mother_lastname'],
        'mother_givenname' => $_POST['mother_givenname'],
        'mother_middlename' => $_POST['mother_middlename'],
        'mother_cellphone' => $_POST['mother_cellphone'],
        'mother_education' => $_POST['mother_education'],
        'mother_occupation' => $_POST['mother_occupation'],
        'mother_income' => $_POST['mother_income'],
        'house_status' => $_POST['house_status'],
    ];

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

        // Get the last inserted ID

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

        // Commit transaction
        $pdo->commit();

        // Log the activity
        $action = "Scholarship application submitted";
        $details = "Application ID $application_id submitted by $username.";
        logActivity($pdo, $user_id, $action, $details);

        $_SESSION['success_message'] = 'Your scholarship application has been successfully submitted.';
    } catch (PDOException $e) {
        // Rollback transaction on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $_SESSION['error_message'] = 'There was an error processing your application. Please try again.';
        echo "Error: " . $e->getMessage();
    }
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
    <title>Applciation Form</title>

    <script src="../js/fontawesome.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/preloader.css?v=<?php echo time(); ?>">
    <script src="../js/preloader.js?v=<?php echo time(); ?>"></script>
    <title>Scholarship Application Form</title>
    <link rel="stylesheet" type="text/css" href="../css/style.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" type="text/css" href="../css/form.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" type="text/css" href="../css/wizard.css?v=<?php echo time(); ?>" />
    <style>
    </style>
    <script>
        function calculateAge() {
            const dob = document.getElementById('date_of_birth').value;
            if (dob) { // Ensure a date is selected
                const dobDate = new Date(dob);
                const today = new Date();
                let age = today.getFullYear() - dobDate.getFullYear();
                const monthDiff = today.getMonth() - dobDate.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dobDate.getDate())) {
                    age--;
                }
                document.getElementById('age').value = age;
            } else {
                document.getElementById('age').value = ''; // Clear age if dob is empty
            }
        }
    </script>
</head>

<body>

    <div class="preloader">
        <img src="../assets/images/icons/scholarship_seal.png" alt="" style="height: 70px; width: 70px;">
        <div class="lds-facebook">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>

    <button class="toggle-btn" onclick="toggleNav()">
        <i class="fas fa-times" id="toggle-icon"></i>
    </button>


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
                <li>
                    <a href="./scholarship_form.php" class="active">
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
                        <i class="fas fa-clipboard-list"></i>
                        <span class="nav-item-2">FAQs</span>
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

        <form method="POST" id="checkout-form" onSubmit="return validateCheckout()">
            <div class="wizard-flow-chart">
                <span class="fill">1</span>
                <span>2</span>
                <span>3</span>
                <span>4</span>
                <span>5</span>
            </div>

            <!-- Wizard section 1 -->
            <section id="personal-info">
                <h2 class="text-center" style="margin-bottom: 20px;">Personal Information</h2>
                <div class="form-container">
                    <div class="form-row">
                        <label class="label-width">Date</label>
                        <input name="date" type="datetime-local" required>
                    </div>
                    <div class="form-row">
                        <label class="label-width">Semester</label>
                        <select name="sem" id="" required>
                            <option value="" disabled selected>Select semester</option>
                            <option value="1st sem">1st sem</option>
                            <option value="2nd sem">2nd sem</option>
                            <option value="Summer">Summer</option>
                        </select>
                    </div>

                    <div class="form-row">
                        <label class="label-width">School Year</label>
                        <select name="sy" required>
                            <option value="" disabled selected>Select year</option>
                            <option value="2024-2025">2024-2025</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="float-left label-width">Full Name</label>
                        <input name="fullName" type="text" required>
                    </div>
                    <div class="form-row">
                        <label class="float-left label-width">Course</label>
                        <select name="course" id="course" style="width:200px;">
                            <option value="" disabled selected>Select Course</option>

                            <!-- College of Maritime Education -->
                            <optgroup label="COLLEGE OF MARITIME EDUCATION (CME)">
                                <option value="BS MARE">BS MAR-E: BACHELOR OF SCIENCE IN MARINE ENGINEERING</option>
                            </optgroup>

                            <!-- College of Engineering and Technology -->
                            <optgroup label="College of Engineering and Technology (CET)">
                                <option value="BS CE">BS CE: BACHELOR OF SCIENCE IN CIVIL ENGINEERING</option>
                                <option value="BSIT PPE">BSIT PPE - BACHELOR OF SCIENCE IN INDUSTRIAL TECHNOLOGY IN
                                    POWER PLANT ENGINEERING</option>
                                <option value="BS AT">BS AT - BACHELOR OF SCIENCE IN AUTOMOTIVE TECHNOLOGY</option>
                                <option value="BS COMPTECH">BS COMPTECH - BACHELOR OF SCIENCE IN COMPUTER TECHNOLOGY
                                </option>
                                <option value="BS ELECT">BS ELECT - BACHELOR OF SCIENCE IN ELECTRICAL TECHNOLOGY
                                </option>
                                <option value="BSIT CT">BSIT CT - BACHELOR OF SCIENCE IN INDUSTRIAL TECHNOLOGY IN CIVIL
                                    TECHNOLOGY</option>
                                <option value="BSIT DT">BSIT DT - BACHELOR OF SCIENCE IN INDUSTRIAL TECHNOLOGY IN
                                    DRAFTING TECHNOLOGY</option>
                                <option value="BSIT FT">BSIT FT - BACHELOR OF SCIENCE IN INDUSTRIAL TECHNOLOGY IN FOOD
                                    TECHNOLOGY</option>
                                <option value="BSIT GTT">BSIT GTT - BACHELOR OF SCIENCE IN INDUSTRIAL TECHNOLOGY IN
                                    GARMENTS AND TEXTILE TECHNOLOGY</option>
                                <option value="BS MECHTECH">BS MECHTECH - BACHELOR OF SCIENCE IN MECHANICAL TECHNOLOGY
                                </option>
                                <option value="BS RACT">BS RACT - BACHELOR OF SCIENCE IN REFRIGERATION AND AIR
                                    CONDITIONING TECHNOLOGY</option>
                                <option value="BS ELEXT">BS ELEXT - BACHELOR OF SCIENCE IN ELECTRONICS TECHNOLOGY
                                </option>
                                <option value="BS MECHATRONICS">BS MECHATRONICS - BACHELOR OF SCIENCE IN MECHATRONICS
                                    TECHNOLOGY
                                </option>
                            </optgroup>

                            <!-- College of Teacher Education -->
                            <optgroup label="College of Teacher Education (CTE)">
                                <option value="BEED">BEED: BACHELOR OF ELEMENTARY EDUCATION</option>
                                <option value="BSED ENGLISH">BSED ENGLISH: BACHELOR OF SECONDARY EDUCATION MAJOR IN
                                    ENGLISH</option>
                                <option value="BSED MATH">BSED MATH: BACHELOR OF SECONDARY EDUCATION MAJOR IN
                                    MATHEMATICS</option>
                                <option value="BTVTED AUTO">BTVTED AUTO: BACHELOR OF TECHNICAL-VOCATIONAL TEACHER
                                    EDUCATION IN AUTOMOTIVE TECHNOLOGY</option>
                                <option value="BTVTED CIVIL">BTVTED CIVIL: BACHELOR OF TECHNICAL-VOCATIONAL TEACHER
                                    EDUCATION IN CIVIL AND CONSTRUCTION TECHNOLOGY</option>
                                <option value="BTVTED DRAFT">BTVTED DRAFT: BACHELOR OF TECHNICAL-VOCATIONAL TEACHER
                                    EDUCATION IN DRAFTING TECHNOLOGY</option>
                                <option value="BTVTED ELECT">BTVTED ELECT: BACHELOR OF TECHNICAL-VOCATIONAL TEACHER
                                    EDUCATION IN ELECTRICAL EDUCATION</option>
                                <option value="BTVTED ELEXT">BTVTED ELEXT: BACHELOR OF TECHNICAL-VOCATIONAL TEACHER
                                    EDUCATION IN ELECTRONICS TECHNOLOGY</option>
                                <option value="BTVTED GFD">BTVTED GFD: BACHELOR OF TECHNICAL-VOCATIONAL TEACHER
                                    EDUCATION IN FASHION, GARMENTS AND TECHNOLOGY</option>
                                <option value="BTVTED FSM">BTVTED FSM: BACHELOR OF TECHNICAL-VOCATIONAL TEACHER
                                    EDUCATION IN FOOD SERVICE MANAGEMENT</option>
                                <option value="BTVTED HVAC">BTVTED HVAC: BACHELOR OF TECHNICAL-VOCATIONAL TEACHER
                                    EDUCATION IN HEATING, VENTILATING AND AIR CONDITIONING TECHNOLOGY</option>
                                <option value="BTVTED WAFT">BTVTED WAFT: BACHELOR OF TECHNICAL-VOCATIONAL TEACHER
                                    EDUCATION IN WELDING AND FABRICATION TECHNOLOGY</option>
                                <option value="BTLED HE">BTLED HE: BACHELOR OF TECHNOLOGY AND LIVELIHOOD EDUCATION IN
                                    HOME ECONOMICS</option>
                                <option value="BTLED IA">BTLED IA: BACHELOR OF TECHNOLOGY AND LIVELIHOOD EDUCATION IN
                                    INDUSTRIAL ARTS</option>
                                <option value="BTLED ICT">BTLED ICT: BACHELOR OF TECHNOLOGY AND LIVELIHOOD EDUCATION IN
                                    INFORMATION AND COMMUNICATION TECHNOLOGY</option>
                            </optgroup>

                            <optgroup label="College of Physical Education and Science">
                                <option value="BPED">BPED: BACHELOR OF PHYSICAL EDUCATION</option>
                                <option value="BSESS">BSESS: BACHELOR OF SCIENCE IN EXCERCISE AND SPORTS SCIENCE
                                </option>

                            </optgroup>

                            <!-- College of Arts, Humanities and Social Sciences -->
                            <optgroup label="College of Arts, Humanities and Social Sciences (CAHSS)">
                                <option value="BS DEVCOM">BS DEVCOM - BACHELOR OF SCIENCE IN DEVELOPMENT COMMUNICATION
                                </option>
                                <option value="BFA-ID">BFA-ID - BACHELOR OF FINE ARTS IN INDUSTRIAL DESIGN</option>
                                <option value="BA FIL">BA FIL - BATSILYER SA SINING NG FILIPINO</option>
                            </optgroup>

                            <!-- School of Business Administration -->
                            <optgroup label="School of Business Administration (SBA)">
                                <option value="BS ENTREP">BS ENTREP - BACHELOR OF SCIENCE IN ENTREPRENEURSHIP</option>
                                <option value="BS HM">BS HM - BACHELOR OF SCIENCE IN HOSPITALITY MANAGEMENT</option>
                            </optgroup>

                            <!-- College of Information Computing Studies -->
                            <optgroup label="College of Information Computing Studies (CICS)">
                                <option value="BS INFOTECH">BS INFOTECH - BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY
                                </option>
                                <option value="BS INFOSYS">BS INFOSYS - BACHELOR OF SCIENCE IN INFORMATION SYSTEMS
                                </option>
                            </optgroup>

                            <!-- Department of Technical Education -->
                            <optgroup label="DEPARTMENT OF TECHNICAL EDUCATION (DTE)">
                                <!-- 2-Year Courses -->
                            <optgroup label="2-YEAR COURSES">
                                <option value="AIT AUTO">AIT AUTO: 2-YEAR ASSOCIATE IN INDUSTRIAL TECHNOLOGY IN
                                    AUTOMOTIVE TECHNOLOGY</option>
                                <option value="AIT ELECT">AIT ELECT: 2-YEAR ASSOCIATE IN INDUSTRIAL TECHNOLOGY IN
                                    ELECTRICAL TECHNOLOGY</option>
                                <option value="AIT ELEXT">AIT ELEXT: 2-YEAR ASSOCIATE IN INDUSTRIAL TECHNOLOGY IN
                                    ELECTRONICS TECHNOLOGY</option>
                                <option value="AIT FOOD">AIT FOOD: 2-YEAR ASSOCIATE IN INDUSTRIAL TECHNOLOGY IN FOOD
                                    TECHNOLOGY</option>
                                <option value="AIT GARMENTS">AIT GARMENTS: 2-YEAR ASSOCIATE IN INDUSTRIAL TECHNOLOGY IN
                                    GARMENTS AND TEXTILE TECHNOLOGY</option>
                                <option value="AIT RACT">AIT RACT: 2-YEAR ASSOCIATE IN INDUSTRIAL TECHNOLOGY IN
                                    REFRIGERATION AND AIR CONDITIONING TECHNOLOGY</option>
                                <option value="TTEC TDT">AIT RACT: 2-YEAR TRADE TECHNICAL CURRICULUM IN TECHNICAL
                                    DRAFTING TECHNOLOGY</option>

                            </optgroup>
                            <!-- 3-Year Courses -->
                            <optgroup label="3-YEAR COURSES">
                                <option value="DT AUTO">TTEC TDT: 3-YEAR DIPLOMA OF TECHNOLOGY IN AUTOMOTIVE ENGINEERING
                                    TECHNOLOGY</option>
                                <option value="DT CIVIL">DT CIVIL: 3-YEAR DIPLOMA OF TECHNOLOGY IN CIVIL ENGINEERING
                                    TECHNOLOGY</option>
                                <option value="DT ELECT">DT ELECT: 3-YEAR DIPLOMA TECHNOLOGY IN ELECTRICAL ENGINEERING
                                    TECHNOLOGY</option>
                                <option value="DT ELEXT">DT ELEXT: 3-YEAR DIPLOMA OF TECHNOLOGY IN ELECTRONICS AND
                                    COMMUNICATION TECHNOLOGY</option>
                                <option value="DT FOOD">DT FOOD: 3-YEAR DIPLOMA OF TECHNOLOGY IN FOOD PRODUCTION AND
                                    SERVICES MANAGEMENT</option>
                                <option value="DT GARMENTS">DT GARMENTS: 3-YEAR DIPLOMA OF TECHNOLOGY IN GARMENT FASHION
                                    AND DESIGN TECHNOLOGY</option>
                                <option value="DT HMT">DT HMT: 3-YEAR DIPLOMA OF TECHNOLOGY IN HOSPITALITY MANAGEMENT
                                    TECHNOLOGY</option>
                                <option value="DT IT">DT IT: 3-YEAR DIPLOMA OF TECHNOLOGY IN INFORMATION TECHNOLOGY
                                </option>
                                <option value="TITE WAFT">TITE WAFT: 3-YEAR TRADE INDUSTRIAL TECHNICAL EDUCATION IN
                                    WELDING AND FABRICATION TECHNOLOGY</option>
                            </optgroup>
                            </optgroup>

                            <!-- Senior High School -->
                            <optgroup label="SENIOR HIGH SCHOOL (SHS)">
                            <optgroup label="SHS STRANDS">
                                <option value="GAS">GAS: GENERAL ACADEMIC STRAND</option>
                                <option value="ABM">ABM: ACCOUNTANCY, BUSINESS AND MANAGEMENT</option>
                                <option value="STEM">STEM: SCIENCE, TECHNOLOGY, ENGINEERING, AND MATHEMATICS</option>
                                <option value="HUMSS">HUMSS: HUMANITIES AND SOCIAL SCIENCES</option>
                                <option value="TVL">TVL: TECHNICAL-VOCATIONAL-LIVELIHOOD</option>
                            </optgroup>
                            </optgroup>
                        </select>

                    </div>
                    <div class="form-row">
                        <label class="float-left label-width">Year/Section</label>
                        <input name="yr_sec" type="text" required>
                    </div>
                    <div class="form-row">
                        <label class="float-left label-width">Major</label>
                        <input name="major" type="text" required>
                    </div>
                    <div class="form-row">
                        <label class="float-left label-width">Cellphone #</label>
                        <input name="cellNo" type="tel" required>
                    </div>
                    <div class="form-row">
                        <label class="float-left label-width">Complete Permanent Address</label>
                        <input name="perma_address" type="text" required>
                    </div>
                    <div class="form-row">
                        <label class="float-left label-width">ZIP code</label>
                        <input name="zip_code" type="number" required>
                    </div>
                    <div class="form-row">
                        <label class="float-left label-width">Complete Present Address</label>
                        <input name="pres_address" type="text" required>
                    </div>
                    <div class="form-row">
                        <label class="float-left label-width">Email Address</label>
                        <input name="email" type="email" required>
                    </div>
                    <div class="form-row">
                        <label class="float-left label-width">Sex</label>
                        <select name="sex" required>
                            <option value="" disabled selected>Select Sex</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="others">Others</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="float-left label-width">Date of Birth</label>
                        <input name="date_of_birth" id="date_of_birth" type="date" onchange="calculateAge()" required>
                    </div>
                    <div class="form-row">
                        <label class="float-left label-width">Age</label>
                        <input name="age" id="age" type="number" readonly>
                    </div>
                    <div class="form-row">
                        <label class="float-left label-width">Place of Birth</label>
                        <input name="place_of_birth" type="text" required>
                    </div>
                    <div class="form-row">
                        <label class="float-left label-width">Civil Status</label>
                        <select name="civil_status" required>
                            <option value="" disabled selected>Select Civil Status</option>
                            <option value="single">Single</option>
                            <option value="married">Married</option>
                            <option value="widowed">Widowed</option>
                            <option value="divorced">Divorced</option>
                            <option value="separated">Separated</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="float-left label-width">Religion</label>
                        <select name="religion" required>
                            <option value="" disabled selected>Select Religion</option>
                            <option value="roman catholic">Roman Catholic</option>
                            <option value="islam">Islam</option>
                            <option value="iglesia ni cristo">Iglesia ni Cristo</option>    
                            <option value="evangelical christian">Evangelical Christian</option>
                            <option value="a biblical church">Aglipayan / Philippine Independent Church</option>
                            <option value="others">Others</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="float-left label-width">Scholarship Grant</label>
                        <select name="scholarship_grant" required>
                            <option value="" disabled selected>Select Scholarship Grant</option>
                            <option value="CHED scholarship">CHED Scholarship</option>
                            <option value="DOST-SEI scholarship">DOST-SEI Scholarship</option>
                            <option value="LGU scholarship">LGU Scholarship</option>
                            <option value="TESDA scholarship">TESDA Scholarship</option>
                            <option value="academic scholarship">Academic Scholarship</option>
                            <option value="athletic scholarship">Athletic Scholarship</option>
                            <option value="government scholarship">Government Scholarship</option>
                            <option value="private scholarship">Private Scholarship</option>
                            <option value="merit based">Merit-Based Scholarship</option>
                            <option value="others">Others</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="float-left label-width">Type of Disability</label>
                        <input name="disability" type="text" required>
                    </div>
                    <div class="form-row">
                        <label class="float-left label-width">Indigenous People Group</label>
                        <select name="indigenous_group" required>
                            <option value="" disabled selected>Select Indigenous People Group</option>
                            <option value="igorot">Igorot</option>
                            <option value="lumad">Lumad</option>
                            <option value="moro">Moro</option>
                            <option value="aeta">Aeta</option>
                            <option value="badjao">Badjao</option>
                            <option value="others">Others</option>
                            <option value="N/A">Not Applicable</option>
                        </select>
                    </div>


                    <div class="row button-row">
                        <button type="button" class="buttonNav" onClick="validate(this)">Next</button>
                    </div>
                </div>

            </section>

            <!-- Wizard section 2 -->
            <section id="schools-attended" class="display-none">
                <h2 class="text-center" style="margin-bottom: 20px;">Schools Attended</h2>
                <div class="form-container">
                    <div class="form-row">
                        <label class="float-left label-width">Elementary</label>
                        <input type="text" name="elementary" id="">
                    </div>
                    <div class="form-row">
                        <label class="float-left label-width">Year Graduate</label>
                        <input type="number" name="elementary_yr_grad" id="">
                    </div>
                    <div class="form-row">
                        <label class="float-left label-width">Honors Received</label>
                        <input type="text" name="elementary_honors_rec" id="">
                    </div>
                    <br>
                    <div class="form-row">
                        <label class="float-left label-width">Secondary</label>
                        <input type="text" name="secondary" id="">
                    </div>
                    <div class="form-row">
                        <label class="float-left label-width">Year Graduate</label>
                        <input type="number" name="secondary_yr_grad" id="">
                    </div>
                    <div class="form-row">
                        <label class="float-left label-width">Honors Received</label>
                        <input type="text" name="secondary_honors_rec" id="">
                    </div>
                    <br>
                    <div class="form-row">
                        <label class="float-left label-width">College</label>
                        <input type="text" name="college" id="">
                    </div>
                    <div class="form-row">
                        <label class="float-left label-width">Year Graduate</label>
                        <input type="number" name="college_yr_grad" id="">
                    </div>
                    <div class="form-row">
                        <label class="float-left label-width">Honors Received</label>
                        <input type="text" name="college_honors_rec" id="">
                    </div>
                    <div class="row button-row">
                        <button type="button" class="buttonNav" onClick="showPrevious(this)">Previous</button>
                        <button type="button" class="buttonNav" onClick="validate(this)">Next</button>
                    </div>
                </div>
            </section>


            <!-- Wizard section 3 -->
            <section id="scholarship-reason" class="display-none">
                <h2 class="text-center" style="margin-bottom: 20px;">Why do you need a Scholarship?</h2>
                <div class="row">
                    <textarea name="reason_scholarship" id="" required></textarea>
                </div>
                <div class="row button-row">
                    <button type="button" class="buttonNav" onClick="showPrevious(this)">Previous</button>
                    <button type="button" class="buttonNav" onClick="validate(this)">Next</button>
                </div>
            </section>

            <!-- Wizard section 4 -->
            <section id="parents-data" class="display-none">
                <h2 class="text-center" style="margin-bottom: 20px;">Parent's Data</h2>

                <!-- Father's Information Section -->
                <div class="form-container">
                    <h4>Father's Information</h4>
                    <div class="form-row">
                        <label for="father_lastname" class="float-left label-width">Last Name:</label>
                        <input id="father_lastname" name="father_lastname" type="text" required>
                    </div>
                    <div class="form-row">
                        <label for="father_givenname" class="float-left label-width">Given Name:</label>
                        <input id="father_givenname" name="father_givenname" type="text" required>
                    </div>
                    <div class="form-row">
                        <label for="father_middlename" class="float-left label-width">Middle Name:</label>
                        <input id="father_middlename" name="father_middlename" type="text" required>
                    </div>
                    <div class="form-row">
                        <label for="father_cellphone" class="float-left label-width">Cellphone Number:</label>
                        <input id="father_cellphone" name="father_cellphone" type="tel" required>
                    </div>
                    <div class="form-row">
                        <label for="father_education" class="float-left label-width">Educational Attainment:</label>
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
                            <option value="Postgraduate">Postgraduate (Master’s/PhD)</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label for="father_occupation" class="float-left label-width">Occupation:</label>
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
                    </div>
                    <div class="form-row">
                        <label for="father_income" class="float-left label-width">Monthly Income:</label>
                        <input id="father_income" name="father_income" type="number" required>
                    </div>
                </div>

                <!-- Mother's Information Section -->
                <div class="form-container">
                    <h4>Mother's Information</h4>
                    <div class="form-row">
                        <label for="mother_lastname" class="float-left label-width">Maiden Name:</label>
                        <input id="mother_lastname" name="mother_lastname" type="text" required>
                    </div>
                    <div class="form-row">
                        <label for="mother_givenname" class="float-left label-width">Given Name:</label>
                        <input id="mother_givenname" name="mother_givenname" type="text" required>
                    </div>
                    <div class="form-row">
                        <label for="mother_middlename" class="float-left label-width">Middle Name:</label>
                        <input id="mother_middlename" name="mother_middlename" type="text" required>
                    </div>
                    <div class="form-row">
                        <label for="mother_cellphone" class="float-left label-width">Cellphone Number:</label>
                        <input id="mother_cellphone" name="mother_cellphone" type="tel" required>
                    </div>
                    <div class="form-row">
                        <label for="mother_education" class="float-left label-width">Educational Attainment:</label>
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
                            <option value="Postgraduate">Postgraduate (Master’s/PhD)</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label for="mother_occupation" class="float-left label-width">Occupation:</label>
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
                    </div>
                    <div class="form-row mb-5">
                        <label for="mother_income" class="float-left label-width">Monthly Income:</label>
                        <input id="mother_income" name="mother_income" type="number" required>
                    </div>
                </div>

                <div class="button-row">
                    <button type="button" class="buttonNav" onClick="showPrevious(this)">Previous</button>
                    <button type="button" class="buttonNav" onClick="validate(this)">Next</button>
                </div>
            </section>


            <!-- Wizard section 5 -->
            <section id="house-status" class="display-none">
                <h2 class="text-center" style="margin-bottom: 20px;">House Status</h2>
                <div class="row">
                    <label>
                        <input type="radio" name="house_status" value="owned" required>
                        House Owned
                    </label>
                    <label>
                        <input type="radio" name="house_status" value="rented">
                        Rented
                    </label>
                    <label>
                        <input type="radio" name="house_status" value="living with relatives">
                        Living with Relatives
                    </label>
                </div>
                <div class="row button-row">
                    <button type="button" class="buttonNav" onClick="showPrevious(this)">Previous</button>
                    <button type="submit" class="submitBtn">Submit</button>
                </div>
            </section>

        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="../js/wizard.js"></script>
    <script>
        // Wait for the DOM to fully load
        document.addEventListener("DOMContentLoaded", function () {
            // Get the success and error message elements
            var successMessage = document.getElementById("successMessage");
            var errorMessage = document.getElementById("errorMessage");

            // Handle success message
            if (successMessage) {
                successMessage.style.display = "inline-block";

                // Set a timeout to hide the success message after 5 seconds
                setTimeout(function () {
                    successMessage.style.display = "none";
                }, 5000);
            }

            // Handle error message
            if (errorMessage) {
                errorMessage.style.display = "inline-block";

                // Set a timeout to hide the error message after 5 seconds
                setTimeout(function () {
                    errorMessage.style.display = "none";
                }, 5000);
            }
        });

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