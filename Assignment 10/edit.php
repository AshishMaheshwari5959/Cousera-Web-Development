<?php
require_once "pdo.php";
session_start();

if ( ! isset($_SESSION['name']) ) {
    die('ACCESS DENIED');
}

if ( isset($_POST['cancel']) ) {
  header('Location: index.php');
  return;
}

if (isset($_POST['Save'])) {

    // Data validation
    if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
        $_SESSION['error'] = 'All fields are required';
        header("Location: edit.php?profile_id=".$_POST['profile_id']);
        return;
    }
    if ( strpos($_POST['email'], '@') !== false ) {
        $sql = "UPDATE profile SET first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su WHERE profile_id = :profile_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary'],
            ':profile_id' => $_POST['profile_id']));
        $_SESSION['success'] = 'Profile updated';
        header( 'Location: index.php' ) ;
        return;
    } else {
        $_SESSION['error'] = 'Email address must contain @';
        header("Location: edit.php?profile_id=".$_POST['profile_id']);
        return;
    }
}

// Guardian: Make sure that autos_id is present
if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT * FROM profile WHERE profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Could not load profile';
    header( 'Location: index.php' ) ;
    return;
}

$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$em = htmlentities($row['email']);
$he = htmlentities($row['headline']);
$su = htmlentities($row['summary']);
$profile_id = $row['profile_id'];
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Ashish Maheshwari</title>
        <?php require_once "bootstrap.php"; ?>
    </head>
    <body>
        <div class="container">
            <h1>Editing Profile for 
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
            <input type="text" name="first_name" value="<?= $fn ?>" size="60"></p>
            <p>Last Name:
            <input type="text" name="last_name" value="<?= $ln ?>" size="60"></p>
            <p>Email:
            <input type="text" name="email" value="<?= $em ?>" size="30"></p>
            <input type="hidden" name="profile_id" value="<?= $profile_id ?>">
            <p>Headline:<br>
            <input type="text" name="headline" value="<?= $he ?>" size="80"></p>
            <p>Summary:<br>
            <textarea name="summary" rows="8" cols="80"><?= $su ?></textarea></p>
            <p>
            <input type="submit" name="Save" value="Save">
            <input type="submit" name="cancel" value="Cancel">
            </p>
            </form>
        </div>
    </body>
</html>