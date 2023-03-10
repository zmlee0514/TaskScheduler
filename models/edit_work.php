<?php
session_start();

// if session expired, use cookie to reconstruct session
if(!isset($_SESSION["account_id"])){
  require_once("check_account.php");
}

if(isset($_SESSION["account_id"])){
  // extract parameters
  if(isset($_REQUEST["state"]) && ($_REQUEST["state"] == "stop" || $_REQUEST["state"] == "resume")){
    $state = $_REQUEST["state"];
    $stopOrMinus = ($_REQUEST["state"] == "resume")? ", `minus` = `minus` + TIMESTAMPDIFF(MINUTE, `stop`, now())": ", `stop` = now()";
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
    $query = $pdo->prepare("UPDATE `working` SET `state` = :state {$stopOrMinus} WHERE `account_id` = :account_id;");
    $query->execute(array(":state" => $state, ":account_id" => $_SESSION["account_id"]));
    
    if($query->rowCount() == 0){
      error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': update fail -> SET `state` = $state $stopOrMinus WHERE `account_id` = {$_SESSION["account_id"]}\n", 3, "error_log");
      echo "fail";
    }
    // else{
    //   if(isset($_REQUEST["refer"])) header('Location: '. $_REQUEST["refer"]);
    //   else header('Location: ../bulletin.php');
    //   exit();
    // }
  }
  catch(PDOException $e){
    error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': query error -> {$e->getMessage()}\n", 3, "error_log");
    error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': statement -> SET `state` = $state $stopOrMinus WHERE `account_id` = {$_SESSION["account_id"]}\n", 3, "error_log");
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