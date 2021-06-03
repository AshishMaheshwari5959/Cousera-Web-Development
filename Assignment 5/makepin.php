<?php
	$error = false;
	$md5 = false;
	$code = "";
	if ( isset($_GET['code']) ) {
	    $code = $_GET['code'];
	    if ( strlen($code) != 4 ) {
	        $error = "Input must be exactly four characters";
	    } else if ( is_numeric($code) == FALSE ) {
	        $error = "Input must numeric";
	    } else {
	        $md5 = hash('md5', $code);
	    }
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Ashish Maheshwari PIN Code</title>
		<style>
			p {
				font-family: Arial,sans-serif,serif;
			}
			h1 {
				font-family: Arial,sans-serif,serif;
			}
			a {
				font-family: Arial,sans-serif,serif;
			}
		</style>
	</head>
	<body>
		<h1>MD5 PIN Maker</h1>
		<?php
			if ( $error !== false ) {
			    print '<p style="color:red">';
			    print htmlentities($error);
			    print "</p>\n";
			}

			if ( $md5 !== false ) {
			    print "<p>MD5 value: ".htmlentities($md5)."</p>";
			}
		?>
		<form>
			<input type="text" name="code" value="<?= htmlentities($code) ?>"/>
			<input type="submit" value="Compute MD5 for CODE"/>
		</form>
		<ul>
			<li><a href="makecode.php">Reset this page</a></li>
			<li><a href="index.php">Back to Cracking</a></li>
		</ul>
	</body>
</html>