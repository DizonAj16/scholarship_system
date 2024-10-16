<?php
// Initialize the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../auth/login.php");
    exit;
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Gather form data
    $date = $_POST['date'];
    $sem = $_POST['sem'];
    $sy = $_POST['sy'];
    $fullName = $_POST['fullName'];
    $course = $_POST['course'];
    $yrSec = $_POST['yrSec'];
    $major = $_POST['major'];
    $phone = $_POST['phone'];
    $permanentAddress = $_POST['permanentAddress'];
    $zipCode = $_POST['zipCode'];
    $presentAddress = $_POST['presentAddress'];
    $email = $_POST['email'];
    $age = $_POST['age'];
    $dob = $_POST['dob'];
    $placeOfBirth = $_POST['placeOfBirth'];
    $sex = $_POST['sex'];
    $civilStatus = $_POST['civilStatus'];
    $religion = $_POST['religion'];
    $scholarshipGrant = $_POST['scholarshipGrant'];
    $disabilityType = $_POST['disabilityType'];
    $indigenousGroup = $_POST['indigenousGroup'];
    $elementarySchool = $_POST['elementarySchool'];
    $yearGraduateElementary = $_POST['yearGraduateElementary'];
    $honorsElementary = $_POST['honorsElementary'];
    $secondarySchool = $_POST['secondarySchool'];
    $yearGraduateSecondary = $_POST['yearGraduateSecondary'];
    $honorsSecondary = $_POST['honorsSecondary'];
    $collegeSchool = $_POST['collegeSchool'];
    $yearGraduateCollege = $_POST['yearGraduateCollege'];
    $honorsCollege = $_POST['honorsCollege'];
    $aboutYourself = $_POST['aboutYourself'];
    $needScholarship = $_POST['needScholarship'];
    $fatherLastName = $_POST['fatherLastName'];
    $fatherGivenName = $_POST['fatherGivenName'];
    $fatherMiddleName = $_POST['fatherMiddleName'];
    $fatherPhone = $_POST['fatherPhone'];
    $fatherEducation = $_POST['fatherEducation'];
    $fatherOccupation = $_POST['fatherOccupation'];
    $fatherIncome = $_POST['fatherIncome'];
    $motherMaidenLastName = $_POST['motherMaidenLastName'];
    $motherGivenName = $_POST['motherGivenName'];
    $motherMiddleName = $_POST['motherMiddleName'];
    $motherPhone = $_POST['motherPhone'];
    $motherEducation = $_POST['motherEducation'];
    $motherOccupation = $_POST['motherOccupation'];
    $motherIncome = $_POST['motherIncome'];
    $housingStatus = $_POST['housingStatus'];

    // Specify the filename
    $filename = 'applications.csv';
    $filePath = __DIR__ . '/csv/' . $filename; // Update this path as needed

    // Open the file for writing
    $file = fopen($filePath, 'a'); // Use 'a' to append to the file or 'w' to overwrite

    // Check if the file is opened successfully
    if ($file) {
        // Check if the file is empty (first time writing headers)
        if (filesize($filePath) == 0) {
            // Write the header row
            fputcsv($file, [
                'Date',
                'Semester',
                'School Year',
                'Full Name',
                'Course',
                'Yr./Sec',
                'Major',
                'Phone',
                'Permanent Address',
                'ZIP Code',
                'Present Address',
                'Email',
                'Age',
                'Date of Birth',
                'Place of Birth',
                'Sex',
                'Civil Status',
                'Religion',
                'Scholarship Grant',
                'Type of Disability',
                'Indigenous Group',
                'Elementary School',
                'Year Graduate Elementary',
                'Honors Received Elementary',
                'Secondary School',
                'Year Graduate Secondary',
                'Honors Received Secondary',
                'College School',
                'Year Graduate College',
                'Honors Received College',
                'About Yourself',
                'Need Scholarship',
                'Father Last Name',
                'Father Given Name',
                'Father Middle Name',
                'Father Phone',
                'Father Education',
                'Father Occupation',
                'Father Income',
                'Mother Maiden Last Name',
                'Mother Given Name',
                'Mother Middle Name',
                'Mother Phone',
                'Mother Education',
                'Mother Occupation',
                'Mother Income',
                'Housing Status'
            ]);
        }

        // Write the data row
        fputcsv($file, [
            $date,
            $sem,
            $sy,
            $fullName,
            $course,
            $yrSec,
            $major,
            $phone,
            $permanentAddress,
            $zipCode,
            $presentAddress,
            $email,
            $age,
            $dob,
            $placeOfBirth,
            $sex,
            $civilStatus,
            $religion,
            $scholarshipGrant,
            $disabilityType,
            $indigenousGroup,
            $elementarySchool,
            $yearGraduateElementary,
            $honorsElementary,
            $secondarySchool,
            $yearGraduateSecondary,
            $honorsSecondary,
            $collegeSchool,
            $yearGraduateCollege,
            $honorsCollege,
            $aboutYourself,
            $needScholarship,
            $fatherLastName,
            $fatherGivenName,
            $fatherMiddleName,
            $fatherPhone,
            $fatherEducation,
            $fatherOccupation,
            $fatherIncome,
            $motherMaidenLastName,
            $motherGivenName,
            $motherMiddleName,
            $motherPhone,
            $motherEducation,
            $motherOccupation,
            $motherIncome,
            $housingStatus
        ]);

        // Close the file
        fclose($file);

        // Success alert and redirect
        echo "<script>
                alert('Your application has been submitted successfully!');
                window.location.href = './views/scholarship_form.php'; // Update this to the correct form URL
              </script>";
    } else {
        // Handle error opening file
        echo "<script>
                alert('Error: Could not open the file. Please try again.');
                window.location.href = './views/scholarship_form.php'; // Update this to the correct form URL
              </script>";
    }
}
?>
