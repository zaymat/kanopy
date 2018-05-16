<?php

require_once("./db.php");
$db = connectDB();
createTables($db);


// Curl the API to get the data
$curl = curl_init();

curl_setopt($curl, CURLOPT_URL, 'https://api.github.com/repos/torvalds/linux/commits');
curl_setopt($curl, CURLOPT_HTTPHEADER, array('User-Agent: zaymat'));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_COOKIESESSION, true);

$ret = curl_exec($curl);

$json = json_decode($ret, true);

foreach($json as $commit){
    commitToDB($commit, $db);
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
        $res = $db->query("SELECT * FROM `commits`");
        while ($commit = $res->fetch()){
        ?>
        <li><a href=<?php echo "commit.php?id=" . $commit["sha"]; ?>><?php echo $commit["sha"]; ?></a></li>
        <?php
        }
        $res->closeCursor(); ?>
    </ul>
</body>
</html>