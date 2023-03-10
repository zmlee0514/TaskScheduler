<?php
session_start();

// if session expired, use cookie to reconstruct session
if(!isset($_SESSION["account_id"])){
  require_once("check_account.php");
}

if(isset($_SESSION["account_id"])){
  // extract form
  if(isset($_REQUEST["name"]) && isset($_REQUEST["color"])){
    $category_name = test_input($_REQUEST["name"]);

    if(is_int(intval($_REQUEST["color"]))){
      // $color_settings = json_decode(file_get_contents("../setting/colors.json"), true);
      // $color = $color_settings["colors"][$color_index];
      $color_index = intval($_REQUEST["color"]);
    }
    else{
      http_response_code(400);
      echo "color field must be natural number";
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
    $query = $pdo->prepare("INSERT INTO `category` VALUES(null, :name, :color_id, 0, now(), {$_SESSION["account_id"]});");
    $query->execute(array(":name" => $category_name, ":color_id" => $color_index));
    
    if($query->rowCount() == 0){
      error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': insert fail -> (null, $category_name, $color_index, 0, now(), '{$_SESSION["account_id"]}')\n", 3, "error_log");
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
    error_log("[{$logTime}] '{$_SERVER["PHP_SELF"]}': parameters -> (null, $category_name, $color_index, 0, now(), '{$_SESSION["account_id"]}')\n", 3, "error_log");
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