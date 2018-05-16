<?php
// Connection to the database
try
{
    $config_json = file_get_contents("./config.json");
    $config = json_decode($config_json, true);
	$db = new PDO("mysql:host=" . $config["hostname"] . ";dbname=" . $config["database"] . ";charset=utf8", $config["username"] , $config["password"]);
    $db->exec("DROP TABLE IF EXISTS `commits`");
    $db->exec("CREATE TABLE IF NOT EXISTS `commits` (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, author VARCHAR(255) NOT NULL) ENGINE=InnoDB;");
}
catch(Exception $e)
{
        die('Erreur : '.$e->getMessage());
}
?>