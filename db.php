<?php
include ("constants.php");

$dsn = "mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME;

$pdo = new PDO($dsn, DB_USER, DB_PASS, [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);
?>
