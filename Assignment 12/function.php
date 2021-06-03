<?php
	function head_info() {
		echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">';
		echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">';
		echo '<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">';
		echo '<script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>';
		echo '<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>';
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

	function validate_education() {
	    for ($i = 1; $i <= 9; $i++) {
	        if (!isset($_POST['edu_year' . $i])) continue;
	        if (!isset($_POST['edu_school' . $i])) continue;

	        $edu_year = $_POST['edu_year' . $i];
	        $edu_school = $_POST['edu_school' . $i];

	        if (strlen($edu_year) == 0 || strlen($edu_school) == 0) {
	            return "All fields are required";
	        }
	        if (!is_numeric($edu_year)) {
	            return "Education year must be numeric";
	        }
	    }
	    return true;
	}

	function loadPro($pdo, $profile_id) {
	    $stmt = $pdo->prepare('SELECT * FROM Profile where profile_id = :prof');
	    $stmt->execute(array(":prof" => $profile_id));
	    $profile = $stmt->fetch();
	    return $profile;
	}

	function loadPos($pdo, $profile_id) {
	    $stmt = $pdo->prepare('SELECT * FROM Position where profile_id = :prof ORDER BY rank');
	    $stmt->execute(array(":prof" => $profile_id));
	    $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
	    return $positions;
	}

	function loadEdu($pdo, $profile_id) {
	    $stmt = $pdo->prepare('SELECT year,name FROM Education join Institution on Education.institution_id = Institution.institution_id
	 where profile_id = :prof ORDER BY rank');
	    $stmt->execute(array(":prof" => $profile_id));
	    $educations = $stmt->fetchAll(PDO::FETCH_ASSOC);
	    return $educations;
	}

	function insertPosition($pdo, $profile_id) {
	    $rank = 1;
	    for ($i = 1; $i <= 9; $i++) {
	        if (!isset($_POST['year' . $i])) continue;
	        if (!isset($_POST['desc' . $i])) continue;


	        $year = $_POST['year' . $i];
	        $desc = $_POST['desc' . $i];

	        $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description) VALUES ( :pid, :rank, :year, :desc)');

	        $stmt->execute(array(
	                ':pid' => $profile_id,
	                ':rank' => $rank,
	                ':year' => $year,
	                ':desc' => $desc)
	        );
	        $rank++;
	    }
	}

	function insertEducation($pdo, $profile_id) {
	    $rank = 1;
	    for ($i = 1; $i <= 9; $i++) {
	        if (!isset($_POST['edu_year' . $i])) continue;
	        if (!isset($_POST['edu_school' . $i])) continue;
	        $year = $_POST['edu_year' . $i];
	        $school = $_POST['edu_school' . $i];

	        print_r($year.$school) ;

	        $institution_id = false;

	        $stmt = $pdo->prepare('SELECT institution_id FROM Institution WHERE name = :name;');
	        $stmt->execute(array(':name' => $school));
	        $row = $stmt->fetch(PDO::FETCH_ASSOC);
	        if ($row != false) $institution_id = $row['institution_id'];

	        if ($institution_id === false) {
	            $stmt = $pdo->prepare('INSERT INTO Institution (name) VALUES (:name)');
	            $stmt->execute(array(':name' => $school));
	            $institution_id = $pdo->lastInsertId();
	        }

	        $stmt = $pdo->prepare('INSERT INTO Education (profile_id, rank, year, institution_id) VALUES ( :pid, :rank, :year, :iid)');
	        $stmt->execute(array(
	                ':pid' => $profile_id,
	                ':rank' => $rank,
	                ':year' => $year,
	                ':iid' => $institution_id)
	        );
	        $rank++;
	    }
	}
?>