<?php
require_once "pdo.php";
require_once "function.php";
session_start();

if ( ! isset($_SESSION['name']) ) {
    die('ACCESS DENIED');
}

// If the user requested logout go back to index.php
if ( isset($_POST['cancel']) ) {
  header('Location: index.php');
  return;
}

if ( isset($_POST['add'])) {
    if ( isset($_POST['first_name']) && isset($_POST['email']) ) {
        $msg = validate_profile();
        $msg2 = validate_position();
        $msg3 = validate_education();
        if (is_string($msg)) {
            $_SESSION['error'] = $msg;
            header("Location: add.php");
            return;
        } elseif (is_string($msg2)) {
            $_SESSION['error'] = $msg2;
            header("Location: add.php");
            return;
        } elseif (is_string($msg3)) {
            $_SESSION['error'] = $msg3;
            header("Location: add.php");
            return;
        } else {
            $stmt = $pdo->prepare('INSERT INTO Profile
              (user_id, first_name, last_name, email, headline, summary)
              VALUES ( :uid, :fn, :ln, :em, :he, :su)');

            $stmt->execute(array(
              ':uid' => $_SESSION['user_id'],
              ':fn' => $_POST['first_name'],
              ':ln' => $_POST['last_name'],
              ':em' => $_POST['email'],
              ':he' => $_POST['headline'],
              ':su' => $_POST['summary'])
            );

            $profile_id = $pdo->lastInsertId();

            $rank = 1;
            for($i=1; $i<=9; $i++) {
                if ( ! isset($_POST['year'.$i]) ) continue;
                if ( ! isset($_POST['desc'.$i]) ) continue;
                $year = $_POST['year'.$i];
                $desc = $_POST['desc'.$i];
                $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description) VALUES ( :pid, :rank, :year, :desc)');
                $stmt->execute(array(
                    ':pid' => $profile_id,
                    ':rank' => $rank,
                    ':year' => $year,
                    ':desc' => $desc)
                );
                $rank++;
            }
            $rank++;

            $rank2 = 1;
            for ($i = 1; $i <= 9; $i++) {
                if (!isset($_POST['edu_year' . $i])) continue;
                if (!isset($_POST['edu_school' . $i])) continue;

                $edu_year = $_POST['edu_year' . $i];
                $edu_school = $_POST['edu_school' . $i];

                $stmt = $pdo->prepare("SELECT * FROM Institution where name = :xyz");
                $stmt->execute(array(":xyz" => $edu_school));
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row) {
                    $institution_id = $row['institution_id'];
                } else {
                    $stmt = $pdo->prepare('INSERT INTO Institution (name) VALUES ( :name)');
                    $stmt->execute(array(':name' => $edu_school,));
                    $institution_id = $pdo->lastInsertId();
                }

                $stmt = $pdo->prepare('INSERT INTO Education (profile_id, institution_id, year, rank) VALUES ( :pid, :institution, :edu_year, :rank)');
                $stmt->execute(array(
                        ':pid' => $profile_id,
                        ':institution' => $institution_id,
                        ':edu_year' => $edu_year,
                        ':rank' => $rank2)
                );
                $rank2++;
            }
            $rank2++;

            $_SESSION['success'] = "Profile added";
            header("Location: index.php");
            return;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php head_info(); ?>
    </head>
    <body>
        <div class="container">
            <h1>Adding Profile for <?= $_SESSION['name'] ?></h1>
            <?php flash_message(); ?>
            <form method="post">
            <p>First Name:
            <input type="text" name="first_name" size="60"></p>
            <p>Last Name:
            <input type="text" name="last_name" size="60"></p>
            <p>Email:
            <input type="text" name="email" size="30"></p>
            <p>Headline:<br>
            <input type="text" name="headline" size="80"></p>
            <p>Summary:<br>
            <textarea name="summary" rows="8" cols="80"></textarea></p>
            <p>Education:  
            <input type="submit" name="addEdu" id="addEdu" value="+">
            <div id="edu_fields"></div></p>
            <p>Position: 
            <input type="submit" name="addPos" value="+" id="addPos">
            <div id="position_fields"></div></p>
            <p>
            <input type="submit" name="add" value="Add">
            <input type="submit" name="cancel" value="Cancel">
            </p>
            </form>
            <script>
            countPos = 0;
            countEdu = 0;
            // http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
            $(document).ready(function(){
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