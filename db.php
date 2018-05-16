<?php
// Connection to the database
function connectDB(){
    try
    {
        $config_json = file_get_contents("./config.json");
        $config = json_decode($config_json, true);
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
        $db->exec("CREATE TABLE IF NOT EXISTS `authors` (id INT UNSIGNED PRIMARY KEY, `name` VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, `image` VARCHAR(255) NOT NULL, `login` VARCHAR(255) NOT NULL) ENGINE=InnoDB;");

        // Create commits table
        $db->exec("CREATE TABLE IF NOT EXISTS `commits` (sha VARCHAR(255) NOT NULL PRIMARY KEY, authorID INT, msg VARCHAR(2048) NOT NULL) ENGINE=InnoDB;");
    }
    catch(Exception $e)
    {
            die('Erreur : '.$e->getMessage());
    }
}


function commitToDB($commit, $db){
    $req = $db->prepare("INSERT INTO `commits` VALUES (?, ?, ?)");
    $req->execute(array($commit["sha"],
                        $commit["author"]["id"],
                        //$commit["commit"]["author"]["date"],
                        $commit["commit"]["message"]));
    
    echo $commit["author"]["id"] . ":" . $commit["sha"] . "\n";

    // Check if the author is already in the database
    $res = $db->prepare("SELECT * FROM `authors` WHERE id=?");
    $res->execute(array($commit["author"]["id"]));

    // Create a new user if the author is not in the db
    if(!$res->fetch()){
        $req = $db->prepare("INSERT INTO authors VALUES (?, ?, ?, ?, ?)");
        $req->execute(array($commit["author"]["id"],
                            $commit["commit"]["author"]["name"],
                            $commit["commit"]["author"]["email"],
                            $commit["author"]["avatar_url"],
                            $commit["author"]["login"]));
    }
    $res->closeCursor();

}

?>