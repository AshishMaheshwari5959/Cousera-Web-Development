<?php
session_start();

if ( ! isset($_SESSION['name']) ) {
    die('Not logged in');
}

// If the user requested logout go back to index.php
if ( isset($_POST['logout']) ) {
    header('Location: logout.php');
    return;
}

require_once "pdo.php";

$failure = false;
$pass = false;
$stmt = $pdo->query("SELECT make, year, mileage FROM autos");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                    echo('<p style="color: green;">'.htmlentities($_SESSION['error'])."</p>\n");
                    unset($_SESSION['error']);
                }
            ?>
            <h2>Automobiles</h2>
            <ul>
                <?php
                    foreach ( $rows as $row ) {
                        echo "<li>";
                        echo($row['year']);
                        echo " ";
                        echo($row['make']);
                        echo " / ";
                        echo($row['mileage']);
                        echo "</li>";
                    }
                ?>
            </ul>
            <p><a href="add.php">Add New</a> | <a href="logout.php">Logout</a>
        </div>
    </body>
</html>