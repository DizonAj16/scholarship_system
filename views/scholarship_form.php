<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect them to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Application Form - Office of Scholarship Programs - ZPPSU</title>

    <!-- Font Awesome Kit -->
    <script src="../js/fontawesome.js" crossorigin="anonymous"></script>

    <!-- CSS file with cache-busting -->
    <link rel="stylesheet" href="../css/styles.css?v=<?php echo time(); ?>">
    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        h1,
        h2 {
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        select {
            background-color: #fff;
        }

        .multi-input {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .multi-input>div {
            flex: 1;
        }

        .multi-input>div label {
            margin-bottom: 5px;
        }

        .submit-button {
            display: block;
            width: 100%;
            padding: 10px;
            background: linear-gradient(to bottom right, #800000, #ffd700);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .submit-button:hover {
            background: linear-gradient(to bottom right, #a00000, #e5c200);
        }


        .form-container {
            margin-top: 15px;
            margin-bottom: 15px;
            width: 80%;
            max-width: 900px;
            padding: 20px;
            margin-left: 150px;
            color: #333;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
        }

        .inline-input {
            display: flex;
            justify-content: space-between;
            gap: 15px;
        }

        .inline-input input {
            width: calc(100% - 10px);
        }

        .inline-input .zip {
            width: 80%;
        }
    </style>
</head>

<body>
    <nav>
        <ul>
            <li>
                <a href="#" class="logo">
                    <img src="../assets/images/icons/scholarship_seal.png" alt="Scholarship Seal">
                    <span class="nav-item">OFFICE OF SCHOLARSHIP PROGRAMS</span>
                </a>
            </li>
            <li>
                <a href="../index.php">
                    <i class="fas fa-solid fa-house"></i>
                    <span class="nav-item-2">Home</span>
                </a>
            </li>
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
            <li>
                <a href="../auth/logout.php" class="logout">
                    <i class="fas fa-solid fa-right-from-bracket"></i>
                    <span class="nav-item-2">Logout</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="form-container">
        <h1>Scholarship Application Form</h1>
        <form action="../submit_application.php" method="POST">
            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" required>
            </div>
            <div class="form-group">
                <label for="sem">Semester:</label>
                <select id="sem" name="sem" required>
                    <option value="1st Sem">1st Sem</option>
                    <option value="2nd Sem">2nd Sem</option>
                    <option value="Summer">Summer</option>
                </select>
            </div>
            <div class="form-group">
                <label for="sy">School Year:</label>
                <input type="text" id="sy" name="sy" required>
            </div>

            <h2>APPLICANT'S DATA</h2>
            <div class="form-group">
                <label for="fullName">Full Name:</label>
                <input type="text" id="fullName" name="fullName" required>
            </div>
            <div class="form-group">
                <label for="course">Course:</label>
                <select id="course" name="course" required>
                    <option value="BS INFOTECH">BS INFOTECH</option>
                    <option value="BSCE">BSCE</option>
                    <option value="BSED">BSED</option>
                    <option value="BEED">BEED</option>
                    <option value="BPED">BPED</option>
                    <option value="BSMT">BSMT</option>
                    <option value="BSCpT">BSCpT</option>
                    <option value="BSMarE">BSMarE</option>
                    <option value="BSBA">BSBA</option>
                </select>
            </div>
            <div class="form-group multi-input">
                <div>
                    <label for="yrSec">Yr./Sec:</label>
                    <input type="text" id="yrSec" name="yrSec" required>
                </div>
                <div>
                    <label for="major">Major:</label>
                    <input type="text" id="major" name="major" required>
                </div>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" name="phone" required>
            </div>
            <div class="form-group inline-input">
                <div class="form-group" style="flex: 3;">
                    <label for="permanentAddress">Complete Permanent Address:</label>
                    <input type="text" id="permanentAddress" name="permanentAddress" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="zipCode">ZIP Code:</label>
                    <input type="text" id="zipCode" name="zipCode" required>
                </div>
            </div>


            <div class="form-group">
                <label for="presentAddress">Complete Present Address:</label>
                <input type="text" id="presentAddress" name="presentAddress" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group multi-input">
                <div>
                    <label for="age">Age:</label>
                    <input type="number" id="age" name="age" required>
                </div>
                <div>
                    <label for="dob">Date Of Birth:</label>
                    <input type="date" id="dob" name="dob" required>
                </div>
                <div>
                    <label for="placeOfBirth">Place of Birth:</label>
                    <input type="text" id="placeOfBirth" name="placeOfBirth" required>
                </div>
            </div>
            <div class="form-group multi-input">
                <div>
                    <label for="sex">Sex:</label>
                    <select id="sex" name="sex" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div>
                    <label for="civilStatus">Civil Status:</label>
                    <select id="civilStatus" name="civilStatus" required>
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                        <option value="Divorced">Divorced</option>
                        <option value="Widowed">Widowed</option>
                    </select>
                </div>
                <div>
                    <label for="religion">Religion:</label>
                    <select id="religion" name="religion" required>
                        <option value="Christianity">Christianity</option>
                        <option value="Islam">Islam</option>
                        <option value="Buddhism">Buddhism</option>
                        <option value="Hinduism">Hinduism</option>
                        <option value="Judaism">Judaism</option>
                        <option value="Others">Others</option>
                    </select>
                </div>
            </div>

            <div class="form-group multi-input">
                <div>
                    <label for="scholarshipGrant">Scholarship Grant:</label>
                    <input type="text" id="scholarshipGrant" name="scholarshipGrant" required>
                </div>
                <div>
                    <label for="disabilityType">Type of Disability (if any):</label>
                    <input type="text" id="disabilityType" name="disabilityType">
                </div>
                <div>
                    <label for="indigenousGroup">Indigenous People Group:</label>
                    <input type="text" id="indigenousGroup" name="indigenousGroup">
                </div>
            </div>

            <h2>Last Schools Attended</h2>
            <div class="form-group multi-input">
                <div>
                    <label for="elementarySchool">Elementary:</label>
                    <input type="text" id="elementarySchool" name="elementarySchool">
                </div>
                <div>
                    <label for="yearGraduateElementary">Year Graduate:</label>
                    <input type="text" id="yearGraduateElementary" name="yearGraduateElementary">
                </div>
                <div>
                    <label for="honorsElementary">Honors Received:</label>
                    <input type="text" id="honorsElementary" name="honorsElementary">
                </div>
            </div>
            <div class="form-group multi-input">
                <div>
                    <label for="secondarySchool">Secondary:</label>
                    <input type="text" id="secondarySchool" name="secondarySchool">
                </div>
                <div>
                    <label for="yearGraduateSecondary">Year Graduate:</label>
                    <input type="text" id="yearGraduateSecondary" name="yearGraduateSecondary">
                </div>
                <div>
                    <label for="honorsSecondary">Honors Received:</label>
                    <input type="text" id="honorsSecondary" name="honorsSecondary">
                </div>
            </div>
            <div class="form-group multi-input">
                <div>
                    <label for="collegeSchool">College:</label>
                    <input type="text" id="collegeSchool" name="collegeSchool">
                </div>
                <div>
                    <label for="yearGraduateCollege">Year Graduate:</label>
                    <input type="text" id="yearGraduateCollege" name="yearGraduateCollege">
                </div>
                <div>
                    <label for="honorsCollege">Honors Received:</label>
                    <input type="text" id="honorsCollege" name="honorsCollege">
                </div>
            </div>

            <div class="form-group">
                <label for="aboutYourself">Say something about yourself:</label>
                <textarea id="aboutYourself" name="aboutYourself" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="needScholarship">Why do you need a scholarship:</label>
                <textarea id="needScholarship" name="needScholarship" rows="4" required></textarea>
            </div>


            <div class="form-group">
                <h2>PARENTS/GUARDIAN'S DATA</h2>
            </div>

            <div class="form-group">
                <label for="fatherLastName">FATHER'S NAME</label>
                <div class="multi-input">
                    <div>
                        <label for="fatherLastName">Last Name:</label>
                        <input type="text" id="fatherLastName" name="fatherLastName" required>
                    </div>
                    <div>
                        <label for="fatherGivenName">Given Name:</label>
                        <input type="text" id="fatherGivenName" name="fatherGivenName" required>
                    </div>
                    <div>
                        <label for="fatherMiddleName">Middle Name:</label>
                        <input type="text" id="fatherMiddleName" name="fatherMiddleName" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="fatherPhone">Tel./Cell No.:</label>
                    <input type="tel" id="fatherPhone" name="fatherPhone" required>
                </div>
                <div class="form-group">
                    <label for="fatherEducation">Educational Attainment:</label>
                    <input type="text" id="fatherEducation" name="fatherEducation" required>
                </div>
                <div class="form-group">
                    <label for="fatherOccupation">Occupation:</label>
                    <input type="text" id="fatherOccupation" name="fatherOccupation" required>
                </div>
                <div class="form-group">
                    <label for="fatherIncome">Monthly Income:</label>
                    <input type="number" id="fatherIncome" name="fatherIncome" required>
                </div>
            </div>

            <div class="form-group">
                <label for="motherMaidenLastName">MOTHER'S MAIDEN NAME</label>
                <div class="multi-input">
                    <div>
                        <label for="motherMaidenLastName">Last Name:</label>
                        <input type="text" id="motherMaidenLastName" name="motherMaidenLastName" required>
                    </div>
                    <div>
                        <label for="motherGivenName">Given Name:</label>
                        <input type="text" id="motherGivenName" name="motherGivenName" required>
                    </div>
                    <div>
                        <label for="motherMiddleName">Middle Name:</label>
                        <input type="text" id="motherMiddleName" name="motherMiddleName" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="motherPhone">Tel./Cell No.:</label>
                    <input type="tel" id="motherPhone" name="motherPhone" required>
                </div>
                <div class="form-group">
                    <label for="motherEducation">Educational Attainment:</label>
                    <input type="text" id="motherEducation" name="motherEducation" required>
                </div>
                <div class="form-group">
                    <label for="motherOccupation">Occupation:</label>
                    <input type="text" id="motherOccupation" name="motherOccupation" required>
                </div>
                <div class="form-group">
                    <label for="motherIncome">Monthly Income:</label>
                    <input type="number" id="motherIncome" name="motherIncome" required>
                </div>
            </div>


            <div class="form-group">
                <label>Housing Status:</label>
                <div class="multi-input">
                    <div>
                        <input type="radio" id="houseOwned" name="housingStatus" value="House Owned" required>
                        <label for="houseOwned">House Owned</label>
                    </div>
                    <div>
                        <input type="radio" id="rented" name="housingStatus" value="Rented" required>
                        <label for="rented">Rented</label>
                    </div>
                    <div>
                        <input type="radio" id="livingWithRelatives" name="housingStatus" value="Living with Relatives" required>
                        <label for="livingWithRelatives">Living with Relatives</label>
                    </div>
                </div>
            </div>


            <div class="form-group">
                <button type="submit" class="submit-button">Submit Application</button>
            </div>
        </form>
    </div>
</body>

</html>