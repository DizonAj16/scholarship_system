<?php
include '../includes/session.php';

// Check if user has necessary privileges
if ($_SESSION['role'] !== 'admin') {
    die("Unauthorized access.");
}

try {
    // Get all filter parameters from the URL
    $params = [];
    $whereConditions = ["1=1"];
    
    // Search filter
    if (isset($_GET['search']) && $_GET['search'] !== '') {
        $whereConditions[] = "(sa.application_id LIKE :search OR sa.full_name LIKE :search)";
        $params[':search'] = '%' . $_GET['search'] . '%';
    }

    // Name filter
    if (isset($_GET['name_filter']) && $_GET['name_filter'] !== '') {
        $whereConditions[] = "sa.full_name = :name";
        $params[':name'] = $_GET['name_filter'];
    }

    // Course filter
    if (isset($_GET['course_filter']) && $_GET['course_filter'] !== '') {
        $whereConditions[] = "sa.course = :course";
        $params[':course'] = $_GET['course_filter'];
    }

    // Year & Section filter
    if (isset($_GET['yr_sec_filter']) && $_GET['yr_sec_filter'] !== '') {
        $whereConditions[] = "sa.yr_sec = :yr_sec";
        $params[':yr_sec'] = $_GET['yr_sec_filter'];
    }

    // Scholarship Grant filter
    if (isset($_GET['grant_filter']) && $_GET['grant_filter'] !== '') {
        $whereConditions[] = "sa.scholarship_grant = :grant";
        $params[':grant'] = $_GET['grant_filter'];
    }

    // Status filter
    if (isset($_GET['sort_status']) && in_array($_GET['sort_status'], ['approved', 'pending', 'not qualified'])) {
        $whereConditions[] = "sa.status = :status";
        $params[':status'] = $_GET['sort_status'];
    }

    // Semester & School Year filter
    if (isset($_GET['sem_sy_filter']) && $_GET['sem_sy_filter'] !== '') {
        $selectedSemSy = $_GET['sem_sy_filter'];
        $lastSpacePos = strrpos($selectedSemSy, ' ');
        if ($lastSpacePos !== false) {
            $semester = substr($selectedSemSy, 0, $lastSpacePos);
            $schoolYear = substr($selectedSemSy, $lastSpacePos + 1);
            $whereConditions[] = "sa.semester = :semester AND sa.school_year = :school_year";
            $params[':semester'] = $semester;
            $params[':school_year'] = $schoolYear;
        }
    }

    $whereClause = implode(' AND ', $whereConditions);

    // Sorting parameters
    $sortBy = isset($_GET['sort_by']) && in_array($_GET['sort_by'], ['application_id', 'full_name', 'course', 'yr_sec', 'date', 'status', 'sem_sy']) 
        ? $_GET['sort_by'] : 'application_id';
    
    $sortOrder = isset($_GET['sort']) && $_GET['sort'] == 'asc' ? 'ASC' : 'DESC';

    // Define the SQL query to fetch data from the scholarship tables with filters
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
        WHERE $whereClause
        ORDER BY $sortBy $sortOrder, sa.application_id $sortOrder
    ";

    $stmt = $pdo->prepare($query);
    
    // Bind all parameters
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    
    $stmt->execute();

    // Create CSV filename with filter info
    $filename = "scholarship_applications_export_" . date('Y-m-d_H-i-s');
    
    // Add filter info to filename if any filters are active
    if (!empty($_GET)) {
        $filterInfo = [];
        foreach ($_GET as $key => $value) {
            if (!in_array($key, ['page', 'sort', 'sort_by']) && $value !== '') {
                $filterInfo[] = $key . '_' . str_replace([' ', '/'], '_', substr($value, 0, 20));
            }
        }
        if (!empty($filterInfo)) {
            $filename .= "_filtered_" . implode('_', array_slice($filterInfo, 0, 3)); // Limit to 3 filters
        }
    }
    $filename .= ".csv";

    // Set headers to download the file
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    // Open the output stream
    $output = fopen('php://output', 'w');
    
    // Add UTF-8 BOM for Excel compatibility
    fwrite($output, "\xEF\xBB\xBF");
    
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
    
} catch (PDOException $e) {
    // Log error and redirect back
    error_log("Export CSV Error: " . $e->getMessage());
    
    // Start output buffering to prevent headers already sent error
    ob_start();
    
    $_SESSION['error_message'] = "Failed to export data: " . $e->getMessage();
    header("Location: ../views/applications.php");
    ob_end_flush();
    exit();
}
?>