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
    $result = [];
    $stmt = "SELECT c.task_id, t.`name`,  t.`category_id`, t.`description`, t.`deadline`, DATEDIFF(t.`deadline`, now()) AS remaining FROM `goal` AS c LEFT JOIN `tasks` AS t ON c.`task_id` = t.`id` WHERE c.`account_id` = {$_SESSION["account_id"]};";
    $query = $pdo->query($stmt);
    if($rows = $query->fetchAll(PDO::FETCH_ASSOC)){
      $result["goals"] = $rows;
    }

    // for goal options
    $stmt = "SELECT `id`,`name`,`description`
      FROM `tasks`
      WHERE `account_id` = {$_SESSION["account_id"]} 
      AND `deadline` IS NOT NULL
      AND `archive` = 0 
      AND `id` NOT IN (
        SELECT `task_id` FROM `goal` WHERE `account_id` = {$_SESSION["account_id"]}
      );";
    $query = $pdo->query($stmt);
    if($rows = $query->fetchAll(PDO::FETCH_ASSOC)){
      $result["task_options"] = $rows;
    }

    echo json_encode($result);
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