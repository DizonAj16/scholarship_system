<?php
include '../includes/session.php';

// Check if user has necessary privileges
if ($_SESSION['role'] !== 'admin') {
    die("Unauthorized access.");
}

// Define the filename for the CSV file
$filename = 'scholarship_applications_export.csv';

// Set headers to download the file
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Open the output stream
$output = fopen('php://output', 'w');

// Define the column headers
$headers = [
    'Application ID',
    'User ID',
    'Date',
    'Semester',
    'School Year',
    'Full Name',
    'Course',
    'Year & Section',
    'Major',
    'Cell Number',
    'Permanent Address',
    'Zip Code',
    'Present Address',
    'Email',
    'Sex',
    'Date of Birth',
    'Age',
    'Place of Birth',
    'Civil Status',
    'Religion',
    'Scholarship Grant',
    'Disability',
    'Indigenous Group',
    'Reason for Scholarship',
    'Elementary School',
    'Elementary Year Graduated',
    'Elementary Honors',
    'Secondary School',
    'Secondary Year Graduated',
    'Secondary Honors',
    'College',
    'College Year Graduated',
    'College Honors',
    'Father Last Name',
    'Father Given Name',
    'Father Middle Name',
    'Father Cellphone',
    'Father Education',
    'Father Occupation',
    'Father Income',
    'Mother Last Name',
    'Mother Given Name',
    'Mother Middle Name',
    'Mother Cellphone',
    'Mother Education',
    'Mother Occupation',
    'Mother Income',
    'House Status',
    'Application Status',
];

// Write the column headers to the CSV
fputcsv($output, $headers);

// Prepare the SQL query to fetch data from the scholarship tables
$query = "
    SELECT 
        CAST(sa.application_id AS CHAR) AS application_id, 
        sa.user_id, sa.date, sa.semester, sa.school_year, sa.full_name, 
        sa.course, sa.yr_sec, sa.major, sa.cell_no, sa.permanent_address, sa.zip_code, 
        sa.present_address, sa.email, sa.sex, sa.date_of_birth, sa.age, sa.place_of_birth, 
        sa.civil_status, sa.religion, sa.scholarship_grant, sa.disability, sa.indigenous_group, 
        sa.reason_scholarship, 
        sa2.elementary, sa2.elementary_year_grad, sa2.elementary_honors, 
        sa2.secondary, sa2.secondary_year_grad, sa2.secondary_honors, 
        sa2.college, sa2.college_year_grad, sa2.college_honors, 
        pi.father_lastname, pi.father_givenname, pi.father_middlename, 
        pi.father_cellphone, pi.father_education, pi.father_occupation, pi.father_income, 
        pi.mother_lastname, pi.mother_givenname, pi.mother_middlename, pi.mother_cellphone, 
        pi.mother_education, pi.mother_occupation, pi.mother_income, hi.house_status, sa.status
    FROM 
        scholarship_applications sa
    JOIN schools_attended sa2 ON sa.application_id = sa2.application_id
    JOIN parents_info pi ON sa.application_id = pi.application_id
    JOIN house_info hi ON sa.application_id = hi.application_id
";

$stmt = $pdo->prepare($query);
$stmt->execute();

// Fetch all results and write them to the CSV
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Force the application_id to be treated as a string in the CSV
    $row['application_id'] = "'" . (string) $row['application_id']; // Adding a single quote in front of the ID
    
    // Replace NULL values with empty strings for CSV export
    foreach ($row as &$value) {
        if (is_null($value)) {
            $value = ''; // Replace NULL with empty string
        }
    }

    // Write the row to the CSV file
    fputcsv($output, $row);
}

// Close the output stream
fclose($output);
exit;
?>
