<?php
session_start();

// if session expired, use cookie to reconstruct session
if(!isset($_SESSION["account_id"])){
  require_once("check_account.php");
}

if(isset($_SESSION["account_id"]) && isset($_REQUEST["task_id"])){
  // validate task id
  if(is_int(intval($_REQUEST["task_id"]))){
    $task_id = intval($_REQUEST["task_id"]);
  }
  else{
    http_response_code(400);
    exit();
  }

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
    $stmt = "SELECT * FROM `tasks` WHERE `id` = {$task_id} AND `account_id` = {$_SESSION["account_id"]} AND `archive` = 0;";
    $query = $pdo->query($stmt);

    if($row = $query->fetch(PDO::FETCH_ASSOC)){
      echo json_encode(array("task" => $row));
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