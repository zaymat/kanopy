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
curl_close($curl);

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
    <div class="col-12 title display1">
        <h1>List of commits</h1>
    </div>
    <div class="list-group col-6">
        <?php
        // We perform a External Join to ensure all commits are printed even if there is no committer
        $res = $db->query("SELECT * FROM `commits` LEFT JOIN authors on commits.committerID = authors.id ORDER BY `date` DESC");
        while ($commit = $res->fetch()){
        ?>
        <a href=<?php echo "commit.php?id=" . $commit["sha"]; ?> class="list-group-item list-group-item-action flex-column align-items-start">
            <div class="d-flex w-100 justify-content-between">
                <h5 class="mb-1">
                <?php
                if(strlen($commit["msg"]) <= 30){
                    echo $commit["msg"];
                }
                else{
                    echo substr($commit["msg"], 0, 30) . " ..."; 
                }
                ?>
                </h5>
                <small><?php echo substr($commit["sha"], 0, 10); ?></small>
            </div>
            <img src=<?php echo $commit["image"]; ?> style="width: 3vh">
            <small><? echo $commit["committerName"];?> committed at <?php echo $commit["date"];?></small>
            
        </a>
        <?php
        }
        $res->closeCursor(); ?>
    </ul>
</body>
</html>