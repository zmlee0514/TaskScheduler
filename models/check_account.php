<?php
/**
 * This file will only be included by others.
 */
require_once("../setting/config.php");

// Check for a cookie, if none go to login page
if (!isset($_COOKIE['session_id'])){
  http_response_code(403);
  exit();
}

// Try to find a match in the database
$guid = $_COOKIE['session_id'];
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
    setcookie("session_id", $guid, [
      'expires' => time() + 86400,
      'samesite' => 'strict'
    ]);
    $_SESSION["account_id"] = $row["id"];
  }
  else{
    // No match for guid
    http_response_code(403);
    $pdo = null;
    exit();
  }
}
catch(PDOException $e){
  error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': query error -> {$e->getMessage()}\n", 3, "error_log");
  error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': statement -> {$query->queryString}\n", 3, "error_log");
  http_response_code(500);
  die("Internal Server Error");
}
$pdo = null;
?>