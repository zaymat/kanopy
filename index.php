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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    <title>Kanopy</title>
</head>
<body>
    <div class="list-group col-6">
        <?php
        $res = $db->query("SELECT * FROM `commits` JOIN authors on commits.authorID = authors.id ORDER BY `date` DESC");
        while ($commit = $res->fetch()){
        ?>
        <a href=<?php echo "commit.php?id=" . $commit["sha"]; ?> class="list-group-item list-group-item-action flex-column align-items-start">
            <div class="d-flex w-100 justify-content-between">
                <h5 class="mb-1"><?php echo substr($commit["msg"], 0, 30) . " ..."; ?></h5>
                <small><?php echo $commit["date"];?></small>
            </div>
            <small><?php echo $commit["sha"]; ?></small>
        </a>
        <?php
        }
        $res->closeCursor(); ?>
    </ul>
</body>
</html>