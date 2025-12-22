<?php
// Include config file
require_once "../includes/config.php";

// Initialize variables for all fields
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";

// Personal Information Fields
$fullName = $course = $major = $yr_sec = $cell_no = $present_address = $permanent_address = "";
$zip_code = $email = $sex = $date_of_birth = $age = $place_of_birth = $civil_status = "";
$religion = $disability = $indigenous_group = $sex_other = $religion_other = $indigenous_group_other = "";

// Schools Attended Fields
$elementary = $elementary_year_grad = $elementary_honors = "";
$secondary = $secondary_year_grad = $secondary_honors = "";
$college = $college_year_grad = $college_honors = "";

// Parents Data Fields
$father_lastname = $father_givenname = $father_middlename = $father_cellphone = "";
$father_education = $father_occupation = $father_income = $father_income_custom = "";
$mother_lastname = $mother_givenname = $mother_middlename = $mother_cellphone = "";
$mother_education = $mother_occupation = $mother_income = $mother_income_custom = "";

// House Status
$house_status = "";

// Fetch Course + Major for dropdown
$course_major_list = $pdo->query("SELECT * FROM dropdown_course_major ORDER BY course ASC, major ASC")->fetchAll(PDO::FETCH_ASSOC);

// Function to convert income range to decimal value
function getIncomeValue($range, $custom_value = '')
{
    if (empty($range))
        return '';

    // If custom is selected, use custom value
    if ($range === 'custom' && !empty($custom_value) && is_numeric($custom_value)) {
        return '₱' . number_format((float) $custom_value, 2, '.', '');
    }

    // Return the actual range string for predefined ranges
    if (
        in_array($range, [
            '0-5000',
            '5000-10000',
            '10000-15000',
            '15000-20000',
            '20000-25000',
            '25000-30000',
            '30000-35000',
            '35000-40000',
            '40000-45000',
            '45000-50000',
            '50000+',
            'Not Applicable',
            'Prefer not to say'
        ])
    ) {
        if ($range === 'Not Applicable' || $range === 'Prefer not to say') {
            return $range;
        }
        return '₱' . $range;
    }

    return '';
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = :username";

        if ($stmt = $pdo->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);

            // Set parameters
            $param_username = trim($_POST["username"]);

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            unset($stmt);
        }
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    // Collect and validate all form data
    // Personal Information
    $fullName = trim($_POST["fullName"] ?? '');
    $course_major = explode('|', $_POST["course_major"] ?? '');
    $course = $course_major[0] ?? '';
    $major = $course_major[1] ?? '';
    $yr_sec = trim($_POST["yr_sec"] ?? '');
    $cell_no = preg_replace('/[^0-9]/', '', $_POST["cellNo"] ?? '');
    $present_address = trim($_POST["pres_address"] ?? '');
    $permanent_address = trim($_POST["perma_address"] ?? '');
    $zip_code = trim($_POST["zip_code"] ?? '');
    $email = trim($_POST["email"] ?? '');

    // Handle sex field with "others" option
    $sex = trim($_POST["sex"] ?? '');
    if ($sex === 'others' && isset($_POST["sex_other"])) {
        $sex_other = trim($_POST["sex_other"]);
        $sex = $sex_other;
    }

    $date_of_birth = trim($_POST["date_of_birth"] ?? '');
    $age = trim($_POST["age"] ?? '');
    $place_of_birth = trim($_POST["place_of_birth"] ?? '');
    $civil_status = trim($_POST["civil_status"] ?? '');

    // Handle religion field with "others" option
    $religion = trim($_POST["religion"] ?? '');
    if ($religion === 'others' && isset($_POST["religion_other"])) {
        $religion_other = trim($_POST["religion_other"]);
        $religion = $religion_other;
    }

    $disability = trim($_POST["disability"] ?? '');

    // Handle indigenous_group field with "others" option
    $indigenous_group = trim($_POST["indigenous_group"] ?? '');
    if ($indigenous_group === 'others' && isset($_POST["indigenous_group_other"])) {
        $indigenous_group_other = trim($_POST["indigenous_group_other"]);
        $indigenous_group = $indigenous_group_other;
    }

    // Schools Attended
    $elementary = trim($_POST["elementary"] ?? '');
    $elementary_year_grad = trim($_POST["elementary_yr_grad"] ?? '');
    $elementary_honors = trim($_POST["elementary_honors_rec"] ?? '');
    $secondary = trim($_POST["secondary"] ?? '');
    $secondary_year_grad = trim($_POST["secondary_yr_grad"] ?? '');
    $secondary_honors = trim($_POST["secondary_honors_rec"] ?? '');
    $college = trim($_POST["college"] ?? '');
    $college_year_grad = trim($_POST["college_yr_grad"] ?? '');
    $college_honors = trim($_POST["college_honors_rec"] ?? '');

    // Handle empty college year graduation - set to NULL if empty
    if (empty($college_year_grad)) {
        $college_year_grad = null;
    }

    // Parents Data
    $father_lastname = trim($_POST["father_lastname"] ?? '');
    $father_givenname = trim($_POST["father_givenname"] ?? '');
    $father_middlename = trim($_POST["father_middlename"] ?? '');
    $father_cellphone = preg_replace('/[^0-9]/', '', $_POST["father_cellphone"] ?? '');
    $father_education = trim($_POST["father_education"] ?? '');
    $father_occupation = trim($_POST["father_occupation"] ?? '');
    $father_income = trim($_POST["father_income"] ?? '');
    $father_income_custom = trim($_POST["father_income_custom"] ?? '');

    $mother_lastname = trim($_POST["mother_lastname"] ?? '');
    $mother_givenname = trim($_POST["mother_givenname"] ?? '');
    $mother_middlename = trim($_POST["mother_middlename"] ?? '');
    $mother_cellphone = preg_replace('/[^0-9]/', '', $_POST["mother_cellphone"] ?? '');
    $mother_education = trim($_POST["mother_education"] ?? '');
    $mother_occupation = trim($_POST["mother_occupation"] ?? '');
    $mother_income = trim($_POST["mother_income"] ?? '');
    $mother_income_custom = trim($_POST["mother_income_custom"] ?? '');

    // Get income values as strings (range or custom amount)
    $father_income_value = getIncomeValue($father_income, $father_income_custom);
    $mother_income_value = getIncomeValue($mother_income, $mother_income_custom);

    // House Status
    $house_status = trim($_POST["house_status"] ?? '');

    // Check input errors before inserting in database
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {

        // Start transaction
        $pdo->beginTransaction();

        try {
            // Insert into users table
            $sql = "INSERT INTO users (username, password, role, email, full_name) 
                        VALUES (:username, :password, :role, :email, :full_name)";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            $stmt->bindParam(":role", $param_role, PDO::PARAM_STR);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            $stmt->bindParam(":full_name", $fullName, PDO::PARAM_STR);

            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            $param_role = 'student';

            if (!$stmt->execute()) {
                throw new Exception("Failed to create user account.");
            }

            $user_id = $pdo->lastInsertId();

            // Insert into user_profile table (all personal information)
            $profile_sql = "INSERT INTO user_profile (
                    user_id, full_name, course, major, yr_sec, cell_no, 
                    present_address, permanent_address, zip_code, email, 
                    sex, date_of_birth, age, place_of_birth, civil_status, 
                    religion, disability, indigenous_group
                ) VALUES (
                    :user_id, :full_name, :course, :major, :yr_sec, :cell_no,
                    :present_address, :permanent_address, :zip_code, :email,
                    :sex, :date_of_birth, :age, :place_of_birth, :civil_status,
                    :religion, :disability, :indigenous_group
                )";

            $profile_stmt = $pdo->prepare($profile_sql);
            $profile_stmt->execute([
                ':user_id' => $user_id,
                ':full_name' => $fullName,
                ':course' => $course,
                ':major' => $major,
                ':yr_sec' => $yr_sec,
                ':cell_no' => $cell_no,
                ':present_address' => $present_address,
                ':permanent_address' => $permanent_address,
                ':zip_code' => $zip_code,
                ':email' => $email,
                ':sex' => $sex,
                ':date_of_birth' => $date_of_birth,
                ':age' => $age,
                ':place_of_birth' => $place_of_birth,
                ':civil_status' => $civil_status,
                ':religion' => $religion,
                ':disability' => $disability,
                ':indigenous_group' => $indigenous_group
            ]);

            // Insert into schools_attended table
            $schools_sql = "INSERT INTO user_schools_attended (
                    user_id, elementary, elementary_year_grad, elementary_honors,
                    secondary, secondary_year_grad, secondary_honors,
                    college, college_year_grad, college_honors
                ) VALUES (
                    :user_id, :elementary, :elementary_year_grad, :elementary_honors,
                    :secondary, :secondary_year_grad, :secondary_honors,
                    :college, :college_year_grad, :college_honors
                )";

            $schools_stmt = $pdo->prepare($schools_sql);
            $schools_stmt->bindParam(':user_id', $user_id);
            $schools_stmt->bindParam(':elementary', $elementary);
            $schools_stmt->bindParam(':elementary_year_grad', $elementary_year_grad);
            $schools_stmt->bindParam(':elementary_honors', $elementary_honors);
            $schools_stmt->bindParam(':secondary', $secondary);
            $schools_stmt->bindParam(':secondary_year_grad', $secondary_year_grad);
            $schools_stmt->bindParam(':secondary_honors', $secondary_honors);
            $schools_stmt->bindParam(':college', $college);
            $schools_stmt->bindParam(':college_year_grad', $college_year_grad);
            $schools_stmt->bindParam(':college_honors', $college_honors);

            // Handle NULL value for college_year_grad
            if ($college_year_grad === null) {
                $schools_stmt->bindValue(':college_year_grad', null, PDO::PARAM_NULL);
            } else {
                $schools_stmt->bindParam(':college_year_grad', $college_year_grad);
            }

            if (!$schools_stmt->execute()) {
                throw new Exception("Failed to save schools attended information.");
            }

            // Insert into parents_info table
            $parents_sql = "INSERT INTO user_parents_info (
                    user_id, 
                    father_lastname, father_givenname, father_middlename, father_cellphone,
                    father_education, father_occupation, father_income,
                    mother_lastname, mother_givenname, mother_middlename, mother_cellphone,
                    mother_education, mother_occupation, mother_income
                ) VALUES (
                    :user_id,
                    :father_lastname, :father_givenname, :father_middlename, :father_cellphone,
                    :father_education, :father_occupation, :father_income,
                    :mother_lastname, :mother_givenname, :mother_middlename, :mother_cellphone,
                    :mother_education, :mother_occupation, :mother_income
                )";

            $parents_stmt = $pdo->prepare($parents_sql);
            $parents_stmt->execute([
                ':user_id' => $user_id,
                ':father_lastname' => $father_lastname,
                ':father_givenname' => $father_givenname,
                ':father_middlename' => $father_middlename,
                ':father_cellphone' => $father_cellphone,
                ':father_education' => $father_education,
                ':father_occupation' => $father_occupation,
                ':father_income' => $father_income_value,  // Use converted value
                ':mother_lastname' => $mother_lastname,
                ':mother_givenname' => $mother_givenname,
                ':mother_middlename' => $mother_middlename,
                ':mother_cellphone' => $mother_cellphone,
                ':mother_education' => $mother_education,
                ':mother_occupation' => $mother_occupation,
                ':mother_income' => $mother_income_value   // Use converted value
            ]);

            // Insert into house_info table
            $house_sql = "INSERT INTO user_house_info (user_id, house_status) VALUES (:user_id, :house_status)";
            $house_stmt = $pdo->prepare($house_sql);
            $house_stmt->execute([
                ':user_id' => $user_id,
                ':house_status' => $house_status
            ]);

            // Commit transaction
            $pdo->commit();

            // Log activity
            $action = "Account created with full profile";
            $details = "User '$username' account created with complete profile information.";
            logActivity($pdo, $user_id, $action, $details);

            // Show success message and redirect
            echo "<script>
                        alert('Account successfully created with complete profile! You can now log in.');
                        window.location.href = './login.php';
                    </script>";
            exit();

        } catch (Exception $e) {
            // Rollback transaction on error
            $pdo->rollBack();
            echo "<script>
                        alert('Error creating account: " . addslashes($e->getMessage()) . "');
                    </script>";
        }
    }

    // Close connection
    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <script src="../js/fontawesome.js" crossorigin="anonymous"></script>

    <style>
        /* Modern Maroon-Gold Light Mode Theme */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: linear-gradient(135deg, #f9f5f0 0%, #f2eee8 100%);
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            padding: 20px;
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: transparent;
            line-height: 1.5;
            color: #5d4037;
        }

        .registration-container {
            width: 100%;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(123, 17, 19, 0.1);
            overflow: hidden;
            max-width: 1000px;
            border: 1px solid #e8d6c3;
        }

        .registration-header {
            background: linear-gradient(135deg, #7B1113 0%, #CFB53B 100%);
            color: white;
            padding: 32px 24px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .registration-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 1px, transparent 1px);
            background-size: 20px 20px;
            opacity: 0.1;
            animation: float 20s linear infinite;
        }

        @keyframes float {
            0% {
                transform: translate(0, 0) rotate(0deg);
            }

            100% {
                transform: translate(-20px, -20px) rotate(360deg);
            }
        }

        .registration-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            line-height: 1.2;
            position: relative;
            z-index: 1;
            letter-spacing: -0.025em;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .registration-header p {
            margin: 12px 0 0 0;
            opacity: 0.95;
            font-size: 16px;
            line-height: 1.5;
            font-weight: 400;
            position: relative;
            z-index: 1;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        /* Modern Wizard Flow Chart - LINE REMOVED ON MOBILE */
        .wizard-flow-chart {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin: 40px 20px 48px;
            counter-reset: step;
            padding-bottom: 12px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
            gap: 8px;
            /* Add gap for better spacing on mobile */
        }

        .wizard-flow-chart::-webkit-scrollbar {
            display: none;
        }

        /* Line styling - HIDDEN ON MOBILE */
        .wizard-flow-chart::before {
            content: "";
            position: absolute;
            top: 20px;
            height: 4px;
            background: linear-gradient(90deg, #e8d6c3 0%, #e8d6c3 100%);
            z-index: 0;
            border-radius: 2px;
            transition: all 0.3s ease;

            /* Default for larger screens */
            left: 8%;
            right: 8%;
        }

        /* Hide line on mobile */
        @media (max-width: 767px) {
            .wizard-flow-chart::before {
                display: none;
            }
        }

        /* Tablet and up - show line */
        @media (min-width: 768px) {
            .wizard-flow-chart::before {
                display: block;
                left: 10%;
                right: 10%;
            }
        }

        /* Desktop */
        @media (min-width: 1024px) {
            .wizard-flow-chart::before {
                left: 8%;
                right: 8%;
            }
        }

        .wizard-flow-chart.active-progress::before {
            background: linear-gradient(90deg, #7B1113 0%, #CFB53B 100%);
        }

        .wizard-flow-chart .step {
            text-align: center;
            position: relative;
            flex-shrink: 0;
            min-width: 64px;
            padding: 0 8px;
            transition: all 0.3s ease;
        }

        .wizard-flow-chart .step:hover {
            transform: translateY(-2px);
        }

        .wizard-flow-chart .step span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f9f5f0;
            color: #8B7D6B;
            font-weight: 600;
            margin-bottom: 10px;
            transition: all 0.3s ease;
            font-size: 16px;
            z-index: 1;
            position: relative;
            border: 2px solid transparent;
        }

        .wizard-flow-chart .step.current span {
            background: linear-gradient(135deg, #7B1113 0%, #CFB53B 100%);
            color: white;
            transform: scale(1.1);
            box-shadow: 0 6px 16px rgba(123, 17, 19, 0.3);
            border-color: white;
        }

        .wizard-flow-chart .step.completed span {
            background: #8B4513;
            color: white;
            box-shadow: 0 4px 12px rgba(139, 69, 19, 0.3);
        }

        .step-label {
            font-size: 12px;
            color: #8B7D6B;
            font-weight: 500;
            line-height: 1.3;
            word-break: break-word;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            transition: color 0.3s ease;
        }

        .step.current .step-label {
            color: #7B1113;
            font-weight: 600;
        }

        .progress-counter {
            text-align: center;
            margin: 20px 0 28px;
            font-size: 16px;
            color: #8B7D6B;
            font-weight: 600;
            padding: 0 24px;
        }

        .progress-counter span {
            color: #7B1113;
            font-weight: 700;
            font-size: 20px;
        }

        /* Form Sections */
        .form-section {
            display: none;
            padding: 28px;
            animation: fadeIn 0.4s ease;
        }

        .form-section.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-section h2 {
            color: #5d4037;
            font-size: 22px;
            margin-bottom: 28px;
            padding-bottom: 16px;
            border-bottom: 2px solid #f9f5f0;
            font-weight: 700;
            letter-spacing: -0.025em;
            position: relative;
        }

        .form-section h2::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 60px;
            height: 2px;
            background: linear-gradient(90deg, #7B1113 0%, #CFB53B 100%);
            border-radius: 1px;
        }

        .form-section h3 {
            color: #7B1113;
            font-size: 18px;
            margin: 32px 0 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f9f5f0;
            font-weight: 600;
        }

        .form-row {
            margin-bottom: 24px;
            position: relative;
        }

        .form-row label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #5d4037;
            font-size: 15px;
            line-height: 1.4;
        }

        .required-field::after {
            content: " *";
            color: #d32f2f;
            font-weight: 700;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e8d6c3;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            box-sizing: border-box;
            -webkit-appearance: none;
            appearance: none;
            background-color: #fdfbf8;
            color: #5d4037;
            font-family: inherit;
            min-height: 52px;
        }

        /* Fix iOS zoom on input focus */
        @media screen and (max-width: 768px) {

            input,
            select,
            textarea {
                font-size: 16px !important;
            }
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #7B1113;
            box-shadow: 0 0 0 4px rgba(123, 17, 19, 0.1);
            outline: none;
            background-color: #fffdfa;
        }

        input:hover,
        select:hover,
        textarea:hover {
            border-color: #CFB53B;
        }

        select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%237B1113' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 18px center;
            background-size: 16px;
            padding-right: 48px;
            cursor: pointer;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
            line-height: 1.5;
            padding-top: 16px;
        }

        /* Address Checkbox */
        .address-checkbox {
            margin: 20px 0 24px;
            display: flex;
            align-items: center;
            padding: 12px 16px;
            background: #f9f5f0;
            border-radius: 12px;
            border: 1px solid #e8d6c3;
            transition: all 0.3s ease;
        }

        .address-checkbox:hover {
            background: #f2eee8;
            border-color: #CFB53B;
        }

        .address-checkbox input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin: 0;
            flex-shrink: 0;
            cursor: pointer;
            accent-color: #7B1113;
        }

        .address-checkbox label {
            margin-left: 12px;
            font-weight: 500;
            font-size: 15px;
            color: #5d4037;
            cursor: pointer;
        }

        /* Other Specify Fields */
        .specify-field {
            margin-top: 16px;
            padding: 16px;
            background: #f9f5f0;
            border-radius: 12px;
            border-left: 4px solid #7B1113;
            display: none;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .specify-field label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 14px;
            color: #7B1113;
        }

        /* Error Handling */
        .section-error {
            background: #ffebee;
            color: #b71c1c;
            padding: 18px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: none;
            font-size: 14px;
            border: 1px solid #ffcdd2;
            animation: shake 0.5s ease;
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
                transform: translateX(-4px);
            }

            20%,
            40%,
            60%,
            80% {
                transform: translateX(4px);
            }
        }

        .section-error strong {
            display: block;
            margin-bottom: 10px;
            font-size: 15px;
            font-weight: 600;
        }

        .section-error ul {
            margin: 0;
            padding-left: 20px;
        }

        .section-error li {
            margin-bottom: 6px;
            line-height: 1.4;
        }

        .error-message {
            color: #d32f2f;
            font-size: 14px;
            margin-top: 8px;
            display: none;
            line-height: 1.4;
            font-weight: 500;
        }

        .form-row.error .error-message {
            display: block;
        }

        .form-row.error input,
        .form-row.error select,
        .form-row.error textarea {
            border-color: #d32f2f;
            background-color: #ffebee;
        }

        .form-row.error input:focus,
        .form-row.error select:focus,
        .form-row.error textarea:focus {
            box-shadow: 0 0 0 4px rgba(211, 47, 47, 0.1);
        }

        /* House Status Group - Modern Cards */
        .house-status-group {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-top: 12px;
        }

        .house-option {
            width: 100%;
        }

        .house-option input[type="radio"] {
            display: none;
        }

        .house-option .option-label {
            display: block;
            padding: 22px 20px;
            border: 2px solid #e8d6c3;
            border-radius: 14px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 16px;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
            background: white;
            color: #5d4037;
            position: relative;
            overflow: hidden;
        }

        .house-option .option-label::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #7B1113 0%, #CFB53B 100%);
            transform: translateY(-100%);
            transition: transform 0.3s ease;
        }

        .house-option input[type="radio"]:checked+.option-label {
            border-color: #7B1113;
            background: linear-gradient(135deg, rgba(123, 17, 19, 0.05) 0%, rgba(207, 181, 59, 0.05) 100%);
            color: #7B1113;
            font-weight: 700;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(123, 17, 19, 0.15);
        }

        .house-option input[type="radio"]:checked+.option-label::before {
            transform: translateY(0);
        }

        .house-option .option-label:hover {
            border-color: #CFB53B;
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(123, 17, 19, 0.08);
        }

        /* Error Box */
        .error-box {
            background: #ffebee;
            color: #b71c1c;
            padding: 18px;
            border-radius: 12px;
            margin: 20px;
            border-left: 4px solid #d32f2f;
            font-size: 14px;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .error-box p {
            margin: 8px 0;
            line-height: 1.4;
        }

        /* Button Row */
        .button-row {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-top: 36px;
            padding-top: 28px;
            border-top: 2px solid #f9f5f0;
        }

        @media (min-width: 480px) {
            .button-row {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
        }

        .buttonNav,
        .submitBtn {
            padding: 18px 32px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            min-height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
            -webkit-tap-highlight-color: transparent;
            touch-action: manipulation;
            font-family: inherit;
            letter-spacing: -0.025em;
            position: relative;
            overflow: hidden;
        }

        @media (min-width: 480px) {

            .buttonNav,
            .submitBtn {
                min-width: 140px;
            }
        }

        .buttonNav {
            background: #8B7D6B;
            color: white;
            order: 2;
        }

        @media (min-width: 480px) {
            .buttonNav {
                order: 1;
            }
        }

        .buttonNav:hover,
        .buttonNav:active {
            background: #7B6B5A;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(139, 125, 107, 0.3);
        }

        .submitBtn {
            background: linear-gradient(135deg, #7B1113 0%, #CFB53B 100%);
            color: white;
            order: 1;
            position: relative;
            overflow: hidden;
        }

        @media (min-width: 480px) {
            .submitBtn {
                order: 2;
            }
        }

        .submitBtn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.7s ease;
        }

        .submitBtn:hover::before {
            left: 100%;
        }

        .submitBtn:hover,
        .submitBtn:active {
            background: linear-gradient(135deg, #6A0F11 0%, #B8A035 100%);
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(123, 17, 19, 0.3);
        }

        /* Login Link */
        .login-link {
            text-align: center;
            margin-top: 32px;
            padding: 28px 24px;
            background: #f9f5f0;
            border-radius: 0 0 16px 16px;
            font-size: 15px;
            color: #8B7D6B;
            border-top: 1px solid #e8d6c3;
        }

        .login-link a {
            color: #7B1113;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin-left: 6px;
            position: relative;
            transition: color 0.3s ease;
        }

        .login-link a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #7B1113 0%, #CFB53B 100%);
            transition: width 0.3s ease;
        }

        .login-link a:hover,
        .login-link a:active {
            color: #6A0F11;
        }

        .login-link a:hover::after {
            width: 100%;
        }

        /* Validation Summary */
        #validationSummary {
            margin: 20px;
            background: #ffebee;
            border: 1px solid #ffcdd2;
            border-radius: 12px;
            padding: 20px;
            animation: slideDown 0.3s ease;
        }

        #validationSummary h3 {
            color: #b71c1c;
            margin-bottom: 12px;
            font-size: 16px;
            font-weight: 600;
        }

        #errorList {
            padding-left: 20px;
        }

        #errorList li {
            margin-bottom: 6px;
            line-height: 1.4;
            color: #7f1d1d;
        }

        /* Small hint text */
        small {
            display: block;
            margin-top: 8px;
            color: #8B7D6B;
            font-size: 13px;
            line-height: 1.4;
        }

        /* Mobile Optimization for Small Screens */
        @media (max-width: 374px) {
            body {
                padding: 16px;
            }

            .registration-header {
                padding: 24px 16px;
            }

            .registration-header h1 {
                font-size: 24px;
            }

            .registration-header p {
                font-size: 14px;
            }

            .form-section {
                padding: 20px;
            }

            .wizard-flow-chart {
                margin: 32px 16px 40px;
            }

            .wizard-flow-chart .step {
                min-width: 56px;
            }

            .wizard-flow-chart .step span {
                width: 36px;
                height: 36px;
                font-size: 14px;
            }

            .step-label {
                font-size: 11px;
            }

            input,
            select,
            textarea {
                padding: 12px 16px;
                min-height: 48px;
            }

            .buttonNav,
            .submitBtn {
                padding: 16px 24px;
                min-height: 52px;
                font-size: 15px;
            }
        }

        /* Tablet Styles */
        @media (min-width: 768px) {
            body {
                padding: 32px;
                background: linear-gradient(135deg, #f5f0e8 0%, #ece6dc 100%);
            }

            .registration-header {
                padding: 40px 32px;
            }

            .registration-header h1 {
                font-size: 32px;
            }

            .registration-header p {
                font-size: 18px;
            }

            .wizard-flow-chart {
                margin: 48px 32px 56px;
            }

            .wizard-flow-chart .step {
                min-width: 80px;
            }

            .wizard-flow-chart .step span {
                width: 48px;
                height: 48px;
                font-size: 18px;
            }

            .step-label {
                font-size: 14px;
            }

            .progress-counter {
                font-size: 18px;
            }

            .progress-counter span {
                font-size: 24px;
            }

            .form-section {
                padding: 36px;
            }

            .form-section h2 {
                font-size: 24px;
            }

            .form-section h3 {
                font-size: 20px;
            }

            .house-status-group {
                flex-direction: row;
                flex-wrap: wrap;
                justify-content: center;
                gap: 20px;
            }

            .house-option {
                min-width: 200px;
                flex: 1;
            }

            .button-row {
                flex-direction: row;
            }

            .buttonNav,
            .submitBtn {
                padding: 18px 36px;
                min-height: auto;
            }
        }

        /* Desktop Styles */
        @media (min-width: 1024px) {
            .registration-container {
                margin: 40px auto;
            }

            .house-status-group {
                max-width: 600px;
                margin-left: auto;
                margin-right: auto;
            }

            .buttonNav,
            .submitBtn {
                padding: 20px 48px;
                font-size: 17px;
            }

            .login-link {
                padding: 32px 28px;
            }
        }

        /* Orientation-specific adjustments */
        @media (max-width: 767px) and (orientation: landscape) {
            .registration-header {
                padding: 24px;
            }

            .wizard-flow-chart {
                margin: 28px 16px 32px;
            }

            .form-section {
                padding: 24px;
                max-height: 70vh;
                overflow-y: auto;
            }

            .button-row {
                position: sticky;
                bottom: 0;
                background: white;
                padding: 16px 0;
                margin-top: 24px;
                box-shadow: 0 -4px 16px rgba(123, 17, 19, 0.05);
                z-index: 10;
            }
        }

        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }

            .registration-container {
                box-shadow: none;
                border-radius: 0;
                border: 1px solid #ddd;
            }

            .button-row,
            .login-link,
            .wizard-flow-chart {
                display: none !important;
            }

            .form-section {
                display: block !important;
                page-break-inside: avoid;
                padding: 0;
                margin-bottom: 24px;
            }

            input,
            select,
            textarea {
                border: 1px solid #ccc;
                background: transparent;
            }
        }

        /* Accessibility Improvements */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* Focus visible for keyboard navigation */
        .buttonNav:focus-visible,
        .submitBtn:focus-visible,
        input:focus-visible,
        select:focus-visible,
        textarea:focus-visible {
            outline: 3px solid #7B1113;
            outline-offset: 2px;
        }

        /* Custom scrollbar for webkit browsers */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f9f5f0;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: #CFB53B;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #B8A035;
        }

        /* Selection color */
        ::selection {
            background-color: rgba(123, 17, 19, 0.2);
            color: #5d4037;
        }

        /* Placeholder styling */
        ::placeholder {
            color: #A1887F;
            opacity: 1;
        }

        /* Number input spinner */
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            opacity: 1;
            height: 24px;
            cursor: pointer;
            background-color: #7B1113;
        }

        /* Loading state for buttons */
        .buttonNav.loading,
        .submitBtn.loading {
            position: relative;
            color: transparent;
        }

        .buttonNav.loading::after,
        .submitBtn.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Additional Maroon-Gold Theme Enhancements */
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(15%) sepia(80%) saturate(4000%) hue-rotate(350deg);
        }

        input[type="date"]:hover::-webkit-calendar-picker-indicator {
            filter: invert(15%) sepia(80%) saturate(4000%) hue-rotate(350deg) brightness(1.2);
        }

        /* Gradient borders for focused elements */
        input:focus,
        select:focus,
        textarea:focus {
            background-image: linear-gradient(white, white),
                linear-gradient(135deg, #7B1113 0%, #CFB53B 100%);
            background-origin: border-box;
            background-clip: padding-box, border-box;
            border: 2px solid transparent;
        }

        /* Radio button custom styling */
        .house-option input[type="radio"]:checked+.option-label::after {
            content: '✓';
            position: absolute;
            top: 8px;
            right: 8px;
            color: #7B1113;
            font-size: 18px;
            font-weight: bold;
        }

        /* Checkbox custom styling */
        .address-checkbox input[type="checkbox"] {
            border-radius: 4px;
        }

        .address-checkbox input[type="checkbox"]:checked {
            background-color: #7B1113;
            border-color: #7B1113;
        }

        /* Select dropdown styling */
        select option {
            background: white;
            color: #5d4037;
            padding: 12px;
        }

        select option:hover {
            background: #f9f5f0;
        }

        /* Progress bar animation */
        @keyframes progress-glow {
            0% {
                box-shadow: 0 0 5px rgba(123, 17, 19, 0.5);
            }

            50% {
                box-shadow: 0 0 20px rgba(207, 181, 59, 0.7);
            }

            100% {
                box-shadow: 0 0 5px rgba(123, 17, 19, 0.5);
            }
        }

        .wizard-flow-chart .step.current span {
            animation: progress-glow 2s infinite;
        }

        /* Hover effects for form elements */
        .form-row:hover label {
            color: #7B1113;
        }

        .form-row:hover small {
            color: #CFB53B;
        }
    </style>
</head>

<body>
    <div class="registration-container">
        <div class="registration-header">
            <h1>Student Registration</h1>
            <p>Create your account and complete your profile information</p>
        </div>

        <div id="validationSummary" class="section-error" style="display: none;">
            <h3>Please fill up the following required fields:</h3>
            <ul id="errorList"></ul>
        </div>

        <?php if (!empty($username_err) || !empty($password_err) || !empty($confirm_password_err)): ?>
            <div class="error-box">
                <strong>Registration Errors:</strong>
                <?php
                if (!empty($username_err))
                    echo "<p>{$username_err}</p>";
                if (!empty($password_err))
                    echo "<p>{$password_err}</p>";
                if (!empty($confirm_password_err))
                    echo "<p>{$confirm_password_err}</p>";
                ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="registration-form" onSubmit="return validateForm()">
            <div class="wizard-flow-chart">
                <div class="step current" id="step1">
                    <span>1</span>
                    <div class="step-label">Account Info</div>
                </div>
                <div class="step" id="step2">
                    <span>2</span>
                    <div class="step-label">Personal Info</div>
                </div>
                <div class="step" id="step3">
                    <span>3</span>
                    <div class="step-label">Schools Attended</div>
                </div>
                <div class="step" id="step4">
                    <span>4</span>
                    <div class="step-label">Parent's Data</div>
                </div>
                <div class="step" id="step5">
                    <span>5</span>
                    <div class="step-label">House Status</div>
                </div>
            </div>

            <div class="progress-counter">Step <span id="currentStep">1</span> of 5</div>

            <!-- Section 1: Account Information -->
            <section id="account-info" class="form-section active">
                <h2>Account Information</h2>
                <div class="section-error" id="account-info-error"></div>

                <div class="form-row">
                    <label class="required-field">Username</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                    <div class="error-message"><?php echo $username_err; ?></div>
                </div>

                <div class="form-row">
                    <label class="required-field">Password</label>
                    <input type="password" name="password" required>
                    <div class="error-message"><?php echo $password_err; ?></div>
                    <small style="color: #666;">Password must be at least 6 characters long</small>
                </div>

                <div class="form-row">
                    <label class="required-field">Confirm Password</label>
                    <input type="password" name="confirm_password" required>
                    <div class="error-message"><?php echo $confirm_password_err; ?></div>
                </div>

                <div class="button-row">
                    <button type="button" class="buttonNav" onClick="nextStep()">Next</button>
                </div>
            </section>

            <!-- Section 2: Personal Information -->
            <section id="personal-info" class="form-section">
                <h2>Personal Information</h2>
                <div class="section-error" id="personal-info-error"></div>

                <div class="form-row">
                    <label class="required-field">Full Name</label>
                    <input type="text" name="fullName" value="<?php echo htmlspecialchars($fullName); ?>" required>
                    <div class="error-message">Please enter your full name</div>
                </div>

                <div class="form-row">
                    <label class="required-field">Course / Major</label>
                    <select name="course_major" required>
                        <option value="" disabled selected>Select Course / Major</option>
                        <?php foreach ($course_major_list as $c): ?>
                            <option value="<?= htmlspecialchars($c['course']) ?>|<?= htmlspecialchars($c['major']) ?>"
                                <?= ($c['course'] === $course && $c['major'] === $major) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['course']) ?> / <?= htmlspecialchars($c['major']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="error-message">Please select course and major</div>
                </div>

                <div class="form-row">
                    <label class="required-field">Year/Section</label>
                    <input type="text" name="yr_sec" value="<?php echo htmlspecialchars($yr_sec); ?>" required>
                    <div class="error-message">Please enter year/section</div>
                </div>

                <div class="form-row">
                    <label class="required-field">Cellphone Number</label>
                    <input type="text" name="cellNo" value="<?php echo htmlspecialchars($cell_no); ?>" required
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="11">
                    <div class="error-message">Please enter a valid 11-digit cellphone number</div>
                </div>

                <div class="form-row">
                    <label class="required-field">Complete Present Address</label>
                    <input type="text" name="pres_address" value="<?php echo htmlspecialchars($present_address); ?>"
                        required>
                    <div class="error-message">Please enter complete present address</div>
                </div>

                <div class="form-row">
                    <label class="required-field">Complete Permanent Address</label>
                    <input type="text" name="perma_address" id="perma_address"
                        value="<?php echo htmlspecialchars($permanent_address); ?>" required>
                    <div class="error-message">Please enter complete permanent address</div>
                </div>

                <div class="address-checkbox">
                    <input type="checkbox" id="same_address" onchange="copyAddress()">
                    <label for="same_address">Same as Present Address</label>
                </div>

                <div class="form-row">
                    <label class="required-field">ZIP Code</label>
                    <input type="number" name="zip_code" value="<?php echo htmlspecialchars($zip_code); ?>" required>
                    <div class="error-message">Please enter ZIP code</div>
                </div>

                <div class="form-row">
                    <label class="required-field">Email Address</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    <div class="error-message">Please enter a valid email address</div>
                </div>

                <div class="form-row">
                    <label class="required-field">Sex</label>
                    <select name="sex" id="sex" required onchange="toggleOtherField(this, 'sex_other')">
                        <option value="" disabled selected>Select Sex</option>
                        <option value="male" <?php echo ($sex == 'male') ? 'selected' : ''; ?>>Male</option>
                        <option value="female" <?php echo ($sex == 'female') ? 'selected' : ''; ?>>Female</option>
                        <!-- <option value="others" <?php echo (in_array($sex, ['male', 'female']) ? '' : 'selected'); ?>>Others</option> -->
                    </select>
                    <div class="specify-field" id="sex_other_field">
                        <label for="sex_other">Please specify:</label>
                        <input type="text" name="sex_other" id="sex_other"
                            value="<?php echo (in_array($sex, ['male', 'female']) ? '' : htmlspecialchars($sex)); ?>"
                            placeholder="Please specify your gender">
                    </div>
                    <div class="error-message">Please select your sex</div>
                </div>

                <div class="form-row">
                    <label class="required-field">Date of Birth</label>
                    <input type="date" name="date_of_birth" id="date_of_birth"
                        value="<?php echo htmlspecialchars($date_of_birth); ?>" onchange="calculateAge()" required>
                    <div class="error-message">Please enter your date of birth</div>
                </div>

                <div class="form-row">
                    <label class="required-field">Age</label>
                    <input type="number" name="age" id="age" value="<?php echo htmlspecialchars($age); ?>" readonly>
                </div>

                <div class="form-row">
                    <label class="required-field">Place of Birth</label>
                    <input type="text" name="place_of_birth" value="<?php echo htmlspecialchars($place_of_birth); ?>"
                        required>
                    <div class="error-message">Please enter your place of birth</div>
                </div>

                <div class="form-row">
                    <label class="required-field">Civil Status</label>
                    <select name="civil_status" required>
                        <option value="" disabled selected>Select Civil Status</option>
                        <option value="single" <?php echo ($civil_status == 'single') ? 'selected' : ''; ?>>Single
                        </option>
                        <option value="married" <?php echo ($civil_status == 'married') ? 'selected' : ''; ?>>Married
                        </option>
                        <option value="widowed" <?php echo ($civil_status == 'widowed') ? 'selected' : ''; ?>>Widowed
                        </option>
                        <option value="divorced" <?php echo ($civil_status == 'divorced') ? 'selected' : ''; ?>>Divorced
                        </option>
                        <option value="separated" <?php echo ($civil_status == 'separated') ? 'selected' : ''; ?>>
                            Separated</option>
                    </select>
                    <div class="error-message">Please select your civil status</div>
                </div>

                <div class="form-row">
                    <label class="required-field">Religion</label>
                    <select name="religion" id="religion" required onchange="toggleOtherField(this, 'religion_other')">
                        <option value="" disabled selected>Select Religion</option>
                        <option value="roman catholic" <?php echo ($religion == 'roman catholic') ? 'selected' : ''; ?>>
                            Roman Catholic</option>
                        <option value="islam" <?php echo ($religion == 'islam') ? 'selected' : ''; ?>>Islam</option>
                        <option value="iglesia ni cristo" <?php echo ($religion == 'iglesia ni cristo') ? 'selected' : ''; ?>>Iglesia ni Cristo</option>
                        <option value="evangelical christian" <?php echo ($religion == 'evangelical christian') ? 'selected' : ''; ?>>Evangelical Christian</option>
                        <option value="a biblical church" <?php echo ($religion == 'a biblical church') ? 'selected' : ''; ?>>Aglipayan / Philippine Independent Church</option>
                        <option value="others" <?php echo (!in_array($religion, ['roman catholic', 'islam', 'iglesia ni cristo', 'evangelical christian', 'a biblical church']) && !empty($religion)) ? 'selected' : ''; ?>>Others</option>
                    </select>
                    <div class="specify-field" id="religion_other_field">
                        <label for="religion_other">Please specify:</label>
                        <input type="text" name="religion_other" id="religion_other"
                            value="<?php echo (!in_array($religion, ['roman catholic', 'islam', 'iglesia ni cristo', 'evangelical christian', 'a biblical church']) && !empty($religion)) ? htmlspecialchars($religion) : ''; ?>"
                            placeholder="Please specify your religion">
                    </div>
                    <div class="error-message">Please select your religion</div>
                </div>

                <div class="form-row">
                    <label class="required-field">Type of Disability</label>
                    <input type="text" name="disability" value="<?php echo htmlspecialchars($disability); ?>" required
                        placeholder="Enter 'None' if not applicable">
                    <div class="error-message">Please enter type of disability</div>
                </div>

                <div class="form-row">
                    <label class="required-field">Indigenous People Group</label>
                    <select name="indigenous_group" id="indigenous_group" required
                        onchange="toggleOtherField(this, 'indigenous_group_other')">
                        <option value="" disabled selected>Select Indigenous People Group</option>
                        <option value="igorot" <?php echo ($indigenous_group == 'igorot') ? 'selected' : ''; ?>>Igorot
                        </option>
                        <option value="lumad" <?php echo ($indigenous_group == 'lumad') ? 'selected' : ''; ?>>Lumad
                        </option>
                        <option value="moro" <?php echo ($indigenous_group == 'moro') ? 'selected' : ''; ?>>Moro</option>
                        <option value="aeta" <?php echo ($indigenous_group == 'aeta') ? 'selected' : ''; ?>>Aeta</option>
                        <option value="badjao" <?php echo ($indigenous_group == 'badjao') ? 'selected' : ''; ?>>Badjao
                        </option>
                        <option value="others" <?php echo (!in_array($indigenous_group, ['igorot', 'lumad', 'moro', 'aeta', 'badjao', 'N/A']) && !empty($indigenous_group)) ? 'selected' : ''; ?>>Others
                        </option>
                        <option value="N/A" <?php echo ($indigenous_group == 'N/A') ? 'selected' : ''; ?>>Not Applicable
                        </option>
                    </select>
                    <div class="specify-field" id="indigenous_group_other_field">
                        <label for="indigenous_group_other">Please specify:</label>
                        <input type="text" name="indigenous_group_other" id="indigenous_group_other"
                            value="<?php echo (!in_array($indigenous_group, ['igorot', 'lumad', 'moro', 'aeta', 'badjao', 'N/A']) && !empty($indigenous_group)) ? htmlspecialchars($indigenous_group) : ''; ?>"
                            placeholder="Please specify your indigenous group">
                    </div>
                    <div class="error-message">Please select indigenous group</div>
                </div>

                <div class="button-row">
                    <button type="button" class="buttonNav" onClick="prevStep()">Previous</button>
                    <button type="button" class="buttonNav" onClick="nextStep()">Next</button>
                </div>
            </section>

            <!-- Section 3: Schools Attended -->
            <section id="schools-attended" class="form-section">
                <h2>Schools Attended</h2>
                <div class="section-error" id="schools-attended-error"></div>

                <h3>Elementary School</h3>
                <div class="form-row">
                    <label class="required-field">Elementary School Name</label>
                    <input type="text" name="elementary" value="<?php echo htmlspecialchars($elementary); ?>" required>
                    <div class="error-message">Please enter elementary school name</div>
                </div>

                <div class="form-row">
                    <label class="required-field">Year Graduated</label>
                    <input type="number" name="elementary_yr_grad"
                        value="<?php echo htmlspecialchars($elementary_year_grad); ?>" required min="1900"
                        max="<?php echo date('Y'); ?>">
                    <div class="error-message">Please enter a valid graduation year</div>
                </div>

                <div class="form-row">
                    <label>Honors Received</label>
                    <input type="text" name="elementary_honors_rec"
                        value="<?php echo htmlspecialchars($elementary_honors); ?>"
                        placeholder="Enter 'None' if not applicable">
                </div>

                <br>
                <h3>Secondary School</h3>
                <div class="form-row">
                    <label class="required-field">Secondary School Name</label>
                    <input type="text" name="secondary" value="<?php echo htmlspecialchars($secondary); ?>" required>
                    <div class="error-message">Please enter secondary school name</div>
                </div>

                <div class="form-row">
                    <label class="required-field">Year Graduated</label>
                    <input type="number" name="secondary_yr_grad"
                        value="<?php echo htmlspecialchars($secondary_year_grad); ?>" required min="1900"
                        max="<?php echo date('Y'); ?>">
                    <div class="error-message">Please enter a valid graduation year</div>
                </div>

                <div class="form-row">
                    <label>Honors Received</label>
                    <input type="text" name="secondary_honors_rec"
                        value="<?php echo htmlspecialchars($secondary_honors); ?>"
                        placeholder="Enter 'None' if not applicable">
                </div>

                <br>
                <h3>College/University (If applicable)</h3>
                <div class="form-row">
                    <label>College/University Name</label>
                    <input type="text" name="college" value="<?php echo htmlspecialchars($college); ?>"
                        placeholder="Leave blank if not applicable">
                </div>

                <div class="form-row">
                    <label>Year Graduated</label>
                    <input type="number" name="college_yr_grad"
                        value="<?php echo htmlspecialchars($college_year_grad); ?>" min="1900"
                        max="<?php echo date('Y'); ?>" placeholder="Leave blank if not applicable">
                </div>

                <div class="form-row">
                    <label>Honors Received</label>
                    <input type="text" name="college_honors_rec"
                        value="<?php echo htmlspecialchars($college_honors); ?>"
                        placeholder="Enter 'None' if not applicable">
                </div>

                <div class="button-row">
                    <button type="button" class="buttonNav" onClick="prevStep()">Previous</button>
                    <button type="button" class="buttonNav" onClick="nextStep()">Next</button>
                </div>
            </section>

            <!-- Section 4: Parents Data -->
            <section id="parents-data" class="form-section">
                <h2>Parent's Data</h2>
                <div class="section-error" id="parents-data-error"></div>

                <h3>Father's Information</h3>
                <div class="form-row">
                    <label class="required-field">Last Name</label>
                    <input type="text" name="father_lastname" value="<?php echo htmlspecialchars($father_lastname); ?>"
                        required>
                    <div class="error-message">Please enter father's last name</div>
                </div>

                <div class="form-row">
                    <label class="required-field">Given Name</label>
                    <input type="text" name="father_givenname"
                        value="<?php echo htmlspecialchars($father_givenname); ?>" required>
                    <div class="error-message">Please enter father's given name</div>
                </div>

                <div class="form-row">
                    <label>Middle Name</label>
                    <input type="text" name="father_middlename"
                        value="<?php echo htmlspecialchars($father_middlename); ?>">
                </div>

                <div class="form-row">
                    <label class="required-field">Cellphone Number</label>
                    <input type="text" name="father_cellphone"
                        value="<?php echo htmlspecialchars($father_cellphone); ?>" required
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="11">
                    <div class="error-message">Please enter a valid 11-digit cellphone number</div>
                </div>

                <div class="form-row">
                    <label class="required-field">Educational Attainment</label>
                    <select name="father_education" required>
                        <option value="" disabled selected>Select Educational Attainment</option>
                        <option value="No Formal Education" <?php echo ($father_education == 'No Formal Education') ? 'selected' : ''; ?>>No Formal Education</option>
                        <option value="Elementary Undergraduate" <?php echo ($father_education == 'Elementary Undergraduate') ? 'selected' : ''; ?>>Elementary Undergraduate</option>
                        <option value="Elementary Graduate" <?php echo ($father_education == 'Elementary Graduate') ? 'selected' : ''; ?>>Elementary Graduate</option>
                        <option value="High School Undergraduate" <?php echo ($father_education == 'High School Undergraduate') ? 'selected' : ''; ?>>High School Undergraduate</option>
                        <option value="High School Graduate" <?php echo ($father_education == 'High School Graduate') ? 'selected' : ''; ?>>High School Graduate</option>
                        <option value="Vocational Course" <?php echo ($father_education == 'Vocational Course') ? 'selected' : ''; ?>>Vocational Course</option>
                        <option value="College Undergraduate" <?php echo ($father_education == 'College Undergraduate') ? 'selected' : ''; ?>>College Undergraduate</option>
                        <option value="College Graduate" <?php echo ($father_education == 'College Graduate') ? 'selected' : ''; ?>>College Graduate</option>
                        <option value="Postgraduate" <?php echo ($father_education == 'Postgraduate') ? 'selected' : ''; ?>>Postgraduate (Master's/PhD)</option>
                    </select>
                    <div class="error-message">Please select father's educational attainment</div>
                </div>

                <div class="form-row">
                    <label class="required-field">Occupation</label>
                    <select name="father_occupation" required>
                        <option value="" disabled selected>Select Occupation</option>
                        <option value="Government" <?php echo ($father_occupation == 'Government') ? 'selected' : ''; ?>>
                            Government</option>
                        <option value="Private Sector" <?php echo ($father_occupation == 'Private Sector') ? 'selected' : ''; ?>>Private Sector</option>
                        <option value="Self-Employed" <?php echo ($father_occupation == 'Self-Employed') ? 'selected' : ''; ?>>Self-Employed</option>
                        <option value="Laborer" <?php echo ($father_occupation == 'Laborer') ? 'selected' : ''; ?>>Laborer
                        </option>
                        <option value="Freelancer" <?php echo ($father_occupation == 'Freelancer') ? 'selected' : ''; ?>>
                            Freelancer</option>
                        <option value="NGO/Non-Profit" <?php echo ($father_occupation == 'NGO/Non-Profit') ? 'selected' : ''; ?>>NGO/Non-Profit</option>
                        <option value="Overseas Employment" <?php echo ($father_occupation == 'Overseas Employment') ? 'selected' : ''; ?>>Overseas Employment</option>
                        <option value="Casual" <?php echo ($father_occupation == 'Casual') ? 'selected' : ''; ?>>Casual
                        </option>
                        <option value="Contractual" <?php echo ($father_occupation == 'Contractual') ? 'selected' : ''; ?>>Contractual</option>
                        <option value="Intern" <?php echo ($father_occupation == 'Intern') ? 'selected' : ''; ?>>Intern
                        </option>
                    </select>
                    <div class="error-message">Please select father's occupation</div>
                </div>

                <div class="form-row">
                    <label class="required-field">Monthly Income</label>
                    <select name="father_income" id="father_income" required
                        onchange="toggleCustomIncome(this, 'father')">
                        <option value="" disabled selected>Select Monthly Income Range</option>
                        <option value="0-5000" <?php echo ($father_income == '0-5000') ? 'selected' : ''; ?>>₱0 - ₱5,000
                        </option>
                        <option value="5000-10000" <?php echo ($father_income == '5000-10000') ? 'selected' : ''; ?>>
                            ₱5,000 - ₱10,000</option>
                        <option value="10000-15000" <?php echo ($father_income == '10000-15000') ? 'selected' : ''; ?>>
                            ₱10,000 - ₱15,000</option>
                        <option value="15000-20000" <?php echo ($father_income == '15000-20000') ? 'selected' : ''; ?>>
                            ₱15,000 - ₱20,000</option>
                        <option value="20000-25000" <?php echo ($father_income == '20000-25000') ? 'selected' : ''; ?>>
                            ₱20,000 - ₱25,000</option>
                        <option value="25000-30000" <?php echo ($father_income == '25000-30000') ? 'selected' : ''; ?>>
                            ₱25,000 - ₱30,000</option>
                        <option value="30000-35000" <?php echo ($father_income == '30000-35000') ? 'selected' : ''; ?>>
                            ₱30,000 - ₱35,000</option>
                        <option value="35000-40000" <?php echo ($father_income == '35000-40000') ? 'selected' : ''; ?>>
                            ₱35,000 - ₱40,000</option>
                        <option value="40000-45000" <?php echo ($father_income == '40000-45000') ? 'selected' : ''; ?>>
                            ₱40,000 - ₱45,000</option>
                        <option value="45000-50000" <?php echo ($father_income == '45000-50000') ? 'selected' : ''; ?>>
                            ₱45,000 - ₱50,000</option>
                        <option value="50000+" <?php echo ($father_income == '50000+') ? 'selected' : ''; ?>>₱50,000+
                        </option>
                        <option value="Not Applicable" <?php echo ($father_income == 'Not Applicable') ? 'selected' : ''; ?>>Not Applicable (No Income)</option>
                        <option value="Prefer not to say" <?php echo ($father_income == 'Prefer not to say') ? 'selected' : ''; ?>>Prefer not to say</option>
                        <option value="custom" <?php echo ($father_income == 'custom') ? 'selected' : ''; ?>>Custom Amount
                        </option>
                    </select>
                    <div class="specify-field" id="father_custom_income_field">
                        <label for="father_income_custom">Enter Exact Monthly Income:</label>
                        <input type="number" name="father_income_custom" id="father_income_custom"
                            value="<?php echo htmlspecialchars($father_income_custom); ?>" min="0" step="0.01"
                            placeholder="0.00">
                        <small>Enter the exact monthly income in pesos (e.g., 32500.50)</small>
                    </div>
                    <div class="error-message">Please select monthly income range</div>
                </div>

                <br>
                <h3>Mother's Information</h3>
                <div class="form-row">
                    <label class="required-field">Maiden Name</label>
                    <input type="text" name="mother_lastname" value="<?php echo htmlspecialchars($mother_lastname); ?>"
                        required>
                    <div class="error-message">Please enter mother's maiden name</div>
                </div>

                <div class="form-row">
                    <label class="required-field">Given Name</label>
                    <input type="text" name="mother_givenname"
                        value="<?php echo htmlspecialchars($mother_givenname); ?>" required>
                    <div class="error-message">Please enter mother's given name</div>
                </div>

                <div class="form-row">
                    <label>Middle Name</label>
                    <input type="text" name="mother_middlename"
                        value="<?php echo htmlspecialchars($mother_middlename); ?>">
                </div>

                <div class="form-row">
                    <label class="required-field">Cellphone Number</label>
                    <input type="text" name="mother_cellphone"
                        value="<?php echo htmlspecialchars($mother_cellphone); ?>" required
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="11">
                    <div class="error-message">Please enter a valid 11-digit cellphone number</div>
                </div>

                <div class="form-row">
                    <label class="required-field">Educational Attainment</label>
                    <select name="mother_education" required>
                        <option value="" disabled selected>Select Educational Attainment</option>
                        <option value="No Formal Education" <?php echo ($mother_education == 'No Formal Education') ? 'selected' : ''; ?>>No Formal Education</option>
                        <option value="Elementary Undergraduate" <?php echo ($mother_education == 'Elementary Undergraduate') ? 'selected' : ''; ?>>Elementary Undergraduate</option>
                        <option value="Elementary Graduate" <?php echo ($mother_education == 'Elementary Graduate') ? 'selected' : ''; ?>>Elementary Graduate</option>
                        <option value="High School Undergraduate" <?php echo ($mother_education == 'High School Undergraduate') ? 'selected' : ''; ?>>High School Undergraduate</option>
                        <option value="High School Graduate" <?php echo ($mother_education == 'High School Graduate') ? 'selected' : ''; ?>>High School Graduate</option>
                        <option value="Vocational Course" <?php echo ($mother_education == 'Vocational Course') ? 'selected' : ''; ?>>Vocational Course</option>
                        <option value="College Undergraduate" <?php echo ($mother_education == 'College Undergraduate') ? 'selected' : ''; ?>>College Undergraduate</option>
                        <option value="College Graduate" <?php echo ($mother_education == 'College Graduate') ? 'selected' : ''; ?>>College Graduate</option>
                        <option value="Postgraduate" <?php echo ($mother_education == 'Postgraduate') ? 'selected' : ''; ?>>Postgraduate (Master's/PhD)</option>
                    </select>
                    <div class="error-message">Please select mother's educational attainment</div>
                </div>

                <div class="form-row">
                    <label class="required-field">Occupation</label>
                    <select name="mother_occupation" required>
                        <option value="" disabled selected>Select Occupation</option>
                        <option value="Government" <?php echo ($mother_occupation == 'Government') ? 'selected' : ''; ?>>
                            Government</option>
                        <option value="Private Sector" <?php echo ($mother_occupation == 'Private Sector') ? 'selected' : ''; ?>>Private Sector</option>
                        <option value="Self-Employed" <?php echo ($mother_occupation == 'Self-Employed') ? 'selected' : ''; ?>>Self-Employed</option>
                        <option value="Laborer" <?php echo ($mother_occupation == 'Laborer') ? 'selected' : ''; ?>>Laborer
                        </option>
                        <option value="Freelancer" <?php echo ($mother_occupation == 'Freelancer') ? 'selected' : ''; ?>>
                            Freelancer</option>
                        <option value="NGO/Non-Profit" <?php echo ($mother_occupation == 'NGO/Non-Profit') ? 'selected' : ''; ?>>NGO/Non-Profit</option>
                        <option value="Overseas Employment" <?php echo ($mother_occupation == 'Overseas Employment') ? 'selected' : ''; ?>>Overseas Employment</option>
                        <option value="Casual" <?php echo ($mother_occupation == 'Casual') ? 'selected' : ''; ?>>Casual
                        </option>
                        <option value="Contractual" <?php echo ($mother_occupation == 'Contractual') ? 'selected' : ''; ?>>Contractual</option>
                        <option value="Intern" <?php echo ($mother_occupation == 'Intern') ? 'selected' : ''; ?>>Intern
                        </option>
                    </select>
                    <div class="error-message">Please select mother's occupation</div>
                </div>

                <div class="form-row">
                    <label class="required-field">Monthly Income</label>
                    <select name="mother_income" id="mother_income" required
                        onchange="toggleCustomIncome(this, 'mother')">
                        <option value="" disabled selected>Select Monthly Income Range</option>
                        <option value="0-5000" <?php echo ($mother_income == '0-5000') ? 'selected' : ''; ?>>₱0 - ₱5,000
                        </option>
                        <option value="5000-10000" <?php echo ($mother_income == '5000-10000') ? 'selected' : ''; ?>>
                            ₱5,000 - ₱10,000</option>
                        <option value="10000-15000" <?php echo ($mother_income == '10000-15000') ? 'selected' : ''; ?>>
                            ₱10,000 - ₱15,000</option>
                        <option value="15000-20000" <?php echo ($mother_income == '15000-20000') ? 'selected' : ''; ?>>
                            ₱15,000 - ₱20,000</option>
                        <option value="20000-25000" <?php echo ($mother_income == '20000-25000') ? 'selected' : ''; ?>>
                            ₱20,000 - ₱25,000</option>
                        <option value="25000-30000" <?php echo ($mother_income == '25000-30000') ? 'selected' : ''; ?>>
                            ₱25,000 - ₱30,000</option>
                        <option value="30000-35000" <?php echo ($mother_income == '30000-35000') ? 'selected' : ''; ?>>
                            ₱30,000 - ₱35,000</option>
                        <option value="35000-40000" <?php echo ($mother_income == '35000-40000') ? 'selected' : ''; ?>>
                            ₱35,000 - ₱40,000</option>
                        <option value="40000-45000" <?php echo ($mother_income == '40000-45000') ? 'selected' : ''; ?>>
                            ₱40,000 - ₱45,000</option>
                        <option value="45000-50000" <?php echo ($mother_income == '45000-50000') ? 'selected' : ''; ?>>
                            ₱45,000 - ₱50,000</option>
                        <option value="50000+" <?php echo ($mother_income == '50000+') ? 'selected' : ''; ?>>₱50,000+
                        </option>
                        <option value="Not Applicable" <?php echo ($mother_income == 'Not Applicable') ? 'selected' : ''; ?>>Not Applicable (No Income)</option>
                        <option value="Prefer not to say" <?php echo ($mother_income == 'Prefer not to say') ? 'selected' : ''; ?>>Prefer not to say</option>
                        <option value="custom" <?php echo ($mother_income == 'custom') ? 'selected' : ''; ?>>Custom Amount
                        </option>
                    </select>
                    <div class="specify-field" id="mother_custom_income_field">
                        <label for="mother_income_custom">Enter Exact Monthly Income:</label>
                        <input type="number" name="mother_income_custom" id="mother_income_custom"
                            value="<?php echo htmlspecialchars($mother_income_custom); ?>" min="0" step="0.01"
                            placeholder="0.00">
                        <small>Enter the exact monthly income in pesos (e.g., 32500.50)</small>
                    </div>
                    <div class="error-message">Please select monthly income range</div>
                </div>

                <div class="button-row">
                    <button type="button" class="buttonNav" onClick="prevStep()">Previous</button>
                    <button type="button" class="buttonNav" onClick="nextStep()">Next</button>
                </div>
            </section>

            <!-- Section 5: House Status -->
            <section id="house-status" class="form-section">
                <h2>House Status</h2>
                <div class="section-error" id="house-status-error"></div>

                <div class="house-status-group">
                    <label class="house-option">
                        <input type="radio" name="house_status" value="owned" <?php echo ($house_status == 'owned') ? 'checked' : ''; ?> required>
                        <span class="option-label">House Owned</span>
                    </label>

                    <label class="house-option">
                        <input type="radio" name="house_status" value="rented" <?php echo ($house_status == 'rented') ? 'checked' : ''; ?>>
                        <span class="option-label">Rented</span>
                    </label>

                    <label class="house-option">
                        <input type="radio" name="house_status" value="living with relatives" <?php echo ($house_status == 'living with relatives') ? 'checked' : ''; ?>>
                        <span class="option-label">Living with Relatives</span>
                    </label>
                </div>
                <div class="error-message" style="text-align: center; margin-top: 10px;">Please select house status
                </div>

                <div class="button-row">
                    <button type="button" class="buttonNav" onClick="prevStep()">Previous</button>
                    <button type="submit" class="submitBtn">Complete Registration</button>
                </div>
            </section>
        </form>

        <div class="login-link">
            Already have an account? <a href="./login.php">Login here</a>
        </div>
    </div>

    <script>
        // Global variables
        let currentStep = 1;
        const totalSteps = 5;
        const sections = [
            'account-info',
            'personal-info',
            'schools-attended',
            'parents-data',
            'house-status'
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

        // Function to toggle custom income fields
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
                    // Don't clear the value in case user switches back
                }
            }
        }

        // Initialize fields on page load
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

        function copyAddress() {
            // Get present address by name attribute
            const presentAddressInput = document.querySelector('input[name="pres_address"]');
            const presentAddress = presentAddressInput ? presentAddressInput.value : '';

            // Get permanent address field by ID
            const permanentAddressField = document.getElementById('perma_address');
            const sameAddressCheckbox = document.getElementById('same_address');

            if (!presentAddressInput || !permanentAddressField || !sameAddressCheckbox) {
                console.error('Required elements not found');
                return;
            }

            if (sameAddressCheckbox.checked) {
                // Copy the present address to permanent address
                permanentAddressField.value = presentAddress;
                permanentAddressField.readOnly = true;
                permanentAddressField.style.backgroundColor = '#f5f5f5';
                permanentAddressField.style.cursor = 'not-allowed';
            } else {
                // If unchecking, only clear if the value matches the present address
                // (to avoid clearing user-modified permanent address)
                if (permanentAddressField.value === presentAddress) {
                    permanentAddressField.value = '';
                }
                permanentAddressField.readOnly = false;
                permanentAddressField.style.backgroundColor = '';
                permanentAddressField.style.cursor = '';
            }
        }

        // Also update the present address field to trigger copy when changed while checkbox is checked
        document.addEventListener('DOMContentLoaded', function () {
            const presentAddressInput = document.querySelector('input[name="pres_address"]');
            if (presentAddressInput) {
                presentAddressInput.addEventListener('input', function () {
                    const sameAddressCheckbox = document.getElementById('same_address');
                    if (sameAddressCheckbox && sameAddressCheckbox.checked) {
                        const permanentAddressField = document.getElementById('perma_address');
                        if (permanentAddressField) {
                            permanentAddressField.value = this.value;
                        }
                    }
                });
            }
        });

        function calculateAge() {
            const dobInput = document.getElementById('date_of_birth');
            const ageInput = document.getElementById('age');
            const errorMessage = dobInput.closest('.form-row').querySelector('.error-message');
            const parentRow = dobInput.closest('.form-row');

            // Clear previous errors
            parentRow.classList.remove('error');
            errorMessage.style.display = 'none';

            if (dobInput.value) {
                const dobDate = new Date(dobInput.value);
                const today = new Date();

                // Validate date is not in the future
                if (dobDate > today) {
                    parentRow.classList.add('error');
                    errorMessage.style.display = 'block';
                    errorMessage.textContent = 'Date of birth cannot be in the future';
                    ageInput.value = '';
                    return false;
                }

                // Calculate age
                let age = today.getFullYear() - dobDate.getFullYear();
                const monthDiff = today.getMonth() - dobDate.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dobDate.getDate())) {
                    age--;
                }

                ageInput.value = age;
                return true;
            } else {
                ageInput.value = '';
                return false;
            }
        }

        // Validate current section
        function validateCurrentSection() {
            const currentSectionId = sections[currentStep - 1];
            const currentSection = document.getElementById(currentSectionId);
            const errorDiv = document.getElementById(currentSectionId + '-error');
            const inputs = currentSection.querySelectorAll('input[required], select[required]');
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

            // SPECIAL VALIDATION FOR ACCOUNT INFO SECTION (STEP 1)
            if (currentSectionId === 'account-info') {
                // Get the form inputs
                const usernameInput = document.querySelector('input[name="username"]');
                const passwordInput = document.querySelector('input[name="password"]');
                const confirmPasswordInput = document.querySelector('input[name="confirm_password"]');

                // Validate username (basic validation first)
                if (!usernameInput.value.trim()) {
                    const parentRow = usernameInput.closest('.form-row');
                    parentRow.classList.add('error');
                    const errorMessage = parentRow.querySelector('.error-message');
                    if (errorMessage) errorMessage.style.display = 'block';
                    errorMessage.textContent = 'Please enter a username';
                    isValid = false;
                    errorMessages.push('Username is required');
                } else if (!/^[a-zA-Z0-9_]+$/.test(usernameInput.value.trim())) {
                    const parentRow = usernameInput.closest('.form-row');
                    parentRow.classList.add('error');
                    const errorMessage = parentRow.querySelector('.error-message');
                    if (errorMessage) errorMessage.style.display = 'block';
                    errorMessage.textContent = 'Username can only contain letters, numbers, and underscores';
                    isValid = false;
                    errorMessages.push('Username can only contain letters, numbers, and underscores');
                }

                // Validate password
                if (!passwordInput.value.trim()) {
                    const parentRow = passwordInput.closest('.form-row');
                    parentRow.classList.add('error');
                    const errorMessage = parentRow.querySelector('.error-message');
                    if (errorMessage) errorMessage.style.display = 'block';
                    errorMessage.textContent = 'Please enter a password';
                    isValid = false;
                    errorMessages.push('Password is required');
                } else if (passwordInput.value.trim().length < 6) {
                    const parentRow = passwordInput.closest('.form-row');
                    parentRow.classList.add('error');
                    const errorMessage = parentRow.querySelector('.error-message');
                    if (errorMessage) errorMessage.style.display = 'block';
                    errorMessage.textContent = 'Password must have at least 6 characters';
                    isValid = false;
                    errorMessages.push('Password must have at least 6 characters');
                }

                // Validate confirm password
                if (!confirmPasswordInput.value.trim()) {
                    const parentRow = confirmPasswordInput.closest('.form-row');
                    parentRow.classList.add('error');
                    const errorMessage = parentRow.querySelector('.error-message');
                    if (errorMessage) errorMessage.style.display = 'block';
                    errorMessage.textContent = 'Please confirm password';
                    isValid = false;
                    errorMessages.push('Please confirm password');
                } else if (passwordInput.value.trim() && confirmPasswordInput.value.trim() !== passwordInput.value.trim()) {
                    const parentRow = confirmPasswordInput.closest('.form-row');
                    parentRow.classList.add('error');
                    const errorMessage = parentRow.querySelector('.error-message');
                    if (errorMessage) errorMessage.style.display = 'block';
                    errorMessage.textContent = 'Password did not match';
                    isValid = false;
                    errorMessages.push('Password did not match');
                }

                // If basic validation passed, check if username is taken via AJAX
                if (isValid && usernameInput.value.trim()) {
                    // Show loading state
                    const usernameRow = usernameInput.closest('.form-row');
                    const usernameError = usernameRow.querySelector('.error-message');

                    // Disable next button while checking
                    const nextButton = currentSection.querySelector('.buttonNav');
                    const originalText = nextButton.textContent;
                    nextButton.textContent = 'Checking username...';
                    nextButton.disabled = true;

                    // Send AJAX request to check username
                    const formData = new FormData();
                    formData.append('username', usernameInput.value.trim());

                    fetch('../includes/check_username.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            // Re-enable button
                            nextButton.textContent = originalText;
                            nextButton.disabled = false;

                            if (data.exists) {
                                usernameRow.classList.add('error');
                                if (usernameError) {
                                    usernameError.style.display = 'block';
                                    usernameError.textContent = 'This username is already taken.';
                                }
                                isValid = false;
                                errorMessages.push('This username is already taken');

                                // Show section error message
                                if (errorDiv && errorMessages.length > 0) {
                                    errorDiv.innerHTML = '<strong>Please fill up the following required fields:</strong><ul>' +
                                        errorMessages.map(error => `<li>${error}</li>`).join('') + '</ul>';
                                    errorDiv.style.display = 'block';
                                    errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                }
                            } else {
                                // Username is available, proceed with navigation
                                proceedToNextStep();
                            }
                        })
                        .catch(error => {
                            console.error('Error checking username:', error);
                            // Re-enable button
                            nextButton.textContent = originalText;
                            nextButton.disabled = false;

                            // Show error message
                            usernameRow.classList.add('error');
                            if (usernameError) {
                                usernameError.style.display = 'block';
                                usernameError.textContent = 'Error checking username availability. Please try again.';
                            }
                            isValid = false;
                            errorMessages.push('Error checking username availability');

                            if (errorDiv && errorMessages.length > 0) {
                                errorDiv.innerHTML = '<strong>Please fill up the following required fields:</strong><ul>' +
                                    errorMessages.map(error => `<li>${error}</li>`).join('') + '</ul>';
                                errorDiv.style.display = 'block';
                                errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                        });

                    // Return false to prevent immediate navigation (wait for AJAX response)
                    return false;
                }

                // If validation failed, show errors
                if (!isValid && errorDiv && errorMessages.length > 0) {
                    errorDiv.innerHTML = '<strong>Please fill up the following required fields:</strong><ul>' +
                        errorMessages.map(error => `<li>${error}</li>`).join('') + '</ul>';
                    errorDiv.style.display = 'block';
                    errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return false;
                }

                // If all basic validation passed and no AJAX check needed, proceed
                if (isValid) {
                    proceedToNextStep();
                    return false; // Navigation handled by proceedToNextStep
                }

                return false;
            }

            // Original validation for other sections
            inputs.forEach(input => {
                // Skip validation for "other" fields if not displayed
                if (input.name.includes('_other') || input.name.includes('_custom')) {
                    const otherField = input.closest('.specify-field');
                    if (otherField && otherField.style.display === 'none') {
                        return; // Skip validation for hidden "other" fields
                    }
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
                        if (parseFloat(input.value) < 0) {
                            parentRow.classList.add('error');
                            if (errorMessage) errorMessage.style.display = 'block';
                            isValid = false;
                            const label = input.labels[0]?.textContent?.replace('*', '').trim() || input.name;
                            errorMessages.push(`${label} cannot be negative`);
                            return;
                        }

                        // Check graduation years (skip for college year grad as it's optional)
                        if (input.name.includes('yr_grad') && !input.name.includes('college')) {
                            const year = parseInt(input.value);
                            const currentYear = new Date().getFullYear();
                            if (year < 1900 || year > currentYear) {
                                parentRow.classList.add('error');
                                if (errorMessage) errorMessage.style.display = 'block';
                                isValid = false;
                                const label = input.labels[0]?.textContent?.replace('*', '').trim() || input.name;
                                errorMessages.push(`${label} must be a valid year between 1900 and ${currentYear}`);
                                return;
                            }
                        }

                        // Validate custom income amount
                        if (input.name.includes('income_custom')) {
                            const amount = parseFloat(input.value);
                            if (amount < 0) {
                                parentRow.classList.add('error');
                                if (errorMessage) {
                                    errorMessage.style.display = 'block';
                                    errorMessage.textContent = 'Income amount cannot be negative';
                                }
                                isValid = false;
                                errorMessages.push('Income amount cannot be negative');
                                return;
                            }
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
                            errorMessages.push(`Please enter a valid 11-digit cellphone number`);
                            return;
                        }
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

                        // Check if date is in the future
                        if (selectedDate > today) {
                            parentRow.classList.add('error');
                            if (errorMessage) errorMessage.style.display = 'block';
                            errorMessage.textContent = 'Date cannot be in the future';
                            isValid = false;
                            errorMessages.push('Date of birth cannot be in the future');
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

            // If validation passed and we're in step 2, proceed to next step
            if (currentSectionId !== 'account-info' && isValid) {
                proceedToNextStep();
                return false; // Navigation handled by proceedToNextStep
            }

            return true;
        }

        // Helper function for navigation
        function proceedToNextStep() {
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
            }
        }

        // Navigate to next step
        function nextStep() {
            validateCurrentSection();
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

        // Validate entire form before submission
        function validateForm() {
            let allErrors = [];

            // Validate all sections
            for (let i = 0; i < sections.length; i++) {
                const section = document.getElementById(sections[i]);
                const inputs = section.querySelectorAll('input[required], select[required]');

                inputs.forEach(input => {
                    // Skip validation for "other" fields if not displayed
                    if ((input.name.includes('_other') || input.name.includes('_custom')) &&
                        input.closest('.specify-field')?.style.display === 'none') {
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

                        // Validate custom income fields
                        if (input.name.includes('income_custom') && input.closest('.specify-field')?.style.display === 'block') {
                            const amount = parseFloat(input.value);
                            if (isNaN(amount) || amount < 0) {
                                parentRow.classList.add('error');
                                if (errorMessage) {
                                    errorMessage.style.display = 'block';
                                    errorMessage.textContent = 'Please enter a valid positive number for income';
                                }
                                allErrors.push('Please enter a valid income amount');
                            }
                        }
                    }
                });

                // Special handling for house status radio buttons
                if (sections[i] === 'house-status') {
                    const radioSelected = section.querySelector('input[name="house_status"]:checked');
                    if (!radioSelected) {
                        const houseStatusError = section.querySelector('.error-message');
                        if (houseStatusError) houseStatusError.style.display = 'block';
                        allErrors.push('Please select house status');
                    }
                }
            }

            if (allErrors.length > 0) {
                // Show validation summary
                const validationSummary = document.getElementById('validationSummary');
                const errorList = document.getElementById('errorList');

                if (validationSummary && errorList) {
                    errorList.innerHTML = '';
                    allErrors.forEach(error => {
                        const li = document.createElement('li');
                        li.textContent = error;
                        errorList.appendChild(li);
                    });
                    validationSummary.style.display = 'block';

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

                    // Scroll to top
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }

                return false;
            }

            return true;
        }
    </script>
</body>

</html>