<?php include_once("header.php")?>
<?php include_once("database.php") ?>
<?php require_once('utilities.php'); ?>

<div class="container my-5">

<?php

// TODO: Extract $_POST variables, check they're OK, and attempt to create
// an account. Notify user of success/failure and redirect/give navigation 
// options.

// Process the registration form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Create database connectionX
    $connection = db_connect();

    // Validate data 
    if (empty($_POST['accountType']) || empty($_POST['email']) || 
        empty($_POST['password']) || empty($_POST['passwordConfirmation']) ||
        empty($_POST['firstName']) || empty($_POST['lastName'])) {

        $errorMessage = "Error: Required fields are empty. Please fill in the following:";

        if (empty($_POST['accountType'])) {
            $errorMessage .= "<br>- Account Type";
        }

        if (empty($_POST['email'])) {
            $errorMessage .= "<br>- Email";
        }

        if (empty($_POST['password'])) {
            $errorMessage .= "<br>- Password";
        }

        if (empty($_POST['passwordConfirmation'])) {
            $errorMessage .= "<br>- Password Confirmation";
        }

        if (empty($_POST['firstName'])) {
            $errorMessage .= "<br>- First Name";
        }

        if (empty($_POST['lastName'])) {
            $errorMessage .= "<br>- Last Name";
        }

    echo '<div class="alert alert-danger mt-3" role="alert">' . $errorMessage . '</div>';
    db_disconnect($connection);
    exit();
    }

    // Check if the email already exists
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $emailCheckQuery = "SELECT * FROM Users WHERE email = '$email'";
    $emailCheckResult = db_query($connection, $emailCheckQuery);

    if (mysqli_num_rows($emailCheckResult) > 0) {
        // Email already exists, show an error message
        echo '<div class="text-center"> User already exists. <a href="login.php">Go to login page.</a></div>';
        db_disconnect($connection);
        exit();
    }

    // Extract and sanitize form data
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $accountType = $_POST['accountType'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $passwordConfirmation = $_POST['passwordConfirmation'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Error: Invalid email format.";
        echo '<div class="alert alert-danger mt-3" role="alert">' . $errorMessage . '</div>';
        db_disconnect($connection);
        exit();
    }

    //Echo values to be inserted into database
    // Display entered values
    /*
    echo '<div class="text-center mt-3">';
    echo '<h3>Entered Values:</h3>';
    echo "<p><strong>Account Type:</strong> " . $accountType . "</p>";
    echo "<p><strong>Email:</strong> " . $email . "</p>";
    echo "<p><strong>Password:</strong> " . $password . "</p>";
    echo "<p><strong>Password Confirmation:</strong> " . $passwordConfirmation . "</p>";
    echo '</div>';
    */


    // Validate password match
    if ($password !== $passwordConfirmation) {
        echo '<div class="alert alert-danger mt-3" role="alert">Error: Passwords do not match.</div>';
        db_disconnect($connection);
        exit();
    }

    // Hash the password
    // $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Set role based on account type
    $role = ($accountType == 'seller') ? 0 : 1;

    // INSERT query for the Users table
    $query = "INSERT INTO Users (email, password, role, first_name, last_name) VALUES ('$email', '$password', '$role', '$firstName', '$lastName')";   

    // Execute the query
    $result = db_query($connection, $query);

    // Check the result of the database operation
    if ($result) {
        echo '<div class="text-center"> Account successfully created! <a href="login.php">Go to login page.</a></div>';

        // ********************* Send out email **************************

        //send email to user
        $recipient = $email;
        $subject = "Account Created!";
        $content = "<body> Welcome to Auction site! </body></br>";
        sendmail($recipient, $subject, $content);

        // ***************************************************************


    } else {
        echo '<div class="alert alert-danger mt-3" role="alert">Error: Registration failed.</div>';
    }

    // Close the database connection
    db_disconnect($connection);
    
}


?>

<?php include_once("footer.php"); ?>