<?php
// Connection to the database
function connectDB(){
    try
    {
        // Load configuration from file
        $config_json = file_get_contents("./config.json");
        $config = json_decode($config_json, true);

        // Connect to the database
        $db = new PDO("mysql:host=" . $config["hostname"] . ";dbname=" . $config["database"] . ";charset=utf8", $config["username"] , $config["password"]);
        return $db;
    }
    catch(Exception $e)
    {
            die('Erreur : '.$e->getMessage());
    }
}

function createTables($db){
    try
    {
        $db->exec("DROP TABLE IF EXISTS `commits`");
        $db->exec("DROP TABLE IF EXISTS `authors`");

        // Create authors table
        $db->exec("CREATE TABLE IF NOT EXISTS `authors` (id INT UNSIGNED PRIMARY KEY, `name` VARCHAR(255), email VARCHAR(255) , `image` VARCHAR(255), `login` VARCHAR(255), `authorUrl` VARCHAR(255)) ENGINE=InnoDB;");

        // Create commits table
        $db->exec("CREATE TABLE IF NOT EXISTS `commits` (sha VARCHAR(255) NOT NULL PRIMARY KEY, authorID INT, authorName VARCHAR(255), committerID INT, committerName VARCHAR(255), `date` DATETIME, msg TEXT, `url` VARCHAR(255)) ENGINE=InnoDB;");
    }
    catch(Exception $e)
    {
            die('Erreur : '.$e->getMessage());
    }
}

function commitToDB($commit, $db){

    // parse date to fit SQL requirements
    $date = preg_replace("#^(.+)T(.+)Z$#","$1 $2", $commit["commit"]["author"]["date"]);

    // Insert commit in the db
    $req = $db->prepare("INSERT INTO `commits` VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $req->execute(array($commit["sha"],
                        $commit["author"]["id"],
                        $commit["commit"]["author"]["name"],
                        $commit["committer"]["id"],
                        $commit["commit"]["committer"]["name"],
                        $date,
                        $commit["commit"]["message"],
                        $commit["url"]
                       ));

    // Add both authors and committers to the db
    $list = ["author", "committer"];

    foreach($list as $item){
        
        // Check if the author is already in the database
        $res = $db->prepare("SELECT * FROM `authors` WHERE id=?");
        $res->execute(array($commit[$item]["id"]));

        // Create a new user if the author is not in the db
        if(!$res->fetch()){
            $req = $db->prepare("INSERT INTO authors VALUES (?, ?, ?, ?, ?, ?)");
            $req->execute(array($commit[$item]["id"],
                                $commit["commit"][$item]["name"],
                                $commit["commit"][$item]["email"],
                                $commit[$item]["avatar_url"],
                                $commit[$item]["login"],
                                $commit[$item]["html_url"]));
        }
        $res->closeCursor();
    }

}

?>