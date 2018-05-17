<?php

require_once("./db.php");
$db = connectDB();
createTables($db);


// Curl the API to get the data
$curl = curl_init();

$url = "https://api.github.com/repos/torvalds/linux/commits";

if(isset($_GET["url"])){
    $url = $_GET["url"];
}

curl_setopt($curl, CURLOPT_URL, $url);
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
    <div class="row">
        <div class="col-12 input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon3">Search</span>
            </div>
            <input placeholder=<?php echo $url ?> type="text" class="form-control" id="basic-url" name="url" aria-describedby="basic-addon3" formaction="index.php" formmethod="get">
        </div>
    </div>
    
    <?php
    // We perform a External Join to ensure all commits are printed even if there is no committer
    $res = $db->query("SELECT * FROM `commits` LEFT JOIN authors on commits.committerID = authors.id ORDER BY `date` DESC");
    while ($commit = $res->fetch()){
    ?>
    <div class="row">
        <div class="col-sm-3">
        </div>
        <div class="list-group col-sm-6">
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
        </div>
    </div>
    <?php
    }
    $res->closeCursor(); 
    ?>
</body>
</html>