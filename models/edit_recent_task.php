<?php
session_start();

// if session expired, use cookie to reconstruct session
if(!isset($_SESSION["account_id"])){
  require_once("check_account.php");
}

if(isset($_SESSION["account_id"]) && isset($_REQUEST["task_id"]) && isset($_REQUEST["target_id"])){
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
    $query = $pdo->prepare("SELECT `sequence` FROM `recent` WHERE `task_id` = :target_id AND `account_id` = {$_SESSION["account_id"]}");
    $query->execute(array(":target_id" => $_REQUEST["target_id"]));
    if($row = $query->fetch()){
      $seq = ($row[0] == 0)? 0: $row[0]-1;
      $query = $pdo->prepare("UPDATE `recent` SET `sequence` = :seq WHERE `task_id` = :task_id AND `account_id` = :account_id;");
      $query->execute(array(":seq" => $seq, ":task_id" => $_REQUEST["task_id"], ":account_id" => $_SESSION["account_id"]));

      if($query->rowCount() == 0){
        error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': update fail -> SET `sequence` = {$seq} WHERE `task_id` = {$task_id} AND `account_id` = {$account_id};\n", 3, "error_log");
        echo "fail";
      }
      else{
        if(isset($_REQUEST["refer"])) header('Location: '. $_REQUEST["refer"]);
        else header('Location: ../bulletin.php');
        exit();
      }
    }
    else{
      error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': empty select -> `task_id` = {$_REQUEST["target_id"]} AND `account_id` = {$_SESSION["account_id"]}\n", 3, "error_log");
      echo "fail";
    }
  }
  catch(PDOException $e){
    error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': query error -> {$e->getMessage()}\n", 3, "error_log");
    http_response_code(500);
    die("Internal Server Error");
  }
  $pdo = null;

}
else{
  http_response_code(403);
}
?>