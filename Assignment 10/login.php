<?php
    session_start();

    require_once "pdo.php";

    if ( isset($_POST['cancel'] ) ) {
        header("Location: index.php");
        return;
    }
    function test_input($data) {
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      return $data;
    }

    $failure = false;
    $salt = 'XyZzy12*_';  // If we have no POST data

    // Check to see if we have some POST data, if we do process it
    if ( isset($_POST['email']) && isset($_POST['pass']) ) {
        if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
            unset($_SESSION['name']);
            $_SESSION['error'] = "User name and password are required";
            header("Location: login.php");
            return;
        } else {
            $email = test_input($_POST["email"]);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Username must have an at-sign (@)";
                header("Location: login.php");
                return;
            } else {
                $check = hash('md5', $salt.$_POST['pass']);
                $stmt = $pdo->prepare('SELECT user_id, name FROM users
                WHERE email = :em AND password = :pw');
                $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                unset($_SESSION['name']);
                if ( $row !== false ) {
                    error_log("Login success ".$_POST['email']);
                    $_SESSION['name'] = $row['name'];
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['success'] = "Logged In.";
                    header("Location: index.php");
                    return;
                } else {
                    error_log("Login fail ".$_POST['email']." $check");
                    $_SESSION['error'] = "Incorrect password";
                    header("Location: login.php");
                    return;
                }    
            }
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <?php require_once "bootstrap.php"; ?>
        <title>Ashish Maheshwari's Login Page</title>
    </head>
    <body>
        <div class="container">
            <h1>Please Log In</h1>
            <?php
                if ( isset($_SESSION['error']) ) {
                    echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
                    unset($_SESSION['error']);
                }
            ?>
            <form method="POST" action="login.php">
                <label for="email">Email </label>
                <input type="text" name="email" id="email"><br/>
                <label for="id_1723">Password </label>
                <input type="password" name="pass" id="id_1723"><br/>
                <input type="submit" onclick="return doValidate();" value="Log In">
                <input type="submit" name="cancel" value="Cancel">
            </form>
            <p>
                For a password hint, view source and find a password hint in the HTML comments.
                <!-- Hint: The password is the three character backend for web  (all lower case) followed by 123. -->
            </p>
            <script>
            function doValidate() {
                console.log('Validating...');
                try {
                    addr = document.getElementById('email').value;
                    pw = document.getElementById('id_1723').value;
                    console.log("Validating addr="+addr+" pw="+pw);
                    if (addr == null || addr == "" || pw == null || pw == "") {
                        alert("Both fields must be filled out");
                        return false;
                    }
                    if ( addr.indexOf('@') == -1 ) {
                        alert("Invalid email address");
                        return false;
                    }
                    return true;
                } catch(e) {
                    return false;
                }
                return false;
            }
            </script>
        </div>
    </body>
</html>