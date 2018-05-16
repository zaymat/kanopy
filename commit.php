<?php 
require_once("./db.php");
$db = connectDB();

if (isset($_GET['id'])) // On a le nom et le prénom
{
	$id = $_GET['id'];
}
else // Il manque des paramètres, on avertit le visiteur
{
	echo 'Error: commit ID is missing';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Kanopy</title>
</head>
<body>
    <ul>
        <?php
        $res = $db->prepare("SELECT * FROM `commits` JOIN authors on commits.authorID = authors.id WHERE sha=?");
        $res->execute(array($id));
        while ($commit = $res->fetch()){
        ?>
        <li><img src=<?php echo $commit["image"]; ?> class="avatar"></li>
        <?php
        }
        $res->closeCursor(); ?>
    </ul>
    <a href="index.php">Return to main page</a>
</body>
</html>