<?php
	require_once "pdo.php";
	require_once "function.php";

	session_start();
	$stmt = $pdo->prepare("SELECT profile_id, first_name, last_name, summary, email, headline FROM profile WHERE profile_id = :xyz");
	$stmt->execute(array(":xyz" => $_GET['profile_id']));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

	$stmt1 = $pdo->prepare("SELECT * FROM Position where profile_id = :xyz");
	$stmt1->execute(array(":xyz" => $_GET['profile_id']));
	$rows = $stmt1->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
	<?php head_info(); ?>
</head>
<body>
	<div class="container">
	    <h1>Profile information</h1>
	    <p>First Name: <?php echo ($row['first_name']); ?></p>
	    <p>Last Name: <?php echo ($row['last_name']); ?></p>
	    <p>Email: <?php echo ($row['email']); ?></p>
	    <p>Headline: <br><?php echo ($row['headline']); ?></p>
	    <p>Summary: <br><?php echo ($row['summary']); ?></p>
	    <p>Position: <br/><ul>
        <?php
        foreach ($rows as $row) {
            echo('<li>'.$row['year'].':'.$row['description'].'</li>');
        } ?>
        </ul></p>
	    <p><a href="index.php">Done</a></p>
	</div>
</body>
</html>