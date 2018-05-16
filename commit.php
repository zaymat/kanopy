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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    <title>Kanopy</title>
</head>
<body>
<?php
    $res = $db->prepare("SELECT * FROM `commits` JOIN authors on commits.authorID = authors.id WHERE sha=?");
    $res->execute(array($id));
    $commit = $res->fetch();
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
    $res->closeCursor(); 
    ?>

    <?php 
    $patches = getPatches($commit["url"]);
    foreach($patches as $patch){
    ?>

    <?php 
    $patches = preg_replace("#^-(.*)#m", "<tr><th class=\"redline\" scope=\"row\">$0</th></tr>", $patch["patch"]);
    $patches = preg_replace("#^\+(.*)#m", "<tr><th class=\"greenline\" scope=\"row\">$0</th></tr>",$patches);
    $patches = preg_replace("#^@@(.*)#m", "<tr><th class=\"hunk\" scope=\"row\">$0</th></tr>",$patches);
    $patches = preg_replace("#^( *)$#m", "", $patches);
    $patches = preg_replace("#^[^+@<-].*#m", "<tr><th class=\"normal\" scope=\"row\">$0</th></tr>", $patches);
    $patches = preg_replace("#\\t#", "&emsp;", $patches);
    ?>
    
    <div class="patch">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col"><?php echo $patch["filename"]; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php echo $patches; ?>
            </tbody>
        </table>
    </div>

    <?php 
    }
    ?>
    </div>

    <div>
        <a href="index.php" class="btn btn-primary">Return to main page</a>
    </div>
</body>
</html>

