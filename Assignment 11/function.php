<?php
	function head_info() {
		echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">';
		echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">';
		echo '<script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>';
		echo "<title>Ashish Maheshwari</title>";
	}

	function flash_message() {
		if ( isset($_SESSION['error']) ) {
		    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
		    unset($_SESSION['error']);
		}
		if ( isset($_SESSION['success']) ) {
		    echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
		    unset($_SESSION['success']);
		}
	}

	function validate_profile() {
		if ( strlen($_POST['first_name'])<1 || strlen($_POST['last_name'])<1 || strlen($_POST['email'])<1 || strlen($_POST['headline'])<1 || strlen($_POST['summary'])<1) {
			return "All fields are required";
		}
		if (strpos($_POST['email'], '@') === false) {
			return "Email address must contain @";
		}
		return true;
	}

	function validate_position() {
	    for ($i = 1; $i <= 9; $i++) {
	        if (!isset($_POST['year' . $i])) continue;
	        if (!isset($_POST['desc' . $i])) continue;

	        $year = $_POST['year' . $i];
	        $desc = $_POST['desc' . $i];

	        if (strlen($year) == 0 || strlen($desc) == 0) {
	            return "All fields are required";
	        }
	        if (!is_numeric($year)) {
	            return "Position year must be numeric";
	        }
	    }
	    return true;
	}
?>