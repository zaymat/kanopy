<?php

require_once("./db.php");

// Curl the API to get the data
$curl = curl_init();

curl_setopt($curl, CURLOPT_URL, 'https://api.github.com/repos/torvalds/linux/commits');
curl_setopt($curl, CURLOPT_HTTPHEADER, array('User-Agent: zaymat'));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_COOKIESESSION, true);

$ret = curl_exec($curl);

$json = json_decode($ret, true);

foreach($json as $commit){
    $req = $db->prepare("INSERT INTO commits(author) VALUES (?)");
    $req->execute(array($commit["commit"]["author"]["name"]));
}
?>

<ul>
    <?php
    $res = $db->query('SELECT * FROM commits');
    while ($commit = $res->fetch()){
    ?>
    <li><?php echo $commit["author"]; ?></li>
    <?php
    }
    $res->closeCursor(); ?>
</ul>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Kanopy</title>
</head>
<body>
    Hello World
</body>
</html>