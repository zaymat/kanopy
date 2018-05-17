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
    $author = $db->prepare("SELECT * FROM `commits` LEFT JOIN authors on commits.authorID = authors.id WHERE sha=?");
    $author->execute(array($id));
    $commit_author = $author->fetch();

    $committer = $db->prepare("SELECT * FROM `commits` LEFT JOIN authors on commits.committerID = authors.id WHERE sha=?");
    $committer->execute(array($id));
    $commit_committer = $committer->fetch();

    $author->closeCursor(); 
    $committer->closeCursor(); 
 
    $patches = getPatches($commit_author["url"]);
    ?>

    <div class="jumbotron">
        <h1 class="display-4">Commit message</h1>
        <p>
            <?php 
                $msg = preg_replace("#\\n#", "<br>", $commit_author["msg"]);
                echo preg_replace("#\\t#", "&emsp;", $msg); 
            ?> 
        </p>
        <hr class="my-4">

        <h1 class="display-4">Author</h1>
        <a href=<?php echo $commit_author["authorUrl"]; ?>><img src=<?php echo $commit_author["image"]; ?> style="width: 10vh"></a>
        <p class="lead">Name: <?php echo $commit_author["authorName"]; ?></p>
        <p class="lead">Email: <?php echo $commit_author["email"]; ?></p>
        <hr class="my-4">

        <h1 class="display-4">Committer</h1>
        <a href=<?php echo $commit_committer["authorUrl"]; ?>><img src=<?php echo $commit_committer["image"]; ?> style="width: 10vh"></a>
        <p class="lead">Name: <?php echo $commit_committer["committerName"]; ?></p>
        <p class="lead">Email: <?php echo $commit_committer["email"]; ?></p>
        <hr class="my-4">

        <?php
            foreach($patches as $patch){
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
                        <th scope="col" style="background-color: white"><?php echo $patch["filename"]; ?></th>
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
        <div>
            <a href="index.php" class="btn btn-primary btn-lg">Return to main page</a>
        </div>
    </div>
</body>
</html>

