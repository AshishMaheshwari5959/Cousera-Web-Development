<?php
	session_start();

	require_once "pdo.php";
	require_once "function.php";

	$failure = false;
	$pass = false;
	$stmt = $pdo->query("SELECT profile_id, first_name, headline, last_name FROM profile");
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
	<title>Ashish Maheshwari's Resume Registry</title>
	<?php require_once "bootstrap.php"; ?>
</head>
<body>
	<div class="container">
		<h1>Ashish Maheshwari's Resume Registry</h1>
		<?php
	            if ( ! isset($_SESSION['name']) ) {
	            	echo "<p><a href='login.php'>Please log in</a></p>";
	            	echo "<p><table border='2' cellPadding='10'>";
	            	if ($rows == false) {
	            		echo "No Rows Found";
		            } else {
		           		echo "<tr><td style='text-align:center'><b>Name</b></td><td style='text-align:center'><b>Headline</b></td></tr>";
		                foreach ( $rows as $row ) {
		                    echo "<tr><td style='text-align:center'>";
		                    echo "<a href='view.php?profile_id=".$row['profile_id']."'>";
		                    echo($row['first_name'].' '.$row['last_name']);
		                    echo "</a>";
		                    echo "</td><td style='text-align:center'>";
		                    echo($row['headline']);
		                    echo "</td></tr>";
		                }
		            }
		            echo "</table></p>";
	            } else {
	            	if ( isset($_SESSION['error']) ) {
	            	    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
	            	    unset($_SESSION['error']);
	            	}
	            	if ( isset($_SESSION['success']) ) {
	            	    echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
	            	    unset($_SESSION['success']);
	            	}
	            	echo "<p><a href='logout.php'>Logout</a></p>";
	            	echo "<p><table border='2' cellPadding='10'>";
	            	if ($rows == false) {
	            		echo "No Rows Found";
		            } else {
		           		echo "<tr><td style='text-align:center'><b>Name</b></td><td style='text-align:center'><b>Headline</b></td><td style='text-align:center'><b>Action</b></td></tr>";
		                foreach ( $rows as $row ) {
		                    echo "<tr><td style='text-align:center'>";
		                    echo "<a href='view.php?profile_id=".$row['profile_id']."'>";
		                    echo($row['first_name'].' '.$row['last_name']);
		                    echo "</a>";
		                    echo "</td><td style='text-align:center'>";
		                    echo($row['headline']);
		                    echo "</td><td style='text-align:center'>";
		                    echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a>');
		                    echo(' / ');
		    				echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
		                    echo "</td></tr>";
		                }
		            }
		            echo "</table></p>";
		            echo "<p><a href='add.php'>Add New Entry</a></p>";
	    	    }
            ?>
		<p>Note: Your implementation should retain data across multiple logout/login sessions. This sample implementation clears all its data periodically - which you should not do in your implementation.</p>
	</div>
</body>
</html>