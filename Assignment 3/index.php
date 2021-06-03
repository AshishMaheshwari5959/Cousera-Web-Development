<!DOCTYPE html>
<html lang='en'>
	<head>
		<title>Ashish Maheshwari PHP</title>
	</head>
	<body>
		<h1>Ashish Maheshwari PHP</h1>
		<p>The ASCII Art is :</p>
		<pre>
    @@@@@@@
    @     @
    @     @
    @     @
    @@@@@@@
    @     @
    @     @
    @     @
		</pre>
		<?php
			echo "<pre>";
			echo 'The SHA256 hash of "Ashish Maheshwari" is ';
			print hash('sha256', 'Ashish Maheshwari');
			echo "</pre>";
		?>
		<p>Click below to find errors. <br>
			<a href="check.php">Click here to check the error setting</a><br>
			<a href="fail.php">Click here to cause a traceback</a>
		</p>
	</body>
</html>