<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet" type="text/css">
</head>

<body>
    <div class="container">
        <form class="form-signin" action="login_result.php" method="post">
            <h2 class="form-signin-heading">Please sign in</h2>
            <label for="inputemail" class="sr-only">email</label>
            <input type="text" id="inputemail" class="form-control" placeholder="email" required autofocus name="email">
            <label for="inputPa ssword" class="sr-only">Password</label>
            <input type="password" id="inputPassword" class="form-control" placeholder="Password" required name="password">
            <div class="checkbox">
                <label>
                    <input type="checkbox" value="remember-me"> Remember me
                </label>
            </div>
            <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
            <?php
                // Determine the base URL dynamically
                $register = 'register.php';

                // Display the registration link with the dynamic base URL
                echo '<p>Not a user? <a href="' . $register . '">Register</a></p>';
            ?>
        </form>
    </div>
</body>

</html>