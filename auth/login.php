<?php
// Initialize the session
session_start();

// Check if the user is already logged in; if so, redirect to the home
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: ../views/home.php");
    exit;
}

// Include config file
require_once "../includes/config.php";

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if username is empty
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter your username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($username_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT id, username, password FROM users WHERE username = ?";

        if ($stmt = $mysqli->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_username);

            // Set parameters
            $param_username = $username;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Store result
                $stmt->store_result();

                // Check if username exists; if yes, verify password
                if ($stmt->num_rows == 1) {
                    // Bind result variables
                    $stmt->bind_result($id, $username, $hashed_password);
                    if ($stmt->fetch()) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct; start a new session
                            session_start();

                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;

                            // Redirect user to the home
                            header("location: ../views/home.php");
                        } else {
                            // Invalid password
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else {
                    // Username doesn't exist
                    $login_err = "Invalid username or password.";
                }
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
    <link rel="stylesheet" href="../css/login-signup.css?<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/preloader.css?<?php echo time(); ?>">
    <script src="../js/preloader.js?<?php echo time(); ?>"></script>
    <script src="../js/fontawesome.js" crossorigin="anonymous"></script>
    <title>Scholarship System - Login</title>

    <style>
        .box {
            display: none;
        }

        body {
            overflow: hidden;
        }
    </style>
</head>

<body>
    
    <div class="loader"></div>

    <div class="box">
        <span class="borderLine"></span>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <h2>Scholarship System - Sign In</h2>

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
                <input type="password" name="password" <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" required>
                <span>Password</span>
                <i></i>
                <span><?php echo $password_err; ?></span>
            </div>
            <div class="links">
                <a href="./reset-password.php">Forgot Password?</a>
                <a href="./register.php">Create an Account</a>
            </div>
            <button type="submit" class="btn btn-submit">
                <span>Login</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>
    </div>

</body>

</html>
