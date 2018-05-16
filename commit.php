<?php 
require_once("./db.php");
$db = connectDB();

if (isset($_GET['id'])) 
{
	$id = $_GET['id'];
}
else
{
	echo 'Error: commit ID is missing';
}


function getPatches($url){
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('User-Agent: zaymat'));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_COOKIESESSION, true);

    $ret = curl_exec($curl);

    curl_close($curl);

    $patches = json_decode($ret, true);

    return $patches["files"];
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