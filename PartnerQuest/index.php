<?php
set_include_path("./src");
//require_once('/users/21911226/private/mysql_config.php');
require_once ("Router.php");
require_once ("model/QuizStorageMySQL.php");
require_once("model/AccountStorageMySQL.php");
require_once("model/AuthenticationManager.php");

$user="21706533";
$pass="zavoot5ijiTh2Ail";
$dbname="21706533_3";
$dbhost="mysql.info.unicaen.fr:3306";

$pdo=new PDO('mysql:host=mysql.info.unicaen.fr:3306;dbname=21706533_3', $user, $pass);

//$pdo=new PDO("mysql:host=".MYSQL_HOST.";dbname=".MYSQL_DB."", MYSQL_USER, MYSQL_PASSWORD);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$quizStorageSQL=new QuizStorageMySQL($pdo);
$accountStorageSQL = new AccountStorageMySQL($pdo);
$authentificationManager = new AuthenticationManager($accountStorageSQL);
$router = new Router();
$router->main($quizStorageSQL,$accountStorageSQL,$authentificationManager);
?>
