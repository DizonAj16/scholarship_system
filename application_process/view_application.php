<?php
include '../includes/session.php';

// Validate application ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: applications.php");
    exit;
}

$application_id = (int) $_GET['id'];

try {
    // Fetch applicant information
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
        hi.house_status, sa.reason_scholarship, u.username
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
} catch (PDOException $e) {
    die("Error fetching application details: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Application</title>

    <link rel="stylesheet" href="../css/view_applications.css?v=<?php echo time(); ?>">
    <script src="../js/fontawesome.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/preloader.css?v=<?php echo time(); ?>">
    <script src="../js/preloader.js?v=<?php echo time(); ?>"></script>
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

        <h1>Applicant Information</h1>

        <div class="submitted-info">
            <p>This application was submitted by: <strong><?= htmlspecialchars($application['username']) ?></strong></p>
            <p><strong>Application ID:</strong> <?= htmlspecialchars($application['application_id']) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($application['status']) ?></p> <!-- Added Status -->
            <p><strong>Application Date:</strong> <?= htmlspecialchars($application['date']) ?></p>
            <!-- Added Application Date -->
        </div>

        <div class="section">
            <h2>Personal Information</h2>
            <div class="details">
                <p><i class="fas fa-user"></i><strong>Full Name:</strong>
                    <?= htmlspecialchars($application['full_name']) ?></p>
                <p><i class="fas fa-envelope"></i><strong>Email:</strong> <?= htmlspecialchars($application['email']) ?>
                </p>
                <p><i class="fas fa-phone"></i><strong>Phone:</strong> <?= htmlspecialchars($application['cell_no']) ?>
                </p>
                <p><i class="fas fa-venus-mars"></i><strong>Sex:</strong> <?= htmlspecialchars($application['sex']) ?>
                </p>
                <p><i class="fas fa-calendar-alt"></i><strong>Date of Birth:</strong>
                    <?= htmlspecialchars($application['date_of_birth']) ?> (Age:
                    <?= htmlspecialchars($application['age']) ?>)
                </p>
                <p><i class="fas fa-map-marker-alt"></i><strong>Address:</strong>
                    <?= htmlspecialchars($application['permanent_address']) ?></p>
            </div>
        </div>

        <div class="section">
            <h2>Educational Background</h2>
            <div class="details">
                <p><i class="fas fa-school"></i><strong>Elementary School:</strong>
                    <?= htmlspecialchars($application['elementary']) ?> (Year:
                    <?= htmlspecialchars($application['elementary_year_grad']) ?>, Honors:
                    <?= htmlspecialchars($application['elementary_honors']) ?>)
                </p>
                <p><i class="fas fa-school"></i><strong>Secondary School:</strong>
                    <?= htmlspecialchars($application['secondary']) ?> (Year:
                    <?= htmlspecialchars($application['secondary_year_grad']) ?>, Honors:
                    <?= htmlspecialchars($application['secondary_honors']) ?>)
                </p>
                <p><i class="fas fa-university"></i><strong>College:</strong>
                    <?= htmlspecialchars($application['college']) ?> (Year:
                    <?= htmlspecialchars($application['college_year_grad']) ?>, Honors:
                    <?= htmlspecialchars($application['college_honors']) ?>)
                </p>
            </div>
        </div>

        <div class="section">
            <h2>Family Information</h2>
            <div class="details">
                <p><i class="fas fa-male"></i><strong>Father:</strong>
                    <?= htmlspecialchars($application['father_givenname'] . ' ' . $application['father_lastname']) ?>
                    (Education: <?= htmlspecialchars($application['father_education']) ?>, Occupation:
                    <?= htmlspecialchars($application['father_occupation']) ?>, Income:
                    <?= htmlspecialchars($application['father_income']) ?>)
                </p>
                <p><i class="fas fa-female"></i><strong>Mother:</strong>
                    <?= htmlspecialchars($application['mother_givenname'] . ' ' . $application['mother_lastname']) ?>
                    (Education: <?= htmlspecialchars($application['mother_education']) ?>, Occupation:
                    <?= htmlspecialchars($application['mother_occupation']) ?>, Income:
                    <?= htmlspecialchars($application['mother_income']) ?>)
                </p>
            </div>
        </div>

        <div class="section">
            <h2>Housing Information</h2>
            <div class="details">
                <p><i class="fas fa-home"></i><strong>Housing Status:</strong>
                    <?= htmlspecialchars($application['house_status']) ?></p>
            </div>
        </div>

        <div class="section" style="overflow: auto;">
            <h2>Reason for Scholarship</h2>
            <div class="details">
                <p><i class="fas fa-question-circle"></i><strong>Reason:</strong>
                    <?= htmlspecialchars($application['reason_scholarship']) ?></p>
            </div>
        </div>

        <a href="javascript:history.back()" class="btn-back">
            <i class="fas fa-chevron-left"></i> Back
        </a>
    </div>
</body>

</html>