<?php
session_start();

// if session expired, use cookie to reconstruct session
if(!isset($_SESSION["account_id"])){
  require_once("check_account.php");
}

if(isset($_SESSION["account_id"])){
  $logTime = date("d-M-Y H:i:s e"); // for logging
  require_once("../setting/config.php");
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
    $stmt = "SELECT count(*) FROM `slogans` where `account_id` = '{$_SESSION["account_id"]}'";
    $query = $pdo->query($stmt);
    $num = $query->fetch()[0];

    if($num > 0){
      srand(date("Ymd"));
      $slogan_index = rand(0, $num-1);

      $stmt = "SELECT `content`,`class` FROM `slogans` where `account_id` = {$_SESSION["account_id"]} LIMIT {$slogan_index},1";
      $query = $pdo->query($stmt);
      if($row = $query->fetch(PDO::FETCH_ASSOC)){
        echo json_encode($row);
      }
      else{
        error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': empty query -> {$stmt}\n", 3, "error_log");
        echo '{}';
      }
    }
    else{
      echo '{}';
    }
  }
  catch(PDOException $e){
    error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': query error -> {$e->getMessage()}\n", 3, "error_log");
    error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': statement -> {$stmt}\n", 3, "error_log");
    http_response_code(500);
    die("Internal Server Error");
  }
  $pdo = null;

}
else{
  http_response_code(403);
}
?>