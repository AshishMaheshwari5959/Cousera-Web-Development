<?php

// Demand a GET parameter
if ( ! isset($_GET['name']) || strlen($_GET['name']) < 1  ) {
    die('Name parameter missing');
}

// If the user requested logout go back to index.php
if ( isset($_POST['logout']) ) {
    header('Location: index.php');
    return;
}

require_once "pdo.php";

$failure = false;
$pass = false;
if ( isset($_POST['add'])) {
    if ( isset($_POST['make']) && isset($_POST['year']) && isset($_POST['mileage']) ) {
        if ( strlen($_POST['make']) < 1 ) {
            $failure = "Make is required";
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
                $pass = "Record inserted";
            } else {
                $failure = "Mileage and year must be numeric";
            }
        }
    }
}
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
                if ( isset($_REQUEST['name']) ) {
                    echo htmlentities($_REQUEST['name']);
                }
            ?>
            </h1>
            <?php
                if ( $failure !== false ) {
                    echo('<p style="color: red;">'.htmlentities($failure)."</p>\n");
                }
                if ( $pass !== false ) {
                    echo('<p style="color: green;">'.htmlentities($pass)."</p>\n");
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
        </div>
    </body>
</html>