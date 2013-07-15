<?php
$host = "localhost";
$root = "root";
$root_password = "86BbCc86";
$db = "test";
$table = "movies";

try {
    $dbh = new PDO("mysql:host=$host;dbname=$db", $root, $root_password);
    $dbh->exec("CREATE TABLE IF NOT EXISTS $table (
        commentID INT NOT NULL AUTO_INCREMENT,
        imdbID INT NOT NULL,
        PRIMARY KEY(commentID),
        comment TEXT NULL,
        rating INT NOT NULL,
        created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        ip TEXT NULL);")
            or die(print_r($dbh->errorInfo(), true));
} catch(PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
?>
