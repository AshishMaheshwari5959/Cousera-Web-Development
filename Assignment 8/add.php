<?php
session_start();

if ( ! isset($_SESSION['name']) ) {
    die('Not logged in');
}

// If the user requested logout go back to index.php
if ( isset($_POST['logout']) ) {
    header('Location: index.php');
    return;
}

require_once "pdo.php";

if ( isset($_POST['add'])) {
    if ( isset($_POST['make']) && isset($_POST['year']) && isset($_POST['mileage']) ) {
        if ( strlen($_POST['make']) < 1 ) {
            $_SESSION['error'] = "Make is required";
            header("Location: add.php");
            return;
        } else {
            if ( is_numeric($_POST['year']) && is_numeric($_POST['mileage']) ) {
                $stmt = $pdo->prepare('INSERT INTO autos
                    (make, year, mileage) VALUES ( :mk, :yr, :mi)');
                $stmt->execute(array(
                    ':mk' => htmlentities($_POST['make']),
                    ':yr' => htmlentities($_POST['year']),
                    ':mi' => htmlentities($_POST['mileage'])
                    )
                );
                $_SESSION['success'] = "Record inserted";
                header("Location: view.php");
                return;
            } else {
                $_SESSION['error'] = "Mileage and year must be numeric";
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
            <h1>Tracking Autos for 
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
                <label for="nam">Make</label>
                <input type="text" name="make" id="nam"><br>
                <label for="nam">Year</label>
                <input type="text" name="year" id="nam"><br>
                <label for="nam">Mileage</label>
                <input type="text" name="mileage" id="nam"><br>
                <input type="submit" name="add" value="Add">
                <input type="submit" name="logout" value="Logout">
            </form><br>
        </div>
    </body>
</html>