<?php
	session_start();

	require_once "pdo.php";

	$failure = false;
	$pass = false;
	$stmt = $pdo->query("SELECT make, model, year, mileage, autos_id FROM autos");
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
            <p><h2>Welcome to the Automobiles Database</h2></p>
            <?php
	            if ( ! isset($_SESSION['name']) ) {
	                echo "<p><a href='login.php'>Please log in</a></p>";
	                echo "<p>Attempt to <a href='add.php'>add data</a> without logging in</p>";
	            } else {
	            	if ( isset($_SESSION['error']) ) {
	            	    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
	            	    unset($_SESSION['error']);
	            	}
	            	if ( isset($_SESSION['success']) ) {
	            	    echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
	            	    unset($_SESSION['success']);
	            	}
	            	echo "<p><table border='2' cellPadding='10'>";
	            	if ($rows == false) {
	            		echo "No Rows Found";
		            } else {
		           		echo "<tr><td style='text-align:center'>----Make----</td><td style='text-align:center'>---Model---</td><td style='text-align:center'>--Year--</td><td style='text-align:center'>--Mileage--</td><td style='text-align:center'>------Action------</td></tr>";
		                foreach ( $rows as $row ) {
		                    echo "<tr><td style='text-align:center'>";
		                    echo($row['make']);
		                    echo "</td><td style='text-align:center'>";
		                    echo($row['model']);
		                    echo "</td><td style='text-align:center'>";
		                    echo($row['year']);
		                    echo "</td><td style='text-align:center'>";
		                    echo($row['mileage']);
		                    echo "</td><td style='text-align:center'>";
		                    echo('<a href="edit.php?autos_id='.$row['autos_id'].'">Edit</a> / ');
		    				echo('<a href="delete.php?autos_id='.$row['autos_id'].'">Delete</a>');
		                    echo "</td></tr>";
		                }
		            }
		            echo "</table></p>";
		            echo "<p><a href='add.php'>Add New Entry</a></p>";
	    	        echo "<p><a href='logout.php'>Logout</a></p>";
	    	    }
            ?>
        </div>
    </body>
</html>