<?php 
require_once("./db.php");
$db = connectDB();
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
        $res = $db->query("SELECT * FROM `commits` JOIN authors on commits.authorID = authors.id");
        while ($commit = $res->fetch()){
        ?>
        <li><img src=<?php echo $commit["image"]; ?> alt="Mountain View"></li>
        <?php
        }
        $res->closeCursor(); ?>
    </ul>
</body>
</html>