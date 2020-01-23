<!-- connect to MySQL Database -->
<?php
$db_host = "localhost";
$db_user = "root";
$db_pass = "root";
$db_name = "crud";
$connection = mysqli_connect($db_host, $db_user, $db_pass, $db_name) or die("Database Connection Error: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
?>

<?php

// create users table
$sql = "CREATE TABLE IF NOT EXISTS  `crud`.`users`("
    . "    `id` INT NOT NULL AUTO_INCREMENT,"
    . "    `email` VARCHAR(254) NOT NULL,"
    . "    `first_name` VARCHAR(225) NOT NULL,"
    . "    `last_name` VARCHAR(255) NOT NULL,"
    . "    PRIMARY KEY(`id`),"
    . "    UNIQUE(`email`)"
    . ") ENGINE = innodb";

$create_users_sqlresult = mysqli_query($connection, $sql);
if (!$create_users_sqlresult) {
    die("CREATE TABLE `crud`.`users`., failed: " . mysqli_error($connection));
}


// create publications table
$sql = "CREATE TABLE IF NOT EXISTS  `crud`.`publications`("
    . "    `id` INT NOT NULL AUTO_INCREMENT,"
    . "    `student_id` INT NOT NULL,"
    . "    `title` VARCHAR(225) NOT NULL,"
    . "    `year` YEAR NOT NULL,"
    . "    PRIMARY KEY(`id`),"
    . "    FOREIGN KEY(`student_id`) REFERENCES users(`id`)"
    . "    ON DELETE CASCADE"
    . ") ENGINE = innodb";

$create_publications_sqlresult = mysqli_query($connection, $sql);
if (!$create_publications_sqlresult) {
    die("CREATE TABLE `crud`.`publications`., failed: " . mysqli_error($connection));
}
?>