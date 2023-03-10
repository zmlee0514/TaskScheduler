<?php
session_start();
require('setting/config.php');

if(!isset($_SESSION["account_id"])){
  // Check for a cookie, if none go to login page
  if (!isset($_COOKIE['session_id'])){
    header('Location: login.php?refer='. urlencode(getenv('REQUEST_URI')));
    exit();
  }
  $guid = $_COOKIE['session_id'];
  
  // Try to find a match in the database
  $logTime = date("d-M-Y H:i:s e"); // for logging
  try{
    $pdo = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    // set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->query("SET CHARACTER SET UTF8");
  }
  catch(PDOException $e){
    error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': connect error -> {$e->getMessage()}\n", 3, "error_log");
    http_response_code(500);
    die("Internal Server Error");
  }
  try{
    $query = $pdo->query("SELECT `id`, `guid` FROM `accounts` WHERE `guid` = '{$guid}'");
    
    if($row = $query->fetch(PDO::FETCH_ASSOC)){
      // refresh expire time whenever load a page, each cookie preserve 1 day
      setcookie("session_id", $guid, time() + 86400);
      $_SESSION["account_id"] = $row["id"];
    }
    else{
      // No match for guid
      header('Location: login.php?refer='. urlencode(getenv('REQUEST_URI')));
    }
  }
  catch(PDOException $e){
    error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': query error -> {$e->getMessage()}\n", 3, "error_log");
    error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': statement -> {$query->queryString}\n", 3, "error_log");
    http_response_code(500);
    die("Internal Server Error");
  }
  $pdo = null;
}
?>