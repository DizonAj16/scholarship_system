<?php
// Include config file
require_once "../includes/config.php";

// Define variables and initialize with empty values
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";

        if ($stmt = $mysqli->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_username);

            // Set parameters
            $param_username = trim($_POST["username"]);

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // store result
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
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

    // Check input errors before inserting in database
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {

        // Prepare an insert statement with the default role 'student'
        $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";

        if ($stmt = $mysqli->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("sss", $param_username, $param_password, $param_role);

            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_role = 'student'; // Default role

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Show a JavaScript alert for successful registration
                echo "<script>
                        alert('Account successfully created');
                        window.location.href = './login.php';
                      </script>";
                exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Close connection
    $mysqli->close();
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="../css/login-signup.css?<?php echo time(); ?>">
</head>

<body>
    <div class="box2">
        <span class="borderLine2"></span>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <h2>Sign Up</h2>
            <p style="margin-top: 10px;">Please fill this form to create an account.</p>
            <!-- Error message box -->
            <div class="error-message" id="errorMessage">
                <?php
                if (!empty($login_err)) {
                    echo $login_err;
                }
                ?>
            </div>
            <div class="inputBox">
                <input type="text" name="username" <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>" required>
                <span>Username</span>
                <i></i>
                <span><?php echo $username_err; ?></span>
            </div>
            <div class="inputBox">
                <input type="password" name="password" <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>" required>
                <span>Password</span>
                <i></i>
                <span><?php echo $password_err; ?></span>
            </div>
            <div class="inputBox">
                <input type="password" name="confirm_password" <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>" required>
                <span>Confirm Password</span>
                <i></i>
                <span><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="links">
                <button type="submit" class="btn btn-submit">
                    <span>Submit</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
                <button type="reset" class="btn btn-submit">
                    <span>Reset</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
            <p style="font-size: 12px; margin-top:10px;">Already have an account? <a href="./login.php">Login here</a>.</p>
        </form>
    </div>
</body>

</html>