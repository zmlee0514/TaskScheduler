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
    $today = date("Y-m-d H:i:s");
    $stmt = "SELECT r.*, c.color_id, c.name 
      AS category FROM `records` AS r LEFT JOIN `category` AS c ON r.`category_id` = c.`id` 
      WHERE TIMESTAMPDIFF(HOUR, r.`start`, '{$today}') < 24 AND r.`account_id` = {$_SESSION["account_id"]} 
      ORDER BY r.`start` DESC;";
    $query = $pdo->query($stmt);
    $color_settings = json_decode(file_get_contents("../setting/colors.json"), true);

    if($rows = $query->fetchAll(PDO::FETCH_ASSOC)){
      // update last finish time
      $last_end = explode(" ", $rows[0]["end"]);
      $last_end[1] = substr($last_end[1], 0, 5);
      $_SESSION["last_end"] = $last_end;

      foreach($rows as &$r){
        $r["color_text"] = $color_settings["colors"][$r["color_id"]]["label"];
        $r["cost"] = round($r["cost"], 2);
      }

      echo json_encode(array("today" => $rows));
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