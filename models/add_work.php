<?php
session_start();

// if session expired, use cookie to reconstruct session
if(!isset($_SESSION["account_id"])){
  require_once("check_account.php");
}

if(isset($_SESSION["account_id"])){
  // extract form
  if(isset($_REQUEST["task"])){
    if(is_int(intval($_REQUEST["task"]))){
      $task_id = intval($_REQUEST["task"]);
    }
    else{
      http_response_code(400);
      echo "task field must be natural number";
      exit();
    }
  }
  else{
    http_response_code(400);exit();
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
    $query = $pdo->prepare("INSERT INTO `working` VALUES(:task_id, now(), 0, 'start', null, {$_SESSION["account_id"]});");
    $query->execute(array(":task_id" => $task_id));
    
    if($query->rowCount() == 0){
      error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': insert fail -> ({$task_id}, now(), 0, 'start', null, '{$_SESSION["account_id"]}')\n", 3, "error_log");
      echo "fail";
    }
    else{
      if(isset($_REQUEST["refer"])) header('Location: '. $_REQUEST["refer"]);
      else header('Location: ../bulletin.php');
      exit();
    }
  }
  catch(PDOException $e){
    error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': query error -> {$e->getMessage()}\n", 3, "error_log");
    error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': parameters -> ({$task_id}, now(), 0, 'start', null, '{$_SESSION["account_id"]}')\n", 3, "error_log");
    http_response_code(500);
    die("Internal Server Error");
  }
  $pdo = null;

}
else{
  http_response_code(403);
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>