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
    $result = array();

    // update recent table from tasks which need to be down in a week
    $stmt = "INSERT INTO `recent`
      SELECT `id`, ROUND(RAND()*100), `account_id`
      FROM `tasks` 
      WHERE `account_id` = {$_SESSION["account_id"]}
      AND `id` NOT IN (
        SELECT `task_id` FROM `recent` WHERE `account_id` = {$_SESSION["account_id"]}
      ) 
      AND `deadline` < DATE_ADD(now(), INTERVAL 7 DAY) ";
    $query = $pdo->query($stmt);

    // for recent table
    $stmt = "SELECT r.`sequence`,t.`id`,t.`name`,t.`description`,t.`deadline`,t.`predict`,c.`color_id`,c.`name` AS category 
      FROM `recent` AS r LEFT JOIN `tasks` AS t ON `r`.`task_id` = t.`id` LEFT JOIN `category` AS c ON t.`category_id` = c.`id` 
      WHERE r.`account_id` = {$_SESSION["account_id"]} AND t.`archive` = 0 
      ORDER BY r.`sequence`, t.`deadline` DESC, t.`id`;";
    $query = $pdo->query($stmt);
    $color_settings = json_decode(file_get_contents("../setting/colors.json"), true);

    if($rows = $query->fetchAll(PDO::FETCH_ASSOC)){
      foreach($rows as &$r){
        $r["color_text"] = $color_settings["colors"][$r["color_id"]]["label"];
        if(!$r["deadline"]) $r["deadline"] = "";
        // $r["description"] = str_replace("\r\n", "<br>", $r["description"]);
      }
      $result["tasks"] = $rows;
    }

    // for recent task options
    $stmt = "SELECT `id`,`name`,`description`
      FROM `tasks`
      WHERE `account_id` = {$_SESSION["account_id"]} AND `archive` = 0 AND `id` NOT IN (
        SELECT `task_id` FROM `recent` WHERE `account_id` = {$_SESSION["account_id"]}
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