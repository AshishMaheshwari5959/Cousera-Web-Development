<?php
require_once "pdo.php";
require_once "function.php";
session_start();

if ( ! isset($_SESSION['name']) ) {
    die('ACCESS DENIED');
}

if ( isset($_POST['cancel']) ) {
  header('Location: index.php');
  return;
}

if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

if (isset($_POST['Save'])) {

    // Data validation
    $msg = validate_profile();
    $msg2 = validate_position();
    $msg3 = validate_education();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=".$_POST['profile_id']); 
        return;
    } elseif (is_string($msg2)) {
        $_SESSION['error'] = $msg2;
        header("Location: edit.php?profile_id=".$_POST['profile_id']); 
        return;
    } elseif (is_string($msg3)) {
        $_SESSION['error'] = $msg3;
        header("Location: add.php");
        return;
    } else {
        $sql = "UPDATE profile SET user_id= :uid, first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su WHERE profile_id = :profile_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':uid' => $_SESSION['user_id'],
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary'],
            ':profile_id' => $_POST['profile_id']));

        $stmt = $pdo->prepare('DELETE FROM position WHERE profile_id=:pid');
        $stmt->execute(array(':pid' => $_GET['profile_id']));

        $rank = 1;
        for ($i = 1; $i <= 9; $i++) {
            if (!isset($_POST['year' . $i])) continue;
            if (!isset($_POST['desc' . $i])) continue;

            $year = $_POST['year' . $i];
            $desci = $_POST['desc' . $i];
            $stmt2 = $pdo->prepare('INSERT INTO position (profile_id, rank, year, description) VALUES ( :pid, :rank, :year, :desci )');
            $stmt2->execute(array(
                    ':pid' => $_GET['profile_id'],
                    ':rank' => $rank,
                    ':year' => $year,
                    ':desci' => $desci ));
            $rank++;
        }

        $stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id=:pid');
        $stmt->execute(array(':pid' => $_REQUEST['profile_id']));
        
        insertEducation($pdo, $_REQUEST['profile_id']);

        $_SESSION['success'] = 'Profile updated';
        header( 'Location: index.php' ) ;
        return;
    }
}

// Guardian: Make sure that autos_id is present
$stmt = $pdo->prepare("SELECT * FROM profile WHERE profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Could not load profile';
    header( 'Location: index.php' ) ;
    return;
}
$stmt = $pdo->prepare("SELECT * FROM Position where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$rowOfPosition = $stmt->fetchAll();

$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$em = htmlentities($row['email']);
$he = htmlentities($row['headline']);
$su = htmlentities($row['summary']);
$profile_id = $row['profile_id'];

#$profile = loadPro($pdo, $_REQUEST['profile_id']);
#$positions = loadPos($pdo, $_REQUEST['profile_id']);
$schools = loadEdu($pdo, $_REQUEST['profile_id']);
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <?php head_info(); ?>
    </head>
    <body>
        <div class="container">
            <h1>Editing Profile for <?= $_SESSION['name'] ?></h1>
            <?php flash_message(); ?>
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
            <p>Education: 
                <input type="submit" id="addEdu" value="+">
                <div id="edu_fields">
                <?php
                $rank2 = 0;
                if (count($schools) > 0) {
                    foreach ($schools as $school) {
                        $rank2++;
                        echo('<div id="edu' . $rank2 . '">');
                        echo '<p>Year: <input type="text" name="edu_year' . $rank2 . '" value="' . $school['year'] . '"><input type="button" value="-" onclick="$(\'#edu' . $rank2 . '\').remove();return false;\"></p><p>School: <input type="text" size="80" name="edu_school' . $rank2 . '" class="school" value="' . htmlentities($school['name']) . '" />';
                        echo "\n</div>\n";
                    }
                } 
                ?>
                </div>
            </p>
            <p>Position: 
                <input type="submit" id="addPos" value="+">
                <div id="position_fields">
                <?php
                $rank = 0;
                foreach ($rowOfPosition as $row) {
                    $rank++;
                    echo "<div id=\"position" . $rank . "\">";
                    echo "<p>Year: <input type=\"text\" name=\"year". $rank. "\" value=\"".$row['year']."\">";
                    echo "<input type=\"button\" value=\"-\" onclick=\"$('#position". $rank ."').remove();return false;\"></p><textarea name=\"desc". $rank ."\"').\" rows=\"8\" cols=\"80\">";
                    echo $row['description'];
                    echo "</textarea></div>";
                } 
                ?>
                </div>
            </p>
            <p>
            <input type="submit" name="Save" value="Save">
            <input type="submit" name="cancel" value="Cancel">
            </p>
            </form>
            <script>
            countPos = <?= $rank ?>;
            countEdu = <?= $rank2 ?>;

            // http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
            $(document).ready(function () {
                window.console && console.log('Document ready called');
                $('#addEdu').click(function (event) {
                    event.preventDefault();
                    if (countEdu >= 9) {
                        alert("Maximum of nine education entries exceeded");
                        return;
                    }
                    countEdu++;
                    window.console && console.log("Adding education " + countEdu);

                    $('#edu_fields').append(
                        '<div id="edu' + countEdu + '"> \
                        <p>Year: <input type="text" name="edu_year' + countEdu + '" value="" /> \
                        <input type="button" value="-" onclick="$(\'#edu' + countEdu + '\').remove();return false;"><br>\
                        <p>School: <input type="text" size="80" name="edu_school' + countEdu + '" class="school" value="" />\
                        </p></div>'
                    );

                    $('.school').autocomplete({
                        source: "school.php"
                    });
                });
                $('#addPos').click(function(event){
                    // http://api.jquery.com/event.preventdefault/
                    event.preventDefault();
                    if ( countPos >= 9 ) {
                        alert("Maximum of nine position entries exceeded");
                        return;
                    }
                    countPos++;
                    window.console && console.log("Adding position "+countPos);
                    $('#position_fields').append(
                        '<div id="position'+countPos+'"> \
                        <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
                        <input type="button" value="-" \
                            onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
                        <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea> \
                        </div>');
                });
            });
            </script>
        </div>
    </body>
</html>