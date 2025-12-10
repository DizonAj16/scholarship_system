<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize the session
session_start();

// Check if the user is already logged in; if so, redirect to the appropriate page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    if ($_SESSION["role"] === "admin") {
        header("location: ../views/dashboard.php");
    } else {
        header("location: ../views/home.php");
    }
    exit;
}

// Include config file
require_once "../includes/config.php";

// Define variables and initialize with empty values
$username = $password = "";
$alert_type = ""; // success, error, warning
$alert_message = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    try {
        // Check if username is empty
        if (empty(trim($_POST["username"]))) {
            throw new Exception("Please enter your username");
        } else {
            $username = trim($_POST["username"]);
        }

        // Check if password is empty
        if (empty(trim($_POST["password"]))) {
            throw new Exception("Please enter your password");
        } else {
            $password = trim($_POST["password"]);
        }

        // Prepare a select statement
        $sql = "SELECT id, username, password, role FROM users WHERE username = :username";

        if ($stmt = $pdo->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $username, PDO::PARAM_STR);

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Check if username exists; if yes, verify password
                if ($stmt->rowCount() == 1) {
                    // Fetch result
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $id = $row["id"];
                    $username = $row["username"];
                    $hashed_password = $row["password"];
                    $role = $row["role"];

                    if (password_verify($password, $hashed_password)) {
                        // Password is correct; start a new session
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["username"] = $username;
                        $_SESSION["role"] = $role;
                        $_SESSION["login_time"] = time();

                        // Log the successful login
                        $action = "User logged in";
                        $details = "User '$username' logged in successfully.";
                        logActivity($pdo, $id, $action, $details);

                        // Redirect user based on role
                        if ($role === "admin") {
                            header("location: ../views/dashboard.php");
                        } else {
                            header("location: ../views/home.php");
                        }
                        exit;
                    } else {
                        // Log the failed login attempt
                        $action = "Failed login attempt";
                        $details = "Invalid password for user '$username'.";
                        logActivity($pdo, $id, $action, $details);
                        
                        throw new Exception("Invalid username or password");
                    }
                } else {
                    // Log the failed login attempt
                    $action = "Failed login attempt";
                    $details = "Username '$username' not found.";
                    logActivity($pdo, null, $action, $details);
                    
                    throw new Exception("Invalid username or password");
                }
            } else {
                throw new Exception("Unable to connect to the system. Please try again");
            }
        } else {
            throw new Exception("System error. Please try again");
        }
    } catch (Exception $e) {
        $alert_type = "error";
        $alert_message = $e->getMessage();
        
        // Store in session for page refresh
        $_SESSION['alert_type'] = $alert_type;
        $_SESSION['alert_message'] = $alert_message;
        
        // Redirect to clear POST data
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Check for alert messages in session (after redirect)
if (isset($_SESSION['alert_type']) && isset($_SESSION['alert_message'])) {
    $alert_type = $_SESSION['alert_type'];
    $alert_message = $_SESSION['alert_message'];
    
    // Clear session variables
    unset($_SESSION['alert_type']);
    unset($_SESSION['alert_message']);
}

// Check for success message from other pages
if (isset($_GET['reset']) && $_GET['reset'] == 'success') {
    $alert_type = 'success';
    $alert_message = 'Password reset successful! You can now login with your new password.';
}

if (isset($_GET['registered']) && $_GET['registered'] == 'success') {
    $alert_type = 'success';
    $alert_message = 'Registration successful! You can now login with your credentials.';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/login-signup.css?<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="../js/fontawesome.js" crossorigin="anonymous"></script>
    <title>Scholarship System - Login</title>
    <style>
        /* Custom Alert Styles */
        .custom-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            min-width: 300px;
            max-width: 400px;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 15px;
            z-index: 10000;
            animation: slideIn 0.3s ease, fadeOut 0.3s ease 4.7s forwards;
            transform: translateX(0);
            opacity: 1;
        }

        .alert-error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }

        .alert-success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }

        .alert-warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            color: #856404;
        }

        .alert-icon {
            font-size: 24px;
            flex-shrink: 0;
        }

        .alert-content {
            flex-grow: 1;
            font-size: 14px;
        }

        .alert-close {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            color: inherit;
            opacity: 0.7;
            transition: opacity 0.3s;
            padding: 0;
            margin-left: 10px;
        }

        .alert-close:hover {
            opacity: 1;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                transform: translateX(100%);
            }
        }

        /* Input error styling */
        .input-error {
            border-color: #dc3545 !important;
            animation: shake 0.3s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        /* Loading button */
        .btn-loading {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .btn-loading i {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Password toggle button */
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
            z-index: 2;
        }

        .password-toggle:hover {
            color: #333;
        }
    </style>
</head>

<body>
    <!-- Custom Alert Container -->
    <div id="alertContainer"></div>

    <div class="box">
        <span class="borderLine"></span>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="loginForm">
            <img src="../assets/images/icons/scholarship_seal.png" alt="Scholarship System Logo" class="login-icon">

            <div class="inputBox">
                <input type="text" name="username" id="username" 
                    value="<?php echo htmlspecialchars($username); ?>" 
                    required>
                <span>Username</span>
                <i></i>
            </div>
            
            <div class="inputBox" style="position: relative;">
                <input type="password" name="password" id="password" required>
                <span>Password</span>
                <i></i>
                <button type="button" class="password-toggle" id="togglePassword">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            
            <div class="links">
                <a href="./reset-password.php">Forgot Password?</a>
                <a href="./register.php">Create an Account</a>
            </div>
            
            <button type="submit" class="btn btn-submit" id="submitBtn">
                <span>Login</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Alert system functions
            function showAlert(type, message) {
                const alertContainer = document.getElementById('alertContainer');
                
                // Create alert element
                const alert = document.createElement('div');
                alert.className = `custom-alert alert-${type}`;
                
                // Set icon based on type
                let icon = 'fa-info-circle';
                if (type === 'error') icon = 'fa-exclamation-circle';
                if (type === 'success') icon = 'fa-check-circle';
                if (type === 'warning') icon = 'fa-exclamation-triangle';
                
                alert.innerHTML = `
                    <i class="fas ${icon} alert-icon"></i>
                    <div class="alert-content">${message}</div>
                    <button class="alert-close" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                
                // Add to container
                alertContainer.appendChild(alert);
                
                // Auto-remove after 5 seconds
                setTimeout(() => {
                    if (alert.parentElement) {
                        alert.remove();
                    }
                }, 5000);
            }
            
            // Show PHP alerts if any
            <?php if (!empty($alert_message)): ?>
                showAlert('<?php echo $alert_type; ?>', '<?php echo addslashes($alert_message); ?>');
            <?php endif; ?>
            
            // Form elements
            const form = document.getElementById('loginForm');
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            const submitBtn = document.getElementById('submitBtn');
            const togglePasswordBtn = document.getElementById('togglePassword');
            
            // Toggle password visibility
            togglePasswordBtn.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.innerHTML = type === 'password' 
                    ? '<i class="fas fa-eye"></i>' 
                    : '<i class="fas fa-eye-slash"></i>';
            });
            
            // Form validation
            form.addEventListener('submit', function(event) {
                let isValid = true;
                let errorMessage = '';
                
                // Remove previous error styles
                usernameInput.classList.remove('input-error');
                passwordInput.classList.remove('input-error');
                
                // Validate username
                const username = usernameInput.value.trim();
                if (!username) {
                    errorMessage = 'Please enter your username';
                    usernameInput.classList.add('input-error');
                    isValid = false;
                }
                
                // Validate password (only if username is valid)
                if (isValid) {
                    const password = passwordInput.value.trim();
                    if (!password) {
                        errorMessage = 'Please enter your password';
                        passwordInput.classList.add('input-error');
                        isValid = false;
                    }
                }
                
                if (!isValid) {
                    event.preventDefault();
                    showAlert('error', errorMessage);
                    
                    // Focus on first error field
                    if (usernameInput.classList.contains('input-error')) {
                        usernameInput.focus();
                    } else {
                        passwordInput.focus();
                    }
                } else {
                    // Show loading state
                    submitBtn.classList.add('btn-loading');
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span>Logging in...</span><i class="fas fa-spinner"></i>';
                    
                    // Re-enable button after 5 seconds in case submission fails
                    setTimeout(() => {
                        submitBtn.classList.remove('btn-loading');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<span>Login</span><i class="fas fa-arrow-right"></i>';
                    }, 5000);
                }
            });
            
            // Clear error styles on input
            usernameInput.addEventListener('input', function() {
                this.classList.remove('input-error');
            });
            
            passwordInput.addEventListener('input', function() {
                this.classList.remove('input-error');
            });
            
            // Focus on username field on page load
            usernameInput.focus();
        });
    </script>
</body>

</html> 