<?php
session_start();

if ( ! isset($_SESSION['name']) ) {
    die('ACCESS DENIED');
}

// If the user requested logout go back to index.php
if ( isset($_POST['logout']) ) {
    header('Location: index.php');
    return;
}

require_once "pdo.php";

if ( isset($_POST['add'])) {
    if ( isset($_POST['first_name']) && isset($_POST['email']) ) {
        if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
            $_SESSION['error'] = "All fields are required";
            header("Location: add.php");
            return;
        } else {
            if ( strpos($_POST['email'], '@') !== false ) {
                
                $stmt = $pdo->prepare('INSERT INTO Profile
                  (user_id, first_name, last_name, email, headline, summary)
                  VALUES ( :uid, :fn, :ln, :em, :he, :su)');

                $stmt->execute(array(
                  ':uid' => $_SESSION['user_id'],
                  ':fn' => $_POST['first_name'],
                  ':ln' => $_POST['last_name'],
                  ':em' => $_POST['email'],
                  ':he' => $_POST['headline'],
                  ':su' => $_POST['summary'])
                );
                $_SESSION['success'] = "Profile added";
                header("Location: index.php");
                return;
            }
            else {
                $_SESSION['error'] = "Email address must contain @";
                header("Location: add.php");
                return;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Ashish Maheshwari</title>
        <?php require_once "bootstrap.php"; ?>
    </head>
    <body>
        <div class="container">
            <h1>Adding Profile for 
            <?php
                if ( isset($_SESSION['name']) ) {
                    echo htmlentities($_SESSION['name']);
                }
            ?>
            </h1>
            <?php
                if ( isset($_SESSION['success']) ) {
                    echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
                    unset($_SESSION['success']);
                }
                if ( isset($_SESSION['error']) ) {
                    echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
                    unset($_SESSION['error']);
                }
            ?>
            <form method="post">
            <p>First Name:
            <input type="text" name="first_name" size="60"></p>
            <p>Last Name:
            <input type="text" name="last_name" size="60"></p>
            <p>Email:
            <input type="text" name="email" size="30"></p>
            <p>Headline:<br>
            <input type="text" name="headline" size="80"></p>
            <p>Summary:<br>
            <textarea name="summary" rows="8" cols="80"></textarea>
            <p>
            <input type="submit" name="add" value="Add">
            <input type="submit" name="cancel" value="Cancel">
            </p>
            </form>
        </div>
    </body>
</html>